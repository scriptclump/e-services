@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<ul class="page-breadcrumb breadcrumb">
    <li><a href="/">{{trans('dashboard.dashboard_heads.heads_home')}}</a><i class="fa fa-angle-right" aria-hidden="true"></i>
    </li><li><span class="bread-color">{{trans('dashboard.dashboard_heads.dpr_sheets')}}</span><i class="fa fa-angle-right" aria-hidden="true"></i></li>
</ul>
<div class="page-head ">
               <div class="col-md-2" style="margin-left: -15px;">                     
                     <select class="form-control select2me" id="dc_fc_selection">
                        <option value=''>Please Select</option>
                        <option value='1016'>DC </option>
                        <option value='1014'>FC </option>
                     </select>
                </div>
                 <div class="col-md-2 customDisplayList" id="customDisplayView">                    
                     <select class="form-control select2me" id="dc_fc_list">                           
                     </select>
                    </div>
                 <div class="col-md-2" style="margin-left: -70px;" >
                     <div class="dashboard_dropdown">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <select class="form-control" id="dashboard_filter_dates" onchange="showDiv('customDatesView',this)">
                        <option value="wtd">{{trans('dashboard.dashboard_time.wtd')}}</option>
                        <option value="mtd">{{trans('dashboard.dashboard_time.mtd')}}</option>
                        <option value="quarter">QTD</option>
                        <option value="ytd">{{trans('dashboard.dashboard_time.ytd')}}</option>
                        <option value="custom">{{trans('dashboard.dashboard_time.custom')}}</option>
                        </select>
                     </div> 
                 </div>
                  <div class="col-md-4 customDateArea" id="customDatesView">
                        <div class="customDateWidth" >
                            <div class="form-group" id="customDatePickerZone">
                              <div class="input-daterange input-group" id="datepicker">
                                 <input type="text" class="form-control" name="fromDate" id="fromDate" placeholder="From Date" />                              
                                <span class="input-group-addon">to</span>
                                <input type="text" class="form-control" name="toDate" id="toDate" placeholder="To Date" />
                              </div>
                            </div>
                         </div>
                    </div>          
                 <div class="col-md-1">
                    <div class="customDateWidthBtn">
                        <input class="form-control" type="button" style="background-color: #4CAF5;0" id="customDateWidthSubmit" value="Go" />
                    </div>
                </div>
                
            </div>
        </div>
    </div>
@stop

@section('style')
<!-- Custom Dashboard Style -->
<link href="{{ URL::asset('assets/global/css/dashboard-custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />

@stop

@section('userscript')
<style type="text/css">
    .textRightAlign {
        text-align:right !important;
    }
    .bu1{
    margin-left: 10px;
    font-size: 18px;
    color:#000000;
    }
    .bu2{
        margin-left: 20px;
        font-size: 16px;
        color:#1d1d1d;
    }.bu3{
        margin-left: 30px;
        font-size: 15px;
        color:#3a3a3a;
    }.bu4{
        margin-left: 40px;
        font-size: 14px;
        color:#535353;
    }.bu5{
        margin-left: 50px;
        font-size: 13px;
        color: #6d6c6c;
    }.bu6{
        margin-left: 60px;
        font-size: 11px;
        color:#868383;
}
</style>
<!-- Custom Dashboard JS -->
<script src="{{ URL::asset('assets/global/scripts/dpr-sheet.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.2/socket.io.js"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    
    function showDiv(divId, element)
   {
    document.getElementById(divId).style.display = element.value == 'custom' ? 'block' : 'none';
   }
</script>
@stop
@extends('layouts.footer')