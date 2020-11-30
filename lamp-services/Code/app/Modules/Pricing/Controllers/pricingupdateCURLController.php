<?php
/*
FileName : pricingDashboadController
Author   : eButor
Description :
CreatedDate :14/Aug/2016
*/
//defining namespace
namespace App\Modules\Pricing\Controllers;

//loading namespaces
use App\Http\Controllers\BaseController;

use App\Modules\Pricing\Models\pricingDashboardModel;
use App\Modules\Notifications\Models\NotificationsModel;
use Mail;
use Utility;

class pricingupdateCURLController extends BaseController{

	//calling model 
    public function __construct() {
    	$this->middleware(function ($request, $next) {
            if (!Session::has('userId')) {
                     Redirect::to('/login')->send();
            }
            return $next($request);
        });
    	$this->objPricing = new pricingDashboardModel();

    }

    public function pricingUpdateWithUpdateDate($updateDate){

    	$environment    = env('APP_ENV');

		$updatedata =  $this->objPricing->priceUpdateWithUpdatedDate($updateDate);

		$mailContent = "<table border='1' cellspacing='0' cellpadding='5'>";
		$mailContent .= "<tr bgcolor='#efefef'>";
		$mailContent .= "<th>PRODUCT TITLE</th>";
		$mailContent .= "<th>SKU</th>";
		$mailContent .= "<th>PRICE</th>";
		$mailContent .= "<th>PTR</th>";
		$mailContent .= "<th>EFFECTIVE DATE</th>";
		$mailContent .= "</tr>";


		foreach ($updatedata as $data) {

			$mailContent .= "<tr>";
			$mailContent .= "<td>".$data['prod_title']."</td>";
			$mailContent .= "<td>".$data['prod_sku']."</td>";
			$mailContent .= "<td>".$data['price']."</td>";
			$mailContent .= "<td>".$data['ptr']."</td>";
			$mailContent .= "<td>".$data['effective_date']."</td>";
			$mailContent .= "</tr>";
		}

		$notificationObj= new NotificationsModel();
        $usersObj = new Users();
        $userIdData= $notificationObj->getUsersByCode('PRIC0001');
        $userIdData=json_decode(json_encode($userIdData));
        $data= $usersObj->wherein('user_id',$userIdData)->select('email_id')->get();
        $emails=json_decode(json_encode($data,1),true);
        $getEmails=array();

		$message 	= "Price Updated";
		$topMsg     = "This is to notify you that -- below product prices had updated last night.";
        $body = array('template'=>'emails.pricingupdateMail', 'attachment'=>'', 'topMsg'=>$topMsg, 'emailContent'=>$mailContent);
        $subject = 'Price Updated Via Cron job  : ' . date('d-m-Y');
        Utility::sendEmail($toEmails, $subject, $body);

		//Mail::send('emails.pricingupdateMail', ['topMsg'=>$topMsg,'emailContent' => $mailContent], function ($message) use ($getEmails,$environment ) {

			// if( $environment=='local'){
   //          	$message->from("tracker@ebutor.com", $name = "Price Update Cron - " . $environment);
   //          	$message->to("venkatesh.burla@ebutor.com");
   //          	$message->subject('Price Updated Via Cron job  : ' . date('d-m-Y') );
   //  	    }

		// 	$message->to($getEmails)->subject('Price Updated Via Cron job  : ' . date('d-m-Y'));
		// });
    }
	
}