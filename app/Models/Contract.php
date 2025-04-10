<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Contract extends Model
{
    use HasFactory;

    protected $guarded=['id'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class)->orderBy('id','desc');
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function contractDetail()
    {
        return $this->hasOne(ContractDetail::class);
    }
    public function contractCards()
    {
        return $this->hasMany(ContractCard::class)->orderBy('id','desc');
    }
    public function uzcard()
    {
        return $this->hasOne(ContractCard::class)->where('card_name', 'uzcard');
    }
    public function paymentSchedule()
    {
        return $this->hasMany(PaymentSchedule::class);
    }public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
    public function checkAndUpdateStatus()
    {
        $hasDebt = $this->paymentSchedule->contains(function ($schedule) {
            $paidPrincipal = $schedule->paymentTransactions->sum('paid_principal_amount');
            $paidInterest = $schedule->paymentTransactions->sum('paid_interest_amount');
        
            // Agar to'lovlardan biri to‘liq yopilmagan bo‘lsa, bu schedule hali yopilmagan
            return $paidPrincipal < $schedule->principal_amount || $paidInterest < $schedule->interest_amount;
        });
        if (! $hasDebt) {
            Log::info('qarz yoq');
            $status=Status::where('key','=','completed')->first();
            $this->status_id = $status->id;
            $this->save();
        }
        
        Log::info('qarz bor');
    }
    protected static function boot()
    {
        parent::boot();

        static::created(function ($contract) {
            $contract->generatePaymentSchedule();
            
        });
        
        static::creating(function ($contract) {
            if ($contract->down_payment && $contract->down_payment > 0) {
                $contract->status_id = 2; 
            }
        });
    }

    public function generatePaymentSchedule()
    {
        $amount = $this->amount;
        $initialPayment =$this->down_payment;
        $paymentDay = $this->payment_day;
        $duration = $this->period_month;

        $monthlyPayment = ($amount - $initialPayment) / $duration;
        $percentage  = $this->tariff->percentage;
        $interestRate = 0.01 * $percentage; // 3%

        $gracePeriod = $this->tariff->grace_period_month;
        
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
            PaymentSchedule::create([
                'contract_id' => $this->id,
                'due_date' => $date,
                'principal_amount' => round($monthlyPayment,2),
                'interest_amount' => round($interest,2),
                'total_amount' => round($total,2),
                'is_paid' => false,
            ]);
        }
    }
    

}
