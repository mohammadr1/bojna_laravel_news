<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\News;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\App;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\NewsResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\NewsResource\RelationManagers;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'مدیریت محتوای خبر';
    protected static ?string $modelLabel = 'اخبار';         // عنوان مفرد
    protected static ?string $pluralLabel = 'اخبار';          // جمع
    protected static ?string $navigationLabel = 'اخبار';      // عنوان در سایدبار

    // فعال کردن خبر برای هر کاربر جدا
    //     public static function getEloquentQuery(): Builder
    // {
    //     $query = parent::getEloquentQuery();

    //     // فقط خبرهای کاربر لاگین‌شده
    //     if (Auth::check()) {
    //         $query->where('author_id', Auth::id());
    //     }

    //     return $query;
    // }

    public static function getNavigationSort(): int
    {
        return 1;
    }

public static function form(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('on_titr')
                ->label('روتیتر')
                ->maxLength(255),

            TextInput::make('title')
                ->label('تیتر')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            // TextInput::make('slug')
            //     ->label('نامک')
            //     ->unique(ignoreRecord: true)
            //     ->required(),

            TextInput::make('subtitle')
                ->label('لید')
                ->maxLength(255)
                ->required(),

                Select::make('content_type')
                    ->label('نوع مطلب')
                    ->options(\App\Models\News::CONTENT_TYPES)
                    ->required()
                    ->searchable()
                    ->native(false),

            Textarea::make('meta_description')
                ->label('توضیحات متا')
                ->rows(2)
                ->columnSpan('full'),

            RichEditor::make('body')
                ->label('محتوا')
                ->required()
                ->extraAttributes(['class' => 'rich-editor'])
                ->fileAttachmentsDisk('public') // اگر فایل‌ها رو ذخیره می‌کنی
                ->fileAttachmentsDirectory('uploads/news') // محل ذخیره عکس‌ها
                ->columnSpanFull(),

            FileUpload::make('image')
                ->label('تصویر شاخص')
                ->image()
                ->required()
                ->directory('thumbnails'),

            Select::make('category_id')
                ->label('دسته‌بندی')
                ->options(\App\Models\Category::pluck('title', 'id'))
                ->searchable()
                ->required(),

            // Select::make('author_id')
            //     ->label('نویسنده')
            //     ->relationship('author', 'name')
            //     ->required(),

           Select::make('tags')
                ->label('برچسب‌ها')
                ->multiple()
                ->relationship('tags', 'name')
                ->searchable()
                ->preload()
                ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('نام تگ')
                            ->required()
                            ->rules(['unique:tags,name']), // ✅ اعتبارسنجی یکتا بودن
                    ])
                    ->createOptionUsing(function (array $data) {
                        return Tag::create($data);
                    }),



            DateTimePicker::make('published_at')
                ->label('تاریخ انتشار')
                ->default(Carbon::now()) // مقدار پیش‌فرض: زمان حال
                ->jalali()
                ->required(),

            // Toggle::make('is_featured')
            //     ->label('ویژه باشد؟'),


                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        0 => 'پیش‌نویس',
                        1 => 'منتشر شده',
                        2 => 'آرشیو شده',
                    ])
                    ->default(1)
                    ->required(),

                    Select::make('position')
                    ->label('موقعیت قرارگیری خبر در سایت')
                    ->options([
                        'slider_bottom' => 'پیشنهاد سر دبیر',
                        'slider_side' => 'سمت چپ اسلایدر',
                        'slider' => 'اسلایدر',
                    ])
                    ->required()
                    ->default('slider_bottom'),


                Hidden::make('author_id')
                    ->default(fn () => auth()->id()),

                Hidden::make('short_link'),
        ]);

}


public static function table(Table $table): Table
{
    return $table
        ->defaultSort('created_at', 'desc')
        ->columns([
            ImageColumn::make('image')
                ->label('تصویر شاخص')
                ->square()
                ->disk('public') // اگر از disk public استفاده می‌کنی
                ->url(fn ($record) => asset('storage/thumbnails/' . $record->thumbnail)), // مسیر دستی

            TextColumn::make('author.display_name')
                ->label('نویسنده')
                ->sortable()
                ->searchable(),

            TextColumn::make('title')
                ->label('عنوان')
                ->searchable()
                ->sortable(),

            // TextColumn::make('category.name')
            //     ->label('دسته‌بندی'),

            BadgeColumn::make('status')
                ->label('وضعیت')
                ->colors([
                    'پیش‌نویس' => 'gray',
                    'منتشر شده' => 'green',
                    'آرشیو شده' => 'red',
                ])
                ->formatStateUsing(function ($state) {
                    return match ($state) {
                        0 => 'پیش‌نویس',
                        1 => 'منتشر شده',
                        2 => 'آرشیو شده',
                        default => 1,
                    };
                }),
            // ToggleColumn::make('is_featured')
            //     ->label('ویژه؟'),


            BadgeColumn::make('position')
                ->label('موقعیت')
                ->colors([
                    'slider' => 'اسلایدر',
                    'slider_side' => 'خبر کنار اسلایدر',
                    'slider_bottom' => 'خبر پایین اسلایدر',
                ])
                ->formatStateUsing(function ($state) {
                    return match ($state) {
                        'slider' => 'اسلایدر',
                    'slider_side' => 'خبر کنار اسلایدر',
                    'slider_bottom' => 'خبر پایین اسلایدر',
                        default => 'نامشخص',
                    };
                }),


            TextColumn::make('published_at')
                ->label('تاریخ انتشار')
                ->dateTime()
                ->date() // ✅ نمایش به‌صورت تاریخ
                ->when(App::isLocale('fa'), fn (TextColumn $column) => $column->jalaliDate()),
        ])
        ->defaultSort('published_at', 'desc')
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            // لینک لیست اخبار
            \Filament\Navigation\NavigationItem::make()
                ->label('اخبار')
                ->url(static::getUrl())
                ->group(static::$navigationGroup)
                ->icon('heroicon-o-newspaper'),

            // لینک ایجاد خبر جدید
            \Filament\Navigation\NavigationItem::make()
                ->label('ایجاد خبر جدید')
                ->url(static::getUrl('create'))
                ->group(static::$navigationGroup)
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
