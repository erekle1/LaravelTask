<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $product;

    public function setUp(): void
    {
        parent::setUp();
        // Create a user and a product for testing
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create();
    }


    /** @test */
    public function user_can_add_product_to_cart()
    {

        $response = $this->actingAs($this->user)->postJson('/api/addProductInCart', [
            'product_id' => $this->product->id
        ]);

        // Assert: check if the product was added to the cart
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'success' => true,
                    'message' => 'Product added to cart successfully.'
                ]
            ]);
        $this->assertDatabaseHas('cart', [
            'user_id'    => $this->user->id,
            'product_id' => $this->product->id
        ]);
    }

    /** @test */

    public function adding_product_to_cart_fails_with_invalid_product_id()
    {
        $response = $this->actingAs($this->user)->postJson('/api/addProductInCart', [
            'product_id' => 999 // Non-existent product ID
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation errors'
            ]);
    }


    public function user_can_remove_product_from_cart()
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $cart = Cart::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 1
        ]);

        // Act
        $response = $this->actingAs($user)->postJson('/removeProductFromCart', [
            'product_id' => $product->id
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Product removed from cart.'
            ]);
        $this->assertDatabaseMissing('cart', ['id' => $cart->id]);
    }

    /** @test */
    public function user_can_set_quantity_of_product_in_cart()
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $cart = Cart::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 1
        ]);

        // Act
        $response = $this->actingAs($user)->postJson('/setCartProductQuantity', [
            'product_id' => $product->id,
            'quantity'   => 3
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cart quantity updated successfully.'
            ]);
        $this->assertDatabaseHas('cart', [
            'id'       => $cart->id,
            'quantity' => 3
        ]);
    }

    /** @test */
    public function user_can_get_their_cart()
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create();
        Cart::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 2
        ]);

        // Act
        $response = $this->actingAs($user)->getJson('/getUserCart');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'products' => [
                    '*' => ['product_id', 'quantity', 'price']
                ],
                'discount'
            ]);
    }


}
