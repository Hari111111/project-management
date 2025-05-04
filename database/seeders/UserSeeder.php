<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
           // Create roles only if they don't already exist
           $adminRole = Role::firstOrCreate(['name' => 'admin']);
           $userRole = Role::firstOrCreate(['name' => 'user']);
   
           // Create Admin User
           $adminUser = User::firstOrCreate(
               ['email' => 'admin@example.com'],
               [
                   'name' => 'Admin User',
                   'password' => Hash::make('password'),
               ]
           );
           $adminUser->assignRole($adminRole);
   
           // Create Regular User
           $normalUser = User::firstOrCreate(
               ['email' => 'user@example.com'],
               [
                   'name' => 'Regular User',
                   'password' => Hash::make('password'),
               ]
           );
           $normalUser->assignRole($userRole);

    }
}
