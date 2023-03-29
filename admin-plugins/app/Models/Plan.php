<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $guarded = ["id"];
    
    /**
     * Add attributte 'period'
     */
    protected $appends = ['period'];

    /**
     * The constants of the interval attribute.
     */
    const MONTH    = 'month';
    const YEAR     = 'year';
    const LIFETIME = 'lifetime';

    /**
     * The constants of the platform attribute.
     */
    const SIIGO          = 'SIIGO';
    const ALEGRA         = 'ALEGRA';
    const FAC_CHILE      = 'FAC_CHILE'; // SII
    const FAC_PERU       = 'FAC_PERU'; // sunat
    const PAGUE_A_TIEMPO = 'PAGUE_A_TIEMPO'; // pague a tiempo
    const ANALITYCS      = 'ANALITYCS'; // clarity - analitycs
    const BEON           = 'BEON24';

    const PLATFORMS = [
        self::SIIGO,
        self::ALEGRA,
        self::FAC_CHILE,
        self::PAGUE_A_TIEMPO,
        self::ANALITYCS,
        self::FAC_PERU,
        self::BEON,
    ];

    public function getPeriodAttribute() 
    {  
        $period = '';
        if($this->interval === $this::MONTH){
            $period = 'mensual';
        }elseif($this->interval === $this::YEAR){
            $period = 'anual';
        }else{
            $period = 'pago único';
        }
        return $period;  
    }
  
}
