<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = auth()->user();
        $user = User::where('username', 'fauzan123')->first();
        for($i = 1; $i <= 20; $i++){
            $contact = new Contact();
            $contact->first_name = 'First ' . $i;
            $contact->last_name = 'Last'. $i;
            $contact->email = 'example'. $i . '@email.com';
            $contact->phone = '081'. $i . '000';
            $contact->user_id = $user->id;
            $contact->save();
        }
    }
}
