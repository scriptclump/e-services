@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'NCTTracker'); ?>

<span id="success_message">@include('flash::message')</span>
<span id="success_message_ajax"></span>
@if($dashboardAccess == 1)
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
          <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
            <div class="portlet-title">
                <div class="caption">NCT TRACKER DASHBOARD</div>
            </div>
        </div>

            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="scroller" style=" height:550px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                            <table id="ncttrackerdatagrid"></table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Modal -->

                <div class="modal fade" id="upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-id="#">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">NCT TRACKER <span style="color: blue"id="balance_amt"></span> </h4> 
                            </div>
                            <div class="modal-body venky">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                                {{ Form::open(array('url' => '/ncttracker/addNctDetails', 'id' => 'add_nct_data', 'files' => 'true'))}}
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Reference No</label>
                                                            <input type = "text" id ="reference_no" name = "reference_no" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="ifsc_code"class="control-label">IFSC Code</label>
                                                            <input type = "text" id ="ifsc_code" name = "ifsc_code" class="form-control">
                                                        </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Bank Name</label>
                                                            <input type = "text" id ="bank_name" name = "bank_name" class="form-control">
                                                            <input type = "hidden" id ="MaintableId" name = "MaintableId" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Branch Name</label>
                                                            <input type = "text" id ="branch_name" name = "branch_name" class="form-control">
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Issued Date</label>
                                                            <input type = "text" id ="issued_date" name = "issued_date" class="form-control">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Holder Name</label>
                                                            <input type = "text" id ="holder_name" name = "holder_name" class="form-control" placeholder ="Enter holder name">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Collected By</label>
                                                            <input type = "text" id ="collected_by" name = "collected_by" placeholder ="Search name" class="form-control">
                                                            <input type = "hidden" id ="user_id" name = "user_id" placeholder ="Search name" class="form-control">
                                                            <!-- <input type = "hidden" id ="collected_by_id" name = "collected_by_id" class="form-control"> -->
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Amount</label>
                                                            <input type = "text" id ="amount" name = "amount" class="form-control" >
                                                            <input type = "hidden" id ="balance_amount" name = "balance_amount" class="form-control" >
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Status</label>
                                                            <select id = "status"  name =  "status" class="form-control">
                                                            <option value="11905">Collected</option>
                                                        </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Proof Image</label>
                                                            <input type = "file" id ="nct_proof_image" name = "nct_proof_image"  accept="image/*" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Deposited To</label>
                                                            <select id="deposited_to" name="deposited_to" class="form-control">
                                                                @foreach($bankNames as $bankName)
                                                                <option value = "{{$bankName->tlm_name}}">{{$bankName->tlm_name}}</option>
                                                            @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Comment</label>
                                                            <input type = "text" id ="comment" name = "comment" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12 text-center">
                                                    <button type="submit" class="btn green-meadow" id="price-save-button">Save</button>
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
                </div>
                 <!-- Update Modal -->
                <div class="modal fade" id="view-update-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-id="#">
                    <div class="modal-dialog " role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">NCT TRACKER UPDATE <span style="color: blue" id="balance_amt_update"></span></h4>
                            </div>
                            <div class="modal-body venky">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">

                                                {{ Form::open(array('url' => '/ncttracker/addNctDetails', 'id' => 'update_nct_data'))}}
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Reference No</label>
                                                            <input type = "text" id ="reference_no_view" name = "reference_no_view" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Bank Name</label>
                                                            <input type = "text" id ="bank_name_view" name = "bank_name_view" class="form-control">
                                                            <input type = "hidden" id ="MaintableId_view" name = "MaintableId_view" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Branch Name</label>
                                                            <input type = "text" id ="branch_name_view" name = "branch_name_view" class="form-control">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Holder Name</label> 
                                                            <input type = "text" id ="holder_name_view" name = "holder_name_view" class="form-control" placeholder ="Enter holder name">
                                                            <!-- <select id = "holder_name_view"  name =  "holder_name_view" class="form-control select2me" >
                                                            </select> -->
                                                            
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Issued Date</label>
                                                            <input type = "text" id ="issued_date_view" name = "issued_date_view" class="form-control">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Collected By</label>
                                                            <input type = "text" id ="collected_by_view" name = "collected_by_view" placeholder ="Search name" class="form-control">
                                                            <input type = "hidden" id ="user_id" name = "user_id_view" placeholder ="Search name" class="form-control">
                                                            <!-- <input type = "hidden" id ="collected_by_id" name = "collected_by_id" class="form-control"> -->
                                                        </div>
                                                    </div>
                                                </div>
                                                    <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group" >
                                                            <label class="control-label">Status</label>
                                                            <select id = "status_view"  name =  "status_view" class="form-control">
                                                            
                                                            @foreach($statusdetails  as $statusfrommasterlookup)

                                                            <option value = "{{$statusfrommasterlookup->value}}">{{$statusfrommasterlookup->master_lookup_name}}</option>
                                                            @endforeach
                                                        </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Amount</label>
                                                            <input type = "text" id ="amount_view" name = "amount_view" class="form-control" readonly>
                                                            <input type = "hidden" id ="balance_amount_update" name = "balance_amount_update" class="form-control" >
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" id="extra_charge_div" style="display: none" >
                                                        <div class="form-group">
                                                            <label class="control-label">Extra Charges</label>
                                                            <input type = "text" id ="extra_charge_view" name = "extra_charge_view" class="form-control" value="">
                                                        </div>
                                                    </div>


                                                    <div class="col-md-12">
                                                        <div class="form-group" id="deposited_view_valid">
                                                            <label class="control-label">Deposited To</label>
                                                            <select id="deposited_to_view" name="deposited_to_view" class="form-control">
                                                                @foreach($bankNames as $bankName)
                                                                <option value = "{{$bankName->tlm_name}}">{{$bankName->tlm_name}}</option>
                                                            @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Comment</label>
                                                            <input type = "text" id ="comment_view" name = "comment_view" class="form-control">
                                                        </div>
                                                    </div>


                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12 text-center" id="Save_Button">
                                                    <button type="submit" class="btn green-meadow" id="price-save-button">Save</button>
                                                    <input type="hidden" name="nct_id_view" id="nct_id_view">
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
                </div>

                <!-- View Modal -->
                <div class="modal fade" id="view-upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel"> VIEW NCT TRACKER</h4>
                            </div>
                            <div class="modal-body">


                        <div class="row">
                            <div class="col-md-6">
                                
                                <div class="row">
                                <div class="col-md-6"><strong>Reference No :</strong></div>
                                <div class="col-md-6" id = "ref_no"></div>
                                </div>

                                <div class="row">
                                <div class="col-md-6"><strong>Amount :</strong></div>
                                <div class="col-md-6" id="det_amount"></div>
                                </div>

                                <div class="row">
                                <div class="col-md-6"><strong>CollectedBy :</strong></div>
                                <div class="col-md-6" id ="collectedby"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <a href="" id="a_prof_image" target="_blank"><img src="" id="prof_image" height="60px" width="360px" alt="No Proof Available"></a>
                            </div>
                        </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">
                                        <div class="tab-pane" id ="append_rows_details">
                                            <div class="row">
                                            <div class="col-lg-12 histhead" >  
                                                <div class="col-md-3"> <b>User</b></div>
                                                <div class="col-md-2"> <b>REF NO</b></div>  
                                                <div class="col-md-1"> <b>Date</b></div>
                                                <div class="col-md-3"> <b>Status</b></div>
                                                <div class="col-md-3"><b>Comments</b></div></div>   
                                            </div>  
                                            <div id="historyContainer" style="height: 250px;overflow-x:hidden;overflow-y: scroll; margin-right: -15px;">
                                            </div>
                                        </div>                                        
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="viewcheque-page-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel"> VIEW CHEQUE</h4>
                            </div>
                            <div class="modal-body">

                        <div class="row">
                            <div class="col-md-6">
                                <img src="" id="prof_image_cheque" height="413px" width="863px" alt="No Proof Available">
                            </div>
                        </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
@endif
@stop

@section('style')

<style type="text/css">
.modal-lg{width:81% !important;}
.timline_style {
    padding: 0 5px 5px 13px !important;
    margin-bottom:-32px !important;
}
.timeline-body {
    position: relative !important;
    padding: 13px !important;
    margin-top: 19px !important;
    margin-left: 71px !important;
}
.changedByName{margin-left:-15px !important;}
.modal-content {
    padding-bottom: 20px;
}
.push_right {
    margin-left: 30px !important;
}
.timeline {
margin-bottom: 0px !important;
}

.timeline-body {
    font-weight: normal !important;
}

.ui-autocomplete{z-index: 99999 !important; height: 250px !important; border:1px solid #efefef !important; overflow-y:scroll !important;overflow-x:hidden !important; width:410px !important; white-space: pre-wrap !important;}
</style>
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />


@stop
@section('script')
@include('includes.validators')
@stop
@section('userscript')
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/getbusinesslist.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/nctTracker/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/nctTracker/bootstrap_framework.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/nctTracker/nctTracker.js') }}" type="text/javascript"></script>

@stop
@extends('layouts.footer') 