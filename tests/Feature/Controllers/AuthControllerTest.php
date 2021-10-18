<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
   
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testWrongPasswordLoginShouldFail()
    {
        
        $user = User::factory()->make();
        
        $payload = ['email' => $user->email, 'password' => Str::random(13) ];


        $response = $this->post('api/login', $payload);
        

        $response->assertStatus(401);
        $response->assertJson(['error' => 'The provided credentials are incorrect.']);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserShouldLoginAndReturnToken() : string 
    {
       

        $password = Str::random(13);

        $user = User::factory()->state([
            'cpf' => '11111111111',
            'password' => bcrypt($password)
        ])->create();


        $payload = ['email' => $user->email , 'password' => $password];

        $response = $this->post('api/login', $payload);

        $response->assertOk();
        $response->assertJsonStructure(['token' => [], 'user' => []]);

        return $response['token'];

    }

     

     /**
     * @depends testUserShouldLoginAndReturnToken
     */
    public function testUserShouldLogoutSucessfully(string $token) : void
    {

        $response = $this->get('api/logout', ['Authorization' => 'Bearer '.$token]);

        $response->assertOk();
        $response->assertJson(['msg' => 'Successfully logged out']);

    }
}
