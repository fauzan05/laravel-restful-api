<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'fauzan123')->first();
        $contact = new Contact();
        $contact->user_id = $user->id;
        $contact->first_name = 'Fauzan';
        $contact->last_name = 'Nur Hidayat';
        $contact->email = 'fauzannurhidayat8@gmail.com';
        $contact->phone = '081335457601';
        $contact->save();
    }
}
