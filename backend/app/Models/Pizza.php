<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pizza extends Model
{
    use HasFactory;

    protected $primaryKey = 'pizza_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'pizza_id',
        'pizza_type_id',
        'size',
        'price',
    ];

    public function pizzaType(): BelongsTo
    {
        return $this->belongsTo(PizzaType::class, 'pizza_type_id', 'pizza_type_id');
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'pizza_id', 'pizza_id');
    }
}
