<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCards extends Model
{
    protected $table = 'cards';

    protected $fillable = [
        'user_id',
        'number_card',
        'month',
        'year',
        'cvc',
        'card_last_four',
        'card_brand',
        'payment_method_id',
        'created_at',
        'updated_at',
    ];

    public function user(){
        return $this->hasOne(User::class);
    }
    
    public function is_active(){
        return $this->hasOne(UserCardsActive::class);
    }
}
