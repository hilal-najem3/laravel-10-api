<?php

namespace App\Containers\Common\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'alpha2Code',
        'alpha3Code',
        'numberCode',
        'type_id',
        'region_id',
    ];

    public function type()
    {
        return $this->belongsTo(RegionType::class, 'type_id');
    }

    public function states()
    {
        return $this->hasMany(Region::class, 'region_id');
    }
}
