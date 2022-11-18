<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Factories\TModel;
use Illuminate\Contracts\Auth\Authenticatable;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    // public function test_user() {
    //     $user = User::factory()->make();
    //         $response = $this->postJson(
    //             '/api/login',
    //             ['email'=> $user->email, 'password'=>'2345678']);
    //         $response->assertStatus(200);
    // }

    public function test_logout() {

        $user =  User::factory()->create();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->postJson('/api/logout');
        $response->assertStatus(204);
    }
}
