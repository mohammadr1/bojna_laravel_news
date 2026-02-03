<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Comment extends Model
{
    use SoftDeletes;

        protected $fillable = [
        'author',
        'email',
        'content',
        'approved',
        'parent_id',
        'admin_content'
    ];

    /* polymorphic */
    public function commentable()
    {
        return $this->morphTo();
    }

    /* replies */
    // public function replies()
    // {
    //     return $this->hasMany(Comment::class, 'parent_id');
    // }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->where('approved', true)
            ->latest();
    }

    // public function parent()
    // {
    //     return $this->belongsTo(Comment::class, 'parent_id');
    // }

    /* فقط تایید شده‌ها */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }
}
