var FormWizard = function () {


    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

			  
		
            var form = $('#supplier_add_products');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

// creating product packge configurations

           var form_wh = $('#warehouse_configuration');
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
                ignore: [],
                rules: {
                    warehouse_name:{
                        required: true,
                        notEqual:0, 
                    },
                    location_name_type:
                    {
                        required: true,
                        notEqual:''
                    },
                    location_name:{
                        required: true,
                        notEqual:0, 
                    },
                    level_type:
                    {
                        required:true,    
                         notEqual:0,            
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
                var data = $('form#warehouse_configuration').serialize();
                   $.ajax({
                            headers: {'X-CSRF-TOKEN': token},
                            url:'/savewarehousedata',
                            data: data, 
                            success:function(response){
                                if(response == 'bin_cat')
                                {
                                    alert("Please select bin category type.");
                                }
                                else if(response == 'bin_pro')
                                {
                                    alert("This Bin have Products. Unable to modify this bin.");
                                     $(".close").trigger('click'); 
                                }else
                                if(response=='rack_config')
                                {
                                    alert("This rack does not support this bin dimension type.");
                                }else
                                if(response=='lbh_false')
                                {
                                    alert("Please select Bin dimension type.");
                                }
                                else if(response==='false')
                                {
                                    alert("Location Name Already Exists .");
                                }else if(response=='location_error')
                                {
                                    alert("Please enter Location Name List.");
                                }
                                else if(response==1)
                                {
                                    //alert('in 1');
                                    $('#warehouse_config_grid').igTreeGrid('dataBind'); 
                                    $('#warehouse_config_grid').igTreeGrid({dataSource: 'getwarehouseconfig'});
									alert("Successfully saved.");									
                                    $(".close").trigger('click'); 
                                    $('form[id="warehouse_configuration"]')[0].reset();
                                    $('form[id="warehouse_configuration"]')[0].reset();
                                }                                
                              },
                          });
                     // return false;
                }

            });

        /*edit warehouse only*/
        // creating product packge configurations

           var form_wh_ed = $('#editWarehouse_model');
            var error_wh_ed = $('.alert-danger', form_wh);
            var success_wh_ed = $('.alert-success', form_wh);
            jQuery.validator.addMethod("notEqual", function(value, element, param) {
              return this.optional(element) || value != param;
            }, "Please specify a different (non-default) value");
            form_wh_ed.validate({
                onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: [],
                rules: {
                    edit_warehouse_name:{
                        required: true,
                    },
                    edit_location_name:{
                        required: true,
                    },
                    edit_level_type:
                    {
                        required:true,    
                         notEqual:0,            
                    },
                    edit_wh_length:
                    {
                        required:true,
                    },edit_wh_breadth:
                    {
                        required:true,
                    },edit_wh_height:
                    {
                        required:true,
                    },edit_weight_id:
                    {
                        required:true,
                    },edit_edit_weight_uom:
                    {
                        required:true,
                        notEqual:0,
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
                var data = $('form#editWarehouse_model').serialize();

                   $.ajax({
                            headers: {'X-CSRF-TOKEN': token},
                            url:'/editwarehousedata',
                            data: data, 
                            success:function(response){ 
                                if(response==='false')
                                {
                                    alert("This is name is already exist  for respective location type.");
                                }else
                                {
									$('#warehouse_config_grid').igTreeGrid('dataBind'); 
                                    $('#warehouse_config_grid').igTreeGrid({dataSource: 'getwarehouseconfig'});
                                    alert("successfully saved.");
                                    $(".close").trigger('click'); 
                                    $('form[id="editWarehouse_model"]')[0].reset();
                                }                                
                              },
                          });
                     // return false;
                }

            });
        /*this is for rack level multiple bin configurations....*/
         var form_multi_bin = $('#multi_bin_config');
            var error_multi_bin = $('.alert-danger', form_wh);
            var success_multi_bin = $('.alert-success', form_wh);
            jQuery.validator.addMethod("notEqual", function(value, element, param) {
              return this.optional(element) || value != param;
            }, "Please specify a different (non-default) value");
            form_multi_bin.validate({
                onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: [],
                rules: {
                    Rack_level_warehouse_name:{
                        notEqual: 0,
                    },
                    rack_level_name:
                    {
                        required:true,    
                         notEqual:0,            
                    },
                    rack_bin_type:
                    {
                        required:true,
                        notEqual:0,
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
                var data = $('form#multi_bin_config').serialize();
                $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        url:'/multiBinLevelConfig',
                        data: data+ '&rack_no_bins='+$("#rack_no_bins").val(), 
                        success:function(response){ 
                            if(response==='false')
                            {
                                alert("Please delete existing Bins.");
                                 $('form[id="multi_bin_config"]')[0].reset();
                            }else
                            {
                                $('#warehouse_config_grid').igTreeGrid('dataBind'); 
                                $('#warehouse_config_grid').igTreeGrid({dataSource: 'getwarehouseconfig'});
                                alert("Successfully Created.");
                                $(".close").trigger('click'); 
                                $('form[id="multi_bin_config"]')[0].reset();
                            }                                
                          },
                      });
                     // return false;
                }

            });

        /*bin dimentions configurations*/

           var form_bin_dim_conf = $('#binDimConf_model');
            var error_wh_ed = $('.alert-danger', form_wh);
            var success_wh_ed = $('.alert-success', form_wh);
            jQuery.validator.addMethod("notEqual", function(value, element, param) {
              return this.optional(element) || value != param;
            }, "Please specify a different (non-default) value");
            form_bin_dim_conf.validate({
                onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: [],
                rules: {
                    bin_dim_name:
                    {
                        required:true,    
                         notEqual:0,            
                    },
                    bin_dim_lenght:
                    {
                        required:true,
                        digits: true,
                    },bin_dim_width:
                    {
                        required:true,
                    },bin_dim_height:
                    {
                        required:true,
                    },bin_weight:
                    {
                        required:true,
                    },bin_lenghtUOm:
                    {
                        required:true,
                        notEqual:0,
                    },bin_lenghtUOm:
                    {
                        required:true,
                         notEqual:0,
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
                var data = $('form#binDimConf_model').serialize();
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url:'/saveBinDimensionsCong',
                    data: data, 
                    success:function(response){
                        if(response!='false')
                        {
                            alert("Successfully saved.");
                            $(".close").trigger('click'); 
                            $('form[id="binDimConf_model"]')[0].reset(); 
                        }else
                        {
                            alert("Bin configuration already exists.");
                            $('form[id="binDimConf_model"]')[0].reset(); 
                        }                                
                      },
                  });
                }
            });  
        }
    };
}();