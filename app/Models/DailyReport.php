<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    protected $table = 'daily_reports';

    protected $fillable = [
        'report_time',
        'total_new_items',
        'news_count',
        'daily_media_count',
        'sliders_count',
        'messages_count',
        'messages_answered_count',
        'new_news_details', // فیلد JSON
    ];

    protected $casts = [
        'new_news_details' => 'array',
        'report_time' => 'datetime',
    ];
}
