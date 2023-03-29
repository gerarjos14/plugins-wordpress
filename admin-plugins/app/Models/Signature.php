<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{

  protected $fillable = ['run', 'name', 'issuer', 'from', 'to', 'email', 'file', 'password', 'company_id'];
}

