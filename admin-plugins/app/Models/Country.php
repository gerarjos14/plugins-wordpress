<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;
    
    protected $fillable = [ 'code', 'name' ];

    public function currency()
    {
        return $this->hasOne(Currency::class);
    }
}
