@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<span id="success_message_ajax"></span>
<!-- <div class="alert alert-info hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="success_message_ajax"></span>
</div> -->
<input type="hidden" name="_token" id="csrf-token_hidden" value="{{ Session::token() }}" />
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/legalentity">DC/FC Center</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Payment Orders</li>
        </ul>
    </div>
</div>
<div class="portlet light tasks-widget">
    <div class="portlet-body">
        @include('LegalEntity::navigationTabstockist')
    </div>
</div>
@stop
@section('style')
<style type="text/css">
    .portlet-title{margin-top: -31px;}
    .rightAlignText{text-align: right;}
    .parent_cat{font-size:14px !important;}
    .sub_cat{ font-size:13px !important; padding-left:3px !important; background:#fff !important;}
    .prod_class{padding-left:10px !important; background:#fff !important; }


    .select2-disabled{font-weight:bold !important;}

   .rowlinht{ line-height:30px !important;}
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
.fileinput .thumbnail > img{padding: 10px;}
.profile-sub{
	margin-top: -56px !important; width:350px; height:45px;  position: absolute;opacity: 0;z-index: 9;
}
.profile-sub-title{
	background-color: #000;
    bottom: 20px;
    color: #fff;
    left: 42px;
    line-height: 45px;
    opacity: 0.60;
    position: absolute;
    text-align: center;
    width: 330px;
    
}
.page-content{background:none !important;}
.rowbotmarg{margin-bottom:10px !important;}
.rightAlign{text-align: right;}


#stockist_history_summaries_footer_row_text_container_sum_pay_code{
   display: none; 
}
.ui-iggrid .ui-iggrid-tablebody td:nth-child(5) {

           text-align: center; !important;
 }
 .ui-iggrid .ui-iggrid-tablebody td:nth-child(4) {

           text-align: center; !important;
 }
 .ui-iggrid .ui-iggrid-tablebody td:nth-child(6) {

           text-align: center; !important;
 }
#creditlimit_history_STATUS{
    text-align: center;
}

</style>
@stop

@section('userscript')
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />


<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<style type="text/css">
    .ui-iggrid-results{
    z-index: 1 !important;
 }
</style>

    <script>
    $(document).ready(function () {
          $("#users_list_new").igGrid({
            dataSource: '/legalentity/getUsersList',
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false, 
            enableUTCDates: false, 
            width: "100%",
            height: "100%",
            columns: [
            {headerText: "Name", key: 'firstname', dataType: 'string', width: '30%'},
            {headerText: "Mobile No", key: 'mobile_no', dataType: 'string', width: '30%'},
            {headerText: "Email ID", key: 'email_id', dataType: 'string', width: '30%'},           
            {headerText: "OTP", key: 'otp', dataType: 'string', width: '20%'}, 
            {headerText: "Action", key:'CustomAction', dataType: 'string', width: '10%'}  
                 ],
             features: [
                 {
                    name: "Sorting",
                    type: "remote",
                    columnSettings: [
                    {columnKey: 'name', allowSorting: true },
                    {columnKey: 'CustomAction', allowSorting: false },
                    {columnKey: 'mobile', allowSorting: true },
                    {columnKey: 'email', allowSorting: true },
                    {columnKey: 'StateName', allowSorting: true },
                    {columnKey: 'role', allowSorting: true },
                    {columnKey: 'otp', allowFiltering: true },
                   
                    ]
                },
                {
                    name: "Filtering",
                    type: "remote",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'CustomAction', allowFiltering: false },
                        {columnKey: 'name', allowFiltering: true },
                        {columnKey: 'mobile', allowFiltering: true },
                        {columnKey: 'email', allowFiltering: true },
                        {columnKey: 'role', allowFiltering: true },
                        {columnKey: 'StateName', allowFiltering: true },
                        {columnKey: 'otp', allowFiltering: true },
                     
                    ]
                },
                { 
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 20,
                    name: 'Paging', 
                    loadTrigger: 'auto', 
                    type: 'remote' 
                }
                
            ],
            primaryKey: 'prmt_tmpl_Id',
            width: '100%',
            height: '500',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            
        });

           $("#warehouse_list").igGrid({
            dataSource: '/legalentity/warehousesList',
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false, 
            enableUTCDates: false, 
            width: "100%",
            height: "100%",
            columns: [
            {headerText: "Contact Name", key: 'contact_name', dataType: 'string', width: '0%'},
            {headerText: "Cost Centre", key: 'lp_wh_name', dataType: 'string', width: '40%'},
            {headerText: "Phone No", key: 'phone_no', dataType: 'string', width: '20%'}, 
            {headerText: "Email", key: 'email', dataType: 'string', width: '20%'},            
            {headerText: "Address1", key: 'address1', dataType: 'string', width: '10%'},            
            {headerText: "Pincode", key: 'pincode', dataType: 'string', width: '10%'},            
            {headerText: "City", key: 'city', dataType: 'string', width: '10%'},
            {headerText: "Warehouse", key: 'types', dataType: 'string', width: '20%'},                     
 
                 ],
             features: [
                 {
                    name: "Sorting",
                    type: "remote",
                    columnSettings: [
                    {columnKey: 'contact_name', allowSorting: false },
                    {columnKey: 'cost_centre', allowSorting: false },
                    {columnKey: 'phone_no', allowSorting: true },
                    {columnKey: 'email', allowSorting: true },
                    {columnKey: 'address1', allowSorting: true },
                    {columnKey: 'pincode', allowSorting: true },
                    {columnKey: 'city', allowSorting: true },
                    {columnKey: 'types', allowSorting: false },
                    ]
                },
                {
                    name: "Filtering",
                    type: "remote",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'contact_name', allowFiltering: false },
                        {columnKey: 'cost_centre', allowFiltering: true },
                        {columnKey: 'phone_no', allowFiltering: true },
                        {columnKey: 'email', allowFiltering: true },
                        {columnKey: 'address1', allowFiltering: true },
                        {columnKey: 'pincode', allowFiltering: true },
                        {columnKey: 'city', allowFiltering: true },
                        {columnKey: 'types', allowFiltering: false },
                     
                    ]
                },
                { 
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 20,
                    name: 'Paging', 
                    loadTrigger: 'auto', 
                    type: 'remote' 
                }
                
            ],
            primaryKey: 'le_wh_id',
            width: '100%',
            height: '300px',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            
        });

    });
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function editCreditLimit(user_ecash_details_id){
    var token  = $("#csrf-token").val();
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/legalentity/rmvcredit/' + user_ecash_details_id,
            success: function (data)
            {
                if(data == 1){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Expired</div></div>' );
                    $(".alert-success").fadeOut(20000);
                }else{
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">Unable to expire</div></div>' );
                    $(".alert-danger").fadeOut(20000);
                }
                
                reloadGridData();
            }
    });
    function reloadGridData(){
                $("#creditlimit_history").igGrid("dataBind");
            }
}
function editCreditHistory(user_ecash_details_id){
    $('#edit_Credit_History').modal('show');
    $('#edit_Credit_History').modal({backdrop:'static', keyboard:false});
    $.post('/legalentity/editcredit/' + user_ecash_details_id,function(response){
        if(response){
            console.log(response);
            $("#from_date").attr('value',response.from_date);
            $("#to_date").attr('value',response.to_date);
            $("#user_ecash_details_id").attr('value',response.user_ecash_details_id);
        }
    });
}
$('#to_date').datepicker({
    dateFormat: 'yy-mm-dd',
    minDate: 0,
});
$("#creditlimit_edit_form").formValidation({
            fields: {
                to_date: {
                    validators: {
                        notEmpty: {
                            message: "Please enter To Date"
                        },
                    }
                },
            }
        }).on('success.form.fv', function (event) {
            event.preventDefault();
            var newOrderData = {
                from_date: $("#from_date").val(),
                to_date: $("#to_date").val(),
                user_ecash_details_id: $("#user_ecash_details_id").val(),
            };
            var token=$("#csrf_token").val();
            $.post('/legalentity/updatecredit',newOrderData,function(response){
                $("#edit_Credit_History").modal("hide");
                console.log(response);
                if(response == 1){
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Updated Succesfully </div></div>' );
                    $(".alert-success").fadeOut(20000);
                }
                else{
                    $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">Something Went Wrong Please Try Again</div></div>' );
                    $(".alert-success").fadeOut(20000);
                }
                reloadGridData();
            });
            function reloadGridData(){
                $("#creditlimit_history").igGrid("dataBind");
            }
        });
        $('#edit_Credit_History').on('hide.bs.modal', function () {
            $("#to_date").val("");
            $('#creditlimit_edit_form').formValidation('resetForm', true);
        });
function updateUsersDetailsData(user_id){   
    var token  = $("#csrf-token").val();
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/legalentity/editData/' + user_id,
            success: function (data)
            {

                  // console.log(data);
                if(data.is_active == 1){
                $("#le_check_active").attr("checked","checked");
                $("#le_check_active").parent().addClass("checked");
                }                
                $("#le_hidden1_id").val(data.legal_entity_id);
                $("#user_id_data").val(data.user_id);
                $("#f_name").val(data.firstname);
                $("#l_name").val(data.lastname);
                $("#OTP_id").val(data.otp);
                $("#mobile_no").val(data.mobile_no);
                $("#email_id").val(data.email_id);
                           
            }
    });
    $('#legalentity_view_data').modal('toggle');
}

$('#update_legalentity_data').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        f_name: {
                validators: {
                    notEmpty: {
                        message: 'Enter First Name'
                    },
                    regexp: {
                        regexp: '^[a-zA-Z_ ]*$',
                                message: "Name  must be string only."
                        },
                }
            },
          email_id: {
                validators: {
                    notEmpty: {
                        message: "Email is required."
                    },
                    regexp: {
                        regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                        message: "Invalid email formate."
                    }
                }
            },


       mobile_no:{
                    validators: {
                    notEmpty: {
                    message: "Mobile is required."
                    },
                            stringLength: {
                            min: 10,
                                    max: 10,
                                    message: "'Mobile number should be 10 digit."
                            },
                            regexp: {
                            regexp: '^[0-9]*$',
                                    message: "Mobile number must be digits only."
                            },                    
                    }
                },     
    }
}).on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#update_legalentity_data').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/legalentity/updateintotable',
        data: frmData,
        success: function (respData)
        {
            $('#legalentity_view_data').modal('toggle');
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Updated Succesfully </div></div>' );
            $(".alert-success").fadeOut(20000);
            reloadGridData();           
        }
    });
});

    function reloadGridData(){

    $("#users_list_new").igGrid("dataBind");
}


$("#legalentity_view_data").on('show.bs.modal', function () {
$('#update_legalentity_data').formValidation('resetForm', true);

    });



$("#customer_info").click(function(){
$('#LegalEntity_update_view').formValidation('resetForm', true);    
})


//update form info validations----------------------
$('#update_form_info').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        legalentity_name: {
                validators: {
                    notEmpty: {
                        message: ' Enter First Name '
                    },
                    regexp: {
                        regexp: '^[a-zA-Z_ ]*$',
                                message: "Name  must be string only."
                        }
                }
            },
      org_address1: {
                validators: {
                    notEmpty: {
                        message: ' Enter Your Address '
                    },
                    
                }
            },
       org_pincode: {
                validators: {
                notEmpty: {
                message: "Pincode is required."
                },
                        stringLength: {
                        min: 6,
                                max: 6,
                                message: "Pincode  should be 6 digit."
                        },
                        regexp: {
                        regexp: '^[0-9]*$',
                                message: "Pincode  must be digits only."
                        },
                    
                    }
                },
      lic_num: {
                validators: {
             
                        stringLength: {
                        min: 14,
                                max: 14,
                                message: "License  should be 14 digit."
                        },
                        regexp: {
                        regexp: '^[0-9]*$',
                                message: "License  must be digits only."
                        },
                    
                    }
                }, 
        org_city: {
            validators: {
                notEmpty: {
                    message: 'Enter City Name'
                },
             regexp: {
                regexp: '^[a-zA-Z_ ]*$',
                        message: "Name  must be string only."
                },
            }
        },
        gstin: {
            validators: {
                regexp: {
                   regexp:'^([0][1-9]|[1-2][0-9]|[3][0-9])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$',  
                    message:'Enter Valid GSTIN.'
                },
                remote: {
                    headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                    url: '/legalentity/checkgstin',
                    data: function (validator, $field, value){
                        return{
                           gstin_number: $('[name="gstin"]').val()
                        };
                      },
                    type: 'POST',
                    delay: 1000,
                    message: 'GSTIN Number already exists  or Invalid State code'
                }
            }
        }, 
    }
}).on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#update_form_info').serialize();
    // alert(frmData);
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/legalentity/updateCustomerInfo',
        data: frmData,
        success: function (respData)
        {
            // $('#flass_message').text("Updated Succesfully");
             //alert(respData);
             if(respData==1){
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Updated Successfully</div></div>');
                         $(".alert-success").fadeOut(50000);
                         location.reload(); 
             }else{
               $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Record Not Updated,Please Contact Administrator</div></div>');
                         $(".alert-success").fadeOut(50000);
             }
             $('#update_form_info').formValidation('resetForm', false);    
             $('div.alert').show();
             $('div.alert').removeClass('hide');
             $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
             /*alert("Updated Succesfully");*/
                        
        }
    });
});

$('#is_self_tax_update').change(function () { 
    var tax = $("#is_self_tax_update").val();
    if(tax ==1)
        tax = 0;
    else
        tax = 1;
     $("#is_self_tax_update").val(tax);

});

function StockistPaymentGrid(supplier_id) {
    $("#stockistPaymentGrid").igGrid({
            columns: [
                    {headerText: "DC/FC Code", key: "Stockist_Code", dataType: "string",width:"20%"},
                    {headerText: "DC/FC Name", key: "Stockist_Name", dataType: "string", width: "20%"},
                    {headerText: "Order Code", key: "Order_Code", dataType: "string", width: "20%"},
                    {headerText: "Invoice Code", key: "Invoice_Code", dataType: "string", width: "20%"},
                    {headerText: "Invoice Date", key: "Invoice_Date", dataType: "date",format:"dd/MM/yyyy", width: "20%"},
                    {headerText: "Order Total", key: "Order_Total", dataType: "number", width: "20%",
                    formatter: function(val, data) {
                        return $.ig.formatter(val, "number", "0.00");
                    }},
                    {headerText: "Invoice Total", key: "Invoice_Total", dataType: "number", width: "20%",
                    formatter: function(val, data) {
                        return $.ig.formatter(val, "number", "0.00");
                    }},
                    {headerText: "Return Total", key: "Return_Total", dataType: "number", width: "20%",
                    formatter: function(val, data) {
                        return $.ig.formatter(val, "number", "0.00");
                    }},
                    {headerText: "Delivered Total", key: "Delivered_Total",  dataType: "number",width: "20%",
                    formatter: function(val, data) {
                        return $.ig.formatter(val, "number", "0.00");
                    }},
                    {headerText: "Status", key: "Status", dataType: "string", width: "20%"},                    
                ],
            features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'created_at', allowSorting: false },
                ]
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                columnSettings: [
                    {columnKey: 'created_at', allowFiltering: false},
                ]


            },
            { 
                name: "Summaries",
                type: "local",
                showDropDownButton: false,
                summariesCalculated: function (evt, ui) {
                    var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                    listPricesummaryCells.each(function () {
                        if ($(this).text() != "") {
                            // $(this).text($(this).text().substr(2));
                            $(this).css({'font-weight': '800'});
                        }
                    });
                },
                columnSettings: [
                    {columnKey: "Stockist_Code", allowSummaries: false},
                    {columnKey: "Stockist_Name", allowSummaries: false},
                    {columnKey: "Order_Code", allowSummaries: false},
                    {columnKey: "Invoice_Code", allowSummaries: false},
                    {columnKey: "Invoice_Date", allowSummaries: false},
                    {columnKey: "Order_Total", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Invoice_Total", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Return_Total", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Delivered_Total", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: "Status", allowSummaries: false},
                ]
            },
            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalPayments',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
                        ],
                        //primaryKey: "pay_id",
        type: 'remote',
        dataSource: "/legalentity/stockistpayment/" + supplier_id,
        autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false, 
            enableUTCDates: false, 
            width: "100%",
            height: "100%",
        
     /*   //responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }*/
        }); 
}




$('#addpaymentforStockist').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        paid_through_stockist: {
                validators: {
                    notEmpty: {
                        message: 'paid through is required'
                    },
                    
                }
            },
          payment_amount_stockist: {
                validators: {
                    notEmpty: {
                        message: "payment amount required."
                    },
                    
                }
            },   
    }
}).on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#addpaymentforStockist').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/legalentity/savestockistamount',
        data: frmData,
        success: function (respData)
        {
            $('#addPaymentModel').modal('toggle');
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Payment added Succesfully </div></div>' );
            $(".alert-success").fadeOut(20000);
            reloadGridHistory();         
        }
    });
});

$('#creditdebit').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
          payment_amount_stockist: {
                validators: {
                    notEmpty: {
                        message: "payment amount required."
                    },
                    
                }
            },
            mode_payment: {
                validators: {
                    notEmpty: {
                        message: "payment mode is required."
                    },
                    
                }
            },   
    }
}).on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#creditdebit').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/legalentity/creditdebitapproval',
        data: frmData,
        success: function (respData)
        {
            console.log(respData);
            $('#close_creditlimit_popup').click();
            // $('#addPaymentModel').modal('toggle');
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+respData+'</div></div>');
            $(".alert-success").fadeOut(20000);
            $("#creditdebit")[0].reset();
            $('#creditdebit').formValidation('resetForm', true);
            location.reload();
        }
    });
});

    
function StockistPaymentHistory(user_id) {
    $("#stockist_history").igGrid({
            columns: [
                    {headerText: "Payment Code", key: "pay_code", dataType: "string",width:"22%"},
                    {headerText: "Ledger Account", key: "ledger_account", dataType: "string",width:"20%"},
                    {headerText: "Reference Number", key: "txn_reff_code", dataType: "string", width: "20%"},
                    {headerText: "Created By", key: "Created_By", dataType: "string", width: "20%"},
                    {headerText: "Created At", key: "Created_At", dataType: "string", width: "20%"},
                    {headerText: "Mode of Deposit", key: "Mode_Type", dataType: "string", width: "20%"},
                    {headerText: "Transaction Date", key: "transaction_date", dataType: "string",width: "20%"},
                    {headerText: "Amount", key: "pay_amount", dataType: "number", width: "20%",
                    formatter: function(val, data) {
                        return $.ig.formatter(val, "number", "0.00");
                    }}, 
                    {headerText: "Action", key:'action', dataType: 'string', width: '10%'}                 
                ],
            features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                {columnKey: 'Created_At', allowSorting: false },
                {columnKey: 'action', allowSorting: false },
                ]
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                columnSettings: [
                    {columnKey: 'Created_At', allowFiltering: false},
                    {columnKey: 'action', allowSorting: false },
                ]
            },

             { 
                name: "Summaries",
                type: "local",
                showDropDownButton: false,
                summariesCalculated: function (evt, ui) {
                    var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                    listPricesummaryCells.each(function () {
                        if ($(this).text() != "") {
                            // $(this).text($(this).text().substr(2));
                            $(this).css({'font-weight': '800'});
                        }
                    });
                },
                columnSettings: [
                    {columnKey: "pay_amount", allowSummaries: false},
                    {columnKey: "ledger_account", allowSummaries: false},
                    {columnKey: "txn_reff_code", allowSummaries: false},
                    {columnKey: "Created_By", allowSummaries: false},
                    {columnKey: "Created_At", allowSummaries: false},
                    {columnKey: "Mode_Type", allowSummaries: false}, 
                    {columnKey: "transaction_date", allowSummaries: false},
                    {columnKey: "pay_amount", allowSummaries: true,summaryOperands:
                                [{"rowDisplayLabel": "", "type": "SUM", "active": true}]},
                    {columnKey: 'action', allowSummaries:false },            
                ]
            },

            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalPayments',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"                
            }
          ],
                        //primaryKey: "pay_id",
        type: 'remote',
        dataSource: "/legalentity/transactionhistory/" + user_id,
        autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false, 
            enableUTCDates: false, 
            width: "100%",
            height: "100%",
        }); 
}

$(".modal").on('hidden.bs.modal', function () {
        $('#addPaymentModel').formValidation('resetForm', true);
        //Removing the error elements from the from-group
        $('.form-group').removeClass('has-error has-feedback');
        $('.form-group').find('small.help-block').hide();
        $('.form-group').find('i.form-control-feedback').hide();
        $('#payment_ref').val('');
        $('#paid_through_stockist').select2('val', '');
        $('#payment_type_stockist').select2('val', '');
        $('#transmission_date').val('');

});
$(".modal").on('hidden.bs.modal', function () {
    console.log("aaradhya");
        $('#addMfc').formValidation('resetForm', true);
        $('#creditdebit').formValidation('resetForm', true);
        $('#creditdebit')[0].reset();
        $('#payment_amount_stockist').val('');
        $('#payment_type').select2('val', '');
        $('#mode_payment').select2('val','');
        $('#payment_ref').val('');
        $('#trans_date').datepicker('setDate', null);
});


var date = new Date();
    $('#transmission_date').datepicker({
        dateFormat: 'yy/mm/dd',
        onSelect: function(datesel) {
            $('#addpaymentforStockist').formValidation('revalidateField', 'transmission_date');
        }
    });
var transdate = new Date();
    $('#trans_date').datepicker({
        dateFormat: 'yy/mm/dd',
        onSelect: function(datesel) {
            $('#creditdebit').formValidation('revalidateField', 'trans_date');
        }
    });


    function reloadGridHistory(){

    $("#stockist_history").igGrid("dataBind");
}

$('#from_date_report').datepicker({
        dateFormat: 'yy/mm/dd',
    });


$('#to_date_report').datepicker({
        dateFormat: 'yy/mm/dd',
    });

    
$("#filter_button").click(function() {
    var csrf_token = $('#csrf-token').val();
    var from_date = $('#from_date_report').val();
    var to_date = $('#to_date_report').val();
    var userid = $('#userid').val();
    if(from_date && to_date){
$("#stockist_history").igGrid({dataSource: '/legalentity/transactionhistory/'+userid+'?%24filter=from_date+eq+'+from_date+' and + to_date+eq+'+to_date});
    }else{
    }
    
    });


 var dateFormat = "dd-mm-yy";
    from = $( "#fromdate" ).datepicker({
            defaultDate: "+1w",
            dateFormat : dateFormat,
              maxDate:0,
            changeMonth: true, 
            changeYear: true,         
          }),
      to = $( "#todate" ).datepicker({
            defaultDate: "+1w",
              dateFormat : dateFormat,
              minDate:0,
            changeMonth: true,  
            changeYear: true,      
          });

     /*$('#fromdate').datepicker({
        dateFormat: 'dd-mm-yy',
        onChange: function(datesel) {
            console.log('dddddd');
            $('#update_form_info').formValidation('revalidateField', 'fromdate');
        }
    });*/
     $('#fromdate').change(function(){
            $('#creditlimitapproval').formValidation('revalidateField', 'fromdate');
     });

      $('#todate').change(function(){
            $('#creditlimitapproval').formValidation('revalidateField', 'todate');
     });


    /* dcl_fromdate = $( "#dcl_fromdate" ).datepicker({
            defaultDate: "+1w",
            dateFormat : dateFormat,
              maxDate:0,
            changeMonth: true, 
            changeYear: true,         
          }),
      dcl_todate = $( "#dcl_todate" ).datepicker({
            defaultDate: "+1w",
              dateFormat : dateFormat,
              minDate:0,
            changeMonth: true,  
            changeYear: true,      
          });

     $('#dcl_fromdate').change(function(){
            $('#decrease_creditlimit').formValidation('revalidateField', 'dcl_fromdate');
     });

      $('#dcl_todate').change(function(){
            $('#decrease_creditlimit').formValidation('revalidateField', 'dcl_todate');
     });*/ 
    $(document).on('click','.deletePayment',function(){
        var pay_id = $(this).attr('data-pay_id');
        var reference = $(this);
        if(confirm('Do you want to remove payment?')){
            $.ajax({
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                url: '/legalentity/deletePayment/'+pay_id,
                type: 'POST',
                data: {},
                dataType:'JSON',
                success: function (data) {
                    if (data.status == 200) {
                        alert(data.message);
                        reference.closest("div").remove();
                        StockistPaymentHistory(<?php echo $userid;?>);
                    }
                },
                error: function (response) {
                }
            });
        }else{
            return false;
        }
    });


$('#creditlimitapproval').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
       
      
       credit_limit: {
                validators: {
                notEmpty: {
                message: "Credit Limit is required."
                },
                        
                        regexp: {
                        regexp: '^[0-9]*$',
                                message: "Credit Limit  must be digits only."
                        },
                    
                    }
                },
        
        fromdate: {
                validators: {
                    notEmpty: {
                        message: 'Enter From Date'
                    },
                }
            },

            todate: {
                validators: {
                    notEmpty: {
                        message: 'Enter To Date'
                    },
                }
            },
    }
}).on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#creditlimitapproval').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/legalentity/creditlimitapproval',
        data: frmData,
        success: function (respData)
        {
              $('#close_creditlimit_popup').click();
              $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>'+respData+'</div></div>');
                         $(".alert-success").fadeOut(20000);
                        $('html, body').animate({scrollTop: '0px'}, 500);
                        location.reload();
        }
    });
})




$('#add_stockist').click(function() {
    var csrf_token = $('#csrf-token').val();
    var userid = $('#stockist_user_id').val();
    var leid= $('#stockist_le_id').val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': csrf_token},
        type: "POST",
        url: '/legalentity/approvedCreditLimit',
        data: "userid=" + userid+"&leid="+leid,
        success: function (respData)
        {
            console.log(respData);
             $('#credit_limit').val(respData.creditlimit);            
        }
    });
    });

function StockistPaymentLedger($legalentityID) {
    $("#stockist_fc_ledger").igGrid({
            columns: [
                    {headerText: "Transaction Date", key: "transaction_date", dataType: "date", width: "20%",format:"dd-MM-yyyy"},
                    {headerText: "Created Date", key: "created_at", dataType: "date", width: "20%",format:"dd-MM-yyyy"},
                    {headerText: "Narration", key: "comment", dataType: "string", width: "40%"},                   
                    {headerText: "Reference Number", key: "reference_no", dataType: "string",width:"20%"},
                    {headerText: "Debit", key: "dr_amount", dataType: "number",width:"15%"},
                    {headerText: "Credit", key: "cr_amount", dataType: "number", width: "15%"},
                    {headerText: "Closing Balance", key: "balance_amount", dataType: "number", width: "15%",
                    formatter: function(val, data) {
                        return $.ig.formatter(val, "number", "0.00");
                    }},

                ],
            features: [
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                columnSettings: [
                    {columnKey: 'transaction_date', allowFiltering: true},
                ]
            },

            //  { 
            //     name: "Summaries",
            //     type: "local",
            //     showDropDownButton: false,
            //     columnSettings: [
            //         {columnKey: "pay_id", allowSummaries: false},
            //         {columnKey: "cash_back_amount", allowSummaries: false},
            //         {columnKey: "balance_amount", allowSummaries: false},
            //         {columnKey: "transaction_date", allowSummaries: false},
            //     ]
            // },

            {
                name: 'Paging',
                type: "remote",
                pageSize: 20,
                recordCountKey: 'total',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"                
            }
          ],
                        //primaryKey: "pay_id",
        type: 'remote',
        dataSource: "/legalentity/stockistLedger/" + $legalentityID,
        autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "result",
            generateCompactJSONResponse: false, 
            enableUTCDates: false, 
            width: "100%",
            height: "100%",
        }); 
}


function CreditlimitHistory($legalentityID) {
    $("#creditlimit_history").igGrid({
            columns: [
                    {headerText: "From Date", key: "From_Date", dataType: "date", width: "15%",format:"dd-MM-yyyy"},
                    {headerText: "To Date", key: "To_Date", dataType: "date", width: "15%",format:"dd-MM-yyyy"},
                    {headerText: "Description", key: "description", dataType: "string",width:"60%"},                
                    {headerText: "Requested Amount", key: "Requested_Amount", dataType: "decimal",width:"20%"},
                    {headerText: "Status", key: "STATUS", dataType: "string",width:"15%"},
                    {headerText: "Actions", key:"actions", dataType: "string", width:"15%"},

                ],
            features: [
            {
                name: 'Paging',
                type: "remote",
                pageSize: 20,
                recordCountKey: 'total',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"                
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'actions', allowFiltering: false}
                ]
            }
          ],
                        //primaryKey: "pay_id",
        type: 'remote',
        dataSource: "/legalentity/creditLimitHistory/" + $legalentityID,
        autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "result",
            generateCompactJSONResponse: false, 
            enableUTCDates: false, 
            width: "100%",
            height: "100%",
        }); 
}

// $('#from_date_ledger').datepicker({
//         dateFormat: 'dd/mm/yy',
//     });

//     $('#to_date_ledger').datepicker({
//         dateFormat: 'dd/mm/yy',
//     });



    $(document).ready(function () {
        $("#from_date_ledger").keypress(function(event) {event.preventDefault();});
        $("#to_date_ledger").keypress(function(event) {event.preventDefault();});
        $('#from_date_ledger').datepicker({
            dateFormat: 'dd/mm/yy',
            maxDate: 0,
            onSelect: function () {
                var select_date = $(this).datepicker('getDate');
                var nextdayDate = getNextDay(select_date);
                $('#to_date_ledger').datepicker('option', 'minDate', select_date);
            }
        });
        $('#to_date_ledger').datepicker({
            dateFormat: 'dd/mm/yy',
            maxDate: '+0D',
        });
        $("#creditlimit_from_date").keypress(function(event) {event.preventDefault();});
        $("#creditlimit_to_date").keypress(function(event) {event.preventDefault();});
        $('#creditlimit_from_date').datepicker({
            dateFormat: 'dd/mm/yy',
            maxDate: 0,
            onSelect: function () {
                var select_date = $(this).datepicker('getDate');
                var nextdayDate = getNextDay(select_date);
                $('#creditlimit_to_date').datepicker('option', 'minDate', select_date);
            }
        });
        $('#creditlimit_to_date').datepicker({
            dateFormat: 'dd/mm/yy',
            // maxDate: '+0D',
        });
    });

    function getNextDay(select_date) {
        select_date.setDate(select_date.getDate());
        var setdate = new Date(select_date);
        var nextdayDate = setdate.getFullYear() + '-' + zeroPad((setdate.getMonth() + 1), 2) + '-' + zeroPad(setdate.getDate(), 2);
        return nextdayDate;
    }

    function zeroPad(num, count) {
        var numZeropad = num + '';
        while (numZeropad.length < count) {
            numZeropad = "0" + numZeropad;
        }
        return numZeropad;
    }



    function callTrigger() {
        var token = $("#token_value").val(),
            from_date = $("#from_date_ledger").val(),
            to_date = $("#to_date_ledger").val(),flag=0;
        if (from_date == "")
        {
            alert("Please Select From Date");
            $("#from_date_ledger").focus();
            return false;
            flag=1;
        }
        if (to_date == "")
        {
            alert("Please Select To Date");
            $("#to_date_ledger").focus();
            return false;
            flag=1;
        }
        if(new Date(to_date) < new Date(from_date))
            {
                alert('To date should be greater than from date');
                $("#to_date_ledger").val('');
                $("#to_date_ledger").focus(); 
                flag=1;
            }

        if(flag==0){

            //var csrf_token = $('#csrf-token').val();
            //var from_date = $('#from_date_ledger').val();
            //var to_date = $('#to_date_ledger').val();
            var le_leder_id = $('#legalentity_id_ledger').val();
            if(from_date && to_date){
                $("#stockist_fc_ledger").igGrid({dataSource: '/legalentity/stockistLedger/'+le_leder_id+'?%24filter=from_date+eq+'+from_date+' and + to_date+eq+'+to_date});
            }

        }
    };

    function callTriggerForDates() {
        var token = $("#token_value").val(),
            from_date = $("#creditlimit_from_date").val(),
            to_date = $("#creditlimit_to_date").val(),flag=0;
        if (from_date == "")
        {
            alert("Please Select From Date");
            $("#creditlimit_from_date").focus();
            return false;
            flag=1;
        }
        if (to_date == "")
        {
            alert("Please Select To Date");
            $("#creditlimit_to_date").focus();
            return false;
            flag=1;
        }
        if(new Date(to_date) < new Date(from_date))
            {
                alert('To date should be greater than from date');
                $("#creditlimit_to_date").val('');
                $("#creditlimit_to_date").focus(); 
                flag=1;
            }

        if(flag==0){
            var le_id = $('#legalentity_id_credit').val();
            if(from_date && to_date){
                $("#creditlimit_history").igGrid({dataSource: '/legalentity/creditLimitHistory/'+le_id+'?%24filter=from_date+eq+'+from_date+' and + to_date+eq+'+to_date});
            }

        }
    };

function callTriggerDownload(){
            var token = $("#token_value").val(),
            from_date = $("#from_date_ledger").val(),
            to_date = $("#to_date_ledger").val(),flag=0;
        if (from_date == "")
        {
            alert("Please Select From Date");
            $("#from_date_ledger").focus();
            return false;
            flag=1;
        }
        if (to_date == "")
        {
            alert("Please Select To Date");
            $("#to_date_ledger").focus();
            return false;
            flag=1;
        }
        if(new Date(to_date) < new Date(from_date))
            {

                alert('To date should be greater than from date');
                $("#to_date_ledger").val('');
                $("#to_date_ledger").focus(); 
                flag=1;
            }

        if(flag==0){

            $('#payment_ledger_export').submit();
        }

};
</script>

<script type="text/javascript">

    $("#frmUpload").formValidation({
        feedbackIcons: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            documentType: {
                validators: {
                    notEmpty: {
                        message: "Please select document type"
                    }
                }
            },
            upload_file: {
                validators: {
                    notEmpty: {
                        message: "Please select file to upload"
                    },
                    file: {
                        extension: 'doc,pdf,docx,jpg,jpeg,png,gif,jfif',
                        type: 'application/pdf,application/msword,application/jfif,image/jpeg,image/png,image/gif,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        maxSize: 2097152,   // 2048 * 1024
                        message: 'The selected file is not valid'                        
                    }
                }
            }
    }}).on('success.form.fv', function (event) {
        event.preventDefault();
        var form = document.getElementById("frmUpload");
        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/legalentity/uploadDoc",
            type: "POST",
            data: new FormData(form),
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'json',
            beforeSend: function (xhr) {
                $('input[name="btnUpload"]').attr('disabled', true);
                $('.loderholder').show();
            },
            success: function (response) {
                // $('#ajaxResponseDoc').removeClass('alert-danger').addClass('alert-success').html(response.message).show();
                $("#ajaxResponseDoc").attr("class","alert alert-success").text(response.message).show().delay(3000).fadeOut(350);
                $('#leDocList').append(response.docText);
                $('#frmUpload')[0].reset();
                $('input[name="btnUpload"]').attr('disabled', false);
                $('.loderholder').hide();
                location.reload();

            },
            error: function (response) {
                $('#ajaxResponseDoc').removeClass('alert-success').addClass('alert-danger').html("Unable to save file").show();
                $('.loderholder').hide();
            }
        });
    });

    $(document).on('click', '.le-del-doc', function () {
        var docId = $(this).attr("id");
        if ( confirm("{{Lang::get('inward.alterDelete')}}") ) {
            deleteDoc(docId);
            $(this).closest('tr').remove();
            var docCount = $('#leDocList tbody').find('tr').length;
            if(!docCount)
            {
                $('[name="is_document_required"]').val(1);
            }
        }
    });


    function deleteDoc(id) {
    $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
        url: "/legalentity/deleteDoc",
        type: "POST",
        data: {id: id},
        dataType: 'json',
        success: function (response) {
            $('#ajaxResponse').html(response.message);
        },
        error: function (response) {
            $('#ajaxResponse').html("{{Lang::get('salesorders.errorInputData')}}");
        }
    });
}
</script>
@stop
@extends('layouts.footer')