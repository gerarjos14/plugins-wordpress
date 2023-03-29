<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    const INVOICE     = 'invoice';
    const EXEMPT_INVOICE = 'exempt_invoice';
    const BALLOT      = 'ballot';
    const CREDIT_NOTE = 'credit_note';
    const DEBIT_NOTE  = 'debit_note';

    const PATH_LOGO  = 'companies/logo/';

    const TYPES = [
      self::INVOICE => 33,
      self::EXEMPT_INVOICE => 34,
      self::BALLOT => 39,
      self::CREDIT_NOTE => 61,
      self::DEBIT_NOTE => 56,
    ];

    protected $fillable = [
      'gr',
      'rut',
      'logo',
      'name',
      'phone',
      'ecomerce_url',
      'email',
      'user_id',
      'address',
      'state_id',
      'type_document',
      'resolution_nro',
      'resolution_date',
      'economy_activity',
    ];

    /**
     * undocumented function
     *
     * @return void
     */
    public function cafs()
    {
      return $this->hasMany(Caf::class);
    }

    public function signature() {
      return $this->hasOne(Signature::class);
    }

    public function state() {
      return $this->belongsTo(State::class);
    }

    #Relación con el modulo [Logs]
    public function log() { //Una Empresa puede terner muchos logs
        return $this->hasMany(Log::class);
    }

}
