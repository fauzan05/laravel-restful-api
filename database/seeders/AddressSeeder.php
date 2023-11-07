<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */  
    public function run(): void
    {
        $contact = Contact::limit(1)->first();
        $address = new Address();
        $address->contact_id = $contact->id;
        $address->street = 'jl. tembana';
        $address->city = 'kebumen';
        $address->province = 'jawa tengah';
        $address->country = 'indonesia';
        $address->postal_code = '54361';
        $address->save();
    }
}
