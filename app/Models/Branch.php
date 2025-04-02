<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
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
    public function bankInfo()
    {
        return $this->hasMany(BranchBankInfo::class);
    }
    public function tariff()
    {
        return $this->hasMany(Tariff::class);
    }

}
