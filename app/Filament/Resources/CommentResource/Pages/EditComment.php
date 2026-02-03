<?php

namespace App\Filament\Resources\CommentResource\Pages;

use App\Filament\Resources\CommentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComment extends EditRecord
{
    protected static string $resource = CommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

        protected function mutateFormDataBeforeSave(array $data): array
    {
        // اگر admin_content پر شده بود، approved هم یک شود
        if (!empty($data['admin_content'])) {
            $data['approved'] = 1;
        }

        return $data;
    }
}
