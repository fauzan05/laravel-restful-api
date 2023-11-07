<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::limit(1)->first();
        $this->post('/api/contacts/' . $contact->id . '/addresses', 
        [
            'street' => 'jl. tembana',
            'city' => 'kebumen',
            'province' => 'jawa tengah',
            'country' => 'indonesia',
            'postal_code' => '54361'
        ],
        [
            'Authorization' => 'token01'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'street' => 'jl. tembana',
                    'city' => 'kebumen',
                    'province' => 'jawa tengah',
                    'country' => 'indonesia',
                    'postal_code' => '54361'
                ]
            ]);
    }

    public function testCreateFail()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::limit(1)->first();
        $this->post('/api/contacts/' . $contact->id . '/addresses', 
        [
            'street' => 'jl. tembana',
            'city' => 'kebumen',
            'province' => 'jawa tengah',
            'postal_code' => '54361'
        ],
        [
            'Authorization' => 'token01'
        ])->assertStatus(400)
            ->assertJson([
                'error_message' => 
                [
                    'country' => [
                        'The country field is required.'
                    ]
                ] 
            ]);
    }

    public function testCreateIfContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::limit(1)->first();
        $this->post('/api/contacts/' . $contact->id+1 . '/addresses', 
        [
            'street' => 'jl. tembana',
            'city' => 'kebumen',
            'province' => 'jawa tengah',
            'country' => 'indonesia',
            'postal_code' => '54361'
        ],
        [
            'Authorization' => 'token01'
        ])->assertStatus(404)
            ->assertJson([
                'error_message' => [
                    'User Contact Not Found'
                ]
            ]);
    }

    public function testGetAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::limit(1)->first();
        $this->get('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [
            'Authorization' => 'token01'
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $address->id,
                    'street' => $address->street,
                    'city' => $address->city,
                    'province' => $address->province,
                    'country' => $address->country,
                    'postal_code' => $address->postal_code
                ]
            ]);
    }

    public function testGetAddressFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::limit(1)->first();
        $this->get('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id + 1, [
            'Authorization' => 'token01'
        ])
            ->assertStatus(404)
            ->assertJson([
                'error_message' => [
                    'Address Not Found'
                ]
            ]);
    }

    public function testUpdateAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::limit(1)->first();
        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
        [
            'street' => 'gatau',
            'city' => 'gatau',
            'province' => 'gatau',
            'country' => 'gatau',
            'postal_code' => 'gatau'
        ],
        [
            'Authorization' => 'token01'
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'street' => 'gatau',
                'city' => 'gatau',
                'province' => 'gatau',
                'country' => 'gatau',
                'postal_code' => 'gatau'
            ]
        ]);
    }

    public function testUpdateAddressFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::limit(1)->first();
        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
        [
            'street' => 'gatau',
            'city' => 'gatau',
            'province' => 'gatau',
            'country' => 'gatau',
        ],
        [
            'Authorization' => 'token01'
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'street' => 'gatau',
                'city' => 'gatau',
                'province' => 'gatau',
                'country' => 'gatau',
                'postal_code' => '54361'
            ]
        ]);
    }

    public function testUpdateAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::limit(1)->first();
        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id + 1,
        [
            'street' => 'gatau',
            'city' => 'gatau',
            'province' => 'gatau',
            'country' => 'gatau',
        ],
        [
            'Authorization' => 'token01'
        ])
        ->assertStatus(404)
        ->assertJson([
            'error_message' => [
                'Address Not Found'
            ]
        ]);
    }

    public function testDeleteAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::limit(1)->first();
        $this->delete('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
        headers:[
            'Authorization' => 'token01'
        ])
            ->assertStatus(200)
            ->assertJson([
                'message' => [
                    'Address Successfully Deleted'
                ]
            ]);
    }

    public function testDeleteAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::limit(1)->first();
        $this->delete('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id + 1,
        headers:[
            'Authorization' => 'token01'
        ])
            ->assertStatus(404)
            ->assertJson([
                'error_message'=> [
                    'Address Not Found'
                    ]
            ]);
    }

    public function testListAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::limit(1)->first();
        $address = Address::limit(1)->first();
        $this->get('/api/contacts/'. $contact->id . '/addresses', 
        [
            'Authorization' => 'token01'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                'id' => $address->id,
                'street' => 'jl. tembana',
                'city' => 'kebumen',
                'province' => 'jawa tengah',
                'country' => 'indonesia',
                'postal_code' => '54361'
                ]
            ]
        ]);        
    }

    public function testListAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::limit(1)->first();
        $address = Address::limit(1)->first();
        $this->get('/api/contacts/'. $contact->id +1 . '/addresses', 
        [
            'Authorization' => 'token01'
        ])->assertStatus(404)
        ->assertJson([
            'error_message' => [
                'Contact Not Found'
            ]
        ]);      
    }
}
