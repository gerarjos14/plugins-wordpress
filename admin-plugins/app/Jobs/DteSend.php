<?php

namespace App\Jobs;

use App\Dtes\FirmaElectronica;
use Illuminate\Bus\Queueable;
/* use Illuminate\Contracts\Queue\ShouldBeUnique; */
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Dte;

use Illuminate\Support\Facades\Crypt;

use App\Dtes\Log;
use App\Dtes\Sii;
use App\Dtes\Sii\Dte as SiiDte;
use App\Dtes\Sii\EnvioDte;
use App\Models\EnvioDte as ModelsEnvioDte;
use App\Notifications\ErrorDteSend;

class DteSend implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $dte;
  public $user;
  public $uniqueFor = 3600;

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
  * The unique ID of the job.
  *
  * @return string
  */
  public function uniqueId()
  {
      return $this->dte->id;
  }


  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    try {
      if ($this->dte->envio_dte) {
        return;
      }

      $dte_sii = new SiiDte(base64_decode($this->dte->xml));

      $signature_object =  new FirmaElectronica([
        'data' => base64_decode($this->dte->company->signature->file),
        'pass' => Crypt::decryptString($this->dte->company->signature->password)
      ]);

      if (!$this->dte->envio_dte) {
        $envio = new EnvioDte();
        $envio->agregar($dte_sii);

        $envio->setCaratula(
          [
            'RutEnvia' => $this->dte->company->signature->run,
            'RutReceptor' => '60803000-K', // NOTE: No tocar
            'FchResol' => $this->dte->company->resolution_date,
            'NroResol' => $this->dte->company->resolution_nro,
          ]
        );

        $envio->setFirma($signature_object);
        $envio_xml = $envio->generar();

        if (!$envio->schemaValidate()) {
          throw new \Exception('Schema validation failed in EnvioDTE');
        }

        if ($this->dte->certification) {
          Sii::setAmbiente(Sii::CERTIFICACION);
          Sii::setServidor('maullin');
        }

        $track_id = $envio->enviar();

        $envio_dte = ModelsEnvioDte::create([
          'xml' => base64_encode($envio_xml),
          'track_id' => $track_id,
        ]);

        $this->dte->envio_dte_id = $envio_dte->id;
        $this->dte->save();

      }

      /* if (isset($data['Encabezado']['Receptor']['Contacto']) &&  $data['Encabezado']['Receptor']['Contacto']) { */
      /*   $email = $data['Encabezado']['Receptor']['Contacto']; */
      /*   /1* logger()->info('Sending email dte ', ['email' => $email]); *1/ */
      /*   Mail::to($email)->send(new DteNew($this->dte)); */
      /* } else { */
      /*   /1* logger()->warning('Not contact mail', $data['Encabezado']); *1/ */
      /* } */

      DteCheckStatus::dispatch($this->dte)->delay(now()->addMinutes(5));

    } catch (\Exception $e) {
      $error = $e->getMessage();
      foreach (Log::readAll() as $err) {
        $error = $error . '<br>' . $err;
      }

      if ($this->dte->retry < 3) {
        $this->dte->retry += 1;
        $this->dte->save();

        DteSend::dispatch($this->dte)->delay(now()->addMinutes(10));
      }

      $this->dte->user->notify(new ErrorDteSend($this->dte, $error));
    }
  }
}
