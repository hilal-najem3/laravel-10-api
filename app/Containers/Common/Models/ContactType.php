<?php

namespace App\Containers\Common\Models;

use Illuminate\Database\Eloquent\Model;

class ContactType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'regex',
    ];

    public function contact()
    {
        return $this->hasMany(Contact::class, 'type_id');
    }
}
