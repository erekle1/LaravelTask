<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class ProductGroupItem extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'product_id'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(UserProductGroup::class, 'group_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
