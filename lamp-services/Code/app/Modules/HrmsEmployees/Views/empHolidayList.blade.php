@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message_ajax"></span>
<span id="failed_message_ajax"></span>

<div class="row">
    <div class="col-md-12">
        <div class="portlet light tasks-widget">
           <div class="portlet-title">
                <div class="caption">Holiday For</div>
                <div class="tools">
                    <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                </div>
            </div>
    
            <form  action ="" method="POST" id = "frm_report_tmpl" name = "frm_report_tmpl">
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="control-label">Holiday Type <span class="required" aria-required="true">*</span></label>
                            <select class="form-control" name="holiday_list_type" id="holiday_list_type">
                                <option value="">Please select</option>
                                @foreach($holidaylist as $details)
                            <option value = "{{$details->emp_group_id}}">{{$details->group_name}}</option>
                            @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="select_report" class="control-label">Year</label>
                                <select id="select_year" name="select_year" class="form-control">
                                    <option value ="">Please Select</option>
                                     <?php  $startdate = 1990;
                                      $enddate = date("Y"); $years = range ($enddate,$startdate); ?>
                                    @foreach($years as $year)
                                    <option value = "{{$year}}">{{$year}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group genra">
                              <button type="button" style = "margin-top: 20px;" class="btn green-meadow" onclick="EmployeholiList();">Go</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                           <div class="actions">
                            @if(isset($Importpermission) and $Importpermission)
                              <a type="button" id="" href="#importholidays" style = "margin-top: 20px;margin-left: 20px;" data-toggle="modal" class="btn green-meadow">Import Holidays</a>
                            @endif
                            </div>
                        </div>       
                    </div>
                </div>

        <table class="table table-striped table-bordered table-advance table-hover" id="holiday_list_all" style="margin-top: 25px;">
            <thead>
                <tr>
                    <th>Occasion</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Day</th>
                    
                </tr>
            </thead>                    
            <tbody>
                @foreach($holidays as $holiday)                     
                <tr>
                    <td>{{$holiday->holiday_name}}</td>
                    <td>{{$holiday->date}}</td>
                    <td>{{$holiday->holiday_type}}</td>
                    <td>{{$holiday->day}}</td>
                </tr>
                @endforeach
            </tbody>   
        </table>  
    <div id="holiday_table_msg1"><p class = "listnot"><?php if(!count($holidays)){?> <p>No Calender Found.</p> <?php } ?><p></div>

</div>
</form>    
</div>
</div>
</div>         
<div class="modal modal-scroll fade in" id="importholidays" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Import Holidays</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form id="importholidayform" action="/employee/importholidayExcel" class="text-center" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12" align="center">
                                <div style="display:none;" id="error-msg" class="alert alert-danger">
                                </div>     
                                <div class="form-group">
                                        <div class="fileUpload btn green-meadow"> <span id="up_text">Choose Holiday Template</span>
                                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                          <input type="file" name="holidaycalender" id="holidaycalender" class="form-control upload"/>
                                        </div>  
                                    </div>
                                </div>
                            </div>
                        <hr/>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" id="uploadfile" class="btn green-meadow">Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-12 text-center">
                        <a href="/employee/downloadholidayimport" class="btn green-meadow">Download Holiday Template</a>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@stop
@section('userscript')
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css" />
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css"/>

<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>


@extends('layouts.footer')
<style>
 
</style>
<script>


    function EmployeholiList(){
        var token  = $("#csrf-token").val();
        var holiday = $("#holiday_list_type").val();  
        var year = $("#select_year").val();  
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            data: {holiday: holiday,year:year},
            url: '/employee/getholidaylistBySelection',
            success: function (respData)
            {
                $("#holiday_list_all td").remove();
                var trHTML = '';
                if(respData[0]){
                for(var i=0;i<respData.length;i++){
                    $("#holiday_table_msg1").empty();
    trHTML += '<tr><td>' + respData[i].holiday_name + '</td><td>' + respData[i].date + '</td><td>' + respData[i].holiday_type + '</td><td>' + respData[i].day + '</td></tr>';    
                }
                $('#holiday_list_all').append(trHTML);
                }else{
                    $('.listnot').append("<b>No Calender Found</b>");
                }
                $("p").removeClass("listnot");
            }
        });
       }

    $('#importholidayform').submit(function (e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var url = $(this).attr('action');
        $('.spinnerQueue').show();
        $('.close').trigger('click');
        $.ajax({
            headers: {'X-CSRF-TOKEN': csrf_token},
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            beforeSend: function (xhr) {
                $('.spinnerQueue').show();
                $('.close').trigger('click');
            },
            success: function (data) {
                $('.spinnerQueue').hide();
                $('.close').trigger('click');
                $("#uploadfile").removeAttr("disabled");
                if(data.status == "success"){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data.message+'</div></div>' );
                    $(".alert-success").fadeOut(30000);
                }else{
                    $("#failed_message_ajax").html('<div class="alert alert-danger">'+data.message+'</div></div>' );
                    $(".alert-danger").fadeOut(30000);
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });
    
        $("#importholidays").on('hide.bs.modal', function () {
            $('#importholidayform')[0].reset();
            $("#uploadfile").removeAttr("disabled");
        });
        $('a.link').on('click touchend', function(e) {
            var link = $(this).attr('href');
            window.location = link;
        });
    });

</script>
@stop
@extends('layouts.footer')