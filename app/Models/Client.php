<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $guarded=['id'];
    public function ClientContacts()
    {
        return $this->hasMany(ClientContact::class)->orderBy('id','desc');
    }
    public function contracts()
    {
        return $this->hasMany(Contract::class)->orderBy('id','desc');
    }
}
