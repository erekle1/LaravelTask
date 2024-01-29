<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};


class Cart extends Model
{

    use HasFactory;

    protected $table='cart';

    protected $fillable = ['user_id', 'product_id', 'quantity'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateDiscount()
    {
        $discount = 0;

        // Group cart items by product
        $groupedItems = $this->groupBy('product_id')
            ->with('product.productGroupItems.group')
            ->get();

        foreach ($groupedItems as $group) {
            $groupCount = $group->count();
            foreach ($group as $item) {
                $productGroups = $item->product->productGroupItems;
                foreach ($productGroups as $productGroup) {
                    if ($productGroup->group->user_id === $this->user_id) {
                        $discount += min($groupCount, $productGroup->group->discount);
                        break;
                    }
                }
            }
        }

        return $discount;
    }


    public function productGroupItems()
    {
        return $this->hasManyThrough(
            ProductGroupItem::class,
            UserProductGroup::class,
            'user_id',
            'group_id'
        );
    }

}
