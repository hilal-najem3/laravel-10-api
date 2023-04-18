<?php

namespace App\Containers\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Containers\Roles\Models\Role;
use App\Models\User;

class Permission extends Model
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
    
    public function roles()
    {
        return $this->belongsToMany(Role::class,'roles_permissions');
    }
     
    public function users()
    {
        return $this->belongsToMany(User::class,'users_roles');
    }
}
