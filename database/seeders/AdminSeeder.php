<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin::create([
        //     'email' => 'admin@example.com',
        //     'password' => Hash::make('nagoyameshi'),
        // ]);

        Admin::create([
            'email' => 'samurai_kadai@example.com',
            'password' => Hash::make('kadaiteishutu'),
        ]);
    }
}
