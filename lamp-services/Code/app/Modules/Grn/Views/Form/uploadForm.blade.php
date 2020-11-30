<form id="frmUpload" action="/grn/uploadDoc" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="POST">
        <input type="hidden" name="inward_id" value="{{$grnProductArr[0]->inward_id}}">
                <div id="ajaxResponseDoc"></div>
                <div class="col-md-3 col-sm-3">
                    <label class="control-label">Document Type <span class="required" aria-required="true">*</span></label>
                <select class="form-control" name="documentType" id="documentType">
                    <option value="">Document Type</option>
                    @foreach($docTypes as $key=>$docType)
                        <option value="{{$key}}">{{$docType}}</option>
                    @endforeach                    
                </select></div>
                <div class="col-md-3 col-sm-3"><label class="control-label">Reference No</label><input class="form-control" type="text" id="ref_no" name="ref_no" placeholder="Ref No" /></div>
                <div class="col-md-3 col-sm-3">
                    <label class="control-label">Document Proof <span class="required" aria-required="true">*</span></label>               
<div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
<div>
<span class="btn default btn-file btn green-meadow">
<span class="fileinput-new">Choose File</span>
<input class="form-control" type="file" id="upload_file" name="upload_file" placeholder="Proof of Document">
<!--<label id="upload_file-error" class="error" for="upload_file"></label>-->
</span>


<span class="fileinput-filename" style=" float:left; width:533px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>


</div>
</div>                
                
</div>
                <div class="col-md-3 col-sm-3 margtop"><input class="btn btn-success" type="submit" name="btnUpload" value="Add"></div>
        </form> 