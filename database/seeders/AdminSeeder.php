<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Admin::count() < 1){
            Admin::updateOrCreate(
                ['name' => "Abdullah zahid joy",
                    'password' => Hash::make('1234')],
                ['email' => "abdullahzahidjoy@gmail.com"]);
        }

    }
}
