<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin Hôtel',
            'email' => 'admin@hotel.test',
        ]);

        User::factory()->create([
            'name' => 'Réception',
            'email' => 'user@hotel.test',
        ]);
    }
}
