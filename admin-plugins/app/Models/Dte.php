<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dte extends Model
{

    protected $fillable = ['type', 'folio', 'xml', 'certification', 'company_id', 'user_id', 'track_id', 'uuid', 'order_id' ];

    /**
     * undocumented function
     *
     * @return void
     */
    public function company()
    {
      return $this->belongsTo(Company::class);
    }

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public function dteOrder()
    {
        return $this->hasOne(DteOrder::class, 'dtes_id', 'id');
    }

    /**
     * undocumented function
     *
     * @return void
     */
    public function envio_dte()
    {
      return $this->belongsTo(EnvioDte::class, 'envio_dte_id');
    }

    /**
     * undocumented function
     *
     * @return void
     */
    public function references()
    {
        return $this->belongsToMany(Dte::class, 'references', 'dte_id', 'dte_reference_id')->withPivot('type');
    }



}
