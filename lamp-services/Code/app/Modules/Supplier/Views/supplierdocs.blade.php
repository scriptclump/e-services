@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="portlet light tasks-widget">
      <div class="portlet-title">
        <div class="caption"> SUPPLIER REQUIRED DOCUMENTS </div>
        <div class="tools"> 
        <button type="button" class="btn btnsize btn-circle blue tooltips" data-container="body" data-placement="top" data-original-title="Tooltip in top"><i class="fa fa-question"></i></button>
        
         </div>
      </div>
      <div class="portlet-body">
      
                  <form id="createdocs" name="createdocs" method="POST" enctype="multipart/form-data">
                              <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                              
    <div class="row CHEQUE" style="margin-top:10px;">
                  <div class="col-md-4">
                    <div class="form-group"> <label class="control-label">Country <span class="required" aria-required="true">*</span></label>
                    <select class="form-control comby" id="org_country" name="org_country">
                        <option value="">Select Country</option>
                        @if(isset($countries))
                            @foreach($countries as $country_value)
                                   <option value="{{$country_value['country_id']}}">{{$country_value['name']}}</option>
                            @endforeach
                        @endif
						
                    </select></div>
                  </div>
                 <div class="col-md-4">
                     <div class="form-group">
                     <label class="control-label">Organization Type <span class="required" aria-required="true">*</span></label>
                    <select class="form-control comby" id="organization_type" name="organization_type">
                        <option value="">Select Organization Type</option>    
                        @foreach($company_data as $companyVal )
                        <option value="{{$companyVal->value}}" >{{$companyVal->company_type}}</option>
                        @endforeach
                    </select>
                     
                     </div>
                     
                  </div>
                </div>
                
                
                <div class="row">
                  <div class="col-md-4">
                      <div class="form-group">
                      
                      <input type="checkbox" name="cheque_file" id="cheque_file" class="chk">
                      <label class="control-label">Cancel Cheque Proof</label>
                      
                      
                          
                      </div>
                  </div>
                    <div class="col-md-4">
                    <div class="form-group">
                    <input type="checkbox" name="mou_fyl" id="mou_fyl" class="chk">
                     <label class="control-label">MOU Document </label>
                    
                    </div>
                  </div>
                </div>                             
                              
                              
                              
                              
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                    <input type="checkbox" name="pan_input" id="pan_input" class="chk">
                      <label class="control-label">PAN Card Number  <span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span> </label>
                      
                    </div>
                      
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                    <input type="checkbox" name="pan_fyl" id="pan_fyl" class="chk">
                    <label class="control-label">PAN Proof </label>
                    </div>
                      
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                     <input type="checkbox" name="cin_input" id="cin_input" class="chk">
                      <label class="control-label">CIN Number <span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                    </div>
                     
                  </div>   
                    <div class="col-md-4">
                    
                     <div class="form-group">
                      <input type="checkbox" name="cin_file" id="cin_file" class="chk">
                      <label class="control-label">CIN Proof </label>
                    </div>
                       
                  </div>
                </div>
                <div class="row VAT">
                  <div class="col-md-4">
                    <div class="form-group">
                    <input type="checkbox" name="tinvat_input" id="tinvat_input" class="chk">
                      <label class="control-label">TIN/VAT Number <span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                    </div>
                      
                  </div>
                   <div class="col-md-4">
                    
                     <div class="form-group">
                     <input type="checkbox" name="tinvat_file" id="tinvat_file" class="chk">
                      <label class="control-label">VAT Proof </label>
                      
                    </div>


                  </div>



                </div>
                <div class="row CST">
                  <div class="col-md-4">
                    <div class="form-group">
                    <input type="checkbox" name="cst_input" id="cst_input" class="chk">
                      <label class="control-label">CST Number  <span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                    </div>
                      
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                      <input type="checkbox" name="cst_file" id="cst_file" class="chk">
                      <label class="control-label">CST Proof </label>
                    </div>
                     
                  </div>




                </div>
             
                
                <hr>              
                <div class="row">
                  <div class="col-md-12 text-center">
                    <button type="submit" class="btn green-meadow">Save</button>
                    <button type="button" id="cancelreqdocs" class="btn green-meadow">Cancel</button>
                  </div>
                </div>
                </form>

        </div>
    </div>
  </div>
</div>
@stop
@section('style')
<style type="text/css">

.thumbnail {
    padding: 0px !important;
 margin-bottom: 0px !important;
}
.fileinput-filename{word-wrap: break-word !important;}

.fileinput-new .thumbnail{ width:100px !important; height:33px !important;}

h4.block{padding:0px !important; margin:0px !important; padding-bottom:10px !important;}

.pac-container .pac-logo{    z-index: 9999999 !important;}
.pac-container{    z-index: 9999999 !important;}
.pac-logo{    z-index: 9999999 !important;}
#dvMap{height:304px !important; width:269px !important;}

label {
	padding-bottom: 0px !important;
}
.modal-header {
    padding: 5px 15px !important;
}

.modal .modal-header .close {
    margin-top: 8px !important;
}



.radio input[type=radio]{ margin-left:0px !important;}

</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('script')
<script type="text/javascript"> 
            jQuery(document).ready(function () {
                FormWizard.init();
            });

</script>
@stop

@section('userscript')
<script src="{{ URL::asset('assets/admin/pages/scripts/form-wizard-supplier.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-migrate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

<script>
$('.chk').change(function() {
         if($('#org_country').val() == '' || $('#organization_type').val() == '' )
         {
            $('.chk').attr('checked', false);
            $('.chk').parent('span').removeClass('checked');
            alert('Please Select Country & Organanization Type');
            return false;
         }    
    });    
    
$('.comby').change(function () {
        var token  = $("#csrf-token").val();
        var organization_type = $('#organization_type').val();
        var org_billingaddress_country = $('#org_country').val();
        $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url: "/suppliers/reqdocs",
                type: "POST",
                data:{org_type: organization_type,
                      org_country: org_billingaddress_country},
               success: function (rs) {
                   console.log(rs);
                   if(rs.length == 0)
                   {
                    $('.chk').attr('checked', false);
                    $('.chk').parent('span').removeClass('checked');   
                   }   
                   $.each(rs, function(i, item) {
                       if(item.document == 'PAN' && item.mandatoryref == 1)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_input").attr('checked', true);
                        $("#"+rs+"_input").parent('span').addClass('checked');
                       }
                       
                       if(item.document == 'PAN' && item.mandatoryref == 0)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_input").attr('checked', false);
                        $("#"+rs+"_input").parent('span').addClass('');
                       }
                       
                       if(item.document == 'PAN' && item.mandatorydoc == 1)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_fyl").attr('checked', true);
                        $("#"+rs+"_fyl").parent('span').addClass('checked');
                       }
                       
                       if(item.document == 'PAN' && item.mandatorydoc == 0)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_fyl").attr('checked', false);
                        $("#"+rs+"_fyl").parent('span').addClass('');
                       }
                       
                       if(item.document == 'CIN' && item.mandatoryref == 1)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_input").attr('checked', true);
                        $("#"+rs+"_input").parent('span').addClass('checked');   
                       }
                       
                       if(item.document == 'CIN' && item.mandatoryref == 0)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_input").attr('checked', false);
                        $("#"+rs+"_input").parent('span').addClass('');   
                       }
                       
                       if(item.document == 'CIN' && item.mandatorydoc == 1)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_file").attr('checked', true);
                        $("#"+rs+"_file").parent('span').addClass('checked');   
                       }
                       
                       if(item.document == 'CIN' && item.mandatorydoc == 0)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_file").attr('checked', false);
                        $("#"+rs+"_file").parent('span').addClass('');   
                       }
                       
                       if(item.document == 'TINVAT' && item.mandatorydoc == 1)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_file").attr('checked', true);
                        $("#"+rs+"_file").parent('span').addClass('checked');   
                       }
                       
                       if(item.document == 'TINVAT' && item.mandatorydoc == 0)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_file").attr('checked', false);
                        $("#"+rs+"_file").parent('span').addClass('');   
                       }
                       
                       if(item.document == 'TINVAT' && item.mandatoryref == 1)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_input").attr('checked', true);
                        $("#"+rs+"_input").parent('span').addClass('checked');   
                       }
                       
                       if(item.document == 'TINVAT' && item.mandatoryref == 0)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_input").attr('checked', false);
                        $("#"+rs+"_input").parent('span').addClass('');   
                       }
                       
                       if(item.document == 'CST' && item.mandatorydoc == 1)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_file").attr('checked', true);
                        $("#"+rs+"_file").parent('span').addClass('checked');   
                       }
                       
                       if(item.document == 'CST' && item.mandatorydoc == 0)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_file").attr('checked', false);
                        $("#"+rs+"_file").parent('span').addClass('');   
                       }
                       
                       if(item.document == 'CST' && item.mandatoryref == 1)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_input").attr('checked', true);
                        $("#"+rs+"_input").parent('span').addClass('checked');   
                       }
                       
                       if(item.document == 'CST' && item.mandatoryref == 0)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_input").attr('checked', false);
                        $("#"+rs+"_input").parent('span').addClass('');   
                       }
                       
                       if(item.document == 'CHEQUE' && item.mandatorydoc == 1)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_file").attr('checked', true);
                        $("#"+rs+"_file").parent('span').addClass('checked');   
                       }
                       
                       if(item.document == 'CHEQUE' && item.mandatorydoc == 0)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_file").attr('checked', false);
                        $("#"+rs+"_file").parent('span').addClass('');   
                       }
                       
                       if(item.document == 'CHEQUE' && item.mandatoryref == 1)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_input").attr('checked', true);
                        $("#"+rs+"_input").parent('span').addClass('checked');   
                       }
                       
                       if(item.document == 'CHEQUE' && item.mandatoryref == 0)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_input").attr('checked', false);
                        $("#"+rs+"_input").parent('span').addClass('');   
                       }
                       
                       if(item.document == 'MOU' && item.mandatorydoc == 1)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_fyl").attr('checked', true);
                        $("#"+rs+"_fyl").parent('span').addClass('checked');   
                       }
                       
                       if(item.document == 'MOU' && item.mandatorydoc == 0)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_fyl").attr('checked', false);
                        $("#"+rs+"_fyl").parent('span').addClass('');   
                       }
                       
                       if(item.document == 'MOU' && item.mandatoryref == 1)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_input").attr('checked', true);
                        $("#"+rs+"_input").parent('span').addClass('checked');   
                       }
                       
                       if(item.document == 'MOU' && item.mandatoryref == 0)
                       {
                        var rs = item.document.toLowerCase();   
                        $("#"+rs+"_input").attr('checked', false);
                        $("#"+rs+"_input").parent('span').addClass('');   
                       }
		     });
               }  
               });
    });
</script>
@stop
@extends('layouts.footer')
