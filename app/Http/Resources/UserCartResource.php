<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cartProducts = $this->resource->carts()->with('product')->get();
        $discountedProducts = $this->resource->productGroupItems()->with('product')->get();

        $discountedProductsIds = $this->getDiscountedProductIds($discountedProducts);
        $minQuantity = $this->getMinQuantity($discountedProductsIds);
        $discountPercent = $this->resource->userProductGroups()->first()?->discount ?? 0;

        $products = $this->getCartProductDetails($cartProducts, $discountedProductsIds, $minQuantity, $discountPercent);
        $discountSum = $this->calculateDiscountSum($cartProducts, $discountedProductsIds, $minQuantity, $discountPercent);

        return [
            'data' => [
                'products'            => $products,
                'discount'            => $discountSum,
                'discounted_products' => $this->areAllCartProductsInUserProductGroups($cartProducts, $discountedProducts),
            ],
        ];
    }

    protected function getDiscountedProductIds($discountedProducts): array
    {
        return $discountedProducts->pluck('product.id')->unique()->toArray();
    }

    protected function getMinQuantity(array $discountedProductsIds): int|float
    {
        return $this->resource->carts()->whereIn('product_id', $discountedProductsIds)->min('quantity') ?? 0;
    }

    protected function getCartProductDetails($cartProducts, array $discountedProductsIds, $minQuantity, float $discountPercent): array
    {
        $products = [];
        foreach ($cartProducts as $cartProduct) {
            $products[] = [
                'product_id' => $cartProduct->product_id,
                'quantity'   => $cartProduct->quantity,
                'price'      => $cartProduct->product->price
            ];
        }

        return $products;
    }

    protected function calculateDiscountSum($cartProducts, array $discountedProductsIds, $minQuantity, float $discountPercent): float
    {
        $discountSum = 0;
        foreach ($cartProducts as $cartProduct) {
            if (in_array($cartProduct->product_id, $discountedProductsIds)) {
                $discountSum += ($cartProduct->product->price / 100) * $discountPercent * $minQuantity;
            }
        }

        return $discountSum;
    }

    protected function areAllCartProductsInUserProductGroups($cartProducts, $discountedProducts): bool
    {
        $cartProductIds = $cartProducts->pluck('product.id')->unique();
        $discountedProductsIds = $discountedProducts->pluck('product.id')->unique();

        return $discountedProductsIds->every(fn($value) => $cartProductIds->contains($value));
    }
}
