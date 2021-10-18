<?php 

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Product;

class ProductControllerTest extends TestCase{


    public function testNormalUserCannotCreateProduct(): void {


    }

    public function testUploadProductImageShouldBesuccessfull(): void {


        $user = User::factory()->state(['admin' => 1])->create();
        
        $token = $user->createToken('email')->plainTextToken;

        $product = Product::factory()->state(['image' => null])->create();

        $mockImage = UploadedFile::fake()->image('fakeimage.jpg', 800, 800);

        $response = $this->post("api/image/product/$product->id", ['image' => $mockImage], ['Authorization' => 'Bearer '.$token]);

        $response->assertOk();
        $response->assertJsonStructure(['msg' , 'url']);

        $product = $product->fresh();

        $this->assertNotNull($product->image);

    }
}