<?php

namespace  App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Permissions\HasPermissionsTrait;
use Laravel\Passport\HasApiTokens;
use App\Containers\Auth\Notifications\ResetPasswordEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Containers\Files\Models\Image;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Containers\Common\Models\Contact;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasPermissionsTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'dob',
        'email',
        'password',
        'password_updated_at',
        'active',
        'profile_image',
        'can_restore',
        'online',
        'last_seen'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profileImage()
    {
        return $this->belongsTo(Image::class, 'profile_image');
    }

    public function contactTags()
    {
        return $this->belongsToMany(Contact::class, 'contacts_users');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordEmail($token, $this->email));
    }
}
