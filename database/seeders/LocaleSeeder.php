<?php

namespace Database\Seeders;

use App\Models\Locale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arr = [
            [
                'name' => 'English',
                'short_code' => 'en',
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'name' => 'French',
                'short_code' => 'fr',
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'name' => 'Espanol',
                'short_code' => 'es',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        Locale::query()->truncate();
        Locale::query()->insert($arr);
    }
}
