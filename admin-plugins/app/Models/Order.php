<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ["id"];

    const SUCCESS = "SUCCESS";
    const PENDING = "PENDING";

    public function order_lines() {
        return $this->hasMany(OrderLine::class);
    }
}
