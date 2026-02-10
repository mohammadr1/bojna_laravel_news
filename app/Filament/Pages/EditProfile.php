<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;

use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // protected static string $view = 'filament.pages.edit-profile';

    // public $display_name;
    // public $password;
    // public $password_confirmation;


    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                         TextInput::make('display_name')
                            ->label('نام نمایشی')
                            ->required()
                            ->minLength(3)
                            ->maxLength(50)
                            ->regex('/^[\p{L}\p{N}\s]+$/u')
                            ->unique(
                                table: 'users',
                                column: 'display_name',
                                ignorable: fn () => auth()->user()
                            ),

                        TextInput::make('password')
                            ->password()
                            ->nullable()
                            ->confirmed(),

                        TextInput::make('password_confirmation')
                            ->password(),
                    ])
                    ->statePath('data') // ← این خط حیاتی است
            ),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // اگر پسورد وارد نشده، کلاً از دیتای ذخیره حذفش کن
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        return $data;
    }
}

