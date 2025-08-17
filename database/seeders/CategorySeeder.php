<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            'سیاسی',
            'اجتماعی',
            'اقتصادی',
            'فرهنگی',
            'ورزشی',
            'چندرسانه',
        ];

        foreach ($categories as $title) {
            $slug = preg_replace('/\s+/', '-', trim($title)); // تولید اسلاگ فارسی

            DB::table('categories')->insert([
                'title' => $title,
                'slug' => $slug,
                'description' => 'دسته‌بندی مربوط به ' . $title,
                'status' => true,
                'meta_title' => $title,
                'meta_keywords' => $title . ', خبر, دسته‌بندی',
                'meta_description' => 'اخبار مربوط به ' . $title,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        function generatePersianSlug(string $text): string
        {
            return preg_replace('/\s+/', '-', trim($text)); // فاصله‌ها رو با - جایگزین می‌کنه
        }
    }
}
