<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateContactSuccess()
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'fauzan123')->first();
        $this->withHeaders([
            'Authorization' => 'token01',
        ])
        ->post('/api/contacts', [
            'first_name' => $user->name,
            'email' => 'fauzan123@gmail.com',
            'phone' => '081335457601',
        ], )->assertStatus(201);
    }

    public function testCreateContactsFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->withHeaders([
            'Authorization' => 'token01',
        ])
        ->post('/api/contacts', [
            'email' => 'fauzan123@gmail.com',
            'phone' => '081335457601',
        ], )->assertStatus(400);
    }

    public function testGetContactSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $user = User::where('username', 'fauzan123')->first();
        $contact = Contact::where('user_id', $user->id)->first();
        $this->get("/api/contacts/$contact->id", [
            'Authorization' => 'token01'
        ])->assertStatus(200);

    }

    public function testGetContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $this->get("/api/contacts/1", [
            'Authorization' => 'token01'
        ])->assertStatus(404);
    }

    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $user = User::where('username', 'fauzan123')->first();
        $contact = Contact::where('user_id', $user->id)->first();
        $this->withHeaders([
            'Authorization' => 'token02',
        ])->get("/api/contacts/". $contact->id)
            ->assertStatus(404);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->put("/api/contacts/". $contact->id,[
            'first_name' => 'fauzan',
            'last_name' => 'nurhidayat',
            'email' => 'fauzannurhidayat8@gmail.com',
            'phone' => '081335457601'
        ], [
            'Authorization' => 'token01'
        ])->assertStatus(200);
    }

    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->put("/api/contacts/". $contact->id,[
            'last_name' => 'nurhidayat',
            'email' => 'fauzannurhidayat8@gmail.com',
            'phone' => '081335457601'
        ], [
            'Authorization' => 'token01'
        ])->assertStatus(400);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->delete("/api/contacts/". $contact->id,[], [
            'Authorization' => 'token01'
        ])->assertStatus(200);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $this->delete("/api/contacts/". ($contact->id + 1),[], [
            'Authorization' => 'token01'
        ])->assertStatus(404);
    }

    public function testSearchByFirstName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?name=first', [
            'Authorization' => 'token01'
        ])
            ->assertStatus(200)
            ->json();
        // Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(10, count($response['data']));
    }
    public function testSearchByLastName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?name=last', [
            'Authorization' => 'token01'
        ])
            ->assertStatus(200)
            ->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(10, count($response['data']));
    }
    public function testSearchByEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?email=email', [
            'Authorization' => 'token01'
        ])
            ->assertStatus(200)
            ->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(10, count($response['data']));
    }
    public function testSearchByPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?phone=081', [
            'Authorization' => 'token01'
        ])
            ->assertStatus(200)
            ->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(10, count($response['data']));
    }
    
    public function testSearchPagination()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?size=5&page=2', [
            'Authorization' => 'token01'
        ])
            ->assertStatus(200)
            ->json();
        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(5, count($response['data']));
    }
    


}
