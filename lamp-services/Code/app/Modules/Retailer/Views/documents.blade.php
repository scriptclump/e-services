
<div class="tab-pane" id="upload_doc">
    <form id="frmUpload" action="/retailers/uploadDoc" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="le_id" id="le_id" class="form-control" value="{{$le_id}}">
        <input type="hidden" name="is_document_required" value="<?php if(!empty($leDocDetails) && count($leDocDetails) > 0) { echo 0; }else { echo 1; } ?>" />
        <div id="ajaxResponseDoc"></div>
        <div class="row">
            <div class="col-md-4">
                <label class="control-label">Document Type <span class="required" aria-required="true">*</span></label>
                <select class="form-control" name="documentType" id="documentType">
                    <option value="">Document Type</option>
                        @foreach($docTypes as $key=>$docType)
                            <option value="{{$key}}">{{$docType}}</option>
                        @endforeach                    
                </select>
            </div>
            <div class="col-md-2">
                <label class="control-label">Document Proof <span class="required" aria-required="true">*</span></label>               
                <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;"><div>
                    <span class="btn default btn-file btn green-meadow">
                        <span class="fileinput-new">Choose File</span>
                        <span class="fileinput-exists" style="margin-top:-9px !important;">Change File</span>
                        <input class="form-control" type="file" id="upload_file" name="upload_file" placeholder="Proof of Document">
                    </span>
                    <span class="fileinput-filename" style=" float:left; width:533px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                    </div>
                </div>                                        
            </div>          
            <div class="col-md-2 margtopbtn text-left" style="margin-top:25px;">
                <input class="btn btn-success" type="submit" name="btnUpload" value="Add">
            </div>
        </div>        
        
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <table class="table table-striped table-bordered table-advance table-hover" id="leDocList">
                    <thead>
                        <tr>
                            <th>Document Type</th>
                            <th>Document Name</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th style="text-align:center;">Attachment</th>
                            <th style="text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($leDocDetails))
                            @foreach($leDocDetails as $leDoc)
                                <tr>
                                    <td>{{ $leDoc->doc_type }}</td>
                                    <td>{{ $leDoc->doc_name }}</td>
                                    <td>{{ $leDoc->created_by }}</td>
                                     <td>{{ $leDoc->created_at }}</td>
                                    <td align="center"><a href="{{ $leDoc->doc_url }}" target="_blank"><i class="fa fa-download"></i></a></td>
                                    <td align="center">
                                        <a class="delete le-del-doc" id="{{ $leDoc->doc_id }}" href="javascript:void(0);">
                                        <i class="fa fa-remove"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach    
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </form>    
</div>

<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
@include('includes.validators')