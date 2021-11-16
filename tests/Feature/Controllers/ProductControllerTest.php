<?php 

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\File;

class ProductControllerTest extends TestCase{

    
    private $user;

    private $token;

    private $product;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->token = $this->user->createToken('email')->plainTextToken;

        $this->product = Product::factory()->state(['image' => null])->create();
        
    }

    public function tearDown(): void{

        $this->user->delete();
        $this->product->delete();
    
    }


    public function testNormalUserCannotCreateProduct(): void {

        
        $normalUser = User::factory()->state(['admin' => 0])->create();

        $token = $normalUser->createToken('email')->plainTextToken;

        $response = $this->post("api/product", [] , ['Authorization' => 'Bearer '.$token]);

        $response->assertUnauthorized();
        $response->assertJsonStructure(['error']);

        $normalUser->delete();


    }

    public function testAdminUserCanCreateAProduct(): void{

        $payload = [
            'name' => 'Teste Product '.Str::random(20),
            'description' => 'Teste Description',
            'price' => 150,
            'category' => 1,
        ];


        $response = $this->post("api/product", $payload , ['Authorization' => 'Bearer '.$this->token]);
        $response->assertCreated();
        $response->assertJsonStructure(['msg', 'product']);

        $this->assertDatabaseHas('products', ['name' => $payload['name'], 'description' => $payload['description'], 'price' => $payload['price'], 'category_id' => $payload['category']]);

        Product::destroy($response['product']['id']);
        
    }
    
    public function testUploadProductImageShouldBesuccessfull(): void {


        $mockImage = UploadedFile::fake()->image('fakeimage.jpg', 800, 800);

        $response = $this->post("api/image/product/".$this->product->id, ['image' => $mockImage], ['Authorization' => 'Bearer '.$this->token]);

        $response->assertOk();
        $response->assertJsonStructure(['msg' , 'url']);

        $product = $this->product->fresh();

        $this->assertNotNull($product->image);

        $filepath = public_path('uploads/products/'.$product->image);
        File::delete($filepath);

    }
}