$(document).ready(function () {
    $('#transmission_date').datepicker({
        //minDate: 0,
        numberOfMonths: 1,
        onSelect: function(dateText) {
       }
    });
    $('#addpaymentform').validate({
        rules: {
            paid_through: {
                required: true
            },
            payment_type: {
                required: true
            },
            payment_amount: {
                required: true
            },
            transmission_date: {
                required: true
            },
        },
        submitHandler: function (form) {
            var po_id = $('#po_id').val();
            var formdata = $('#addpaymentform').serialize();
            if (po_id != '') {
                if (confirm('Do you want to add payment?')) {
                    $('.loderholder').show();
                    $.ajax({
                        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                        url: '/po/addpayment?' + formdata,
                        type: 'GET',
                        data: {po_id: po_id},
                        dataType: 'JSON',
                        success: function (data) {
                            if (data.code == 200) {
                                $('#error-msg2').removeClass('alert-danger').addClass('alert-success').html(data.message).show();
                                $('.loderholder').hide();
                                window.setTimeout(function () {
                                    $('#error-msg2').hide()
                                }, 2000);
                                window.setTimeout(function () {
                                    $('.close').click()
                                }, 1000);
                                $('#addpaymentform')[0].reset();
                                $("#paid_through").select2().select2("val", "");
                                $("#payment_type").select2().select2("val", "");
                                window.setTimeout(function () {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                $('#error-msg2').removeClass('alert-success').addClass('alert-danger').html(data.message).show();
                                window.setTimeout(function () {
                                    $('#error-msg2').hide()
                                }, 2000);
                                $('.loderholder').hide();
                            }
                        },
                        error: function (response) {
                        }
                    });
                } else {
                    return false;
                }
            } else {
                $('#error-msg2').removeClass('alert-success').addClass('alert-danger').html('Invalid PO ID').show();
                $('.loderholder').hide();
                window.setTimeout(function () {
                    $('#error-msg2').hide()
                }, 2000);
            }

        }
    });
   
    $('#addlegalpaymentform').validate({
        rules: {
            paid_through: {
                required: true
            },
            payment_type: {
                required: true
            },
            payment_amount: {
                required: true
            },
            transmission_date: {
                required: true
            },
        },
        submitHandler: function (form) {
            var le_id = $('#le_id').val();
            var formdata = $('#addlegalpaymentform').serialize();
            if (le_id != '') {
                if (confirm('Do you want to add payment?')) {
                    $('.loderholder').show();
                    $.ajax({
                        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                        url: '/po/addlegalpayment?' + formdata,
                        type: 'GET',
                        data: {le_id: le_id},
                        dataType: 'JSON',
                        success: function (data) {
                            if (data.code == 200) {
                                $('#error-msg2').removeClass('alert-danger').addClass('alert-success').html(data.message).show();
                                $('.loderholder').hide();
                                window.setTimeout(function () {
                                    $('#error-msg2').hide()
                                }, 2000);
                                window.setTimeout(function () {
                                    $('.close').click()
                                }, 1000);
                                $('#addlegalpaymentform')[0].reset();
                                $("#paid_through").select2().select2("val", "");
                                $("#payment_type").select2().select2("val", "");
                                window.setTimeout(function () {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                $('#error-msg2').removeClass('alert-success').addClass('alert-danger').html(data.message).show();
                                window.setTimeout(function () {
                                    $('#error-msg2').hide()
                                }, 2000);
                                $('.loderholder').hide();
                            }
                        },
                        error: function (response) {
                        }
                    });
                } else {
                    return false;
                }
            } else {
                $('#error-msg2').removeClass('alert-success').addClass('alert-danger').html('Invalid Legal Entity ID').show();
                $('.loderholder').hide();
                window.setTimeout(function () {
                    $('#error-msg2').hide()
                }, 2000);
            }

        }
    });
    
    $("#addPaymentModel").on('hide.bs.modal', function () {
        if ($('#addpaymentform').length > 0) {
            $('#addpaymentform')[0].reset();
        }
        if ($('#addlegalpaymentform').length > 0) {
            $('#addlegalpaymentform')[0].reset();
            $("#cost_center").select2().select2("val", "");
            $("#payment_for").select2().select2("val", "");
            $('#edit_payid').val('');
        }
        $("#paid_through").select2().select2("val", "");
        $("#payment_type").select2().select2("val", "");
        $('#popuphead').text('Add Payment');
    });
    $(document).on('click', ".pmtApprv", function () {
        var pid = $(this).attr('data-pid');
        //var status = $(this).attr('data-status');
        $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                url: '/po/getApprlForm/Other Payment/' + pid,
                type: "POST",
                data: {},
                success: function (response) {
                $('#paymentPupupId').html(response);
                },
                error: function (response) { }
        });
    });
    $(document).on('click', ".historyDetail", function () {
        var pid = $(this).attr('data-pid');
        $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                url: '/po/getApprovalHistory/Other Payment/' + pid,
                type: "POST",
                data: {},
                success: function (response) {
                $('#pmtHistoryBody').html(response);
                },
                error: function (response) { }
        });
    });

    $(document).on('click','.deletePOPayment',function(){
        var pid = $(this).attr('data-pid');
        var reference = $(this);
        if(confirm('Do you want to remove payment?')){
            $.ajax({
                headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
                url: '/po/deletePOPayment/'+pid,
                type: 'POST',
                data: {},
                dataType:'JSON',
                success: function (data) {
                    if (data.status == 200) {
                        alert(data.message);
                        reference.closest("div").remove();
                        $("#leid").click(); 
                    }
                },
                error: function (response) {
                }
            });
        }else{
            return false;
        }
    });

    $(document).on('click','.editpayment',function(){
        var pid = $(this).attr('data-payid');
        $('#popuphead').text('Edit Payment');
        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: '/payment/paymentdetailsdata/' + pid,
            type: "POST",
            data: {},
            success: function (response) {
                response = jQuery.parseJSON(response);
                var pay_id = response.pay_id;
                var cost_center = response.cost_center;
                var pay_for = response.pay_for;
                var ledger_group = response.ledger_group;
                var ledger_account = response.ledger_account;
                var paid_through = ledger_account+'==='+ledger_group;
                var pay_type = response.pay_type;
                var pay_amount = response.pay_amount;
                var pay_date = response.pay_date;
                var d = new Date(pay_date);
                var date = d.getDate();
                var month = d.getMonth();
                var year = d.getFullYear();
                var txn_reff_code = response.txn_reff_code;
                $('#edit_payid').val(pay_id);
                $('#cost_center').val(cost_center).select2();
                $("#payment_for").val(pay_for).select2();
                $("#paid_through").val(paid_through).select2();
                $("#payment_type").val(pay_type).select2();
                $('#payment_ref').val(txn_reff_code);
                $('#payment_amount').val(pay_amount);
                $('#transmission_date').val(month+'/'+date+'/'+year);
                //$('#pmtHistoryBody').html(response);
                if($("#payment_for").val()==16601 || $("#payment_for").val()==16602){
                    $("#showbannerpopupdetails").css("display", "block");
                    $('#item').select2('val',response.item_id).change();                    
                    $('#clicks').val(response.clicks);
                    $('#clicks_cost').val(response.click_cost);
                    $('#clicks_amt').val(response.click_amt);
                    $('#impressions').val(response.impressions);
                    $('#impressions_cost').val(response.impression_cost);
                    $('#impressions_amt').val(response.impression_amt);
                    var tot_amt=parseFloat(response.click_amt)+parseFloat(response.impression_amt);
                    $('#click_impressions_amt').val(tot_amt);
                    $('#config_mapping_id').val(response.config_mapping_id);
                    //$('#banner_name').select2('val',response.config_mapping_id);
                }else{
                    $("#showbannerpopupdetails").css("display", "none");
                }
            },
            error: function (response) { }
        });
    });
    $(document).on('click','.addLePayment',function(){
        var leId = $(this).attr('data-leId');
        $('#le_id').val(leId);
    });
});

function poPaymentList(supplier_id) {
    $("#poPaymentList").igGrid({
            columns: [
                    {headerText: "Pay Code", key: "pay_code", dataType: "string", columnCssClass: "leftAlignment", width: "130px"},
                    {headerText: "PO Code", key: "po_code", dataType: "string", columnCssClass: "leftAlignment", width: "130px"},
                    {headerText: "PO Value", key: "po_value", dataType: "number",format: "0.00", columnCssClass: "rightAlignment", width: "95px"},
                    {headerText: "GRN Value", key: "grn_value", dataType: "number",format: "0.00", columnCssClass: "rightAlignment", width: "95px"},
                    {headerText: "Payment Type", key: "pay_type", dataType: "string", columnCssClass: "centerAlignment", width: "100px"},
                    {headerText: "Ledger Account", key: "ledger_account", dataType: "string", columnCssClass: "leftAlignment", width: "130px"},
                    {headerText: "Amount", key: "pay_amount", dataType: "number",format: "0.00", columnCssClass: "rightAlignment", width: "90px"},
                    {headerText: "Pay Date", key: "pay_date", dataType: "date",format: "dd/MM/yyyy", columnCssClass: "centerAlignment", width: "100px"},
                    {headerText: "Payment Ref.", key: "txn_reff_code", dataType: "string", columnCssClass: "leftAlignment", width: "130px"},
                    {headerText: "UTR Code", key: "pay_utr_code", dataType: "string", columnCssClass: "leftAlignment", width: "80px"},
                    {headerText: "Created By", key: "createdBy", dataType: "string", columnCssClass: "leftAlignment", width: "80px"},
                    {headerText: "Created At", key: "created_at", dataType: "date",format: "dd/MM/yyyy HH:mm:ss", columnCssClass: "leftAlignment", width: "120px"},
                    {headerText: "Actions", key: "actions", dataType: "string", columnCssClass: "leftAlignment", width: "120px"},               
                ],
            features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                
                ]
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                columnSettings: [
                    {columnKey: 'created_at', allowFiltering: false},                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
                    {columnKey: 'actions', allowFiltering: false},

                ]


            },
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 20,
                recordCountKey: 'totalPayments',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"

            }
                        ],
                        primaryKey: "pay_id",
        width: '100%',
        type: 'remote',
        dataSource: "/po/ajax/payments/" + supplier_id,
        responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }
        }); 
}
function legalEntityPaymentList(supplier_id) {
    $("#PaymentList").igGrid({
            columns: [
                    {headerText: "Pay Code", key: "pay_code", dataType: "string", columnCssClass: "leftAlignment", width: "130px"},
                    {headerText: "PO Code", key: "po_code", dataType: "string", columnCssClass: "leftAlignment", width: "130px"},
                    {headerText: "PO Value", key: "po_value", dataType: "number",format: "0.00", columnCssClass: "leftAlignment", width: "95px"},
                    {headerText: "GRN Value", key: "grn_value", dataType: "number",format: "0.00", columnCssClass: "leftAlignment", width: "95px"},
                    {headerText: "Pay For", key: "pay_for", dataType: "string", columnCssClass: "centerAlignment", width: "100px"},
                    {headerText: "Payment Type", key: "pay_type", dataType: "string", columnCssClass: "leftAlignment", width: "100px"},
                    {headerText: "Ledger Account", key: "ledger_account", dataType: "string", columnCssClass: "leftAlignment", width: "130px"},
                    {headerText: "Amount", key: "pay_amount", dataType: "number",format: "0.00", columnCssClass: "rightAlignment", width: "90px"},
                    {headerText: "Pay Date", key: "pay_date", dataType: "date",format: "dd/MM/yyyy", columnCssClass: "centerAlignment", width: "100px"},
                    {headerText: "Payment Ref.", key: "txn_reff_code", dataType: "string", columnCssClass: "leftAlignment", width: "130px"},
                    {headerText: "UTR Code", key: "pay_utr_code", dataType: "string", columnCssClass: "leftAlignment", width: "80px"},
                    {headerText: "Created By", key: "createdBy", dataType: "string", columnCssClass: "leftAlignment", width: "80px"},
                    {headerText: "Created At", key: "created_at", dataType: "date",format: "dd/MM/yyyy HH:mm:ss", columnCssClass: "leftAlignment", width: "120px"},
                    {headerText: "Appoval Status", key: "approval_status", dataType: "string", columnCssClass: "leftAlignment", width: "100px"},
                    {headerText: "Actions", key: "actions", dataType: "string", columnCssClass: "leftAlignment", width: "60px"},
                ],
            features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'actions', allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                columnSettings: [                    
                    {columnKey: 'actions', allowFiltering: false},                    
                ]


            },
            {
                name: "ColumnFixing",
                fixingDirection: "right",
                columnSettings: [                    
                    {
                        columnKey: "pay_code",
                        allowFixing: false,
                    },
                    {
                        columnKey: "po_code",
                        allowFixing: false,
                    },
                    {
                        columnKey: "po_value",
                        allowFixing: false,
                    },
                    {
                        columnKey: "grn_value",
                        allowFixing: false,
                    },
                    {
                        columnKey: "pay_for",
                        allowFixing: false,
                    },
                    {
                        columnKey: "pay_type",
                        allowFixing: false,
                    },
                    {
                        columnKey: "ledger_account",
                        allowFixing: false,
                    },                    
                    {
                        columnKey: "pay_amount",
                        allowFixing: false,
                    },
                    {
                        columnKey: "pay_date",
                        allowFixing: false,
                    },
                    {
                        columnKey: "txn_reff_code",
                        allowFixing: false,
                    },
                    {
                        columnKey: "pay_utr_code",
                        allowFixing: false,
                    },
                    {
                        columnKey: "createdBy",
                        allowFixing: false,
                    },
                    {
                        columnKey: "created_at",
                        allowFixing: false,
                    },
                    {
                        columnKey: "approval_status",
                        isFixed: true,
                        allowFixing: false,
                    },
                    {
                        columnKey: "actions",
                        isFixed: true,
                        allowFixing: false,
                    },
                ]
            },
            {
        
                name: 'Paging',
                type: 'remote',
                pageSize: 20,
                recordCountKey: 'totalPayments',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"


            }
        ],
        primaryKey: "pay_id",
        width: '100%',
        type: 'remote',
        dataSource: "/po/ajax/payments/" + supplier_id+"/legal",
        responseDataKey: 'data',
        rendered: function (evt, ui) {
                $("#PaymentList_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
                $("#PaymentList_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
                $("#PaymentList_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();    
                //$("#PaymentList_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
                $("#PaymentList_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
                $("#PaymentList_container").find(".ui-iggrid-filtericonbefore").closest("li").remove();   
                $("#PaymentList_container").find(".ui-iggrid-filtericontoday").closest("li").remove();   
                $("#PaymentList_container").find(".ui-iggrid-filtericonyesterday").closest("li").remove();   
                $("#PaymentList_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
                $("#PaymentList_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
                $("#PaymentList_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
                $("#PaymentList_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
                $("#PaymentList_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
                $("#PaymentList_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
                $("#PaymentList_container").find(".ui-iggrid-filtericonon").closest("li").remove();   
                $("#PaymentList_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();
                $("#PaymentList_container").find(".ui-iggrid-filtericonafter").closest("li").remove();
            }
        });
    $('.enableit').attr('readonly',false);
    $('.enableit').attr('disabled', false);
    $('.enableit').css('display','block');
}
$("#item").change(function (){

   
    var item=$("#item").val();
    var cost_center=$('#cost_center').val();
    var payment_for = $('#payment_for').val();
    var token = $('#csrf-token').val();

     $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/suppliers/getbannerslist",
                type:"POST",
                data: 'item='+item+'&cost_center='+cost_center+'&payment_for='+payment_for,
                success:function(data){
                 $("#banner_name").empty(); 
                 $("#banner_name").html(data);
                 if($('#config_mapping_id').val()!=''){
                    $('#banner_name').select2('val',$('#config_mapping_id').val());
                 }
                }
        });   
    
});
$("#banner_name").change(function (){

   
    var banner_id=$("#banner_name").val();
    var cost_center=$('#cost_center').val();
    var payment_for = $('#payment_for').val();
    var token = $('#csrf-token').val();
    var pay_id=$('#edit_payid').val();

     $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/suppliers/getimpressionclicksbybannerid",
                type:"POST",
                data: 'banner_id='+banner_id+'&cost_center='+cost_center+'&payment_for='+payment_for+'&pay_id='+pay_id,
                dataType:"json",
                success:function(data){
                    //console.log(data.impression_info[0]);
                    //alert(data.impression_info[0].impressions_cost);
                    if(data.click_info[0].clicks!='' && data.click_info[0].clicks!=null){
                        var clkcnt=data.click_info[0].clicks;
                    }else{
                        var clkcnt=0;
                    }
                    if(data.click_info[0].clickamt!=''&& data.click_info[0].clickamt!=null){
                        var clkamt=data.click_info[0].clickamt;
                    }else{
                        var clkamt=0;
                    }
                    if(data.click_info[0].click_cost!='' && data.click_info[0].click_cost!=null){
                        var clkcost=data.click_info[0].click_cost;
                    }else{
                        var clkcost=0;
                    }
                    if(data.impression_info[0].impressions!='' && data.impression_info[0].impressions!=null){
                        var impcnt=data.impression_info[0].impressions;
                    }else{
                        var impcnt=0;
                    }
                    if(data.impression_info[0].impressions_cost!='' && data.impression_info[0].impressions_cost!=null){
                        var impcst=data.impression_info[0].impressions_cost;
                    }else{
                        var impcst=0;
                    }
                    if(data.impression_info[0].impressionsamt!='' && data.impression_info[0].impressionsamt!=null){
                        var impamt=data.impression_info[0].impressionsamt;
                    }else{
                        var impamt=0;
                    }
                    if(data.total_amt!='' && data.total_amt!=null){
                        var totamt=data.total_amt;
                    }else{
                        var totamt=0;
                    }
                    $('#clicks').val(clkcnt);
                    $('#clicks_cost').val(clkcost);
                    $('#clicks_amt').val(clkamt);
                    $('#impressions').val(impcnt);
                    $('#impressions_cost').val(impcst);
                    $('#impressions_amt').val(impamt);
                    $('#click_impressions_amt').val(totamt);
                    //console.log(data);
                    //console.log(data.click_info[0]);
                    //console.log(data.impression_info[0]);
                }
        });   
    
});

$("#payment_for").change(function (){
    var payment_for=$(this).val();

    if(payment_for==16601 || payment_for==16602){
        $("#showbannerpopupdetails").css("display", "block");
        
    }else{
        $("#showbannerpopupdetails").css("display", "none");
    }
         $("#item").select2('val','');
        $("#banner_name").select2('val','');
        $('#clicks').val('');
        $('#clicks_cost').val('');
        $('#clicks_amt').val('');
        $('#impressions').val('');
        $('#impressions_cost').val('');
        $('#impressions_amt').val('');
        $('#click_impressions_amt').val('');

});
