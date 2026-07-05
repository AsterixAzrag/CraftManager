<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'material_id',
        'user_id',
        'type',
        'quantity',
        'unit_cost',
        'reason',
        'active',
        'reversed_by',
        'reversed_at',
        'reference_type',
        'reference_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'active' => 'boolean',
            'reversed_at' => 'datetime',
        ];
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reverser()
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
