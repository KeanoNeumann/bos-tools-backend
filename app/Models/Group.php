<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'organisation_id',
    ];

    public static function getValidationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'organisation_id' => 'required|exists:organisations,id',
        ];
    }


    /**
     * Get the organisation's group.
     *
     * @return string
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
}
