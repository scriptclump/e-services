<?php

return [

    /*
    |--------------------------------------------------------------------------
    | channelid & GDS URL 
    |--------------------------------------------------------------------------
    |
    | channelid is globally decalred to use in place order dmapis 
    | for Ebutor app. GDS url details in place Order cancel order 
    */

    'channelid' => '1',
   
    //'GDSAPIURL'=>'http://'.$_SERVER['HTTP_HOST'],
    //'GDSAPIURL'=>'http://fbedev.ebutor.com',
    'GDSAPIKey'=> env('GDSAPIKey'),
    'GDSAPISECRETKey'=>env('GDSAPISECRETKey'),

    //cancel order & return Order
    'CR_GDSAPIKey'=> env('CR_GDSAPIKey'),
    'CR_GDSAPISECRETKey' => env('CR_GDSAPISECRETKey'),

    //generate invoice
    'CR_GenerateInvoiceAPIKey'=>  env('CR_GenerateInvoiceAPIKey'),
    'CR_GenerateInvoiceAPISECRETKey' => env('CR_GenerateInvoiceAPISECRETKey'),
	
	//PICK API KEYS
   'PICKAPIKey'=> env('PICKAPIKey'),
   'PICKAPISECRETKey'=> env('PICKAPISECRETKey'),
   



    /*
    |--------------------------------------------------------------------------
    | SMS details 
    |--------------------------------------------------------------------------
    |SMS URL and user id & sender id use to globally decalred and used in Orders &
    | registration apis
     
    */

    'SMS_URL'=>'http://api.mVaayoo.com/mvaayooapi/MessageCompose',
    'SMS_USER'=> 'vinil@esealinc.com:eseal@123',
    'SMS_SENDER_ID'=> 'EBUTOR',

    'DB_URL'=>'http://api.mVaayoo.com/mvaayooapi/MessageCompose',
    'DB_USER'=> 'vinil@esealinc.com:eseal@123',
    'DB_SENDER_ID'=> 'EBUTOR',

    'TAX_Node_URL' => 'http://10.175.8.65:3100/centralApiManager/tax/gettaxdetails',
    //'TAX_Node_URL' => 'http://app1.node.prd.in2.ebutor.int:3100/centralApiManager/tax/gettaxdetails',
         
];

?>
