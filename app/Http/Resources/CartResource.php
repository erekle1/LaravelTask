<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Http;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $discountedProducts = [];
        $products = [];
        $totalDiscount = 0;
        $carts = $this->resource['carts'];
        $userProductGroups = $this->resource['userProductGroups'];
        foreach ($carts as $cart) {
            foreach ($cart->product as $product) {
                $groupItem = $userProductGroups->productGroupItems
                    ->firstWhere('product_id', $product->id);

                if ($groupItem) {
                    $quantityInGroup = $groupItem->count;
                    $quantityInCart = $product->cartItems->sum('quantity');
                    $discountQuantity = min($quantityInGroup, $quantityInCart);

                    $discount = $groupItem->group->discount;
                    $discountAmount = $product->price * $discount / 100 * $discountQuantity;
                    $totalDiscount += $discountAmount;

                    $discountedProducts[] = [
                        'product_id' => $product->id,
                        'discounted_quantity' => $discountQuantity,
                        'discount' => $discountAmount,
                    ];
                }
                $products[] = [
                    'product_id' => $product->id,
                    'title' => $product->title,
                    'price' => $product->price
                ];
            }
        }

        return [
            'data' => [
                'products' => $products,
                'discount' => $totalDiscount,
                'discounted_products' => $discountedProducts,
            ],
        ];
    }


    /**
     * Calculate the discounted items and total discount.
     *
     * @return array
     */
    protected function calculateDiscountedItems()
    {
        $totalDiscount = 0;
        $groupedCartItems = $this->groupByProduct();

        foreach ($this->user->productGroups as $group) {
            foreach ($group->productGroupItems as $groupItem) {
                $productId = $groupItem->product_id;
                $discountPerProduct = $group->discount; // Assuming discount is a fixed amount per product

                if (isset($groupedCartItems[$productId])) {
                    $quantityInCart = $groupedCartItems[$productId];
                    $quantityForDiscount = min($quantityInCart, $groupItem->quantity);
                    $totalDiscount += $quantityForDiscount * $discountPerProduct;
                }
            }
        }

        return [
            'totalDiscount' => $totalDiscount,
        ];
    }

    /**
     * Group cart items by product ID.
     *
     * @return array
     */
    protected function groupByProduct()
    {
        $grouped = [];
        foreach ($this->cartItems as $item) {
            $productId = $item->product_id;
            if (!isset($grouped[$productId])) {
                $grouped[$productId] = 0;
            }
            $grouped[$productId] += $item->quantity;
        }
        return $grouped;
    }
}
