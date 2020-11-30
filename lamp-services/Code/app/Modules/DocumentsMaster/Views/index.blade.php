@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">                
                <div class="caption">Documents Repository</div>
                <div class="actions">                    
                <a class="btn green-meadow" data-toggle="modal" href="#tag_2" onclick="emptyfields()">Upload File</a>
                <!-- <a href="#tag_2" data-toggle="modal" class="btn btn-success">Upload File</a> -->             
                </div>
            </div>                               
            <div class="portlet-body">
                <table id="getDocumentsUploaded"></table>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-scroll fade in" id="tag_2" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="modalpopupclose" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Documents Upload</h4>
            </div>
            <div class="modal-body">
                <form id="frmupload_doc" action="" class="text-center" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" id = "csrf-token" value="{{ Session::token() }}">

                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                     <label class="control-label">Document Name<span class="required">*</span></label> 
                            <input type="text" id="document_name_id" name="document_name_id" class="form-control" autocomplete="Off">                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                              <label class="control-label">Tag Name<span class="required">*</span></label>          
                            <input type="text" id="tag_name" name="tag_name" class="form-control" autocomplete="Off">                                        
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                  <label class="control-label">Document Type<span class="required">*</span></label>    
                                <select  name="doc_type" id="doc_type" class="form-control select2me"  autocomplete="Off">
                                    <option value="">Select Option</option>
                                 @foreach($types as $name)    
                                        <option value="{{$name->master_lookup_id}}">{{$name->description}}</option>
                                 @endforeach       

                                 </select>                                        
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3" style="margin-right: -28px">
                                <div class="form-group">

                                   <div class="input-icon right">
                                  <label class="control-label">&nbsp;</label>    
                            <!-- <input class="form-control" type="file" id="docs_upload" name="docs_upload" placeholder=""> -->
                                <span class="btn default btn-file btn green-meadow" >
           
                  <span class="fileinput-exists" style="margin-top:-9px !important;">Choose File</span>
                  <input class="form-control" type="file" id="docs_upload" name="docs_upload" placeholder="">
                  
                  </span>
                                </div>
                            </div>
                                <span id="filename"></span>
                            </div>
<!--                              <div class="col-md-3"></div>
                             <div class="clearfix"></div>
                             <div class="col-md-4"></div>
                            <div class="col-md-4"> -->
                                <div class="form-group">
                            <input class="btn btn-success" style="margin-top: 25px" type="button" onclick="uplodadocuments();" name="btnUpload" value="Add">
                                </div>
                            </div>                                    
                        </div>
                            <div class="clearfix"></div>
                     <div class="row">
                        <div class="col-md-12 text-center">
                            
                        </div>
                    </div>
                        </div>
                      </div>
                    </div>
                   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>





<div class="modal modal-scroll fade in" id="update_docs" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="modaldoctype" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Documents</h4>
            </div>
            <div class="modal-body">
                <form id="edit_doc_type_id" action="" class="text-center" method="POST" >
                <input type="hidden" name="_token" id = "csrf-token" value="{{ Session::token() }}">

                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                  <label class="control-label">Document Name<span class="required">*</span></label>       
                            <input type="text" id="edit_document_name" name="edit_document_name" class="form-control" autocomplete="Off">
                            <input type="hidden" id="hidden_id_repo" name="hidden_id_repo">                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                         <label class="control-label">Tag Name<span class="required">*</span></label>
                            <input type="text" id="edit_tag_name_field" name="edit_tag_name_field" class="form-control" autocomplete="Off">                                        
                                    </div>
                                </div>
                            </div> 
                            
                            <div class=clearfix></div>
                           <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="form-group">
                            <input class="btn green-meadow" type="button" onclick="editDocuments_type();" name="btnUpload" value="Update">
                                </div>
                            </div>                                    
                        </div>
                            <div class="clearfix"></div>
                     <div class="row">
                        <div class="col-md-12 text-center">
                            
                        </div>
                    </div>
                        </div>
                      </div>
                    </div>
                   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

{{HTML::style('css/switch-custom.css')}}
@stop

@section('style')
<style type="text/css">
<style>
* {box-sizing: border-box;}

body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
}

.topnav {
  overflow: hidden;
  background-color: #e9e9e9;
}

.topnav a {
  float: left;
  display: block;
  color: black;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
}

.topnav a:hover {
  background-color: #ddd;
  color: black;
}

.topnav a.active {
  background-color: #2196F3;
  color: white;
}

.topnav .search-container {
  float: right;
}

.topnav input[type=text] {
  padding: 6px;
  margin-top: 8px;
  font-size: 17px;
  border: none;
}

.topnav .search-container button {
  float: right;
  padding: 6px 10px;
  margin-top: 8px;
  margin-right: 16px;
  background: #ddd;
  font-size: 17px;
  border: none;
  cursor: pointer;
}

.topnav .search-container button:hover {
  background: #ccc;
}

@media screen and (max-width: 600px) {
  .topnav .search-container {
    float: none;
  }
  .topnav a, .topnav input[type=text], .topnav .search-container button {
    float: none;
    display: block;
    text-align: left;
    width: 100%;
    margin: 0;
    padding: 14px;
  }
  .topnav input[type=text] {
    border: 1px solid #ccc;  
  }
}


code {
     border: none !important; 
     -webkit-box-shadow: none !important; 
    -moz-box-shadow: none !important;
     box-shadow: none !important; 
}

</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@stop

@section('userscript')
@section('script') 
@include('includes.ignite')
@include('includes.validators') 
@include('includes.jqx')
@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
        
<script>

$(document).ready(function ()
    {
        $(function () {
            
                    $('#getDocumentsUploaded').igGrid({
                      
                        dataSource: '/documents/getdocuments',
                        initialDataBindDepth: 0,
                        autoGenerateColumns: false,
                        mergeUnboundColumns: false,
                        generateCompactJSONResponse: false,
                        responseDataKey: "results", 
                        enableUTCDates: true, 
                        width: "100%",
                         height: "100%",
                         columns: [
                            // {headerText: "Document", template: "<img width = '30' height = '30' src = ${doc_url}></img>" , key: 'doc_url', dataType: 'string', width: '20%'},
                            {headerText: "Document Name", template: "<a href=${doc_url} target=_blank>${doc_name}</a>" ,key: 'doc_name', dataType: 'string', width: '25%'},
                            {headerText: "Media Type", key: 'media_type', dataType: 'string', width: '15%'},
                            {headerText: "Document Type", key: 'doc_type', dataType: 'string', width: '25%'},
                            {headerText: "Tags", key: 'tag_name', dataType: 'string', width: '20%'},
                            {headerText: "Date", key: 'created_at', format: "dd/MM/yyyy" ,dataType: 'string', width: '20%'},
                            {headerText: "File", key: 'doc_url', dataType: 'string', width: '35%'},
                            {headerText: "Created By", key: 'fullName', dataType: 'string', width: '20%'},
                            {headerText: "{{trans('banners.grid.grid_action')}}", key: 'CustomAction', dataType: 'string', width: '15%'},
                            
                            
                        ],
                        features: [
                        {
                            name:'Filtering',
                            type: "remote",
                            mode: "simple",
                            allowFiltering: true,
                            filterDialogContainment: "window",
                            columnSettings: [
                             {columnKey: 'doc_name', allowFiltering: true },
                             {columnKey: 'fullName', allowFiltering: true },
                             {columnKey: 'doc_url', allowFiltering: true },
                             {columnKey: 'tag_name', allowFiltering: true },
                            {columnKey: 'created_at', allowFiltering: true },
                            {columnKey: 'CustomActio', allowFiltering: false },
                                                         
                    ]
                    },
                    { 
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 20,
                    name: 'Paging',
                    loadTrigger: 'auto',
                    type: 'local'
                     }
                            ]
                    });

        });
    });


  function uplodadocuments(){
        var form = document.getElementById("frmupload_doc");
        var doc = $("#docs_upload").val();
        var tagname = $("#tag_name").val();
        var doc_type = $("#doc_type").val();
        var document_name_id = $("#document_name_id").val();
        if(doc==''){
            alert('Please Choose The File');
            return false;
        }
        if(tagname==''){
            alert('Enter Tag Name');
            return false;
        }
        if(doc_type==''){
            alert('Please Select Any Option');
            return false;
        }
       if(document_name_id==''){
            alert('Please Document Name');
            return false;
        }

        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/documents/uploadDoc",
            type: "POST",
            data: new FormData(form),
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function (xhr) {
                $('input[name="btnUpload"]').attr('disabled', true);
                $('.loderholder').show();

            },
            success: function (response) {             
                $('input[name="btnUpload"]').attr('disabled', false);
                $( "#modalpopupclose" ).trigger( "click" );
                if (response==1 || response==2){
                    alert("Successfully Created");
                }

                //$('#supplier_doc_table>tbody').append(response);
                $("#getDocumentsUploaded").igGrid({dataSource: '/documents/getdocuments'});
                 $("#getDocumentsUploaded").igGrid("dataBind");
            },
            error: function (response) {
                $( "#modalpopupclose" ).trigger( "click" );
                $('#ajaxResponseDoc').removeClass('alert-success').addClass('alert-danger').html("Unable to save file").show();
                $('.loderholder').hide();
            }
        });
    } 

    function deleteData(did){

      token  = $("#csrf-token").val();
        var documents = confirm("Are you sure you want to delete ?"), self = $(this);
            if ( documents == true )
            {
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                /*data: 'deleteData='+did,*/
                type: "POST",
                url: '/documents/deleteDocumentId/'+did,
                success: function( data ) {
                if (data==1 || data==2){
                    alert("Deleted Successfully");
                }  
                $("#getDocumentsUploaded").igGrid({dataSource: '/documents/getdocuments'});
                 $("#getDocumentsUploaded").igGrid("dataBind");
              
                        
                    }
            });  
        }

   } 


    function  showEditData(id){
      token  = $("#csrf-token").val();
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "POST",
            url: '/documents/editDocument/'+id,
            success: function(data){
                var doc_name=data.doc_name;
                var tag_name_field = data.tag_name
                var doc_repo_id =data.doc_repo_id;
          $('#edit_document_name').val(doc_name);
          $('#edit_tag_name_field').val(tag_name_field);
          $('#hidden_id_repo').val(doc_repo_id);                  
            }
        });  
   } 


    $("#docs_upload").change(function() {
        var abc=$('#docs_upload').val().replace(/C:\\fakepath\\/i, '')
        $('#filename').text(abc);
    });

        $("#modalpopupclose").on('click', function() {

        document.getElementById("frmupload_doc").reset();
        $('#filename').text("");

        
    });



    function editDocuments_type(){
        var form = document.getElementById("edit_doc_type_id");
        var file_name = $("#edit_document_name").val();
        var name_tag = $("#edit_tag_name_field").val();
        if(file_name==''){
            alert('File Name Should Not Be Empty');
            return false;
        }
        if(name_tag==''){
            alert('Tag Name Should Not Be Empty');
            return false;
        }

        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/documents/updateDocsData",
            type: "POST",
            data: new FormData(form),
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function (xhr) {
                $('input[name="btnUpload"]').attr('disabled', true);
                $('.loderholder').show();

            },
            success: function (response) {
                $('input[name="btnUpload"]').attr('disabled', false);
                $( "#modaldoctype" ).trigger( "click" );
                if (response==0 || response==1){
                    alert("Updated Successfully");
                }

                //$('#supplier_doc_table>tbody').append(response);
                $("#getDocumentsUploaded").igGrid({dataSource: '/documents/getdocuments'});
                 $("#getDocumentsUploaded").igGrid("dataBind");
            },
            error: function (response) {
                $( "#modaldoctype" ).trigger( "click" );
                $('#ajaxResponseDoc').removeClass('alert-success').addClass('alert-danger').html("Unable to save file").show();
                $('.loderholder').hide();
            }
        });
    } 
   
   function emptyfields(){
    $('#doc_type').select2("val",'');
    $('#document_name_id').val('');
    $('#tag_name').val('');
   }

</script>



@stop

@extends('layouts.footer')