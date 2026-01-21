<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@commune.ma'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'agent@commune.ma'],
            [
                'name' => 'Agent Saisie',
                'password' => Hash::make('password'),
                'role' => User::ROLE_AGENT_SAISIE,
            ]
        );

        User::updateOrCreate(
            ['email' => 'lecture@commune.ma'],
            [
                'name' => 'Utilisateur Consultation',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CONSULTATION,
            ]
        );
    }
}