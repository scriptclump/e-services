<!-- Purpose : "Used to Business Partners Dashboard On Screens";
Technology   : Jquery  , ajax , bootstrap ;
Author : Deepak Tiwari -->

@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<ul class="page-breadcrumb breadcrumb">
    <li><a href="/">{{trans('businessPartnersDashboard.dashboard_heads.heads_home')}}</a><i class="fa fa-angle-right" aria-hidden="true"></i>
    </li><li><span class="bread-color">{{trans('businessPartnersDashboard.dashboard_heads.today_reports')}}</span><i class="fa fa-angle-right" aria-hidden="true"></i></li>
    <span style="color : #795548; float:right;">
        <i class="fa fa-clock-o" aria-hidden="true"> {{trans('businessPartnersDashboard.dashboard_heads.last_updated')}}: 
            <span id="last_updated">@if(isset($last_updated)){{$last_updated}}@endif</span>
        </i>
    <span>
</ul>

<div class="page-head">
    <div>
        <div style="width: 58%; float: left;">
            <div class="page-title">
                <h1>{{trans('businessPartnersDashboard.dashboard_heads.today_reports')}}</h1> 
                <!-- <div class="dc_dropdown"> -->
                  
        <!-- </div>   --> 
        </div>
        </div>
        <div class="customDateArea" id="customDatesView">
            <div class="customDateWidth" style="width: 25% !important;">
                <div class="form-group" id="customDatePickerZone">
                    <div class="input-daterange input-group" id="datepicker">
                        <input type="text" class="form-control" name="fromDate" id="fromDate" autocomplete="Off"/>
                        <span class="input-group-addon">to</span>
                        <input type="text" class="form-control" name="toDate" id="toDate" autocomplete="Off"/>
                    </div>
                </div>
            </div>
            <div class="customDateWidthBtn">
                <input class="form-control" type="button" id="customDateWidthSubmit" value="Go" />
            </div>
        </div>
        <div class="dashboard_dropdown" style="width: 10% !important;">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <select class="form-control" id="dashboard_filter_dates">
                <option value="today">{{trans('businessPartnersDashboard.dashboard_time.today')}}</option>
                <option value="yesterday">{{trans('businessPartnersDashboard.dashboard_time.yesterday')}}</option>
                <option value="wtd">{{trans('businessPartnersDashboard.dashboard_time.wtd')}}</option>
                <option value="mtd">{{trans('businessPartnersDashboard.dashboard_time.mtd')}}</option>
                <option value="ytd">{{trans('businessPartnersDashboard.dashboard_time.ytd')}}</option>
                <option value="custom">{{trans('businessPartnersDashboard.dashboard_time.custom')}}</option>
            </select>
        </div>
    </div>
</div>

<!-- To get businessUnit dropDown Value -->
<div class="row" style="padding-bottom: 14px; width: 80%; float: left;">
                  <div class="col-md-4">  
                      <input type="hidden" id="bu_id" value="{{$buid}}">
                      <select class="form-control select2me" id="dc_all_legalentity" onchange="myChangeFunction(this)">
                        <option value=''>Please Select</option>
                     </select>
                  </div>
              

</div>

<!-- peice of code is used to show top boxes with value -->
 <div class="row">
        <div class="item active">
        <?php 
            $rowNumber = 6;
            $count = 0;
            if(isset($BusinessPartnerDatails))
            foreach ($BusinessPartnerDatails as $key => $value)
            {
                $temp = 1;
                if ($count >= $rowNumber)
                {
                    $count = 0;
                    ?>
        </div>
        <div class="item">
        <?php } ?>
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12" style="padding-right:0px;">
                <div class="dashboard-stat2 bordered">
                    <?php
                        if(count($value) > 1){
                        foreach($value as $boxElments)
                        {
                    ?>                
                        <div class="display">
                            <div class="number">
                                    <h3 class="font-green-sharp down-count">
                                    <span data-counter="counterup">
                                            <?php
                                                $field_key = property_exists($boxElments, 'key') ? $boxElments->key : '';                                        
                                                $field_id = '';
                                                if($field_key != '')
                                                {
                                                    $field_id = strtolower(preg_replace('/[^a-zA-Z0-9_.]/', '_', $field_key));                 
                                                }                    
                                                $val = property_exists($boxElments, 'val') ? $boxElments->val : 0;
                                                $per = property_exists($boxElments, 'per') ? $boxElments->per : '';
                                                $link = property_exists($boxElments, 'link') ? $boxElments->link : "javascript:void(0);"; 
                                            ?>                                    
                                        <span class="data_value" id="<?php echo $field_id; ?>">
                                                <?php echo $val; ?>
                                        </span>
                                            @if($per != '')
                                                <span class="data_per" id="data_per_<?php echo $field_id; ?>">{{ $per }}</span>
                                            @endif
                                        <div class="loader">Loading...</div>
                                    </span>
                                </h3>
                                    <div class="progress-info">
                                        <div class="status">
                                            <div class="status-title"> <a href=<?php echo $link;?> id="<?php echo $field_id; ?>" target="_blank"><?php echo $field_key; ?> </a> </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                            <?php if($temp == 1) { ?>
                                <div class="progress-info">
                                    <div class="progress">
                                        <span style="width: 100%;" class="progress-bar progress-bar-success green-sharp">
                                        </span>
                                    </div>
                                </div>
                            <?php } $temp++; ?>
                        <?php } ?>
                    <?php }else{ ?>
                        <div class="display">
                            <div class="number">
                                <h3 class="font-green-sharp">
                                    <span data-counter="counterup">
                                        <?php
                                            $details = isset($value[0]) ? $value[0] : [];
                                            $field_key = '';
                                            $field_id = '';
                                            $value = 0;
                                            $pre = '';                                                                                    
                                            if(!empty($details)){
                                                $field_key = property_exists($details, 'key') ? $details->key : '';
                                                if($field_key != '')
                                                {
                                                    $field_id = strtolower(preg_replace('/[^a-zA-Z0-9_.]/', '_', $field_key));                                                                                            
                                                }                    
                                                $val = property_exists($details, 'val') ? $details->val : 0;
                                                $per = property_exists($details, 'per') ? $details->per : '';
                                                $link = property_exists($details, 'link') ? $details->link : "javascript:void(0);";    
                                            }
                                        ?>                                    
                                        <span class="data_value" id="<?php echo $field_id; ?>">
                                            <?php echo $val; ?>
                                        </span>
                                        @if($per != '')
                                            <span class="data_per" id="data_per_<?php echo $field_id; ?>">{{ $per }}</span>
                                        @endif
                                        <div class="loader">Loading...</div>
                                    </span>
                                </h3>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                                <span style="width: 100%;" class="progress-bar progress-bar-success green-sharp">
                                </span>
                            </div>
                            <div class="status">
                                <div class="status-title"> <a href="<?php echo $link;?>" id="<?php echo $field_id; ?>" target="_blank"><?php echo $field_key; ?> </a> </div>
                            </div>
                        </div>
                    <?php } ?>                    
                    </div>
                </div>
        <?php if ($count >= $rowNumber)
        { ?>
                
        </div>
            <?php } $count++; ?>
        <?php } ?>
    
        </div>
</div>
<!-- END of top  -->


<!-- used to botton tabular data -->

<!-- Checking weather  user is having access to export feature or not -->
<!-- @if($ExportAccess == 1)
<div id="Div1">
    <button style="width:10%; margin-bottom :10px; float: right;" id="dashboard_export" class="btn btn-success" onClick = "switchVisible()"> Export </button>
</div>
@endif -->

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-body">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs">
                        <li class="ch_tabs active">
                            <a href="#ff_list_tab" id="ff_list" data-toggle="tab">{{trans('businessPartnersDashboard.dashboard_bottom_grid.businessInfo')}}</a>
                        </li>
                        <!-- Removed Invertory Tab UI Button here -->
                        <div class="actions" style="margin-right:10px; "> 
                        @if($ExportAccess == 1)
                        <div id="Div1">
                            <button style="width:10%; margin-bottom :10px; float: right;" id="dashboard_export" class="btn green-meadow pull-right" onClick = "switchVisible()"> Export </button>
                        </div>
                        @endif
                           
                        </div>
                       <form id="sales_details_export" action="businessPartners/getPartnersReport" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="exportbuId" id="exportbuId">
                            <div class="customDateArea" id="Div2" style="width:30%; float:right;">
                            <div class="customDateWidth" style="width: 75% !important;">
                                <div class="form-group" id="customDatePicker">
                                    <div class="input-daterange input-group" id="datepicker">
                                        <input type="text" id="fsdate" name="fromdate" class="form-control" placeholder="From Date" autocomplete="Off" required/>
                                        <span class="input-group-addon">to</span>
                                        <input type="text" id="todate" name="todate" class="form-control" placeholder="To Date" autocomplete="Off"/>
                                    </div>
                                </div>
                            </div>
                            <div class="customDateWidthBtn">
                            <button class="btn green-meadow"  id="salesexport">Go</button>
                            <input id="csrf-token" type="hidden" name="_token" value="{{csrf_token()}}">
                            </div>
                            </div>
                       </form>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="ff_list_tab">
                            <div id="ff_list_error" class="hideError" align="center"></div>
                            <div id="ff_list_table"><table id="dashboard_partners_list" style="white-space: nowrap;"></table></div>
                        </div>
                        <!-- Removed Invertory Tab UI Area here -->
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
<!-- End of Bottom table -->

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
    .textCenterAlign {
        text-align:center; !important;
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
.flex {
     display: flex;

#Div2 {
  display: none;
}
</style>


<!-- Custom Dashboard JS -->
<script src="{{ URL::asset('assets/global/scripts/businessPartners-customs.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<!-- External files for exporting -->
<script src="{{ URL::asset('assets/global/plugins/igniteui/modules/infragistics.documents.core.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/modules/infragistics.util.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/modules/infragistics.gridexcelexporter.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/modules/infragistics.excel.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/Blob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/filesaver.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css"/>

<!-- End of grid related CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/components-select2.min.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
    
<!----- peice of code used to get DC/FC dropDown value---- from businessPartners/getbu api  through ajax call-->

<!-- External files for exporting -->
    <script>
        function switchVisible() {
            if (document.getElementById('Div1')) {

                if (document.getElementById('Div1').style.display == 'none') {
                    document.getElementById('Div1').style.display = 'block';
                    document.getElementById('Div2').style.display = 'none';
                }
                else {
                    document.getElementById('Div1').style.display = 'none';
                    document.getElementById('Div2').style.display = 'block';
                }
            }
        }  

        function myChangeFunction(input1) {
        var BUID = input1.value;
      //  $('BUID').append(BUID);
       // console.log("BUID",  BUID)
        }
       
        $(function(){
            // Custom Title for The Dashboard
            var token=$('#csrf-token').val();
            var buid=$('#bu_id').val(); 
            $.ajax({
            type:'get',
            headers: {'X-CSRF-TOKEN': token},
            url:'/businessPartners/getbu',
            success: function(res){
                res.forEach(data=>{
                    $('#dc_all_legalentity').append(data);
                });
                $("#dc_all_legalentity").select2("val", buid);
            }

        });
        });

        // export features 
        $("#salesexport").click(function(){
            var csrf_token = $('#csrf-token').val();
            var buid = $('#dc_all_legalentity').val();
            $('#exportbuId').val(buid)
            var todate=$("#todate").val();
            var fromdate=$("#fromdate").val();
            /*if(){

            }else{*/
                $('#sales_details_export').submit();
            //} 
        })
         </script>
@stop
@extends('layouts.footer')