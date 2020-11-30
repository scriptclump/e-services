<?php
//NCT dashboard 
namespace App\Modules\NCTTracker\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\NCTTracker\Models\nctTrackerHistoryModel;
use Log;
use Illuminate\Http\Request;
use Response;
use Mail;
class nctNotifyController extends BaseController {

	private $objNctTracker = "";
	private $objNctTrackerHistory = "";
	private $objCommonGrid = "";
	private $objLedger = "";

	public function __construct(){
		$this->objNctTrackerHistory = new nctTrackerHistoryModel();
	}

	public function checkAuthentication($auth_token){
    	if( $auth_token=='E446F5E53AD8835EAA4FA63511E22' ){
    		return true;
    	}else{
    		return false;
    	}
    }

	//funtion for nct cron for notifying 
	public function nctnotify(Request $request){

		$auth_token = $request->header('auth');

		if( !$this->checkAuthentication($auth_token) ){
			$finalResponse = array(
				'message'	=> 'Invalid authentication! Call aborted',
				'status'	=> 'failed',
				'code'		=> '400'
			);
			return $finalResponse;
		}

		$chequedata = $this->objNctTrackerHistory->getChequeDataOnCurrentDate();
		$chequebouncedata = $this->objNctTrackerHistory->getChequeBounceDate();
		$chequedepositeddata = $this->objNctTrackerHistory->checkDepositedDate();

		$mailContent = "";
		$mailContent .= "<h3>Below Cheques are Collected and not Deposited in Bank!</h3>";
		$mailContent .= "<table border='1' cellspacing='0' cellpadding='5'>";
		$mailContent .= "<tr bgcolor='#efefef'>";
		$mailContent .= "<th>Collection Code</th>";
		$mailContent .= "<th>Order Code</th>";
		$mailContent .= "<th>Reference Code</th>";
		$mailContent .= "<th>Amount</th>";
		$mailContent .= "<th>Customer Name</th>";
		$mailContent .= "<th>Days Exceeded</th>";
		$mailContent .= "</tr>";


		foreach ($chequedata as $data) {

			$mailContent .= "<tr>";
			$mailContent .= "<td>".$data->collection_code."</td>";
			$mailContent .= "<td>".$data->order_code."</td>";
			$mailContent .= "<td>".$data->nct_ref_no."</td>";
			$mailContent .= "<td align='right'>".$data->nct_amount."</td>";
			$mailContent .= "<td>".$data->customer_name."</td>";
			$mailContent .= "<td>".$data->daysdiff."</td>";

			$mailContent .= "</tr>";
		}
		$mailContent .= "</table>";
		
		$mailContent .= "</br></br>";
		$mailContent .= "<h3>Below Cheques are Bounced and are not Updated Since Last 5 days!</h3>";
		$mailContent .= "<table border='1' cellspacing='0' cellpadding='5'>";
		$mailContent .= "<tr bgcolor='#efefef'>";
		$mailContent .= "<th>Collection Code</th>";
		$mailContent .= "<th>Order Code</th>";
		$mailContent .= "<th>Reference Code</th>";
		$mailContent .= "<th>Amount</th>";
		$mailContent .= "<th>Customer Name</th>";
		$mailContent .= "<th>Days Exceeded</th>";
		$mailContent .= "</tr>";

		foreach ($chequebouncedata as $data) {

			$mailContent .= "<tr>";
			$mailContent .= "<td>".$data->collection_code."</td>";
			$mailContent .= "<td>".$data->order_code."</td>";
			$mailContent .= "<td>".$data->nct_ref_no."</td>";
			$mailContent .= "<td align='right'>".$data->nct_amount."</td>";
			$mailContent .= "<td>".$data->customer_name."</td>";
			$mailContent .= "<td>".$data->daysdiff."</td>";

			$mailContent .= "</tr>";
		}
		$mailContent .="</table>";

		$mailContent .= "</br></br>";
		$mailContent .= "<h3> Below Cheques are Deposited in Bank and not Updated Since Last 5 days!</h3>";
		$mailContent .= "<table border='1' cellspacing='0' cellpadding='5'>";
		$mailContent .= "<tr bgcolor='#efefef'>";
		$mailContent .= "<th>Collection Code</th>";
		$mailContent .= "<th>Order Code</th>";
		$mailContent .= "<th>Reference Code</th>";
		$mailContent .= "<th>Amount</th>";
		$mailContent .= "<th>Customer Name</th>";
		$mailContent .= "<th>Days Exceeded</th>";
		$mailContent .= "</tr>";
 		foreach ($chequedepositeddata as $data) {

			$mailContent .= "<tr>";
			$mailContent .= "<td>".$data->collection_code."</td>";
			$mailContent .= "<td>".$data->order_code."</td>";
			$mailContent .= "<td>".$data->nct_ref_no."</td>";
			$mailContent .= "<td align='right'>".$data->nct_amount."</td>";
			$mailContent .= "<td>".$data->customer_name."</td>";
			$mailContent .= "<td>".$data->daysdiff."</td>";

			$mailContent .= "</tr>";
		}

		$mailContent .="</table></br></br></br>";
		$toEmails = array();
    	$environment    = env('APP_ENV');
		$message  = "Nct Update";
		$topMsg     = "This is to notify you that -- below cheques are not updated";

		Mail::send('emails.nctMail', ['topMsg'=>$topMsg,'emailContent' => $mailContent], function ($message) use ($toEmails,$environment ) {
			if( $environment=='local' || $environment=='dev' || $environment=='qc' || $environment=='supplier' ){
				$message->from("tracker@ebutor.com", $name = "Nct Status Cron - " .$environment );
				$message->to("rasheed.shaik@ebutor.com");
				$message->subject('Nct Updated Via Cron job  : ' . date('d-m-Y') );
			}elseif($environment=='production'){
				$emails = ['ramanjaneya.dandu@ebutor.com', 'Hassan.Basha@ebutor.com','maheshwar.thalakanti@ebutor.com'];
				$message->from("tracker@ebutor.com", $name = "Nct Status Cron - " .$environment );
				$message->to($emails);
				$message->subject('Nct Updated Via Cron job  : ' . date('d-m-Y') );
			}else{

				$message->from("tracker@ebutor.com", $name = "Tech Support -" .$environment );
				$message->to("satish.racha@ebutor.com");
			}
			$message->bcc("somnath.chowdhury@ebutor.com");
			$message->subject('Nct Updated Via Cron job  : ' . date('d-m-Y') );

		});

	}
	
}