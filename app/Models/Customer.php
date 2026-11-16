<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'Customers';
    protected $primaryKey = 'customer_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'customer_code',
        'customer_name',
    ];

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'customer_id', 'customer_id');
    }
}

