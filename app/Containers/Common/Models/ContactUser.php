<?php

namespace App\Containers\Common\Models;

use Illuminate\Database\Eloquent\Model;

class ContactUser extends Model
{
    protected $table = 'contacts_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'contact_id'
    ];
}
