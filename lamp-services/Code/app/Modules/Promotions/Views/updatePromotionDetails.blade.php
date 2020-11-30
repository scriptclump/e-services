<title>Update New Promotions</title>
@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Update Promotion'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">Update New Promotion</div>
                <div class="tools">
                    <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                </div>
            </div>
            <form  action ="{{url('promotions/updatenewpromotion')}}" method="POST" id = "frm_update_new_tmpl" name = "frm_update_new_tmpl">
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                <input type = "hidden" name = "prmt_det_id" id = "prmt_det_id" value = "{{$getpromotionData->prmt_det_id}}"/>
                <input type = "hidden" id = "select_offer_tmpl"  name =  "select_offer_tmpl" value = "{{$getpromotionData->prmt_tmpl_Id}}"/>
                <input type="hidden" name="gridCallType" id="gridCallType" value="{{$getpromotionData->offer_on}}">
                <input type="hidden" name="applied_ids" id="applied_ids" value="{{$getpromotionData->applied_ids}}">

                <div class="portlet-body">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Offer Type</label>
                                <select id = "select_offer_tmpl"  name =  "select_offer_tmpl" class="form-control" disabled>
                                <option value = "">--please select--</option>
                                @foreach($templateNames as $prodata)
                                    <option value="{{$prodata->prmt_tmpl_Id}}"  @if($prodata->prmt_tmpl_Id == $getpromotionData->prmt_tmpl_Id) {{ "selected" }} @endif> {{$prodata->prmt_tmpl_name}} </option>
                                @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label>Name</label>
                                        <input type="text" class="form-control" name="promotion_name" id="promotion_name" value = "{{$getpromotionData->prmt_det_name}}"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="mt-checkbox-list margt">
                                            <label class="mt-checkbox">
                                                <input type="checkbox" value = "1" id="promotion_status" name = "promotion_status" @if($getpromotionData->prmt_det_status == '1') {{'checked'}}@endif> Active
                                            <span></span>
                                            </label>
                                        </div>
                                    </div>  
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                    <label>Start Date</label>
                                    <div class="input-icon input-icon-sm right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" name="start_date" id="start_date" class="form-control" value = "{{$getpromotionData->start_date}}">
                                    </div>
                                    <div class="cust-error-start-date"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <div class="input-icon input-icon-sm right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" name="end_date" id="end_date" class="form-control" value = "{{$getpromotionData->end_date}}" >
                                        </div>
                                        <div class="cust-error-to-date"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="mt-checkbox-list margt">
                                            <label class="mt-checkbox">
                                            <input type="checkbox"  value="1" id = "is_repeated" name = "is_repeated" @if($getpromotionData->is_repeated == '1') {{'checked'}}@endif> Is Repeated
                                            <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Repeated Day</label>
                                <div class="task-content">
                                    <ul class="task-list">
                                        <li>
                                            <div class="task-checkbox">
                                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                <input type="checkbox" class="checkboxes" value="1" id="all_days" name = "all_days"  @if(isset($getDateAndTime[0])) @if($getDateAndTime[0]->day_name == 'all') {{'checked'}} @endif @endif/>
                                                <span></span>
                                                </label>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-2 text-left">
                                                    <div class="task-title">
                                                        <span class="task-title-sp"> Select All </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="row datesss" id="select_all" style="display:{{$selectDayFlag}};">
                                                        <div class="col-md-1">From</div>
                                                        <div class="col-md-5">
                                                        <div class="form-group" style="margin-top:-4px;">
                                                            <div class="input-icon input-icon-sm">
                                                                <i class="fa fa-clock-o"></i>
                                                                <input type="text" class="form-control input-sm timepicker timepicker-default" style="width:130px;" id = "select_all" name= "select_all_from" value = "{{ isset($getDateAndTime[0]) ? $getDateAndTime[0]->day_time_from : '' }}"> 
                                                            </div>
                                                        </div>
                                                        </div>
                                                        <div class="col-md-1">To</div>
                                                        <div class="col-md-5">
                                                            <div class="form-group" style="margin-top:-4px;">
                                                            <div class="input-icon input-icon-sm">
                                                                <i class="fa fa-clock-o"></i>
                                                                <input type="text" class="form-control input-sm timepicker timepicker-default" style="width:130px;" id = "select_to" name= "select_all_to" value = "{{ isset($getDateAndTime[0]) ? $getDateAndTime[0]->day_time_to : '' }}"> 
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-1 text-right">
                                                    <a id="select_all_down_button" href="javascript:;" class="btn dropdown-toggle" data-toggle="dropdown" style="margin-top:-4px; margin-right:-48px; @if(isset($getDateAndTime[0])) @if($getDateAndTime[0]->day_name == 'all') display:none; @endif @endif" >
                                                        <i class="fa fa-angle-down"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu1 theme-panel pull-right dropdown-custom hold-on-click">

                                                        <?php
                                                        $wekName = array(
                                                                'Sunday',
                                                                'Monday',
                                                                'Tuesday',
                                                                'Wednesday',
                                                                'Thursday',
                                                                'Friday',
                                                                'Saturday'
                                                            );
                                                        ?>
                                                        @for ($i = 0; $i < 7; $i++)

                                                        <?php
                                                            $dayFoundFlag = 0;
                                                            $fromTime = '';
                                                            $toTime = '';
                                                            foreach ($getDateAndTime as $key => $value) {
                                                                if($value->day_name==$wekName[$i]){
                                                                    $dayFoundFlag=1;
                                                                    $fromTime = $value->day_time_from;
                                                                    $toTime = $value->day_time_to;
                                                                }
                                                            }
                                                        ?>

                                                        

                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <div class="mt-checkbox-list" class="checked">
                                                                        <label class="mt-checkbox mt-checkbox-outline">
                                                                             <input type="checkbox" @if( $dayFoundFlag==1 ) {{'checked'}} @endif  id="click_{{$wekName[$i]}}" value="{{$wekName[$i]}}" name = "days[]"/>{{ $wekName[$i] }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <div class="col-md-9">
                                                                <div class="row" id="show_{{$wekName[$i]}}" @if( $dayFoundFlag==0 ) {{'style=display:none;'}} @endif name = "show_{{$wekName[$i]}}">
                                                                    <div class="col-md-1">From</div>
                                                                    <div class="col-md-5">
                                                                        <div class="form-group" style="margin-top:-4px;">
                                                                            <div class="input-icon input-icon-sm">
                                                                            <i class="fa fa-clock-o"></i>
                                                                            <input type="text" class="form-control input-sm timepicker timepicker-default" style="width:130px;" id = "select_day" name= "select_day[]" value="{{$fromTime}}"> </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">To</div>
                                                                        <div class="col-md-5">
                                                                        <div class="form-group" style="margin-top:-4px;">
                                                                            <div class="input-icon input-icon-sm">
                                                                            <i class="fa fa-clock-o"></i>
                                                                                 <input type="text" class="form-control input-sm timepicker timepicker-default" style="width:130px;" id = "select_day_to" name= "select_day_to[]" value="{{$toTime}}"> 
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea rows="1" class="form-control" id = "description" name = "description">{{$getpromotionData->prmt_description}}</textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Label</label>
                                <input type="text" class="form-control"  id = "label" name = "label" value = "{{$getpromotionData->prmt_label}}"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Lock Quantity</label>
                                <input type="text" class="form-control"  id = "prmt_lock_qty" name = "prmt_lock_qty" value = "{{$getpromotionData->prmt_lock_qty}}"/>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="multiple" class="control-label">Business Location</label>
                                    <select id = "state"  name =  "state[]" class="form-control multi-select-search-box" multiple="multiple" style="height:30px">

                                    <option value = "">--please select--</option>
                                    @foreach($stateData as $state)
                                        <option value="{{$state->zone_id}}" @if (in_array($state->zone_id, explode(',', $getpromotionData->prmt_states))) {{ "selected" }} @endif > {{$state->name}} </option>
                                    @endforeach
                                    <option value = "All">All</option>
                                        
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label  class="control-label">Warehouse</label>
                                    <!--<select id="customer_group" name = "customer_group[]" class="form-control select2-multiple" multiple>-->

                                    <select id="warehouse_details" name = "warehouse_details[]" class="form-control multi-select-search-box"  multiple="multiple" style="height:30px">
                                        <option value = "">--Please Select--</option>
                                        @foreach($warehouse_detail as $wh)
                                            <option value = "{{ $wh->le_wh_id }}"
                                            @if(in_array($wh->le_wh_id, $getpromotionData->warehouse)) {{ "selected" }} @endif> {{$wh->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="cust-error-warehouse"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="multiple" class="control-label">Customer Group</label>
                                    <select id="customer_group" name = "customer_group[]" class="form-control multi-select-search-box" multiple="multiple" style="height:30px">
                                        <option value = "">--please select--</option>
                                        @foreach($get_customer as $customer)
                                            <option value = "{{ $customer->value }}" @if (in_array($customer->value, explode(',', $getpromotionData->prmt_customer_group))) {{ "selected" }} @endif> {{ $customer->master_lookup_name }} </option>
                                        @endforeach

                                    </select>
                            </div>
                        </div>
                    </div>
                    <div id="updateloaddata" style="display: none" class="loader" ></div>

                    @if($getpromotionData->prmt_tmpl_Id==1)
                        <div class="itemSection">
                            @include('Promotions::sections/updateItemSection')
                        </div>
                        <div class="slabCondition">
                            @include('Promotions::sections/updateSlabSection')
                        </div>
                    @elseif($getpromotionData->prmt_tmpl_Id==2)
                        <div class="itemSectionWithQty">
                            @include('Promotions::sections/updateItemSectionWithQty')
                        </div>
                        <div class="bundleCondition">
                            @include('Promotions::sections/updateBundleSection')
                        </div>
                    @elseif($getpromotionData->prmt_tmpl_Id==3)
                        <div class="discount_ProductCondition">
                            @include('Promotions::sections/updateDiscountSection')
                        </div>
                        <div class="bundleCondition">
                            @include('Promotions::sections/updateBundleSection')
                        </div>
                    @elseif($getpromotionData->prmt_tmpl_Id==4)
                        <div class="discountOnBillSection">
                            @include('Promotions::sections/updateDiscountBillSection')
                        </div>
                     @elseif($getpromotionData->prmt_tmpl_Id==5)
                    <div class="discountOnBillSection">
                        @include('Promotions::sections/updatecashBackSection')
                    </div>
                    @elseif($getpromotionData->prmt_tmpl_Id==6)
                    <div class="freeSampleSection">
                        @include('Promotions::sections/updateFreeSampleSection')
                    </div>
                    @elseif($getpromotionData->prmt_tmpl_Id==7)
                    <div class="tradeCashbackSection">
                        @include('Promotions::sections/updatetradecashback')
                    </div>
                    @endif
        
                    <br>
                    <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit"class="btn green-meadow">Update Promotion</button>
                            </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@stop

@section('style')
<style type="text/css">
.prom-font-size{ font-size: 12px !important; color: #444 !important;}
.table-advance thead tr th {
    background-color: #f2f2f2 !important;
}
.margt{margin-top:13px;}
.dropdown-menu1 {
    min-width: 589px;
    margin: 5px 8px 0px 30px !important;
	position:absolute; right:-80% !important;
    border: 1px solid #eee;
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    -webkit-border-radius: 0px !important;
    -moz-border-radius: 0px !important;
    -ms-border-radius: 0px !important;
    -o-border-radius: 0px !important;
    border-radius: 0px !important;
}
.theme-panel {
    padding: 10px !important;
}
.margt{margin-top:13px;}
.borderselect{border:1px solid #efefef; padding:4px 2px 4px;}
label {
    padding-bottom: 0px !important;
}
.margt{margin-top:5px;}
.tasks-widget .task-list > li > .task-checkbox {
    float: left;
    width: 30px;
	margin-left:11px !important;
	margin-top:4px !important;
}

.loader {
    position:relative;
    top:40%;
    left: 40%;
    border: 5px solid #f3f3f3;
    border-radius: 50%;
    border-top: 5px solid #d3d3d3;
    width: 50px
;    height: 50px;
    -webkit-animation: spin 2s linear infinite;
    animation: spin 2s linear infinite;
}
</style>
<!--Sumoselect CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/admin/pages/css/tasks.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/javascript" />
@stop

@section('userscript')
<!--Sumoselect JavaScriptFiles-->
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.full.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/components-select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/promotion/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/promotion/bootstrap_framework.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/clockface/js/clockface.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}" type="text/javascript"></script>


<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/promotion/updatenewpromotion.js') }}" type="text/javascript"></script>
@stop

@extends('layouts.footer')