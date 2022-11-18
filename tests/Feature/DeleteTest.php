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

class DeleteTest extends TestCase
{
    use refreshdatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function test_delete_author() {
    //     $response = $this->postJson('/api/authors', [
    //         'name' => 'Владимир Владимирович Жириновский',
    //         'year' => '1969'
    //     ]);
    //     $response->assertStatus(201);

    //     $author_id = $response->json()['Author']['id'];
    //     // dump($response->json()['Author']['id']);

    //     $response = $this->deleteJson('/api/authors/'. $author_id, [
    //         'message' => 'Author successfuly deleted'
    //     ]);
    //     return response()->json([
    //         'message' => 'Author successfuly deleted',
    //     ], 204);

    //     $response->assertJsonFragment([
    //         'name' => 'Владимир Владимирович Жириновский',
    //     ], 404);
    // }


    // public function test_delete_book() {
    //     $response = $this->postJson('/api/authors', [
    //         'name' => 'Эрих Мария Ремарк',
    //         'year' => '1926'
    //     ]);
    //     $response->assertStatus(201);
    //     $author_id = $response->json()['Author']['id'];

    //     $response = $this->postJson('/api/books/', [
    //         'author_id' => $author_id,
    //         'name' => 'Ночь в Лиссабоне',
    //         'year' => '1943'
    //     ]);
    //     $book_id = $response->json()['book']['id'];

    //     $response = $this->deleteJson('/api/books/'. $book_id, [
    //         'message' => 'Book successfuly deleted'
    //     ]);
    //     return response()->json([
    //         'message' => 'Author successfuly deleted',], 204);

    //     $response->assertJsonFragment([
    //         'name' => 'Ночь в Лиссабоне',
    //     ], 404);
    // }

    public function test_ability_delete_all_books() {

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
            'name' => 'Ночь в Лиссабоне',
            'year' => '1943'
        ]);
        $book_id_1 = $response->json()['data']['id'];

        $response = $this->postJson('/api/admin/books/', [
            'author_id' => $author_id,
            'name' => 'Ослиная шкура',
            'year' => '1911'
        ]);
        $book_id_2 = $response->json()['data']['id'];

        $response = $this->postJson('/api/admin/books/', [
            'author_id' => $author_id,
            'name' => 'Программист на горе',
            'year' => '1973'
        ]);
        $book_id_3 = $response->json()['data']['id'];


        $response = $this->postJson('/books/admin/delete_many', [
            'ids' => $book_id_1, $book_id_2, $book_id_3
        ]);
        return response()->json([
            'message' => 'Books successfuly deleted',], 204);

        $response->assertJsonFragment([
            'name' => 'Ночь в Лиссабоне',
            'name' => 'Ослиная шкура',
            'name' => 'Программист на горе'
        ], 403);
    }



    public function test_ability_delete_author() {

        $response = $this->postJson('/api/register', [  
            'name' => 'Джон Сноу',
            'email' => 'Snow.02@mail.ru',
            'password' => '12234879',
            'is_admin' => 1
        ]);
        
        $response = $this->postJson('/api/login', [  
            'email' => 'Snow.02@mail.ru',
            'password' => '12234879',
        ]);
        $token = $response->json()['data']['attributes']['token'];
        $response->assertStatus(200);
        
        $response = $this->postJson('/api/admin/authors', [
            'name' => 'Марк Остин',
            'year' => '1926',
        ],
        [
            'Authorization' => 'Bearer ' .$token,
            'Accept' => 'application/json'
        ],);
        $author_id = $response->json()['data']['id'];
        // dump($response->json()['Author']['id']);

        $response = $this->deleteJson('/api/admin/authors/'. $author_id, [
            'message' => 'Author successfuly deleted'
        ]);
        return response()->json([
            'message' => 'Author successfuly deleted',
        ], 204);
    }


    public function test_ability_delete_fail_author() {

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


    public function test_ability_delete_book() {

        $response = $this->postJson('/api/register', [  
            'name' => 'Joe joe',
            'email' => 'joe.02@mail.ru',
            'password' => '12234879',
            'is_admin' => 1
        ]);
        
        $response = $this->postJson('/api/login', [  
            'email' => 'joe.02@mail.ru',
            'password' => '12234879',
        ]);
        $token = $response->json()['data']['attributes']['token'];
        $response->assertStatus(200);
        
        $response = $this->postJson('/api/admin/authors', [
            'name' => 'Лев Николаевич Толстой',
            'year' => '1926',
        ],
        [
            'Authorization' => 'Bearer ' .$token,
            'Accept' => 'application/json'
        ],);
        $author_id = $response->json()['data']['id'];

        $response = $this->postJson('/api/admin/books/', [
            'author_id' => $author_id,
            'name' => 'Война и Мир',
            'year' => '1943'
        ]);
        $book_id = $response->json()['data']['id'];

        $response = $this->deleteJson('/api/admin/books/'. $book_id, [
            'message' => 'Book successfuly deleted'
        ]);
        return response()->json([
            'message' => 'Book successfuly deleted',
        ], 201);
    }

    public function test_ability_delete_fail_book() {

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