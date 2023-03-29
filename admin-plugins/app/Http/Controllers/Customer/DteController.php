<?php

namespace App\Http\Controllers\Customer;

use App\Dtes\Log;
use App\Dtes\Sii;
use App\Models\Dte;
use App\Jobs\DteSend;
use App\Dtes\Sii\EnvioDte;
use App\Dtes\Sii\Autenticacion;
use App\Http\Controllers\Controller;

use App\Models\Company;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Dtes\Sii\Dte\PDF\Dte as PDFDte;

class DteController extends Controller
{
    public function download($uuid) {


        $dte = Dte::where('uuid', $uuid)->first();

        if(empty($dte)){
          abort(404);
        }
        $company = Company::find($dte->company_id);

        $EnvioDte = new EnvioDte(); // Usar clase DTE
        if ($dte->envio_dte) {
          $EnvioDte->loadXML(base64_decode($dte->envio_dte->xml));
        } else {
          $EnvioDte->loadXML(base64_decode($dte->xml));
        }

        $Caratula = $EnvioDte->getCaratula();
        $Documentos = $EnvioDte->getDocumentos();

        $pdf = new PDFDte();
        if($dte->type==Company::TYPES[Company::BALLOT])
        {
            $pdf->setWebVerificacion(url('/voucher'));
        }
        // =false hoja carta, =true papel contínuo (false por defecto si no se pasa)
        $pdf->setLogo(storage_path("app/files/logos/".$company->logo));
        $pdf->setFooterText(config('app.name'));
        $pdf->setResolucion(['FchResol'=>$Caratula['FchResol'], 'NroResol'=>$Caratula['NroResol']]);
        $pdf->agregar($Documentos[0]->getDatos(), $Documentos[0]->getTED());


        $pdf->Output($dte->folio . '.pdf', 'I');

    }

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public function send(Dte $dte)
	{
        if ($dte->track_id) {
            response()->json(['message' => 'Dte has track_id'], 400);
        }

        DteSend::dispatch($dte);

        return ['message' => 'Dte send'];
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public function xml($uuid)
	{
    header("Content-Type: application/xml; charset=ISO-8859-1");
    $dte = Dte::where('uuid', $uuid)->where('user_id', Auth::id())->first();

    if(empty($dte)){
      abort(404);
    }

    return base64_decode($dte->envio_dte ? $dte->envio_dte->xml : $dte->xml);
	}
}
