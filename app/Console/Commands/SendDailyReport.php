<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route as RouteFacade; // برای استفاده از تابع route()
use App\Models\News;
use App\Models\Message;
use App\Models\DailyMedia;
use App\Models\Slider;
use App\Models\DailyReport;
use App\Notifications\DailyReportNotification; 
use Illuminate\Database\Eloquent\Model; // برای ساخت مدل موقت قابل ارسال نوتیفیکیشن


class SendDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates the 24-hour site report and sends it to Telegram.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
                $this->info('Generating 24-hour report for ' . env('APP_NAME'));

        $yesterday = now()->subHours(24);
        
        // 1. استخراج داده‌ها 
        $new_news = News::where('published_at', '>=', $yesterday)
                        ->where('status', 1) 
                        ->get(['title', 'position', 'published_at', 'id']);
        
        $metrics = [
            'news_count' => $new_news->count(),
            'messages_count' => Message::where('created_at', '>=', $yesterday)->count(),
            'messages_answered_count' => Message::whereNotNull('response')->where('created_at', '>=', $yesterday)->count(),
            'daily_media_count' => DailyMedia::where('created_at', '>=', $yesterday)->count(),
            'sliders_count' => Slider::where('created_at', '>=', $yesterday)->count(),
        ];

        $metrics['total_new_items'] = array_sum($metrics);

        // 2. فرمت جزئیات خبرها
        $news_details = $new_news->map(function ($news) {
             try {
                $link = RouteFacade::has('customer.news.show') ? route('customer.news.show', $news->id) : url('/news/' . $news->id);
            } catch (\Exception $e) {
                $link = url('/news/' . $news->id);
            }
            return [
                'id' => $news->id,
                'title' => $news->title,
                'position' => $news->position,
                'published_at' => $news->published_at ? $news->published_at->format('Y/m/d H:i') : null,
                'link' => $link
            ];
        })->toArray();

        // 3. ذخیره گزارش در جدول daily_reports
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

        // 4. ارسال اعلان (Notification)
        // ساخت یک مدل Notifiable موقت برای ارسال نوتیفیکیشن
        $notifiable = new class extends Model {
            use \Illuminate\Notifications\Notifiable;
            // این متد توسط پکیج تلگرام فراخوانی می شود تا Chat ID را بگیرد
            public function routeNotificationForTelegram() {
                return env('TELEGRAM_CHAT_ID'); 
            }
        };

        // ارسال نوتیفیکیشن حاوی همه داده‌ها
        $notifiable->notify(new DailyReportNotification($metrics, $news_details, env('APP_NAME')));

        $this->info('Report sent successfully!');
    }
}
