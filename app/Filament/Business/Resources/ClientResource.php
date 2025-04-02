<?php

namespace App\Filament\Business\Resources;

use App\Filament\Business\Resources\ClientResource\Pages;
use App\Filament\Business\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationGroup = 'Клиенты';
    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label('Ism')
                    ->placeholder('Ism')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label('Familiya')
                    ->placeholder('Familiya')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('patronymic')
                    ->label('Otasining ismi')
                    ->placeholder('Otasining ismi')
                    ->required()
                    ->maxLength(255),
                Select::make('gender')
                    ->options([
                        'female'=>'Ayol',
                        'male'=>'Erkak',
                    ])
                    ->label('jinsi')
                    ->required(),
                Forms\Components\TextInput::make('birthplace')
                    ->label("Tug'ilgan joyi")
                    ->placeholder("Tug'ilgan joyi")
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('birthday')
                    ->label("Tug'ilgan kuni")
                    ->placeholder("Tug'ilgan kuni")
                    ->required(),
                Forms\Components\TextInput::make('passport_series')
                    ->label('Pasport seriyasi')
                    ->placeholder('Pasport seriyasi')
                    ->maxLength(2) // Maksimal 2 ta belgi
                    ->minLength(2) // Minimal 2 ta belgi
                    ->extraAttributes([
                        'x-on:input' => "event.target.value = event.target.value.toUpperCase()"
                    ])
                    ->required(),
                Forms\Components\TextInput::make('passport_number')
                    ->label('Pasport raqami')
                    ->numeric()
                    ->unique(ignoreRecord: true)
                    ->placeholder('Pasport raqami')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('inn')
                    ->label('INN')
                    ->unique(ignoreRecord: true)
                    ->numeric()
                    ->placeholder('INN')
                    ->maxLength(15),
                Forms\Components\TextInput::make('pinfl')
                    ->label('PINFL')
                    ->unique(ignoreRecord: true)
                    ->numeric()
                    ->placeholder('PINFL')
                    ->required()
                    ->maxLength(14),
                Forms\Components\DatePicker::make('passport_date_issue')
                    ->label('Pasport berilgan sana')
                    ->required(),
                Forms\Components\DatePicker::make('passport_date_expiration')
                    ->label('Pasportning amal qilish muddati')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Создать')
                    ->modal()
                    ->modalHeading('Создать')
                    ->modalSubmitActionLabel('Сохранить')
                    ->modalWidth(MaxWidth::TwoExtraLarge)
                    ->modalAlignment('end')
                    ->slideOver()
                    ->action(function (array $data) {
                        $client = Client::create([
                            'customer_id' => auth()->user()->id,
                            'first_name' => $data['first_name'],
                            'last_name' => $data['last_name'],
                            'patronymic' => $data['patronymic'],
                            'gender' => $data['gender'],
                            'birthplace' => $data['birthplace'],
                            'birthday' => $data['birthday'],
                            'passport_series' => $data['passport_series'],
                            'passport_number' => $data['passport_number'],
                            'inn' => $data['inn'],
                            'pinfl' => $data['pinfl'],
                            'passport_date_issue' => $data['passport_date_issue'],
                            'passport_date_expiration' => $data['passport_date_expiration'],
                        ]);

                        Notification::make()
                            ->title('Klient muvaffaqiyatli yaratildi!')
                            ->success()
                            ->send();
                    })
            ])
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('FIO')
                    ->formatStateUsing(fn ($record) => $record->first_name . ' ' . $record->last_name .' '.$record->patronymic)
                    ->searchable(),
                Tables\Columns\TextColumn::make('passport_series')
                    ->label('Pasport    ')
                    ->formatStateUsing(fn ($record) => $record->passport_series . ' ' . $record->passport_number)
                    ->searchable(),
                Tables\Columns\TextColumn::make('pinfl')
                    ->label('PINFL')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                
                ->modal()
                ->modalHeading('Изменение')
                ->modalWidth('lg')
                ->modalAlignment('end')
                ->using(function (Client $record, array $data): Client {
                    // Filial ma'lumotlarini yangilash
                    $record->update([
                        'customer_id' => auth()->user()->id,
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'patronymic' => $data['patronymic'],
                        'gender' => $data['gender'],
                        'birthplace' => $data['birthplace'],
                        'birthday' => $data['birthday'],
                        'passport_series' => $data['passport_series'],
                        'passport_number' => $data['passport_number'],
                        'inn' => $data['inn'],
                        'pinfl' => $data['pinfl'],
                        'passport_date_issue' => $data['passport_date_issue'],
                        'passport_date_expiration' => $data['passport_date_expiration'],
                    ]);

                    Notification::make()
                        ->title('Tarif muvaffaqiyatli tahrirlandi!')
                        ->success()
                        ->send();

                    return $record;
                }),
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
        return 'Клиенты'; // Rus tilidagi nom
    }
    public static function getModelLabel(): string
    {
        return 'Клиенты'; // Rus tilidagi yakka holdagi nom
    }
    public static function getPluralModelLabel(): string
    {
        return 'Клиенты'; // Rus tilidagi ko'plik shakli
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            // 'create' => Pages\CreateClient::route('/create'),
            // 'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
