<?php
namespace App\Modules\GSTReports\Controllers;
date_default_timezone_set('Asia/Kolkata');
use Log;
use Mail;
use File;
use Excel;
use Session;
use Redirect;
use App\Lib\Queue;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\GSTReports\Models\OutwardSupplyReport;
class GstReportsController extends BaseController {
    public function __construct() {
        try {

            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                    Redirect::to('/login')->send();
                }
                return $next($request);
            });

            parent::__construct();
            parent::Title('GST Reports');
            //$this->queue = new Queue();
            $this->_inventory = new Inventory();
            $this->_outwardSupply = new OutwardSupplyReport();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function indexAction(Request $request) {
      try { 
            $dcData=$this->_outwardSupply->warehouseData();      
            return view('GSTReports::index',['warehouse'=>$dcData]);
         }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
     public function invoceReport() {
      try { 
            $dcData=$this->_outwardSupply->warehouseData();      
           return view('GSTReports::index1',['warehouse'=>$dcData]);
         }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function invExportReport(Request $request){
        $fromDate = date('Y-m-d 00:00:00', strtotime($request->get('transac_date_from')));
        $toDate = date('Y-m-d 23:59:59', strtotime($request->get('transac_date_to')));
        $wh_Id=$request->get('le_id');
        $legalEntityId = Session::get('legal_entity_id');
        $details = json_decode(json_encode($this->_outwardSupply->generateInvoiceTaxReport($fromDate,$toDate,$legalEntityId,$wh_Id)), true);
        if (count($details)>0) {
                $details = array_map(function (array $elem) {
                    if(isset($elem['TAX']))
                        $elem['TAX'] = (float)$elem['TAX'];
                    if(isset($elem['Invoice Voucher Value']))
                        $elem['Invoice Voucher Value'] = (float)$elem['Invoice Voucher Value'];
                    if(isset($elem['Taxable Value']))
                        $elem['Taxable Value'] = (float)$elem['Taxable Value'];
                    if(isset($elem['CGST']))
                        $elem['CGST'] = (float)$elem['CGST'];
                    if(isset($elem['SGST']))
                        $elem['SGST'] = (float)$elem['SGST'];
                    if(isset($elem['IGST']))
                        $elem['IGST'] = (float)$elem['IGST'];
                    if(isset($elem['UTGST']))
                        $elem['UTGST'] = (float)$elem['UTGST'];
                    return $elem;             
                    },
                    $details
                );
            }
        Excel::create('Invoice Tax Report  - '. date('Y-m-d'),function($excel) use($details) {
            $excel->sheet('Invoice Tax Report ', function($sheet) use($details) {          
            $sheet->fromArray($details);
            });      
        })->export('xls');
    }
    public function getOutwardReportAction(Request $request) {
        try {
            $input = json_decode($request->input('filterDetails'), true);
            $param = array();
            $param['userId'] = $request->session()->get('userId');
            $param['start'] = $input[0]['startDate'];
            $param['end'] = $input[0]['endDate'];
            $param['type'] = 'tax';
            $encoded = base64_encode(json_encode($param));
            
            $args = array("ConsoleClass" => 'OutwardSupplyReport', 'arguments' => array('data' => $encoded));
            $this->queue->enqueue('default', 'ResqueJobRiver', $args);
            return "You will get an email with Outward Supply Report attached !!";
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function createExcel($userId, $start, $end, $type) {
        try {
            $from = date('Y-m-d 00:00:00', strtotime($start));
            $to = date('Y-m-d 23:59:59', strtotime($end));
            if ($type === 'tax') {
                $result = json_decode(json_encode($this->_outwardSupply->invoiceDetails($from, $to)), true);
                $fileName = 'Outward_Supply_Report_' . date('Y_m_d', strtotime($start)) . '_to_' . date('Y_m_d', strtotime($end));
                $subject = 'Outward Supply Report';
                $viewPage = 'gstreportexcle';
            } else if ($type === 'hsn') {
                $result = json_decode(json_encode($this->_outwardSupply->hsnCodeDetails($from, $to)), true);
                $fileName = 'Hsn_Outward_Supply_Report_' . date('Y_m_d', strtotime($start)) . '_to_' . date('Y_m_d', strtotime($end));
                $subject = 'HSN Wise Outward Supply Report';
                $viewPage = 'gsthsnreportexcle';
            }
            echo "Total Rows: " . count($result) . "\n";
//            Log::info("Total Rows: " . count($result));
            
            Excel::create($fileName, function($excel) use($result, $subject) {
                $excel->sheet($subject, function($sheet) use($result) {
                    $chunckResult = array_chunk($result, 12500, true);
                    foreach($chunckResult as $chunckKey => $eachChunk){
                        echo "Chunck Rows: " . count($eachChunk) . "\n";
                    //    Log::info("Chunck Rows: " . count($eachChunk));
                        $sheet->fromArray($eachChunk);
                    }
                });
            })->store('xlsx', public_path('download/gst_reports'));
            
            $filePath[] = public_path('download/gst_reports') . DIRECTORY_SEPARATOR . $fileName . ".xlsx";
            return $this->sendMail($filePath, $userId, $subject);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            echo $ex;
        }
    }
    public function sendMail($filePath, $userId, $subject) {
        try {
            $email = $this->_outwardSupply->getUserEmail($userId);
            $emailBody = "Hello " . ucwords(str_replace(".", " ", explode("@", $email)[0])) . ", <br/><br/>";
            $emailBody .= "Please find attached " . $subject . ".<br/><br/>";
            $emailBody .= "*Note: This is an auto generated email !!";
            if (Mail::send('emails.dmsMail', ['emailBody' => $emailBody], function ($message) use ($email, $filePath, $subject) {
                        $message->to($email);
                        $message->subject($subject . date('d-m-Y'));
                        foreach($filePath as $eachFilepath){
                            echo $eachFilepath."\n";
                            $message->attach($eachFilepath);
                        }
                    }, true)) {
                foreach($filePath as $eachFilepath){
                    File::delete($eachFilepath);
                }
                echo "Mail sent to - " . $email . " !! Temp file deleted !!\n";
            } else {
                echo "Error in sending mail to " . $email . " !!\n";
            }
            return $email;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
            echo $ex;
        }
    }
    public function hsnwiseAction() {
        try {
             $dcData=$this->_outwardSupply->warehouseData();      
            return view('GSTReports::hsnwise',['warehouse'=>$dcData]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getHsnOutwardReportAction(Request $request) {
        try {
        $fromDate = date('Y-m-d 00:00:00', strtotime($request->get('hsn_date_from')));
        $toDate = date('Y-m-d 23:59:59', strtotime($request->get('hsn_date_to')));
        $legal_id=$request->get('le_id');
        $details = json_decode(json_encode($this->_outwardSupply->generateInvoiceHsnWiseReport($fromDate,$toDate,$legal_id)), true);
                if (count($details)>0) {
                $details = array_map(function (array $elem) {
                    if(isset($elem['Total Qty']))
                        $elem['Total Qty'] = (float)$elem['Total Qty'];
                    if(isset($elem['Invoice With Tax']))
                        $elem['Invoice With Tax'] = (float)$elem['Invoice With Tax'];
                    if(isset($elem['Invoice Without Tax']))
                        $elem['Invoice Without Tax'] = (float)$elem['Invoice Without Tax'];
                    if(isset($elem['CGST']))
                        $elem['CGST'] = (float)$elem['CGST'];
                    if(isset($elem['SGST']))
                        $elem['SGST'] = (float)$elem['SGST'];
                    if(isset($elem['IGST']))
                        $elem['IGST'] = (float)$elem['IGST'];
                    if(isset($elem['UTGST']))
                        $elem['UTGST'] = (float)$elem['UTGST'];
                    return $elem;             
                    },
                    $details
                );
            }
        Excel::create('Invoice HSN Wise Report  - '. date('Y-m-d'),function($excel) use($details) {
            $excel->sheet('Invoice HSN Wise Report', function($sheet) use($details) {          
            $sheet->fromArray($details);
            });      
        })->export('xls');
           
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getReturnTaxReport(){
        $dcData=$this->_outwardSupply->warehouseData();      
           return view('GSTReports::returntaxreport',['warehouse'=>$dcData]);
    }
    public function getReturnTaxReportToExcel(Request $request){
        try{
        $fromDate = date('Y-m-d 00:00:00', strtotime($request->get('hsn_date_from')));
        $toDate = date('Y-m-d 23:59:59', strtotime($request->get('hsn_date_to')));
        $legal_id=$request->get('le_id');
        $details = json_decode(json_encode($this->_outwardSupply->generateReturnTaxReport($fromDate,$toDate,$legal_id)), true);
            if (count($details)>0) {
            $details = array_map(function (array $elem) {
                if(isset($elem['TAX']))
                    $elem['TAX'] = (float)$elem['TAX'];
                if(isset($elem['Return Voucher Valu']))
                    $elem['Return Voucher Value'] = (float)$elem['Return Voucher Value'];
                if(isset($elem['Taxable Amount']))
                    $elem['Taxable Amount'] = (float)$elem['Taxable Amount'];
                if(isset($elem['CGST']))
                    $elem['CGST'] = (float)$elem['CGST'];
                if(isset($elem['SGST']))
                    $elem['SGST'] = (float)$elem['SGST'];
                if(isset($elem['IGST']))
                    $elem['IGST'] = (float)$elem['IGST'];
                if(isset($elem['UTGST']))
                    $elem['UTGST'] = (float)$elem['UTGST'];
                if(isset($elem['TAX Total']))
                    $elem['TAX Total'] = (float)$elem['TAX Total'];
                return $elem;             
                },
                $details
            );
        }
        Excel::create('Return Tax Report    - '. date('Y-m-d'),function($excel) use($details) {
            $excel->sheet('Return Tax Report ', function($sheet) use($details) {          
            $sheet->fromArray($details);
            });      
        })->export('xls');
        }catch (\ErrorException $ex){
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getReturnHsnWiseReport(){
        $dcData=$this->_outwardSupply->warehouseData();      
           return view('GSTReports::returnhsnreport',['warehouse'=>$dcData]);
    }
    public function getReturnHsnWiseReportToExcel(Request $request){
        try{
        $fromDate = date('Y-m-d 00:00:00', strtotime($request->get('transac_date_from')));
        $toDate = date('Y-m-d 23:59:59', strtotime($request->get('transac_date_to')));
        $legal_id=$request->get('le_id');
        $details = json_decode(json_encode($this->_outwardSupply->generateReturnHSNWiseReport($fromDate,$toDate,$legal_id)), true);
            if (count($details)>0) {
                $details = array_map(function (array $elem) {
                if(isset($elem['Total Qty']))
                    $elem['Total Qty'] = (float)$elem['Total Qty'];
                if(isset($elem['With Tax']))
                    $elem['With Tax'] = (float)$elem['With Tax'];
                if(isset($elem['Without Tax']))
                    $elem['Without Tax'] = (float)$elem['Without Tax'];
                if(isset($elem['CGST']))
                    $elem['CGST'] = (float)$elem['CGST'];
                if(isset($elem['SGST']))
                    $elem['SGST'] = (float)$elem['SGST'];
                if(isset($elem['IGST']))
                    $elem['IGST'] = (float)$elem['IGST'];
                if(isset($elem['UTGST']))
                    $elem['UTGST'] = (float)$elem['UTGST'];
                return $elem;             
                },
                $details
            );
        }
        Excel::create('Return HSN Wise Report - '. date('Y-m-d'),function($excel) use($details) {
            $excel->sheet('Return HSN Wise Report ', function($sheet) use($details) {          
            $sheet->fromArray($details);
            });      
        })->export('xls');
        }catch (\ErrorException $ex){
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    public function getDeliveredHsnWiseReport(){
        $dcData=$this->_outwardSupply->warehouseData();      
           return view('GSTReports::deliveredhsn',['warehouse'=>$dcData]);
    }
    public function getDeliveredHsnWiseReportToExcel(Request $request){
        try{
        $fromDate = date('Y-m-d 00:00:00', strtotime($request->get('hsn_date_from')));
        $toDate = date('Y-m-d 23:59:59', strtotime($request->get('hsn_date_to')));
        $legal_id=$request->get('le_id');
        $details = json_decode(json_encode($this->_outwardSupply->generateDeliveredHSNWiseReport($fromDate,$toDate,$legal_id)), true);
            if (count($details)>0) {
                $details = array_map(function (array $elem) {
                    if(isset($elem['Total Qty']))
                        $elem['Total Qty'] = (float)$elem['Total Qty'];
                    if(isset($elem['With Tax']))
                        $elem['With Tax'] = (float)$elem['With Tax'];
                    if(isset($elem['Without Tax']))
                        $elem['Without Tax'] = (float)$elem['Without Tax'];
                    if(isset($elem['CGST']))
                        $elem['CGST'] = (float)$elem['CGST'];
                    if(isset($elem['SGST']))
                        $elem['SGST'] = (float)$elem['SGST'];
                    if(isset($elem['IGST']))
                        $elem['IGST'] = (float)$elem['IGST'];
                    if(isset($elem['UTGST']))
                        $elem['UTGST'] = (float)$elem['UTGST'];
                return $elem;             
                },
                $details
            );
        }
        Excel::create('Delivered HSN Wise Report  - '. date('Y-m-d'),function($excel) use($details) {
            $excel->sheet('Delivered HSN Wise Report ', function($sheet) use($details) {          
            $sheet->fromArray($details);
            });      
        })->export('xls');
        }catch (\ErrorException $ex){
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function index() {
      try {                       
            return view('GSTReports::finance.index1');
         }catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function exportFinanceReports(Request $request){
        $fromDate = date('Y-m-d 00:00:00', strtotime($request->get('from_date')));
        $toDate = date('Y-m-d 23:59:59', strtotime($request->get('to_date')));
        $buId=$request->get('business_unit_id');
        $report=$request->input('report_type');
        Excel::create('GST_Reports-'. date('Y-m-d'),function($excel) use($fromDate,$toDate,$buId,$report){
            if(in_array(1, $report)){
                $invoiceTax = json_decode(json_encode($this->_outwardSupply->invoiceTaxReport($fromDate,$toDate,$buId)), true);
                $excel->sheet('Invoice_Tax_Report ',function($sheet) use($invoiceTax) { 
                    $sheet->fromArray($invoiceTax);
                });  
            }
            if(in_array(2, $report)){
                $invoiceHsn = json_decode(json_encode($this->_outwardSupply->invoiceHsnWiseReport($fromDate,$toDate,$buId)), true);
                $excel->sheet('Invoice_HsnWise_Report', function($sheet) use($invoiceHsn) { 
                    $sheet->fromArray($invoiceHsn);
                });  
            }

            if(in_array(3, $report)){
                $returnTax = json_decode(json_encode($this->_outwardSupply->returnTaxReport($fromDate,$toDate,$buId)), true);
                $excel->sheet('ReturnTax_Report', function($sheet) use($returnTax) { 
                    $sheet->fromArray($returnTax);
                });  
            }
            if(in_array(4, $report)){
                $returnHSN = json_decode(json_encode($this->_outwardSupply->returnHSNWiseReport($fromDate,$toDate,$buId)), true);
                $excel->sheet('ReturnHSNWise_Report', function($sheet) use($returnHSN) { 
                    $sheet->fromArray($returnHSN);
                });  
            }
            if(in_array(5, $report)){
                $deliveredHSNWise = json_decode(json_encode($this->_outwardSupply->deliveredHSNWiseReport($fromDate,$toDate,$buId)), true);
                $excel->sheet('DeliveredHSNWise_Report',function($sheet) use($deliveredHSNWise) { 
                    $sheet->fromArray($deliveredHSNWise);
                });  
            }
        })->export('xls');
    }
    public function getBuUnit(){
        return $this->_inventory->businessTreeData();

    }

}

