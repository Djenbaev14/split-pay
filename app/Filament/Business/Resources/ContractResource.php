<?php

namespace App\Filament\Business\Resources;

use App\Filament\Business\Resources\ContractResource\Pages;
use App\Filament\Business\Resources\ContractResource\RelationManagers;
use App\Models\Branch;
use App\Models\District;
use App\Models\PaymentSchedule;
use App\Models\Region;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Tariff;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Контракты';
    public static function form(Form $form): Form
    {
        return $form
            ->schema(fn ($livewire)  => $livewire instanceof CreateRecord ? self::createSchema() : self::editSchema());
    }
    protected static function editSchema(): array
    {
        return [
            Group::make()
                ->schema([
                    Tabs::make('')
                        ->tabs([
                            Tab::make('Umumiy malumotlar')
                                ->schema([
                                ]),
                            Tab::make('Kontaktlar')
                                ->schema([
                                ]),
                            Tab::make('Pochta')
                                ->schema([
                                ]),
                            Tab::make('Hujjatlar')
                                ->schema([
                                ]),
                            Tab::make('Fotohisobolar')
                                ->schema([
                                ]),
                            Tab::make('Kafillar')
                                ->schema([
                                ]),
                            Tab::make('Sipuni')
                                ->schema([
                                ]),
                        ])->columnSpan(6)->columns(6),
                    Section::make('Shartnoma haqida ma’lumot')
                        ->schema([
                            Placeholder::make('created_at')
                                ->label(false)
                                ->content(fn ($record) => view('inline-label-value', [
                                    'label' => 'Yaratilgan',
                                    'value' => $record->created_at->format('Y-m-d H:i:s'),
                                ]))
                                ->columnSpan(6),
                            Placeholder::make('amount')
                                ->label(false)
                                ->content(fn ($record) => view('inline-label-value', [
                                    'label' => 'Miqdori',
                                    'value' => number_format($record->amount) .' сум',
                                ]))
                                ->columnSpan(6),
                            Placeholder::make('down_payment')
                                ->label(false)
                                ->content(fn ($record) => view('inline-label-value', [
                                    'label' => "Dastlabki to'lov",
                                    'value' => number_format($record->down_payment) .' сум',
                                ]))
                                ->columnSpan(6),
                            Placeholder::make('tariff_id')
                                ->label(false)
                                ->content(fn ($record) => view('inline-label-value', [
                                    'label' => 'Tarif',
                                    'value' => Tariff::find($record->tariff_id)->name,
                                ]))
                                ->columnSpan(6),
                            Placeholder::make('period_month')
                                ->label(false)
                                ->content(fn ($record) => view('inline-label-value', [
                                    'label' => 'Davr',
                                    'value' => $record->period_month,
                                ]))
                                ->columnSpan(6),
                            Placeholder::make('percentage')
                                ->label(false)
                                ->content(fn ($record) => view('inline-label-value', [
                                    'label' => 'Foiz(oy)',
                                    'value' => Tariff::find($record->tariff_id)->percentage.' %',
                                ]))
                                ->columnSpan(6),
                            Placeholder::make('period_month')
                                ->label(false)
                                ->content(fn ($record) => view('inline-label-value', [
                                    'label' => 'Kompaniya',
                                    'value' => $record->period_month,
                                ]))
                                ->columnSpan(6),
                            Placeholder::make('period_month')
                                ->label(false)
                                ->content(fn ($record) => view('inline-label-value', [
                                    'label' => 'Davr',
                                    'value' => $record->period_month,
                                ]))
                                ->columnSpan(6),
                        ])->columnSpan(6)->columns(12)
                ])->columnSpan(12)->columns(12)
        ];
    }

    public static function createSchema(): array
    {
        return [
                Grid::make(3)
                    ->schema([
                        Wizard::make([
                                Step::make('Подробности договора')
                                    ->schema([
                                        Section::make('')
                                            ->schema([
                                            Select::make('branch_id')
                                                ->label('Filiallar')
                                                ->options(Branch::where('business_id',auth()->user()->business_id)->get()->pluck('name', 'id'))
                                                ->afterStateUpdated(fn (callable $set) => $set('tariff_id', null))
                                                ->reactive()
                                                ->columnSpan(6),
                                            Select::make('tariff_id')
                                                ->label('Tariff')
                                                ->options( fn (callable $get) => 
                                                    Tariff::where('branch_id',$get('branch_id'))->get()->pluck('name', 'id'))
                                                ->reactive()
                                                ->disabled(fn (callable $get) => empty($get('branch_id'))) 
                                                ->columnSpan(6),
                                            TextInput::make('amount')
                                                ->label('Miqdori')
                                                ->columnSpan(6)
                                                ->reactive()
                                                ->numeric(),
                                            TextInput::make('down_payment')
                                                ->label("Dastlabki to'lov")
                                                ->columnSpan(6)
                                                ->reactive()
                                                ->numeric(),
                                            TextInput::make('payment_day')
                                                ->label("To'lov kuni")
                                                ->columnSpan(6)
                                                ->reactive()
                                                ->numeric(),
                                            TextInput::make('period_month')
                                                ->label("Davr")
                                                ->columnSpan(6)
                                                ->reactive()
                                                ->numeric(),
                                            TextInput::make('product')
                                                ->label('Mahsulotlarga izohlar')
                                                ->columnSpan(6)
                                                ->maxLength(255),
                                            Textarea::make('comment')
                                                ->label('Sharh')
                                                ->columnSpanFull(),
                                        ])->columnSpan(8)->columns(12),
                                            
                                    Section::make('Tarif haqida ma’lumot:')
                                        ->schema([
                                            Placeholder::make('tariff_name')
                                                ->label('Nomlanishi')
                                                ->content(fn (Get $get) => Tariff::find($get('tariff_id'))->name ?? '-')->columnSpan(6),
                    
                                            Placeholder::make('tariff_type')
                                                ->label('Turi')
                                                ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->tariffType->name ?? '-')->columnSpan(6),
                    
                                            Placeholder::make('percentage')
                                                ->label('Foiz')
                                                ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->percentage . '%' ?? '-')->columnSpan(6),
                    
                                            Placeholder::make('grace_period_month')
                                                ->label('Imtiyozli davr (oy)')
                                                ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->grace_period_month . ' oy' ?? '-')->columnSpan(6),
                    
                                            Placeholder::make('min_period_month')
                                                ->label('Minimal davr')
                                                ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->min_period_month ?? '-')->columnSpan(6),
                    
                                            Placeholder::make('max_period_month')
                                                ->label('Maksimal davr')
                                                ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->max_period_month ?? '-')->columnSpan(6),
                    
                                            Placeholder::make('min_amount')
                                                ->label('Minimal miqdor')
                                                ->content(fn (Get $get) => number_format(Tariff::find($get('tariff_id'))?->min_amount). ' swm' ?? '-')->columnSpan(6),
                    
                                            Placeholder::make('max_amount')
                                                ->label('Maksimal miqdor')
                                                ->content(fn (Get $get) => number_format(Tariff::find($get('tariff_id'))?->max_amount) . ' swm' ?? '-')->columnSpan(6),
                                            ])
                                        ->columnSpan(4)->columns(12), // **O‘ng tomonda 1 ustun egallaydi**
                                    ]),
                            Step::make('Информация о клиенте')
                            ->schema([
                                Group::make()
                                    ->columnSpan(8)
                                    ->columns(8)
                                    ->schema([
                                        Section::make()
                                            ->schema([
                                                TextInput::make('passport_series')
                                                    ->label('Pasport seriyasi')
                                                    ->extraAttributes([
                                                        'x-on:input' => "event.target.value = event.target.value.toUpperCase()"
                                                    ])
                                                    ->columnSpan(3),
                                                TextInput::make('passport_number')
                                                    ->label('Pasport raqami')
                                                    ->numeric()
                                                    // ->required()
                                                    ->reactive()
                                                    ->suffixAction(
                                                        Action::make('search_client')
                                                            ->icon('heroicon-o-magnifying-glass')
                                                            ->action(fn ($get, $set) => self::searchClient($get, $set))
                                                    )
                                                    ->columnSpan(6),
                                                Hidden::make('client_id'),
                                                Hidden::make('customer_id')->default(auth()->user()->id),
                                                Hidden::make('business_id')->default(auth()->user()->business_id),
                                                Placeholder::make('fios')
                                                    // ->label('Документ: ')
                                                    ->label(fn (Get $get) => Client::find($get('client_id')) ? 'Документ: '.Client::find($get('client_id'))->last_name .' '. Client::find($get('client_id'))->first_name.' '.Client::find($get('client_id'))->patronymic : 'Документ:')->columnSpan(12),
                                            ])->columns(12),
                                            Section::make("Qo'shimcha ma'lumot")
                                                ->relationship('contractDetail') // 1:1 bog‘lanish
                                                ->schema([
                                                    Select::make('region_id')
                                                        ->label('Regionlar')
                                                        ->options(Region::get()->pluck('name', 'id'))
                                                        ->afterStateUpdated(fn (callable $set) => $set('district_id', null))
                                                        ->reactive()
                                                        ->searchable()
                                                        ->columnSpan(12),
                                                    Select::make('district_id')
                                                        ->label('Tumanlar')
                                                        ->searchable()
                                                        ->options( fn (callable $get) => 
                                                            District::where('region_id',$get('region_id'))->get()->pluck('name', 'id'))
                                                        ->reactive()
                                                        ->disabled(fn (callable $get) => empty($get('region_id'))) 
                                                        ->columnSpan(12),
                                                    TextInput::make('address')
                                                        ->label('Yashash manzili')
                                                        ->columnSpan(12),
                                                    TextInput::make('mfy_address')
                                                        ->label('MFY manzili')
                                                        ->columnSpan(12),
                                                    TextInput::make('passport_address')
                                                        ->label('Pasport manzili')
                                                        ->columnSpan(12),
                                                    TextInput::make('position')
                                                        ->label('Lavozim')
                                                        ->columnSpan(12),
                                                    TextInput::make('workplace')
                                                        ->label('Ish joyi')
                                                        ->columnSpan(12),
                                                    Repeater::make('phones')
                                                            ->schema([
                                                                TextInput::make('phone')
                                                                    ->label('Telefon')
                                                                    ->placeholder('Telefon raqamini kiriting')
                                                                    ->mask('+998999999999'), // Telefon formatini cheklash uchun
                                                            ])
                                                            ->label('Telefonlar')
                                                            ->addable(true) // "+" tugmasini qo'shish
                                                            ->deletable(true) // O'chirish tugmasini qo'shish
                                                            ->columnSpan(12)
                                            ])->columns(12)
                                ]),
                                
                            Section::make("Hujjat haqida ma'lumot:")
                                ->schema([
                                    Placeholder::make('fio')
                                        ->label('FIO')
                                        ->content(fn (Get $get) => Client::find($get('client_id')) ? Client::find($get('client_id'))->last_name .' '. Client::find($get('client_id'))->first_name.' '.Client::find($get('client_id'))->patronymic : '-')->columnSpan(12),
            
                                    Placeholder::make('inn')
                                        ->label('INN')
                                        ->content(fn (Get $get) => Client::find($get('client_id'))->inn ?? '-')->columnSpan(6),
            
                                    Placeholder::make('pinfl')
                                        ->label('PINFL')
                                        ->content(fn (Get $get) => Client::find($get('client_id'))->pinfl ?? '-')->columnSpan(6),
            
                                    Placeholder::make('gender')
                                        ->label('Jinsi')
                                        ->content(fn (Get $get) => Client::find($get('client_id')) ? (Client::find($get('client_id'))->gender == 'male' ?'Erkak':'Ayol') :'-')->columnSpan(6),
            
                                    Placeholder::make('birthday')
                                        ->label('Ismi')
                                        ->content(fn (Get $get) => Client::find($get('client_id'))->birthday ?? '-')->columnSpan(6),
            
                                    Placeholder::make('birthplace')
                                        ->label("Tug'ilgan joyi")
                                        ->content(fn (Get $get) => Client::find($get('client_id'))->birthplace ?? '-')->columnSpan(6),
            
                                    ])
                                    ->columns(12)->columnSpan(4), // **O‘ng tomonda 1 ustun egallaydi**
                            ]), 
                        Step::make('Банковские карты')
                            ->schema([
                                Tabs::make('Tabs')
                                    ->tabs([
                                        Tabs\Tab::make('Uzcard')
                                            ->schema([
                                                Group::make()
                                                    ->relationship('contractCards') 
                                                    ->schema([
                                                        TextInput::make('car_number')
                                                            ->label('Karta raqami')
                                                            ->placeholder('Karta raqami')
                                                            ->mask('9999-9999-9999-9999')
                                                            ->columnSpan(12),
                                                        TextInput::make('expiry_date')
                                                            ->label('Amal qilish muddati')
                                                            ->placeholder('Amal qilish muddati')
                                                            ->mask('99/99')
                                                            ->columnSpan(12),
                                                        TextInput::make('phone')
                                                            ->tel()
                                                            ->label('Telefon')
                                                            ->placeholder('Telefon')
                                                            ->mask('+998999999999')
                                                            ->columnSpan(12),
                                                        Hidden::make('card_id')->default(1)
                                                    ])->columns(8)->columnSpan(12)
                                            ]),
                                        // Tabs\Tab::make('Atmos')
                                        //     ->schema([
                                        //         Group::make()
                                        //             ->schema([
                                        //                 TextInput::make('atmos_car_number')
                                        //                     ->label('Karta raqami')
                                        //                     ->placeholder('Karta raqami')
                                        //                     ->mask('9999-9999-9999-9999')
                                        //                     ->columnSpan(12),
                                        //                 TextInput::make('atmos_expiry_date')
                                        //                     ->label('Amal qilish muddati')
                                        //                     ->placeholder('Amal qilish muddati')
                                        //                     ->mask('99/99')
                                        //                     ->columnSpan(12),
                                        //             ])->columns(8)->columnSpan(12)
                                        //     ]),
                                    ])->columnSpan(8)->columns(8),
                                    Group::make()
                                    ->schema([
                                        Section::make('Tarif haqida ma’lumot:')
                                            ->schema([
                                                Placeholder::make('tariff_name')
                                                    ->label('Nomlanishi')
                                                    ->content(fn (Get $get) => Tariff::find($get('tariff_id'))->name ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('tariff_type')
                                                    ->label('Turi')
                                                    ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->tariffType->name ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('percentage')
                                                    ->label('Foiz')
                                                    ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->percentage . '%' ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('grace_period_month')
                                                    ->label('Imtiyozli davr (oy)')
                                                    ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->grace_period_month . ' oy' ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('min_period_month')
                                                    ->label('Minimal davr')
                                                    ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->min_period_month ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('max_period_month')
                                                    ->label('Maksimal davr')
                                                    ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->max_period_month ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('min_amount')
                                                    ->label('Minimal miqdor')
                                                    ->content(fn (Get $get) => number_format(Tariff::find($get('tariff_id'))?->min_amount). ' swm' ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('max_amount')
                                                    ->label('Maksimal miqdor')
                                                    ->content(fn (Get $get) => number_format(Tariff::find($get('tariff_id'))?->max_amount) . ' swm' ?? '-')->columnSpan(6),
                                                ])
                                                ->columns(12)->columnSpan(1), // **O‘ng tomonda 1 ustun egallaydi**
                                        Section::make("Hujjat haqida ma'lumot:")
                                            ->schema([
                                                Placeholder::make('fio')
                                                    ->label('FIO')
                                                    ->content(fn (Get $get) => Client::find($get('client_id')) ? Client::find($get('client_id'))->last_name .' '. Client::find($get('client_id'))->first_name.' '.Client::find($get('client_id'))->patronymic : '-')->columnSpan(12),
                        
                                                Placeholder::make('inn')
                                                    ->label('INN')
                                                    ->content(fn (Get $get) => Client::find($get('client_id'))->inn ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('pinfl')
                                                    ->label('PINFL')
                                                    ->content(fn (Get $get) => Client::find($get('client_id'))->pinfl ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('gender')
                                                    ->label('Jinsi')
                                                    ->content(fn (Get $get) => Client::find($get('client_id')) ? (Client::find($get('client_id'))->gender == 'male' ?'Erkak':'Ayol') :'-')->columnSpan(6),
                        
                                                Placeholder::make('birthday')
                                                    ->label('Ismi')
                                                    ->content(fn (Get $get) => Client::find($get('client_id'))->birthday ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('birthplace')
                                                    ->label("Tug'ilgan joyi")
                                                    ->content(fn (Get $get) => Client::find($get('client_id'))->birthplace ?? '-')->columnSpan(6),
                        
                                                ])
                                                ->columns(12)->columnSpan(1), // **O‘ng tomonda 1 ustun egallaydi**
                                        ])->columnSpan(4),
                            ]), // Chap tomonda turadi
                        Step::make('Создание контракта')
                            // ->icon('fas-address-card')
                            // ->completedIcon('fas-address-card')
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Section::make('')
                                            ->schema([
                                                    Placeholder::make('first_name')
                                                        ->label('Ism:')
                                                        ->content(fn (Get $get) => Client::find($get('client_id')) ? Client::find($get('client_id'))->first_name : '-')->columnSpan(4),
                            
                                                    Placeholder::make('last_name')
                                                        ->label('Familiya:')
                                                        ->content(fn (Get $get) => Client::find($get('client_id')) ? Client::find($get('client_id'))->last_name : '-')->columnSpan(4),
                            
                                                    Placeholder::make('first_name')
                                                        ->label('Otasning ismi:')
                                                        ->content(fn (Get $get) => Client::find($get('client_id')) ? Client::find($get('client_id'))->patronymic : '-')->columnSpan(4),
                            
                                                    Placeholder::make('birthplace')
                                                        ->label("Tug'ilgan kuni:")
                                                        ->content(fn (Get $get) => Client::find($get('client_id'))->birthplace ?? '-')->columnSpan(4),
                            
                                                    Placeholder::make('passport_series')
                                                        ->label('Pasport seriyasi:')
                                                        ->content(fn (Get $get) => Client::find($get('client_id'))->passport_series ?? '-')->columnSpan(4),
                            
                                                    Placeholder::make('passport_number')
                                                        ->label('Pasport raqami:')
                                                        ->content(fn (Get $get) => Client::find($get('client_id')) ? (Client::find($get('client_id'))->passport_number) :'-')->columnSpan(4),
                                                    Placeholder::make('gender')
                                                        ->label('Jinsi')
                                                        ->content(fn (Get $get) => Client::find($get('client_id')) ? (Client::find($get('client_id'))->gender == 'male' ?'Erkak':'Ayol') :'-')->columnSpan(4),
                                                    Placeholder::make('passport_date_issue')
                                                        ->label('Pasport berilgan sana:')
                                                        ->content(fn (Get $get) => Client::find($get('client_id'))->passport_date_issue ?? '-')->columnSpan(4),
                                                    Placeholder::make('passport_date_expiration')
                                                        ->label("Pasportning amal qilish muddati:")
                                                        ->content(fn (Get $get) => Client::find($get('client_id'))->passport_date_expiration ?? '-')->columnSpan(4),
                                                ])
                                                ->columns(columns: 12)->columnSpan(1), // **O‘ng tomonda 1 ustun egallaydi**
                                            
                                        Section::make('')
                                        ->schema([
                                                Placeholder::make('place_address')
                                                    ->label('Yashash manzili:')
                                                    ->content(fn (Get $get) => $get('address') ?? '-')->columnSpan(4),
                                                Placeholder::make('place_mfy_address')
                                                    ->label('MFY manzili:')
                                                    ->content(fn (Get $get) => $get('mfy_address') ?? '-')->columnSpan(4),
                                                Placeholder::make('place_passport_address')
                                                    ->label("Passport bo'yicha manzil:")
                                                    ->content(fn (Get $get) => $get('passport_address') ?? '-')->columnSpan(4),
                                                Placeholder::make('birthday')
                                                    ->label("Tug'ilgan joyi:")
                                                    ->content(fn (Get $get) => Client::find($get('client_id') )->birthplace ?? '')->columnSpan(4),
                                                Placeholder::make('inn')
                                                    ->label('INN:')
                                                    ->content(fn (Get $get) => Client::find($get('client_id'))->inn ?? '')->columnSpan(4),
                                                Placeholder::make('pinfl')
                                                    ->label('PINFL:')
                                                    ->content(fn (Get $get) => Client::find($get('client_id'))->pinfl ?? '')->columnSpan(4),
                                                Placeholder::make('place_phone')
                                                    ->label('Telefon:')
                                                    ->content(fn (Get $get) => $get('phone') ?? '-')->columnSpan(4),
                                                Placeholder::make('place_position')
                                                    ->label('Lavozim:')
                                                    ->content(fn (Get $get) => $get('position') ?? '-')->columnSpan(4),
                                                Placeholder::make('place_workplace')
                                                    ->label('Ish joyi:')
                                                    ->content(fn (Get $get) => $get('workplace') ?? '-')->columnSpan(4),
                        
                                            ])
                                            ->columns(columns: 12)->columnSpan(1), // **O‘ng tomonda 1 ustun egallaydi**
                                             
                                        Section::make('Shartnoma tafsilotlari:')
                                            ->schema([
                                                Placeholder::make('place_branch_id')
                                                    ->label('Kompaniya')
                                                    ->content(fn (Get $get) => Branch::find($get('branch_id'))->name ?? '-')->columnSpan(4),
                        
                                                Placeholder::make('place_xodim')
                                                    ->label('Xodim')
                                                    ->content(fn (Get $get) => auth()->user()->name ?? '-')->columnSpan(4),
                        
                                                Placeholder::make('place_amount')
                                                    ->label('Miqdori')
                                                    ->content(fn (Get $get) => number_format($get('amount'))  ?? '-')->columnSpan(4),
                        
                                                Placeholder::make('place_grace_period_month')
                                                    ->label("Dastlabki to'lov:")
                                                    ->content(fn (Get $get) => number_format($get('down_payment'))  ?? '-')->columnSpan(4),
                        
                                                Placeholder::make('place_payment_day')
                                                    ->label("To'lov kuni")
                                                    ->content(fn (Get $get) => $get('payment_day')  ?? '-')->columnSpan(4),
                        
                                                Placeholder::make('place_period_month')
                                                    ->label('Davr')
                                                    ->content(fn (Get $get) => $get('period_month')  ?? '-')->columnSpan(4),
                        
                                                Placeholder::make('place_region')
                                                    ->label('Region:')
                                                    ->content(fn (Get $get) => Region::find($get('region_id')->name ?? ''))->columnSpan(4),
                                                Placeholder::make('place_district')
                                                    ->label('Tuman:')
                                                    ->content(fn (Get $get) => District::find($get('district_id')->name ?? ''))->columnSpan(4),
                                                Placeholder::make('place_product')
                                                    ->label('Mahsulotlarga izohlar:')
                                                    ->content(fn (Get $get) => $get('product')  ?? '-')->columnSpan(4),
                                                Placeholder::make('place_comment')
                                                    ->label('Sharh')
                                                    ->content(fn (Get $get) => $get('comment')  ?? '-')->columnSpan(4),
                                                ])
                                                ->columns(12)->columnSpan(1), // **O‘ng tomonda 1 ustun egallaydi**
                                        ])->columnSpan(8),
                                        
                                        Section::make('Tarif haqida ma’lumot:')
                                            ->schema([
                                                Placeholder::make('tariff_name')
                                                    ->label('Nomlanishi')
                                                    ->content(fn (Get $get) => Tariff::find($get('tariff_id'))->name ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('tariff_type')
                                                    ->label('Turi')
                                                    ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->tariffType->name ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('percentage')
                                                    ->label('Foiz')
                                                    ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->percentage . '%' ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('grace_period_month')
                                                    ->label('Imtiyozli davr (oy)')
                                                    ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->grace_period_month . ' oy' ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('min_period_month')
                                                    ->label('Minimal davr')
                                                    ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->min_period_month ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('max_period_month')
                                                    ->label('Maksimal davr')
                                                    ->content(fn (Get $get) => Tariff::find($get('tariff_id'))?->max_period_month ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('min_amount')
                                                    ->label('Minimal miqdor')
                                                    ->content(fn (Get $get) => number_format(Tariff::find($get('tariff_id'))?->min_amount). ' swm' ?? '-')->columnSpan(6),
                        
                                                Placeholder::make('max_amount')
                                                    ->label('Maksimal miqdor')
                                                    ->content(fn (Get $get) => number_format(Tariff::find($get('tariff_id'))?->max_amount) . ' swm' ?? '-')->columnSpan(6),
                                                ])
                                                ->columns(12)->columnSpan(4), // **O‘ng tomonda 1 ustun egallaydi**
                            ])
                        ])->columnSpan(3)->columns(12),
                    ])
            ];
    }

    public static function searchClient($get,$set)
    {
        if (strlen($get('passport_number')) !== 7) {
            Notification::make()
                ->title('Xatolik!')
                ->danger()
                ->body('Pasport raqami 7 ta raqamdan iborat bo‘lishi kerak!')
                ->send();
            return;
        }

        $client = Client::where('passport_series', $get('passport_series'))
            ->where('passport_number', $get('passport_number'))
            ->first();

        if ($client) {
            $set('client_id', $client->id);
            $set('first_name', $client->first_name);
            Notification::make()
                ->title('Mijoz topildi!')
                ->success()
                ->body("Mijoz ma'lumotlari yuklandi.")
                ->send();
        } else {
            $set('client_id', null);
            Notification::make()
                ->title('Xatolik!')
                ->danger()
                ->body("Bu pasport ma’lumotlari topilmadi.")
                ->send();
        }
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('branch.name')
                    ->label('Kompaniya nomi'),
                TextColumn::make('client')
                    ->label('FIO')
                    ->formatStateUsing(function ($state) {
                        return $state->first_name . ' '. $state->last_name.' '.$state->patronymic; // Masalan, 1000.50 ni 1,000.50 formatida
                    }),
                TextColumn::make('debt')
                    ->label('Qiymat')
                    ->getStateUsing(function (Contract $record) {
                        return number_format($record->amount- $record->down_payment, 0, ',', ' ') . ' сум';
                    }),
                TextColumn::make('created_at')
                    ->label('Yaratilgan sana')
                    ->dateTime(),
                TextColumn::make('period_month')
                    ->label('Muddati (oy)'),
            ])
            ->defaultSort('id','desc')
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
    public static function getNavigationLabel(): string
    {
        return 'Контракты'; // Rus tilidagi nom
    }
    public static function getModelLabel(): string
    {
        return 'Контракты'; // Rus tilidagi yakka holdagi nom
    }
    public static function getPluralModelLabel(): string
    {
        return 'Контракты'; // Rus tilidagi ko'plik shakli
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
