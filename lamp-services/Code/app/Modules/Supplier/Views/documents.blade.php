<div class="tab-pane" id="tab_22">
    <form id="supplierdocs" name="supplierdocs" method="POST" enctype="multipart/form-data">       
        <div class="row">
            <div class="col-md-4">
                <label class="control-label">Document Type <span class="required" aria-required="true">*</span></label>
                <select class="form-control" name="documentType" id="documentType">
                    <option value="">Document Type</option>
                    @foreach($docTypes as $key=>$docType)
                    <option value="{{$docType}}">{{$docType}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="control-label">Reference No.<span class="required" aria-required="true">*</span></label><input class="form-control" type="text" id="ref_no" name="ref_no" placeholder="Ref. No." maxlength="15" >
                <label class="control-label" id="gst_error" style="color:#e02222"></label>
                <input type="hidden" id="gst_codes" value="">
            </div>
            
            <div class="col-md-2"><div class="form-group">
            <label class="control-label">Document Proof </label>

            <div class="fileinput fileinput-new" data-provides="fileinput">
                <span class="btn default btn-file btn green-meadow" style="width:110px !important;">
                    <span class="fileinput-new">Choose File </span>
                    <span class="fileinput-exists" style="margin-top:-9px !important;">Change File</span>
                </span>                                                              
                
                <br />
                <input id="manuLogo" type="file"  name="upload_file" style="margin-top: -27px !important;  position: absolute;opacity: 0; width:103px;"/>
                <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">&nbsp; 
                    <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                
            </div>
            </div>
            </div>
            
            <div class="col-md-2 margtopbtn text-left" style="margin-top:29px;">
                <input class="btn btn-success green-meadow" type="submit" name="btnUpload" value="Add">
            </div>
        </div>                
    </form>

    <table class="table table-striped table-bordered table-advance table-hover" id="supplier_doc_table">
        <thead>
            <tr>
                <th>Document Type</th>
                <th>Ref. No.</th>
                <th>Created By</th>
                <th style="text-align:center;">Attachment</th>
                <th style="text-align:center;">Action</th>
            </tr>
        </thead>                    
        <tbody>
        @if(isset($docsArr) && count($docsArr) > 0)
        @foreach($docsArr as $doc)                        
        <tr>
            <td>{{$doc->doc_type}}</td>
            <td>{{$doc->reference_no}}</td>
            <td>{{$doc->created_by}}</td>
			@if(strpos($doc->doc_url, 'http') !== false)
            <td align="center"><a href="{{$doc->doc_url}}" target="_blank"><i class="fa fa-download"></i></a></td>
		    @else
			<td align="center"><a href="/uploads/Suppliers_Docs/{{$doc->doc_url}}" target="_blank"><i class="fa fa-download"></i></a></td>	
			@endif
            <td align="center">
                <a class="delete grn-del-doc" id="{{$doc->doc_id}}" href="javascript:void(0);"><i class="fa fa-remove"></i></a>
            </td>
        </tr>
        @endforeach
        </tbody>
        @endif       
    </table>      

    <div id="no_rec_id"><?php if(!count($docsArr)){?> <p>No Records Found.</p> <?php } ?></div>	
</div>

