<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class EndpointBeon24 extends Model
{
    // Definición de endpoints de Beon24
    const CHECK_TOKEN_STORE          = 'checkVendor/';
    const UPDATE_VENDOR_SUBSCRIPTION = 'updateVendorSubscription';
    const GET_DATA_VENDOR_BY_EMAIL   = 'shop_data_email/';
}
