<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\News;


class EditNews extends EditRecord
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

        protected function mutateFormDataBeforeFill(array $data): array
    {
        // اگر نوع رسانه تصویر باشد
        if ($data['media_type'] === 'image') {
            $data['image_upload'] = $data['media_path'];
        }

        // اگر نوع رسانه ویدیو باشد
        if ($data['media_type'] === 'video') {
            $data['video_link'] = $data['media_path'];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // چون فیلد media_path در ویرایش تغییر نمی‌کند، همان را نگه می‌داریم
        unset($data['image_upload'], $data['video_link']);
        return $data;
    }

    protected function afterSave(): void
    {
        $news = $this->record;

        // --- محدودیت اسلایدر ---
        if ($news->position === 'slider') {
            $sliders = News::where('position', 'slider')
                           ->orderBy('created_at', 'desc')
                           ->skip(5)->take(PHP_INT_MAX) // از خبر ششم به بعد
                           ->get();

            foreach ($sliders as $slider) {
                $slider->update(['position' => 'slider_bottom']); // انتقال به پیشنهاد سردبیر
            }
        }

        // --- محدودیت کنار اسلایدر ---
        if ($news->position === 'slider_side') {
            $sideNews = News::where('position', 'slider_side')
                            ->orderBy('created_at', 'desc')
                            ->skip(2)->take(PHP_INT_MAX) // از خبر سوم به بعد
                            ->get();

            foreach ($sideNews as $side) {
                $side->update(['position' => 'slider_bottom']); // انتقال به پیشنهاد سردبیر
            }
        }
    }
}
