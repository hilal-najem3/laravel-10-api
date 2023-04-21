<?php

namespace App\Containers\Plans\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Containers\Agencies\Models\Agency;

class AgencyPlan extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'plan_id',
        'agency_id',
        'starting_date',
        'ending_date',
        'active',
        'note'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
}
