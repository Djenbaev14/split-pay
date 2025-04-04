<?php

namespace App\Models;

use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Customer extends Authenticatable implements FilamentUser
{
    use HasFactory,HasRoles,HasSuperAdmin;

    protected $guarded=['id'];
    protected $hidden = [
        'password',
    ];
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        // Faqat business paneliga kirishga ruxsat
        return $panel->getId() === 'business';
    }
    protected $casts = [
        'password' => 'hashed',
    ];
    
}
