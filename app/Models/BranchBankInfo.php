<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchBankInfo extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
