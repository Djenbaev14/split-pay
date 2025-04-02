<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
    public function branch()
    {
        return $this->hasMany(Branch::class);
    }
}
