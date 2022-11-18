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

class BookCreateTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_book() {

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
            'name' => 'Евгений Онегин',
            'year' => '1926',
        ],
        [
            'Authorization' => 'Bearer ' .$token,
            'Accept' => 'application/json'
        ],);
        $response->assertStatus(201);
    }


    public function test_ability_book() {
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
            'Authorization' => 'Bearer ' .$token,
            'Accept' => 'application/json'
        ],);
        $author_id = $response->json()['data']['id'];

        $response = $this->postJson('/api/admin/books/', [
            'author_id' => $author_id,
            'name' => 'Странник',
            'year' => '1943'
        ]);
        $response->assertStatus(201);

        $response->assertJsonFragment([
            'name' => 'Странник',
        ]);
    }

    public function test_ability_book_fail() {
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
}