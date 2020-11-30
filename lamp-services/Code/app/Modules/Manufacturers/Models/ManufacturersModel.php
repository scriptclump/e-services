<?php

namespace App\Modules\Manufacturers\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Modules\Manufacturers\Models\Legalentities;
use App\Modules\Manufacturers\Models\Manufacturers;
use App\Modules\Manufacturers\Models\Users;
use Mail;
use DB;

class ManufacturersModel extends Model
{
    public $timestamps = false;
    protected $fillable = ['supplier_id','legal_entity_id','erp_code','est_year','sup_add1','sup_add2','sup_country','sup_state','sup_city','sup_pincode','sup_account_name','sup_bank_name','sup_account_no','sup_account_type','sup_ifsc_code','sup_branch_name','sup_micr_code','sup_currency_code','sup_currency_code'];
	protected $table = "suppliers";
	protected $primaryKey = 'supplier_id';


public function createdBy($userId,$legalEntityId,$supplierId)
{
			$supplier = new ManufacturersModel();
			$legalEntity = new Legalentities();
	$current_time = Carbon::now()->toDateTimeString();
	    $status = $supplier->where ("supplier_id",$supplierId)
                      ->update(['updated_by'=>$userId,'updated_at'=>$current_time]);
        $Legalstatus = $legalEntity->where ("legal_entity_id",$legalEntityId)
                      ->update(['updated_by'=>$userId,'updated_at'=>$current_time]);
}

public function updatedBy($userId,$legalEntityId)
{
			$supplier = new ManufacturersModel();
			$legalEntity = new Legalentities();
	$current_time = Carbon::now()->toDateTimeString();
	   /* $status = $supplier->where ("supplier_id",$supplierId)
                      ->update(['updated_by'=>$userId,'updated_at'=>$current_time]);
     */   $Legalstatus = $legalEntity->where ("legal_entity_id",$legalEntityId)
                      ->update(['updated_by'=>$userId,'updated_at'=>$current_time]);
}

	public function approvedBy($userId,$legalEntityId,$supplierId)
	{
				$supplier = new ManufacturersModel();
				$legalEntity = new Legalentities();
		$current_time = Carbon::now()->toDateTimeString();
			$status = $supplier->where ("supplier_id",$supplierId)
						  ->update(['approved_by'=>$userId,'approved_at'=>$current_time]);
			$Legalstatus = $legalEntity->where ("legal_entity_id",$legalEntityId)
						  ->update(['approved_by'=>$userId,'approved_at'=>$current_time]);
	}

    public function sendEmailReminder($userId, $supplierId,$requestComments)
    {
	$supplier = new ManufacturersModel();
        $userModel = new Users();
        $legalEntities = new Legalentities();
        $srmUser = $supplier->select('sup_rm','legal_entity_id')->where("supplier_id",$supplierId)->get()->all();
        $srmUserId = $srmUser[0]->sup_rm;
        $legalId = $srmUser[0]->legal_entity_id;
        $supplierName = $legalEntities->select('business_legal_name')->where("legal_entity_id",$legalId)->get()->all();
                
        $fmName = $this->getUserNameById($userId);
        $fmEmail = "ravinder.majoju@ebutor.com";//$this->getUserEmailById($userId);       
        $srmName = $this->getUserNameById($srmUserId);        
        $srmEmail = "ravinder.majoju@ebutor.com";//$this->getUserEmailById($srmUserId);        
        $sender = "ravinder.majoju@ebutor.com";
        $name = "Ebutor";             
        /*
        Mail::send('emails.reminder', ['user' => $user], function ($m) use ($user) {
            $m->from('ravinder.majoju@ebutor.com', 'Ebutor');
            $m->to($user->email, $user->name)->subject('Your Reminder!');
        });
        */
       
        $copyTo = ['toName'=>$srmName,'toEmail'=>$srmEmail,'ccName'=>$fmName,'ccEmail'=>$fmEmail,'fromName'=>$name,'fromEmail'=>$sender,'suppliername'=>$supplierName[0]->business_legal_name];
         // Mail::send('emails.supplierrej',['srmname'=>$srmName,'suppliername'=>$supplierName[0]->business_legal_name,'comments'=>$requestComments], function ($message) use ($copyTo) {            
         //     $message->from($copyTo['fromEmail'], $copyTo['fromName']);
         //    $message->to($copyTo['toEmail'], $copyTo['toName']);
         //    $message->cc($copyTo['ccEmail'], $copyTo['ccName']);
         //    $message->subject('supplier "'.$copyTo['suppliername'].'" rejected');
         //    });
    }
    
    public function getUserNameById($id)
    {
        $userModel = new Users();
        $srmName = $userModel->select(DB::raw('CONCAT(firstname," ",lastname) as name'))
                   ->where("user_id",$id)->get()->all(); 
        if(isset($srmName[0]))
        {
            $name = $srmName[0]->name;
        }
        else
        {
            $name="";
        }
        return $name;
    }
    public function getUserEmailById($id)
    {
        $userModel = new Users();
        $srmName = $userModel->select('email_id')
                   ->where("user_id",$id)->where('is_active',1)->get()->all(); 
        if(isset($srmName[0]))
        {
            $emailId = $srmName[0]->email_id;
        }
        else
        {
            $emailId="";
        }
        return $emailId;        
    }
            
}
