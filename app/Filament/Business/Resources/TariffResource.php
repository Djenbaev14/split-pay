<?php

namespace App\Filament\Business\Resources;

use App\Filament\Business\Resources\TariffResource\Pages;
use App\Filament\Business\Resources\TariffResource\RelationManagers;
use App\Models\Branch;
use App\Models\Tariff;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TariffResource extends Resource
{
    protected static ?string $model = Tariff::class;

    protected static ?string $navigationIcon = 'fas-percent';
    // protected static ?string $navigationGroup = 'Филиалы';

    protected static ?int $navigationSort = 2;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nomlanishi')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(12),
                        Select::make('branch_id')
                            ->label('Filiallar')
                            ->options(Branch::where('business_id',auth()->user()->business_id)->get()->pluck('name', 'id'))
                            ->required()
                            ->columnSpan(12),
                        Select::make('tariff_type_id')
                            ->label('Turi')
                            ->relationship('tariffType','name')
                            ->required()
                            ->columnSpan(6),
                        Select::make('period_type_id')
                            ->label('Тип периода')
                            ->relationship('PeriodType','name')
                            ->required()
                            ->columnSpan(6),
                        TextInput::make('percentage')
                            ->label('Foiz')
                            ->required()
                            ->numeric()
                            ->columnSpan(6),
                        TextInput::make('grace_period_month')
                            ->label('Imtiyozli davr (oy)')
                            ->required()
                            ->numeric()
                            ->columnSpan(6),
                        TextInput::make('min_amount')
                            ->label('Minimal miqdor')
                            ->required()
                            ->numeric()
                            ->columnSpan(6),
                        TextInput::make('max_amount')
                            ->label('Maksimal miqdor')
                            ->required()
                            ->numeric()
                            ->columnSpan(6),
                        TextInput::make('min_period_month')
                            ->label('Minimal davr')
                            ->required()
                            ->numeric()
                            ->columnSpan(6),
                        TextInput::make('max_period_month')
                            ->label('Maksimal davr')
                            ->required()
                            ->numeric()
                            ->columnSpan(6),
                    ])->columns(12)->columnSpan(12)
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
                        ->modalWidth('lg')
                        ->modalAlignment('end')
                        ->action(function (array $data) {
                            $tariff = Tariff::create([
                                'customer_id' => auth()->user()->id,
                                'business_id' => Branch::find($data['branch_id'])->business_id,
                                'branch_id' => $data['branch_id'],
                                'tariff_type_id' => $data['tariff_type_id'],
                                'period_type_id' => $data['period_type_id'],
                                'name' => $data['name'],
                                'percentage' => $data['percentage'],
                                'grace_period_month' => $data['grace_period_month'],
                                'min_amount' => $data['min_amount'],
                                'max_amount' => $data['max_amount'],
                                'max_period_month' => $data['max_period_month'],
                                'min_period_month'=>$data['min_period_month']
                            ]);
                            Notification::make()
                                ->title('Tarif muvaffaqiyatli yaratildi!')
                                ->success()
                                ->send();
                        })
                ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nomlanishi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Filial')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tariffType.name')
                    ->label('Turi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('percentage')
                    ->label('Foiz')
                    ->formatStateUsing(fn ($record) => $record->percentage . '%')
                    ->sortable(),
                ToggleColumn::make('status')
                ->afterStateUpdated(function ($record, $state) {
                    if ($state) {
                        Notification::make()
                            ->title('Tariff yoqildi')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Tariff o‘chirildi')
                            ->danger()
                            ->send();
                    }
                }),
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
                ->using(function (Tariff $record, array $data): Tariff {
                    // Filial ma'lumotlarini yangilash
                    $record->update([
                        'customer_id' => auth()->user()->id,
                        'business_id' => Branch::find($data['branch_id'])->business_id,
                        'branch_id' => $data['branch_id'],
                        'tariff_type_id' => $data['tariff_type_id'],
                        'period_type_id' => $data['period_type_id'],
                        'name' => $data['name'],
                        'percentage' => $data['percentage'],
                        'grace_period_month' => $data['grace_period_month'],
                        'min_amount' => $data['min_amount'],
                        'max_amount' => $data['max_amount'],
                        'max_period_month' => $data['max_period_month'],
                        'min_period_month'=>$data['min_period_month']
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
    public static function getNavigationLabel(): string
    {
        return 'Тарифы'; // Rus tilidagi nom
    }
    public static function getModelLabel(): string
    {
        return 'Тарифы'; // Rus tilidagi yakka holdagi nom
    }
    public static function getPluralModelLabel(): string
    {
        return 'Тарифы'; // Rus tilidagi ko'plik shakli
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
            'index' => Pages\ListTariffs::route('/'),
            // 'create' => Pages\CreateTariff::route('/create'),
            // 'edit' => Pages\EditTariff::route('/{record}/edit'),
        ];
    }
}
