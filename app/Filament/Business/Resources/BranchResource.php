<?php

namespace App\Filament\Business\Resources;

use App\Filament\Business\Resources\BranchResource\Pages;
use App\Filament\Business\Resources\BranchResource\RelationManagers;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;
    
    protected static ?string $navigationGroup = 'Филиалы';

    protected static ?int $navigationSort = 1;
    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make("Filial ma'lumotlari")
                    ->schema([
                        Hidden::make('business_id')
                            ->default(fn () => auth()->user()->business_id)
                            ->dehydrated(true),
                        TextInput::make('name')
                                ->label('Nomlanishi')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(6),
                        TextInput::make('company_name')
                                ->label('Kompaniya')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(6),
                        TextInput::make('address')
                                ->label('Manzil')
                                ->columnSpan(6),
                        TextInput::make('telegram_phone')
                                ->label('Telegramdagi telefon')
                                ->tel()
                                ->maxLength(255)
                                ->columnSpan(6),
                        FileUpload::make('logo')
                                ->label('Logotip')
                                ->image()
                                ->disk('public') 
                                ->directory('logos')
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ])
                                ->columnSpan(4),
                        Textarea::make('comment')
                                ->label('Sharh')
                                ->rows(3)
                                ->columnSpan(8),
                    ])
                    ->columnSpan(12)
                    ->columns(12),
                    // Bank ma'lumotlari
                Forms\Components\Section::make('Bank ma\'lumotlari')
                ->schema([
                    Forms\Components\TextInput::make('bankInfo.bank')
                        ->label('Bank nomi')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('bankInfo.inn')
                        ->label('INN')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('bankInfo.mfo')
                        ->label('MFO')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('bankInfo.payment_account')
                        ->label('Hisob raqami')
                        ->required()
                        ->maxLength(255),
                    Hidden::make('bankInfo.business_id')
                        ->default(fn () => auth()->user()->business_id)
                        ->dehydrated(true),
                ])
                ->columns(2)->columnSpan(12),
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
                        ->modalWidth(MaxWidth::ThreeExtraLarge)
                        ->modalAlignment('end')
                        ->slideOver()
                        ->action(function (array $data) {
                            // Filialni yaratish
                            $branch = Branch::create([
                                'customer_id' => auth()->user()->id,
                                'business_id' => $data['business_id'],
                                'name' => $data['name'],
                                'company_name' => $data['company_name'],
                                'address' => $data['address'],
                                'telegram_phone' => $data['telegram_phone'],
                                'logo' => $data['logo'],
                                'comment' => $data['comment'],
                            ]);

                            // Bank ma'lumotini yaratish
                            $branch->bankInfo()->create([
                                'business_id' => $data['bankInfo']['business_id'],
                                'bank' => $data['bankInfo']['bank'],
                                'inn' => $data['bankInfo']['inn'],
                                'mfo' => $data['bankInfo']['mfo'],
                                'payment_account' => $data['bankInfo']['payment_account'],
                            ]);

                            Notification::make()
                                ->title('Filial muvaffaqiyatli yaratildi!')
                                ->success()
                                ->send();
                        })
                ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Kompaniya')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bankInfo.inn')
                    ->label('INN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telegram_phone')
                    ->label('Telefon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yaratilgan vaqti')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->using(function (Branch $record, array $data): Branch {
                    // Filial ma'lumotlarini yangilash
                    $record->update([
                        'business_id' => $data['business_id'],
                        'name' => $data['name'],
                        'company_name' => $data['company_name'],
                        'address' => $data['address'],
                        'telegram_phone' => $data['telegram_phone'],
                        'logo' => $data['logo'],
                        'comment' => $data['comment'],
                    ]);

                    // Bank ma'lumotlarini yangilash yoki yaratish
                    $record->bankInfo()->updateOrCreate(
                        ['branch_id' => $record->id],
                        [
                            'business_id' => $data['bankInfo']['business_id'],
                            'bank' => $data['bankInfo']['bank'],
                            'inn' => $data['bankInfo']['inn'],
                            'mfo' => $data['bankInfo']['mfo'],
                            'payment_account' => $data['bankInfo']['payment_account'],
                        ]
                    );

                    Notification::make()
                        ->title('Filial muvaffaqiyatli tahrirlandi!')
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
        return 'Филиалы'; // Rus tilidagi nom
    }
    public static function getModelLabel(): string
    {
        return 'Филиалы'; // Rus tilidagi yakka holdagi nom
    }
    public static function getPluralModelLabel(): string
    {
        return 'Филиалы'; // Rus tilidagi ko'plik shakli
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            // 'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
