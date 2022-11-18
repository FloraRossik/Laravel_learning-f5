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

class UpdateTest extends TestCase
{
    use refreshdatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_update_author() {
        $response = $this->postJson('/api/register', [  
            'name' => 'Джим Керри',
            'email' => 'Джим.02@mail.ru',
            'password' => '12234879',
            'is_admin' => 1
        ]);

        $response = $this->postJson('/api/login', [  
            'email' => 'Джим.02@mail.ru',
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
        ]);

        $author_id = $response->json()['data']['id'];

        $response = $this->postJson('/api/admin/books/', [
            'author_id' => $author_id,
            'name' => 'Бутылка',
            'year' => '1974'
        ]);
        $response->assertStatus(201);
    }

    public function test_update_book() {
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
        $author_id = $response->json()['data']['id'];

        $response = $this->postJson('/api/admin/books/', [
            'author_id' => $author_id,
            'name' => 'Бутылка',
            'year' => '1974'
        ]);
        $book_id = $response->json()['data']['id'];

        $response = $this->putJson('/api/admin/books/'.$book_id, [
            'author_id' => $author_id,
            'name' => 'Обновление',
            'year' => '1978'
        ]);
    }

    public function test_ability_update_author() {

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
        $author_id = $response->json()['data']['id'];

        $response = $this->postJson('/api/admin/books/', [
            'author_id' => $author_id,
            'name' => 'Бутылка',
            'year' => '1974'
        ]);
        $response->assertStatus(201);
    }

    public function test_ability_update_fail_author() {

        $response = $this->postJson('/api/register', [  
            'name' => 'Джон Сноу',
            'email' => 'Snow.02@mail.ru',
            'password' => '12234879',
            'is_admin' => 0
        ]);
        $token = $response->json()['data']['attributes']['token'];

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


    public function test_ability_update_book() {

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
        $author_id = $response->json()['data']['id'];

        $response = $this->postJson('/api/admin/books/', [
            'author_id' => $author_id,
            'name' => 'Бутылка',
            'year' => '1974'
        ]);
        $book_id = $response->json()['data']['id'];

        $response = $this->putJson('/api/admin/books/'.$book_id, [
            'author_id' => $author_id,
            'name' => 'Обновление',
            'year' => '1978'
        ]);
    }

    public function test_ability_update_fail_book() {

        $response = $this->postJson('/api/register', [  
            'name' => 'Джон Сноу',
            'email' => 'Snow.02@mail.ru',
            'password' => '12234879',
            'is_admin' => 0
        ]);
        $token = $response->json()['data']['attributes']['token'];

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