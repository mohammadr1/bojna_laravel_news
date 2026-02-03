<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'news_id'   => 'required|exists:news,id',
            'author'    => 'required|string|max:255',
            'email'     => 'nullable|email',
            'content'   => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        
        // $news = News::findOrFail($request->news_id);
        $news = News::with(['comments.replies', 'tags', 'category'])
            ->findOrFail($request->news_id);

        $news->comments()->create([
            'author'    => $request->author,
            'email'     => $request->email,
            'content'   => $request->content,
            'parent_id' => $request->parent_id,
            'approved'  => false,
        ]);

            // اگر مدیر پاسخ بدهد، آن را تایید کنید
        if ($request->has('admin_content')) {
            $comment->update([
                'admin_content' => $request->admin_content,
                'approved' => true, // وقتی مدیر پاسخ بدهد، تایید می‌شود
            ]);
        }

        return back()->with('success', 'نظر شما ثبت شد و پس از تایید نمایش داده می‌شود');
    }
}
