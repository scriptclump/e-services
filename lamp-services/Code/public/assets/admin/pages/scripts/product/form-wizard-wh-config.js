var WarehouseFormWizard = function () {


    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

           var form_wh = $('#wh_bin_configuration');
            var error = $('.alert-danger', form_wh);
            var success = $('.alert-success', form_wh);
  
            jQuery.validator.addMethod("valueNotEquals", function(value, element, arg){
            return arg != value;
            }, "This field is required.");
            form_wh.validate({
                onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    wh_id: {
                        required: true,
                        valueNotEquals:'0'
                    },
                    bin_type:{
                        required: true,
                        valueNotEquals:'0'
                    }, 
                    wh_pack_type:{
                        required: true,
                        valueNotEquals:'0'
                    },                                        
                    pro_min_capacity: {
                        required: true
                    },
                    pro_max_capacity: {
                        required: true
                    }
                },
                errorPlacement: function (error, element) { // render error placement for each input type
                    error.appendTo(element.closest('.err1'));
                },

                invalidHandler: function (event, validator) { //display error alert on form submit   
                    success.hide();
                    error.show();
                    Metronic.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                   
                },

                submitHandler: function (form) {
                token  = $("#csrf-token").val();
                    var data = $('form#wh_bin_configuration').serialize();
                    $.ajax({                            
                    headers: {'X-CSRF-TOKEN': token},
                    url:"/saveWhBinConfigData/"+$('#product_id').val(),
                    data:data+ '&edit_wh_id=' + $("#edit_wh_id").val(),
                    processData: false,
                    success:function(response){
                        if(response=='false')
                        {
                            alert("This configurations already exit.");
                            $('form[id="wh_bin_configuration"]')[0].reset();
                        }else
                        {
                                alert("Successfully Saved.");    
                             $(".close").trigger('click'); 
                             $('form[id="wh_bin_configuration"]')[0].reset();   
                              warehouseBinConfigGrid();  
                        }
                               
                    }});
                  
                }

            });
   /* $(document).on('click', '.save_package', function (event) {
        
         if(form_wh.validate())
         {
              token  = $("#csrf-token").val();

                    
                    



         }
    });     */       
            
        }

    };

}();