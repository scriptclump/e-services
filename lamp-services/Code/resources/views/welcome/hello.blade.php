<!-- Please Note that we are not using the below page for our Landing Page -->
<!-- This page is Changed to Code/app/Modules/Dashboard/Views/hello.blade.php -->
<!-- Please consider the above page for any changes to be made (if so). -->
@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<ul class="page-breadcrumb breadcrumb">
    <li><a href="">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i>
    </li><li><span class="bread-color">Today Summary Reports</span><i class="fa fa-angle-right" aria-hidden="true"></i></li>
    <span style="color : #795548; float:right;">
        <i class="fa fa-clock-o" aria-hidden="true"> Last Updated: 
            <span id="last_updated"><?php echo $last_updated;?></span>
        </i>
    <span>
</ul>
<div class="page-head">
    <div class="page-title">
        <h1>Today Summary Reports</h1>   
    </div>
    <div class="dashboard_dropdown">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <select class="form-control" id="dashboard_filter_dates">
            <option value="today">TODAY</option>
            <option value="wtd">WTD</option>
            <option value="mtd">MTD</option>
            <option value="ytd">YTD</option>
        </select>
    </div>
</div>
<?php

$retailersList['le_code'] = 'Retailer Code';
$retailersList['business_legal_name'] = 'Shop Name';
$retailersList['legal_entity_type'] = 'Customer Type';
$retailersList['business_type'] = 'Segment';
$retailersList['name'] = 'Name';
$retailersList['mobile_no'] = 'Mobile';
$retailersList['volume_class'] = 'Volume Class';
$retailersList['No_of_shutters'] = 'Shutters#';
$retailersList['suppliers'] = 'Other Suppliers';
$retailersList['business_start_time'] = 'Business Start Time';
$retailersList['business_end_time'] = 'Business End Time';
//$retailersList['preference_value'] = 'Prefered Time';
$retailersList['address'] = 'Address';
$retailersList['area'] = 'Area';
$retailersList['beat'] = 'Beat';
$retailersList['city'] = 'City';
$retailersList['state'] = 'State';
$retailersList['pincode'] = 'Pincode';
$retailersList['smartphone'] = 'Is Smart Phone';
$retailersList['network'] = 'Internet Availability';
$retailersList['orders'] = '#Orders';
$retailersList['last_order_date'] = 'Last Ordered Date';
$retailersList['created_at'] = 'Created Date';
$retailersList['created_time'] = 'Created Time';
$retailersList['created_by'] = 'Created By';
$retailersList['updated_at'] = 'Updated Date';
$retailersList['updated_time'] = 'Updated Time';
$retailersList['updated_by'] = 'Updated By';
$retailersList['latitude'] = 'Latitude';
$retailersList['longitude'] = 'Longitude';
//$retailersList['is_approved'] = 'Is Approved';

$self_orders_list['name'] = 'Name';
$self_orders_list['shop_name'] = 'Shop Name';
$self_orders_list['phone_no'] = 'Mobile No';
$self_orders_list['order_code'] = 'Order Code';
$self_orders_list['order_date'] = 'Order Date';
$self_orders_list['dc_name'] = 'DC Name';
$self_orders_list['hub_name'] = 'Hub Name';
$self_orders_list['beat_name'] = 'Beat Name';
$self_orders_list['order_status'] = 'Status';
$self_orders_list['ff_assoc'] = 'FF Associate';
$self_orders_list['total'] = 'Total';

?>
    <div class="row">
        <div class="item active">
        <?php 
        $rowNumber = 6;
        $count = 0;
            foreach ($order_details['dashboard'] as $key => $value)
            {
                $temp = 1;
                ?>
        <?php
                if ($count >= $rowNumber)
                {
                    $count = 0;
                    ?>
        </div>
        <div class="item">
                
        <?php } ?>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-12" style="padding-right:0px;">
                    <div class="dashboard-stat2 bordered">
                    <?php //echo "<pre>";print_r($value);die; ?>
                    <?php if(count($value) > 1){ ?>
                        <?php foreach($value as $boxElments){ ?>                
                        <div class="display">
                            <div class="number">
                                    <h3 class="font-green-sharp down-count">
                                    <span data-counter="counterup">
                                            <?php $field_key = property_exists($boxElments, 'key') ? $boxElments->key : '';                                        
                                                $field_id = '';
                                                if($field_key != '')
                                                {
                                                    $field_id = strtolower(preg_replace('/[^a-zA-Z0-9_.]/', '_', $field_key));                                                                                            
                                                }                    
                                                $val = property_exists($boxElments, 'val') ? $boxElments->val : 0;
                                                $per = property_exists($boxElments, 'per') ? $boxElments->per : ''; 
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
                                            <div class="status-title"> <a href="javascript:void(0);" id="<?php echo $field_id; ?>"><?php echo $field_key; ?> </a> </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                            <?php if($temp == 1) { ?>
                                <div class="progress-info">
                                    <div class="progress">
                                        <span style="width: 100%;" class="progress-bar progress-bar-success green-sharp">
                                            <span class="sr-only">76% progress</span>
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
                                            }
                                        ?>                                    
                                        <span class="data_value" id="<?php echo $field_id; ?>">
                                            <?php echo $val; ?>
                                        </span>
                                        @if($per != '')
                                            <span class="data_per" id="data_per">{{ $per }}</span>
                                        @endif
                                        <div class="loader">Loading...</div>
                                    </span>
                                </h3>
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                                <span style="width: 100%;" class="progress-bar progress-bar-success green-sharp">
                                    <span class="sr-only">76% progress</span>
                                </span>
                            </div>
                            <div class="status">
                                <div class="status-title"> <a href="javascript:void(0);" id="<?php echo $field_id; ?>"><?php echo $field_key; ?> </a> </div>
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

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-body">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs active">
                        <li class="ch_tabs active ff_list">
                            <a href="#ff_list" data-toggle="tab">Field Force List</a>
                        </li>
                        <li class="ch_tabs retailer_list">
                            <a href="#retailer_list" id="retailer_list_tab" data-toggle="tab">New On-Boarded Outlets</a>
                        </li>
                        <li class="ch_tabs retailer_list">
                            <a href="#self_orders_list" id="self_orders_list_tab" data-toggle="tab">Self Orders</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="ff_list">
                        <div class="col-md-12 text-right suggest"><b>* All Amounts in <i class="fa fa-inr" aria-hidden="true"></i></b></div>

                <table class="table table-striped table-bordered table-hover" id="dashboard_table" width="100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>HUB Name</th>
                            <th>TGM</th>
                            <th>Orders</th>
                            <th>First Order</th>
                            <th>Calls</th>
                            <th>First Call</th>
                            <th>TBV</th>
                            <th>UOB</th>
                            <th>ABV</th>
                            <th>TLC</th>
                            <th>ULC</th>
                            <th>ALC</th>
                            <th>Contribution %</th>
                            <th>Success %</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($order_details['customer_info']))
                        @foreach($order_details['customer_info'] as $customerInfo)
                        <tr class="odd gradeX">
                            <td>@if(property_exists($customerInfo, 'NAME')) {{ $customerInfo->NAME }} @else '' @endif</td>
                            <td>@if(property_exists($customerInfo, 'hub_name')) {{ $customerInfo->hub_name }} @else '' @endif</td>
                            <td>@if(property_exists($customerInfo, 'margin')) {{ $customerInfo->margin }} @else 0.00 @endif</td>
                            <td>@if(property_exists($customerInfo, 'order_cnt')) {{ $customerInfo->order_cnt }} @else 0 @endif</td>
                            <td>@if(property_exists($customerInfo, 'first_order')) {{ $customerInfo->first_order }} @else 0                                                         @endif</td>
                            <td>@if(property_exists($customerInfo, 'calls_cnt')) {{ $customerInfo->calls_cnt }} @else 0 @endif</td>
                            <td>@if(property_exists($customerInfo, 'first_call')) {{ $customerInfo->first_call }} @else 0 @endif</td>
                            <td>@if(property_exists($customerInfo, 'tbv')) {{ $customerInfo->tbv }} @else 0 @endif</td>
                            <td>@if(property_exists($customerInfo, 'UOB')) {{ $customerInfo->UOB }} @else 0 @endif</td>
                            <td>@if(property_exists($customerInfo, 'ABV')) {{ $customerInfo->ABV }} @else 0 @endif</td>
                            <td>@if(property_exists($customerInfo, 'TLC')) {{ $customerInfo->TLC }} @else 0 @endif</td>
                            <td>@if(property_exists($customerInfo, 'ULC')) {{ $customerInfo->ULC }} @else 0 @endif</td>
                            <td>@if(property_exists($customerInfo, 'ALC')) {{ $customerInfo->ALC }} @else 0 @endif</td>
                            <td>@if(property_exists($customerInfo, 'Contribution')) {{ $customerInfo->Contribution }} @else 0 @endif</td>
                            <td>@if(property_exists($customerInfo, 'success_rate')) {{ $customerInfo->success_rate }} @else 0 @endif</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>

                        </div>
                        <div class="tab-pane" id="retailer_list">

                            <table class="stripe row-border order-column" id="dashboard_customer_table">
                                <thead>
                                    <tr>
                                        @foreach($retailersList as $key => $value)
                                        <th>{{ $value }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="self_orders_list">
                            <div class="col-md-12 text-right suggest"><b>* All Amounts in <i class="fa fa-inr" aria-hidden="true"></i></b></div>
                            <table class="table table-striped table-bordered table-hover" id="self_orders_table">
                            <thead>
                                <tr>
                                    @if(isset($self_orders_list))
                                        @foreach($self_orders_list as $key => $value)
                                            <th>{{ $value }}</th>
                                        @endforeach
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($order_details['self_orders']))
                                    @foreach($order_details['self_orders'] as $selfOrdersInfo)
                                        <tr class="odd gradeX">
                                            <td>
                                                @if(property_exists($selfOrdersInfo, 'firstname')) {{ $selfOrdersInfo->firstname }} @else '' @endif
                                                @if(property_exists($selfOrdersInfo, 'lastname')) {{ $selfOrdersInfo->lastname }} @else '' @endif
                                            </td>
                                            <td>
                                                @if(property_exists($selfOrdersInfo, 'shop_name')) {{ $selfOrdersInfo->shop_name }} @else '' @endif
                                            </td>
                                            <td>
                                                @if(property_exists($selfOrdersInfo, 'phone_no')) {{ $selfOrdersInfo->phone_no }} @else '' @endif
                                            </td>
                                            <td>
                                                @if(property_exists($selfOrdersInfo, 'order_code')) {{ $selfOrdersInfo->order_code }} @else '' @endif
                                            </td>
                                            <td>
                                                @if(property_exists($selfOrdersInfo, 'order_date')) {{ $selfOrdersInfo->order_date }} @else '' @endif
                                            </td>
                                            <td>
                                                @if(property_exists($selfOrdersInfo, 'dc_name')) {{ $selfOrdersInfo->dc_name }} @else '' @endif
                                            </td>
                                            <td>
                                                @if(property_exists($selfOrdersInfo, 'hub_name')) {{ $selfOrdersInfo->hub_name }} @else '' @endif
                                            </td>
                                            <td>
                                                @if(property_exists($selfOrdersInfo, 'beat_name')) {{ $selfOrdersInfo->beat_name }} @else '' @endif
                                            </td>
                                            <td>
                                                @if(property_exists($selfOrdersInfo, 'order_status')) {{ $selfOrdersInfo->order_status }} @else '' @endif
                                            </td>
                                            <td>
                                                @if(property_exists($selfOrdersInfo, 'ff_assoc')) {{ $selfOrdersInfo->ff_assoc }} @else '' @endif
                                            </td>
                                            <td>
                                                @if(property_exists($selfOrdersInfo, 'total')) {{ $selfOrdersInfo->total }} @else '' @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            </table>
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
.right_align.sorting.sorting_asc.width_change
{
	width: 50px !important;
}
.right_align.sorting.sorting_desc.width_change
{
	width: 50px !important;
}
.right_align.width_change.sorting
{
	width: 50px !important;
}
.table-scrollable table tr td {
    font-size: 12px !important; white-space: nowrap;
}


.table-scrollable > .table-bordered > thead > tr:last-child > th{
    font-size: 12px !important;
}

table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>td.sorting {
    padding-right: 20px !important;
	padding-left: 5px !important;
}

.right_align{ text-align: right; }
.data_value{word-break:break-word;}
.data_per{ font-size:14px !important; color:#847f7f;}
.down-count{ margin-top:6px !important;}

    .item{
        margin-bottom: 10px !important;
    clear: both;
        display: list-item;
    }
    .dashboard-stat2 {
        margin-bottom: 13px !important;
    }
    .dashboard-stat2 .progress-info .status a {
        padding:0px !important;
   }
   .suggest {
        font-size: 11px;
   }
   #ff_list{
        margin-top: -5px;
   }
/*.dashboard-stat2 .progress-info .status {
    font-size: 9px !important;
    }
.dashboard-stat2 .display .number small {
    font-size: 9px !important;
}*/
    .page-content {
        background:none !important;
    }
    .dashboard-stat2 .display .number h3 {
        font-size: 22px !important; 
    }
    .dashboard_dropdown{
        float: right;
    }
    .col-lg-3 {
    width: 24.7%;
}
.loader {
        margin:1em auto;
  font-size: 10px;
        width: 1em;
        height: 1em;
        border-radius: 50%;
        position: absolute;
  text-indent: -9999em;
        -webkit-animation: load5 1.1s infinite ease;
        animation: load5 1.1s infinite ease;
  -webkit-transform: translateZ(0);
  -ms-transform: translateZ(0);
  transform: translateZ(0);
        z-index:999;
        top:22em;
        left:30em;
}
    @-webkit-keyframes load5 {
        0%,
        100% {
          box-shadow: 0em -2.6em 0em 0em #8fa4ed, 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.5), -1.8em -1.8em 0 0em rgba(143,164,237, 0.7);
        }
        12.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.7), 1.8em -1.8em 0 0em #8fa4ed, 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.5);
}
        25% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.5), 1.8em -1.8em 0 0em rgba(143,164,237, 0.7), 2.5em 0em 0 0em #8fa4ed, 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
}
        37.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.5), 2.5em 0em 0 0em rgba(143,164,237, 0.7), 1.75em 1.75em 0 0em #8fa4ed, 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        50% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.5), 1.75em 1.75em 0 0em rgba(143,164,237, 0.7), 0em 2.5em 0 0em #8fa4ed, -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
}
        62.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.5), 0em 2.5em 0 0em rgba(143,164,237, 0.7), -1.8em 1.8em 0 0em #8fa4ed, -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        75% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.5), -1.8em 1.8em 0 0em rgba(143,164,237, 0.7), -2.6em 0em 0 0em #8fa4ed, -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
  }
        87.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.5), -2.6em 0em 0 0em rgba(143,164,237, 0.7), -1.8em -1.8em 0 0em #8fa4ed;
  }
}
    @keyframes load5 {
  0%,
  100% {
          box-shadow: 0em -2.6em 0em 0em #8fa4ed, 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.5), -1.8em -1.8em 0 0em rgba(143,164,237, 0.7);
        }
        12.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.7), 1.8em -1.8em 0 0em #8fa4ed, 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.5);
        }
        25% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.5), 1.8em -1.8em 0 0em rgba(143,164,237, 0.7), 2.5em 0em 0 0em #8fa4ed, 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        37.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.5), 2.5em 0em 0 0em rgba(143,164,237, 0.7), 1.75em 1.75em 0 0em #8fa4ed, 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        50% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.5), 1.75em 1.75em 0 0em rgba(143,164,237, 0.7), 0em 2.5em 0 0em #8fa4ed, -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        62.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.5), 0em 2.5em 0 0em rgba(143,164,237, 0.7), -1.8em 1.8em 0 0em #8fa4ed, -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        75% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.5), -1.8em 1.8em 0 0em rgba(143,164,237, 0.7), -2.6em 0em 0 0em #8fa4ed, -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
  }
        87.5% {
          box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.5), -2.6em 0em 0 0em rgba(143,164,237, 0.7), -1.8em -1.8em 0 0em #8fa4ed;
  }
}
</style>
<link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/admin/pages/css/dashboard/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('userscript')
<script src="{{ URL::asset('assets/admin/pages/scripts/dashboard/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function () {
    $('[class="loader"]').hide();
    $('#dashboard_table').dataTable({
							columnDefs: [
								{ targets: [ 2,3,4,5,6,7,8,9,10,11,12], className: 'right_align'},
								{ targets: [ 13,14], className: 'right_align width_change'}
							]
						});
        $('#self_orders_table').dataTable();
        retailerDeails();
        selfOrderDetails();
});
$('#dashboard_filter_dates').change(function () {
    $('[class="loader"]').show();
    $('[class="data_value"]').text(0);    
//        $('#dashboard_table tbody').empty();
        var table = $('#dashboard_table').DataTable();
        table.clear().draw();
    var filterData = $(this).val();
    $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: '/',
        type: 'POST',
        data: {'filter_date': filterData},
        dataType: 'JSON',
        success: function (data) {
            if ( data.order_details != 'undefined' )
            {
                $.each(data.order_details, function (key, value) {
//                        var temp = key.toLowerCase().replace(' ', '_');
                        var test = key.toLowerCase();
                        var temp = test.replace(/[^A-Z0-9]/ig, "_");
                    if ( 'customer_info' == temp )
                    {
                        $.each(value, function (key1, customers) {
                                var callSuccessRate = 0;
                                if(customers.calls_cnt > 0)
                                {
                                    callSuccessRate = ((customers.order_cnt * 100)/customers.calls_cnt).toFixed(2);
                                }
                                table.row.add([
                                   customers.NAME,
                                   customers.hub_name,
                                   customers.margin,
                                   customers.order_cnt,
                                   customers.first_order,
                                   customers.calls_cnt, 
                                   customers.first_call,
                                   customers.tbv,
                                   customers.UOB,
                                   customers.ABV,
                                   customers.TLC,
                                   customers.ULC,
                                   customers.ALC,
                                   customers.Contribution,
								   customers.success_rate
                                   //callSuccessRate
                               ]).draw( false );
                        });
                        }else if ( 'dashboard' == temp ){
                            $.each(value, function (key2, dashboard) {
                                //console.log(dashboard.length);
                                $.each(dashboard, function (key3, dashboardData) {
                                    var key3 = dashboardData.key;
                                    var val3 = dashboardData.val;
                                    var per3 = dashboardData.per;
									if(key3 != null){
                                    var test3 = key3.toLowerCase();
                                    var temp3 = test3.replace(/[^A-Z0-9]/ig, "_");
                                    console.log(temp3);
                                    $('#' + temp3).text(val3);
                                    $('#data_per_' + temp3).text(per3);
									}
                                });                                
                            });
                    }else if ( 'productivity' == temp )
                    {
                            $('#' + temp).text(parseFloat(value).toFixed(2));
                    } else {
                        $('#' + temp).text(value);
                    }
                });
            }
        }
    });
    $('[class="loader"]').hide();
        retailerDeails();
    });
//    $('#retailer_list_tab').click(function(){
//        retailerDeails();
//    });
    
    function selfOrderDetails()
    {
        $('[class="loader"]').show();
        var fieldList = <?php echo json_encode($self_orders_list); ?>;
        var table = $('#self_orders_table').DataTable();
        table.clear().draw();
        var filterData = $('#dashboard_filter_dates').val();
        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: '/retailers/selforders',
            type: 'GET',
            data: {'filter_date': filterData},
            //dataType: 'JSON',
            success: function (response) {
                var data = (response);
                if ( data != 'undefined' )
                {
                    $.each(data, function (key, value) {
                        var temp = new Array();
                        $.each(fieldList, function (fieldCode, fieldName) {
							temp.push(value[fieldCode]);
                        });
                        table.row.add(temp).draw();
                    });
                }
            }
        });
        $('[class="loader"]').hide();   
    }

    function retailerDeails()
    {
        $('[class="loader"]').show();
        var fieldList = <?php echo json_encode($retailersList); ?>;
        var table = $('#dashboard_customer_table').DataTable();
        table.clear().draw();
        var filterData = $('#dashboard_filter_dates').val();
        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: '/retailers/dashboardcustomers',
            type: 'POST',
            data: {'filter_date': filterData},
            dataType: 'JSON',
            success: function (response) {
                var data = $.parseJSON(response);
                if ( data != 'undefined' )
                {
                    $.each(data, function (key, value) {
                        var temp = new Array();
                        $.each(fieldList, function (fieldCode, fieldName) {
                            temp.push(value[fieldCode]);
                        });
                        table.row.add(temp).draw();
                    });
                }
            }
});
        $('[class="loader"]').hide();
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.2/socket.io.js"></script>
    
    <script>
        var socket = io('<?php echo env('SOCKET_IO') ?>');
        socket.on("dashboard-channel", function(message){     
                console.log(message);       
                var order_details = message.data.data;
                    $.each(order_details, function (key2, dashboard) {
                        $.each(dashboard, function (key3, dashboardData) {
                            var key3 = dashboardData.key;
                            var val3 = dashboardData.val;
                            var per3 = dashboardData.per;
                            var test3 = key3.toLowerCase();
                            var temp3 = test3.replace(/[^A-Z0-9]/ig, "_");
                            $('#' + temp3).text(val3);
                            $('#data_per_' + temp3).text(per3);
                        });                                
                    });
                    $('#last_updated').text(message.data.time) ;
                
        });
    </script>
@stop
@extends('layouts.footer')