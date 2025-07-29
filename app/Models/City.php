<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'min_price',
    ];

    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
