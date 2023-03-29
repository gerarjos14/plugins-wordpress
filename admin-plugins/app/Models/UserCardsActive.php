<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCardsActive extends Model
{
    protected $table = 'pivote_user_cards';

    protected $fillable = [
        'user_id',
        'card_id',
    ];

    public function cards(){
        return $this->hasone(UserCards::class);
    }

    public function user(){
        return $this->hasOne(User::class);
    }
  
}
