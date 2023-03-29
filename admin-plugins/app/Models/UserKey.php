<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserKey extends Model
{
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'alegra_user', 
        'alegra_token', 
        'wc_consumer_key', 
        'wc_consumer_secret', 
        'website'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
