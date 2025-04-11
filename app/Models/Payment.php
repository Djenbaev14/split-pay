<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded=['id'];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
