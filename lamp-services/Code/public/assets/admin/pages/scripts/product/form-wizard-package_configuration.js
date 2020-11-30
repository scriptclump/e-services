var PackConficFormWizard = function () {


    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

           var form_wh = $('#package_configuration');
            var error = $('.alert-danger', form_wh);
            var success = $('.alert-success', form_wh);
            jQuery.validator.addMethod("notEqual", function(value, element, param) {
              return this.optional(element) || value != param;
            }, "Please select pack level.");
            form_wh.validate({
			    onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {

                   /* packSkuCode:{
                        required: true
                    },*/
                    packageLevel:{
                         required: true,
                         notEqual:''
                    },
                    packEaches:{
                         required: true,
                    },
                    packInner: {

                        required: true
                    },
                    pack_lenght: {
                        required: true
                    },  
                    pack_breadth: {
                        required: true
                    },                  
                    pack_height: {
                        required: true
                    },
                    weight: {
                        required: true
                    },  
                   /* stackHeight: {
                        required: true
                    },
                    packingMeterial: {
                        required: true
                    },                   
                    palleteCapacity: {
                        required: true
                    },*/
                    effective_date: {
                        required: true
                    },

                },

                messages: { // custom messages for radio buttons and checkboxes
                    
                   packageLevel: {
                       remote: jQuery.validator.format("This Level is already exists...") 
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
              packid = $('#edit_pack_id').val();

                     var data = $('form#package_configuration').serialize();
					 
                    $.ajax({                            
                        beforeSend: function () {
                            $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);
                        },

                    headers: {'X-CSRF-TOKEN': token},
                    url:"/editPackageLevel/"+$('#product_id').val(),
                    data:data,
                    processData: false,
                    contentType: false, 
                    success:function(response){                        
                    if(response == '1')
                    {
                        $.ajax({                                                           
                             headers: {'X-CSRF-TOKEN': token},
                         url:'/packageConfigurations',
                         data: data,
                         processData: false,
                         contentType: false, 
                         success:function(response){
                             $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);  
                         //$("#addSupplierWarehouseGrid").igHierarchicalGrid({"dataSource":'/suppliers/getWarehouseList'});
                         $("#packingConfigGrid").igHierarchicalGrid({"dataSource":'/packingproducts/'+$('#product_id').val()});
                         alert("Package Information Successfully Saved.");
                         $(".close").trigger('click'); 
                         $('form[id="package_configuration"]')[0].reset();
                                 },
                       });
                                        }
					else if(response == '0')
					{
						$('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true); 
						  alert('Effective date already exists.');
					}
					else if(response == '3')
					{
						$('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true); 
						alert('Please Check, an Entry already exists with this Level and Effective Date');
					}else if(response == '4')
                    {
                        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true); 
                           alert('Eaches quantity already exists.');
                    }
					else if(response == '5')
					{
                        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true); 
                           alert("Invalid 'Eaches#' and 'SU' Configuration.");
					}
                    else if(response == '6')
                    {
                        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true); 
                           alert("Failed to save package information.Please try again.");
                    }
                    else{
                        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true); 
                           alert('Each level already exists.');
                    }
					}});
					
					 

                    return;
                  
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