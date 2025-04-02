<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    use HasFactory;

    protected $guarded=['id'];
    public function tariffType()
    {
        return $this->belongsTo(TariffType::class);
    }
    public function PeriodType()
    {
        return $this->belongsTo(PeriodType::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

}
