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

class BookFilterTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_author_filter() {

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
        
        $response1 = $this->postJson('/api/admin/authors', [
            'name' => 'Евгений Онегин',
            'year' => '1926',
        ],
        [
            'Authorization' => 'Bearer ' .$token,
            'Accept' => 'application/json'
        ],);
        $author_id = $response1->json()['data']['id'];
        $response1->assertStatus(201);

        $response = $this->postJson('/api/admin/books/', [
            'author_id' => $author_id,
            'name' => 'Странник',
            'year' => '1943'
        ]);

        $response = $this->postJson('/api/admin/books/', [
            'author_id' => $author_id,
            'name' => 'book2',
            'year' => '1943'
        ]);

        $response = $this->postJson('/api/admin/books/', [
            'author_id' => $author_id,
            'name' => 'book3',
            'year' => '1943'
        ]);
        $response->assertStatus(201);

        $response = $this->getJson('/api/authors/', [
            'name' => 'book2'
        ]);
        if ($response == $response1)
            $response->assertStatus(200);
    }
}