
$(document).on('click','#approval_submit',function () {
    var approval_unique_id = $('#approval_unique_id').val();
    var approval_status = $('#approval_status').val();
    var approval_comment = $('#approval_comment').val();
    var approval_module = $('#approval_module').val();
    var current_status = $('#current_status').val();
    var table_name = $('#table_name').val();
    var unique_column = $('#unique_column').val();

    var due_amount = parseInt($('#due_amount').val()) || 0;
    var coins_on_hand = parseInt($('#coins_on_hand').val()) || 0;
    var notes_on_hand = parseInt($('#notes_on_hand').val()) || 0;
    var used_expenses = parseInt($('#used_expenses').val()) || 0;
    var submitted_amount = parseInt($('#submitted_amount').val()) || 0;
    var submittable_amount = parseInt($('#submitted_amount').attr('data-amount'));
    var fuel = parseInt($('#fuel').val()) || 0;
    var other_vehicle = parseInt($('#other_vehicle').val()) || 0;
    var fuel_image = '';
    var voucher_image = '';
    var due_deposited = parseInt($('#due_deposited').val()) || 0;
    var short = parseInt($('#short').val()) || 0;

    var denominations = '';

    $('.loderholder').show();
    
    if(current_status == 57055) {

        if(fuel>0 && $('#fuel_pic').val()=='') {

            alert('Please upload Picture for Fuel')
            return false;
        }

        if(other_vehicle>0 && $('#vehicle_pic').val()=='') {

            alert('Please upload Picture for Vehicle')
            return false;
        }


        if(typeof submitted_amount != 'undefined' ) {

            if(submittable_amount<submitted_amount) {

                alert('Actual Deposited cannot be greater than Amount To Be Submitted')
                return false;
            }
        }


        if($.trim(parseInt($('.net_diffrence').html())) > 0) {

                alert('Net Difference should be zero')
                return false;
        }

        if((coins_on_hand+notes_on_hand) != getDenominations()) {

                //alert('Total of Coins On Hand, Notes On Hand not matching with Denominations')
                //return false;
        }


        if((due_amount+submitted_amount) != submittable_amount) {

                //alert('Total of Actual Deposited, Due Amount is not matching with Amount To Be Submitted')
                //return false;
        }

        //fuel_image = $('#fuel_pic')[0].files[0];
        //voucher_image = $('#vehicle_pic')[0].files[0];
    
        denominations = getDenominationJson();

    }


    if(current_status == 57051) {
        
        var ecash_val = parseInt($('.by_ecash').html()) || 0;
        var by_cheque_val = parseInt($('.by_cheque').html()) || 0;
        var by_upi_val = parseInt($('.by_upi').html()) || 0;
        var by_online_val = parseInt($('.by_online').html()) || 0;

        var fuel = parseInt($('.fuel').html()) || 0;
        var vehicle = parseInt($('.vehicle').html()) || 0;
        var short = parseInt($('.short').html()) || 0;


        alert(submittable_amount-submitted_amount-ecash_val-by_cheque_val-by_upi_val-by_online_val-fuel-vehicle-short-coins_on_hand-notes_on_hand-used_expenses);


    }    

    var data;

    data = new FormData();

    data.append('approval_unique_id',approval_unique_id);
    data.append('approval_status', approval_status);

    data.append('approval_comment',approval_comment);
    data.append('approval_module', approval_module);

    data.append('current_status',current_status);
    data.append('table_name', table_name);

    data.append('unique_column',unique_column);
    data.append('due_amount', due_amount);

    data.append('coins_on_hand',coins_on_hand);
    data.append('notes_on_hand', notes_on_hand);

    data.append('used_expenses',used_expenses);
    data.append('denominations', JSON.stringify(denominations));

    data.append('fuel',fuel);
    data.append('extra_vehicle',other_vehicle);
    data.append('submitted_amount',submitted_amount);
    data.append('fuel_image',fuel_image);
    data.append('voucher_image',voucher_image);
    data.append('due_deposited',due_deposited);
    data.append('short',short);


    //{approval_unique_id: approval_unique_id, approval_status: approval_status, 
    //        approval_comment: approval_comment, approval_module: approval_module,
    //        current_status: current_status,table_name:table_name,unique_column:unique_column,
    //due_amount:due_amount,coins_on_hand:coins_on_hand,notes_on_hand:notes_on_hand,used_expenses:used_expenses,denominations:denominations,fuel:fuel,extra_vehicle:other_vehicle,submitted_amount:submitted_amount,fuel_image:fuel_image,voucher_image:voucher_image}

    $.ajax({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
        url: '/payments/approvalSubmit',
        type: 'POST',
        data: data,
        dataType: "json",
        contentType: false,
        processData: false,
        success: function (data) {
            $('#addSkubtn').attr('disabled', false);
            if (data.status == 200) {
                location.reload();
            } else {
                $('#error-msg').html(data.message).show();
                window.setTimeout(function () {
                    $('#error-msg').hide()
                }, 3000);
            }
        },
        error: function (response) {

        }
    });
});

$(document).on('click','.den-show-hide',function() {

    $('.denominations').toggle();

});


