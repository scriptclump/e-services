@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<ul class="page-breadcrumb breadcrumb">
    <li><a href="/">Home</a><i class="fa fa-circle"></i></li>
    <li><a href="/seller/index">Sellers</a><i class="fa fa-circle"></i></li>
    <li class="active">Edit Seller</li>
</ul>
<h1>Edit Seller</h1>
<div class="row">
    <div class="col-md-12">
        <div class="portlet box" id="form_wizard_1">
            <div class="portlet-body form">
				<form id="submit_form" action="/seller/updateSeller/{{$data->legal_entity_id}}" >
                      <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                          <label class="control-label">Business Name</label>
                          <div class="input-group ">
                            <span class="input-group-addon addon-red"><i class="ion-arrow-shrink"></i></span>
                              <input type="text"  id="business_name" name="business_name" class="form-control" value="{!! $data->business_legal_name !!}" readonly> 
                          </div>
                        </div>
                       </div>
                       <div class="col-md-6">
                            <div class="form-group">
                          <label class="control-label">City</label>
                          <div class="input-group ">
                            <span class="input-group-addon addon-red"><i class="ion-document-text"></i></span>
                           <input type="text"  id="city" name="city" class="form-control" value="{!! $data->city !!}" >
                          </div>
                        </div>
                    </div>
                   	</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                          <label class="control-label">Address1</label>
                          <div class="input-group ">
                            <span class="input-group-addon addon-red"><i class="ion-arrow-shrink"></i></span>
                              <input type="text"  id="address1" name="address1" class="form-control" value="{!! $data->address1!!}"> 
                          </div>
                        </div>
                       </div>
                       <div class="col-md-6">
                            <div class="form-group">
                          <label class="control-label">Address2</label>
                          <div class="input-group ">
                            <span class="input-group-addon addon-red"><i class="ion-document-text"></i></span>
                           <input type="text"  id="address2" name="address2" class="form-control" value="{!!  $data->address2  !!}" >
                          </div>
                        </div>
                    </div>
                   </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                          <label class="control-label">State</label>
                          <div class="input-group ">
                            <span class="input-group-addon addon-red"><i class="ion-arrow-shrink"></i></span>
                              <select name="state_id" class ="form-control" id="state_id">
		                          @foreach ($states as $key => $value)
		                          @if($value->state_id == $data->state_id)
		                          	<option value="{!! $value->state_id !!}" selected>{!! $value->state !!}</option>
		                          @else
		                           <option value="{!! $value->state_id !!}">{!! $value->state !!}</option>
		                          @endif
		                          @endforeach
		                        </select>
                            </div>
                        </div>
                      </div>
                       <div class="col-md-6">
                          <div class="form-group">
                          <label class="control-label">Pincode</label>
                          <div class="input-group ">
                            <span class="input-group-addon addon-red"><i class="ion-document-text"></i></span>
                           <input type="text"  id="pincode" name="pincode" class="form-control" value="{!!  $data->pincode  !!}" >
                          </div>
                        </div>
                    </div>
                    </div>
                      <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                          <label class="control-label">Pan Number</label>
                          <div class="input-group ">
                            <span class="input-group-addon addon-red"><i class="ion-arrow-shrink"></i></span>
                              <input type="text"  id="pan" name="tin" class="form-control" value="{!! $data->pan_number!!}" readonly> 
                          </div>
                        </div>
                      </div>
                       
                       <div class="col-md-6">
                          <div class="form-group">
                          <label class="control-label">Tin Number</label>
                          <div class="input-group ">
                            <span class="input-group-addon addon-red"><i class="ion-document-text"></i></span>
                           <input type="text"  id="tin" name="tin" class="form-control" value="{!!  $data->tin_number  !!}" readonly>
                          </div>
                        </div>
                    </div>
                   </div>
                   <input type="hidden" name="id" id="id" value="{!! $data->legal_entity_id !!}">
                   <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                   <div class="row" style="margin-top:40px;">
                        <div class="col-md-12 text-center">
                            <a href="/seller/index" class="btn blue goBack" ><i class="m-icon-swapleft m-icon-white"></i> Back </a>
                            <input type="button" value="Update" class="btn blue button-next submit" id="update" />
                            <i class="m-icon-swapright m-icon-white"></i>

                        </div>
                    </div>
				</form>
			</div>
		</div>
	</div>
</div>
<script src="{{ URL::asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-migrate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
	 $.ajaxSetup({
        headers:
        {
            'X-CSRF-Token': $('input[name="_token"]').val()
        }
    });

	$('#update').click(function (){
		//alert('hrer');
		var form = $('#submit_form').serialize();
		console.log(form);
		var id = $('#id').val();
		var url = '/seller/updateSeller/'+id;
		console.log(url);
		console.log(id);
		$.ajax({
        url: url,
        data: form,
        type: 'POST',
        success: function (result)
        {
        	var response = JSON.parse(result);
            console.log(response);
            if(response.status == true){
	    	alert(response.message);
              window.location = "/seller/index";
            }
        }
		});
	});
</script>
@stop
@extends('layouts.footer')