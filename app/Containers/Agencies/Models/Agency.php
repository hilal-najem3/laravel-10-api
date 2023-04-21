<?php

namespace App\Containers\Agencies\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Containers\Files\Models\Image;
use App\Containers\Currencies\Models\CurrencyConversion;
use App\Containers\Currencies\Models\CurrencyConversionHistory;
use App\Containers\Plans\Models\AgencyPlan;
class Agency extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'bio',
        'active',
        'is_branch',
        'type_id',
        'logo_id',
        'agency_id'
    ];

    public function type()
    {
        return $this->belongsTo(AgencyType::class, 'type_id');
    }

    public function parent_agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function logo()
    {
        return $this->belongsTo(Image::class, 'logo_id');
    }

    public function agencyAdmins()
    {
        return $this->belongsToMany(User::class, 'agency_admins');
    }

    public function currentConversions()
    {
        return $this->hasMany(CurrencyConversion::class, 'agency_id');
    }

    public function conversionsHistory()
    {
        return $this->hasMany(CurrencyConversionHistory::class, 'agency_id');
    }

    public function plans()
    {
        return $this->hasMany(AgencyPlan::class, 'agency_id');
    }
}
