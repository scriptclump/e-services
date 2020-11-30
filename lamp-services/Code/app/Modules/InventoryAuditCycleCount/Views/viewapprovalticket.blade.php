<?php
// echo $error_counter;die;
$clickevent = "return confirm('All Pending Items Will Be Marked as Completed')";
$clickevent = 'onclick ="'.$clickevent.'"';
if($all_status_counts['pending'] == 0)
{
   $clickevent = ""; 
}

$disabled = "";

if($error_counter == 1)
{
    $disabled = "disabled='disabled'";
}

?>

@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<span id="success_message"></span>
<span id="error_message"></span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">
                    Inventory Audit Approval
                </div>
                <div class="actions">
                    <input id="take" type="hidden" name="_token" value="{{csrf_token()}}">
                </div>
            </div>
            <div class="portlet-body">

<div class="caption">
                             <span class="caption-subject bold font-blue uppercase"> Filter By :</span>
                             <span class="caption-helper sorting">
      
                            <a href="/inventoryauditcc/viewapprovalticket/{{ $audit_id }}" class="{{($status == '') ? 'active' : 'inactive'}}" data-toggle="tooltip" title="All Items">All (<span id="all">{{ $all_status_counts['all'] }}</span>)</a>&nbsp;
                            
                            <a href="/inventoryauditcc/viewapprovalticket/{{ $audit_id }}/pending" class="{{($status == 'pending') ? 'active' : 'inactive'}}" data-toggle="tooltip" title="Pending Items">Pending (<span id="pending">{{ $all_status_counts['pending'] }}</span>)</a>&nbsp;
                            
                                        
                            <a href="/inventoryauditcc/viewapprovalticket/{{ $audit_id }}/completed" class="{{($status == 'completed') ? 'active' : 'inactive'}}" data-toggle="tooltip" title="Completed Items">Completed (<span id="completed">{{ $all_status_counts['completed'] }}</span>)</a>&nbsp;
                        </span>
                        </div>



                <div id="actions-submit">
                
                <div class="actions vertical-space">
<a href="/inventoryauditcc/auditapprovaldownload/{{ $audit_id }}" target="_blank" class = "btn green-meadow pull-right btn-space" <?php echo $clickevent; ?> >Download Sheet</a>
                    @if($curr_status_id == "57129")
                        <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document" class="btn green-meadow pull-right btn-space">Import Sheet</a>
                    @endif
                </div>
                
                </div>
                <div class="modal modal-scroll fade" id="upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                       <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">{{ trans('inventorycyclecountlabel.pop_up_title_st') }}</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                {{ Form::open(array('url' => '/inventoryauditcc/downloadtemplatest', 'id' => 'downloadexcel-mapping-stock-take'))}}


                                            </div>
     
                                            {{ Form::close() }}
                                            {{ Form::open(['id' => 'replanishment-upload-excel']) }}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">






                                                        <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                                            <div>
                                                                <span class="btn default btn-file btn green-meadow btnwidth">
                                                                    <span class="fileinput-new">{{ trans('inventorycyclecountlabel.pop_up_choosefile') }}</span>
                                                                    <span class="fileinput-exists" style="margin-top:-9px !important;">Choose Inventory File</span>
                                                                    <input type="file" name="upload_taxfile_replanishment" id="upload_taxfile_replanishment" value="" class="form-control"/>
                                                                    <input type="hidden" name="audit_id" id="audit_id" value="{{ $audit_id }}">  
                                                                </span>
                                                                <span class="fileinput-filename" style=" float:left; width:533px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" >
                                                    <div class="form-group">
                                                        <label class="control-label"> </label>
                                                        <button type="button"  class="btn green-meadow" id="excel-upload-button-replanishment">{{ trans('inventorycyclecountlabel.inventory_import_btn_stoke_take') }}</button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <span id="loader-st" style="display:none;">Please Wait<img src="/img/spinner.gif" style="width:225px; padding-left:20px;" /></span>
                                                </div>      
                                            </div>   
                                            {{ Form::close() }} 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <div id="old-data" class="row vertical-space table-data-arrange">
                <div class="row vertical-space">
                    <div class="col-md-12">
                        
                    </div>
                </div>
                
                    <div class="row">
                    <div class="col-md-12">
                        <table class="table table-border table-hover table-advance" border="1" cellpadding="10px">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Title</th>
                                    <th>SKU</th>

                                    <th>MRP</th>
                                    <th>ELP</th>
                                    <th>Opening Balance</th>
                                    <th>SOH</th>
                                    <th>Pending Return Qty</th>
                                    <th>Purchase Returns</th>
                                    <th>Picked Qty</th>
                                    <th>Quarantine Qty</th>

                                    <th>Location</th>
                                    <!-- <th>New Location</th> -->
                                    <th>Bin Qty</th>
                                    <th>Asssigned By</th>
                                    <th>Good Qty</th>

                                    <th>Damage Qty</th>
                                    <th>Damaged ELP</th>

                                    <th>Expired Qty</th>
                                    <th>Expired ELP</th>

                                    <th>Short Qty</th>
                                    <th>short ELP</th>

                                    <th>Excess Qty</th>
                                    <th>Excess ELP</th>

                                    <th>Current Bin Qty</th>
                                    <th>Deviation Value</th>
                                    <th>Approved Good Qty</th>
                                    <th>Approved Damaged Qty</th>
                                    <th>Approved Expired Qty</th>
                                    <th>Approved short Qty</th>
                                    <th>Approved Excess Qty</th>
                                    
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($audit_data as $value)
                                   <tr> 
                                       <td>{{ $value['product_id'] }}</td>
                                       <td>{{ $value['product_title'] }}</td>
                                       <td>{{ $value['sku'] }}</td>

                                       <td>{{ $value['mrp'] }}</td>
                                       <td>{{ $value['elp'] }}</td>

                                       <td>{{ $value['opening_balance'] }}</td>
                                       <td>{{ $value['soh'] }}</td>
                                       <td>{{ $value['sales_return_qty'] }}</td>
                                       <td>{{ $value['purchase_return_qty'] }}</td>
                                       <td>{{ $value['picked_qty'] }}</td>
                                       <td>{{ $value['quarantine_qty'] }}</td>

                                       <td>{{ $value['location_code'] }}</td>
                                       <!-- <td>{{ $value['new_location_code'] }}</td> -->

                                       <td>{{ $value['old_bin_qty'] }}</td>
                                       <td>{{ $value['updated_by'] }}</td>
                                       <td>{{ $value['good_qty'] }}</td>

                                       <td>{{ $value['damage_qty'] }}</td>
                                       <td>{{ $value['damage_qty'] * $value['elp'] }}</td>

                                       <td>{{ $value['expire_qty'] }}</td>
                                       <td>{{ $value['expire_qty'] * $value['elp'] }}</td>

                                       <td>{{ $value['missing_qty'] }}</td>
                                       <td>{{ $value['missing_qty'] * $value['elp'] }}</td>

                                       <td>{{ $value['excess_qty'] }}</td>
                                       <td>{{ $value['excess_qty'] * $value['elp'] }}</td>

                                       <td>{{ $value['bin_qty'] }}</td>
                                       <td>{{ $value['deviation_value'] }}</td>
                                       <td>{{ $value['appr_good_qty'] }}</td>
                                       <td>{{ $value['appr_damage_qty'] }}</td>
                                       <td>{{ $value['appr_expire_qty'] }}</td>
                                       <td>{{ $value['appr_missing_qty'] }}</td>
                                       <td>{{ $value['appr_excess_qty'] }}</td>

                                       
                                   </tr>
                                @endforeach
                            </tbody>
                            
                        </table>
                </div>
                </div>
            </div>
                    </div>

                <div id="response-data" style="width: 100%;display:none; " class="table-data-arrange"></div>
                </div>

                @if(!empty($approvalStatus))
                <div class="container">
                <div class="row" id="approval_div">
                    {{ Form::open(array('id' => 'approval-workflow-form'))}}
                    <div class="col-md-3">
                        <div class="form-group">
                         <select name="next_status" id="next_status" class="form-control">
                                <option value="">Select</option>
                                @foreach ($approvalStatus as $eachOptionKey => $eachOptionValue)
                                    <option value="{{ $eachOptionValue['nextStatusId'] }},{{ $eachOptionValue['isFinalStep'] }}">{{ $eachOptionValue['condition'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <textarea name="approval_comment" rows="3" id="approval_comment" class="form-control"></textarea>
                        <input type="hidden" name="current_status_id" id="current_status_id" value="{{ $curr_status_id }}">
                        <input type="hidden" name="bulk_upload_id" id="bulk_upload_id" value="{{ $audit_id }}">
                    </div>
                    
                    <div class="col-md-3">
                        <input type="submit" id="approval_submit" class="btn btn-primary" {{ $disabled }} value="Submit"> 
                    </div>
                    
                     {{ Form::close() }}
                    <div id="loader_div" class="loader-outer display_div">
                        <div class="loader" style=""></div>
                    </div>
                </div>
                </div>


                <br />

                <div class="row display_div" id="message_div">
                    <div class="col-md-12 text-center" id="after_submit">
                        Your request was submitted, <a href="javascript:closeWindow();">Close tab!</a>
                    </div>

                    <div class="col-md-12 text-center" id="after_success">
                        This request was already approved, <a href="javascript:closeWindow();">Close tab!</a>
                    </div>
                </div>

                @endif

            </div>
        </div>
    </div>
</div>
@stop

@section('userscript')
<style type="text/css">

th{ white-space: nowrap;}

    table{border: 1px solid #ddd;}
    .display_div { display: none; }
    .rowmargin{ margin: 10px;}
    .btnwidth{width:250px;}
    .fa-pencil{ color:#3598DC !important;}
    .actionss{padding-left: 22px !important;}
    .sorting a{ list-style-type:none !important;text-decoration:none !important;}
    .sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
    .sorting a:active{text-decoration:none !important;}
    .active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
    .inactive{text-decoration:none !important; color:#ddd !important;}

    .loader-outer{    
        background-color: #fff;
        opacity: 0.9;
        width: 97%;
        height: 500px;
        position: absolute;
        top: 2.5em;
        left: 1.2em;
    }
    .table-data-arrange{
        width: 98.5%;
    overflow: scroll;
    margin-left: 11px;
    margin-right: 4px;
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
        left:60em;
    }
    .btn-space
    {
        margin-right: 5px;
    }
    .vertical-space{
        margin-bottom:5px;
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
<!--Bootstrap JavaScript & CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
@extends('layouts.footer')

<script>

    function closeWindow() { 
        console.log("close window");
        var url = window.location.href;
        window.open(url, '_self', '');
        window.close();
    }



    
$('#approval-workflow-form').validate({
    rules: {
        next_status: {
            required: true
        },
        approval_comment: {
            required: true
        }
    },
    highlight: function (element) {
        var id_attr = "#" + $(element).attr("id") + "1";
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
        $(id_attr).removeClass('glyphicon-ok').addClass('glyphicon-remove');
    },
    unhighlight: function (element) {
        var id_attr = "#" + $(element).attr("id") + "1";
        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
        $(id_attr).removeClass('glyphicon-remove').addClass('glyphicon-ok');
    },
    errorElement: 'span',
    errorClass: 'help-block',
    errorPlacement: function (error, element) {
        if (element.length) {
            error.insertAfter(element);
        } else {
            error.insertAfter(element);
        }
    },
    submitHandler: function (form) {
        console.log("testing done");
        var token = $("#take").val();
        
          $.ajax({
            type: "POST",
            url: "/inventoryauditcc/submitapprovalstatus?_token=" + token,
            data: $("#approval-workflow-form").serialize(),
            // processData: false,
            // contentType: false,
            // dataType: "json",
            beforeSend: function () {
                $('#loader_div').show();
            },
            complete: function () {
                $('#loader_div').hide();
                $('#approval_div').hide();
                $('#message_div').show();
            },
            success: function (data)
            {
                $("#actions-submit").hide();
                if(data == 1)
                {
                    $("#after_submit").hide();
                    $("#after_success").show();
                }
                else if(data == 0)
                {
                    $("#after_submit").show();
                    $("#after_success").hide();
                }
            }
        });
    

    }
});



    $("#excel-upload-button-replanishment").on('click',function () {
    var token = $("#take").val();
    var stn_Doc = $("#upload_taxfile_replanishment")[0].files[0];
    var audit_id = $("#audit_id").val();
    
    
    if (typeof stn_Doc == 'undefined')
    {
        alert("Please select file");
        return false;
    }
    var formData = new FormData();
    formData.append('upload_excel_sheet', stn_Doc);
    formData.append('audit_id', audit_id);
    var ext = stn_Doc.name.split('.').pop().toLowerCase();
        if($.inArray(ext, ['xls']) == -1) {
            alert("Please choose a valid file");
            return false;
        }
        // console.log(stn_Doc);return false;
    $.ajax({
        type: "POST",
        url: "/inventoryauditcc/uploadappovalworkflowsheet?_token=" + token,
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
         beforeSend: function () {
            $('#loader-st').show();
            $("#excel-upload-button-replanishment").attr('disabled', true);
        },
        complete: function () {
            $('#loader-st').hide();
            $("#excel-upload-button-replanishment").removeAttr('disabled');
        },
        success: function (data)
        {
            /*checking here if the user is not having the access for approval work flow */
            console.log("HTML DATA"+data);
           if(data == 0 || data == "0")
            {
                    $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Excel Headers Mis-matched</div></div>');  
            
            }
            else if(data == 2){
                        $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Invalid Ticket Id</div></div>'); 
            }else{
                 var consolidatedmsg = "Total Updations : "+data.total_insertions+" || Total Failed : "+data.total_fails;
                $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+ consolidatedmsg +'</div></div>');
                $("#old-data").hide();
                if(data.error_counter == 0){
                    $("#approval_submit").removeAttr("disabled");
                }
                $("#response-data").show();
                $("#response-data").html(data.htmldata);
                // console.log("tetsttttttt data");
            }

            
            $("#upload_taxfile_stock_take").val("");
            $(".fileinput-filename").html("");
            // $("#warehousenamess-replanishment").prop('selectedIndex',0);
            $('#upload-document').modal('toggle');
            // $("#inventorygrid").igGrid("dataBind");
            return false;
            
        }
    });
});


$('#upload-document').on('hidden.bs.modal', function (e) {
    $("#fileinput-filename").html("");
    
});

$('#downloadexcel-mapping-stock-take').submit(function (e) {
    this.submit(); // use the native submit method of the form element
    $('#upload-document').modal('toggle');
});
</script>

@stop
@extends('layouts.footer')