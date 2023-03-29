<?php

namespace App\Http\Controllers\Agency;

use App\Models\Plan;
use App\Models\User;
use App\Models\Currency;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Agency\StorePlanRequest;

class PlanController extends Controller
{

    public function __construct()
    {
        $this->middleware('is_agency');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('agency.plans.index');
    }

    /**
     * Data for the list
     * 
     * @return Datatable
     */

    public function datatable()
    {
        $plans = Plan::select('id', 'name', 'interval', 'currency', 'amount', 'platform', 'active')
            ->where('user_id', auth()->user()->id)
            ->get();
        return datatables()->of($plans)
            ->addColumn('action', function ($row) {
                $html = '<a href="' . route('agency.plans.edit', $row->id) . '" class="btn btn-sm btn-outline-secondary mr-1"><i class="fas fa-pencil-alt mr-1"></i>Editar</a>';
                if ($row->active) {
                    $html .= '<a href="#" data-route="' . route('agency.plans.deactivate', $row->id) . '" class="btn btn-sm btn-outline-danger active-record"><i class="fa fa-times mr-1"></i>Desactivar</a>';
                } else {
                    $html .= '<a href="#" data-route="' . route('agency.plans.activate', $row->id) . '" class="btn btn-sm btn-outline-success active-record"><i class="fa fa-check mr-1"></i>Activar</a>';
                }
                return $html;
            })
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $admin = User::where('role', User::ADMIN)->first();
        $plans = Plan::where('user_id', $admin->id)
            ->where('interval', '<>', Plan::LIFETIME)
            ->where('country_id',  $user->country_id)
            ->orderBy('platform', 'asc')
            ->get();
        $isChile = ($user->country_id == 1);
        $platforms = $isChile ? [Plan::FAC_CHILE] : [Plan::SIIGO, Plan::ALEGRA, Plan::BEON];

        return view('agency.plans.create', compact('plans', 'isChile', 'platforms'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePlanRequest $request)
    {
        $user = Auth::user();
        $admin = User::where('role', User::ADMIN)->first();
        $base_plan = Plan::where('user_id', $admin->id)
            ->where('platform', $request->platform)
            ->where('interval', $request->interval)
            ->first();
        if ($base_plan) {
            if ($request->plan_price <= $base_plan->amount) {
                return back()
                    ->withErrors(['plan_price' => 'The price must be greater than ' . $base_plan->amount])
                    ->withInput($request->all());
            }
            $currency = Currency::where('country_id', $user->country_id)->first();
            try {
                var_dump(env(['STRIPE_SECRET']));

                DB::beginTransaction();
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));



                $plan = \Stripe\Plan::create([
                    'currency' => $currency->code,
                    'interval' => request('interval'),
                    "product" => [
                        "name" => request('plan_name')
                    ],
                    'nickname' => request('plan_name'),
                    'amount' => round(request('plan_price') * $this->resolveFactor($currency->code)),
                ]);

                if ($plan) {
                    \App\Models\Plan::create([
                        'product_id'    => $plan->product,
                        'name'          => request('plan_name'),
                        'amount'        => request('plan_price'),
                        'interval'      => request('interval'),
                        'plan_id'       => $plan->id,
                        'description'   => request('description'),
                        'platform'      => request('platform'),
                        'user_id'       => auth()->user()->id,
                        'country_id'    => auth()->user()->country_id,
                        'qty_documents' => request("qty_documents", 0),
                        'currency'      => $currency->code,
                    ]);
                }

                DB::commit();
                session()->flash('message', ['success', __('Plan dado de alta con exito')]);
                return redirect(route('agency.plans.index'));
            } catch (\Exception $exception) {
                DB::rollBack();
                if (isset($plan)) {
                    $plan->delete();
                }
                session()->flash('message', ['danger', $exception->getMessage()]);
                return back()->withInput();
            }
        }
        return redirect(route('agency.plans.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Plan $plan)
    {
        $this->authorize('update', $plan);
        return view('agency.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plan $plan)
    {
        $this->authorize('update', $plan);
        $request->validate(['description' => 'required|string|max:255']);
        $plan->description = $request->description;
        $plan->save();
        session()->flash('message', ['success', 'Plan actualizado con exito']);
        return redirect()->route('agency.plans.index');
    }

    /**
     * Update the active field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activate(Plan $plan)
    {
        $plan->active = 1;
        $plan->save();
        return true;
    }

    /**
     * Update the active field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deactivate(Plan $plan)
    {
        $plan->active = 0;
        $plan->save();
        return true;
    }

    private function resolveFactor($currency)
    {
        $zeroDecimalcurrencies = ['JPY', 'CLP'];
        if (in_array(strtoupper($currency), $zeroDecimalcurrencies)) {
            return 1;
        }
        return 100;
    }
}
