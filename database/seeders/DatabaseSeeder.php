<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductGroupItem;
use App\Models\User;
use App\Models\UserProductGroup;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory(10)->create()->each(function ($user) {
            // Create product groups for some users
            UserProductGroup::factory(rand(0, 2))->create(['user_id' => $user->id])
                ->each(function ($group) {
                    // Create products and link them to the group
                    ProductGroupItem::factory(rand(1, 5))->create(['group_id' => $group->id]);
                });
        });

        // Create additional products
        Product::factory(20)->create();


        // Seed the Product table
//        Product::factory(100)->create()->each(function ($product) {
//            // For each product, seed the UserProductGroup table
//            $group = UserProductGroup::factory()->create(['user_id' => $product->user_id]);
//
//            // Seed the ProductGroupItem table
//            ProductGroupItem::factory()->create([
//                'group_id'   => $group->id,
//                'product_id' => $product->id
//            ]);
//
//            // Seed the Cart table
//            Cart::factory()->create([
//                'user_id'    => $product->user_id,
//                'product_id' => $product->id
//            ]);
//        });
    }
}
