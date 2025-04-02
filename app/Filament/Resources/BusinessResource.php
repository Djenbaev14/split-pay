<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessResource\Pages;
use App\Models\Business;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Table;

class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->label('Название')
                ->placeholder('Название')
                ->unique(ignoreRecord: true)
                ->required()
                ->maxLength(255)
                ->columnSpan(12),
            // Quyida `customers` jadvaliga tegishli maydonlar qo‘shiladi
            Forms\Components\Section::make('Владелец бизнеса')
                ->schema([
                    Forms\Components\TextInput::make('customer_name')
                        ->label('Владелец бизнеса')
                        ->placeholder('Владелец бизнеса')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(6),
                    Forms\Components\TextInput::make('customer_login')
                        ->label('Логин')
                        ->placeholder('Логин')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(6)
                        ->unique(table: 'customers', column: 'login', ignoreRecord: true),
                    Forms\Components\TextInput::make('customer_password')
                        ->password()
                        ->label('Пароль')
                        ->placeholder('Пароль')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(6),
                ])
                ->columns(12),
        ])
        ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->label('Создавать')
                    ->modalHeading('Создание нового бизнеса')
                    ->modalSubmitActionLabel('Создание')
                    ->modalWidth('lg') // Modal o‘lchami (sm, md, lg, xl)
                    ->modalAlignment('right') // Modalni o‘ng tarafga joylashtirish
                    ->modal() // Modal oynada ochish
                    ->action(function (array $data) {
                        // Biznesni yaratish
                        $business = Business::create([
                            'name' => $data['name'],
                        ]);

                        // Biznes egasini (customer) yaratish
                        $business->customers()->create([
                            'name' => $data['customer_name'],
                            'login' => $data['customer_login'],
                            'password' => bcrypt($data['customer_password']),
                            'business_id' => $business->id,
                        ]);

                        Notification::make()
                            ->title('Бизнес успешно создан!')
                            ->success()
                            ->send();
                    })
                    ->slideOver(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Время создания')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->slideOver(),
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
    public static function getNavigationLabel(): string
    {
        return 'Бизнесы'; // Rus tilidagi nom
    }
    public static function getModelLabel(): string
    {
        return 'Бизнесы'; // Rus tilidagi yakka holdagi nom
    }
    public static function getPluralModelLabel(): string
    {
        return 'Бизнесы'; // Rus tilidagi ko'plik shakli
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBusinesses::route('/'),
            // 'create' => Pages\CreateBusiness::route('/create'),
            'edit' => Pages\EditBusiness::route('/{record}/edit'),
        ];
    }
}
