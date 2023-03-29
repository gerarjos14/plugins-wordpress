<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferRequest extends Model
{
    // Esperando, pendiente, confirmada
    public $timestamps = false;

    /**
     * Add attributte 'period'
     */
    protected $appends = ['status_for_human'];

    /**
     * The constants of the interval attribute.
     */
    const WAITING = 'waiting';
    const PENDING = 'pending';
    const CONFIRMED = 'confirmed';

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getStatusForHumanAttribute() 
    {  
        $status_for_human = '';
        if($this->status === $this::WAITING){
            $status_for_human = 'Esperando';
        }elseif($this->status === $this::PENDING){
            $status_for_human = 'Pendiente';
        }else{
            $status_for_human = 'Confirmada';
        }
        return $status_for_human;  
    }
}
