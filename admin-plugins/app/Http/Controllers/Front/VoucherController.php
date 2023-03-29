<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Dte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\Rut;
use App\Dtes\Sii\Dte as SiiDte;

class VoucherController extends Controller
{
    public function index()
    {

        return view('front.voucher.index');
    }

    public function searchDte(Request $request)
    {


        try {
            $validator = Validator::make($request->all(), [
                'rut'               => ['required', new Rut],
                'type'              => ['required', 'in:33,39,61,34,56'],
                'folio'             => ['required', 'int'],
                'date'              => ['required', 'date_format:Y-m-d'],
                'amount'            => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            ]);


            if ($validator->fails()) {
                return redirect()->back()->withInput()->with('error',$validator->getMessageBag()->first());
            }

            $company = Company::where('rut', $request['rut'])->first();

            $dte = Dte::where('company_id', $company->id)
                ->where('type', $request['type'])
                ->where('folio', $request['folio'])->first();

            //return $dte->xml;
            if (!empty($dte)) {
                $dte_ = new SiiDte(base64_decode($dte->xml));
                $data = $dte_->getDatos();

                $amount = $data['Encabezado']['Totales']['MntTotal'];
                $date = $data['Encabezado']['IdDoc']['FchEmis'];

                if ($amount != $request['amount']) {
                    throw new \Exception('No existe el DTE');
                }

                if ($date != $request['date']) {
                    throw new \Exception('No existe el DTE');
                }

                return redirect(route('dte.download', $dte->uuid));
            } else {
                throw new \Exception('No existe el DTE');
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return redirect()->back()->withInput()->with('warning',$error);
        }
    }

    public function withValidator($validator)
    {
        if ($validator->fails()) {
            session()->flash('message', ['danger', $validator->messages()->first()]);
        }

    }
}
