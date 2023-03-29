<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Carbon\Carbon;
use App\Models\TransferRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransferRequestController extends Controller
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
        return view('admin.transfer_request.index');
    }

    /**
     * Data for the list
     * 
     * @return Datatable
     */

    public function datatable()
    {
        $transfers = TransferRequest::with('user')->get();
        
        return datatables()->of($transfers)
            ->editColumn('user', function ($row) {
                return '<a href="#" data-route="'. route('admin.transfer-request.view-agency', $row->user->id) .'"  class="ajax-modal">'.$row->user->name.'</a>';
            })
            ->editColumn('status', function ($row){
                if($row->status == TransferRequest::WAITING){
                    $html = '<span class="badge badge-secondary">'. $row->status_for_human.'</span>';
                }elseif ($row->status == TransferRequest::PENDING) {
                    $html = '<span class="badge badge-primary">'. $row->status_for_human.'</span>';
                }else{
                    $html = '<span class="badge badge-success">'. $row->status_for_human.'</span>';
                }
                return $html;
            })
            ->editColumn('created_at', function ($row){
                return date('d-m-Y', strtotime($row->created_at));
            })
            ->editColumn('pending_at', function ($row){
                if($row->pending_at){
                    return date('d-m-Y', strtotime($row->pending_at));
                }
                return '';
            })
            ->editColumn('confirmed_at', function ($row){
                if($row->confirmed_at){
                    return date('d-m-Y', strtotime($row->confirmed_at));
                }
                return '';
            })
            ->addColumn('action', function ($row) {
                if($row->status == TransferRequest::WAITING){
                    $html = '<a href="#" data-route="'. route('admin.transfer-request.pending', $row->id) .'" class="transfer-status btn btn-sm btn-outline-primary">Acusar recibo</a>';
                }elseif ($row->status == TransferRequest::PENDING) {
                    $html = '<a href="#" data-route="'. route('admin.transfer-request.confirmed', $row->id) .'" class="transfer-status btn btn-sm btn-outline-success">Confirmar</a>';
                }else{
                    $html = '-';
                }
                return $html;
            })
            ->rawColumns(['user', 'status', 'action'])
            ->toJson();
    }

    public function viewAgency(User $agency){
        $bankAccount = $agency->bank_account;
        return view('admin.transfer_request.modal', compact('bankAccount','agency'))->render();
    }

    public function pending(TransferRequest $transfer_request)
    {
        if(!$transfer_request->pending_at){
            $transfer_request->pending_at = Carbon::now();
            $transfer_request->status = TransferRequest::PENDING;
            $transfer_request->save();
        }   
        return true;
    }

    public function confirmed(TransferRequest $transfer_request)
    {
        if(!$transfer_request->confirmed_at){
            $user = $transfer_request->user;
            if($user->balance >= $transfer_request->amount){
                $transfer_request->confirmed_at = Carbon::now();
                $transfer_request->status = TransferRequest::CONFIRMED;
                $transfer_request->save();
            }else{
                session()->flash('message', ['danger','No tiene saldo disponible para confirmar la transacción']);
            }
        }   
        return true;
    }

}
