<?php
namespace App\Modules\GSTReports\Models;
use Session;
use DB;
use Log;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Roles\Models\Role;

class OutwardSupplyReport extends Model
{
    
    protected $role;
    public function __construct()
    {
        $this->role = new Role();
    }
    public function invoiceDetails($from, $to)
    {
        try {
            $finalResult = json_decode(json_encode(DB::selectFromWriteConnection(DB::raw("CALL getOutwardSupplyReport('" . $from . "', '" . $to . "')"))), true);
            return $finalResult;
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getUserEmail($userId)
    {
        try {
            $userEmail = json_decode(json_encode(DB::table("users")->where('user_id', '=', $userId)->pluck('email_id')->all()), true);
            return $userEmail[0];
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function hsnCodeDetails($from, $to)
    {
        try {
            $finalResult = json_decode(json_encode(DB::selectFromWriteConnection(DB::raw("CALL getHSNOutwardSupplyReport('" . $from . "', '" . $to . "')"))), true);
            return $finalResult;
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function generateInvoiceTaxReport($fromDate, $toDate, $legalEntityId, $wh_id)
    {
        $query = DB::selectFromWriteConnection(DB::raw("CALL getTaxInvoiceReport('" . $fromDate . "','" . $toDate . "','" . $legalEntityId . "','" . $wh_id . "')"));
        
        return $query;
    }
    
    public function generateInvoiceHsnWiseReport($fromDate, $toDate, $legal_id)
    {
        $query = DB::selectFromWriteConnection(DB::raw("CALL getHSNInvoiceReport('" . $fromDate . "','" . $toDate . "','" . $legal_id . "')"));
        return $query;
    }
    
    public function generateReturnTaxReport($fromDate, $toDate, $legal_id)
    {
        $query = DB::selectFromWriteConnection(DB::raw("CALL getTaxReturnReport('" . $fromDate . "','" . $toDate . "','" . $legal_id . "')"));
        return $query;
    }
    public function generateReturnHSNWiseReport($fromDate, $toDate, $legalid)
    {
        $query = DB::selectFromWriteConnection(DB::raw("CALL getHSNReturnReport('" . $fromDate . "','" . $toDate . "','" . $legalid . "')"));
        return $query;
    }
    public function generateDeliveredHSNWiseReport($fromDate, $toDate, $legalid)
    {
        $query = DB::selectFromWriteConnection(DB::raw("CALL getHSNDeliveredReport('" . $fromDate . "','" . $toDate . "','" . $legalid . "')"));
        return $query;
    }
    public function warehouseData()
    {
        $id     = Session::get('userId');
        $data   = $this->role->getWarehouseData($id, 6);
        $dcList = json_decode($data, true);
        $dcData = array();
        if (isset($dcList['118001'])) {
            $dcList = explode(',', $dcList['118001']);
            $dcData = DB::table('legalentity_warehouses AS lw')->leftjoin('legal_entities AS l', 'l.legal_entity_id', '=', 'lw.legal_entity_id')->select(DB::raw('CONCAT(lw.display_name, "-", IFNULL(lw.tin_number,\' \')) AS display_name'), 'lw.le_wh_id')
            // ->whereIn('l.legal_entity_type_id',[1016,1014])
                ->where('lw.status', 1)->whereIn('lw.le_wh_id', $dcList)->get()->all();
        }
        return $dcData;
    }
      
    public function invoiceTaxReport($fromDate, $toDate, $buId)
    {
        $query = DB::selectFromWriteConnection(DB::raw("CALL getTaxInvoiceReport_bu('" . $fromDate . "','" . $toDate . "','" . $buId . "')"));
        
        return $query;
    }
    
    public function invoiceHsnWiseReport($fromDate, $toDate, $businessUnit_id)
    {
        $query = DB::selectFromWriteConnection(DB::raw("CALL getHSNInvoiceReport_bu('" . $fromDate . "','" . $toDate . "','" . $businessUnit_id . "')"));
        return $query;
    }
    
    public function returnTaxReport($fromDate, $toDate, $bu_unit_id)
    {
        $query = DB::selectFromWriteConnection(DB::raw("CALL getTaxReturnReport_bu('" . $fromDate . "','" . $toDate . "','" . $bu_unit_id . "')"));
        return $query;
    }
    public function returnHSNWiseReport($fromDate, $toDate, $business_id)
    {
        $query = DB::selectFromWriteConnection(DB::raw("CALL getHSNReturnReport_bu('" . $fromDate . "','" . $toDate . "','" . $business_id . "')"));
        return $query;
    }
    public function deliveredHSNWiseReport($fromDate, $toDate, $bussiness_id_unit)
    {
        $query = DB::selectFromWriteConnection(DB::raw("CALL getHSNDeliveredReport_bu('" . $fromDate . "','" . $toDate . "','" . $bussiness_id_unit . "')"));
        return $query;
    }
    
}
