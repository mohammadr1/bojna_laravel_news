<?php

namespace App\Models;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'on_titr',
        'title',
        // 'slug',
        'subtitle',
        'content_type',
        // 'image',
        'media_type',
        'media_path',
        'thumbnailVideo',
        'body',
        'meta_description',
        'author_id',
        'published_at',
        'category_id',
        // 'tags',
        'short_link',
        'news_code',
        'position',
        'status',
        'views',
    ];

    const CONTENT_TYPES = [
        'یادداشت'         => 'یادداشت',
        'گفتگوی تفصیلی'   => 'گفتگوی تفصیلی',
        'مصاحبه'          => 'مصاحبه',
        'بازنشر'          => 'بازنشر',
        'پوششی'           => 'پوششی',
        'دریافتی'         => 'دریافتی',
        'گزارش'           => 'گزارش',
        'گزارش تصویری'    => 'گزارش تصویری',
        'میزگرد'          => 'میزگرد',
        'فیلم'            => 'فیلم',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        // 'tags' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'title';
    }
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'news_tag');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($news) {
            do {
                $code = Str::random(6);
            } while (News::where('short_link', $code)->exists());

            $news->short_link = $code;
        });
    }

    protected static function booted()
    {
        static::created(function ($news) {
            $news->update([
                'news_code' => self::generateNewsCode($news->id),
            ]);
        });
    }
    public static function generateNewsCode(int $id): string
    {
        $secret_multiplier = 7531;
        $secret_increment = 52847;
        $code_length = 8;
        $modulus = pow(10, $code_length);

        $encoded_number = ($id * $secret_multiplier) + $secret_increment;
        $final_code = $encoded_number % $modulus;

        return str_pad((string)$final_code, $code_length, '0', STR_PAD_LEFT);
    }


    public static function modInverse($a, $m)
    {
        $m0 = $m;
        $x0 = 0;
        $x1 = 1;

        if ($m == 1)
            return 0;

        while ($a > 1) {
            $q = intdiv($a, $m);
            $t = $m;

            $m = $a % $m;
            $a = $t;
            $t = $x0;

            $x0 = $x1 - $q * $x0;
            $x1 = $t;
        }

        if ($x1 < 0)
            $x1 += $m0;

        return $x1;
    }

    public static function decodeNewsCode(string $code): int
    {
        $secret_multiplier = 7531;
        $secret_increment = 52847;
        $code_length = 8; // ← اینجا هم با بالا هماهنگ بشه
        $modulus = pow(10, $code_length);

        $code_int = (int)$code;
        $inv = self::modInverse($secret_multiplier, $modulus);

        $id = (($code_int - $secret_increment) * $inv) % $modulus;

        if ($id < 0) {
            $id += $modulus;
        }

        return $id;
    }

    // public function comments()
    // {
    //     return $this->morphMany(Comment::class, 'commentable')
    //         ->whereNull('parent_id')
    //         ->latest();
    // }
    public function comments()
    {
        // return $this->morphMany(Comment::class, 'commentable')
        //     ->whereNull('parent_id')
        //     ->where('approved', true)
        //     ->with(['replies' => function ($q) {
        //         $q->where('approved', true);
        //     }])
        //     ->latest();

        return $this->morphMany(Comment::class, 'commentable')
            ->whereNull('parent_id')
            ->approved()
            // ->with('replies')
            ->latest();
    }
}
