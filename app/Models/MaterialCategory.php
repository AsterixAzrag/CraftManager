<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class MaterialCategory extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function productCustomizationOptions()
    {
        return $this->hasMany(ProductCustomizationOption::class);
    }
}
