<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DteOrderDetail;

class DteOrder extends Model
{


    public $table = 'dte_order';

    protected $fillable = [
        'type',
        'user_id',
        'dtes_id',
        'order_id',
        'rut',
        'classification',
        'name',
        'email',
        'phone',
        'address',
        'state',
        'city',
        'discount',
        'discount_prec'];

        public function details()
        {
            return $this->HasMany(DteOrderDetail::class,'dte_order','id');
        }
        public function dte()
        {
            return $this->belongsTo(DTE::class,'dtes_id','id');
        }



}
