<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['manage-sales']);
    }

    /** @test */
    public function it_can_create_a_sale()
    {
        $product = Product::factory()->create(['stock' => 10]);
        
        $saleData = [
            'total_amount' => 99.99,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 49.99
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/sales', $saleData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'id',
                         'total_amount',
                         'status'
                     ]
                 ]);

        $this->assertDatabaseHas('sales', ['total_amount' => 99.99]);
        $this->assertDatabaseHas('sale_items', ['product_id' => $product->id, 'quantity' => 2]);
        $this->assertEquals(8, $product->fresh()->stock);
    }

    /** @test */
    public function it_fails_when_insufficient_stock()
    {
        $product = Product::factory()->create(['stock' => 1]);
        
        $saleData = [
            'total_amount' => 99.99,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 49.99
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/sales', $saleData);

        $response->assertStatus(500);
        $this->assertDatabaseMissing('sales', ['total_amount' => 99.99]);
    }

    /** @test */
    public function it_ensures_rollback_on_error()
    {
        $product = Product::factory()->create(['stock' => 10]);
        
        $saleData = [
            'total_amount' => 99.99,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 49.99
                ],
                [
                    'product_id' => 999999,
                    'quantity' => 1,
                    'unit_price' => 49.99
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/sales', $saleData);

        $response->assertStatus(500);
        $this->assertDatabaseMissing('sales', ['total_amount' => 99.99]);
        $this->assertEquals(10, $product->fresh()->stock);
    }

    /** @test */
    public function it_can_list_sales()
    {
        Sale::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/sales');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_can_show_a_sale()
    {
        $sale = Sale::factory()->create();

        $response = $this->getJson('/api/v1/sales/' . $sale->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $sale->id,
                         'total_amount' => $sale->total_amount
                     ]
                 ]);
    }

    /** @test */
    public function it_can_cancel_a_sale()
    {
        $product = Product::factory()->create(['stock' => 10]);
        $sale = Sale::factory()->create();
        $sale->saleItems()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 49.99
        ]);

        $response = $this->deleteJson('/api/v1/sales/' . $sale->id);

        $response->assertStatus(202)
                 ->assertJson([
                     'message' => 'Sale cancelled successfully!'
                 ]);

        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
        $this->assertEquals(12, $product->fresh()->stock);
    }
}