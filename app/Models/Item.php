<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'Items';
    protected $primaryKey = 'item_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'item_code',
        'item_name',
        'description',
        'unit',
        'unit_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function salesLines()
    {
        return $this->hasMany(SalesLine::class, 'item_id', 'item_id');
    }
}

