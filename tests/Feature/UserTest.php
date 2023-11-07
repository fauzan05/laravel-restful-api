<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'username' => 'zane14',
            'password' => 'fauzan123',
            'name' => 'Fauzan Nur Hidayat'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => 'zane14',
                    'name' => 'Fauzan Nur Hidayat'
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => ''
        ])->assertStatus(400)
            ->assertJson([
                'error_message' => [
                    'username' => ['The username field is required.'],
                    'password' => ['The password field is required.'],
                    'name' => ['The name field is required.']
                ]
            ]);
    }

    public function testRegisterUsernameAlreadyRegistered()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users', [
            'username' => 'fauzan123',
            'password' => 'fauzan123',
            'name' => 'Fauzan Nur Hidayat'
        ])->assertStatus(400)
            ->assertJson([
                'error_message' => [
                    'username' => ['Username has been already registered']
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed(UserSeeder::class);
        $user = User::where('username','fauzan123')->first();
        $this->post('/api/users/login', [
            'username' => 'fauzan123',
            'password' => 'fauzan123'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->all()[0]->id,
                    'username' => 'fauzan123',
                    'name' => 'Fauzan Nur Hidayat',
                    'token' => $user->all()[0]->token
                ]
            ]);
    }

    public function testLoginFailed()
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'username' => 'haha',
            'password' => 'fauzan123'
        ])->assertStatus(401)
            ->assertJson([
                'error_message' => [
                    'username' => [
                        'username or password isnt valid'
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current', [
            'Authorization' => 'token01'
        ])->assertStatus(200)
            ->assertJson([
                'data'=> [
                    'username' => 'fauzan123',
                    'name' => 'Fauzan Nur Hidayat',
                    'token' => 'token01'
                ]
        ]);
        // var_dump(Auth::user()->name);
    }

    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current')
        ->assertStatus(401)
            ->assertJson([
                'error_message'=> [
                    'Unauthorized' 
                ]
            ]);
    }

    public function testInvalidToken()
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current', [
            'Authorization' => 'haha123'
        ])->assertStatus(401)
            ->assertJson([
            'error_message'=> [
                'Unauthorized' 
                ]
            ]);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);
        $userBefore = User::where('username', 'fauzan123')->first();
        $this->patch('/api/users/current', 
            [
                'password' => 'rahasia'
            ],
            [
                'Authorization' => 'token01'
            ])->assertStatus(200);
        $userAfter = User::where('username', 'fauzan123')->first();
        self::assertNotEquals($userBefore->password, $userAfter->password);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);
        $userBefore = User::where('username', 'fauzan123')->first();
        $this->patch('/api/users/current', 
            [
                'name' => 'zane'
            ],
            [
                'Authorization' => 'token01'
            ])->assertStatus(200);
        $userAfter = User::where('username', 'fauzan123')->first();
        self::assertNotEquals($userBefore->name, $userAfter->name);    
    }

    public function testNoUpdateAnything()
    {
        $this->seed([UserSeeder::class]);
        $userBefore = User::where('username', 'fauzan123')->first();
        $this->patch('/api/users/current', 
            [],
            [
                'Authorization' => 'token01'
            ])->assertStatus(200);
        $userAfter = User::where('username', 'fauzan123')->first();
        self::assertEquals($userBefore->password, $userAfter->password);    
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class]);
        $userBefore = User::where('username', 'fauzan123')->first();
        $this->patch('/api/users/current', 
            [
                'name' => '',
                'password' => ''
            ]
        )->assertStatus(401);
        $userAfter = User::where('username', 'fauzan123')->first();
        self::assertEquals($userBefore->name, $userAfter->name);  
        self::assertEquals($userBefore->password, $userAfter->password);  
    }

    public function testLogoutSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->delete('/api/users/logout', headers: [
            'Authorization' => 'token01'
        ])->assertStatus(200)
            ->assertJson([
                'message' => [
                    'status' => 'OK'
                ]
            ]);
        $userToken = User::where('username', 'fauzan123')->first();
        self::assertNull($userToken->token);
    
    }
    public function testLogoutFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->delete('/api/users/logout', headers: [
            'Authorization' => 'haha123'
        ])->assertStatus(401)
            ->assertJson([
                'error_message' => [
                    'Unauthorized'
                ]
            ]);
    }

}
