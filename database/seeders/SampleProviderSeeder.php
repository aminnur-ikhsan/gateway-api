<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\ProvidersModel as Providers;

class SampleProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=SampleProviderSeeder
     *
     * @return void
     */
    public function run()
    {
        $many = 3;
        for ($i = 0; $i < $many; $i++) {
            Providers::create([
                'name' => fake()->name(),
                'token' => Str::uuid(),
                'salt' => Str::random(60),
            ]);
        }
    }
}
