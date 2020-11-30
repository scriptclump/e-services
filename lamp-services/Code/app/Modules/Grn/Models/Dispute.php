<?php

namespace App\Modules\Grn\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;

class Dispute extends Model {

	
	public function saveDocument($docsArr) {
		try {
			$id = DB::table('inward_docs')->insertGetId($docsArr);
            return $id;
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
	}

	
	public function deleteDocuments($id) {
		try {
			DB::table('inward_docs')->where('inward_doc_id', '=', $id)->delete();
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
	}

	public function getDocuments($inwardId) {
		try {
			$fieldArr = array('inward_docs.*');
			
			$query = DB::table('inward_docs')->select('inward_docs.*',DB::raw("getMastLookupValue(inward_docs.doc_ref_type) as doc_type"),DB::raw("GetUserName(inward_docs.created_by,2) as fullname"));
            //$query->join('users', 'users.user_id', '=', 'inward_docs.created_by');
			$query->where('inward_docs.inward_id', $inwardId);
			return $query->get()->all();
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
	}
	public function getDocumentTypes() {
		try {
			$fields = array('lookup.value','lookup.master_lookup_name');
                        $query = DB::table('master_lookup as lookup')->select($fields);
                        $query->where('lookup.mas_cat_id',95);
                        return $query->pluck('lookup.master_lookup_name','lookup.value')->all();
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
	}

	public function getDocumentById($id) {
		try {
			$fieldArr = array('inward_docs.*');
			
			$query = DB::table('inward_docs')->select($fieldArr);			
			$query->where('inward_docs.inward_doc_id', $id);
			return $query->first();
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
	}
	public function inwardDocUpdate($inward_id,$docid) {
		try {
			$query = DB::table('inward_docs');			
			$query->where('inward_doc_id',$docid);
			$query->update(array('inward_id'=>$inward_id));
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
	}


	public function getCommentsTransactionId($transactionId, $count=0, $offset=0, $perpage=10){
        try{
            
           
			$fieldArr = array('history.comments', 'history.status', 'history.created_at', 'users.firstname', 'users.lastname', 'users.profile_picture', 'roles.name as roleName');
			
			$query = DB::table('disputes')->select($fieldArr);
			$query->join('dispute_history as history', 'disputes.dispute_id', '=', 'history.dispute_id');	
			$query->join('users', 'users.user_id', '=', 'history.created_by');
			$query->join('user_roles', 'user_roles.user_id', '=', 'users.user_id');
			$query->join('roles', 'roles.role_id', '=', 'user_roles.role_id');
			$query->where('disputes.transaction_id', $transactionId);
			if($count) {
				return $query->count();	
			}
			$query->groupBy('history.dispute_history_id');
			$query->orderBy('history.dispute_history_id', 'desc');
			$query->skip($offset)->take($perpage);
			//echo $query->toSql();die;
			return $query->get()->all();

        } catch (Exception $ex) {

        }        
    }

	public function saveDispute($disputeArr) {
		try {
			
            $id = DB::table('disputes')->insertGetId($disputeArr);
            return $id;
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
	}

	public function saveHistrory($disputeHistoryArr) {
		try {
			$id = DB::table('dispute_history')->insertGetId($disputeHistoryArr);

            return $id;
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
	}

    public function getDisputIdByTransactionId($transactionId) {
            try{
                    $fieldArr = array('disputes.dispute_id');

                    $query = DB::table('disputes')->select($fieldArr);			
                    $query->where('disputes.transaction_id', $transactionId);
                    return $query->first();
            }
            catch(Exception $e) {

            }		
    }
    public function getLoginUserInfo() {
        try{
            $userId = Session::get('userId');//Session('userId'),
            $fieldArr = array('users.*');
            $query = DB::table('users')->select($fieldArr);
            $query->where('users.user_id', $userId);
            $userdata = $query->first();
            return $userdata;
        }
        catch(Exception $e) {

        }
    }
}
?>
