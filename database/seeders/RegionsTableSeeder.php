<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            ['name' => 'Тошкент шаҳар'],
            ['name' => 'Тошкент вилояти'],
            ['name' => 'Андижон вилояти'],
            ['name' => 'Бухоро вилояти'],
            ['name' => 'Жиззах вилояти'],
            ['name' => 'Қашқадарё вилояти'],
            ['name' => 'Навоий вилояти'],
            ['name' => 'Наманган вилояти'],
            ['name' => 'Самарқанд вилояти'],
            ['name' => 'Сурхондарё вилояти'],
            ['name' => 'Сирдарё вилояти'],
            ['name' => 'Фарғона вилояти'],
            ['name' => 'Хоразм вилояти'],
            ['name' => 'Қорақалпоғистон Республикаси'],
        ];

        DB::table('regions')->insert($regions);
    }
}
