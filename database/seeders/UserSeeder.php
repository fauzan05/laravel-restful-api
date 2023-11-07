<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->name = "Fauzan Nur Hidayat";
        $user->username = "fauzan123";
        $user->password = Hash::make("fauzan123");
        $user->token = 'token01';
        $user->save();

        $user = new User();
        $user->name = "Susi Rahmawati";
        $user->username = "susi123";
        $user->password = Hash::make("susi123");
        $user->token = 'token02';
        $user->save();
    }
}
