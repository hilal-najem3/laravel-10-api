<?php

namespace App\Containers\Currencies\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKeys;

class CurrencyConversion extends Model
{
    use SoftDeletes, HasCompositePrimaryKeys;

    public $incrementing = false;

    /**
     * Indicates model primary keys.
     */
    protected $primaryKey = ['agency_id', 'from', 'to'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'agency_id',
        'from',
        'to',
        'ratio',
        'operation',
        'date_time',
    ];

    public function agency()
    {
        $this->belongsTo(Agency::class, 'agency_id');
    }

    public function from()
    {
        $this->belongsTo(Currency::class, 'from');
    }

    public function to()
    {
        $this->belongsTo(Currency::class, 'to');
    }
}
