@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="portlet light tasks-widget">
      <div class="portlet-title">
        <div class="caption"> Documents </div>
        <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question"></i></a></span>
          <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        </div>
      </div>
      <div class="portlet-body">
      	<form id="documentsMaster">
      		@foreach($documents as $document)	
      			<div class="row PAN">
                  <div class="col-md-4">
                    <div class="form-group">
                    	@if($document['is_ref_no_required'] == 1)
                        <label class="control-label">@if(isset($document['reference_label'])) {{$document['reference_label']}} @endif
                        @if(isset($document['mandatory_refno']) && $document['mandatory_refno'] ==1)
                        <span class="required PAN_I" aria-required="true">*</span>
                        @endif
                        </label>
                      <input type="text" class="form-control @if(isset($document['mandatory_refno']) && $document['mandatory_refno'] ==1) refmandate @else refnotmandate @endif" id="@if(isset($document['reference_no'])){{$document['reference_no']}}@endif" name="@if(isset($document['reference_no'])){{$document['reference_no']}}@endif" value="@if(isset($document['ref_value'])) {{$document['ref_value']}} @endif">
                      @endif
                    </div>
                  </div>
                  @if(isset($document['is_doc_required']) && $document['is_doc_required'] ==1)
                  <div class="col-md-4">
                    <div class="form-group">
                    <label class="control-label">@if(isset($document['doc_label'])) {{$document['doc_label']}} @endif 
                    @if(isset($document['mandatory_doc']) && $document['mandatory_doc'] ==1)
                    <span class="required PAN_F" aria-required="true">*</span>
                    @endif
                    </label>

                      <div class="row">
                       <div class="col-md-12">  
						<div class="fileinput fileinput-new" data-provides="fileinput">
						<span class="btn default btn-file btn green-meadow" style="width:110px !important;">	
                           <span class="fileinput-new">Choose File </span>
                              <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>
                              </span>
                              <?php
                              	$bp = url('uploads/Suppliers_Docs');
                    			$base_path = $bp."/";
                                $extn = '';
                                if(isset($document['doc_name']))
                                {    
                                $ext1 = strrchr($document['doc_name'],".");
                                //
                                $ext1 = explode(".", $ext1);
                                if(isset($ext1[1]))
                                {
                                $extn = $ext1[1];
                                }
                                }
                              ?>
                              <div class="fileinput-preview fileinput-exists thumbnail" style="height: 33px; margin-left:9px; margin-top:10px; z-index:99; position: relative; display: -webkit-inline-box;">

                              	@if($extn == 'png' || $extn == 'jpg' || $extn == 'jpeg')<a href="@if(isset($document['doc_url'])){{$document['doc_url']}}@endif" target="blank"> <img src="@if(isset($document['doc_url'])){{$document['doc_url']}}@endif" alt="" class="tinvat_files_id"/></a>
	                             @elseif($extn == 'doc' || $extn == 'docx')
	                             <a target="_blank" class="tinvat_files_id" href="@if(isset($document['doc_url'])){{$document['doc_url']}}@endif"><img src="{{$base_path."word.jpg"}}" style="width: 40px; height: 33px;"  /></a>
	                              @elseif($extn == 'pdf')
	                             <a target="_blank" class="tinvat_files_id" href="@if(isset($document['doc_url'])){{$document['doc_url']}}@endif"><img src="{{$base_path."pdf.jpg"}}" style="width: 40px; height: 33px;" /></a>
	                             @endif
							</div>
                              <input type="hidden" name="doc_id" value="@if(isset($document['doc_master_id'])){{$document['doc_master_id']}}@endif">
                              <br />
                              <input id="@if(isset($document['doc_name'])){{$document['doc_name']}}@endif" type="file" class="@if(isset($document['mandatory_doc']) && $document['mandatory_doc'] ==1)uploadmandate @else uploadnotmandate @endif" name="@if(isset($document['doc_name'])){{$document['doc_name']}}@endif" style="margin-top: -27px !important;  position: absolute;opacity: 0;"/>
			     
                          <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">@if(isset($document['document_name'])){{$document['document_name']}}@else&nbsp;@endif <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
					    </div>
                    </div>

                  </div>
                </div>
              </div>
            @endif
           </div>
      	@endforeach
      </form>
      	<div class="row">
           <div class="col-md-12 text-center">
              <input type="button" name="" id="saveDocMaster" class="btn green-meadow" value="Save">

           </div>
        </div>
      </div>
    </div>
  </div>
 </div>
@stop
@section('style')
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
	.thumbnail img {display:run-in!important; max-height: 100% !important;}
.thumbnail{padding: 0px !important; border: 0px !important;}
</style>
@stop
@section('script')      
@include('includes.validators')
@include('includes.jqx')
{{HTML::script('js/helper.js')}}
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
    var inputFieldValidators = {
            validators: {
                notEmpty: {
                    message:  ' '
                }
            }
        },
        fileValidators = {
            validators: {
                notEmpty: {
                    message: ' '
                },
             file: {
                  extension: 'doc,docx,pdf,jpeg,jpg,png',
                  type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
                  maxSize: 2*1024*1024,   // 5 MB
                  message: 'The selected file is not valid, it should be (doc,docx,pdf,jpeg,png,jpg) and 2 MB at maximum.'
                },
            }
        };
    $('#documentsMaster')
        .formValidation({
            framework: 'bootstrap',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                uploadmandate:{ 
                	selector: '.uploadmandate',
                	validators: {
                notEmpty: {
                    message: ' '
                },
             file: {
                  extension: 'doc,docx,pdf,jpeg,jpg,png',
                  type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
                  maxSize: 2*1024*1024,   // 5 MB
                  message: 'The selected file is not valid, it should be (doc,docx,pdf,jpeg,png,jpg) and 2 MB at maximum.'
                },
            	}
            },
            uploadnotmandate:{
            	selector: '.uploadnotmandate',
                	validators: {
                		file: {
		                  extension: 'doc,docx,pdf,jpeg,jpg,png',
		                  type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
		                  maxSize: 2*1024*1024,   // 5 MB
		                  message: 'The selected file is not valid, it should be (doc,docx,pdf,jpeg,png,jpg) and 2 MB at maximum.'
                	},
                }
            },
            refmandate: { 
                	selector: '.refmandate',
                	validators: {
                notEmpty: {
                    message: ' '
                	},
                 regexp: {
                        regexp: /^[a-zA-Z0-9]+$/i,
                        message: ' '
                    },
            	}
        	},
        	refnotmandate:{
        		selector:'.refnotmandate',
        		validators: {
        			 regexp: {
                            regexp: /^[a-zA-Z0-9]+$/i,
                            message: ' '
                    },
        		}
        	}
    	}
        });
});
</script>
@stop


