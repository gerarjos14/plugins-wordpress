<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePlanRequest;

class PlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('is_admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.plans.index');
    }

    /**
     * Data for the list
     * 
     * @return Datatable
     */

    public function datatable()
    {
        $plans = Plan::select('id', 'interval', 'currency', 'amount', 'platform')
                    ->where('user_id', auth()->user()->id)
                    ->get();
        return datatables()->of($plans)
            ->addColumn('action', function ($row) {
                $html = '<a href="'. route('admin.plans.edit', $row->id) .'" class="btn btn-sm btn-outline-secondary mr-1"><i class="fas fa-pencil-alt mr-1"></i>Editar</a>';
                return $html;
            })
            ->toJson();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePlanRequest $request, Plan $plan)
    {        
        $plan->amount = $request->plan_price;
        $plan->name = isset($request->plan_name) ? $request->plan_name : $plan->name;
        $plan->description = isset($request->description) ? $request->description : $plan->description;
        $plan->qty_documents = isset($request->qty_documents) ? $request->qty_documents : 0;
        $plan->save();
        session()->flash('message', ['success', 'Plan actualizado con exito']);
        return redirect()->route('admin.plans.index');
    }

}
