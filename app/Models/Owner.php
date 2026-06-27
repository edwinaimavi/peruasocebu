<?php

namespace App\Models;

use App\Models\Concerns\DecodesTextValues;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model
{
    use DecodesTextValues, SoftDeletes;

    protected $fillable = [
        'document_type', 'document_number', 'full_name', 'business_name',
        'phone', 'email', 'address', 'photo_path', 'owner_type', 'notes', 'status',
    ];

    protected array $decodedTextAttributes = [
        'full_name', 'business_name', 'address', 'notes',
    ];

    public function cattle(): HasMany
    {
        return $this->hasMany(Cattle::class, 'current_owner_id');
    }

    public function ownershipHistories(): HasMany
    {
        return $this->hasMany(OwnershipHistory::class);
    }

    public function salesAsSeller(): HasMany
    {
        return $this->hasMany(CattleSale::class, 'seller_owner_id');
    }

    public function salesAsBuyer(): HasMany
    {
        return $this->hasMany(CattleSale::class, 'buyer_owner_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

}
