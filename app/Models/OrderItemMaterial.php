<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemMaterial extends Model
{
    protected $fillable = [
        'order_item_id',
        'material_id',
        'quantity',
        'cost',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'cost' => 'decimal:2',
        ];
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
