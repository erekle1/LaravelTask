<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddProductToCartRequest;
use App\Http\Requests\RemoveProductFromCartRequest;
use App\Http\Requests\SetCartProductQuantityRequest;
use App\Http\Resources\ApiResponseResource;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CartController extends Controller
{
    /**
     * Add a product to the authenticated user's cart.
     *
     * @param AddProductToCartRequest $request
     * @return ApiResponseResource
     */
    public function addToCart(AddProductToCartRequest $request): ApiResponseResource
    {
        // Add the specified product to the user's cart with a default quantity of 1
        auth()->user()->carts()->create([
            'product_id' => $request->product_id,
            'quantity' => 1,
        ]);

        // Return a success response
        return new ApiResponseResource([
            'success' => true,
            'message' => 'Product added to cart successfully.'
        ]);
    }

    /**
     * Remove a product from the authenticated user's cart.
     *
     * @param RemoveProductFromCartRequest $request
     * @return ApiResponseResource
     */
    public function removeFromCart(RemoveProductFromCartRequest $request): ApiResponseResource
    {
        // Remove the specified product from the user's cart
        auth()->user()->carts()->where('product_id', $request->product_id)->delete();

        // Return a success response
        return new ApiResponseResource([
            'success' => true,
            'message' => 'Product removed from cart.'
        ]);
    }

    /**
     * Set the quantity for a product in the authenticated user's cart.
     *
     * @param SetCartProductQuantityRequest $request
     * @return ApiResponseResource
     */
    public function setQuantity(SetCartProductQuantityRequest $request)
    {
        // Find the cart item and update its quantity
        $cartItem = auth()->user()->carts()
            ->where('product_id', $request->product_id)
            ->first();

        $cartItem->update(['quantity' => $request->quantity]);

        // Return a success response
        return new ApiResponseResource([
            'success' => true,
            'message' => 'Cart updated successfully.'
        ]);
    }

    /**
     * Retrieve the authenticated user's cart items.
     *
     * @param Request $request
     */
    public function getUserCart(Request $request)
    {

        $user = $request->user();


        dd($this->areAllCartProductsInUserProductGroups($user));
        // Load the user's carts and the associated products for each cart item.
        $carts = $user->carts()->with('product')->get();


        foreach ($carts as $cart) {

        }


        // Load the user product groups and their related items.
        $userProductGroups = $user->userProductGroups()->with('productGroupItems.product')->get();

        return new CartResource(['carts' => $carts, 'userProductGroups' => $userProductGroups]);


//        return response()->json(['error' => 'Cart not found'], 404);
    }


    public function areAllCartProductsInUserProductGroups(User $user)
    {
        // Fetch product IDs in the user's cart
        $cartProductIds = $user->carts()->with('product')->get()
            ->pluck('product.id')->unique();

        // Fetch product IDs in the user's product groups
        $groupProductIds = $user->productGroupItems()->with('product')->get()
            ->pluck('product.id')->unique();

        // Check if all cart products are in the product groups
        return $cartProductIds->diff($groupProductIds)->isEmpty();
    }


}
