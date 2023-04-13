<?php

namespace App\Containers\Agencies\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Containers\Files\Models\Image;

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

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function logo()
    {
        return $this->belongsTo(Image::class, 'logo_id');
    }
}
