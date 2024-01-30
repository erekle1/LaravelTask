<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'price'];
    protected $casts = [
        'price' => 'double',  // or use 'double' if you prefer
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
