<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caf extends Model
{

  protected $fillable = ['xml', 'type', 'from', 'to', 'available', 'next', 'certification', 'company_id', 'authorized_at'];


  const TYPE = [
        39=>'Boleta electronica',
        33=>'Factura',
        56=>'Nota de Debito',
        61=>'Nota de Credito',
        34=>'Factura Exenta',
    ];
}
