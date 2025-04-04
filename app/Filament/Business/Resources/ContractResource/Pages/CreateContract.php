<?php

namespace App\Filament\Business\Resources\ContractResource\Pages;

use App\Filament\Business\Resources\ContractResource;
use App\Models\Client;
use App\Models\Tariff;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\View;
use Filament\Support\Enums\MaxWidth;
class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;
    protected function getSteps(): array
    {
        return [
            'Подробности договора',
            'Информация о клиенте',
        ];
    }

    protected function afterCreate(): void
    {
        // Ma'lumotlarni saqlagandan keyin qadamlar holatini saqlash
        $this->record->update([
            'customer_id'=>auth()->user()->id,
            'business_id'=>auth()->user()->business_id,
            'branch_id' => $this->form->getState()['branch_id'],
            'tariff_id' => $this->form->getState()['tariff_id'],
            'amount' => $this->form->getState()['amount'],
            'down_payment' => $this->form->getState()['down_payment'],
            'payment_day' => $this->form->getState()['payment_day'],
            'period_month' => $this->form->getState()['period_month'],
            'product' => $this->form->getState()['product'],
            'comment' => $this->form->getState()['comment'],
        ]);
    }
    public  function getHeaderActions(): array
    {
        return [
            Action::make('add_client')
                    ->label("Klient qo'shish")
                    ->modalHeading('Yangi mijoz qo‘shish')
                    ->modalWidth(MaxWidth::TwoExtraLarge)
                    ->form([
                        Section::make()
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('Ism')
                                    ->placeholder('Ism')
                                    ->required()
                                    ->maxLength(255)->columnSpan(6),
                                TextInput::make('last_name')
                                    ->label('Familiya')
                                    ->placeholder('Familiya')
                                    ->required()
                                    ->maxLength(255)->columnSpan(6),
                                TextInput::make('patronymic')
                                    ->label('Otasining ismi')
                                    ->placeholder('Otasining ismi')
                                    ->required()
                                    ->maxLength(255)->columnSpan(6),
                                Select::make('gender')
                                    ->options([
                                        'female'=>'Ayol',
                                        'male'=>'Erkak',
                                    ])
                                    ->label('jinsi')
                                    ->required()->columnSpan(6),
                                TextInput::make('birthplace')
                                    ->label("Tug'ilgan joyi")
                                    ->placeholder("Tug'ilgan joyi")
                                    ->required()
                                    ->maxLength(255)->columnSpan(6),
                                DatePicker::make('birthday')
                                    ->label("Tug'ilgan kuni")
                                    ->placeholder("Tug'ilgan kuni")
                                    ->required()->columnSpan(6),
                                TextInput::make('passport_series')
                                    ->label('Pasport seriyasi')
                                    ->placeholder('Pasport seriyasi')
                                    ->maxLength(2) // Maksimal 2 ta belgi
                                    ->minLength(2) // Minimal 2 ta belgi
                                    ->extraAttributes([
                                        'x-on:input' => "event.target.value = event.target.value.toUpperCase()"
                                    ])
                                    ->required()->columnSpan(6),
                                TextInput::make('passport_number')
                                    ->label('Pasport raqami')
                                    ->numeric()
                                    ->placeholder('Pasport raqami')
                                    ->required()
                                    ->maxLength(7)->columnSpan(6),
                                TextInput::make('inn')
                                    ->label('INN')
                                    ->numeric()
                                    ->placeholder('INN')
                                    ->maxLength(15)->columnSpan(6),
                                TextInput::make('pinfl')
                                    ->label('PINFL')
                                    ->numeric()
                                    ->placeholder('PINFL')
                                    ->required()
                                    ->maxLength(14)->columnSpan(6),
                                DatePicker::make('passport_date_issue')
                                    ->label('Pasport berilgan sana')
                                    ->required()->columnSpan(6),
                                DatePicker::make('passport_date_expiration')
                                    ->label('Pasportning amal qilish muddati')
                                    ->required()->columnSpan(6),
                            ])->columnSpan(12)->columns(12)
                    ])
                    ->action(function ($data, $livewire) {
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
                            ->title('Mijoz qo‘shildi!')
                            ->success()
                            ->body('Yangi mijoz ma\'lumotlari saqlandi.')
                            ->send();
                        
                        $livewire->form->fill([
                            'client_id' => $client->id,
                            'passport_number' => $client->passport_number,
                            'passport_series' => $client->passport_series,
                        ]);
                    }),
        ];
    }
    
    
    public function generatePaymentSchedule()
    {
        $data = $this->form->getState();
    
        // $requiredFields = ['amount', 'tariff_id', 'down_payment', 'period_month','payment_day'];

        // foreach ($requiredFields as $field) {
        //     if (!isset($data[$field]) || empty($data[$field])) {
        //         return Notification::make()
        //             ->title('Xatolik!')
        //             ->danger()
        //             ->body("{$field} maydoni to‘ldirilishi shart!")
        //             ->send();
        //     }
        // }
        $amount = $data['amount'] ?? 0;
        $initialPayment = $data['down_payment'] ?? 0;
        $paymentDay = $data['payment_day'] ?? 1;
        $duration = $data['period_month'];

        $monthlyPayment = ($amount - $initialPayment) / $duration;
        $percentage  =Tariff::find($data['tariff_id'])->percentage;
        $interestRate = 0.01 * $percentage; // 3%

        $gracePeriod =Tariff::find($data['tariff_id'])->grace_period_month;
        
        $now = Carbon::now();
        $startDate = Carbon::now()->addMonths($gracePeriod)->day($paymentDay); 
        if ($paymentDay < $now->day) {
            $startDate->addMonths(1); // Agar oldingi oyga tushib qolsa, keyingi oydan boshlaydi
        }
        $schedule = [];
        for ($i = 1; $i <= $duration; $i++) {
            $date = $startDate->copy()->addMonths($i - 1)->format('Y-m-d');
            $interest = ($amount - $initialPayment) * $interestRate;
            $total = $monthlyPayment + $interest;

            $schedule[] = [
                'id' => $i,
                'date' => $date,
                'monthlyPayment' => $monthlyPayment,
                'interest' => $interest,
                'total' => $total,
            ];
        }

        return View::make('livewire.payment-schedule', compact('schedule'));
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
}
