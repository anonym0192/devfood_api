<?php 

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;


class UserControllerTest extends TestCase{

    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserShouldBeRegisteredSuccessfully()
    {
        
        $user = User::factory()->make();
     
        $userPassword = Str::random(13);
        
        $payload = [
                'name' => $user->name,
                'email' => $user->email, 
                'password' => $userPassword,
                'password_confirmation' => $userPassword, 
                'cpf' => $user->cpf, 
                'area_code' => $user->area_code,
                'born_date' => $user->born_date,
                'phone' => $user->phone,
            ];
        

        $response = $this->post('api/register', $payload);
    
        $response->assertCreated();
        $response->assertJsonStructure(['token' => [], 'user' => ['id','name', 'email', 'cpf', 'born_date', 'phone', 'area_code']]);

        $this->assertDatabaseHas($user, ['name' => $user->name , 'email' => $user->email, 'cpf' => $user->cpf, 'area_code' => $user->area_code, 'born_date' => $user->born_date, 'phone' => $user->phone]);
        
    }

       /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserUpdateUsingDifferentId()
    {
        
        $user = User::factory()->create();

        $token = $user->createToken('email')->plainTextToken;
        
        $payload = [
            'name' => 'Some Other UserName',
            'email' => 'changedemail@net.com', 
            'password' => 123,
            'password_confirmation' => 123, 
            'cpf' => '22222222222222', 
            'area_code' => '21',
            'born_date' => '1995-01-01',
            'phone' => '66666666',
        ];


        $response = $this->put("api/user/666666", $payload, ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthorized']);

    }
    /*
    * A basic test example.
    *
    * @return void
    */
   public function testUserShouldBeUpdatedSuccessfully()
   {
       
        $user = User::factory()->create();

        $token = $user->createToken('email')->plainTextToken;
        
        $payload = [
            'name' => 'Some Other UserName',
            'email' => 'changedemail@net.com', 
            'password' => 123,
            'password_confirmation' => 123, 
            'cpf' => '22222222222', 
            'area_code' => '21',
            'born_date' => '1995-01-01',
            'phone' => '66666666',
        ];


        $response = $this->put("api/user/$user->id", $payload, ['Authorization' => 'Bearer '.$token]);

        $response->assertOk();
        $response->assertJsonStructure(['msg' , 'user' => ['id','name', 'email', 'cpf', 'born_date', 'phone', 'area_code']]);

        $this->assertDatabaseHas($user, ['id' => $user->id,'name' => $payload['name'] , 'email' => $payload['email'], 'cpf' => $payload['cpf'], 'area_code' => $payload['area_code'], 'born_date' => $payload['born_date'], 'phone' => $payload['phone']]);


   }

   public function testOnlyAdminCanDeleteUsers()
    {
        $user = User::factory()->state(['admin' => 0])->create();
        
        $token = $user->createToken('email')->plainTextToken;

        $response = $this->delete("api/user/$user->id", [], ['Authorization' => 'Bearer '.$token]);

        $response->assertUnauthorized();
    
        $this->assertDatabaseHas($user, ['id' => $user->id]);

    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserDeleteIdDoesnotExist()
    {
        
        $user = User::factory()->create();

        $token = $user->createToken('email')->plainTextToken;

        $response = $this->delete("api/user/548785558", [], ['Authorization' => 'Bearer '.$token]);

        $response->assertStatus(400);
        $response->assertJsonStructure(['error']);

    
    }

      /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserShouldBeDeletedSuccessfully()
    {
        $user = User::factory()->state(['admin' => 1])->create();

    
        $token = $user->createToken('email')->plainTextToken;

        $response = $this->delete("api/user/$user->id", [], ['Authorization' => 'Bearer '.$token]);

        $response->assertOk();
        $response->assertJson(['msg' => "User $user->id deleted successfully!"]);

        $this->assertDatabaseMissing($user, ['id' => $user->id]);
        
    }





}