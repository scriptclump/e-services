<?php
/*
FileName : h2hAxisAPIModel
Author   : eButor
Description :
CreatedDate : 12/Mar/2017
*/

//defining namespace
namespace App\Modules\H2HAxis\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use UserActivity;
use Log;
use Utility;

class h2hAxisAPIModel extends Model
{
    protected $table = 'payment_details';
    protected $primaryKey = "pay_id";

    // Save API call details into the table
    public function storeAPICallDetails($apiName, $hostIP, $paramData, $finalResponse){

        $tableData = array(
            "api_name"              =>  $apiName,
            "call_from"             =>  $hostIP,
            "input_params"          =>  json_encode($paramData),
            "api_response"          =>  json_encode($finalResponse),
            "created_at"            =>  date('Y-m-d H:i:s')
            );

        DB::table("h2h_api_call_details")->insert($tableData);

        return 1;
    }

    // Add data into the Main Payment Information table
    public function addPaymentInformationIntoDB($mainTableData,$state_code="TS"){

        // Double check in the Payment Table code and Get the Unique once
        $codeFound = 0;
        $refNoArr = 0;
        Log::info("state_code ---------______".$state_code);
        // Get the Serial Number count from the Table
        $refNoArr = Utility::getReferenceCode('PY',$state_code); 
        // Log::info("CALL prc_reference_no('".$state_code."', 'PY')");
        // Log::info("---------------");   
        $refNo = $refNoArr;
        Log::info("---------------". $refNo);
        // Prepare the mail table data to save into the table
        $mainTableData['pay_code']  =  $refNo;

        Log::info($mainTableData['pay_code']);

        // Log::info("++++++++++");
        $pay_id = $this->insertGetId($mainTableData);
        if( $pay_id > 0){
            return array('p_pay_id' => $pay_id,'payment_ref' =>$refNo);
        }else{
            return 0;
        }
    }

    // Call Axis Bank API to post a request
    public function callAxisH2HAPI($mainTableLastID, $paramData){

        $postRequestAML = $this->preparePostRequetXML($mainTableLastID, $paramData);
        return $this->callCurlWithXML($postRequestAML);
    }

    private function preparePostRequetXML($mainTableLastID, $paramData){

        $xml = '
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:gen="http://axis.com/payment/generic_payments">
               <soapenv:Header/>
               <soapenv:Body>
                  <gen:MTO_Corporate_Payment_WebService>
                     <Record>
                        <!--Zero or more repetitions:-->
                        <Payment_Details>
                           <API_VERSION>1.0</API_VERSION>
                           <CORP_CODE>'.$paramData['CorpCode'].'</CORP_CODE>
                           <CMPY_CODE>'.$paramData['CmpyCode'].'</CMPY_CODE>
                           <TXN_CRNCY>INR</TXN_CRNCY>
                           <TXN_PAYMODE>NE</TXN_PAYMODE>
                           <CUST_UNIQ_REF>'.$mainTableLastID.'</CUST_UNIQ_REF>
                           <TXN_TYPE>'.$paramData['TxnType'].'</TXN_TYPE>
                           <TXN_AMOUNT>'.$paramData['TxnAmount'].'</TXN_AMOUNT>
                           <CORP_ACC_NUM>'.$paramData['CorpAccNum'].'</CORP_ACC_NUM>
                           <CORP_IFSC_CODE>'.$paramData['CorpIFSCCode'].'</CORP_IFSC_CODE>
                           <ORIG_USERID>'.$paramData['OrigUserID'].'</ORIG_USERID>
                           <USER_DEPARTMENT>'.$paramData['UserDepartment'].'</USER_DEPARTMENT>
                           <TRANSMISSION_DATE>'.$paramData['TransmissionDate'].'</TRANSMISSION_DATE>
                           <BENE_CODE>'.$paramData['BeneCode'].'</BENE_CODE>
                           <VALUE_DATE>'.$paramData['ValueDate'].'</VALUE_DATE>
                           <RUN_IDENTIFICATION>'.$paramData['RunIdentification'].'</RUN_IDENTIFICATION>
                           <FILE_NAME>'.$paramData['FileName'].'</FILE_NAME>
                           <BENE_NAME>'.$paramData['BeneName'].'</BENE_NAME>
                           <BENE_ACC_NUM>'.$paramData['BeneAccNum'].'</BENE_ACC_NUM>
                           <BENE_IFSC_CODE>'.$paramData['BeneIFSCCode'].'</BENE_IFSC_CODE>
                           <BENE_AC_TYPE></BENE_AC_TYPE>
                           <BENE_BANK_NAME>'.$paramData['BeneBankName'].'</BENE_BANK_NAME>
                           <BASE_CODE></BASE_CODE>
                           <CHEQUE_NUMBER></CHEQUE_NUMBER>
                           <CHEQUE_DATE>'.$paramData['ChequeDate'].'</CHEQUE_DATE>
                           <PAYABLE_LOCATION></PAYABLE_LOCATION>
                           <PRINT_LOCATION></PRINT_LOCATION>
                           <PRODUCT_CODE></PRODUCT_CODE>
                           <BATCH_ID></BATCH_ID>
                           <BENE_ADDR_1></BENE_ADDR_1>
                           <BENE_ADDR_2></BENE_ADDR_2>
                           <BENE_ADDR_3></BENE_ADDR_3>
                           <BENE_CITY></BENE_CITY>
                           <BENE_STATE></BENE_STATE>
                           <BENE_PINCODE></BENE_PINCODE>
                           <CORP_EMAIL_ADDR></CORP_EMAIL_ADDR>
                           <BENE_EMAIL_ADDR1></BENE_EMAIL_ADDR1>
                           <BENE_EMAIL_ADDR2></BENE_EMAIL_ADDR2>
                           <BENE_MOBILE_NO></BENE_MOBILE_NO>
                           <INVOICE_NUMBER></INVOICE_NUMBER>
                           <INVOICE_DATE></INVOICE_DATE>
                           <NET_AMOUNT></NET_AMOUNT>
                           <TAX></TAX>
                           <CASH_DISCOUNT></CASH_DISCOUNT>
                           <INVOICE_AMOUNT></INVOICE_AMOUNT>
                           <ENRICHMENT1></ENRICHMENT1>
                           <ENRICHMENT2></ENRICHMENT2>
                           <ENRICHMENT3></ENRICHMENT3>
                           <ENRICHMENT4></ENRICHMENT4>
                           <ENRICHMENT5></ENRICHMENT5>
                           <STATUS_ID></STATUS_ID>
                        </Payment_Details>
                     </Record>
                  </gen:MTO_Corporate_Payment_WebService>
               </soapenv:Body>
            </soapenv:Envelope>';

        return $xml;
    }

    private function callCurlWithXML($postRequestAML){

        $serverURL = "https://qah2h.axisbank.co.in/XISOAPAdapter/MessageServlet?senderParty=&senderService=BC_AXIS_WEBSERVICE_EDPL&receiverParty=&receiverService=&interface=SIOA_Corporate_Payment_WebService&interfaceNamespace=http://axis.com/payment/generic_payments";
 
        $headers = array(
                "authorization: Basic Y29ycHVzZXI6YXhpc2NvcnBjb24xIQ==",
                'Content-Type: application/xml'
        ); 

        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $serverURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postRequestAML);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);

        curl_close($ch);
        return $data;
    }
}