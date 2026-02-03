<?php

namespace App\Http\Controllers\customer;

use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


class NewsController extends Controller
{
    public function show(News $news)
    {
        // $news->load('tags');
        $comments = $news->comments;
        $news->load('tags', 'category', 'comments');

        $categories = Category::where('status', 1)->withCount('news')->get();

        // گرفتن IP کاربر
        // $ip = request()->ip();

        // ساخت کلید یکتا برای ذخیره در کش
        // $key = 'news_viewed_' . $news->id . '_' . $ip;

        // بررسی اینکه آیا این IP قبلاً این خبر را دیده یا نه (مثلاً در 1 ساعت گذشته)
        // if (!Cache::has($key)) {
        //     $news->increment('views'); // فقط اگه IP جدید باشه، بازدید زیاد میشه
        //     Cache::put($key, true, now()->addHour()); // ذخیره برای 1 ساعت
        // }

        return view('customer.news.show', compact('news', 'categories'));
    }



}
