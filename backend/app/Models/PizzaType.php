<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PizzaType extends Model
{
    use HasFactory;

    protected $primaryKey = 'pizza_type_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'pizza_type_id',
        'name',
        'category',
        'ingredients',
    ];

    public function pizzas(): HasMany
    {
        return $this->hasMany(Pizza::class, 'pizza_type_id', 'pizza_type_id');
    }
}
