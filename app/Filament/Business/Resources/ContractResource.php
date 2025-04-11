<?php

namespace App\Filament\Business\Resources;

use App\Filament\Business\Resources\ContractResource\Pages;
use App\Filament\Business\Resources\ContractResource\RelationManagers;
use App\Models\Branch;
use App\Models\ClientContact;
use App\Models\ContractCard;
use App\Models\District;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentSchedule;
use App\Models\PaymentTransaction;
use App\Models\Region;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Tariff;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Actions;
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
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\View;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Response;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;
    

    protected static ?string $navigationIcon = 'fas-file-signature';
    // protected static ?string $navigationGroup = 'Контракты';
    public static function form(Form $form): Form
{
    return $form->schema(fn ($livewire) => 
        $livewire instanceof CreateRecord
            ? self::createSchema()
            : self::editSchema()
    );
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
                                    Placeholder::make('last_name')
                                        ->label(false)
                                        ->content(fn ($record) => view('contract.contract-status', [
                                            'contract' => $record,
                                        ]))
                                        ->columnSpan(12),
                                    Placeholder::make('last_name')
                                        ->label('Familiya')
                                        ->content(fn ($record) => $record->client->last_name)
                                        ->columnSpan(4),
                                    Placeholder::make('first_name')
                                        ->label('Ism')
                                        ->content(fn ($record) => $record->client->first_name)
                                        ->columnSpan(4),
                                    Placeholder::make('patronymic')
                                        ->label('Otasining ismi')
                                        ->content(fn ($record) => $record->client->patronymic)
                                        ->columnSpan(4),
                                    Placeholder::make('birthday')
                                        ->label("Tug'ilgan kuni")
                                        ->content(fn ($record) => $record->client->birthday)
                                        ->columnSpan(4),
                                    Placeholder::make('passport')
                                        ->label('Passport')
                                        ->content(fn ($record) => $record->client->passport_series.''.$record->client->passport_number)
                                        ->columnSpan(4),
                                    Placeholder::make('gender')
                                        ->label('Jinsi')
                                        ->content(fn ($record) => $record->client->gender == 'male' ? 'Erkak':'Ayol')
                                        ->columnSpan(4),
                                    Placeholder::make('birthplace')
                                        ->label("Tug'ilgan joyi")
                                        ->content(fn ($record) => $record->client->birthplace)
                                        ->columnSpan(4),
                                    Placeholder::make('inn')
                                        ->label('INN')
                                        ->content(fn ($record) => $record->client->inn)
                                        ->columnSpan(4),
                                    Placeholder::make('gender')
                                        ->label('PINFL')
                                        ->content(fn ($record) => $record->client->pinfl)
                                        ->columnSpan(4),
                                    Placeholder::make('passport_address')
                                        ->label("Passport bo'yicha manzil")
                                        ->content(fn ($record) => $record->contractDetail->passport_address)
                                        ->columnSpan(4),
                                    Placeholder::make('address')
                                        ->label('Yashash manzili')
                                        ->content(fn ($record) => $record->contractDetail->address)
                                        ->columnSpan(4),
                                    Placeholder::make('mfy_address')
                                        ->label('MFY manzili')
                                        ->content(fn ($record) => $record->contractDetail->mfy_address)
                                        ->columnSpan(4),
                                    Placeholder::make('workplace')
                                        ->label("Ish joyi")
                                        ->content(fn ($record) => $record->contractDetail->workplace)
                                        ->columnSpan(4),
                                    Placeholder::make('reyting')
                                        ->label('Mijoz reytingi')
                                        ->content(fn ($record) => $record->contractDetail->reyting)
                                        ->columnSpan(4),
                                    Placeholder::make('phones')
                                        ->label('Telefonlar')
                                        // ->content(fn ($record) => $record->contractDetail->phones)
                                        ->columnSpan(4),
                                    Actions::make([
                                        Action::make('contract_download')
                                            ->label(false)
                                            ->tooltip('Shartnomani yuklab olish')
                                            ->icon('fas-download')
                                            ->action(fn ($record) => static::generateWordFile($record))
                                            ->color('primary')
                                            ->extraAttributes(['style' => 'border-radius: 100%; padding: 10px 5px 10px 10px;'])
                                    ])->columnSpan(6),
                                    Group::make()
                                        ->schema([
                                        Actions::make([
                                            Action::make('add_contact')
                                                ->label("Karta qo'shish")
                                                ->icon('fas-plus')
                                                ->button()
                                                ->color('primary')
                                                ->modalWidth('lg')
                                                ->modalHeading("Karta qo'shish")
                                                ->modalSubmitActionLabel('Saqlash')
                                                ->visible(fn ($record) => ($record->status->key != 'cancelled'))
                                                ->modalCancelActionLabel('Bekor qilish')
                                                ->form([
                                                    Tabs::make('Tabs')
                                                        ->tabs([
                                                            Tabs\Tab::make('Uzcard')
                                                                ->schema([
                                                                    Group::make()
                                                                        ->schema([
                                                                            TextInput::make('car_number')
                                                                                ->label('Karta raqami')
                                                                                ->placeholder('Karta raqami')
                                                                                ->mask('9999-9999-9999-9999')
                                                                                ->nullable()
                                                                                ->columnSpan(12),
                                                                            TextInput::make('expiry_date')
                                                                                ->label('Amal qilish muddati')
                                                                                ->placeholder('Amal qilish muddati')
                                                                                ->nullable()
                                                                                ->mask('99/99')
                                                                                ->columnSpan(12),
                                                                            TextInput::make('phone')
                                                                                ->tel()
                                                                                ->label('Telefon')
                                                                                ->placeholder('Telefon')
                                                                                ->mask('+998999999999')
                                                                                ->nullable()
                                                                                ->columnSpan(12),
                                                                            Hidden::make('card_id')->default(1)
                                                                        ])->columns(8)->columnSpan(12)
                                                                ]),
                                                        ])->columnSpan(8)->columns(8),
                                                ])
                                                ->action(function (array $data,$record) {
                                                    ContractCard::create([
                                                        'contract_id' => $record->id,
                                                        'car_number' => $data['car_number'],
                                                        'expiry_date' => $data['expiry_date'],
                                                        'phone' => $data['phone'],
                                                        'card_id' => $data['card_id'],
                                                    ]);
                                                })
                                            ])->columnSpan(3),
                                        Placeholder::make('card')
                                            ->label('')
                                            ->content(fn ($record) => view('contract.plastik-card', [
                                                'cards' => $record->contractCards,
                                            ]))
                                            ->columnSpan(9),
                                        ])->columnSpan(12)->columns(12)
                                ]),
                            Tab::make('Kontaktlar')
                                ->schema([
                                    Placeholder::make('client-contacts')
                                        ->label(false)
                                        ->content(fn ($record) => view('contract.client-contacts', [
                                            'label' => 'Yaratilgan',
                                            'clientContacts' => $record->client->ClientContacts,
                                        ]))
                                    ->columnSpan(12),
                                    Actions::make([
                                        Action::make('add_contact')
                                            ->label('Yangi kontakt qo‘shish')
                                            ->icon('heroicon-o-plus')
                                            ->button()
                                            ->color('primary')
                                            ->modalWidth('lg')
                                            ->modalHeading('Yangi kontakt qo‘shish')
                                            ->modalSubmitActionLabel('Saqlash')
                                            ->modalCancelActionLabel('Bekor qilish')
                                            ->form([
                                                Forms\Components\TextInput::make('fio')
                                                    ->label('FIO')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('phone')
                                                    ->label('Telefon raqami')
                                                    ->tel()
                                                    ->required()
                                                    ->maxLength(20),
                                                Forms\Components\TextInput::make('relation')
                                                    ->label('Munosabat')
                                                    ->required()
                                                    ->maxLength(20),
                                            ])
                                            ->action(function (array $data,$record) {
                                                ClientContact::create([
                                                    'client_id' => $record->client_id,
                                                    'fio' => $data['fio'],
                                                    'phone' => $data['phone'],
                                                    'relation' => $data['relation'],
                                                ]);
                                            }),
                                    ])->columnSpan(12),
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
                        ])->columnSpan(6)->columns(12),
                    Section::make('Shartnoma haqida ma’lumot')
                        ->schema([
                            Placeholder::make('created_at')
                                ->label(false)
                                ->content(fn ($record) => view('inline-label-value', [
                                    'label' => 'Yaratilgan',
                                    'value' => $record->created_at->format('Y-m-d H:i'),
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
                                    'value' => $record->branch->name,
                                ]))
                                ->columnSpan(6),
                            Placeholder::make('xodim')
                                ->label(false)
                                ->content(fn ($record) => view('inline-label-value', [
                                    'label' => 'Xodim',
                                    'value' => $record->customer->name,
                                ]))
                                ->columnSpan(6),
                            Placeholder::make('overdue_debts')
                                ->label(false)
                                ->content(fn ($record) => view('inline-label-value', [
                                    'label' => "Muddati o'tgan qarzlar",
                                    'value' => '',
                                ]))
                                ->columnSpan(6),
                            Placeholder::make('updated_at')
                                ->label(false)
                                ->content(fn ($record) => view('inline-label-value', [
                                    'label' => "Yangilangan",
                                    'value' => $record->updated_at->format('Y-m-d H:i'),
                                ]))
                                ->columnSpan(6),
                            Placeholder::make('product')
                                ->label(false)
                                ->content(fn ($record) => view('inline-label-value', [
                                    'label' => "Mahsulotlarga izohlar",
                                    'value' => $record->product,
                                ]))
                                ->columnSpan(6),
                            Placeholder::make('comment')
                                ->label(false)
                                ->content(fn ($record) => view('inline-label-value', [
                                    'label' => "Sharh",
                                    'value' => $record->comment,
                                ]))
                                ->columnSpan(6),
                        ])->columnSpan(6)->columns(12),
                        Section::make('To‘lov jadvali')
                            // ->hidden(fn () => auth()->id() !== 1) // Faqat sizga ko‘rsatish
                            ->schema([
                            Actions::make([
                                Action::make('grafik_download')
                                    ->label(false)
                                    ->icon('fas-print')
                                    ->color('success')
                                    ->tooltip('Chop etish')
                                    ->action(function ($record) {
                                        // PDFni yaratish va yuklash
                                        $pdf = Pdf::loadView('pdf.contract-schedule', [
                                            'contract' => $record,
                                            // 'schedules' => static::generateScheduleData($record), // Grafigi ma'lumotlari
                                        ])->setPaper([0, 0, 900, 600], 'landscape') // Albom yo'nalishi
                                        ->setOption('defaultFont', 'Arial')
                                        ->setOption('isHtml5ParserEnabled', true)
                                        ->setOption('isRemoteEnabled', true)
                                        ->setOption('orientation', 'landscape');
                
                                        return response()->streamDownload(
                                            fn () => print($pdf->output()),
                                            "contract_schedule_{$record->id}.pdf"
                                        );
                                    })
                                    ->extraAttributes(['style' => 'border-radius: 100%; padding: 10px 5px 10px 10px;'])
                            ])->columnSpan(12),
                                Placeholder::make('comment')
                                    ->label(false)
                                    ->content(fn ($record) => view('contract.payments-table', [
                                        'payments' => $record->paymentSchedule,
                                    ]))
                                    ->columnSpan(12),
                                Actions::make([
                                    Action::make('payments')
                                        ->label("To'lovni qabul qilish")
                                        ->icon('fas-circle-check')
                                        ->button()
                                        ->color('primary')
                                        ->modalWidth('lg')
                                        ->modalHeading("To'lovni qabul qilish")
                                        ->modalSubmitActionLabel('Saqlash')
                                        ->visible(fn ($record) => ($record->status->key != 'cancelled'))
                                        ->modalCancelActionLabel('Bekor qilish')
                                        ->form([
                                            Select::make('payment_method_id')
                                                ->label("To'lov turi")
                                                ->options(PaymentMethod::orderBy('id','desc')->get()->pluck('name','id'))
                                                ->reactive()
                                                ->afterStateUpdated(fn (callable $set) => $set('card_id', null))
                                                ->required(),
                                            Select::make('card_id')
                                                ->label('Karta')
                                                ->options(function (callable $get, $livewire) {
                                                    $record = $livewire->getRecord();
                                            
                                                    return $record->contractCards->mapWithKeys(function ($card) {
                                                        $number = $card->car_number;
                                                        $masked = substr($number, 0, 4) . ' **** **** ' . substr($number, -4);
                                                        return [$card->id => $masked];
                                                    });
                                                })
                                                ->reactive()
                                                ->visible(fn (callable $get) => PaymentMethod::find($get('payment_method_id'))?->type == 'online') 
                                                ->required(),
                                            Forms\Components\TextInput::make('amount')
                                                ->label('Miqdori')
                                                ->required(),
                                        ])
                                        ->action(function (array $data,$record) {$contract = $record;
                                            $amount = $data['amount'];

                                            $totalRemainingDebt = $contract->paymentSchedule
                                                ->map(function ($schedule) {
                                                    $paidPrincipal = $schedule->paymentTransactions->sum('paid_principal_amount');
                                                    $paidInterest = $schedule->paymentTransactions->sum('paid_interest_amount');

                                                    return max(0, $schedule->principal_amount - $paidPrincipal)
                                                        + max(0, $schedule->interest_amount - $paidInterest);
                                                })
                                                ->sum(); // jami qolgan qarz
                                        
                                            if ($data['amount'] > $totalRemainingDebt) {
                                                    Notification::make()
                                                        ->title("To‘lov summasi ortiqcha")
                                                        ->body("Sizdan faqat {$totalRemainingDebt} so‘m qabul qilinishi mumkin")
                                                        ->danger()
                                                        ->send();
                                                
                                                    return;
                                            }
                                            // To‘lovni yozish
                                            $payment = \App\Models\Payment::create([
                                                'contract_id' => $contract->id,
                                                'payment_method_id' => $data['payment_method_id'],
                                                'contract_card_id' => $data['card_id'] ?? null,
                                                'total_amount' => $amount,
                                            ]);
                                        
                                            // To‘lovni ajratish uchun grafiklarni olib kelamiz
                                            $schedules = $contract->paymentSchedule()
                                                ->where(function ($query) {
                                                    $query->whereRaw('(
                                                        (interest_amount > (
                                                            SELECT COALESCE(SUM(pt.paid_interest_amount), 0)
                                                            FROM payment_transactions pt
                                                            WHERE pt.payment_schedule_id = payment_schedules.id
                                                        )) OR 
                                                        (principal_amount > (
                                                            SELECT COALESCE(SUM(pt.paid_principal_amount), 0)
                                                            FROM payment_transactions pt
                                                            WHERE pt.payment_schedule_id = payment_schedules.id
                                                        ))
                                                    )');
                                                })
                                                ->orderBy('due_date')
                                                ->get();
                                            foreach ($schedules as $schedule) {
                                                    if ($amount <= 0) break;
                                            
                                                    $paidInterest = $schedule->paymentTransactions->sum('paid_interest_amount');
                                                    $paidPrincipal = $schedule->paymentTransactions->sum('paid_principal_amount');
                                            
                                                    $interestLeft = $schedule->interest_amount - $paidInterest;
                                                    $principalLeft = $schedule->principal_amount - $paidPrincipal;
                                            
                                                    $payInterest = min($amount, $interestLeft);
                                                    $amount -= $payInterest;
                                            
                                                    $payPrincipal = min($amount, $principalLeft);
                                                    $amount -= $payPrincipal;
                                            
                                                    if ($payInterest > 0 || $payPrincipal > 0) {
                                                        PaymentTransaction::create([
                                                            'contract_id' => $contract->id,
                                                            'payment_schedule_id' => $schedule->id,
                                                            'payment_id' => $payment->id,
                                                            'paid_interest_amount' => $payInterest,
                                                            'paid_principal_amount' => $payPrincipal,
                                                            'paid_total_amount' => $payInterest + $payPrincipal,
                                                        ]);
                                                    }
                                            }
                                            
                                            $record->checkAndUpdateStatus(); // statusni tekshir
                                        
                                            Notification::make()
                                                ->title(number_format($payment->total_amount,2,'.',' ')." сум to'lov qabul qilindi")
                                                ->success()
                                                ->send();
                                        }),
                                ])->columnSpan(3),
                            
                                // View::make('contract.payments-table')
                                //     ->viewData(['payments' => fn ($livewire) => $livewire->record->payments,])
                            ])->columns(12)->columnSpan(12),
                        
                        Tabs::make('')
                        ->tabs([
                            Tab::make("To'lov tarixi")
                                ->schema([
                                    Placeholder::make('payments-history')
                                        ->label(false)
                                        ->content(fn ($record) => view('contract.payments-history', [
                                            'contract' => $record,
                                        ]))
                                    ->columnSpan(12),
                                ]),
                            Tab::make('Tranzaksiyalar jurnali')
                                ->schema([
                                ]),
                            Tab::make('Shartnomalar tarixi')
                                ->schema([
                                    Placeholder::make('client-contacts')
                                        ->label(false)
                                        ->content(fn ($record) => view('contract.contracts', [
                                            'contracts' => $record->client->contracts,
                                        ]))
                                    ->columnSpan(12),
                                ]),
                            Tab::make("KATM to'lov")
                                ->schema([
                                ]),
                        ])->columnSpan(12)->columns(12),
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
                                        Hidden::make('customer_id')->default(auth()->user()->id),
                                        Hidden::make('business_id')->default(auth()->user()->business_id),
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
                                                    ->reactive()
                                                    ->suffixAction(
                                                        Action::make('search_client')
                                                            ->icon('heroicon-o-magnifying-glass')
                                                            ->action(fn ($get, $set) => self::searchClient($get, $set))
                                                    )
                                                    ->columnSpan(6),
                                                    // Actions::make([
                                                    //         Action::make('add_client')
                                                    //             ->label("Ma'lumotlarni qo'lda kiritish")
                                                    //             ->modalWidth(MaxWidth::TwoExtraLarge)
                                                    //             ->modalHeading('Yangi mijoz qo‘shish')
                                                    //             ->form([
                                                    //                 Section::make()
                                                    //                     ->schema([
                                                    //                         TextInput::make('new_first_name')
                                                    //                             ->label('Ism')
                                                    //                             ->placeholder('Ism')
                                                    //                             ->required()
                                                    //                             ->maxLength(255)->columnSpan(6),
                                                    //                         TextInput::make('new_last_name')
                                                    //                             ->label('Familiya')
                                                    //                             ->placeholder('Familiya')
                                                    //                             ->required()
                                                    //                             ->maxLength(255)->columnSpan(6),
                                                    //                         TextInput::make('new_patronymic')
                                                    //                             ->label('Otasining ismi')
                                                    //                             ->placeholder('Otasining ismi')
                                                    //                             ->required()
                                                    //                             ->maxLength(255)->columnSpan(6),
                                                    //                         Select::make('new_gender')
                                                    //                             ->options([
                                                    //                                 'female'=>'Ayol',
                                                    //                                 'male'=>'Erkak',
                                                    //                             ])
                                                    //                             ->label('jinsi')
                                                    //                             ->required()->columnSpan(6),
                                                    //                         TextInput::make('new_birthplace')
                                                    //                             ->label("Tug'ilgan joyi")
                                                    //                             ->placeholder("Tug'ilgan joyi")
                                                    //                             ->required()
                                                    //                             ->maxLength(255)->columnSpan(6),
                                                    //                         DatePicker::make('new_birthday')
                                                    //                             ->label("Tug'ilgan kuni")
                                                    //                             ->placeholder("Tug'ilgan kuni")
                                                    //                             ->required()->columnSpan(6),
                                                    //                         TextInput::make('new_passport_series')
                                                    //                             ->label('Pasport seriyasi')
                                                    //                             ->placeholder('Pasport seriyasi')
                                                    //                             ->maxLength(2) // Maksimal 2 ta belgi
                                                    //                             ->minLength(2) // Minimal 2 ta belgi
                                                    //                             ->extraAttributes([
                                                    //                                 'x-on:input' => "event.target.value = event.target.value.toUpperCase()"
                                                    //                             ])
                                                    //                             ->required()->columnSpan(6),
                                                    //                         TextInput::make('new_passport_number')
                                                    //                             ->label('Pasport raqami')
                                                    //                             ->unique('clients', 'passport_number')
                                                    //                             ->numeric()
                                                    //                             ->placeholder('Pasport raqami')
                                                    //                             ->required()
                                                    //                             ->maxLength(7)->columnSpan(6),
                                                    //                         TextInput::make('new_inn')
                                                    //                             ->unique('clients', 'inn')
                                                    //                             ->label('INN')
                                                    //                             ->numeric()
                                                    //                             ->placeholder('INN')
                                                    //                             ->maxLength(15)->columnSpan(6),
                                                    //                         TextInput::make('new_pinfl')
                                                    //                             ->unique('clients', 'pinfl')
                                                    //                             ->label('PINFL')
                                                    //                             ->numeric()
                                                    //                             ->placeholder('PINFL')
                                                    //                             ->required()
                                                    //                             ->maxLength(14)->columnSpan(6),
                                                    //                         DatePicker::make('new_passport_date_issue')
                                                    //                             ->label('Pasport berilgan sana')
                                                    //                             ->required()->columnSpan(6),
                                                    //                         DatePicker::make('new_passport_date_expiration')
                                                    //                             ->label('Pasportning amal qilish muddati')
                                                    //                             ->required()->columnSpan(6),
                                                    //                     ])->columnSpan(12)->columns(12)
                                                    //             ])
                                                    //             ->action(function ($data, $livewire) {
                                                    //                 $client = Client::create([
                                                    //                     'customer_id' => auth()->user()->id,
                                                    //                     'first_name' => $data['new_first_name'],
                                                    //                     'last_name' => $data['new_last_name'],
                                                    //                     'patronymic' => $data['new_patronymic'],
                                                    //                     'gender' => $data['new_gender'],
                                                    //                     'birthplace' => $data['new_birthplace'],
                                                    //                     'birthday' => $data['new_birthday'],
                                                    //                     'passport_series' => $data['new_passport_series'],
                                                    //                     'passport_number' => $data['new_passport_number'],
                                                    //                     'inn' => $data['new_inn'],
                                                    //                     'pinfl' => $data['new_pinfl'],
                                                    //                     'passport_date_issue' => $data['new_passport_date_issue'],
                                                    //                     'passport_date_expiration' => $data['new_passport_date_expiration'],
                                                    //                 ]);
                                            
                                                    //                 Notification::make()
                                                    //                     ->title('Mijoz qo‘shildi!')
                                                    //                     ->success()
                                                    //                     ->body('Yangi mijoz ma\'lumotlari saqlandi.')
                                                    //                     ->send();
                                                                    
                                                    //                 $livewire->form->fill([
                                                    //                     'client_id' => $client->id,
                                                    //                     'passport_number' => $client->passport_number,
                                                    //                     'passport_series' => $client->passport_series,
                                                    //                 ]);
                                                    //             }),
                                                    //         ])->columnSpan(6),
                                                Hidden::make('client_id'),
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
                                                            ->columnSpan(12),
                                                    // Repeater::make('phones')
                                                    //         ->schema([
                                                    //             TextInput::make('')->label('Phone') // Bo'sh key - faqat qiymat saqlash uchun
                                                    //                 ->required()
                                                    //                 ->maxLength(15),
                                                    //         ])
                                                    //         ->columnSpan(12)
                                                    //         ->defaultItems(2)
                                                    //         ->afterStateHydrated(function ($component, $state) {
                                                    //             // Ma'lumotni massiv shaklida chiqarish
                                                    //             if (is_array($state)) {
                                                    //                 $component->state(collect($state)->pluck(null)->toArray());
                                                    //             }
                                                    //         })
                                                    //         ->addable(true) // "+" tugmasini qo'shish
                                                    //         ->deletable(true) // O'chirish tugmasini qo'shish
                                                    //         ->dehydrateStateUsing(fn ($state) => collect($state)->pluck(null)->toArray())
                                                    
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
                                                    ->schema([
                                                        TextInput::make('contractCards.car_number')
                                                            ->label('Karta raqami')
                                                            ->placeholder('Karta raqami')
                                                            ->mask('9999-9999-9999-9999')
                                                            ->nullable()
                                                            ->columnSpan(12),
                                                        TextInput::make('contractCards.expiry_date')
                                                            ->label('Amal qilish muddati')
                                                            ->placeholder('Amal qilish muddati')
                                                            ->nullable()
                                                            ->mask('99/99')
                                                            ->columnSpan(12),
                                                        TextInput::make('contractCards.phone')
                                                            ->tel()
                                                            ->label('Telefon')
                                                            ->placeholder('Telefon')
                                                            ->mask('+998999999999')
                                                            ->nullable()
                                                            ->columnSpan(12),
                                                        Hidden::make('contractCards.card_id')->default(1)
                                                    ])->columns(8)->columnSpan(12)
                                            ]),
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
                                                    ->content(fn (Get $get) => $get('down_payment')  ?? '-')->columnSpan(4),
                        
                                                Placeholder::make('place_payment_day')
                                                    ->label("To'lov kuni")
                                                    ->content(fn (Get $get) => $get('payment_day')  ?? '-')->columnSpan(4),
                        
                                                Placeholder::make('place_period_month')
                                                    ->label('Davr')
                                                    ->content(fn (Get $get) => $get('period_month')  ?? '-')->columnSpan(4),
                        
                                                Placeholder::make('place_region')
                                                    ->label('Region:')
                                                    ->content(fn (Get $get) => Region::find($get('region_id'))->name ?? '')->columnSpan(4),
                                                Placeholder::make('place_district')
                                                    ->label('Tuman:')
                                                    ->content(fn (Get $get) => District::find($get('district_id'))->name ?? '')->columnSpan(4),
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
                                            ]),
                        ])->columnSpan(3)->columns(12),
                        
                        Actions::make([
                            Action::make('calculateSchedule')
                                ->label('To‘lov grafigini olish')
                                ->modal()
                                ->action(fn ($livewire) => $livewire->generatePaymentSchedule()) 
                                ->modalContent(fn ($livewire) =>     $livewire->generatePaymentSchedule())
                        ])
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
    
    public static function generateWordFile($record)
    {
        
        $templatePath = storage_path('app/templates/contract_template.docx');
        $fileName = 'shartnoma_' . $record->client->first_name."_".$record->client->last_name."_".$record->client->patronymic."_".$record->created_at->format('M d, Y'). '.docx';
        
        // Yangi TemplateProcessor obyekti
        $templateProcessor = new TemplateProcessor($templatePath);

        // O'zgaruvchilarni to‘ldiramiz
        $templateProcessor->setValue('contract_id', $record->id);
        $templateProcessor->setValue('seller', $record->customer->name);
        $templateProcessor->setValue('contract_created', $record->created_at->format('M d, Y - H:i'));
        $templateProcessor->setValue('client_first_name', $record->client->first_name);
        $templateProcessor->setValue('client_last_name', $record->client->last_name);
        $templateProcessor->setValue('client_patronymic', $record->client->patronymic);
        $templateProcessor->setValue('region', $record->contractDetail->region->name);
        $templateProcessor->setValue('district', $record->contractDetail->district->name);
        $templateProcessor->setValue('address', $record->contractDetail->address);
        $templateProcessor->setValue('mfy_address', $record->contractDetail->mfy_address);
        $templateProcessor->setValue('passport_address', $record->contractDetail->passport_address);
        $templateProcessor->setValue('position', $record->contractDetail->position);
        $templateProcessor->setValue('workplace', $record->contractDetail->workplace);
        $templateProcessor->setValue('passport_series', $record->client->passport_series);
        $templateProcessor->setValue('passport_number', $record->client->passport_number);
        $templateProcessor->setValue('passport_date_issue', $record->client->passport_date_issue);
        $templateProcessor->setValue('product', $record->product);
        $templateProcessor->setValue('comment', $record->comment);
        $templateProcessor->setValue('total_amount', number_format($record->amount,2,'.',' '));
        $templateProcessor->setValue('monthly_payment', number_format($record->paymentSchedule->avg('total_amount'),2,'.',' '));
        $templateProcessor->setValue('down_payment', number_format($record->down_payment,2,'.',' '));
        $templateProcessor->setValue('residual_debt', number_format($record->paymentSchedule->sum('total_amount'),2,'.',' '));
        $templateProcessor->setValue('period_month', $record->period_month);
        $templateProcessor->setValue('company_name', $record->branch->company_name);
        $templateProcessor->setValue('company_address', $record->branch->address);
        $templateProcessor->setValue('company_bank', $record->branch->bankInfo->bank);
        $templateProcessor->setValue('company_inn', $record->branch->bankInfo->inn);
        $templateProcessor->setValue('company_mfo', $record->branch->bankInfo->mfo);  
        $templateProcessor->setValue('company_payment_account', $record->branch->bankInfo->payment_account);  
        $templateProcessor->setValue('company_phone', $record->branch->telegram_phone);  
        $templateProcessor->setValue('company_owner', $record->branch->customer->name);  
        $templateProcessor->setValue('plastik_card', $record->contractCard ? substr($record->contractCards->car_number, 0, 4). "**** ****". substr($record->contractCards->car_number, -4) : '-');
        $templateProcessor->setValue('inn', $record->client->inn);
        $templateProcessor->setValue('phones', collect($record->contractDetail->phones)->pluck('phone')->implode(', '));

        $schedules = $record->paymentSchedule;

        // Jadval qatorlarini dinamik ravishda ko'paytirish
        $templateProcessor->cloneRow('row_number', count($schedules));

        // Har bir qatorni to'ldirish
        foreach ($schedules as $index => $schedule) {
            $rowIndex = $index + 1;
            $templateProcessor->setValue("row_number#{$rowIndex}", $rowIndex);
            $templateProcessor->setValue("due_date#{$rowIndex}", $schedule->due_date);
            $templateProcessor->setValue("amount#{$rowIndex}", number_format($schedule->total_amount, 2, '.', ' '));
        }
        // Saqlash uchun vaqtinchalik fayl
        $tempPath = storage_path("app/public/{$fileName}");
        $templateProcessor->saveAs($tempPath);

        // Foydalanuvchiga yuklab berish
        return response()->download($tempPath)->deleteFileAfterSend();
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
                        return '<span style="color: #3EB0C0;">' .$state->first_name . ' '. $state->last_name.' '.$state->patronymic.'</span>'; // Masalan, 1000.50 ni 1,000.50 formatida
                    })
                    ->html() 
                    ->action(
                        Tables\Actions\Action::make('view_contracts')
                                ->label('Shartnomalarni ko‘rish')
                                ->modalHeading(fn ($record) => $record->client->first_name . ' '. $record->client->last_name.' '.$record->client->patronymic)
                                ->modalContent(function ($record) {
                                    $contracts = $record->client->contracts;
                                    return view('tables.modals.contract-list', [
                                        'contracts' => $contracts,
                                        'client_id'=>$record->client->id
                                    ]);
                                })
                                ->modalWidth('lg')
                                ->slideOver()
                                ->modalSubmitAction(false) // "Saqlash" tugmasini o‘chirish
                                ->modalCancelActionLabel('Yopish')
                    ),
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
                SelectFilter::make('branch_id')
                    ->label('Филиалы')
                    ->multiple()
                    ->searchable()
                    ->options(fn () => Branch::where('customer_id',auth()->user()->id)->get()->pluck('name', 'id')->map(fn ($name) =>$name))
                    ->preload(),
                    
                Filter::make('fio')
                ->form([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('contract_number')
                                ->label('Shartnoma raqami')
                                ->placeholder('Shartnoma raqami')
                                ->columnSpan(1),
            
                            TextInput::make('fio')
                                ->label('FIO')
                                ->placeholder('FIO')
                                ->columnSpan(1),
            
                            TextInput::make('passport')
                                ->label('Pasport seriyasi va raqami')
                                ->placeholder('Pasport seriyasi va raqami')
                                ->columnSpan(1)
                                ->extraAttributes([
                                    'x-on:input' => "event.target.value = event.target.value.toUpperCase()"
                                ])
                        ])
                ])->columnSpan(3)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                        
                        ->when($data['contract_number'], function ($query) use ($data) {
                            $query->where('id', 'like', '%' . $data['contract_number'] . '%');
                        })

                        ->when($data['fio'], function ($query) use ($data) {
                            $search = $data['fio'];

                            $query->whereHas('client', function ($q) use ($search) {
                                $q->whereRaw("CONCAT(first_name, ' ', last_name, ' ', patronymic) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(last_name, ' ', first_name, ' ', patronymic) LIKE ?", ["%{$search}%"])
                                ;
                            });
                        })
                        
                        ->when($data['passport'], function ($query) use ($data) {
                            $search = $data['passport'];

                            $query->whereHas('client', function ($q) use ($search) {
                                $q->whereRaw("CONCAT(passport_series, '', passport_number) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(passport_number, '', passport_series) LIKE ?", ["%{$search}%"])
                                ;
                            });
                        });
                    }),
                
            ],layout: FiltersLayout::AboveContent)
            ->actions([
            ])
            ->groups([
                Tables\Grouping\Group::make('branch.name')
                    ->label('Филиал')
                    ->collapsible(),
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
