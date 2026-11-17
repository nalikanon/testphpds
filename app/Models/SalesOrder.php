<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $table = 'Sales_Orders';

    protected $primaryKey = 'order_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'order_no',
        'customer_id',
        'order_date',
        'delivery_date',
        'total_amount',
        'discount_percent',
        'discount_amount',
        'net_amount',
        'vat_percent',
        'vat_amount',
        'grand_total',
        'status',
        'remark',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function isOwnedBy(User $user): bool
    {
        return (string) $this->created_by === (string) $user->user_id;
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if (! $user->isAdmin()) {
            $query->where('created_by', $user->user_id);
        }

        return $query;
    }

    public function lines()
    {
        return $this->hasMany(SalesLine::class, 'order_id', 'order_id')->orderBy('line_no');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'confirmed' => '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> ยืนยันแล้ว (Confirmed)</span>',
            'cancelled' => '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> ยกเลิก (Cancelled)</span>',
            default => '<span class="badge bg-warning text-dark"><i class="bi bi-file-earmark-text me-1"></i> แบบร่าง (Draft)</span>',
        };
    }
}
