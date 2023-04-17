<?php

namespace App\Containers\Currencies\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'symbol',
        'symbol_native',
        'decimal_digits',
        'code',
        'name_plural',
        'rounding'
    ];
}
