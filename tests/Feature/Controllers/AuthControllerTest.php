<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $token;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->state([
            'password' => bcrypt('666')
        ])->create();

        $this->token = $this->user->createToken('email')->plainTextToken;
        
    }

    public function tearDown(): void{
        $this->user->delete();
    }
   
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
    public function testUserShouldLoginAndReturnToken() 
    {

        $payload = ['email' => $this->user->email , 'password' => '666'];

        $response = $this->post('api/login', $payload);

        $response->assertOk();
        $response->assertJsonStructure(['token' => [], 'user' => []]);

        

    }


     /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserShouldLogoutSucessfully() : void
    {

        $response = $this->get('api/logout', ['Authorization' => 'Bearer '.$this->token]);

        $response->assertOk();
        $response->assertJson(['msg' => 'Successfully logged out']);

    }
}
