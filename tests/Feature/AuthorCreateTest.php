<?php

namespace Tests\Feature;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factory\Factory;

use Illuminate\Database\Eloquent\Factories\TModel;
use Illuminate\Contracts\Auth\Authenticatable;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthorCreateTest extends TestCase
{
    use refreshdatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_author() {

        $response = $this->postJson('/api/register', [  
                'name' => 'Александр Сергеевич Пушкин',
                'email' => 'Alex.02@mail.ru',
                'password' => '12234879',
                'is_admin' => 1
        ]);
        
        $response = $this->postJson('/api/login', [  
            'email' => 'Alex.02@mail.ru',
            'password' => '12234879',
        ]);
        $response->assertStatus(200);
    }

    public function test_ability_author() {
        $response = $this->postJson('/api/register', [  
            'name' => 'Джей Колинз',
            'email' => 'kolinz.02@mail.ru',
            'password' => '12234879',
            'is_admin' => 1
        ]);
       
        
        $response = $this->postJson('/api/login', [  
            'email' => 'kolinz.02@mail.ru',
            'password' => '12234879',
        ]);
        
        $token = $response->json()['data']['attributes']['token'];

        $response->assertStatus(200);
        

        $response = $this->postJson('/api/admin/authors', [
            'name' => 'Martin Iden',
            'year' => '1926',
        ],
        [
            'Authorization' => 'Bearer ' .$token,
            'Accept' => 'application/json'
        ],  201);

        $response->assertJsonFragment([
            'name' => 'Martin Iden',
        ]);
    }

    public function test_ability_author_fail() {
        $response = $this->postJson('/api/register', [  
            'name' => 'Джей Колинз',
            'email' => 'kolinz.02@mail.ru',
            'password' => '12234879',
            'is_admin' => 0
        ]);

        $response = $this->postJson('/api/login', [  
            'email' => 'kolinz.02@mail.ru',
            'password' => '12234879',
        ]);
        
        $token = $response->json()['data']['attributes']['token'];

        $response->assertStatus(200);

        $response = $this->postJson('/api/admin/authors', [
            'name' => 'Martin Iden',
            'year' => '1926',
        ],
        [
            'Authorization' => 'Bearer' .$token,
            'Accept' => 'application/json'
        ],  401);
        $response->assertStatus(401);
    }


    public function test_function_index_author() {
        $response = $this->postJson('/api/register', [  
            'name' => 'Джек Лондон',
            'email' => 'London.02@mail.ru',
            'password' => '12234879',
            'is_admin' => 1
        ]);
        
        $response = $this->postJson('/api/login', [  
            'email' => 'London.02@mail.ru',
            'password' => '12234879',
        ]);
        $token = $response->json()['data']['attributes']['token'];
        $response->assertStatus(200);
        
        $response = $this->postJson('/api/admin/authors', [
            'name' => 'Джек Лондон',
            'year' => '1926',
        ],
        [
            'Authorization' => 'Bearer' .$token,
            'Accept' => 'application/json'
        ],);
        
        $response = $this->getJson('/api/authors/0', [
            'Authorization' => 'Bearer' .$token,
            'Accept' => 'application/json'
        ], 404);
    }
}