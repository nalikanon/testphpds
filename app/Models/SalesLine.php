<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesLine extends Model
{
    protected $table = 'Sales_Lines';
    protected $primaryKey = 'line_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'order_id',
        'item_id',
        'quantity',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'line_total',
        'remark',
        'line_no',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'order_id', 'order_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }
}

