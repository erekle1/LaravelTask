<?php

namespace App\Http\Resources;

use App\Models\UserProductGroup;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        // Fetch product groups and items for the user in one query
        $userGroups = UserProductGroup::with('productGroupItems.product')
            ->where('user_id', $this->user_id)
            ->get();

        $products = $this->map(function ($cartItem) use ($userGroups) {
            $regularPrice = $cartItem->product->price;
            $productInGroup = $this->findProductInGroups($cartItem->product_id, $userGroups);

            // If the product is in a discount group, calculate the discounted price
            if ($productInGroup) {
                $discountPercentage = $productInGroup['discount'];
                $discountedPrice = $regularPrice - ($regularPrice * ($discountPercentage / 100));
            } else {
                $discountedPrice = $regularPrice;
            }

            return [
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $discountedPrice
            ];
        });


        return ['products' => $products, 'discount' => $this->calculateTotalDiscount($userGroups)];
    }

    // Calculate the total discount based on the products in the cart and user's product groups

    private function calculateTotalDiscount($userGroups): float
    {
        $totalDiscount = 0;

        // Iterate over each cart item
        foreach ($this->cartItems as $cartItem) {
            // Check if the cart item is in any of the user's product groups
            foreach ($userGroups as $group) {
                foreach ($group->productGroupItems as $groupItem) {
                    if ($groupItem->product_id === $cartItem->product_id) {
                        // Calculate discount based on the lower quantity between cart and group
                        $applicableQuantity = min($cartItem->quantity, $groupItem->quantity);
                        // Calculate the discount amount for this product
                        $discountAmount = $applicableQuantity * $cartItem->product->price * ($group->discount / 100);
                        $totalDiscount += $discountAmount;
                        break 2; // Break out of both inner loops once the discount is found
                    }
                }
            }
        }

        return $totalDiscount;
    }
    // Find if a product is in any user's product groups and return discount info

    private function findProductInGroups($productId, $userGroups): ?array
    {
        foreach ($userGroups as $group) {
            foreach ($group->productGroupItems as $item) {
                if ($item->product_id == $productId) {
                    return ['discount' => $group->discount]; // Discount is a percentage
                }
            }
        }
        return null;
    }
}
