//This validation is only for Product Pages
$('#updateproducts').validate({
    rules: {
        soh_update: {
            required: true
        },
        excess_qty: {
            required: true
        },
        reason: {
            required: true
        },
        inventory_comments: {
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

        var token = $("#token_value").val();
//         var productid  = $("#product_id").val();
//         var warehouseId = $("#warehouse_id").val();
//         var soh_value = $("#soh_update").val();
//         var atp_value = $("#ATP_update").val();
//         var comments = $("#inventory_comments").val();
//         var reason = $("#reason").val();
//         var formData = new FormData();
// // "soh_value="+soh_value+"&atp_value="+atp_value+"&prod_id="+productid+"&ware_id="+warehouseId+"&comment="+comments+"&reason="+reason,
//         formData.append('soh_value', soh_value);
//         formData.append('atp_value', atp_value);
//         formData.append('product_id', productid);
//         formData.append('ware_id', warehouseId);
//         formData.append('comment', comments);
        var inputdata = $("#updateproducts").serialize();
        console.log("form data"+inputdata);
        // return false;
        $.ajax({
            // headers:{'X-CSRF-Token': token},
            type:"POST",
            data:inputdata,
            url:"/inventory/updateInventory?_token=" + token,
        beforeSend: function () {
            $('#loader').show();
            $("#update_products").attr('disabled', true);
        },
        complete: function () {
            $('#loader').hide();
            $("#update_products").removeAttr('disabled');
        },
            success:function(data)
            {
                // $("#inventorygrid").igHierarchicalGrid("dataBind");
                // $('#edit-products').modal('toggle');
                $("#inventory_comments").val("");
                $("#reason").val("");
                if(data == "allzero")
                {
                    $("#success_message_popup_box").html('<div class="flash-message"><div class="alert alert-danger">All Inputs are empty!!</div></div>');
                    // $(".alert-danger").fadeOut(5000);
                    return false;
                }
                if(data == "negitivevalues")
                {
                    $("#success_message_popup_box").html('<div class="flash-message"><div class="alert alert-danger">Dit or Dnd values entered as wrong values!!</div></div>');
                    // $(".alert-danger").fadeOut(5000);
                    return false;
                }

                if(data == "failed")
                {
                    $("#success_message_popup_box").html('<div class="flash-message"><div class="alert alert-danger">Sum of dit qty and missing qty always be less than soh!!</div></div>');
                    // $(".alert-danger").fadeOut(5000);
                    return false;
                }
                
                if(data == "opentickets")
                {
                    $("#success_message_popup_box").html('<div class="flash-message"><div class="alert alert-danger">Error !! Approval request for same product is pending. Please close pending requests first to continue.</div></div>');
                    // $(".alert-danger").fadeOut(5000);
                    return false;
                }
                if(data == 0)
                {
                    $("#success_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>You are not permitted for this action</div></div>');
                    $(".alert-danger").fadeOut(5000)
                }
                else
                {
                    $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Updated successfully</div></div>');
                }
                $(".alert-success").fadeOut(5000);

                $('#edit-products').modal('toggle');
            }

        });
    

    }
});

$(".modal").on('hide.bs.modal', function () {
    $('#reason').val($('#reason').prop('defaultSelected'));
    $('#inventory_comments').val("");
    var form_id = $(this).find('form').attr('id');
    var validator1 = $("#updateproducts").validate();
    validator1.resetForm();
    $("#updateproducts div.form-group").removeClass('has-error');
});
