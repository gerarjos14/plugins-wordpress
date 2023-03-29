<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvioDte extends Model
{
    protected $fillable = ['track_id', 'xml', 'estado', 'glosa', 'status_xml'];
}
