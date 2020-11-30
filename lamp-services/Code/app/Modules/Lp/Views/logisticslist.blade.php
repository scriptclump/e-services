@extends('layouts.default')

@extends('layouts.header')

@extends('layouts.sideview')

@section('content')

<div class="row">
<div class="col-md-12 col-sm-12">
  <div class="portlet light tasks-widget">
    <div class="portlet-title">
      <div class="caption"> LOGISTIC PARTNERS </div>
<a class="btn green-meadow" href="{{url('/')}}/logisticpartners/add" style="margin-top: 6px;margin-left: 10px;">Add New LP</a> 
      <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question"></i></a></span>
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
      </div>
    </div>
    <div class="portlet-body">
    
    
    <div class="row"><div class="col-md-12">&nbsp;</div></div>
    
    
    
        <div class="row">
          <div class="col-md-12">
            <table id="logisticPrtnersList">
            </table>
            <div class="modal fade" id="editlp" tabindex="-1" role="editlp" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">EDIT WAREHOUSE</h4>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Warehouse Name</label>
                          <input type="text" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Address 1</label>
                          <input type="text" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Address 2</label>
                          <input type="text" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Area</label>
                          <input type="text" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">City</label>
                          <input type="text" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Pincode</label>
                          <input type="text" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-5">
                            <div class="form-group">
                              <label class="control-label">Latitude</label>
                              <input type="text" class="form-control">
                            </div>
                          </div>
                          <div class="col-md-5">
                            <div class="form-group">
                              <label class="control-label">Logitude</label>
                              <input type="text" class="form-control">
                            </div>
                          </div>
                          <div class="col-md-2" style="margin-top:28px;"> <a href="" class="btn btn-sm blue"><i class="fa fa-map-marker"></i></a> </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Landmark</label>
                          <input type="text" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Email</label>
                          <input type="text" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Phone</label>
                          <input type="text" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 text-center">
                        <button type="button" class="btn green-meadow">Add New</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.modal-content --> 
              </div>
              <!-- /.modal-dialog --> 
            </div>
          </div>
        </div>
    </div>
  </div>
</div>
</div>
@stop
@section('style')
<style type="text/css">

#logisticPrtnersList_Warehouses{text-align:center !important;}
#logisticPrtnersList_LpFullService{text-align:center !important;}
#logisticPrtnersList_LpForService{text-align:center !important;}
.ui-iggrid .ui-igedit-buttonimage{
    margin-top: 0px!important;
}
#logisticPrtnersList_scroll {
    position: relative !important;
    right: 0px !important;
}

.centerAlignment{
  text-align:  center !important;
}.fa-pencil
{
    color: #a9bde2 !important;
}

.fa-trash-o{
  color: #a9bde2 !important;
}
.ui-icon-check {
    color: #22be9c !important;
}


.ui-igcheckbox-small-off{ color:#ff0000 !important;}
.ui-icon-close:before{ color:#ff0000 !important;}
td img{width:100px; height:33px;}
#logisticPrtnersList_LpID{text-align:center !important;}
#logisticPrtnersList_LpCODService{text-align:center !important;}
#logisticPrtnersList_Action{text-align:center !important;}


.ui-igcheckbox-small-off:before {
    content: '\e675' !important;
}

</style>

<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/cockpit.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('userscript')
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery.sparkline.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/grid_script.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/form-wizard-lp.js') }}" type="text/javascript"></script>

<script>
    $(document).ready( function(){
    FormWizard.init();
    });
	
$(".ui-icon-triangle-1-se").click(function(){
        $(".ui-iggrid-filterrow").toggle();
    });


$('.deleteLogisticPartner').live('click',function(event) {
     event.preventDefault();
     if(confirm('Are you sure do you want to delete ?'))
     {

        
         var token  = $("#csrf-token").val();  
         var LpID   = $(this).attr('href');
            $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/logisticpartners/delete',
                        data: {'LpID':LpID},
                        success: function (rs) { 
                           
						   alert('Logistic Partner deleted successfully');
                                                   $("#success").css("display","block");
                                                   $("#success").html('<li style="display:inline-block;text-align:center;color: #428bca;" class="success">Logistic Partner deleted successfully.</li>');
                                                   setTimeout(function() { $("#success").css("display","none"); }, 10000); 
						   $("#logisticPrtnersList").igHierarchicalGrid({'dataSource':'logisticpartners/getLpList'});

                        }
                    });



     }
})

function deleteWarehoust(wh_id)
{
  console.log("deleted id is ="+wh_id);
  if (confirm('Are you sure you want to delete?'))
  {
    token  = $("#csrf-token").val();
     $.ajax({
          headers: {'X-CSRF-TOKEN': token},
          url: '/logisticpartners/deletewh/'+wh_id,
          processData: false,
          contentType: false,                                             
          success: function (rs) 
          {
			alert('Warehouse deleted successfully');
			$("#logisticPrtnersList").igHierarchicalGrid({'dataSource':'logisticpartners/getLpList'});
           //alert(rs);
          }
      });
}}

 function sel_selbx(dv_id, sel_val) {
                var options = $(dv_id + ' option');
                $(dv_id + ' option').removeAttr('selected');
                var ind = '';

                $.map(options, function(option) {
                    if (option.text == sel_val)
                    {
                        ind = $(option).index();
                        $(dv_id + ' option').eq(ind).prop("selected", "selected")
                    }if (option.value == sel_val)
                    {
                        ind = $(option).index();
                        $(dv_id + ' option').eq(ind).prop("selected", "selected")
                    }
                });
                return;
            }


function editWarehoust(wh_id)
{
    token  = $("#csrf-token").val();
 $.ajax({
      headers: {'X-CSRF-TOKEN': token},
      url: '/logisticpartners/editwh/'+wh_id,
      processData: false,
      contentType: false,                                             
      success: function (rs) 
      {
        var test =rs[0].lp_wh_name;
        $("#wh_name").val(rs[0].lp_wh_name);
        $("#wh_cont_name").val(rs[0].contact_name);
        $("#wh_email").val(rs[0].email);
        $("#wh_phone").val(rs[0].phone_no);
        $("#wh_address1").val(rs[0].address1);
        $("#wh_address2").val(rs[0].address2);
        $("#wh_pincode").val(rs[0].pincode);
        $("#wh_city").val(rs[0].city);
        $("#wh_lat").val(rs[0].longitude);
        $("#wh_log").val(rs[0].latitude);
        
        $("#wh_latitude").val(rs[0].latitude);
        $("#wh_logitude").val(rs[0].longitude);
        sel_selbx('#wh_state',rs[0].state);
        sel_selbx('#wh_country',rs[0].country);

        $("#click_editwh").trigger('click');
      }
    });
}


</script>
@stop
@extends('layouts.footer')