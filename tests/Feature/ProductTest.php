<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['manage-products']);
    }

    /** @test */
    public function it_can_create_a_product()
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
            'stock' => 10
        ];

        $response = $this->postJson('/api/v1/products', $productData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'id',
                         'name',
                         'description',
                         'price',
                         'stock'
                     ]
                 ]);

        $this->assertDatabaseHas('products', $productData);
    }

    /** @test */
    public function it_can_list_products()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_can_show_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson('/api/v1/products/' . $product->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $product->id,
                         'name' => $product->name
                     ]
                 ]);
    }

    /** @test */
    public function it_can_update_a_product()
    {
        $product = Product::factory()->create();
        $updatedData = [
            'name' => 'Updated Product',
            'price' => 199.99
        ];

        $response = $this->putJson('/api/v1/products/' . $product->id, $updatedData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Product updated successfully!',
                     'data' => [
                         'name' => 'Updated Product',
                         'price' => '199.99'
                     ]
                 ]);

        $this->assertDatabaseHas('products', $updatedData);
    }

    /** @test */
    public function it_can_delete_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson('/api/v1/products/' . $product->id);

        $response->assertStatus(202)
                 ->assertJson([
                     'message' => 'Product deleted successfully!'
                 ]);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}