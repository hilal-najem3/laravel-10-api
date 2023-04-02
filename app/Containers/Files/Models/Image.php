<?php

namespace  App\Containers\Files\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'link',
        'size',
        'type_id'
    ];

    public function type()
    {
        return $this->belongsTo(ImageType::class, 'type_id');
    }
}
