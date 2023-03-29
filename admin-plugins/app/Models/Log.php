<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
  protected $table = 'logs';

  protected $fillable = [
      'user_id',
      'company_id',
      'visitor_id',
      'big_data',
      'web_url',
  ];

  #Relación con Modulo [Users]
  public function user() { //Un Log pertenece a un usuario
      return $this->belongsTo(User::class, 'user_id');
  }

  #Relación con Modulo [Companies]
  public function company() { //Un Log pertenece a una compañia
      return $this->belongsTo(Company::class, 'company_id');
  }

}
