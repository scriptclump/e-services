<?php
/*
FileName : CustomertallyPushVoucherController
Author   : eButor
Description :
CreatedDate :18/02/2019
*/

//defining namespace
namespace App\Modules\TallyConnector\Controllers;

//loading namespaces
use App\Http\Controllers\BaseController;

use App\Modules\TallyConnector\Models\CustomertallyModel;
use Illuminate\Http\Request;
use App\Central\Repositories\MongoRepo;
use Input;
use Log;
use Session;

class CustomertallyPushVoucherController extends BaseController
{	

	public function __construct()
	{
		$this->customerModel = new CustomertallyModel();
	
	}


    public  function CustomertallyPushVouchers(){
	try
		{
		$data = Input::all();
		$resultArray = array();
		$finalResultArray = array();
		$postData = json_decode($data['data'], true);
		$HubId = $postData['le_wh_id'];
		$voucher_date_from = $postData['voucher_date_From'];
		$voucher_date_to = $postData['voucher_date_to'];
		$voucher_type = $postData['voucher_type'];
		$voucher_flag = $postData['is_posted'];
		// Null Value Validation...
		if ($HubId == '')
		{
		return json_encode(array(
			"status" => "failure",
			"data" => [],
			"message" => "Hub Id can not be Blank"
		));
		}

		if ($voucher_type == '')
		{
		return json_encode(array(
			"status" => "failure",
			"data" => [],
			"message" => "voucher type can not be Blank"
		));
		}

		if ($voucher_date_from == '')
		{
		return json_encode(array(
			"status" => "failure",
			"data" => [],
			"message" => "From Date can not be Blank"
		));
		}
	  else if ($voucher_date_to == '')
		{
		return json_encode(array(
			"status" => "failure",
			"data" => [],
			"message" => "To Date can not be Blank"
		));
		} else if (!(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $voucher_date_from)))
		{

		// here the date format must be in yyyy-mm-dd...

		return json_encode(array(
			"status" => "failure",
			"data" => [],
			"message" => "voucher Date is not in required format"
		));
		}
		else if (!(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $voucher_date_to)))
		{
		// here the date format must be in yyyy-mm-dd...
		return json_encode(array(
				"status" => "failure",
				"data" => [],
				"message" => "voucher Date is not in required format"
		));
		}
        else
		{
		}

		// Based on the Given input Getting all the Voucher Data
		$vouchertypes = ['Sales','Credit Note'];
        if(in_array($voucher_type, $vouchertypes)){
        	$cost_centredata = 	$this->customerModel->getCostCentreData($HubId);
        	$cost_centre=isset($cost_centredata->cost_center)?$cost_centredata->cost_center:"";
        	$bu_name=isset($cost_centredata->bu_name)?$cost_centredata->bu_name:"";
        	$cost_centre.=' - '.$bu_name;
        } else {
        	$cost_centredata = $this->customerModel->getWarehouseCostCentreByHub($HubId);
        	$le_wh_id = $cost_centredata->le_wh_id;
        	$cost_centre = $cost_centredata->cost_center;
        }
        $tally_company_name=isset($cost_centredata->tally_company_name)?$cost_centredata->tally_company_name:"";
        $sales_ledger_name=isset($cost_centredata->sales_ledger_name)?$cost_centredata->sales_ledger_name:"";
        if ($tally_company_name == '') {
			return json_encode(array("status" => "failure","data" => [],"message" => "Company name is empty"));
		}

		$voucherData = $this->customerModel->getVoucherData($cost_centre, $voucher_type, $voucher_flag, $voucher_date_from, $voucher_date_to);
		$response = [];
		$finalresponse = [];
		foreach($voucherData as $voucher)
			{
			$VCType = $voucher->voucher_type;
			$VCNumber = $voucher->voucher_code;
			$VCDate = $voucher->voucher_date;
			$Naration = $voucher->naration;
			$reffNumber = $voucher->reference_no;
			$cost_centre = $voucher->cost_centre;

			// Check Vouvher Duplication

			$checkVoucherFlag = 0;
			$ignoreList = ['Receipt', 'Payment'];
			if (!in_array($VCType, $ignoreList))
			{ //Adding receipt exception for multiple payments
				$checkVoucherFlag = $this->customerModel->checkDuplicateVoucher($VCNumber,$reffNumber, $VCType);
			}

			if ($checkVoucherFlag <= 0)
			{
				$voucherLineData = $this->customerModel->getVoucherLineData($voucher->voucher_code, $voucher->voucher_type, $reffNumber);
				$drList = array();
				$crList = array();
				// Arrange Dr/Cr Type
				$drcnt = $crcnt = 0;
				foreach($voucherLineData as $lineData)
				{
					$drList[$drcnt]['trans_type'] = $lineData->tran_type;
					if ($lineData->voucher_type == 'Sales' && $lineData->ledger_group == 'Sundry Debtors')
					{
						if($sales_ledger_name!=""){
							$drList[$drcnt]['ledger_account'] = $sales_ledger_name;
						}else{
							$drList[$drcnt]['ledger_account'] = substr($lineData->ledger_account, -15);
						}
					}
					elseif ($lineData->voucher_type == 'Credit Note' && $lineData->ledger_group == 'Sundry Debtors')
					{
						$drList[$drcnt]['ledger_account'] = substr($lineData->ledger_account, -15);
					}
					elseif ($lineData->voucher_type == 'Receipt' && $lineData->ledger_group == 'Sundry Debtors')
					{
						$drList[$drcnt]['ledger_account'] = substr($lineData->ledger_account, -15);
					}
					else
					{
						$drList[$drcnt]['ledger_account'] = $lineData->ledger_account;
					}

					$drList[$drcnt]['amount'] = $lineData->amount;
					$drList[$drcnt]['cost_centre'] = $lineData->cost_centre;
					$drcnt++;
				}

				$transDetails['DR'] = $drList;
				$transDetails['CR'] = $crList;
				$resp = $this->executeTallyAPI($VCType, $VCNumber, $VCDate, $Naration, $reffNumber, $transDetails['DR'], $transDetails['CR'],$tally_company_name);
				$finalxml = str_replace(["\n", "\t"], "", $resp);
				$response[] = ['xmldata' => $finalxml, 'voucher_code' => $VCNumber];
				$finalresponse= array(
					"status" => "Success",
					"data" => $response,
					"message" => "successfully returned xmldata"
				);
				}
			}
          if(count($finalresponse)>0)
          {
          	return json_encode($finalresponse);
          }
	      else
	      {
	      	return json_encode(array("status"=>"failure","data"=>[],"message"=>"Vouchers Not Found"));
	      }
		}
	   catch(Exception $e)
		{
		Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}
  public function executeTallyAPI($VCType, $VCNumber, $VCDate, $Naration, $reffNumber, $drTransDet, $crTransDet,$companyName=''){
      //need to return the xml data file....
    // Convert date format as Tally
        //$vcDate 	= $request->input('vcDate');
		$vcDate		= Date('Ymd', strtotime($VCDate));
		$naration 	= str_replace("'", "&apos;", (str_replace("&", "&amp;",$Naration)) );
		// Prepare tally DR side
		$drTallyXml = '';
			foreach($drTransDet as $data){
				$minusSymbol = $data['trans_type']=='Dr' ? '-' : '';
				$yesNo = $data['trans_type']=='Dr' ? 'Yes' : 'No';

				$newReff = '';
				if($data['trans_type']=='Cr' && $VCType=='Purchase'){
					$newReff = '<BILLALLOCATIONS.LIST>
									<NAME>'.trim($reffNumber).'</NAME>
									<BILLTYPE>New Ref</BILLTYPE>
									<TDSDEDUCTEEISSPECIALRATE>No</TDSDEDUCTEEISSPECIALRATE>
									<AMOUNT>'.trim($data['amount']).'</AMOUNT>
									<INTERESTCOLLECTION.LIST></INTERESTCOLLECTION.LIST>
									<STBILLCATEGORIES.LIST></STBILLCATEGORIES.LIST>
								</BILLALLOCATIONS.LIST>';
				}
				if($data['trans_type']=='Dr' && $VCType=='Sales'){
					$newReff = '<BILLALLOCATIONS.LIST>
									<NAME>'.trim($reffNumber).'</NAME>
									<BILLTYPE>New Ref</BILLTYPE>
									<TDSDEDUCTEEISSPECIALRATE>No</TDSDEDUCTEEISSPECIALRATE>
									<AMOUNT>-'.trim($data['amount']).'</AMOUNT>
									<INTERESTCOLLECTION.LIST></INTERESTCOLLECTION.LIST>
									<STBILLCATEGORIES.LIST></STBILLCATEGORIES.LIST>
								</BILLALLOCATIONS.LIST>';
				}
				if($data['trans_type']=='Cr' && $VCType=='Credit Note'){
					$newReff = '<BILLALLOCATIONS.LIST>
									<NAME>'.trim($reffNumber).'</NAME>
									<BILLTYPE>Agst Ref</BILLTYPE>
									<TDSDEDUCTEEISSPECIALRATE>No</TDSDEDUCTEEISSPECIALRATE>
									<AMOUNT>'.trim($data['amount']).'</AMOUNT>
									<INTERESTCOLLECTION.LIST></INTERESTCOLLECTION.LIST>
									<STBILLCATEGORIES.LIST></STBILLCATEGORIES.LIST>
								</BILLALLOCATIONS.LIST>';
				}
				if($data['trans_type']=='Dr' && $VCType=='Journal'){
					$newReff = '<BILLALLOCATIONS.LIST>
									<NAME>'.trim($reffNumber).'</NAME>
									<BILLTYPE>Agst Ref</BILLTYPE>
									<TDSDEDUCTEEISSPECIALRATE>No</TDSDEDUCTEEISSPECIALRATE>
									<AMOUNT>-'.trim($data['amount']).'</AMOUNT>
									<INTERESTCOLLECTION.LIST></INTERESTCOLLECTION.LIST>
									<STBILLCATEGORIES.LIST></STBILLCATEGORIES.LIST>
								</BILLALLOCATIONS.LIST>';
				}
				
				if($data['trans_type']=='Cr' && $VCType=='Receipt' && trim($reffNumber)!=""){
					$newReff = '<BILLALLOCATIONS.LIST>
									<NAME>'.trim($reffNumber).'</NAME>
									<BILLTYPE>Agst Ref</BILLTYPE>
									<TDSDEDUCTEEISSPECIALRATE>No</TDSDEDUCTEEISSPECIALRATE>
									<AMOUNT>'.trim($data['amount']).'</AMOUNT>
									<INTERESTCOLLECTION.LIST></INTERESTCOLLECTION.LIST>
									<STBILLCATEGORIES.LIST></STBILLCATEGORIES.LIST>
								</BILLALLOCATIONS.LIST>';
				}

				if($data['trans_type']=='Dr' && $VCType=='Payment' && trim($reffNumber)!=""){
					$newReff = '<BILLALLOCATIONS.LIST>
									<NAME>'.trim($reffNumber).'</NAME>
									<BILLTYPE>Agst Ref</BILLTYPE>
									<TDSDEDUCTEEISSPECIALRATE>No</TDSDEDUCTEEISSPECIALRATE>
									<AMOUNT>-'.trim($data['amount']).'</AMOUNT>
									<INTERESTCOLLECTION.LIST></INTERESTCOLLECTION.LIST>
									<STBILLCATEGORIES.LIST></STBILLCATEGORIES.LIST>
								</BILLALLOCATIONS.LIST>';
				}

				$drTallyXml .= '<ALLLEDGERENTRIES.LIST>
									<LEDGERNAME>'.trim( str_replace("'", "&apos;", (str_replace("&", "&amp;", $data['ledger_account']))) ).'</LEDGERNAME>
									<ISDEEMEDPOSITIVE>'.$yesNo.'</ISDEEMEDPOSITIVE>
									<AMOUNT>'.$minusSymbol.trim($data['amount']).'</AMOUNT>
									'.$newReff.'
									<CATEGORYALLOCATIONS.LIST>
										<CATEGORY>EBUTOR</CATEGORY>
										<COSTCENTREALLOCATIONS.LIST>
											<NAME>'.trim(str_replace("&", "&amp;", $data['cost_centre'])).'</NAME>
											<ISDEEMEDPOSITIVE>'.$yesNo.'</ISDEEMEDPOSITIVE>
											<AMOUNT>'.$minusSymbol.trim($data['amount']).'</AMOUNT>
										</COSTCENTREALLOCATIONS.LIST>
									</CATEGORYALLOCATIONS.LIST>
								</ALLLEDGERENTRIES.LIST>';	
			}

		// Prepare tally CR side
		$crTallyXml = '';
		
		// Prepare XML to create Voucher Master
		$requestXML = '
			<ENVELOPE>
				<HEADER>
					<VERSION>1</VERSION>
					<TALLYREQUEST>Import</TALLYREQUEST>
					<TYPE>Data</TYPE>
					<ID>Vouchers</ID>
				</HEADER>
				<BODY>
					<DESC>
						<STATICVARIABLES>
							<SVCURRENTCOMPANY>'.trim($companyName).'</SVCURRENTCOMPANY>
						</STATICVARIABLES>
					</DESC>
					<DATA>
						<TALLYMESSAGE>
							<VOUCHER>
								<DATE>'.trim($vcDate).'</DATE>
								<NARRATION>'.trim($naration).'</NARRATION>
								<VOUCHERTYPENAME>'.trim($VCType).'</VOUCHERTYPENAME>
								<VOUCHERNUMBER>'.trim($VCNumber).'</VOUCHERNUMBER>
								<REFERENCEDATE>'.trim($VCDate).'</REFERENCEDATE>
								<REFERENCE>'.trim($VCNumber).'</REFERENCE>
								'.$drTallyXml.
								$crTallyXml.'
							</VOUCHER>
						</TALLYMESSAGE>
					</DATA>
				</BODY>
			</ENVELOPE>';

		return $requestXML;
	}
    //function for updating the tally response in the database...
    public function CustomerVoucherUpdate()
    {
    	try{
           $data = Input::all();
           $finaldata = str_replace("\\", "", $data);
           $resultArray = array();
           $finalResultArray = array();
           $postData = json_decode($finaldata['data'],true);
           $HubId = $postData['le_wh_id'];
			//Mongo connection... and storing the logs ...
	       $api_request = $postData;
           $updateArr = [];
          //  print_r($postData['TallyResponse']);die();
          if(isset($postData['TallyResponse']) && is_array($postData['TallyResponse'])){
	          	foreach ($postData['TallyResponse'] as $response ) {
		          	if(isset($response['status'])){
						if($response['status']==1){
							// update table by 1
							$voucherData = $this->customerModel->updateVoucher($response['vCode'], 1,"Data Imported Successfully");
						}else{
							//update table by 0
		                    $voucherData = $this->customerModel->updateVoucher($response['vCode'], 0, $response['error']);   
						}
					}else{
						$voucherData = $this->customerModel->updateVoucher($response['vCode'], 0, "No Response Received");
					}
				    if($voucherData==true)
				    {
				    	$updateArr[] = array("vCode"=>$response['vCode'],"status"=>1,"message"=>"Voucher updated successfully");
				    }
				    else
				    {
				    	$updateArr[] = array("vCode"=>$response['vCode'],"status"=>0,"message"=>"Failed to update voucher");
				    }
	          	}
	          	$api_log_array = array("route"=>"CustomerVoucherUpdate",
	       		"le_wh_id"=>$HubId,
	       		"created_at"=>date('Y:m:d'),
	       		"request"=>$api_request,
	       		"response"=>$updateArr);
		       $mongoRepo = new MongoRepo();
	           $mongoRepo->insert("tally_api_logs", $api_log_array);
	          	return json_encode(array("status"=>"Success","data"=>$updateArr,"message"=>"Vouchers updated successfully"));
			}else {
	            return json_encode(array("status"=>"failure","data"=>[],"message"=>"Empty data to update"));
	        }
	    }catch(Exception $e){

	    }
	}

    public function pushTallyLedgers(){
    	try{
    		$data = Input::all();
			$response = array();
			$finalResultArray = array();
			if(!isset($data['data'])){
				return json_encode(array("status" => "failure","data" => [],"message" => "Some parameters missing"));
			}
			$postData = json_decode($data['data'], true);
			$HubId = $postData['le_wh_id'];
			$type = isset($postData['type'])?$postData['type']:0; // 0-Retailers(Debtors), 1- Suppliers(Creditors)
			$cost_centredata = 	$this->customerModel->getCostCentreData($HubId);
	        $companyName=isset($cost_centredata->tally_company_name)?$cost_centredata->tally_company_name:"";
	        $cost_centre=isset($cost_centredata->cost_center)?$cost_centredata->cost_center:"";
        	$bu_name=isset($cost_centredata->bu_name)?$cost_centredata->bu_name:"";
        	$cost_centre.=' - '.$bu_name;

	        $parent_le_id=isset($cost_centredata->legal_entity_id)?$cost_centredata->legal_entity_id:"";
	        $legal_entity_type_id=isset($cost_centredata->legal_entity_type_id)?$cost_centredata->legal_entity_type_id:"";

	        if ($companyName == '') {
				return json_encode(array("status" => "failure","data" => [],"message" => "Company name is empty"));
			}
			if ($parent_le_id == '') {
				return json_encode(array("status" => "failure","data" => [],"message" => "Parent Le ID is empty"));
			}
			//echo time().'====';
			$FCData = [];
			$customerData = $this->customerModel->getRetailerData($parent_le_id);//Customers data
			//echo '---'.time().'====';
			if($legal_entity_type_id==1016){
				$FCData = $this->customerModel->getFCData($parent_le_id);	//Fc data
			}

			$result = array_merge_recursive($customerData, $FCData);
			$uniqueCustomerResult = array_map("unserialize",
     					array_unique(array_map("serialize", $result)));
			$failedLedgers = $this->customerModel->getFailedLedgerData($cost_centre);	//Failed Ledger data
			//echo '--FAIL--'.time().'====';
			if(count($failedLedgers)>0){
				$result = array_merge_recursive($uniqueCustomerResult, $failedLedgers);
				$uniqueCustomerResult = array_map("unserialize",
	     					array_unique(array_map("serialize", $result)));
				//echo '---MRGGG---'.time().'====';
			}
			//print_r($uniqueCustomerResult);die;

			foreach($uniqueCustomerResult as $customer){
				$ebutorCustomerName = $customer->business_legal_name . '-' . $customer->le_code;
				$address1 	= $customer->address1;
				$address2 	= $customer->address2;
				$city		= $customer->city;
				$pinCode	= $customer->pincode;
				$state		= $customer->state_name;

				$postFields = array(
					'parentDC'			=>	'Sundry Debtors',
					'ledgerName'		=>	$ebutorCustomerName,
					'aliasName'			=>	$customer->le_code,
					'gstin'			=>	$customer->gstin,
					'openingBalance'	=>	'0',
					'address1'			=>	$address1,
					'address2'			=>	$address2,
					'city'				=>	$city,
					"pinCode"			=>	$pinCode,
					"state"				=>	$state,
					'companyName'		=> 	$companyName
				);
				if( $customer->le_code!=''){
					$xmlresponse = $this->createLedgerMaster($postFields);
					if(isset($xmlresponse['status'])){						
						if($xmlresponse['status']=='success'){
							$finalxml = str_replace(["\n", "\t"], "", $xmlresponse['xmldata']);
							$response[] = ['xmldata' => $finalxml, 'ledger_code' => $customer->le_code];
						}
					}
				}
			}
			$finalresponse= array(
								"status" => "Success",
								"data" => $response,
								"message" => "successfully returned xmldata"
							);
			return json_encode($finalresponse);
    	}catch(Exception $e){

    	}
	}

	// Entry point to create the ledger master
	public function createLedgerMaster($postFields){
        $compnayFromInput 	= str_replace("&", "&amp;", $postFields['companyName']); 
		$parentDC 			= str_replace("&", "&amp;", $postFields['parentDC']);
		$ledgerName 		= str_replace("&", "&amp;", $postFields['ledgerName']);
		$openingBalance 	= $postFields['openingBalance'];
		$aliasName 			= str_replace("&", "&amp;", $postFields['aliasName']);

		$address1			= str_replace("&", "&amp;", $postFields['address1']);
		$address2			= str_replace("&", "&amp;", $postFields['address2']);
		$city				= str_replace("&", "&amp;", $postFields['city']);
		$pinCode			= $postFields['pinCode'];
		$stateName			= str_replace("&", "&amp;", $postFields['state']);
		$gstin			= str_replace("&", "&amp;", $postFields['gstin']);

		$aliasName = $aliasName!='' ? '<NAME TYPE="String">'.trim($aliasName).'</NAME>' : '</NAME>';

		$address1  = $address1!='' ? '<ADDRESS>'.trim($address1).'</ADDRESS>' : '';
		$address2  = $address2!='' ? '<ADDRESS>'.trim($address2).'</ADDRESS>' : '';
        $city      = $city!='' ? '<ADDRESS>'.trim($city).'</ADDRESS>' : '';

		// check for empty data
		if($ledgerName=='' || $parentDC==''){
			$finalResponse = array(
				'message'	=> 'API Argument does not match, Call aborted!',
				'status'	=> 'failed',
				'code'		=> '401'
			);
			return $finalResponse;
		}

		// Prepare XML to create Ledger Master
		$headerPart = $this->prepareHeaderPart($compnayFromInput);
		$requestXML = $headerPart . '
					<DATA>
						<TALLYMESSAGE>
							<LEDGER NAME="'.trim($ledgerName).'" Action = "Alter">
								<NAME.LIST TYPE="String">
									<NAME TYPE="String">'.trim($ledgerName).'</NAME>
									'.$aliasName.'
								</NAME.LIST>
								<PARENT>'.trim($parentDC).'</PARENT>
								<OPENINGBALANCE>'.trim($openingBalance).'</OPENINGBALANCE>
								<ISCOSTCENTRESON>Yes</ISCOSTCENTRESON>
								<ADDRESS.LIST>
									'.$address1.
									$address2.
									$city.'
								</ADDRESS.LIST>
								<STATENAME>'.trim($stateName).'</STATENAME>
								<PINCODE>'.trim($pinCode).'</PINCODE>
								<ADDITIONALNAME>'.trim($ledgerName).'</ADDITIONALNAME>
								<CURRENCYNAME>INR</CURRENCYNAME>
								<COUNTRYNAME>India</COUNTRYNAME>
								<GSTREGISTRATIONTYPE>Regular</GSTREGISTRATIONTYPE>
								<PARTYGSTIN>'.trim($gstin).'</PARTYGSTIN>
								<LEDSTATENAME>'.trim($stateName).'</LEDSTATENAME>
							</LEDGER>
						</TALLYMESSAGE>
					</DATA>
				</BODY>
			</ENVELOPE>';
			$finalResponse = array(
				'message'	=> 'success',
				'status'	=> 'success',
				'code'		=> '200',
				'xmldata' => $requestXML
			);
		return $finalResponse;
	}
		// Preparing the common header part of Tally Ledger XML
	private function prepareHeaderPart($compnayFromInput){
		// Get Company Name
		$currentCompanyName = trim($compnayFromInput);
		$xmlLedgerHeader = '
		<ENVELOPE>
				<HEADER>
					<VERSION>1</VERSION>
					<TALLYREQUEST>Import</TALLYREQUEST>
					<TYPE>Data</TYPE>
					<ID>All Masters</ID>
				</HEADER>
				<BODY>
					<DESC>
						<STATICVARIABLES>
							<SVCURRENTCOMPANY>'.$currentCompanyName.'</SVCURRENTCOMPANY>
						</STATICVARIABLES>
					</DESC>';
		return $xmlLedgerHeader;
	}
	 //function for updating the tally response in the database...
    public function CustomerLedgerUpdate()
    {
    	try{
           $data = Input::all();
           $finaldata = str_replace("\\", "", $data);
           $resultArray = array();
           $finalResultArray = array();
           $postData = json_decode($finaldata['data'],true);
           $HubId = $postData['le_wh_id'];
			//Mongo connection... and storing the logs ...
	       $api_request = $postData;
           $updateArr = [];
          //  print_r($postData['TallyResponse']);die();
          if(isset($postData['TallyResponse']) && is_array($postData['TallyResponse'])){
	          	foreach ($postData['TallyResponse'] as $response ) {
		          	if(isset($response['status'])){
						if($response['status']==1){
							// update table by 1
							$voucherData = $this->customerModel->updateLedger($response['vCode'], 1,"Data Imported Successfully");
						}else{
							//update table by 0
		                    $voucherData = $this->customerModel->updateLedger($response['vCode'], 0, $response['error']);   
						}
					}else{
						$voucherData = $this->customerModel->updateLedger($response['vCode'], 0, "No Response Received");
					}
				    if($voucherData==true)
				    {
				    	$updateArr[] = array("vCode"=>$response['vCode'],"status"=>1,"message"=>"Ledger updated successfully");
				    }
				    else
				    {
				    	$updateArr[] = array("vCode"=>$response['vCode'],"status"=>0,"message"=>"Failed to update ledger");
				    }
	          	}
	          	$api_log_array = array("route"=>"CustomerLedgerUpdate",
	       		"le_wh_id"=>$HubId,
	       		"created_at"=>date('Y:m:d'),
	       		"request"=>$api_request,
	       		"response"=>$updateArr);
		       $mongoRepo = new MongoRepo();
	           $mongoRepo->insert("tally_api_logs", $api_log_array);
	          	return json_encode(array("status"=>"Success","data"=>$updateArr,"message"=>"Ledgers updated successfully"));
			}else {
	            return json_encode(array("status"=>"failure","data"=>[],"message"=>"Empty data to update"));
	        }
	    }catch(Exception $e){

	    }
	}
}








    




