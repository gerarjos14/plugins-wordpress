<?php

namespace App\Jobs;

use App\Models\Dte;
use App\Notifications\CheckStatusDteError;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mail\SendError;
use App\Mail\sendInformation;

use App\Dtes\Sii;
use App\Dtes\Log;
use App\Dtes\Sii\Autenticacion;
use App\Models\Company;
use Illuminate\Support\Facades\Crypt;

class DteCheckStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dte;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Dte $dte)
    {
      $this->dte = $dte;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      try {
        if ($this->dte->certification) {
          Sii::setAmbiente(Sii::CERTIFICACION);
          Sii::setServidor('maullin');
        }

        $signature = $this->dte->company->signature;
        $token = Autenticacion::getToken([
          'data' => base64_decode($signature->file),
          'pass' => Crypt::decryptString($signature->password),
        ]);

        if (!$token) {
          throw new \Exception('Error sii authentication');
        }

        $rut = explode('-', $this->dte->company->rut);
        $estado = Sii::request('QueryEstUp', 'getEstUp', [$rut[0], $rut[1], $this->dte->envio_dte->track_id, $token]);

        $code = 0;

        // si el estado se pudo recuperar se muestra estado y glosa
        if ($estado!==false) {
          $envio = $this->dte->envio_dte;

          $envio->estado  = (string)$estado->xpath('/SII:RESPUESTA/SII:RESP_HDR/ESTADO')[0];
          $envio->glosa  = (string)$estado->xpath('/SII:RESPUESTA/SII:RESP_HDR/GLOSA')[0];


          $dteOrder =   $this->dte->dteOrder;
          $order_id =   $this->dte->order_id;

          if ($envio->estado == 'EPR') {

            $result = (array)$estado->xpath('/SII:RESPUESTA/SII:RESP_BODY')[0];
            if ($result['ACEPTADOS']) {
              /**
               * Enviar email al cliente la factura
               */

              Mail::to($dteOrder->email)->send(new sendInformation($dteOrder->name, url('order/'.$order_id)));
              $code = 1;
            } elseif ($result['RECHAZADOS']) {
              /**
               * TODO
               * Enviar mail al comerciate notificando del rechazo
               * Order_id null en Dtes table
               */
              $message = 'El envio fue recahzado';
              Mail::to($this->dte->company->email)->send(new SendError($this->dte->company->name, url('order/'.$order_id), $message, 'ENVIO'));


              $code = -1;
            } elseif ($result['REPAROS']) {
              /**
               * Enviar email al cliente la facturas
               */
              Mail::to($dteOrder->email)->send(new sendInformation($dteOrder->name, url('order/'.$order_id)));
              $code = -2;
            }
          }else{

              /**
               * TODO
               * Notificar al comerciante
               * Order_id null en Dtes table
               */
              $this->dte->order_id = null;
              $this->dte->save();
              $message = 'Error al general el envio del DTE, revise la orden de woocomerce, y reintente el envio.';
              Mail::to($this->dte->company->email)->send(new SendError($this->dte->company->name, url('order/'.$order_id), $message, 'ENVIO'));
          }

          $envio->status_xml = base64_encode($estado->saveXML());
          $envio->save();
        }

        if ($code != 1) {
          throw new \Exception((string)$estado->saveXML());
        }


      } catch (\Exception $e) {
        $error = $e->getMessage();
        foreach (Log::readAll() as $err) {
          $error = $error . ', ' . $err;
        }

        $this->dte->user->notify(new CheckStatusDteError($this->dte, $error));
      }

    }
}
