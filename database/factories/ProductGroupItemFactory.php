<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\UserProductGroup;
use App\Models\ProductGroupItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductGroupItem>
 */
class ProductGroupItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ProductGroupItem::class;

    public function definition(): array
    {
        return [
            'group_id' => UserProductGroup::factory(),
            'product_id' => Product::factory()
        ];
    }
}
