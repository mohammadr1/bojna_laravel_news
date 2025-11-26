<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;


class DailyReportNotification extends Notification
{
    use Queueable;

    protected $metrics;
    protected $newsDetails;
    protected $siteName;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $metrics, array $newsDetails, string $siteName)
    {
        $this->metrics = $metrics;
        $this->newsDetails = $newsDetails;
        $this->siteName = $siteName;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['telegram'];
    }

    
    /**
     * آماده‌سازی نوتیفیکیشن برای ارسال از طریق تلگرام.
     */
    public function toTelegram($notifiable)
    {
        $reportTime = now()->format('Y/m/d H:i');
        
        $messageText = "📈 *گزارش 24 ساعته سایت:* [{$this->siteName}] ({$reportTime})\n\n";
        
        // بخش خلاصه‌ی کلید
        $messageText .= "--- *خلاصه آمار:* ---\n";
        $messageText .= "🔹 آیتم‌های جدید کل: `{$this->metrics['total_new_items']}`\n";
        $messageText .= "🔹 خبر جدید منتشر شده: `{$this->metrics['news_count']}`\n";
        $messageText .= "🔹 پیام‌های دریافتی: `{$this->metrics['messages_count']}`\n";
        $messageText .= "🔹 پیام‌های پاسخ داده شده: `{$this->metrics['messages_answered_count']}`\n";
        $messageText .= "🔹 اسلایدر/مدیا جدید: `{$this->metrics['sliders_count']}` / `{$this->metrics['daily_media_count']}`\n";
        
        // بخش جزئیات خبرها
        if (!empty($this->newsDetails)) {
            $messageText .= "\n📰 *جزئیات خبرهای جدید:*\n";
            $limit = 5; // نمایش حداکثر 5 خبر
            
            foreach (array_slice($this->newsDetails, 0, $limit) as $news) {
                // ساختار: - [عنوان خبر](لینک خبر) (زمان انتشار)
                $newsLine = "- [{$news['title']}]({$news['link']}) ({$news['published_at']})\n";
                
                // جایگزینی پرانتزها در لینک با URL-encoded (تلگرام در لینک پرانتز را خطا می‌گیرد)
                $newsLine = str_replace(['(', ')'], ['%28', '%29'], $newsLine);

                // اگر لینک شامل نقطه ویرگول (;) باشد، باید آن را هم کدگذاری کنیم
                $newsLine = str_replace(';', '%3B', $newsLine);

                $messageText .= $newsLine;
            }
            
            if (count($this->newsDetails) > $limit) {
                $messageText .= "\n_و " . (count($this->newsDetails) - $limit) . " مورد دیگر..._\n";
            }
        } else {
            $messageText .= "\n_در 24 ساعت گذشته خبر جدیدی منتشر نشده است._\n";
        }
        
        $messageText .= "\n_گزارش از سرویس " . env('APP_NAME') . "_\n";

        return TelegramMessage::create()
            ->content($messageText)
            ->parseMode('Markdown'); // مهم: استفاده از Markdown برای فرمت‌بندی
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // return (new MailMessage)
        //     ->line('The introduction to the notification.')
        //     ->action('Notification Action', url('/'))
        //     ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
