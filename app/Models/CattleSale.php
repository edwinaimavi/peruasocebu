<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CattleSale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cattle_id', 'seller_owner_id', 'buyer_owner_id', 'sale_date',
        'sale_price', 'currency', 'payment_method', 'contract_file_path',
        'notes', 'status',
    ];

    protected function casts(): array
    {
        return ['sale_date' => 'date', 'sale_price' => 'decimal:2'];
    }

    public function cattle(): BelongsTo
    {
        return $this->belongsTo(Cattle::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'seller_owner_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'buyer_owner_id');
    }
}
