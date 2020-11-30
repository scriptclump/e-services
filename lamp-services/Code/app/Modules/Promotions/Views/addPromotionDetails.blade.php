@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Add New Promotion'); ?>
<div class="row">
<div class="col-md-12">
<div class="portlet light tasks-widget">

    <div class="portlet-title">
        <div class="caption">Add New Promotion</div>
        <div class="tools">
        <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
        </div>
    </div>
    
<form  action ="{{url('promotions/savenewpromotion')}}" method="POST" id = "frm_add_new_tmpl" name = "frm_add_new_tmpl">
<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
<input type="hidden" name="gridCallType" id="gridCallType" value="Bill">
<input type="hidden" name="condition_range" id="condition_range" value="">

    <div class="portlet-body">

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Offer Type</label>
                    <select id = "select_offer_tmpl"  name =  "select_offer_tmpl" class="form-control" onChange="loadPromotionData(this);">
                        <option value = "">--Please Select--</option>
                        @foreach($getpromotionData as $prodata)
                        <option value = "{{$prodata->prmt_tmpl_Id}}">{{$prodata->prmt_tmpl_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                        <label>Name</label>
                            <input type="text" class="form-control" name="promotion_name" id="promotion_name"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="mt-checkbox-list margt">
                                <label class="mt-checkbox">
                                    <input type="checkbox" id="promotion_status" value="1" name = "promotion_status"> Active
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
                        <input type="text" name="start_date" id="start_date" class="form-control" value="">
                        </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>End Date</label>
                            <div class="input-icon input-icon-sm right">
                            <i class="fa fa-calendar"></i>
                            <input type="text" name="end_date" id="end_date" class="form-control" value="" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="mt-checkbox-list margt">
                                <label class="mt-checkbox">
                                <input type="checkbox"  value="1" id = "is_repeated" name = "is_repeated"> Is Repeated
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
                                    <input type="checkbox" class="checkboxes" value="1" id="all_days" name = "all_days"/>
                                    <span></span>
                                    </label>
                                </div>
                                <div class="row blockDays">
                                    <div class="col-md-2 text-left">
                                        <div class="task-title">
                                            <span class="task-title-sp"> Select All </span>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="row" id="select_all">
                                            <div class="col-md-2">From</div>
                                            <div class="col-md-5">
                                            <div class="form-group" style="margin-top:-4px;">
                                                <div class="input-icon input-icon-sm">
                                                    <i class="fa fa-clock-o"></i>
                                                    <input type="text" class="form-control input-sm timepicker timepicker-default" style="width:130px;" id = "select_all" name= "select_all_from"> 
                                                </div>
                                            </div>
                                            </div>
                                            <div class="col-md-1">To</div>
                                            <div class="col-md-4">
                                                <div class="form-group" style="margin-top:-4px;">
                                                <div class="input-icon input-icon-sm">

                                                    <i class="fa fa-clock-o"></i>
                                                    <input type="text" class="form-control input-sm timepicker timepicker-default" style="width:130px;" id = "select_to" name= "select_all_to"> 
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 text-right">
                                        <a id="select_all_down_button" href="javascript:;" class="btn dropdown-toggle" data-toggle="dropdown" style="margin-top:-10px; margin-right:-48px;">
                                            <i class="fa fa-angle-down"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu1 theme-panel pull-right dropdown-custom hold-on-click select_days">

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
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <div class="mt-checkbox-list">
                                                            <label class="mt-checkbox mt-checkbox-outline">
                                                                 <input type="checkbox"  id="click_{{$wekName[$i]}}" value="{{$wekName[$i]}}" name = "days[]"/>{{ $wekName[$i] }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-md-9">
                                                    <div class="row" id="show_{{$wekName[$i]}}" style="display:none;" name = "show_{{$wekName[$i]}}">
                                                        <div class="col-md-1">From</div>
                                                        <div class="col-md-5">
                                                            <div class="form-group" style="margin-top:-4px;">
                                                                <div class="input-icon input-icon-sm">
                                                                <i class="fa fa-clock-o"></i>
                                                                <input type="text" class="form-control input-sm timepicker timepicker-default" style="width:130px;" id = "select_day" name= "select_day[]"> </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">To</div>
                                                            <div class="col-md-5">
                                                            <div class="form-group" style="margin-top:-4px;">
                                                                <div class="input-icon input-icon-sm">
                                                                <i class="fa fa-clock-o"></i>
                                                                     <input type="text" class="form-control input-sm timepicker timepicker-default" style="width:130px;" id = "select_day_to" name= "select_day_to[]"> 
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
                <textarea rows="1" class="form-control" id = "description" name = "description"></textarea>
            </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Label</label>
                    <input type="text" class="form-control"  id = "label" name = "label"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Lock Quantity</label>
                    <input type="text" class="form-control"  id = "prmt_lock_qty" name = "prmt_lock_qty"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="multiple" class="control-label">Business Location</label>
                        <select id="select2_sample2" name =  "state[]" class="form-control multi-select-search-box" multiple style="height:30px">
                            @foreach($getstate as $state)
                                <option value = "{{$state->zone_id}}">{{$state->name}}</option>
                            @endforeach
                        </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label  class="control-label">Warehouse</label>
                        <!--<select id="customer_group" name = "customer_group[]" class="form-control select2-multiple" multiple>-->

                        <select id="warehouse_details" name = "warehouse_details[]" class="form-control multi-select-search-box" multiple style="height:30px">
                            <option value = "">--Please Select--</option>
                            @foreach($warehouse_detail as $wh)
                                <option value = "{{ $wh->le_wh_id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                        <div class="cust-error-warehouse"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="multiple" class="control-label">Customer Group</label>
                        <!--<select id="customer_group" name = "customer_group[]" class="form-control select2-multiple" multiple>-->
                        <select id="select2_sample1" name = "customer_group[]" class="form-control multi-select-search-box" multiple style="height:30px">
                            @foreach($customer_group as $customer)
                                <option value = "{{ $customer->value }}">{{ $customer->master_lookup_name }}</option>
                            @endforeach
                        </select>
                </div>
            </div>
        </div>
        <div id="loaddata" style="display: none" class="loader" ></div>
        <!-- Loading each section for all option -->
        <div class="itemSection" style="display:none">
            @include('Promotions::sections/addItemSection')
        </div>
        <div class="slabCondition" style="display:none">
            @include('Promotions::sections/addSlabSection')
        </div>
        <div class="discount_ProductCondition" style="display:none">
            @include('Promotions::sections/discountSection')
        </div>
        <div class="bundleCondition" style="display:none">
            @include('Promotions::sections/addBundleSection')
        </div> 
        <div class="DiscountOnTotalBill" style="display:none">
            @include('Promotions::sections/DiscountBillSection')
        </div>
        <div class="cashBackonbill" style="display:none">
            @include('Promotions::sections/cashBack')
        </div>
        <div class="freeSamplePromotion" style="display:none">
            @include('Promotions::sections/freesample')
        </div>
        <div class="tradesection" style="display: none">
            @include('Promotions::sections/tradecashback')            
        </div>   
        <br><br>
        <div class="row">
            <div class="col-md-12 text-center">
                <button type="submit"class="btn green-meadow">Save Promotion</button>
            </div>
        </div>
    </div>
</div>
</form>    
</div>
</div>
</div>

@stop

@section('style')
<!--Sumoselect CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
<!-- Ignite UI Required Combined CSS Files -->
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

<style type="text/css">

.addbut{ margin-top: 25px;}

.dropdown-menu1 {
    min-width: 598px;
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
.borderselect{border:1px solid #efefef; padding:4px 2px 4px;}
label {
    padding-bottom: 0px !important;
}
.margt{margin-top:13px;}
.tasks-widget .task-list > li > .task-checkbox {
    float: left;
    width: 30px;
	margin-left:11px !important;
	margin-top:4px !important;
}

#product_grid_pager_label{display: none !important;}
.dropdown-menu{z-index:99999;}
.open > .dropdown-menu {
    z-index: 99999;
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
 bootstrap-timepicker-widget{z-index:99999;}
</style>
@stop

@section('userscript')
<!--Sumoselect JavaScriptFiles-->
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.min.js') }}" type="text/javascript"></script>
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
<script src="{{ URL::asset('assets/admin/pages/scripts/promotion/addpromotion.js') }}" type="text/javascript"></script>

@stop


@extends('layouts.footer')