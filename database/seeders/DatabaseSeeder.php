<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Business;
use App\Models\Card;
use App\Models\Customer;
use App\Models\PeriodType;
use App\Models\Status;
use App\Models\TariffType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DistrictsTableSeeder::class
        ]);
        User::create([
            'name' => 'Admin User',
            'login' => 'admin',
            'password' => Hash::make('admin'),
        ]);
        Business::create([
            'name'=>'DBC'
        ]);
        Customer::create([
            'name'=>'Shariyar Jaksulikov',
            'login'=>'admin123',
            'password'=>Hash::make('admin'),
            'business_id'=>1
        ]);
        PeriodType::create([
            'name'=>'Ежедневно',
            'type'=>'day'
        ]);
        PeriodType::create([
            'name'=>'Еженедельно',
            'type'=>'week'
        ]);
        PeriodType::create([
            'name'=>'Ежемесячно',
            'type'=>'month'
        ]);
        
        TariffType::create([
            'name'=>'Аннуитетный',
            'type'=>'annuity'
        ]);
        TariffType::create([
            'name'=>'Дифференцированный',
            'type'=>'differentiated'
        ]);
        Card::create([
            'name'=>'Uzcard',
        ]);

        Status::create([
            'name'=>'Ожидает подтверждение',
            'key'=>'pending',
            'color'=>'#a39e0ca6',
        ]);
        Status::create([
            'name'=>'Ожидает первоначальный взнос',
            'key'=>'initial_payment',
            'color'=>'#a39e0ca6',
        ]);
        Status::create( [
            'name'=>'Актив',
            'key'=>'active',
            'color'=>'#0d6efd',
        ]);
        Status::create([
            'name'=>'Завершенный',
            'key'=>'completed',
            'color'=>'#28B446',
        ]);
        Status::create([
            'name'=>'Отмененный',
            'key'=>'cancelled',
            'color'=>'#ff1414',
        ]);
    }
}
