<?php

/*
 * Filename: DmsEmailSetupController.php
 * Description: This file is used for manage product inventory
 * Author: Ebutor <info@ebutor.com>
 * Copyright: ebutor@2016
 * Version: v1.0
 * Created date: 25th October 2016
 * Modified date: 25th October 2016
 */

/*
 * InventoryController is used to manage product inventory
 * @author		Ebutor <info@ebutor.com>
 * @copyright	ebutor@2016
 * @package		Orders
 * @version: 	v1.0
 */

namespace App\Modules\MeanProducts\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Log;
use Excel;
use Carbon\Carbon;
use App\Modules\MeanProducts\Models\MeanProducts;
use Mail;
use File;

class DmsEmailSetupController extends BaseController {

    public function __construct() {
        try {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId')) {
                         Redirect::to('/login')->send();
                }
                $this->_meanproducts = new MeanProducts();
                return $next($request);
            });
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function dmsRepMail() {
        $final_data = $this->_meanproducts->dmsMailSetup();
        $time = Carbon::now();
        foreach ($final_data as $srmMail => $srmValues) {
            $fileName = "DMS-Report (".str_replace("@", "-", str_replace(".", "-", $srmMail)) . ")-" . $time->toDateTimeString();
            $filePath = $this->makeExcelFile(str_replace(":", "-", str_replace(" ", "-", $fileName)), $srmValues);
            $emailBody = "Hello " . ucwords(str_replace(".", " ", explode("@", $srmMail)[0])) . ", <br/><br/>";
            $emailBody .= "Please find atached DMS Report.<br/><br/>";
            $emailBody .= "*Note: This is an auto generated email !!";
            if (Mail::send('emails.dmsMail', ['emailBody' => $emailBody], function ($message) use ($srmMail, $filePath, $time) {
                        $message->to($srmMail);
                        $message->subject('DMS Report '.date('d-m-Y',strtotime($time->toDateTimeString())));
                        $message->attach($filePath);
                    })) {
                File::delete($filePath);
                echo "Mail sent to - ".$srmMail." !! Temp file deleted !!\n";
            } else {
                echo "Error in sending mail to ".$srmMail." !!\n";
            }
        }
    }

    public function makeExcelFile($fileName, $results) {
        $i = 0;
        Excel::create($fileName, function($excel) use($results, $i) {
            $excel->sheet("DMSReport", function($sheet) use($results, $i) {
                $sheet->fromArray($results);
                $totRows = count($results);
                for ($i = 2; $i <= $totRows + 1; $i++) {
                    $sheet->cell("S" . $i, function($cell) use($i) {
                        $cell->setValue('=P' . $i . '*Q' . $i);
                    });
                }
            });
        })->store('xls', public_path('download'));
        return public_path('download') . DIRECTORY_SEPARATOR . $fileName . ".xls";
    }
    
    public function dmsEmailSetup(Request $request) {
        return $this->_meanproducts->dmsEmailSetupInsert($request->all());
    }

}