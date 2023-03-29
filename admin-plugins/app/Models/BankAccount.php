<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $guarded = ["id"];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'last_name', 'account_number', 'account_type', 'bank_name', 'identity_card',
    ];

    /**
     * Add attributte 'period'
     */
    protected $appends = ['type'];

    /**
     * The constants of the interval attribute.
     */
    const SAVINGS = 'savings';
    const CHECKING = 'checking';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeAttribute() 
    {  
        $type = '';
        if($this->account_type === $this::CHECKING){
            $type = 'Cuenta corriente';
        }else{
            $type = 'Cuenta de ahorro';
        }
        return $type;  
    }
}
