var FormWizard = function () {


    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

			         //save approval
		
            var form_comments = $('#approval_form');
            form_comments.validate({
                onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    approval_select: {
                        required: true
                    },                   
                    approval_comments: {
                        required: true,
                    }
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                        error.insertAfter(element); // for other inputs, just perform default behavior
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
					
                        var formData = new FormData();
                        var token = $("#csrf-token").val();  
                        formData.append('approval_comments', $("#approval_comments").val());              
                        formData.append('_token',token);
                        formData.append('approval_select_id',$("#approval_select_id").val());
                        formData.append('approval_for_id',$("#approval_for_id").val());
                        formData.append('approval_type_id',$("#approval_type_id").val());  
			 $.ajax({
                            headers: {'X-CSRF-TOKEN': token},
                            method: "POST",
                            url: '/approvalsave',
                            processData: false,
                            contentType: false,                                             
                            data: formData,
                            success: function (rs) {
								
                                $('#approval_save').css('display','none');
                                $('#approva_comments').css('display','none');                                
                                $('#approval_row_id').css('display','none');      
                                alert('Approval submitted Successfully.');
                                window.location="/products";
                            }
                        });
                }

            });
		
            var form = $('#supplier_add_products');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

// creating product packge configurations

           var form_wh = $('#freebie_configuration');
            var error_wh = $('.alert-danger', form_wh);
            var success_wh = $('.alert-success', form_wh);
              jQuery.validator.addMethod("notEqual", function(value, element, param) {
              return this.optional(element) || value != param;
            }, "Please specify a different (non-default) value");
            form_wh.validate({
			    onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    freebie_mpq:{
                        required: true,
                    },
                    freeBieProduct_id:{
                       required: true,
                        notEqual: ''
                    },     
                                   
                    freeBieQty:{
                         required: true,
                    },
                    freebie_state_id: {
                        required: true,
                        notEqual: ''
                    },
                    freebie_warehouse_id: {
                        required: true,
                        notEqual: ''
                    },  
                    freebie_start_date: {
                        required: true
                    },                  
                    freebie_end_date: {
                        required: true
                    },
                    freebie_stock_limit: {
                        required: true
                    },


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

                     var data = $('form#freebie_configuration').serialize();
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                    url:'/freeBieConfigurations',
                    data: data,
                    processData: false,
                    contentType: false, 
                    success:function(response){
                        if(response == 'false')
                        {
                            alert("Please check product Is Selable/Offer Pack value.");
                            $(".close").trigger('click'); 
                            $('form[id="freebie_configuration"]')[0].reset();
                        }
                        else
                        {
                          //$("#addSupplierWarehouseGrid").igHierarchicalGrid({"dataSource":'/suppliers/getWarehouseList'});
                          $("#freeBieConfigGrid").igHierarchicalGrid({"dataSource":'/freeBieProducts/'+$('#product_id').val()});
                          alert("Freebie Information Successfully Saved.");
                          $(".close").trigger('click'); 
                          $('form[id="freebie_configuration"]')[0].reset();
                        }
                      },
                  });
                    return false;
                  
                    //document.getElementById("submit_form").submit();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }

            });
   /* $(document).on('click', '.save_package', function (event) {
        
         if(form_wh.validate())
         {
              token  = $("#csrf-token").val();

                    
                    



         }
    });     */   


            var form = $('#set_price_form');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            set_price_formwizard = form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    set_price_elp: {
						number: true,
                        valueNotEquals: ""
                    },
                    set_price_date: {
                        required: true
                    },
                },
                messages: {// custom messages for radio buttons and checkboxes
                    'payment[]': {
                        required: "Please select at least one option",
                        minlength: jQuery.validator.format("Please select at least one option")
                    }
                },
                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.attr("name") == "return_accepted") { // for uniform radio buttons, insert the after the given container
                        error.insertAfter(return_accepted);
                    } else if (element.attr("name") == "payment[]") { // for uniform checkboxes, insert the after the given container
                        error.insertAfter("#form_payment_error");
                    } else {
                        error.insertAfter(element); // for other inputs, just perform default behavior
                    }
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

                    var token = $("#csrf-token").val();
                    success.show();
                    error.hide();
                    var formData = $('#set_price_form').serialize();
                    formData += '&_token=' + token;
                    var setpriceElp = parseInt($("#set_price_elp").val());                    
                    var priceMrp    = parseInt($("#price_mrp").val());
                   

                    $.ajax({
                        beforeSend: function (xhr) {
                              $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);  
                      },
                        complete: function (jqXHR, textStatus) {
                        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
                      },
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/setPrice/save',
                        data: formData,
                        success: function (response) {
                                alert('LP saved successfully');
                                $(".close").trigger('click');
                        }
                    });

                }

            })    
			
        }

    };

}();