<?php

namespace App\Modules\DocumentsMaster\Controllers;

use App\Http\Controllers\BaseController;
use Session;
use View;
use Validator;
use Illuminate\Support\Facades\Input;
use Log;
use Redirect;
use \App\Modules\DocumentsMaster\Models\DocumentMasterModel;
use App\Modules\ApprovalEngine\Controllers\commonIgridController;
use \App\Modules\Users\Models\Users;
use DB;
use Route;
use Illuminate\Http\Request;
use App\Central\Repositories\ProductRepo;
class DocumentMasterController extends BaseController
{
    
    public function __construct() {
        $this->_productRepo = new ProductRepo();
        $this->_documentMaster = new DocumentMasterModel();
        $this->objCommonGrid = new commonIgridController();
    }

    public function getMasterDocs($master_type)
    {	
    	try {

			$documents = DocumentMasterModel::where('master_type',$master_type)->get()->all();
			//echo "<pre>"; print_r($documents); die();
			$data = [];
			$doc_data = [];
			$doc_data['doc_id'] = 1;
			$doc_data['doc_name'] = "Pan.pdf";
			$doc_data['doc_url'] = "/xyz/asasa";
			$doc_data['ref_value'] = "123455";
			$data[0] = $doc_data;
			$doc_data = [];
			$doc_data['doc_id'] = 2;
			$doc_data['doc_name'] = "tin.pdf";
			$doc_data['doc_url'] = "/xyz/asasa";
			$doc_data['ref_value'] = "22222";
			$data[1] = $doc_data;
			foreach ($data as $key => $doc) {
				# code...
				foreach ($documents as $x => $value) {
					# code...
					if($doc['doc_id'] == $value['doc_master_id']){
						$value['document_name'] = $doc['doc_name'];
						$value['doc_url'] = $doc['doc_url'];
						$value['ref_value'] = $doc['ref_value'];
					}
				}
				
			}
			return view('DocumentsMaster::documentMaster')->with(['documents'=>$documents,'data' => $data]);
    	} catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

   public function index(){
    $breadCrumbs = array('Home' => url('/'),'Reports' => '#', 'Documents' => '#');
    $types = $this->_documentMaster->descriptionDrop();
     parent::Breadcrumbs($breadCrumbs);
   	 parent::Title('Documents - Ebutor');
   	return view('DocumentsMaster::index')->with(['types'=>$types]);
   }

    public function uploadDoc(Request $request){
      $sessionId = Session::get('userId');
      ini_set('max_execution_time', -1);
      ini_set('upload_max_filesize', "200M");
      ini_set('post_max_size', "200M");

    	$data = $request->all();  	
      $url = "";
      $typeId = $data['doc_type'];
      $doc_Name = $data['document_name_id'];
    	$file_upload =$request->file('docs_upload');
      $tag_name =$data['tag_name'];
      $folder = env('S3_EBUTOR_DOCUMENTS');
      $type=1;
      $ext =  $file_upload->getClientOriginalExtension();
    if(is_object($file_upload)){
             $url=$this->_productRepo->uploadToS3($file_upload,$folder,$type);
        }
      return $uploaddocs = $this->_documentMaster->upLoadDocumentsToDB($ext,$url,$sessionId,$tag_name,$typeId,$doc_Name);

    }
   public function getUploadedDoc(Request $request){

        $makeFinalSql = array();
        $filter = $request->input('$filter');
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("doc_name", $filter, false);
        
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("doc_url", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("tag_name", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("doc_type", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }
        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("media_type", $filter, false);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("fullName", $filter, false);
        $fieldQuery =str_replace('fullName', 'GetUserName(ds.created_by,1)', $fieldQuery);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $fieldQuery = $this->objCommonGrid->makeIGridToSQL("created_at", $filter, false);
        $fieldQuery =str_replace('created_at', 'ds.created_at', $fieldQuery);
        if($fieldQuery!=''){
            $makeFinalSql[] = $fieldQuery;
        }

        $orderBy = $request->input('%24orderby');
        if($orderBy==''){
            $orderBy = $request->input('$orderby');
        }


        $page="";
        $pageSize="";
        if( ($request->input('page') || $request->input('page')==0)  && $request->input('pageSize') ){
            $page = $request->input('page');
            $pageSize = $request->input('pageSize');
        }
          $content = $this->_documentMaster->getDocumentsFromDB($makeFinalSql, $orderBy, $page, $pageSize);
            
        return $content;
   }
   public function deleteDocumentId($id){
     return $data = $this->_documentMaster->deleteDocId($id);
   }
   public function editDocument($id){
    $editData = $this->_documentMaster->editDocumentData($id);
    $editData=json_decode(json_encode($editData),true);
    return $editData;
   } 
   public function updateDocsData(Request $request){
    $sessionId = Session::get('userId');
    $data = $request->all();
    $id = isset($data['hidden_id_repo'])?$data['hidden_id_repo']:'';
    $doc_name = isset($data['edit_document_name'])?$data['edit_document_name']:'';
    $tag_name =isset($data['edit_tag_name_field'])?$data['edit_tag_name_field']:'';
    $update = $this->_documentMaster->upateDocumetsDetails($id,$doc_name,$tag_name,$sessionId);
    return $update;
   }
}
?>