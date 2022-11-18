<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Factories\TModel;
use Illuminate\Contracts\Auth\Authenticatable;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use refreshdatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function test_register() {
    //     $user = User::factory()->make();
    //         $response = $this->postJson(
    //             '/api/register',
    //             ['name'=>$user->name,'email'=> $user->email,'password'=>'3747597']);
    //         $response->assertStatus(200);
    // }

    public function test_ability_register() {
    $response = $this->postJson('/api/register', [  
        'name' => 'Малик',
        'email' => 'Malic.01@mail.ru',
        'password' => '12234879',
        'is_admin' => 0
    ]);
    $token = $response->json()['data']['attributes']['token'];
    
    $response = $this->postJson('/api/login', [  
        'email' => 'Malic.01@mail.ru',
        'password' => '12234879',
    ]);
    $response->assertStatus(200);

    $response->assertJsonFragment([
        'email' => 'Malic.01@mail.ru',
    ]);
    }

    public function test_ability_second_register() {
        $response = $this->postJson('/api/register', [  
            'name' => 'Малик',
            'email' => 'Malic.01@mail.ru',
            'password' => '12234879',
            'is_admin' => 1
        ]);
        $token = $response->json()['data']['attributes']['token'];
        
        $response = $this->postJson('/api/login', [  
            'email' => 'Malic.01@mail.ru',
            'password' => '12234879',
        ]);
        $response->assertStatus(200);
    
        $response->assertJsonFragment([
            'email' => 'Malic.01@mail.ru',
        ]);
    }
}