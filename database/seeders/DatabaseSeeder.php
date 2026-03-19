<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Role::create([
            // 'uuid' => Str::uuid(),
            'name' => 'admin',
        ]);
        Role::create([
            // 'uuid' => Str::uuid(),
            'name' => 'operator'
        ]);

        $admin = User::firstOrCreate([
            'name' => 'admin',
            'email' => 'admin@grtech.com',
            'password' => 'password',
        ]);
        $operator = User::firstOrCreate([
            'name' => 'user',
            'email' => 'user@grtech.com',
            'password' => 'password',
        ]);
        $admin->assignRole('admin');
        $operator->assignRole('operator');

        Company::factory()
            ->count(4)
            ->hasEmployees(10)
            ->create();
    }
}
