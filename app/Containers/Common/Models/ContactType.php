<?php

namespace App\Containers\Common\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ContactType extends Model
{
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'allow_duplicates',
        'regex',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'type_id');
    }
}
