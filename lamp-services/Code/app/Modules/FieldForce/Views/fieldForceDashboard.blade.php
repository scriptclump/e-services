@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'FF Targets'); ?>
<span id="success_message">@include('flash::message')</span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
          <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">

            <div class="portlet-title">
                <div class="caption">FIELDFORCE TARGETS</div>
            </div>

{{ Form::open(array('url' => '', 'method' => 'POST', 'id' =>'fieldforce_form_id'))}}
<div class="modal modal-scroll fade" id="fieldforce_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="myModalLabel">SET FF TARGETS </h4>
        <span id="success_message_ajax"></span>
    </div>

<div class="modal-body">
<div class="row">
<div class="col-md-12">
<div class="form-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
            <label class="col-md-2 control-label text-left">FF Name</label>
                <div class="col-md-10 text-left padleftbut">
                    <input type="text" class="form-control" id="ffname" name="ffname" readonly>
                    <input type = "hidden" id = "HiddenInputID" name = "HiddenInputID"/>
                </div>
            </div>
        </div>
       
    </div>
</div>

<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label effectdate text-left">Target Param</label>

        </div>
    </div>

    <div class="col-md-10">
        <div class="row">
            <div class="col-md-2 padleftbut">
                <div class="form-group">
                    <label class="control-label inputfont">Target Name</label>
                        <select class="form-control" id="mascat_target" name="mascat_target" onChange="loadFieldForceData(this.value);">
                            <option value = "">Please Select</option>
                            @foreach($loadmascatdata as $mascatData)
                                <option value = "{{$mascatData->value}}">{{$mascatData->master_lookup_name}}</option>
                                @endforeach
                        </select>
                </div>
            </div>

            <div class="col-md-2 padleftbut">
                <div class="form-group">
                    <label class="control-label inputfont">Daily</label>
                        <input type = "text" class="form-control" id = "daily" name = "daily" placeholder="number only">
                        
                </div>
            </div>

            <div class="col-md-2 padleftbut">
                <div class="form-group">
                    <label class="control-label inputfont">Weekly</label>
                    <input type="text" class="form-control" id="weekly" name="weekly" placeholder="number only">
                    
                </div>
            </div>

            <div class="col-md-2 padleftbut">
                <div class="form-group">
                    <label class="control-label inputfont">Monthly</label>
                        <input type = "text" class="form-control" id="monthly" name="monthly" placeholder="number only">
                        
                </div>
            </div>

            <div class="col-md-3 padleftbut">
            <div class="form-group">
            <label class="control-label inputfont">Effective Date</label>
                    <input type="text" class="form-control" id="date" name="date">
                    <input type ="hidden" id="refresh-after-ajax"/>
            </div>
        </div>

            <div class="col-md-1 padleftbut">
                <div class="form-group">
                @if($addFlag==1)
                <button type="submit" class="btn green-meadow butmargtop" id="price-save-button">Add</button>
                @endif
                    <!-- <span class="btn green effectbut" ><i class="fa fa-plus"> Add</i></span> -->
                </div>
            </div>
            
        </div>
    </div>
</div>


<!-- <div class="row">
    <div class="col-md-2">
    </div>
    <div class="col-md-10" id="graph_container" style="display:none;">
        <div class="row">
        <div class="col-md-2">&nbsp;</div>
        <div class="col-md-2 text-center" style="padding-left: 0px !important;" id = "dailycenter">
            <span id = "daily_target" name = "daily_target" class="text_span">
                <dl style="width: 110px; margin-left:0px; font-size:8px;">
                    <dt id="daily_target_graph"></dt>
                    <dd><div id="data-one" class="bar" style="width: 100%">100%</div></dd>
                    <dt id="daily_achive_graph"></dt>
                    <dd><div id="data-two" class="bar daily_bar" style="width: 0%">0%</div></dd>
                </dl>
            </span>
        </div>
        <div class="col-md-2 text-center" style="padding-left: 0px !important;">
            <span id = "weekly_target" name = "weekly_target" class="text_span">
                <dl style="width: 110px; margin-left:0px; font-size:8px;">
                    <dt id="weekly_target_graph"></dt>
                    <dd><div id="data-one" class="bar" style="width: 100%">100%</div></dd>
                    <dt id="weekly_achive_graph"></dt>
                    <dd><div id="data-two" class="bar weekly_bar" style="width: 0%">0%</div></dd>
                </dl>
            </span>
        </div>
        <div class="col-md-2 text-center" style="padding-left: 0px !important;">
            <span id = "monthly_target" name = "monthly_target" class="text_span">
                <dl style="width: 110px; margin-left:0px; font-size:8px;">
                    <dt id="monthly_target_graph"></dt>
                    <dd><div id="data-one" class="bar" style="width: 100%">100%</div></dd>
                    <dt id="monthly_achive_graph"></dt>
                    <dd><div id="data-two" class="bar monthly_bar" style="width: 0%">0%</div></dd>
                </dl>
            </span>
        </div>

        <div class="col-md-2">&nbsp;</div>
        <div class="col-md-2">&nbsp;</div>
        </div>
    </div>
</div> -->
{{ Form::close() }}



<div class="row">
    <div class="col-md-12">
        <div class="scroller mydream" style="height: 150px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" >
            <table class="table table-striped table-bordered table-hover table-advance fixed_headers" id = "fieldforcedata" name = "fieldforce_data1[]">
                <thead>
                    <tr>
                        <th>Target Name</th>
                        <th>Daily</th>
                        <th>Weekly</th>
                        <th>Monthly</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
            <tbody>          
            
            </tbody>
            </table>
        </div>                        
    </div>
</div>

<div class="margbottom"></div>

</div>
</div>
</div>
</div>
</div>
</div>
</div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
                <div class="scroller" style=" height:550px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                    <table id="fieldForcedata"></table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@stop
@section('style')
<style type="text/css">

.margbottom{ margin-bottom: 100px;}
.padleftbut{ padding-left: 0px !important;}
.text_span{text-align: center !important; margin: 0 auto; font-weight: bold;color:blue;}
.butmargtop{ margin-top: 28px;}
.inputfont{ font-size: 11px;}
 .control-label {
    margin-top: 8px !important;
}
.effectdate{margin-top: 38px !important;}
.effectbut{margin-top: 32px !important;}

.ui-iggrid-results{
        height: 14px !important;
    }


dt { float: left; /*padding: 4px;*/ }

.bar {
    margin-bottom: 5px;
    color: #fff;
    padding: 2px;
    /*text-align: center;*/
    background: -webkit-gradient(linear, left top, left bottom, from(#ff7617), to(#ba550f));
    background-color: #ff7617;
    /*-webkit-box-reflect: below 0 -webkit-gradient(linear, left top, left bottom, from(rgba(0,0,0,0)), to(rgba(0,0,0,0.25)));
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;*/
    border-radius: 2px;
    -webkit-animation-name:bar;
    -webkit-animation-duration:0.5s;
    -webkit-animation-iteration-count:1;
    -webkit-animation-timing-function:ease-out;
}

#data-one { -webkit-animation-name:bar-one; }
#data-two { -webkit-animation-name:bar-two; }
#data-three { -webkit-animation-name:bar-three; }
#data-four { -webkit-animation-name:bar-four; }

@-webkit-keyframes bar-one {
    0% { width:0%; }
    100% { width:60%; }
}

@-webkit-keyframes bar-two {
    0% { width:0%; }
    100% { width:80%; }
}

@-webkit-keyframes bar-three {
    0% { width:0%; }
    100% { width:64%; }
}

@-webkit-keyframes bar-four {
    0% { width:0%; }
    100% { width:97%; }
}


</style>

<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/javascript"/>
@stop
@section('script')
@include('includes.validators')
@stop
@section('userscript')
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/getbusinesslist.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
 
<script type="text/javascript">
    $('#fieldforce_form_id').formValidation({
        message: 'This value is not valid',
        icon: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            daily: {
                validators: {
                    notEmpty: {
                        message: 'Enter some number!'
                    }
                }
            },
            mascat_target: {
                validators: {
                    notEmpty: {
                        message: 'Select Target!'
                    }
                }
            },
            weekly: {
                validators: {
                    notEmpty: {
                        message: 'Enter some number!'
                    }
                }
            },
            monthly: {
                validators: {
                    notEmpty: {
                        message: 'Enter some number!'
                    }
                }
            },
        }
})
.on('success.form.fv', function(e){
    e.preventDefault();
    ffid  = $("#HiddenInputID").val();
    var frmData = $('#fieldforce_form_id').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/fftarget/savefieldforcedata',
        data: frmData,
        success: function (respData)
        {
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                type: "POST",
                url: '/fftarget/getfieldforcedata/' + ffid,
                success: function (data)
                { 
                    var tableRow='';
                    $("#fieldforcedata td").remove();
                    for(var i=0; i<data.length; i++){
                        $("#ffname").val(data[i].firstname);
                        if(data[i].dataFlag==1){
                            tableRow = tableRow + '<tr class="gradeX odd">\
                                    <td data-val="list_details" class="prom-font-size">'+data[i].master_lookup_name+'</td>\
                                    <td data-val="list_details" class="prom-font-size">'+data[i].target_daily+'</td>\
                                    <td data-val="list_details" class="prom-font-size">'+data[i].target_weekly+'</td>\
                                    <td data-val="list_details" class="prom-font-size">'+data[i].target_monthly+'</td>\
                                    <td data-val="list_details" class="prom-font-size">'+data[i].effective_date+'</td>\
                                    <td>@if($deleteFFtarget==1)<a href="javascript:void(0);" onclick="deleteFieldForceData('+data[i].ff_target_id+',this)"><i class="fa fa-trash-o"></i></a>@endif</td></tr>';
                        }
                    }
                    $('#fieldforcedata').append(tableRow);

                    // Revalidate all the fields at the time of modification
                    $('#fieldforce_form_id').formValidation('revalidateField', 'daily');
                    $('#fieldforce_form_id').formValidation('revalidateField', 'weekly');
                    $('#fieldforce_form_id').formValidation('revalidateField', 'monthly');
                    
                }
            });
            $('#daily').val('');
            $('#weekly').val('');
            $('#monthly').val('');
            $('#date').val('');
            $('#mascat_target').val('');

            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+respData+'</div></div>' );
            $(".alert-success").fadeOut(20000)
        
        }
    });
});

function allowNumber(event){
    if (event.shiftKey == true) {
        event.preventDefault();
    }
    if ((event.keyCode >= 48 && event.keyCode <= 57) || 
        (event.keyCode >= 96 && event.keyCode <= 105) || 
        event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
        event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {
    } else {
        event.preventDefault();
    }
    if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
        event.preventDefault(); 
}

function loadFieldForceData(selectedValue){
    
    token  = $("#csrf-token").val();
    hiddenid  = $("#HiddenInputID").val();
     $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        url: '/fftarget/loadfieldforcedata',
        type: 'POST',
        data: 'selectedData=' + selectedValue + '&hiddenid='+hiddenid,
        success: function(data) {

            /*$('#graph_container').show();

            var achived = parseInt(data['daily'][0]);
            var totTarget = parseInt(data['daily'][1]);
            achived = (achived / totTarget) * 100;
            achived = parseInt(achived);

            if(achived>totTarget){
                achived=100;
            }

            // Bar for Daily
            $('#daily_target_graph').html( data['daily'][1] );
            $('#daily_achive_graph').html( data['daily'][0] );
            $('.daily_bar').css("width", achived+"%");
            $('.daily_bar').html(achived+"%");


            // Bar for Weekly
            var achived = parseInt(data['weekly'][0]);
            var totTarget = parseInt(data['weekly'][1]);
            achived = (achived / totTarget) * 100;
            achived = parseInt(achived);
            if(achived>totTarget){
                achived=100;
            }

            $('#weekly_target_graph').html( data['weekly'][1] );
            $('#weekly_achive_graph').html( data['weekly'][0] );
            $('.weekly_bar').css("width", achived+"%");
            $('.weekly_bar').html(achived+"%");

            // Bar for Monthly
            var achived = parseInt(data['monthly'][0]);
            var totTarget = parseInt(data['monthly'][1]);
            achived = (achived / totTarget) * 100;
            achived = parseInt(achived);
            if(achived>totTarget){
                achived=100;
            }

            $('#monthly_target_graph').html( data['monthly'][1] );
            $('#monthly_achive_graph').html( data['monthly'][0] );
            $('.monthly_bar').css("width", achived+"%");
            $('.monthly_bar').html(achived+"%");*/

        }
    });
}

$("#daily").keydown(function (event) {
        allowNumber(event);
    });

$("#weekly").keydown(function (event) {
        allowNumber(event);
    });

$("#monthly").keydown(function (event) {
        allowNumber(event);
    });


$("#fieldForcedata").igGrid({
    dataSource: '/fftarget/showfieldforceDetails',
    autoGenerateColumns: false,
    mergeUnboundColumns: false,
    responseDataKey: "results",
    generateCompactJSONResponse: false, 
    enableUTCDates: true, 
    width: "100%",
    height: "100%",
    columns: [
        { headerText: "FF Name", key: "FFFullName", dataType: "string", width: "25%" },
        { headerText: "Current Beat", key: "BeatName", dataType: "string", width: "30%"},
        { headerText: "Phone No", key: "mobile_no", dataType: "string", width: "10%" },
        { headerText: "RM Name", key: "RMName", dataType: "string", width: "25%" },
        { headerText: "Action", key: "actions", dataType: "string", width: "10%" }
         ],
     features: [
         {
            name: "Sorting",
            type: "remote",
            columnSettings: [
            {columnKey: 'RMName', allowSorting: true },
            {columnKey: 'mobile_no', allowSorting: true },
            {columnKey: 'FFFullName', allowSorting: true },
            {columnKey: 'state_names', allowSorting: false },
            {columnKey: 'actions', allowSorting: false },
            ]
        },
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'SNO', allowFiltering: false },
                {columnKey: 'prmt_tmpl_name', allowFiltering: true },
                {columnKey: 'offer_type', allowFiltering: true },
                {columnKey: 'offer_on', allowFiltering: true },
                {columnKey: 'state_names', allowFiltering: false },
                {columnKey: 'actions', allowFiltering: false },
            ]
        },
        { 
            recordCountKey: 'TotalRecordsCount', 
            chunkIndexUrlKey: 'page', 
            chunkSizeUrlKey: 'pageSize', 
            chunkSize: 20,
            name: 'AppendRowsOnDemand', 
            loadTrigger: 'auto', 
            type: 'remote' 
        }
            
    ],
    primaryKey: 'prmt_tmpl_Id',
    width: '100%',
    height: '500px',
    initialDataBindDepth: 0,
    localSchemaTransform: false,
    
}); 

function updateDetailsData(ffid){

    $('#HiddenInputID').val(ffid);

    // Clear all the field
    $('#daily').val("");
    $('#weekly').val("");
    $('#monthly').val("");

    $("#graph_container").css("display", "none");
    var token  = $("#csrf-token").val();
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "POST",
            url: '/fftarget/getfieldforcedata/' + ffid,
            success: function (data)
            {
                var tableRow='';
                    $("#fieldforcedata td").remove();
                for(var i=0; i<data.length; i++){
                    $("#ffname").val(data[i].firstname);

                    if(data[i].dataFlag==1){
                        tableRow = tableRow + '<tr class="gradeX odd">\
                                <td data-val="list_details" class="prom-font-size">'+data[i].master_lookup_name+'</td>\
                                <td data-val="list_details" class="prom-font-size">'+data[i].target_daily+'</td>\
                                <td data-val="list_details" class="prom-font-size">'+data[i].target_weekly+'</td>\
                                <td data-val="list_details" class="prom-font-size">'+data[i].target_monthly+'</td>\
                                <td data-val="list_details" class="prom-font-size">'+data[i].effective_date+'</td>\
                                <td>@if($deleteFFtarget==1)<a href = "javascript:void(0);" onclick="deleteFieldForceData('+data[i].ff_target_id+',this)"><i class="fa fa-trash-o"></i></a>@endif</td></tr>';
                    }
                }               

                $('#fieldforcedata').append(tableRow);
                $('#fieldforce_data').modal('toggle');

                $('#fieldforce_form_id').formValidation('resetForm');
                $('.form-group').removeClass('has-error');
            }
        });
} 

function deleteFieldForceData(deleteid, element){
    token  = $("#csrf-token").val();
        var fieldforce_delete = confirm("Are you sure you want to delete this fftarget?"), self = $(this);
            if ( fieldforce_delete == true )
            {
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                data: 'deleteData='+deleteid,
                type: "POST",
                url: '/fftarget/deletefieldforce',
                success: function( data ) {
                    
                        $(element).closest('tr').remove();
                    }
            });  
        }    
}

 $( document ).ready(function() {
    var date = new Date();
    $('#date').datepicker({
        dateFormat: 'dd/mm/yy',
    });
    
});
</script>    
@stop   