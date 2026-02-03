<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Filament\Resources\CommentResource\RelationManagers;
// use App\Filament\Resources\CommentResource\RelationManagers\RepliesRelationManager;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('author')
                ->disabled(),

                TextInput::make('email')
                    ->disabled(),

                Textarea::make('content')
                    ->disabled(),

                Toggle::make('approved')
                    ->label('تأیید شده'),

                Textarea::make('admin_content')
                    ->label('پاسخ مدیر')
                    ->helperText('در صورت ثبت، به‌عنوان پاسخ نمایش داده می‌شود'),
            ]);
    }

   public static function table(Table $table): Table
{
    return $table
        ->defaultSort('created_at', 'desc')
        ->columns([
            Tables\Columns\TextColumn::make('author')->label('نام'),
            Tables\Columns\TextColumn::make('content')->limit(10),
            Tables\Columns\BooleanColumn::make('approved')->label('تأیید'),
            Tables\Columns\TextColumn::make('commentable_title')
                ->label('مربوط به خبر...')
                ->limit(30)
                ->getStateUsing(function ($record) {
                    if ($record->commentable instanceof \App\Models\News) {
                        return $record->commentable->title;
                    }
                    return '—';
                })
                ->url(function ($record) {
                    if ($record->commentable instanceof \App\Models\News) {
                        return route('customer.news.show', $record->commentable->title);
                    }
                    return null;
                })
                ->openUrlInNewTab(),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            
            // اکشن پاسخ
            Action::make('reply_admin')
                ->label('پاسخ')
                ->form([
                    Textarea::make('admin_content')
                        ->label('مدیر متن پاسخ')
                        ->required(),
                ])
                ->action(function (Comment $record, array $data) {
                    // بررسی داده‌ها
                    if (isset($data['admin_content']) && !empty($data['admin_content'])) {
                        $record->update([
                            'admin_content' => $data['admin_content'],
                            'approved' => 1,
                        ]);
                        Log::debug($data);
                        // ارسال پیام موفقیت
                        return Notification::make()
                            ->success()
                            ->title('پاسخ با موفقیت ثبت شد.')
                            ->send();
                    } else {
                        // ارسال پیام خطا
                        return Notification::make()
                            ->danger()
                            ->title('متن پاسخ نمی‌تواند خالی باشد.')
                            ->send();
                    }
                }),

            // اکشن تأیید
            Action::make('approve')
                ->label('تأیید')
                ->visible(fn (Comment $record) => !$record->approved)
                ->action(fn (Comment $record) => $record->update(['approved' => true])),

        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}


    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->whereNull('parent_id');
    // }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
