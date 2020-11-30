<?php

/*
 * Filename: Utility.php
 * Description: This file is used for manage custom methods
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 4 Sep 2016
 * Modified date: 4 Sep 2016
 */

/*
 * Utility is used to manage custom methods
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Utility
 * @version: 	v1.0
 */

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Lib\Queue;

class Utility
{

	/*
	 * dateFormat() method is used to get date format
	 * @param $date String
	 * @param $format String, default d-m-Y
	 * @return String
	 */

	static public function dateFormat($date, $format='d/m/Y') {
		$date = date($format, strtotime($date));
		return self::verifyDate($date);
	}

	/*
	 * timeFormat() method is used to get time format
	 * @param $date String
	 * @param $format String, default h:i A
	 * @return String
	 */

	static public function timeFormat($date, $format='h:i A') {
		$date = date($format, strtotime($date));
		return self::verifyDate($date);
	}

	/*
	 * dateTimeFormat() method is used to get date and time format
	 * @param $date String
	 * @param $format String, default d-m-Y h:i A
	 * @return String
	 */

	static public function dateTimeFormat($date, $format='d/m/Y h:i A') {
		$date = date($format, strtotime($date));
		return self::verifyDate($date);
	}

	/*
	 * verifyDate() method is used to verify actual date format
	 * @param $date String
	 * @return String
	 */

	static private function verifyDate($date) {
		return ($date == '01-01-1970') ? false : $date;
	}

	/*
	 * sendRequest() method is used to send request by curl
	 * @param $url String
	 * @param $data Array
	 * @return Array
	 */

    static public function sendRequest($url, $data) {
        try {
            $headers = array("cache-control: no-cache","content-type: application/json");
            $response=Utility::sendcUrlRequest($url, $data,$headers);            
            if (isset($response) && is_array($response)) {
                return $response['ResponseBody'];
            } else {
                Log::info('Getting empty response from Tax API');
                return 'Getting empty response from Tax API';
            }
        } catch (Exception $e) {
            Log::info('Tax api error');
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    static public function sendcUrlRequest($url, $data,$headers,$encode_data=1) {
        try {
            Log::info('URL='.$url);
            if($encode_data==1){
                $data = json_encode($data);
            }
            Log::info($data);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                //CURLOPT_PORT => "3100",
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                //CURLOPT_MAXREDIRS => 10,
                //CURLOPT_TIMEOUT => 30,
                //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $data,//json_encode($data),
                CURLOPT_HTTPHEADER => $headers,
            ));
            $response = curl_exec($curl);
            Log::info('TAX Class Response:'.$response);
            $err = curl_error($curl);
            curl_close($curl);
            //Log::info('Curl msg');
            //Log::info($err);
            if ($err) {
                Log::info('curl error');
                Log::info($err);
            } else {
                $response = json_decode($response, true);                
                return $response;
            }
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
	 * sendEmail() method is used to send email with attachment
	 * @param $mailTo Array
	 * @param $subject String
	 * @return $body Array
	 */

    static public function sendEmail($mailTo, $subject, $body = array()) {
        try {
            $data['mailTo'] = $mailTo;
            $data['subject'] = $subject;
            $data['body'] = $body;
            $data = json_encode($data);
            //Log::info($data);
            $data = base64_encode($data);
            $queue = new Queue();
            $args = array("ConsoleClass" => 'mailUtility', 'arguments' => array($data));
            Log::info('Before Queue');
            $token = $queue->enqueue('default', 'ResqueJobRiver', $args);
            return $token;
        }
        catch(Exception $e) {
            return false;
        }
    }
    
    static public function sendEmailQueue($mailTo, $subject, $body = array()) {
        try {
            Log::info('mail enque');
            if(isset($body['attachment']) && !empty($body['attachment']) && is_array($body['attachment'])){
                Session::push('userId', 0);
                //array('nameSpace' => 'App\Modules\PurchaseOrder\Controllers','className' => 'PurchaseOrderController','functionName'=>'downloadPOAction','args'=>array($poId,1));
                $collective_Data = $body['attachment'];
                $namespace = $collective_Data['nameSpace'];
                $object = new $namespace();
                $functioncall = $collective_Data['functionName'];
//                Log::info('Email namespace');
  //              Log::info($namespace);
    //            Log::info('function_'.$functioncall);
                $pdf = $object->$functioncall($collective_Data['args'][0],$collective_Data['args'][1]);
      //          Log::info('after attachment');
                $body['attachment'] = $pdf;
            }
            
            $fields = array('mailTo'=>$mailTo, 'subject'=>$subject, 'attachment'=>$body['attachment'], 'file_name'=>(isset($body['file_name']) ? $body['file_name'] : ''));
           
            $success = Mail::send($body['template'], array(
                'name'=>(isset($body['name']) ? $body['name'] : ''),
                'comment'=>(isset($body['comment']) ? $body['comment'] : ''), 
                'emailContent'=>(isset($body['emailContent']) ? $body['emailContent'] : ''),
                'topMsg'=>(isset($body['topMsg']) ? $body['topMsg'] : ''), 
                'changedby'=>(isset($body['changedby']) ? $body['changedby'] : ''), 
                'mailHTML'=>(isset($body['mailHTML']) ? $body['mailHTML'] : ''), 
                'editFlag'=>(isset($body['editFlag']) ? $body['editFlag'] : ''),
                'tableData'=>(isset($body['tableData']) ? $body['tableData'] : ''), 
                'colunmNames'=>(isset($body['colunmNames']) ? $body['colunmNames'] : ''), 
                'TableCaptions'=>(isset($body['TableCaptions']) ? $body['TableCaptions'] : ''), 
                'count'=>(isset($body['count']) ? $body['count'] : ''),
                'emailBody'=>(isset($body['emailBody']) ? $body['emailBody'] : ''), 
                'size'=>(isset($body['size']) ? $body['size'] : ''), 
                'title'=>(isset($body['title']) ? $body['title'] : ''), 
                'sku'=>(isset($body['sku']) ? $body['sku'] : ''), 
                'mrp'=>(isset($body['mrp']) ? $body['mrp'] : ''), 
                'url'=>(isset($body['url']) ? $body['url'] : ''), 
                'link'=>(isset($body['link']) ? $body['link'] : ''), 
                'username'=>(isset($body['username']) ? $body['username'] : '')), function ($message) use ($fields) {

                $message->to($fields['mailTo']);
                $message->subject($fields['subject']);

                // attachment with data
                if ($fields['file_name'] !='') {
                    $message->attachData($fields['attachment'], $fields['file_name']);
                }
                else if ($fields['attachment'] != '') {
                    // attachment with file path
                    $message->attach($fields['attachment']);
                }
            });
            return $success;
        }
        catch(Exception $e) {
            Log::info('in Exception');
            return false;
        }
    }


    /*
     * getReferenceCode() method is used to get reference code
     * @param $prefix String
     * @param $stateCode String, default TS
     * @return String
     */

    static public function getReferenceCode($prefix, $stateCode='TS',$commit=1) {
        
        $semaphore_key = 2112; // unique integer key for this semaphore (Rush fan!)
        $semaphore_max = 1; // The number of processes that can acquire this semaphore
        $semaphore_permissions = 0666; // Unix style permissions for this semaphore
        $semaphore_autorelease = 1; // Auto release the semaphore if the request shuts down 

        $semaphore = sem_get($semaphore_key, $semaphore_max, $semaphore_permissions, $semaphore_autorelease);
        if(!$semaphore) {
          echo "Failed to get semaphore - sem_get().\n";
            exit();
        }

        // acquire exclusive control
        sem_acquire($semaphore);

        //$stateCode = DB::table('zone')->select('code')->where('zone_id',$stateId)->first();
        //$stateCode = isset($stateCode->code) ? $stateCode->code : "TS";
        if($prefix!="" && $stateCode!=""){
            $refNoArr = DB::connection('mysql-write')->select(DB::raw("SELECT CONCAT(state_code,prefix,DATE_FORMAT(CURDATE(), '%y'),LPAD(MONTH(CURDATE()), 2, '0'),LPAD(serial_numbers.`reference_id`,serial_numbers.`length`,0)) AS ref_no
              FROM serial_numbers
              WHERE serial_numbers.`state_code` = '".$stateCode."' AND serial_numbers.`prefix` = '".$prefix."'
              LIMIT 1"));
            DB::connection('mysql-write')->table("serial_numbers")->where('prefix', $prefix)->where('state_code', $stateCode)->update(['reference_id'=>DB::raw('(reference_id+1)')]);
            $ref_no = isset($refNoArr[0]->ref_no) ? $refNoArr[0]->ref_no : '';
        }else{
            $ref_no = "";
        }
      /*  if($commit==1) { 
            $refNoArr = DB::selectFromWriteConnection(DB::raw('CALL prc_reference_no("'.$stateCode.'", "'.$prefix.'")'));
        }else{
            $refNoArr = DB::selectFromWriteConnection(DB::raw("CALL reference_no('".$stateId."', '".$prefix."')"));
        } */
        // release exclusive control 
        sem_release($semaphore);
        return $ref_no;
    }

    static function getUnitPrice($sumOfUnitPrice, $orderedQty) {
        return ($sumOfUnitPrice / $orderedQty);
    }

    static function getFillRate($invQty, $orderedQty) {
        return (float)(($invQty*100)/$orderedQty);
    }

    /**
     * [putIntorequestLog description]
     * @param  [string] $api_name [Api Name]
     * @param  [string] $data     [Data pushed latter on put this into the mongo central for 
     *                             Work around ]
     * @return [type]           [description]
     */
    static public function putLog($api_name, $data, $dir='so_logs') {

        try {
            $folder_path = trim(storage_path() . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . " ");
            $write_file = $api_name . '.log';
            $file_name = $folder_path . $write_file;
            $write_data = '' . PHP_EOL;
            $write_data .= 'Date:' . date('d-m-Y H:i:s') . PHP_EOL;
            $write_data .= 'Request Data ' . PHP_EOL;
            $write_data .= json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;
            $file = fopen($file_name, "a");
            fwrite($file, $write_data);
            fclose($file);
            return true;
        } catch (ErrorException $e) {
            $order_id = 0;
            $message = $e->getTraceAsString();
            return false;
        }
    }

    static public function getRoundOff($grandTotal, $type='') {
        $grandTotalWithRound = round($grandTotal);
        $roundoff = $grandTotalWithRound - $grandTotal;
        $data  = array('grandtotal'=>$grandTotal, 'roundoff'=>$roundoff, 'gtround'=>$grandTotalWithRound);  
        
        switch ($type) {
            case 'grandtotal':
                return $data['grandtotal'];
            break;

            case 'roundoff':
                return $data['roundoff'];
            break;
            
            case 'gtround':
                return $data['gtround'];
            break;
                    
            default:
                return $data;
            break;
        }        
    }
/*
     * convertNumberToWords() method is used to convert number to words to differentiate decimals for paise
     * @param $number Number
     * @return String
     * It Support Max Number  999999999
     */
    static public function convertNumberToWords($number) {
        //$number = 999999999; Max Number
        if ($number > 0) {
            $num = explode('.', number_format($number, 2));
            $wholenum = str_replace(',', '', $num[0]);
            $decimal = (isset($num[1]) && $num[1] != '00') ? $num[1] : '';
            $decimaltext = '';
            $numtext = self::no_to_words($wholenum).' Rupees';
            $decimaltext = ($decimal != '') ? self::no_to_words($decimal) : '';
            $numtext .= ($decimaltext != '') ? ' & ' . $decimaltext . ' paise' : '';
            return ucwords($numtext . ' Only');
        }else if($number == 0) {
            return ucwords('Zero Rupees Only');
        } else {
            return 'Invalid Number Format';
        }
    }
    /*
     * no_to_words() method is used to convert number to words
     * @param $no Number
     * @return String
     * It Support Max Number  999999999
     */
    static function no_to_words($no) {
        $words = array('0' => '', '1' => 'one', '2' => 'two', '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six', 
            '7' => 'seven', '8' => 'eight', '9' => 'nine', '10' => 'ten', '11' => 'eleven', '12' => 'twelve', '13' => 'thirteen', 
            '14' => 'fourteen', '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen', '18' => 'eighteen', '19' => 'nineteen', 
            '20' => 'twenty', '30' => 'thirty', '40' => 'fourty', '50' => 'fifty', '60' => 'sixty', '70' => 'seventy', 
            '80' => 'eighty', '90' => 'ninty',
            '100' => 'hundred', '1000' => 'thousand', '100000' => 'lakh',
            '10000000' => 'crore');
        if ($no == 0)
            return ' ';
        else {
            $novalue = '';
            $highno = $no;
            $remainno = 0;
            $value = 100;
            $value1 = 1000;
            while ($no >= 100) {
                if (($value <= $no) && ($no < $value1)) {
                    $novalue = $words["$value"];
                    $highno = (int) ($no / $value);
                    $remainno = $no % $value;
                    break;
                }
                $value = $value1;
                $value1 = $value * 100;
            }
            if (array_key_exists("$highno", $words))
                return $words["$highno"] . " " . $novalue . " " . self::no_to_words($remainno);
            else {
                $unit = $highno % 10;
                $ten = (int) ($highno / 10) * 10;
                return $words["$ten"] . " " . $words["$unit"] . " " . $novalue . " " . self::no_to_words($remainno);
            }
        }
    }

    /*
     * This method is used to check first two digits of GST Number is matching the state code from our application.
     * @param GST Number
     * @return Boolean 
     */
    static public function check_gst_state_code($gst_no) {

        $gst_state_code = substr($gst_no, 0, 2);
        
        $result = DB::connection('mysql-write')
                    ->table('zone')
                    ->select('gst_state_code')
                    ->where('gst_state_code', '=', $gst_state_code)
                    ->get();
        $status = count($result) > 0 ? true : false;
        return $status;
    }

    static public function query_db_results_to_file($procedure_name,$fdate,$tdate, $dcNames,$userId, $limit, $offset, $filename) {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', -1);

        $filepath = public_path().'/uploads/reports/'.$filename;

        $query_off_set =  ($offset != 0) ? $offset +1 : $offset;
            
        $query = "CALL getOrderConsolidateReport1(0,0,'$fdate','$tdate',$dcNames,$userId,'".$limit."' , '".$query_off_set."')";        
        $results = json_decode(json_encode(DB::connection('mysql_reports')->select($query)), true);
        if (count($results) > 0) {
            if($offset == 0) {
                if(file_exists($filepath))
                    unlink($filepath);
                $fp = fopen($filepath, 'a+');                             
                fputcsv($fp,array_keys($results[0]));
            } else {
                // Open a file in write mode ('w')
                $fp = fopen($filepath, 'a+');
            }       
              
            // Loop through file pointer and a line 
            foreach ($results as $key => $fields) {

                fputcsv($fp, $fields); 
            }
            
            if (count($results) == 50000) {

                $offset += $limit;
                
                Utility::query_db_results_to_file($procedure_name,$fdate,$tdate, $dcNames,$userId, $limit, $offset, $filename);
            }
            fclose($fp);

            header("Content-Type: application/force-download");
            header("Content-Disposition:  attachment; filename=\"" . $filename . "\";" );
            header("Content-Transfer-Encoding:  binary");
            header("Accept-Ranges: bytes");
            header('Content-Length: ' . filesize($filepath));
            
            $readFile = file($filepath);
            foreach($readFile as $val){
                echo $val;
            }
            exit();
        } else {
            echo "No results found";
            exit();
        }       
    }
}
