<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class ApiTestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create mobile app test user
        $mobileUser = User::create([
            'name' => 'Mobile App Test User',
            'email' => 'mobile@onthenight.app',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $this->command->info('Mobile API test user createdAt:');
        $this->command->info('Email: mobile@onthenight.app');
        $this->command->info('Password: password');
        $this->command->info('This user can authenticate and create reviews');
        $this->command->info('Use this for mobile app API testing');
    }
}