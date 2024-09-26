<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Organisation extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'address',
        'city',
        'postal_code',
        'state',
        'country',
        'phone',
        'email',
        'website',
        'description',
        'active',
        'tenant_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'member_count' => 'integer',
    ];

    public static function getValidationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
        ];
    }

    /**
     * Get the members for the organisation.
     */
    public function members()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the organisation's groups.
     *
     * @return string
     */
    public function groups()
    {
        return $this->HasMany(Group::class);
    }

    /**
     * Get the vehicles for the organisation.
     */
    // public function vehicles()
    // {
    //     return $this->hasMany(Vehicle::class);
    // }

    /**
     * Get the organisation's full address.
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        return "{$this->address}, {$this->city}, {$this->postal_code}, {$this->state}, {$this->country}";
    }
}
