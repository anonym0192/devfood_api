<?php 

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;


class UserControllerTest extends TestCase{

    use RefreshDatabase;

    private $user;

    private $token;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

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
    public function testUserShouldBeRegisteredSuccessfully()
    {
    
        $payload = [
            'name' => 'Some Other UserName',
            'email' => 'changedemail@net.com', 
            'password' => '123',
            'password_confirmation' => '123', 
            'cpf' => '22222222222222', 
            'area_code' => '21',
            'born_date' => '1995-01-01',
            'phone' => '66666666',
        ];
        
        $response = $this->post('api/register', $payload);
        
        $response->assertCreated();
        $response->assertJsonStructure(['token' => [], 'user' => ['id','name', 'email', 'cpf', 'born_date', 'phone', 'area_code']]);

        $this->assertDatabaseHas($this->user , ['name' => $payload['name'] , 'email' => $payload['email'], 'cpf' => $payload['cpf'], 'area_code' => $payload['area_code'], 'born_date' => $payload['born_date'], 'phone' => $payload['phone']]);
        
        $createdUser = $response['user']['id'];
        User::destroy($createdUser);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    
   public function testUserShouldBeUpdatedSuccessfully(): array
   {

        $payload = [
            'name' => 'Some Other UserName',
            'email' => Str::random(5).'@net.com', 
            'password' => 'changedpwd',
            'password_confirmation' => 'changedpwd', 
            'cpf' => '22222222222', 
            'area_code' => '21',
            'born_date' => '1995-01-01',
            'phone' => '66666666',
        ];
        
        $response = $this->put("api/user/".$this->user->id , $payload, ['Authorization' => 'Bearer '.$this->token]);
        
        $response->assertOk();
        $response->assertJsonStructure(['msg' , 'user' => ['id','name', 'email', 'cpf', 'born_date', 'phone', 'area_code']]);

        $this->assertDatabaseHas($this->user, ['id' => $this->user->id,'name' => $payload['name'] , 'email' => $payload['email'], 'cpf' => $payload['cpf'], 'area_code' => $payload['area_code'], 'born_date' => $payload['born_date'], 'phone' => $payload['phone']]);

        return $payload;
   }

    /**
     * @depends testUserShouldBeUpdatedSuccessfully
     */
    public function testUserUpdateUsingDifferentId(array $payload): void
    {
             
        $response = $this->put("api/user/666666", $payload, ['Authorization' => 'Bearer '.$this->token]);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Unauthorized']);

    }


   public function testOnlyAdminCanDeleteUsers()
    {
        //A non admin user created
        $user = User::factory()->state(['admin' => 0])->create();
        
        $token = $user->createToken('email')->plainTextToken;

        $response = $this->delete("api/user/".$user->id, [], ['Authorization' => 'Bearer '.$token]);

        $response->assertUnauthorized();
    
        $this->assertDatabaseHas($user, ['id' => $user->id]);

        $user->delete();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserDeleteIdDoesnotExist()
    {

        $response = $this->delete("api/user/548785558", [], ['Authorization' => 'Bearer '.$this->token]);

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
  
        $response = $this->delete("api/user/".$this->user->id, [], ['Authorization' => 'Bearer '.$this->token]);

        $response->assertOk();
        $response->assertJson(['msg' => "User ".$this->user->id." deleted successfully!"]);

        $this->assertDatabaseMissing($this->user, ['id' => $this->user->id]);
        
    }


}