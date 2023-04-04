<?php

namespace App\Containers\Common\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Contact extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'value',
        'type_id'
    ];

    public function type()
    {
        return $this->belongsTo(ContactType::class, 'type_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'contacts_users');
    }
}
