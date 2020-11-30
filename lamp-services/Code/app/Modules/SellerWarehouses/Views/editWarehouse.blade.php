<style>

#tinProof, #apobProof {
  background-color: #fff !important;
  border: 0px;
  float: left;
  position: absolute;
  margin-left: 20px;
  line-height: 30px;

}
</style>
<form id="edit_wh" ole="form" method="POST"  files="true" enctype ="multipart/form-data">
    <input type="hidden" name="legal_entity_id" id="legal_entity_id" value="{{$data->legal_entity_id}}" />
    <input type="hidden" name="le_wh_id" id="le_wh_id" value="{{$id}}">
    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
	<div class="row">
        <div class="col-md-4">
	        <div class="form-group">
	                <label class="control-label">Warehouse Name</label>
				<p>{{$data->lp_wh_name}}</p>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
		                <label class="control-label">City</label>
					<p>{{$data->city}}</p>
			</div>
		</div>

		<div class="col-md-4">
	        <div class="form-group">
	                <label class="control-label">Tin Number</label>
	                <input  class="form-control" type="text" name="tin_number" value="{{$data->tin_number}}">
			</div>
		</div>
	</div>

			 <div class="table-responsive" >
                  <table class="table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                      	<th>Doc</th>
                        <th>Doc Name</th>
                        <th>Actions</th>
                      </tr>
                    </thead>

                    <tbody>
                      <tr>
                        <td>Tin Doc</td>
                        <td><a href="@if(isset($tinProof->doc_url)){{$tinProof->doc_url}}@endif" target="_blank"> @if(isset($tinProof->doc_name)){{$tinProof->doc_name}}@endif</a></td>
                        <td>   <div class="row">
                                    <div class="col-md-12">
                                       <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-left:16px !important;">
                                          <span class="btn default btn-file btn green-meadow" style="width:110px !important;">
                                          <span class="fileinput-new">Update File </span>
                                          <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>
                                          </span>
                                          <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                                          <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px; z-index:99; position: relative;"> <img src="@if(isset($tinProof->doc_url)){{$tinProof->doc_url}}@endif" alt="" class="tinvat_files_id" /></div>
                                          <input type="hidden" name="tin_doc_id" value="{{$tinProof->doc_id}}">
                                          <br />
                                          <input id="tinProof" type="file" class="upload" name="tinProof" style="margin-top: -35px !important; height:35px;  position: absolute;opacity: 0;"/>
                                           @if(isset($tinProof->doc_name))
                                           <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">&nbsp;{{$tinProof->doc_name}}<a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                            @else
                                          <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">&nbsp;<a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                          @endif
                                       </div>
                                    </div>
                                 </div>
                        </td>
                      </tr>
                      <tr>
                      	<td>APOB Doc</td>
                        <td><a href="@if(isset($apobProof->doc_url)){{$apobProof->doc_url}}@endif" target="_blank"> @if(isset($tinProof->doc_name)){{$apobProof->doc_name}}@endif</a></td>
                        <td><div class="row">
                                    <div class="col-md-12">
                                       <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-left:16px !important;">
                                          <span class="btn default btn-file btn green-meadow" style="width:110px !important;">
                                          <span class="fileinput-new">Update File </span>
                                          <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>
                                          </span>
                                          <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                                          <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px; z-index:99; position: relative;"> <img src="@if(isset($apobProof->doc_url)){{$apobProof->doc_url}}@endif" alt="" class="tinvat_files_id" /></div>
                                          <input type="hidden" name="apob_doc_id" value="{{$apobProof->doc_id}}">
                                          <br />
                                          <input id="apobProof" type="file" class="upload" name="apobProof" style="margin-top: -35px !important; height:35px;  position: absolute;opacity: 0;"/>
                                           @if(isset($apobProof->doc_name))
                                           <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">&nbsp;{{$apobProof->doc_name}}<a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                            @else
                                          <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">&nbsp;<a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                          @endif
                                       </div>
                                    </div>
                                 </div>
                        </td>
                      </tr>
                    </tbody>

                  </table>
                  <div class="row">
                          <div class="col-md-12 text-center">
                             <input type="button" name="" id="update_wh" class="btn green-meadow" value="Update">
                          </div>
                       </div>
</form>
<script type="text/javascript">


$('#update_wh').click(function (){
     var formValid = $('#edit_wh').formValidation('validate');
         formValid = formValid.data('formValidation').$invalidFields.length;
          if(formValid != 0){
            return false;
          }
       else{
       	var id = $('#le_wh_id').val();
       	var form = document.forms.namedItem("edit_wh"); 
      	var formdata = new FormData(form);
    //console.log(form);
        $.ajax({
            url: '/warehouse/updateWarehouse/'+id,
            data: formdata,
	        type: $(form).attr('method'),
	        processData :false,
	        contentType:false,
            success: function (result)
            {   
                var response = JSON.parse(result);
                if(response.status == true){
                    alert(response.message);
                    window.location = "/warehouse";
                }
            }
        });
    }
});
	
/*	$('#edit_wh').formValidation({
        framework: 'bootstrap',
            icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
        fields: {
            tin_number:{
                validators: {
                    notEmpty: {
                        message: ' '
                    }
                }
            },
            tinProof:{
              validators: {
                file: {
                      extension: 'doc,docx,pdf,jpeg,jpg,png',
                      type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
                      maxSize: 10*1024*1024,   // 5 MB
                      message: 'The selected file is not valid, it should be (doc,docx,pdf,jpeg,png,jpg) and 10 MB at maximum.'
                    },
                notEmpty: {
                            message: ' '
                        }
                }
            },
            apobProof:{
              validators: {
                file: {
                      extension: 'doc,docx,pdf,jpeg,jpg,png',
                      type: 'application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png',
                      maxSize: 10*1024*1024,   // 5 MB
                      message: 'The selected file is not valid, it should be (doc,docx,pdf,jpeg,png,jpg) and 10 MB at maximum.'
                    },
                notEmpty: {
                            message: ' '
                        }
                }
            }
 		}
    }).on('success.form.fv', function(event) {
          event.preventDefault();
          console.log('here in success');
    }); */


</script>