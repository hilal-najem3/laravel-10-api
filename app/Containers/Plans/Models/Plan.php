<?php

namespace App\Containers\Plans\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'abbreviation',
        'description',
        'price',
        'currency_id'
    ];

    public function agency_plan()
    {
        return $this->hasMany(AgencyPlan::class, 'plan_id');
    }
}
