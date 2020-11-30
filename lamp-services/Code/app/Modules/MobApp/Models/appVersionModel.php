<?php
/*
FileName :appVersionModel.php
Author   :eButor
Description : All the outbound order related functions are here.
CreatedDate :7/jul/2016
*/
//defining namespace
namespace App\Modules\MobApp\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class appVersionModel extends model
{
    
	protected $table = 'app_version_info';
	protected $primaryKey = 'version_id';
     
	public function viewAppVersiondata($makeFinalSql, $orderBy, $page, $pageSize){

		if($orderBy!=''){
			$orderBy = ' ORDER BY ' . $orderBy;
		}

		$sqlWhrCls = '';
		$countLoop = 0;
		foreach ($makeFinalSql as $value) {
			if( $countLoop==0 ){
				$sqlWhrCls .= ' WHERE ' . $value;
			}elseif( count($makeFinalSql)==$countLoop ){
				$sqlWhrCls .= $value;
			}else{
				$sqlWhrCls .= ' AND ' .$value;
			}
			$countLoop++;
		}

        $sqlQuery ="select *, 
        CONCAT('<center>
        <code>
        <a href=\"javascript:void(0)\" onclick=\"updateVersion(',version_id,')\" data-toggle=\"tooltip\" title=\"Edit\">
        <i class=\"fa fa-pencil codepadright\"></i>
        </a>
        <a href=\"javascript:void(0)\" onclick=\"deleteVersion(',version_id,')\" data-toggle=\"tooltip\" title=\"Delete\">
        <i class=\"fa fa-trash-o\"></i>
        </a>
        </code>
        </center>') 
        AS 'CustomAction',
        @rowcnt:=@rowcnt+1 AS 'slno' FROM app_version_info, (SELECT @rowcnt:= 0) AS rowcnt " . $sqlWhrCls . $orderBy;

        $allRecallData = DB::select(DB::raw($sqlQuery));
        $TotalRecordsCount = count($allRecallData);

        // prepare for limit
		if($page!='' && $pageSize!=''){
			$page = $page=='0' ? 0 : (int)$page * (int)$pageSize;
			$allRecallData = array_slice($allRecallData, $page, $pageSize);

		}
	    return json_encode(array('results'=>$allRecallData, 'TotalRecordsCount'=>(int)($TotalRecordsCount)));	
	}

	public function saveVersionData($AppversionData){

		$this->version_name = $AppversionData['version_name'];
        $this->version_number = $AppversionData['version_number'];
        $this->app_type = $AppversionData['app_type'];
        if ($this->save()) {
        	return true;
        }else{
        	return false;
        }
    }
    public function getUpdateData($updateId){

        $getUpdateData = DB::table('app_version_info')
                ->where("app_version_info.version_id",$updateId)
                ->first();
        return $getUpdateData;
                                            }
    public function updateVersionData($data){
     $update_data = appVersionModel::find($data['version_id']);
     $update_data->version_name = $data['version_name'];
     $update_data->version_number = $data['version_number'];
     $update_data->app_type = $data['app_type'];

     if ($update_data->save()){

      return true;
       }else{
      return false;
       }
  }
    public function deleteVersion($versionID){
    	$appVersion = appVersionModel::find($versionID);
    	if( $appVersion->delete() ){
    		return "Record Deleted";
    	}else{
    		return "Can not delete the record, due to some error ..";
    	}
    }

}