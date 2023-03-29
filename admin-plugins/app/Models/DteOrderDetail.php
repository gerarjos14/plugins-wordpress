<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DteOrderDetail extends Model
{

    public $table = 'dte_order_detail';

    protected $fillable = [
        'dte_order',
        'exempt',
        'description',
        'discount',
        'discount_prec',
        'quantity',
        'unit_price'];


}

