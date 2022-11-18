<?php

namespace Tests\Feature;

use App\Models\Author;

use Illuminate\Database\Eloquent\Factories\TModel;
use Illuminate\Contracts\Auth\Authenticatable;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use refreshdatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_admin() {

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

        $response->assertJsonFragment([
            'name' => 'Марк Остин',
        ]);
    }



    public function test_ability_admin() {

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

        $response->assertJsonFragment([
            'name' => 'Марк Остин',
        ]);
    }

    public function test_not_authorzied() {
        $response = $this->postJson('/api/register', [  
            'name' => 'Джей Колинз',
            'email' => 'kolinz.02@mail.ru',
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