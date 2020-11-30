var FormWizard = function () {


    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

			
			//create suppliers
			var form = $('#createproduct');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            
            jQuery.validator.addMethod("valueNotEquals", function(value, element, arg){
            return arg != value;
            }, "This field is required.");
            
            $.validator.addMethod("lessThan", function(value, element) {
            var year = $("#date_estb").val(); //why not $(element) ?!?
            return (new Date()).getFullYear() >= parseInt(year, 10);
            }, "Invalid year");
            
            jQuery.validator.addMethod("email", function(value, element) {
            return this.optional(element) ||  /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/.test(value);
            }, "Email Invalid");
            
            $.validator.addMethod("alphanumeric", function(value, element) {
            return this.optional(element) || /^[a-z0-9]+$/i.test(value);
            }, "Invalid IFSC.");
            
            jQuery.validator.addMethod("url", function(value, element) {
            return this.optional(element) || /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi.test(value);
            });
            
            jQuery.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^[a-z\s]+$/i.test(value);
            }, "Only alphabetical characters");
             jQuery.validator.addMethod("product_pack_size_uom", function(value, element) {
            return this.optional(element) || /^[a-z\s]+$/i.test(value);
            }, "Only alphabetical characters");
            
            jQuery.validator.addMethod("erpregexp", function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
            }, "Only alphanumerics characters");

            $.validator.addMethod("uniqueProductName", function(value, element) {
              $.ajax({
                  type: "POST",
                   url: "checkproductname/"+value,
                  dataType:"html",
                  async: false, 
               success: function(msg)
               {
                console.log(msg);
                  // if the user exists, it returns a string "true"
                    // username is free to use
                    return false;
               }
             })}, "Username is Already Taken");
             jQuery.validator.addMethod("notEqual", function(value, element, param) {
              return this.optional(element) || value != param;
            }, "Please select...");
            form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    product_title: {
                       required: true,
                        remote: {
                            headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
                            url:"/checkproductname",
                            type: "post",
                            async: false,
                            delay:3000
                        }
                    },
                    product_name: {
                        required: true
                    },
                    brand_id: {
                        required: true,
                        notEqual:'0'
                    },
                    manufacturer_name:{
                        required: true,
                        notEqual:'0'
                    },                    
                    category: {
                        required: true,
                        notEqual:'0'
                    },
                    product_offer_pack:
                    {
                        required: true,
                        notEqual:'0'
                    },
                    product_suppliers:
                    {
                        required: true,
                        notEqual:'0'
                    },
                    product_esu:{
                         required: true,
                    },
                    product_star:{
                        required: true,
                        notEqual:'0'
                    },
                    product_pack_size_uom:{
                        required: true,
                        product_pack_size_uom:true
                    },
                    kvi_value:{
                        required: true,
                        notEqual:'0'
                    }
                },
                messages: { // custom messages for radio buttons and checkboxes
                    
                   product_title: {
                       remote: jQuery.validator.format("This Product Title is already exists...") 
                    }
                },
                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.attr("name") == "gender") { // for uniform radio buttons, insert the after the given container
                        error.insertAfter("#form_gender_error");
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
						$('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);
                        token  = $("#csrf-token").val();
                        var formData = $('#createproduct').serialize();
                        var formData = new FormData();
                        formData.append('_token', token);
                        formData.append('product_title', $("#product_title").val());
                        formData.append('product_name', $("#product_name").val());
                        formData.append('manufacturer_name', $("#manufacturer_name").val());
                        formData.append('brand', $("#brand_id").val());
                        formData.append('category',$('#category').val());
                        formData.append('product_mrp',$('#product_mrp').val());
                        formData.append('product_esu',$('#product_esu').val());
                        formData.append('product_star', $("#product_star").val());
                        formData.append('product_offer_pack',$('#product_offer_pack').val());
                        //formData.append('product_is_sellable',$('#product_is_sellable').val());
                        formData.append('product_each_qty',$('#product_each_qty').val());
                        formData.append('product_cfc_qty',$('#product_cfc_qty').val());
                        formData.append('product_pack_size',$('#product_pack_size').val());
                        formData.append('product_pack_size_uom',$('#product_pack_size_uom').val());
                        formData.append('product_suppliers',$('#product_suppliers').val());
                        formData.append('kvi',$('#kvi_value').val());
                         $.ajax({
                            headers: {'X-CSRF-TOKEN': token},
                            method: "POST",
                            url: '/productSave',
                            data: formData,
                            processData: false,
                            contentType: false,                                             
                            success: function (rs) {
								$('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , false);
                                if(rs == "true"){
                                    alert("Sorry failed to create a new product!");
                                    window.location.href ='/editproduct/'+rs;
                                }else if(rs == "false"){
                                    alert("You don't have permission to create a new product!");
                                }else{
                                    alert("Successfully Created Product.");
                                    window.location.href ='/editproduct/'+rs;
									//window.location.href =document.referrer;
                                }
                                   
                            }
                        });
                    

		     
					//document.getElementById("submit_form").submit();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }

            });
            
            $('#cancelmaninfo').on('click', function (e) {
             e.preventDefault();
             window.location="/products";
             });
            
            //create suppliers documents
	    var form = $('#supplierdocs');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            
            form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    pan_number: {
                        //required: true
                    },
                    pan_files: {
                         required: false, 
                         extension: "png|jpeg|jpg|pdf|doc|docx"
                    },
                    cin_number: {
                        //required: true
                    },
                    cin_files: {
                         required: false, 
                         extension: "png|jpeg|jpg|pdf|doc|docx"
                    },
                    tinvat_number: {
                        //required: true
                    },
                    tinvat_files: {
                         required: false, 
                         extension: "png|jpeg|jpg|pdf|doc|docx"
                    },
                    cst_number: {
                        //required: true
                    },
                    cst_files: {
                         required: false, 
                         extension: "png|jpeg|jpg|pdf|doc|docx"
                    },
                    cheque_files: {
                         required: false, 
                         extension: "png|jpeg|jpg|pdf|doc|docx"
                    },
                    mou_files: {
                         required: false, 
                         extension: "png|jpeg|jpg|pdf|doc|docx"
                    }
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.attr("name") == "gender") { // for uniform radio buttons, insert the after the given container
                        error.insertAfter("#form_gender_error");
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
		token  = $("#csrf-token").val();

                    success.show();
                    error.hide();

		       var formData = $('#supplierdocs').serialize();
                          var formData = new FormData();
                         formData.append('_token', token);
                         
                         formData.append('pan_number', $("#pan_number").val());
                         formData.append('cin_number', $("#cin_number").val());
                         formData.append('tinvat_number', $("#tinvat_number").val());
                         formData.append('cst_number', $("#cst_number").val());
                         formData.append('pan_files',$('#pan_files')[0].files[0]);
                         formData.append('cin_files',$('#cin_files')[0].files[0]);
                         formData.append('tinvat_files',$('#tinvat_files')[0].files[0]);
                         formData.append('cst_files',$('#cst_files')[0].files[0]);
                         formData.append('cheque_files',$('#cheque_files')[0].files[0]);
                         formData.append('mou_files',$('#mou_files')[0].files[0]);
						 $.ajax({
                            headers: {'X-CSRF-TOKEN': token},
                            method: "POST",
                            url: '/manufacturer/supplierdocs',
                            data: formData,
                            processData: false,
                            contentType: false,                                             
                            success: function (rs) { 
                                if(rs == "true")
                                {
                                    $("#success").css("display","block");
                                    $("#success").html('<li style="display:inline-block;text-align:center;color: #428bca;" class="success">Supplier Details Saved Successfully.</li>');
                                    setTimeout(function() { $("#success").css("display","none"); }, 10000); 
                                    $('a[href="#tab_33"]').trigger('click');
                                }
                            }
                        });
                    
					//document.getElementById("submit_form").submit();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }

            });
            
            $('#canceldocs').on('click', function (e) {
             e.preventDefault();
             window.location="/manufacturer";
             });
            
            
            //create suppliers required docs
			var form = $('#createdocs');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            
            jQuery.validator.addMethod("valueNotEquals", function(value, element, arg){
            return arg != value;
            }, "This field is required.");
            
            form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    pan_input: {
                        required: false
                    },
                    pan_fyl: {
                        required: false
                    },
                    cin_input: {
                        required: false
                    },
                    cin_file: {
                        required: false
                    },
                    tinvat_input: {
                        required: false
                    },
                    tinvat_file: {
                        required: false,
                    },
                    cst_input: {
                        required: false,
                    },
                    cst_file: {
                        required: false
                    },
                    cheque_file: {
                        required: false,
                    },
                    mou_fyl: {
                        required: false,
                    },
                    org_country: {
                        required: true,
                        valueNotEquals: ""
                    },
                    organization_type: {
                        required: true,
                        valueNotEquals: ""
                    }
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.attr("name") == "gender") { // for uniform radio buttons, insert the after the given container
                        error.insertAfter("#form_gender_error");
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
		token  = $("#csrf-token").val();

                    success.show();
                    error.hide();

		       var formData = $('#createdocs').serialize();
                          var formData = new FormData();
                         formData.append('_token', token);
                         formData.append('pan_input', $("#pan_input:checked").val());
                         formData.append('pan_fyl', $("#pan_fyl:checked").val());
                         formData.append('cin_input', $("#cin_input:checked").val());
                         formData.append('cin_file', $("#cin_file:checked").val());
                         formData.append('tinvat_input', $("#tinvat_input:checked").val());
                         formData.append('tinvat_file',$("#tinvat_file:checked").val());
                         formData.append('cst_input', $("#cst_input:checked").val());
                         formData.append('cst_file', $("#cst_file:checked").val());
                         formData.append('cheque_file', $("#cheque_file:checked").val());
                         formData.append('mou_fyl', $("#mou_fyl:checked").val());
                         formData.append('org_country', $("#org_country").val());
                         formData.append('organization_type', $("#organization_type").val());

						 $.ajax({
                            headers: {'X-CSRF-TOKEN': token},
                            method: "POST",
                            url: '/manufacturer/requireddocscreate',
                            data: formData,
                            processData: false,
                            contentType: false,                                             
                            success: function (rs) {
                                if(rs == "true")
                                {
                                    $("#success").css("display","block");
                                    $("#success").html('<li style="display:inline-block;text-align:center;color: #428bca;" class="success">Supplier Required Documents Details Saved Successfully.</li>');
                                    setTimeout(function() { $("#success").css("display","none"); }, 10000); 
                                }
                            }
                        });
                    
                }

            });
			
        }

    };

}();