<?php

namespace App\Modules\DocumentsMaster\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

class DocumentMasterModel extends Model{

  protected $table = 'documents_master';
    protected $primaryKey = 'doc_master_id';
    public $timestamps = false;


    public function upLoadDocumentsToDB($ext,$url,$sessionId,$tag_name,$typeId,$doc_Name){
      $master_lookup_name = DB::table('master_lookup')->select('value')->where('master_lookup_id','=',$typeId)->first();
      $master_lookup_name= isset($master_lookup_name->value)? $master_lookup_name->value:'';
      $query = DB::table("doc_repository")->insertGetId(['media_type'=>$ext,'doc_url'=>$url,'doc_type'=>$master_lookup_name,'created_by'=>$sessionId,'doc_name'=>$doc_Name]);
      $doc_repo = DB::table("doc_repo_tags")->insert(['doc_repo_id'=>$query,'created_by'=>$sessionId,'tag_name'=>$tag_name]);

      if($query && $doc_repo){
      return 1;
      }else{
        return 0;
      }
    }

    public function getDocumentsFromDB($makeFinalSql, $orderBy, $page, $pageSize){
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
        $concatQuery='';
         $concatQueryedit = "CONCAT('<center><code>',
            '<a data-toggle=\"modal\" href=\"#update_docs\" onclick=\"showEditData(',ds.doc_repo_id,')\">
              <i class=\"fa fa-pencil\"></i>
              </a>&nbsp;&nbsp;&nbsp;
              <a href=\"javascript:void(0)\" onclick=\"deleteData(',ds.doc_repo_id,')\">
              <i class=\"fa fa-trash-o\"></i>
              </a>
            </code>
            </center>') 
            AS 'CustomAction', ";       
     $sqlQuery ="select ds.*,tg.*,ms.description as doc_type,".$concatQueryedit ." GetUserName(ds.created_by,1) as fullName from doc_repository as ds 
                left join doc_repo_tags as tg on tg.doc_repo_id = ds.doc_repo_id 
                JOIN master_lookup AS ms ON ms.value =ds.doc_type";
     if($sqlWhrCls!='')
         {
            if($sqlWhrCls != "")
              $sqlWhrCls = str_ireplace("doc_type", "ms.description", $sqlWhrCls);

              $sqlQuery.=$sqlWhrCls;
         }

         $sqlQuery.=" order by ds.doc_repo_id desc";
                 
         $pageLimit = '';
        if($page!='' && $pageSize!='')
        {
            $pageLimit = " LIMIT " . (int)($page*$pageSize) . ", " . $pageSize;
        }
        
        $result = DB::select(DB::raw($sqlQuery . $pageLimit));
        
    return $result;
    }
    public function deleteDocId($id){
      $repository = DB::table('doc_repository')->where('doc_repo_id',$id)->delete();
      $repo_tag = DB::table('doc_repo_tags')->where('doc_repo_id',$id)->delete();
      if ($repository && $repo_tag){
        return 1;
      }else{
        return 0;
      }
    }
    public function descriptionDrop(){
      $query = DB::table('master_lookup')->select(['master_lookup_id','master_lookup_name','value','description'])->where('mas_cat_id','=',176)->get()->all();
      return $query;
    }
    public function editDocumentData($id){
      $query = DB::table('doc_repository as drp')
            ->leftjoin('doc_repo_tags as tags','tags.doc_repo_id','=','drp.doc_repo_id')
            ->select(['drp.doc_name','tags.tag_name','drp.doc_repo_id'])
            ->where('drp.doc_repo_id','=',$id)
            ->first();
      return $query;      
    }
    public function upateDocumetsDetails($id,$doc_name,$tag_name,$sessionId){
      $tagTable = DB::table('doc_repo_tags')->select('*')->where('doc_repo_id','=',$id)->first();
      if(count($tagTable)==0){
        $doc_repo_tag = DB::table("doc_repo_tags")->insert(['doc_repo_id'=>$id,'created_by'=>$sessionId,'tag_name'=>$tag_name]);
      }
      $query = DB::table('doc_repository as drp')
            ->join('doc_repo_tags as tags','tags.doc_repo_id','=','drp.doc_repo_id')
            ->select(['drp.doc_name','tags.tag_name'])
            ->where('drp.doc_repo_id','=',$id)
            ->update(['drp.doc_name' =>$doc_name,'tags.tag_name'=>$tag_name]);    
      return $query;
    }
    
}
 
?>