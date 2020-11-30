<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script>
$(document).ready(function() {

var orderId = "<?php echo $orderdata->gds_order_id ?>";
  $(document).on('click','.collectionPopup',function() {

      $('form#collection_form')[0].reset();
      collection_validator.reset();
      collection_validator.resetForm();
      $(".error").removeClass("error");
      $('#invoice_code').select2("val", "");      

         $.ajax({
             headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
             url: '/salesorders/getInvoicesByOrderid/'+orderId,
             type: "POST",
             data: {},
             success: function (response) {
                $('#invoice_code').html(response)
                $('#invoice_code').select2("val", $("#invoice_code option:eq(1)").val());
                $("#invoice_code").trigger('change');             },
             error: function (response) {             }
         });




  } );


  $(document).on('click','.editCollectionPopup',function() {

         var collection_id = $(this).attr('collection_id'); 

         if(collection_id!="") {

           $.ajax({
               headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
               url: '/salesorders/collectiondetailbyid/'+collection_id,
               type: "POST",
               data: {},
               dataType: 'json',
               success: function (response) {
                  
                  $('#edit_coll_collection_id').val(response.collection_id);
                  $('#edit_coll_collection_history_id').val(response.history_id);
                  $('#edit_coll_invoice_code').val(response.invoice_code);
                  $('#edit_coll_mode_of_payment').val(response.payment_mode);
                  $('#edit_coll_reference_num').val(response.reference_no);
                  $('#edit_coll_collection_amount').val(response.amount);
                  $('#edit_coll_collected_by').select2("val", response.collected_by).select2("enable",false);
                  $('#edit_coll_collected_on').val(response.collected_on);
                  $('#edit_coll_remarks').val(response.reference_no);
                },
               error: function (response) { }
           });

         }




  } );

  $('#collected_on').datepicker({maxDate:0,minDate:0});

  $(document).on('change','#invoice_code',function() {

      var invoiceId = $(this).val();

      if(invoiceId!='') {



         $.ajax({
             headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
             url: '/salesorders/getInvoiceDueAmount/'+invoiceId,
             type: "POST",
             data: {},
             dataType: 'json',
             success: function (response) {
                //alert(response.Due_Amt)
                $('#invoice_due').val(response.Due_Amt)             
                $('#collection_amount').val(response.Due_Amt)             
              },
             error: function (response) {             }
         });



      }

  }); 

  $('form#edit_collection_form').on('submit', function(event) {

      event.preventDefault();


      var formData = $('#edit_collection_form').serialize();

                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
                        method: "POST",
                        url: '/salesorders/updateCollection',
                        data: formData,
                        dataType: 'json',
                        beforeSend: function () {
                           $('#loader1').show();
                        },
                        complete: function () {
                            $('#loader1').hide();
                        },
                        success: function (data) {
                          
                          if (data.status == 200) {
                              $('.loderholder').hide();
                              $('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html(data.message).show();
                              $('html, body').animate({scrollTop: '0px'}, 500);
                              $(".error").removeClass("error");
                              $('.close').trigger('click');
                              $(".collectionGrid").igGrid("dataBind");
                          } else {
                              $('#collectionAjaxResponse').removeClass('alert-success').addClass('alert-danger').html(data.message).show();
                              $('.loderholder').hide();
                              $('.close').trigger('click');
                              $('html, body').animate({scrollTop: '0px'}, 500);
                          }
                        }
                    });



  });

$.validator.addMethod('checkCollectionAmt', function(value, element) {
        
        return (parseFloat($('#invoice_due').val())>=value && value>0);
        
        //if($('#invoice_due').val())
    }, "Please enter valid Amount");

        var collection_validator = $('#collection_form').validate({
            rules: {
                invoice: {
                    required: true
                },
                invoice_due: {
                    required: true
                },
                mode_of_payment: {
                    required: true
                },
                collection_amount: {
                    required: true,
                    checkCollectionAmt:true
                },
                collected_by: {
                    required: true
                },
                collected_on: {
                    required: true
                }
            },
            submitHandler: function (form) {

                    var formData = new FormData();

                    var token = $("#csrf-token").val();
                    if ($('#proof').val())
                        formData.append('proof', $('#proof')[0].files[0]);


                    formData.append('_token', token);
                    formData.append('invoice', $("#invoice_code").val());
                    formData.append('mode_of_payment', $('#mode_of_payment').val());
                    formData.append('reference_num', $('#reference_num').val());
                    formData.append('collection_amount', $('#collection_amount').val());
                    formData.append('collected_by', $('#collected_by').val());
                    formData.append('collected_on', $('#collected_on').val());
                    formData.append('remarks', $('#remarks').val());
                    console.log(formData);
//      formData+='&_token=' + token;
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/salesorders/createCollection',
                        processData: false,
                        contentType: false,
                        data: formData,
                        dataType: 'json',
                        beforeSend: function () {
                           $('#loader1').show();
                        },
                        complete: function () {
                            $('#loader1').hide();
                        },
                        success: function (data) {
                          
                          if (data.status == 200) {
                              $('.loderholder').hide();
                              $('#collectionAjaxResponse').removeClass('alert-danger').addClass('alert-success').html(data.message).show();
                              $('html, body').animate({scrollTop: '0px'}, 500);
                              $('form#collection_form')[0].reset();
                              collection_validator.resetForm();
                              collection_validator.reset();
                              $(".error").removeClass("error");
                              $('a[href="#tab_15_2"]').trigger('click');
                              $('#invoice_code').select2("val", "");      
                              $(".collectionGrid").igGrid("dataBind");
                          } else {
                              $('#collectionAjaxResponse').removeClass('alert-success').addClass('alert-danger').html(data.message).show();
                              $('.loderholder').hide();
                              $('html, body').animate({scrollTop: '0px'}, 500);
                          }
                        }
                    });

                
        }
        });
});
</script>