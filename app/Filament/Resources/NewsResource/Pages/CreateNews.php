<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\News;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;

    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // اگر نوع رسانه تصویر باشد، مقدار image_upload را در media_path ذخیره کن
        if ($data['media_type'] === 'image' && isset($data['image_upload'])) {
            $data['media_path'] = $data['image_upload'];
        }

        // اگر نوع رسانه ویدیو باشد، مقدار video_link را در media_path ذخیره کن
        if ($data['media_type'] === 'video' && isset($data['video_link'])) {
            $data['media_path'] = $data['video_link'];
        }

        // فیلدهای اضافی را حذف کنیم
        unset($data['image_upload'], $data['video_link']);

        return $data;
    }


    protected function afterCreate(): void
    {
        $news = $this->record;

        // --- محدودیت اسلایدر ---
        if ($news->position == 'slider') {
            $sliders = News::where('position', 'slider')
                           ->orderBy('created_at', 'desc')
                           ->skip(5)->take(PHP_INT_MAX) // از خبر ششم به بعد
                           ->get();

            foreach ($sliders as $slider) {
                $slider->update(['position' => 'slider_bottom']); // انتقال به پیشنهاد سردبیر
            }
        }

        // --- محدودیت کنار اسلایدر ---
        if ($news->position == 'slider_side') {
            $sideNews = News::where('position', 'slider_side')
                            ->orderBy('created_at', 'desc')
                            ->skip(2)->take(PHP_INT_MAX) // از خبر سوم به بعد
                            ->get();

            foreach ($sideNews as $side) {
                $side->update(['position' => 'slider_bottom']);
            }
        }
    }
}
