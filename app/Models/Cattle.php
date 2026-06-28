<?php

namespace App\Models;

use App\Models\Concerns\DecodesTextValues;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cattle extends Model
{
    use DecodesTextValues, SoftDeletes;

    protected $table = 'cattle';

    protected $fillable = [
        'code', 'name', 'breed_id', 'ranch_id', 'current_owner_id', 'father_id',
        'mother_id', 'sex', 'birth_date', 'color', 'weight_kg', 'height_cm',
        'ear_tag', 'chip_number', 'purity_percentage', 'status', 'sale_status',
        'main_photo_path', 'is_public', 'observations',
    ];

    protected array $decodedTextAttributes = [
        'name', 'color', 'observations',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'weight_kg' => 'decimal:2',
            'height_cm' => 'decimal:2',
            'purity_percentage' => 'decimal:2',
            'is_public' => 'boolean',
        ];
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    public function ranch(): BelongsTo
    {
        return $this->belongsTo(Ranch::class);
    }

    public function currentOwner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'current_owner_id');
    }

    public function father(): BelongsTo
    {
        return $this->belongsTo(self::class, 'father_id');
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(self::class, 'mother_id');
    }

    public function offspringAsFather(): HasMany
    {
        return $this->hasMany(self::class, 'father_id');
    }

    public function childrenAsFather(): HasMany
    {
        return $this->offspringAsFather();
    }

    public function offspringAsMother(): HasMany
    {
        return $this->hasMany(self::class, 'mother_id');
    }

    public function childrenAsMother(): HasMany
    {
        return $this->offspringAsMother();
    }

    public function genealogyLinks(): HasMany
    {
        return $this->hasMany(CattleGenealogyLink::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(CattlePhoto::class)->orderBy('sort_order')->orderBy('id');
    }

    public function mainPhoto()
    {
        return $this->hasOne(CattlePhoto::class)->where('is_main', true);
    }

    public function ownershipHistories(): HasMany
    {
        return $this->hasMany(OwnershipHistory::class)->orderByDesc('start_date');
    }

    public function currentOwnership()
    {
        return $this->hasOne(OwnershipHistory::class)->where('is_current', true);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(CattleSale::class);
    }

    public function veterinaryRecords(): HasMany
    {
        return $this->hasMany(VeterinaryRecord::class)->orderByDesc('record_date');
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(Vaccination::class)->orderByDesc('application_date');
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class)->orderByDesc('treatment_date');
    }

    public function weightRecords(): HasMany
    {
        return $this->hasMany(WeightRecord::class)->orderByDesc('record_date');
    }

    public function reproductionRecords(): HasMany
    {
        return $this->hasMany(ReproductionRecord::class)->orderByDesc('reproduction_date');
    }

    public function reproductionAsPartner(): HasMany
    {
        return $this->hasMany(ReproductionRecord::class, 'partner_cattle_id')->orderByDesc('reproduction_date');
    }

    public function reproductionAsOffspring(): HasMany
    {
        return $this->hasMany(ReproductionRecord::class, 'offspring_cattle_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class)->orderByDesc('issue_date');
    }
}
