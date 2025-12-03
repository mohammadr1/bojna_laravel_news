<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\News;
use App\Models\Message;
use App\Models\DailyMedia;
use App\Models\Slider;
use App\Models\DailyReport;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * تولید گزارش روزانه/بازه زمانی با تفکیک محتوای تولیدی و بازنشر.
     * دسترسی به API با کلید X-API-KEY کنترل می‌شود.
     */
    public function dailyReport(Request $request)
    {
        // 1. دریافت و تنظیم بازه زمانی
        // پارامترها: start_date و end_date (به صورت YYYY-MM-DD HH:MM:SS)
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        // اگر بازه ای مشخص نشده بود، به صورت پیش‌فرض ۲۴ ساعت گذشته را در نظر می‌گیرد.
        $end_time = $end ? Carbon::parse($end) : Carbon::now();
        $start_time = $start ? Carbon::parse($start) : $end_time->copy()->subDay();
        
        // 2. گارد امنیتی: چک کردن کلید API در هدر
        if ($request->header('X-API-KEY') !== env('REPORT_API_KEY')) {
            return response()->json(['error' => 'Unauthorized Access'], 401);
        }
        
        // 3. کوئری‌های پایه و تفکیک خبرها
        $news_query = News::whereBetween('created_at', [$start_time, $end_time]);

        // تفکیک خبرها:
        $original_news_count = $news_query->clone()->where('content_type', '!=', 'بازنشر')->count();
        $repost_news_count = $news_query->clone()->where('content_type', 'بازنشر')->count();
        $total_news_count = $original_news_count + $repost_news_count;

        // 4. استخراج جزئیات خبرها (با افزودن content_type)
        $new_news = $news_query->latest('created_at')
            ->where('status', 1)
            ->get(['title', 'position', 'published_at', 'id', 'content_type']);
        
        // 5. محاسبه متریک‌ها (اعمال شرط بازه زمانی بر تمام مدل‌ها)
        $metrics = [
            'news_count' => $total_news_count,
            'original_news_count' => $original_news_count, // تعداد خبر تولیدی
            'repost_news_count' => $repost_news_count,     // تعداد خبر بازنشر
            
            // 👇 اصلاح شده: استفاده از whereBetween برای فیلتر تاریخ در تمام مدل‌ها
            'messages_count' => Message::whereBetween('created_at', [$start_time, $end_time])->count(),
            
            // پیام‌های پاسخ داده شده
            'messages_answered_count' => Message::whereBetween('created_at', [$start_time, $end_time])
                                              ->whereNotNull('response')
                                              ->count(),
            
            'daily_media_count' => DailyMedia::whereBetween('created_at', [$start_time, $end_time])->count(),
            'sliders_count' => Slider::whereBetween('created_at', [$start_time, $end_time])->count(),
        ];

        // محاسبه مجموع آیتم‌های جدید (که شامل خبر، مدیا و اسلایدر است و پیام‌ها را حذف می‌کند)
        $metrics['total_new_items'] = $total_news_count + 
                                      $metrics['daily_media_count'] + 
                                      $metrics['sliders_count'];

        // 6. فرمت جزئیات خبرها (با content_type)
        $news_details = $new_news->map(function ($news) {
            // ساخت لینک کامل خبر (استفاده از try/catch برای جلوگیری از خطای روت)
            try {
                $link = Route::has('customer.news.show') ? route('customer.news.show', $news->id) : url('/news/' . $news->id);
            } catch (\Exception $e) {
                $link = url('/news/' . $news->id);
            }

            return [
                'id' => $news->id,
                'title' => $news->title,
                'position' => $news->position,
                'published_at' => $news->published_at ? $news->published_at->format('Y/m/d H:i') : null,
                'link' => $link,
                'content_type' => $news->content_type // اضافه شدن فیلد جدید
            ];
        })->toArray(); 

        // 7. ذخیره گزارش در جدول daily_reports
        // توجه: این ذخیره سازی همچنان بر اساس 'report_time' فعلی است و بازه زمانی را ذخیره نمی‌کند.
        $report = DailyReport::create([
            'total_new_items' => $metrics['total_new_items'],
            'news_count' => $metrics['news_count'],
            'messages_count' => $metrics['messages_count'],
            'messages_answered_count' => $metrics['messages_answered_count'],
            'daily_media_count' => $metrics['daily_media_count'],
            'sliders_count' => $metrics['sliders_count'],
            'new_news_details' => $news_details, 
            'report_time' => now()
        ]);
        
        // 8. بازگرداندن پاسخ JSON
        return response()->json([
            'status' => 'success',
            'site_name' => env('APP_NAME', 'Local App'),
            'report_id' => $report->id,
            'report_date' => $report->report_time->format('Y-m-d H:i:s'),
            'metrics_24h' => $metrics, // این شامل تمام متریک‌ها و تفکیک‌ها است
            'new_news_details' => $news_details
        ]);
    }
}