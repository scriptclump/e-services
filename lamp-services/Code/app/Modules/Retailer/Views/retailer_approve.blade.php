@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="alert alert-info hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>



<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
        <div class="portlet-title">
                <div class="caption"> {{trans('retailers.title.approve_page_title')}} </div>
                <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Click here for Full Screen"><i class="fa fa-question-circle-o"></i></a></span> </div>
            </div>
            <div class="portlet-body">
<input type="hidden" value="{{$retailers->legal_entity_id}}" id="legal_entity_id" name="legalEntityId" />
@include('Retailer::retailer_approve_info')
</div>
</div>
</div>
</div>


<div class="row" style="margin-top:10px;">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            
            <div class="portlet-body">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_11" data-toggle="tab">{{trans('retailers.tab.users')}}</a></li>
                        <li><a href="#tab_22" data-toggle="tab">{{trans('retailers.tab.documents')}}</a></li>
                        <li><a href="#tab_33" data-toggle="tab">{{trans('retailers.tab.approval_form')}}</a></li>
                        <li><a href="#tab_44" data-toggle="tab">{{trans('retailers.tab.approval_history')}}</a></li>
                        <li><a href="#tab_55" data-toggle="tab">{{trans('retailers.tab.orders')}}</a></li>
                        <li><a href="#tab_666" data-toggle="tab">{{trans('retailers.tab.collections_details')}}</a></li>
                        <li><a href="#tab_77" data-toggle="tab">{{trans('retailers.tab.warehouse_mapped')}}</a></li>                        
                        <li><a href="#tab_88" data-toggle="tab">{{ trans('retailers.tab.e_cash') }}</a></li>
                        <li><a href="#tab_99" data-toggle="tab">{{ trans('retailers.tab.credit_approval_history') }}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_11">  
                            @include('Retailer::users')
                        </div>
                        <div class="tab-pane" id="tab_22">
                            @include('Retailer::documents')
                        </div>
                        <div class="tab-pane" id="tab_33">
                            @include('Manufacturers::approval')
                        </div>
                        <div class="tab-pane" id="tab_44">
                            @include('Retailer::approval_history')
                        </div>
                        <div class="tab-pane" id="tab_55">
                            @include('Retailer::orders')
                        </div>
                        <div class="tab-pane" id="tab_666">
                            @include('Retailer::collection_details')
                        </div>
                        <div class="tab-pane" id="tab_77">
                            @include('Retailer::warehouse_mapping')
                        </div>
                        <div class="tab-pane" id="tab_88">
                            @include('Retailer::cash_back')
                        </div>
                        <div class="tab-pane" id="tab_99">
                            <?php echo $creditApprovalHistory; ?>
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
    .parent_cat{font-size:14px !important;}
    .sub_cat{ font-size:13px !important; padding-left:3px !important; background:#fff !important;}
    .prod_class{padding-left:10px !important; background:#fff !important; }
     .rowlinht{ line-height:30px !important;}


    .select2-disabled{font-weight:bold !important;}


    .thumbnail {
        padding: 0px !important;
        margin-bottom: 0px !important;
    }
    .fileinput-filename{word-wrap: break-word !important;}

    .fileinput-new .thumbnail{ width:100px !important; height:33px !important;}

    h4.block{padding:0px !important; margin:0px !important; padding-bottom:10px !important;}

    .pac-container .pac-logo{    z-index: 9999999 !important;}
    .pac-container{    z-index: 9999999 !important;}
    .pac-logo{    z-index: 9999999 !important;}
    #dvMap{height:304px !important; width:269px !important;}

    label {
        margin-bottom: 0px !important;
        padding-bottom: 0px !important;
    }
    .modal-header {
        padding: 5px 15px !important;
    }

    .modal .modal-header .close {
        margin-top: 8px !important;
    }

    .form-group {
        margin-bottom: 5px !important;
    }

    .radio input[type=radio]{ margin-left:0px !important;}
    .thumbnail  {
	height: 350px;
	width: 350px;
    }
    .thumbnail img {width: 100%;}
    .fileinput .thumbnail > img{padding: 10px;}
    .page-content{background:none !important;}
</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('userscript')
@include('includes.ignite')
<script src="{{ URL::asset('assets/global/scripts/approval.js') }}" type="text/javascript"></script>
{{HTML::script('assets/global/plugins/select2/select2.min.js') }}
<script type="text/javascript">
    $(document).ready(function () {
        $('#master_manf').select2('enable', false);
        approval("{{url('/')}}/retailers/index");
        getUserList();
        getAreaList();
        getOrderList();
        getCollectionDetails();
        getCashBackHistoryGrid("#cash_back_list");
    });
    function getCashBackHistoryGrid(grid_id)
    {
        var legal_entity_id = $('#legal_entity_id').val();
        console.log("Retaile Aprove func");
        $(grid_id).igGrid({
            dataSource: "/users/cashbackhistory/"+legal_entity_id+"/-1",
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            recordCountKey: 'totalRecCount',
            columns: [
                {headerText: "{{trans('retailers.grid.order_details')}}", key: 'order_details', headerCssClass: 'rightAlign', dataType: 'string', width: '20%'},
                {headerText: "{{trans('retailers.grid.delivery_amt')}}", key: 'delivery_amt', template: '<div class="rightAlign"> ${delivery_amt} </div>', dataType: 'string', width: '15%'},
                {headerText: "{{trans('retailers.grid.cash_back_amt')}}", key: 'cash_back_amt', template: '<div class="rightAlign"> ${cash_back_amt} </div>', dataType: 'string', width: '15%'},
                {headerText: "{{trans('retailers.grid.transaction_type')}}", key: 'transaction_type', dataType: 'string', width: '20%'},
                {headerText: "{{trans('retailers.grid.transaction_date')}}", key: 'transaction_date', dataType: 'string', width: '15%'}
                ],
            features: [
                {
                    name: 'Paging',
                    type: 'local',
                    pageSize: 10,
                    recordCountKey: 'totalRecCount',
                    pageIndexUrlKey: "page",
                    pageSizeUrlKey: "pageSize"
                }
            ],
            primaryKey: 'user_id',
            width: '80%',
            height: '80%',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            type: 'local',
            showHeaders: true,
            fixedHeaders: true
        });
    }
    function getUserList()
    {
        let le_id=$('#legal_entity_id').val();
        let input_data={
            le_id: le_id
        };

        $('#users_list').igGrid({
        dataSource: '/retailers/getUsersList?data='+input_data,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true, 
        renderCheckboxes: true,
        columns: [
            {headerText: '{{ trans("retailers.grid.user_id") }}', key: 'user_id', dataType: 'string', width: '0%'},
            {headerText: '{{ trans("retailers.grid.name") }}', key: 'name', dataType: 'string', width: '15%'},
            {headerText: '{{ trans("retailers.grid.mobile") }}', key: 'mobile_no', dataType: 'string', width: '20%'},            
            {headerText: '{{ trans("retailers.grid.email") }}', key: 'email_id', dataType: 'string', width: '20%'},            
        ],
        features: [
            {
                name: 'Paging',
                type: 'local',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "local",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'is_approved', allowFiltering: false},
                    {columnKey: 'actions', allowFiltering: false}
                ]
            },
            {
                name: 'Sorting',
                type: 'local',
                persist: false

            }
        ],
            primaryKey: 'user_id',
            width: '100%',
            initialDataBindDepth: 0,
            localSchemaTransform: false
        });
    }
    function getAreaList()
    {
        $('#warehouse_list').igGrid({
        dataSource: '/retailers/getServicableList/'+$('#pincode').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true, 
        renderCheckboxes: true,
        columns: [
            {headerText: '{{ trans("retailers.grid.warehouse_name") }} Name', key: 'lp_wh_name', dataType: 'string', width: '15%'},
            {headerText: '{{ trans("retailers.grid.contact_name") }} Name', key: 'contact_name', dataType: 'string', width: '15%'},
            {headerText: '{{ trans("retailers.grid.mobile") }}', key: 'phone_no', dataType: 'string', width: '7%'},            
            {headerText: '{{ trans("retailers.grid.email") }}', key: 'email', dataType: 'string', width: '15%'},            
            {headerText: '{{ trans("retailers.grid.address1") }}', key: 'address1', dataType: 'string', width: '20%'},            
            {headerText: '{{ trans("retailers.grid.address2") }}', key: 'address2', dataType: 'string', width: '10%'},            
            {headerText: '{{ trans("retailers.grid.city") }}', key: 'city', dataType: 'string', width: '7%'},            
            {headerText: '{{ trans("retailers.grid.state") }}', key: 'state', dataType: 'string', width: '10%'}
        ],
        features: [
            {
                name: 'Paging',
                type: 'local',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "local",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'is_approved', allowFiltering: false},
                    {columnKey: 'actions', allowFiltering: false}
                ]
            },
            {
                name: 'Sorting',
                type: 'local',
                persist: false

            }
        ],
            primaryKey: 'lp_wh_id',
            width: '100%',
            initialDataBindDepth: 0,
            localSchemaTransform: false
        });
    }
    //collection_details
    function getCollectionDetails()
    {
        $('#collection_details').igGrid({
        dataSource: '/retailers/getCollectionDetails/'+$('#legal_entity_id').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true, 
        renderCheckboxes: true,
        columns: [
        // Order ID , Delivery Boy, Amount Colected, Payement Type, Handover Boy, Due Amount, Due Amount Reason, Item Recieved Date, Payment Received Date...
            {headerText: "{{trans('retailers.grid.payment_date')}}", key: 'date', dataType: 'date', width: '8%'},
            {headerText: "{{trans('retailers.grid.order_id')}}", key: 'order_code', dataType: 'string', width: '10%'},
            {headerText: "{{trans('retailers.grid.collection_id')}}", key: 'collection_code', dataType: 'string', width: '10%'},
            {headerText: "{{trans('retailers.grid.amount')}}", key: 'amount', dataType: 'string', width: '10%', template: "<div class='rightAlignText'>${amount}</div>"},
            {headerText: "{{trans('retailers.grid.paid_by')}}", key: 'paid_by', dataType: 'string', width: '10%'},
            {headerText: "{{trans('retailers.grid.delivered_by')}}", key: 'delivered_by', dataType: 'string', width: '20%'},
            {headerText: "{{trans('retailers.grid.collected_amount')}}", key: 'collected_amount', dataType: 'string', width: '10%', template: "<div class='rightAlignText'>${collected_amount}</div>"},
            {headerText: "{{trans('retailers.grid.payment_mode')}}", key: 'Payment_Mode', dataType: 'string', width: '7%'}
        ],
        features: [
            {
                name: 'Paging',
                type: 'local',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "local",
                mode: "simple",
                filterDialogContainment: "window"
            },
            {
                name: 'Sorting',
                type: 'local',
                persist: false

            }
        ],
            primaryKey: 'mp_order_id',
            width: '100%',
            initialDataBindDepth: 0,
            localSchemaTransform: false
        });
    }
    function getOrderList()
    {
        $('#orders_list').igGrid({
        dataSource: '/retailers/getOrderList/'+$('#legal_entity_id').val(),
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true, 
        renderCheckboxes: true,
        columns: [
            {headerText: '{{ trans("retailers.grid.order_id") }}', key: 'mp_order_id', dataType: 'string', width: '15%'},
            {headerText: '{{ trans("retailers.grid.shop_name") }}', key: 'shop_name', dataType: 'string', width: '15%'},
            {headerText: '{{ trans("retailers.grid.order_status") }}', key: 'master_lookup_name', dataType: 'string', width: '7%'},            
            {headerText: '{{ trans("retailers.grid.total") }}', key: 'total', dataType: 'string', width: '15%'},            
            {headerText: '{{ trans("retailers.grid.email") }}', key: 'email', dataType: 'string', width: '20%'},            
            {headerText: '{{ trans("retailers.grid.mobile") }}', key: 'phone_no', dataType: 'string', width: '10%'}
        ],
        features: [
            {
                name: 'Paging',
                type: 'local',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "local",
                mode: "simple",
                filterDialogContainment: "window"
            },
            {
                name: 'Sorting',
                type: 'local',
                persist: false

            }
        ],
            primaryKey: 'mp_order_id',
            width: '100%',
            initialDataBindDepth: 0,
            localSchemaTransform: false
        });
    }
function getAreas(validator)
{
//    var formValidation = $('#retailersinfo').data('formValidation');
//    var isValid = formValidation.isValidField('org_pincode');
    var isValid = validator.isValidField('org_pincode');
    console.log(isValid);
    if(isValid != false)
    {
        $('#area').empty();
        var pincode = $('#pincode').val();    
        if(pincode.length == 6)
        {
            $.ajax({
                url: '/retailers/getAreaList',
                data: {'_token' : $('#csrf-token').val(), 'pincode' : pincode},
                type: 'POST',
                success: function (response) {
                    var data = $.parseJSON(response);
                    $('#area').append($("<option></option>")
                                           .attr("value",'')
                                           .text("{{ trans('retailers.form_validate.select_area') }}"));
                    $('#area').select2({placeholder: "{{ trans('retailers.form_validate.select_area') }}"});
                    if(data.length > 0)
                    {
                        $.each(data, function(key, value){
                            $('#area').append($("<option></option>")
                                           .attr("value",value.city_id)
                                           .text(value.officename));
                        });
                    }
                }
            });
        }
    }
}
</script>
@stop
@extends('layouts.footer')