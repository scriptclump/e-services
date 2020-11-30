@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message"></span>
<span id="error_message"></span>
<div id="loadingmessage" class=""></div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget" style="height: auto;">
            <div class="portlet-title">
                <div class="caption">
                   Inventory Writeoff Report
                </div>
                <div class="actions">
                    <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
                     <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document" class="btn green-meadow">Upload Inventory Writeoff</a>
                </div>
                
            </div>
        </div>
    </div>
</div>
<div class="portlet-body">
    <div class="row" style="margin-left: -2px;">
     <!-- {{ Form::open(array('id' => 'inventory-snapshot'))}} -->
     {{ Form::open(array('url' => '/inventory/writeoffdownload', 'id' => 'writeoff_form_submit'))}}
        <div class="custom_range" id="custom_range" >
           <div class="col-md-3">
            <label class="control-label">Warehouse List</label>
            <select id="wh_id"  name="wh_id" class="form-control " >
              <option value = "">Please select</option>
                @foreach($wh_list as $details)
                    <option value = "{{$details['le_wh_id']}}">{{$details['lp_wh_name']}}</option>
                @endforeach
            </select>
           </div>
          <div class="col-md-3">
              <div class="form-group">
                  <label class="control-label">From Date</label>
                  <input type="text" class="form-control " name="from_date" id="from_date" placeholder="From Date">
              </div>
          </div>
          <div class="col-md-3">
              <div class="form-group">
                  <label class="control-label">To Date</label>
                  <input type="text" class="form-control" name="to_date" id="to_date" placeholder="To Date">                            
              </div>
          </div>
        </div>
        <div class="col-md-2">
          <button type="submit" id="inv_writeoff_btn" style="height:36px;margin-top: 21px;"class="btn green-meadow subBut">Download</button>
        </div>
        {{ Form::close() }}

    </div>
        
    <div class="modal modal-scroll fade" id="upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
              <h4 class="modal-title" id="myModalLabel">Import Inventory Writeoff</h4>
          </div>
        {{ Form::open(['id' => 'uploadexcel']) }}
        <div class="row" style="padding-top: 30px;padding-left:50px;">
            <div class="col-md-6" style="padding-top: 10px;">
                <div class="form-group">

                    <div class="fileinput fileinput-new" data-provides="fileinput">
                         <div>
                            <span class="btn default btn-file btn green-meadow btnwidth">
                                <span class="fileinput-new">{{ trans('inventorylabel.filters.pop_up_choosefile') }}</span>
                                <span class="fileinput-exists" style="margin-top:-9px !important;">Choose Inventory File</span>
                                <input type="file" name="upload_taxfile" id="upload_taxfile" value="" class="form-control"/>
                            </span>
                            <span class="fileinput-filename" style=" float:left; width:533px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6" >
                <div class="form-group">
                    <label class="control-label"> </label>
                    <button type="button"  class="btn green-meadow" id="excel-upload-button">{{ trans('inventorylabel.filters.pop_up_upload_btn') }}</button>
                </div>
            </div>
            <div class="row">
               <div class="col-md-12 text-center">
                <span id="loader" style="display:none;">Please Wait<img src="/img/spinner.gif" style="width:225px; padding-left:20px;" /></span>
            </div> 
            </div>
        </div>
       
        {{ Form::close() }} 
      </div>
    </div>
 
</div>
@stop


@section('style')
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<!--Sumoselect CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>



<script type="text/javascript">
 $(document).ready(function(){
    $(".actions").click(function(){
      $(".fileinput-filename").html("");
    });
      var start = new Date();
      var end = new Date(new Date().setYear(start.getFullYear() + 5)); 
      $('#from_date').datepicker({
          endDate: "0d",
          autoclose: true, 
          format: 'yyyy-mm-dd'    
      }).on('changeDate', function () 
      {        
          stDate = new Date($(this).val());    
          $('#to_date').datepicker('setStartDate', $('#from_date').val());
      }); 
      $('#to_date').datepicker({        
          endDate: "0d",       
          autoclose: true,        
          format: 'yyyy-mm-dd'    
      }).on('changeDate', function () { 
          $('#from_date').datepicker('setEndDate', $('#to_date').val());
      });



      $("#inv_writeoff_btn").click(function(e){
        e.preventDefault();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var dc_id = $("#wh_id").val();
        if(from_date!= "" && to_date !="" && dc_id!="")
        {    
          $("#writeoff_form_submit").submit();
          $("#writeoff_form_submit")[0].reset();
        }
        else
        {
          alert("Please select all options.");
        }
      });

    });
$("#excel-upload-button").on('click',function (e) {
    var token = $("#token_value").val();
    var stn_Doc = $("#upload_taxfile")[0].files[0];
    
    if (typeof stn_Doc == 'undefined')
    {
        alert("Please select file");
        return false;
    }
    var formData = new FormData();
    console.log(stn_Doc);
    formData.append('upload_excel_sheet', stn_Doc);
    formData.append('test', "sample");
    var ext = stn_Doc.name.split('.').pop().toLowerCase();
    console.log(ext);
        if($.inArray(ext, ['xlsx']) == -1) {
            alert("Please choose a valid file");
            return false;
        }
        console.log(stn_Doc);
    $.ajax({
        type: "POST",
        url: "/inventory/writeoffupload?_token=" + token,
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        beforeSend: function () {
            $('#loader').show();
            $('body').spinnerQueue({showSpeed: 'fast', hideSpeed: 'fast'}).spinnerQueue('started', 'box2Load', true);
        },
        complete: function () {
            $('#loader').hide();
            $('body').spinnerQueue({showSpeed: 'fast', hideSpeed: 'fast'}).spinnerQueue('finished', 'box2Load', true);
        },
        success: function (data)
        {
            console.log("dataaaaa"+data);
            if(data == 0)
            {
                console.log("stop here");
                $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Excel Headers were mis-matched</div></div>');  
                $("#upload_taxfile").val("");
                $(".fileinput-filename").html("");
                $("#warehousenamess").prop('selectedIndex',0);
                $('#upload-document').modal('toggle');
                return false;
            }
            /*checking here if the user is not having the access for approval work flow */
           if(data.no_permission == "No Permission")
            {
                    $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>You are not permitted for this action</div></div>');  
            
            }else{
                var datalink = data.linkdownload;
                var LINK = "<a target='_blank' href=/" + datalink + ">View Details</a>";
                var consolidatedmsg = "{{ trans('inventorylabel.filters.inv_writeoff_update') }}";
                consolidatedmsg = consolidatedmsg.replace('UPDATE', data.updated_count);
                consolidatedmsg = consolidatedmsg.replace('TOTAL', data.total_uploaded_count);
                consolidatedmsg = consolidatedmsg.replace('ERROR', data.error_count);
                
                consolidatedmsg = consolidatedmsg.replace('LINK', LINK);
                $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+ consolidatedmsg +'</div></div>');  
            }
            
            $("#upload_taxfile").val("");
            $(".fileinput-filename").html("");
            $("#warehousenamess").prop('selectedIndex',0);
            $('#upload-document').modal('toggle');
            // $("#inventorygrid").igGrid("dataBind");
            return false;
            
        },
        error:function(jqXHR, textStatus, errorThrown) {
              console.log(textStatus, errorThrown);
            }
    });
    
});

</script>
@stop

