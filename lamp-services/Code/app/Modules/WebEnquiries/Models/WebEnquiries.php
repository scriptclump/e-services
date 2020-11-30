<?php
namespace App\Modules\WebEnquiries\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Central\Repositories\RoleRepo;


class WebEnquiries extends Model
{
    protected $roleAccess;
    public function __construct(RoleRepo $roleAccess)
    {
        $this->roleAccess = $roleAccess;
    }

    public function updateWebEnquiry($data){
        $query = 'UPDATE web_enquiry
                  SET
                    name = ?,
                    type = ?,
                    address = ?,
                    phone = ?,
                    email = ?,
                    purpose = ?,
                    status = ?,
                    comments = ?
                WHERE
                    enquiry_no = ?';

        $result = DB::UPDATE($query,[
            $data['name'],
            $data['type'],
            $data['address'],
            $data['phone'],
            $data['email'],
            $data['purpose'],
            $data['status'],
            $data['comments'],
            $data['enquiry_no']
        ]);
        return true;
    }

    public function getSingleRecord($id){
        $query = '
            SELECT
                name,
                type,
                address,
                phone,
                email,
                purpose,
                status,
                comments
            FROM 
                web_enquiry
            WHERE 
                enquiry_no = ?';
        $result = DB::SELECT($query,[$id]);
        if(!empty($result))
            return $result;
        return NULL;
    }

    public function deleteWebEnquiry($id){
        $query = 'DELETE FROM web_enquiry WHERE enquiry_no = ?';
        $status = DB::DELETE($query,[$id]);
        if(!empty($status))
            return $status;
        return false;
    }

    public function getstatusInfo($ids = null){
        if($ids == null)
        return DB::table('master_lookup')
                ->select('description')
                ->where('mas_cat_id','=','175')
                ->get()->all();
    }  

    public function getWebEnquiriesList($makeFinalSql, $orderBy, $page, $pageSize){
        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        else{
            $orderBy = ' ORDER BY enquiry_no desc';
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
       $query = "SELECT * from web_enquiry". $sqlWhrCls . $orderBy ;

        $allRecallData = DB::select(DB::raw($query));
        $TotalRecordsCount = count($allRecallData);
        if($page!='' && $pageSize!=''){
            $page = $page=='0' ? 0 : (int)$page * (int)$pageSize;
            $allRecallData = array_slice($allRecallData, $page, $pageSize);
        }
        $arr = array('results'=>$allRecallData,
        'TotalRecordsCount'=>(int)($TotalRecordsCount)); 
        return $arr;        
    }
}








    