<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\News;
use App\Models\Message;
use App\Models\DailyMedia;
use App\Models\Slider;
use App\Models\DailyReport;


class ReportController extends Controller
{
    public function dailyReport(Request $request)
    {
        // 1. گارد امنیتی: چک کردن کلید API در هدر
        if ($request->header('X-API-KEY') !== env('REPORT_API_KEY')) {
            return response()->json(['error' => 'Unauthorized Access'], 401);
        }
        
        $yesterday = now()->subHours(24);
        
        // 2. استخراج داده ها
        // فرض می‌کنیم `published_at` در جدول News موجود است.
        $new_news = News::where('published_at', '>=', $yesterday)
                        ->where('status', 1) 
                        ->get(['title', 'position', 'published_at', 'id']);
        
        $metrics = [
            'news_count' => $new_news->count(),
            // فرض می‌کنیم `created_at` در مدل Message وجود دارد.
            'messages_count' => Message::where('created_at', '>=', $yesterday)->count(),
            // فرض می‌کنیم فیلد `response` در مدل Message وجود دارد و برای پاسخ‌ها استفاده می‌شود.
            'messages_answered_count' => Message::whereNotNull('response')->where('created_at', '>=', $yesterday)->count(),
            'daily_media_count' => DailyMedia::where('created_at', '>=', $yesterday)->count(),
            'sliders_count' => Slider::where('created_at', '>=', $yesterday)->count(),
        ];

        // محاسبه مجموع آیتم‌های جدید
        $metrics['total_new_items'] = array_sum($metrics);

        // 3. فرمت جزئیات خبرها
        $news_details = $new_news->map(function ($news) {
            // نکته: اگر route customer.news.show وجود ندارد، آن را با یک مسیر معتبر جایگزین کنید یا حذف کنید.
            try {
                // ساخت لینک کامل خبر - اگر خطا داد، فقط لینک اصلی سایت را قرار دهید.
                $link = Route::has('customer.news.show') ? route('customer.news.show', $news->id) : url('/news/' . $news->id);
            } catch (\Exception $e) {
                // اگر روت به درستی تعریف نشده باشد، از یک آدرس ثابت استفاده شود.
                $link = url('/news/' . $news->id);
            }

            return [
                'id' => $news->id,
                'title' => $news->title,
                'position' => $news->position,
                'published_at' => $news->published_at ? $news->published_at->format('Y/m/d H:i') : null,
                'link' => $link
            ];
        })->toArray(); // تبدیل به آرایه برای ذخیره راحت‌تر

        // 4. ذخیره گزارش در جدول daily_reports
        $report = DailyReport::create([
            'total_new_items' => $metrics['total_new_items'],
            'news_count' => $metrics['news_count'],
            'messages_count' => $metrics['messages_count'],
            'messages_answered_count' => $metrics['messages_answered_count'],
            'daily_media_count' => $metrics['daily_media_count'],
            'sliders_count' => $metrics['sliders_count'],
            'new_news_details' => $news_details, // بدون toJson() به دلیل casts='array'
            'report_time' => now()
        ]);
        
        // 5. بازگرداندن پاسخ JSON
        return response()->json([
            'status' => 'success',
            'site_name' => env('APP_NAME', 'Local App'), // اضافه کردن نام سایت برای تجمیع کننده
            'report_id' => $report->id,
            'report_date' => $report->report_time->format('Y-m-d H:i:s'),
            'metrics_24h' => $metrics,
            'new_news_details' => $news_details
        ]);
    }
}
