<?php

namespace App\Containers\Addresses\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Containers\Agencies\Models\Agency;
use App\Models\User;

class Address extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'country_id',
        'state_id',
        'city',
        'street',
        'building',
        'floor',
        'details',
        'location'
    ];

    public function agencies()
    {
        return $this->belongsToMany(Agency::class, 'agencies_addresses');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_addresses');
    }
}
