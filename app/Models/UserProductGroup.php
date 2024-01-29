<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};


class UserProductGroup extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'discount'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productGroupItems(): HasMany
    {
        return $this->hasMany(ProductGroupItem::class,'group_id','id');
    }
}

