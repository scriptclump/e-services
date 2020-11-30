$(function () {
    
    //for date 
    var date = new Date();
    $('#issued_date').datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function(datesel) {
            $('#add_nct_data').formValidation('revalidateField', 'issued_date');
        }
    });

    $('#date').datepicker({
        dateFormat: 'dd/mm/yy',
    });

    // Load Grid Data 
    nctTrackerGrid();

    // Code to check for the validation
    $.validator.setDefaults({
        onfocusout: function (element) {
            $(element).valid();
        },
        errorPlacement: function (error, element) {
            element.closest('.form-group').append(error);
        },
        unhighlight: function (element, errorClass, validClass) {
            if ($(element).hasClass('optional') && $(element).val() == '') {
                $(element).removeClass('error valid');
            } else {
                $(element).removeClass('error').addClass('valid');
            }
        }
    });

});

// view history data
function viewNctData(nctid){
    $('#view-upload-document').modal('toggle');
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/ncttracker/gethistorydetailsbyid/' + nctid,
        success: function (data)
        {

         if(data.profImage == ''){
                $('#a_prof_image').hide();
            }else{
                $("#a_prof_image").show();
                $('#prof_image').attr('src', data.profImage);
                $('#a_prof_image').attr('href', data.profImage);
            }

            $('#historyContainer').html(data.historyHTML);
            $('#ref_no').html(data.reffNo);
            $('#det_amount').html(data.amount);
            $('#collectedby').html(data.collectedBy);
            

        }
    });
}
//update nct details

function updateNCTData(nctrefno){
    
    $('#view-update-document').modal('toggle');

    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/ncttracker/getNctDataByRow/' + nctrefno,
        success: function (data)
        {
            $('#issued_date_view').datepicker("option", "minDate", new Date(data.IssuedDateForDate));
            $("#amount_view").val(data.Amount);
            $("#balance_amount_update").val(data.Balance);
            $("#reference_no_view").val(data.ReffNo);
            $("#bank_name_view").val(data.BankName);
            $("#branch_name_view").val(data.BranchName);
            $("#holder_name_view").val(data.Holdername);
            $("#status_view").val(data.Status);
            $("#collected_by_view").val(data.CollectedByName);
            $("#user_id_view").val(data.collectedById);
            $("#issued_date_view").val(data.IssuedDate);
            $("#deposited_to_view").val(data.DepositedTo);
            $("#nct_id_view").val(data.NCTid);
            $("#MaintableId_view").val(data.MaintableId);
            $("#balance_amt_update").html('<b>Balance Amount: [ '+data.Balance+' ]</b>');
            $("#extra_charge_view").val('');
            $("#extra_charge_div").hide();
            if(data.Status == '11904'){
                $("#Save_Button").hide();
            }else{
                $("#Save_Button").show();
            }

        }
    });
}

//for cheque image
function viewNctCheckPage(nctid){
    $('#viewcheque-page-document').modal('toggle');
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/ncttracker/getcollectionimagebyid/' + nctid,
        success: function (data)
        {
            $('#prof_image_cheque').attr('src', data);
        }
    });  
}


$(".modal").on('hidden.bs.modal', function () {
    $('#add_nct_data').formValidation('resetForm', true);
        //Removing the error elements from the from-group
        $('.form-group').removeClass('has-error has-feedback');
        $('.form-group').find('small.help-block').hide();
        $('.form-group').find('i.form-control-feedback').hide();
});

/*$(function () {
    $("#upload-document").bind("click", function () {
    $("#ledger")[0].selectedIndex = 0;
    });
});*/

// empty values hide the button
$('#upload-document').on('hide.bs.modal', function () {
    $('#add_nct_data').formValidation('resetForm', true);
    $('#add_nct_data')[0].reset();
    $("#collected_by").val('');
    $("#issued_date").val('');
});

$('#update-status-document').on('hide.bs.modal', function () {
    $('#update_status_nct_data').formValidation('resetForm', true);
    $('#update_status_nct_data')[0].reset();
    $("#comment").val('');
    $("#changes_by").val('');
});


//ajax search by name
/*$( "#holder_name" ).autocomplete({   
        minLength:2,
        source: '/ncttracker/getholdernamefromtallyledger',  
        select: function (event, ui) {
            var label = ui.item.label;
            var tlm_name = ui.item.tlm_name;
            var tlm_name = ui.item.tlm_name;
            $("#holder_name").val(tlm_name);
        }
});*/
//ajax search for ifsc code
$( "#ifsc_code" ).autocomplete({   
        minLength:2,
        source: '/ncttracker/getifsclist',  
        select: function (event, ui) {
            var label = ui.item.label;
            var ifsc = ui.item.ifsc;
            var branch = ui.item.branch;
            var bank = ui.item.bank_name;
            $("#ifsc_code").val(ifsc);
            $("#bank_name").val(bank);
            $("#branch_name").val(branch);
        }
});

//add nctdetails
function addNctData(nctid,bal){
    var maintableid  = document.getElementById('MaintableId').value = nctid;
    $('#upload-document').modal('toggle');
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/ncttracker/getnctdetailsbyid/' + nctid,
        success: function (data)
        {   
                

                $('#issued_date').datepicker("option", "minDate", new Date(data.IssuedDateForDate));
                $("#amount").val(bal);
                $("#reference_no").val(data.ReffNo);
                $("#bank_name").val(data.BankName);
                $("#branch_name").val(data.BranchName);
                $("#holder_name").val(data.Holdername);
                $("#status").val(data.Status);
                $("#collected_by").val(data.CollectedByName);
                $("#user_id").val(data.collectedById);
                $("#issued_date").val(data.IssuedDate);
                $("#deposited_to").val(data.DepositedTo);
                $("#balance_amount").val(bal);
                $("#balance_amt").html('<b>Balance Amount: [ '+bal+' ]</b>');
        }
    });
}

// for show the grid
function nctTrackerGrid()
{  
 var url = "/ncttracker/ncttrackerdata"; 
        $('#ncttrackerdatagrid').igHierarchicalGrid({
            dataSource: url,
            dataSourceType: "json",
            responseDataKey: "result",
            initialDataBindDepth: 1,
            autoGenerateColumns: false,
            primaryKey: "history_id",
            height:"100%",
            columns: [
                {headerText: "History ID", key: "history_id", dataType: "number",width: "5%", hidden: true},
                {headerText:"Collection Code", key: "collection_code", dataType: "string", width: "12%", template: '<div class="textLeftAlign"> ${collection_code} </div>'},
                {headerText:"Order No", key: "OrderCode", dataType: "string", width: "12%"},
                {headerText:"Business Units", key: "BuName", dataType: "string", width: "11%" },
                {headerText:"Pmt Mode", key: "master_lookup_name", dataType: "string", width: "7%", template: '<div class="textLeftAlign"> ${master_lookup_name} </div>'},
                {headerText:"REF NO", key: "reference_no", dataType: "string", width: "12%", template: '<div class="textLeftAlign"> ${reference_no} </div>'},
                {headerText:"Amount", key: "amount", dataType: "number", width: "6%", template: '<div style="text-align:right;"> ${amount} </div>'},
                {headerText:"Trans Date", key: "collected_on", dataType: "date", format:"dd/MM/yyyy", width: "10%", template: '<div style="text-align:center;"> ${collected_on} </div>'},
                {headerText:"Status", key: "CollectionStatus", dataType: "string", width: "10%", template: '{{html CollectionStatus}}'},
                {headerText: "Trans By", key: "FullName", dataType: "string", width: "12%", template: "${FullName}" },
                {headerText:"Actions", key: "CustomAction", width: "8%", dataType: "string"}
            ],
            features: [
                {
                    name: "Sorting",
                    type: "remote",
                    columnSettings: [
                    {columnKey: 'collection_code', allowSorting: true },
                    {columnKey: 'master_lookup_name', allowSorting: true },
                    {columnKey: 'reference_no', allowSorting: true },
                    {columnKey: 'amount', allowSorting: true },
                    {columnKey: 'FullName', allowSorting: true },
                    {columnKey: 'collected_on', allowSorting: true },
                    {columnKey: 'collected_by', allowFiltering: true },
                    {columnKey: 'CustomAction', allowSorting: false },
                    {columnKey: 'OrderCode', allowSorting: true },
                    {columnKey: 'BuName', allowSorting: true },
                    ]
                },
                {
                    name: "Filtering",
                    type: "remote",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'collection_code', allowFiltering: true },
                        {columnKey: 'master_lookup_name', allowFiltering: true },
                        {columnKey: 'reference_no', allowFiltering: true },
                        {columnKey: 'amount', allowFiltering: true },
                        {columnKey: 'FullName', allowFiltering: true },
                        {columnKey: 'collected_on', allowFiltering: true },
                        {columnKey: 'collected_by', allowFiltering: true },
                        {columnKey: 'CustomAction', allowFiltering: false },
                        {columnKey: 'OrderCode', allowFiltering: true },
                        {columnKey: 'BuName', allowFiltering: true },
                    ]
                },
                {
                     
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 2,
                    name: 'Paging', 
                    loadTrigger: 'auto', 
                    type: 'local'
                }
            ],
            autoGenerateLayouts: false,
            columnLayouts: [{
                    key: "nct_details",
                    responseDataKey: "results",
                    autoGenerateColumns: false,
                    primaryKey: "nct_id",
                    foreignKey: "nct_history_id",
                    columns: [
                        {headerText: "REF NO", key: "nct_ref_no", dataType: "string", width: "25%", template: '<div class="textLeftAlign">${nct_ref_no} </div>'},
                        {headerText: "Bank Name", key: "nct_bank", dataType: "string", width: "20%", template: '<div class="textRightAlign">${nct_bank} </div>'},
                        {headerText: "Branch", key: "nct_branch", dataType: "string", width: "20%", template: '<div class="textLeftAlign">${nct_branch} </div>'},
                        {headerText: "Amount", key: "nct_amount", dataType: "string", width: "20%", template: '<div class="textLeftAlign">${nct_amount} </div>'},
                        {headerText: "Status", key: "master_lookup_name", dataType: "string", width: "20%", template: '<div class="textLeftAlign">${master_lookup_name} </div>'},
                        {headerText: "Actions", key: "CustomAction", dataType: "string", width: "10%"}
                    ]
                }]
        });
}

//ajax search by name
$( "#changes_by" ).autocomplete({   
        minLength:2,
        source: '/ncttracker/getuserlist',  
        select: function (event, ui) {
            var label = ui.item.label;
            var firstname = ui.item.firstname;
            var user_id = ui.item.user_id;
            $("#collected_user_id").val(user_id);
        }
});

//form validation and save the data into main table
$('#add_nct_data').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        cheque_no: {
            validators: {
                notEmpty: {
                    message: 'Please Enter No '
                }
            }
        },
        bank_name: {
            validators: {
                notEmpty: {
                    message: 'Select bank name'
                },
                regexp: {
                    regexp: /^[a-zA-Z ]*$/,
                    message: 'Bank name allow only alphabets'
                }
            }
        },
        holder_name: {
            validators: {
                notEmpty: {
                    message: 'Enter holder name'
                }
            }
        },
        issued_date: {
            validators: {
                notEmpty: {
                    message: 'Enter date '
                },
                issued_date: {
                        format: 'DD-MM-YYYY',
                        message: 'Voucher Date is not a valid'
                    }
            }
        },
        collected_by: {
            validators: {
                notEmpty: {
                    message: 'Enter collected by'
                },
                regexp: {
                    regexp: /^[a-zA-Z ]*$/,
                    message: 'Collected by only alphabets'
                }
            }
        },
        amount: {
            validators: {
                notEmpty: {
                    message: 'Enter amount'
                },
                between: {
                    min: 0.1,
                    max: 'balance_amount',
                    message: 'Please enter valid amount.'
                },
                numeric: {
                    message: 'The value is not a number',
                }
            }
        },
        deposited_to: {
            validators: {
                notEmpty: {
                    message: 'Select deposited bank '
                }
            }
        },
        reference_no: {
            validators: {
                notEmpty: {
                    message: 'Enter Ref No'
                },
                regexp: {
                    regexp:  /^[a-zA-Z0-9]+$/,
                    message: 'Ref No allows only Alphabets and Numbers.'
                }
            }
        },

        status: {
            validators: {
                notEmpty: {
                    message: 'Please select status'
                }
            }
        },
        
    }
})
.on('success.form.fv', function(e){
    e.preventDefault();

    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/ncttracker/addnctdata',
        data: new FormData($("#add_nct_data")[0]),
        processData: false,
        contentType: false,
        success: function (respData)
        {
            $('#upload-document').modal('toggle');
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+respData+'</div></div>' );
            $(".alert-success").fadeOut(30000)
            nctTrackerGrid();
        }
    });

});

//keydown for issued date
$("#issued_date, #date").keydown(function() {
    return false;
});

//form validation for update page and update main table and history table
$('#update_status_nct_data').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        update_status: {
            validators: {
                notEmpty: {
                    message: 'Please select status '
                }
            }
        },
        comment: {
            validators: {
                notEmpty: {
                    message: 'Enter Comment'
                }
            }
        },

        update_status: {
            validators: {
                notEmpty: {
                    message: 'Please Select Status'
                }
            }
        },
    }
})
.on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#update_status_nct_data').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/ncttracker/updatestatusnctdetails',
        data: frmData,
        success: function (respData)
        {
            $('#update-status-document').modal('toggle');
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+respData+'</div></div>' );
            nctTrackerGrid();
            $(".alert-success").fadeOut(20000)

        }
    });
});


// ajax search by name
$( "#collected_by" ).autocomplete({
        minLength:2,
        source: '/ncttracker/getuserlist',  
        select: function (event, ui) {
            var label = ui.item.label;
            var firstname = ui.item.firstname;
            var user_id = ui.item.user_id;
            $("#user_id").val(user_id);
        }
});
//empty the value open modal page
$('#upload-document').on('show.bs.modal', function (e) {
       $("#cheque_no").val('');$("#bank_name").val('');
       $("#branch_name").val('');$("#holder_name").val('');
       $("#issued_date").val('');$("#collected_by").val('');
       $("#status").val('');$("#amount").val('');
       $("#reference_no").val('');
});

//save and update each row in nct
$('#update_nct_data').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        cheque_no_view: {
            validators: {
                notEmpty: {
                    message: 'Please Enter No '
                }
            }
        },
        bank_name_view: {
            validators: {
                notEmpty: {
                    message: 'Select bank name'
                },
                regexp: {
                    regexp: /^[a-zA-Z ]*$/,
                    message: 'Bank name allow only alphabets'
                }
            }
        },
        holder_name_view: {
            validators: {
                notEmpty: {
                    message: 'Select holder name'
                }
            }
        },
        issued_date_view: {
            validators: {
                notEmpty: {
                    message: 'Enter date '
                },
                issued_date_view: {
                        format: 'DD-MM-YYYY',
                        message: 'Voucher Date is not a valid'
                    }
            }
        },
        collected_by_view: {
            validators: {
                notEmpty: {
                    message: 'Enter collected by'
                },
                regexp: {
                    regexp: /^[a-zA-Z ]*$/,
                    message: 'Bank name allow only alphabets'
                }
            }
        },
        amount_view: {
            validators: {
                notEmpty: {
                    message: 'Enter amount'
                }
            }
        },
        deposited_to_view: {
            validators: {
                notEmpty: {
                    message: 'Select deposited bank '
                }
            }
        },
        reference_no_view: {
            validators: {
                notEmpty: {
                    message: 'Enter Ref No'
                },
                regexp: {
                    regexp:  /^[a-zA-Z0-9]+$/,
                    message: 'Ref No allows only alphabets and numbers.'
                }
            }
        },

        status_view: {
            validators: {
                notEmpty: {
                    message: 'Please select status'
                },
                callback: {
                    callback: function(value, validator, $field) {
                        if($('#status_view').val()==11908){
                            $('#update_nct_data').formValidation('enableFieldValidators', 'extra_charge_view', true);
                            $("#extra_charge_div").show();
                            var charge = $('#extra_charge_view').val();
                            return (charge !== '') ? true : (value !== '');
                        
                        }else{
                            $('#update_nct_data').formValidation('enableFieldValidators', 'extra_charge_view',false);
                            $("#extra_charge_div").hide();
                            return true;
                        }
                        
                    }
                }

            }
        },
        extra_charge_view: {
            validators: {
                notEmpty: {
                    message: 'Enter extra charge'
                },
                numeric: {
                    message: 'The value is not a number',
                }
            }
        },

        
    }
})
.on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#update_nct_data').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/ncttracker/updatenctdata',
        data: frmData,
        success: function (respData)
        {
            $('#view-update-document').modal('toggle');
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+respData+'</div></div>' );
            $(".alert-success").fadeOut(20000);
            nctTrackerGrid();
            

        }
    });
});

// to get particular option
$('#status_view').change(function () {
    var option = $(this).find(':selected')[0].value;
    
    $.ajax({
        type: 'GET',
        url: '/ncttracker/getDeposited/'+option,
        success: function (data) {
            // the next thing you want to do 
            var $depositedOptions = $('#deposited_to_view');
            $depositedOptions.empty();
            $depositedOptions.append(data);
            $depositedOptions.change();
            $('#update_nct_data').formValidation('revalidateField', 'deposited_to_view');

        }
    });

});