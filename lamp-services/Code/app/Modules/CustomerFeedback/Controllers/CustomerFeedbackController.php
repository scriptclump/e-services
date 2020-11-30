<?php

namespace App\Modules\CustomerFeedback\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Log;
use App\Modules\CustomerFeedback\Models\CustomerFeedback;
use App\Central\Repositories\RoleRepo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Excel;
Class CustomerFeedbackController extends BaseController
{

    public function indexAction()
    {
        try
        {
            if (!Session::has('userId'))
            {
                return redirect()->to('/');
            }

            $roleRepo = new RoleRepo();
            $userId = Session::get('userId');
            $approveAccess = $roleRepo->checkActionAccess($userId, 'CUFB001');
            $xlsAccess = $roleRepo->checkActionAccess($userId, 'CUFB003');
            if (!$approveAccess)
            {
                return redirect()->to('/');
            }
            parent::Title('Customer Feedback - Ebutor');
            $breadCrumbs = array('Dashboard' => url('/'), 'Customer Feedback' => '#');
            parent::Breadcrumbs($breadCrumbs);
            return View::make('CustomerFeedback::index',['xlsaccess'=>$xlsAccess]);
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getCustomerFeedback()
    {
        try
        {
            $CustomerFeedback = new CustomerFeedback();
            $reports = $CustomerFeedback->getCustomerFeedback();
            $i = 0;
            $finalReport = array();
            foreach ($reports as $report)
            {
                if ($reports[$i]->audio)
                {
                    $reports[$i]->audio = '<audio class="audiofile" controls><source src="'.$reports[$i]->audio.'" type="audio/ogg"></audio>';
                }
                if ($reports[$i]->picture)
                {
                    $reports[$i]->picture = '<a class="btn btn-default" href="'.$reports[$i]->picture.'" data-featherlight="image"><img width=20px height=20px src="'.$reports[$i]->picture.'" /></a>';
                }
                $roleRepo = new RoleRepo();
                $userId = Session::get('userId');
                $isDelete = $roleRepo->checkActionAccess($userId, 'CUFB002');
                if($isDelete)
                {
                    $reports[$i]->Actions = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u><a class="deletefeedback" href="' . $report->fid . '"><i class="fa fa-trash-o" aria-hidden="true"></i></a></u>';
                }
                else
                {
                    $reports[$i]->Actions = '&nbsp';
                }
                $i++;
            }
            $report_result = json_encode(array('Records' => $reports));
            return $report_result;
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function deleteFeedback(Request $request)
    {
        try
        {
         $fid = $request->get('fid');
         if($fid)
         {
             $data = CustomerFeedback::find($fid);             
             $data->delete(); 
         }
        }
        catch (\ErrorException $ex)
        {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function custFeedbackXls() {
        try {
	    $reportDate = Carbon::now(); 			
            $excel_reports = new CustomerFeedback();
            $report_excel = $excel_reports->excelReports();                        
            Excel::create('Customer_Feeback_'.$reportDate, function($excel) use($report_excel) {                        
                $excel->sheet('CustomerFeedback', function($sheet) use($report_excel) {                        
                        $sheet->loadView('CustomerFeedback::xls_reports')->with('Reportinfo', $report_excel);
                    });
        })->export('xls');        
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

}
