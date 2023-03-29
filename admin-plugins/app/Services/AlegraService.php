<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\ConsumeExternalServices;

class AlegraService
{
    use ConsumeExternalServices;

    protected $user;
    
    protected $token;
    
    protected $baseUri;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->baseUri = config('services.alegra.base_uri');
    }

    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $headers['Authorization'] = $this->resolveAccessToken();
    }

    public function decodeResponse($response)
    {
        return json_decode($response);
    }

    public function resolveAccessToken()
    {
        $credentials = base64_encode("{$this->user}:{$this->token}");
        return "Basic {$credentials}";
    }

    /**
     *  FACTURAS
     */

    public function consultarFactura($id){
        return $this->getInvoice($id);
    }

    public function getInvoice($id){
        return $this->makeRequest(
            'GET',
            "/api/v1/invoices/{$id}",
            [],
        );
    }

    public function crearFactura($customer, $line_items)
    {
        $formParams = [
            'date'      => Carbon::now()->format('Y-m-d'),
            'dueDate'   => Carbon::now()->addDay(30)->format('Y-m-d'),
            'client'    => $customer,
            'items'     => $line_items,
            'status'    => 'open',
        ];
        return $this->createInvoice($formParams);               
    }

    public function createInvoice($formParams)
    {
        return $this->makeRequest(
            'POST',
            '/api/v1/invoices',
            [],
            $formParams,
            [],
            $isJsonRequest = true,
        );
    }

    public function listInvoices(){
        return $this->getInvoices();
    }

    public function getInvoices()
    {
        return $this->makeRequest(
            'GET',
            '/api/v1/invoices',
            [],
        );
    }

    /**
     * PRODUCTOS
     */
    public function crearProducto($data){        
        $formParams = [
            'name'  => $data->name,
            'price' => $data->price,                          
        ];
        return $this->storeProduct($formParams);
    }


    public function storeProduct($formParams){        
        return $this->makeRequest(
            'POST',
            '/api/v1/items',
            [],
            $formParams,
            [],
            $isJsonRequest = true,
        );
    }

    /**
     * CONTACTOS o CUSTOMER
     */

    public function consultarContacto($id){
        return $this->getContact($id);
    }
    
    public function editarContacto($id, $data){        
        $formParams = [
            'name'  => $data->first_name .' '.$data->last_name,
            'phonePrimary' => $data->phone,
            'email' => $data->email,
            'address' => [
                'description'   => $data->address_1 ? $data->address_1 : '',
                'city'          => $data->city ? $data->city : '',
                'zipCode'       => $data->postcode ? $data->postcode : 0,
            ],
        ];

        return $this->updateContact($id,$formParams);
    }  

    public function crearContacto($data){
        $formParams = [
            'name'  => $data->first_name .' '.$data->last_name,
            'phonePrimary' => $data->phone,
            'email' => $data->email,
            'address' => [
                'description'   => $data->address_1 ? $data->address_1 : '',
                'city'          => $data->city ? $data->city : '',
                'zipCode'       => $data->postcode ? $data->postcode : 0,
            ],
            'type' => ['client'],
        ];

        return $this->storeContact($formParams);
    }
 
    public function getContact($id){
        return $this->makeRequest(
            'GET',
            "/api/v1/contacts/{$id}"
        );
    }

    public function updateContact($id, $formParams){
        return $this->makeRequest(
            'PUT',
            "/api/v1/contacts/{$id}",
            [],
            $formParams,
            [],
            $isJsonRequest = true,
        );
    }

    public function storeContact($formParams){        
        return $this->makeRequest(
            'POST',
            '/api/v1/contacts',
            [],
            $formParams,
            [],
            $isJsonRequest = true,
        );
    }

    /**
     * Pagos 
     */

    public function crearPago($invoice, $id){
        $formParams = [
            'date'      => Carbon::now()->format('Y-m-d'),
            'invoices'  => [
                [
                    'id'        => $invoice->id,
                    'amount'    => $invoice->total,
                ],
            ],
            'bankAccount' => $id,
        ];
        return $this->createPayment($formParams);
    }

    public function createPayment($formParams){
        return $this->makeRequest(
            'POST',
            '/api/v1/payments',
            [],
            $formParams,
            [],
            $isJsonRequest = true,
        );
    }

    /**
     * Cuentas de banco
     */

    public function listaCuentasBancancarias(){
        return $this->getBankAccounts();
    }

    public function getBankAccounts()
    {
        return $this->makeRequest(
            'GET',
            '/api/v1/bank-accounts',
            [],
        );
    }

}
