<?php

namespace App\Containers\Roles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Containers\Permissions\Models\Permission;
use App\Models\User;

class Role extends Model
{
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'roles_permissions');
    }
     
    public function users()
    {
        return $this->belongsToMany(User::class,'users_roles');
    }
}
