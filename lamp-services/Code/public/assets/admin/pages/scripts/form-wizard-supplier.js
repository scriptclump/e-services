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
            
            $.validator.addMethod("alphanumeric_sku", function(value, element) {
            return this.optional(element) || /^[a-z0-9]+$/i.test(value);
            }, "Please enter the alphanumeric values.");
            
            
            supplier_product_formwizard = form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    /*le_wh_id: {
                        required: true
                    },
                    brand: {
                        required: true
                    },
                    category: {
                        required: true
                    },*/
                    product_name: {
                        required: true
                    },
                    product_title: {
                        required: true
                    },
                    /*supplier_sku_code: {
                        number: false,
                        alphanumeric_sku : true
                    },*/
                    dlp: {
                        required: true,
                        number: true
                    },
                    /*distributor_margin: {
                        number: true

                    },
                    rlp: {
                        number: true
                    },
                    supplier_dc_relationship: {
                        required: true
                    },
                    grn_freshness_percentage: {
                        number: true
                    },
                    tax_type: {
                        required: true
                    },
                    tax: {
                        required: true,
                        number: true
                    },
                    moq: {
                        required: true,
                        number: true
                    },
                    moq_uom: {
                        required: true
                    },
                    delivery_terms: {
                        number: true
                    },
                    delivery_tat_uom: {
                        required: false
                    },
                    grn_days: {
                        required: false
                    },
                    rtv_allowed: {
                        required: false
                    },
                    inventory_mode: {
                        required: true
                    },
                    atp: {
                        number: true
                    },
                    atp_period: {
                        required: false
                    },
                    kvi: {
                        required: false
                    },
                    is_preferred_supplier: {
                        required: true
                    },*/
                    efet_dat: {
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

                    token = $("#csrf-token").val();

                    success.show();
                    error.hide();

                    var formData = $('#supplier_add_products').serialize();

                    formData += '&_token=' + token;

                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/tot/save',
                        data: formData,
                        success: function (rs) {

                            if ($.trim($("#supplier_tot_grid").html()) == "")
                            {                                
                                if ($("#supplier_tot_grid").length != 0)
                                {
                                    $('#supplier_list_grid').igHierarchicalGrid({dataSource: '/suppliers/getSuppliers'});
                                    console.log('000');
                                } else {
                                    totGrid(rs);                                    
                                }                                
                        
                            } else {                                
                                $("#supplier_tot_grid").igGrid({"dataSource": '/suppliers/getProducts/' + rs});
                                $('#supplier_tot_grid').igGrid('dataBind');
                                $("#close").trigger('click');                                
                            }
                            $("#close").trigger('click');
                            
                            $('#flass_message').text('TOT saved successfully.');
                            $('div.alert').show();
                            $('div.alert').removeClass('hide');
                            $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                            $('html, body').animate({scrollTop: '0px'}, 800);
                        }
                    });

                }

            });
            
            
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
                        required: true,
                        number: true
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
                    
                    if(setpriceElp>priceMrp || setpriceElp<=0) {
                        alert('Please provide valid ELP ');
                        return false;
                    }
                    
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/setPrice/save',
                        data: formData,
                        success: function (response) {
                                alert('Price for this product saved');  
                                $('#supplier_tot_grid').igGrid('dataBind');
                                $(".close").trigger('click');
                        }
                    });

                }

            });
            
// suppiler ware house save script

            var form_wh = $('#submit_form_wh');
            var error_wh = $('.alert-danger', form_wh);
            var success_wh = $('.alert-success', form_wh);

            form_wh.validate({
                onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    wh_name: {
                        required: true,
                        remote: {
                            headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
                            url: "/suppliers/warehuniq/" + $('#legalentity_id').val(),
                            type: "post",
                            async: false,
                        }

                    },
                    wh_email: {
                        required: true,
                        email: true

                    },
                    wh_cont_name: {
                        required: true,
                        lettersonly: true
                    },
                    wh_phone: {
                        required: true,
                        number: true
                    },
                    wh_address1: {
                        required: true
                    },
                    wh_address2: {
                        required: false
                    },
                    wh_pincode: {
                        required: true,
                        number: true,
                        minlength: 6
                    },
                    wh_city: {
                        required: true,
                        lettersonly: true

                    },
                    wh_state: {
                        required: true
                    },
                    wh_country: {
                        required: true
                    },
                },
                messages: { // custom messages for radio buttons and checkboxes

                    'payment[]': {
                        required: "Please select at least one option",
                        minlength: jQuery.validator.format("Please select at least one option")
                    }
                },
                errorPlacement: function (error, element) { // render error placement for each input type

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


                    //document.getElementById("submit_form").submit();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }

            });
            $(document).on('click', '.savewh', function (event) {

                if (form_wh.valid())
                {
                    token = $("#csrf-token").val();


                    var data = $('form#submit_form_wh').serialize();
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        url: '/suppliers/savewh',
                        data: data,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            $("#addSupplierWarehouseGrid").igHierarchicalGrid({"dataSource": '/suppliers/getWarehouseList'});
                            if (response === '1')
                            {
                                $("#addSupplierWarehouseGrid").igHierarchicalGrid({"dataSource": '/suppliers/getWarehouseList'});
                                $(".close").trigger('click');
                                $('form[id="submit_form_wh"]')[0].reset();
                                alert("Successfully Created.");
                            } else if (response === '2')
                            {
                                $("#addSupplierWarehouseGrid").igHierarchicalGrid({"dataSource": '/suppliers/getWarehouseList'});
                                $(".close").trigger('click');
                                $('form[id="submit_form_wh"]')[0].reset();
                                alert("Successfully Updated.");
                            } else
                            {
                                alert("Please select a supplier or create new supplier .");
                                $(".close").trigger('click');
                            }
                        },
                    });



                }
            });




            // Brand save scripts

            var form_brand = $('#add_brand_form');
            var error = $('.alert-danger', form_brand);
            var success = $('.alert-success', form_brand);


            brand_validation_var = form_brand.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    brand_name: {
                        required: true
                    },
                    brand_desc: {
                        required: true,
                    }
                },
                messages: {// custom messages for radio buttons and checkboxes
                    'brand_logo': {
                        extension: jQuery.validator.format("Please choose images only")
                    },
                    'brand_trademark_proof': {
                        extension: jQuery.validator.format("Please choose valid file only")
                    },
                    'brand_authorization': {
                        extension: jQuery.validator.format("Please choose valid file only")
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

                    success.show();
                    error.hide();

                    var formData = new FormData();

                    var token = $("#csrf-token").val();
                    formData.append('logo', $('#brandLogo')[0].files[0]);
                    formData.append('trademark_proof', $('#tradeMarkProof')[0].files[0]);
                    formData.append('authorization_proof', $('#authorizationProof')[0].files[0]);
                    formData.append('_token', token);
                    formData.append('brand_id', $('#edit_brand_id').val());
                    formData.append('brand_name', $('#brand_name').val());
                    formData.append('brand_desc', $('#brand_desc').val());
                    formData.append('brand_trademark_number', $('#brand_trademark_number').val());
//						formData+='&_token=' + token;
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/brand/save',
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function (rs) {

                            if ($.trim($("#brands_grid").html()) == "")
                            {
                                if ($("#brands_grid").length != 0)
                                {
                                    brandsGrid($('#legalentity_id').val());
                                } else {

                                    $('#supplier_list_grid').igHierarchicalGrid({dataSource: '/suppliers/getSuppliers'});

                                }

                            } else {

                                $("#brands_grid").igHierarchicalGrid({"dataSource": '/suppliers/getBrands/' + $('#legalentity_id').val()});
                            }


                            $(".close").trigger('click');
                        }
                    });

                }

            });

       

            //create suppliers
            var form = $('#suppliersinfo');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            jQuery.validator.addMethod("valueNotEquals", function (value, element, arg) {
                return arg != value;
            }, "This field is required.");

            $.validator.addMethod("lessThan", function (value, element) {
                var year = $("#date_estb").val(); //why not $(element) ?!?
                return (new Date()).getFullYear() >= parseInt(year, 10);
            }, "Invalid year");

            jQuery.validator.addMethod("email", function (value, element) {
                return this.optional(element) || /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/.test(value);
            }, "Email Invalid");

            $.validator.addMethod("alphanumeric", function (value, element) {
                return this.optional(element) || /^[a-z0-9]+$/i.test(value);
            }, "Invalid IFSC.");

            jQuery.validator.addMethod("lettersonly", function (value, element) {
                return this.optional(element) || /^[a-z\s]+$/i.test(value);
            }, "Only alphabetical characters");

            jQuery.validator.addMethod("url", function (value, element) {
                return this.optional(element) || /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi.test(value);
            });

            jQuery.validator.addMethod("erpregexp", function (value, element) {
                return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
            }, "Only alphanumerics characters");
            jQuery.validator.addMethod("erpregexr", function (value, element) {
                return this.optional(element) || /^[A-Z]{2}[ -][0-9]{1,2}(?: [A-Z])?(?: [A-Z]*)? [0-9]{4}$/.test(value);
            }, "invalid Registration");


            form.validate({
                onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    //org_file: {
                    //required:false,
                    //extension: "png|jpeg|jpg"
                    //},
                    organization_name: {
                        required: true,
                        remote: {
                            headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
                            //data:{'reg_no':$('#reg_no').val(),'veh_model':$("#organization_name option:selected").attr('value_type')},
                            url: "/suppliers/suppuniq",
                            type: "post",
                            async: false
                        }
                    },
                    organization_type: {
                        required: true,
                        valueNotEquals: ""
                    },
                    supplier_type: {
                        required: true,
                        valueNotEquals: ""
                    },
                    reference_erp_code: {
                        required: false,
                        erpregexp: true

                    },
                    supplier_rank: {
                        required: true,
                        valueNotEquals: ""
                    },
                    //date_estb: {
                    //required: false,
                    //number:true,
                    //minlength: 4,
                    //maxlength:4,
                    //lessThan:new Date().getFullYear()
                    //},
                    //org_site: {
                    //required: false,
                    //url: true
                    //},
                    // license_no: {
                    //     required: false,
                    //     valueNotEquals: "",
                    //     remote: {
                    //         headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
                    //         url: "/suppliers/uniquelicense",
                    //         type: "post",
                    //         async: false
                    //     }
                    // },
                    reg_no: {
                        required: true,
                        erpregexr: true,
                        valueNotEquals: "",
                        remote: {
                            headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
                            data:{'vehicle_id':$("#vehicle_id").val()},
                            url: "/suppliers/uniqueregistration",
                            type: "post",
                            async: false
                        }
                    },
                    //license_exp_date: {
                    //    required: true,
                    //    valueNotEquals: ""
                    //},
                    org_firstname: {
                        required: true,
                        lettersonly: true
                    },
                    org_lastname: {
                        required: true,
                        lettersonly: true
                    },
//                    org_email: {
//                        required: true,
//                        email: true
//                    },
                    org_email: {
                        required: false,
                        email: true,
                        remote: {
                            headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
                            url: "/suppliers/uniquemail",
                            type: "post",
                            async: false
                        }
                    },                    
                    org_mobile: {
                        required: true,
                        number: true,
                        minlength: 10,
                        maxlength: 11
                    },
                    org_landline: {
                        required: false,
                        number: true,
                        minlength: 6,
                        maxlength: 8
                    },
                    org_extnumber: {
                        required: false,
                        number: true,
                        maxlength: 4
                    },
                    org_address1: {
                        required: true
                    },
                    org_address2: {
                        required: false
                    },
                    org_country: {
                        required: true,
                        valueNotEquals: ""
                    },
                    org_state: {
                        required: true,
                        valueNotEquals: ""
                    },
                    org_city: {
                        required: true,
                        lettersonly: true
                    },
                    org_pincode: {
                        required: true,
                        number: true,
                        minlength: 6,
                        maxlength: 6
                    },
                    org_billingaddress_city: {
                        required: true,
                        lettersonly: true
                    },
                    org_billingaddress_pincode: {
                        required: true,
                        number: true,
                        maxlength: 6,
                        minlength: 6
                    },
                    org_bank_acname: {
                        required: false,
                        //alphanumeric: true
                    },
                    org_bank_name: {
                        required: false,
                        lettersonly: true
                    },
                    org_bank_acno: {
                        required: false,
                        number: true
                    },
                    org_bank_actype: {
                        required: false,
                    },
                    org_bank_ifsc: {
                        required: false,
                        alphanumeric: true
                    },
                    org_bank_branch: {
                        required: false,
                        lettersonly: true
                    },
                    org_micr_code: {
                        required: false,
                        number: true
                    },
                    org_billingaddress_address1: {
                        required: true
                    },
                    org_billingaddress_country: {
                        required: true,
                        valueNotEquals: ""
                    },
                    org_billingaddress_state: {
                        required: true,
                        valueNotEquals: ""
                    },
                    org_curr_code: {
                        required: false
                    },
                    org_rm: {
                        required: true,
                        valueNotEquals: ""
                    }
                },
                messages: {// custom messages for radio buttons and checkboxes
                    org_landline: {
                        minlength: "Please Enter Atleast 6 Digits.",
                        maxlength: "Please Enter Max 8 Digits."
                    },
					org_mobile: {
                        minlength: "Please Enter Atleast 10 Digits.",
                        maxlength: "Please Enter Max 10 Digits."
                    },
					org_extnumber: {
                        maxlength: "Please Enter Max 4 Digits."
                    },					
                    org_pincode: {
                        minlength: "Please Enter Valid Pincode.",
                        maxlength: "Please Enter Valid Pincode."
                    },
                    org_billingaddress_pincode: {
                        minlength: "Please Enter Valid Pincode.",
                        maxlength: "Please Enter Valid Pincode."
                    },
                    org_bank_acno: {
                        number: "Please Enter Valid Account Number."
                    },
                    //org_file: {
                    //extension:"Please Upload Valid Format."
                    //},
                    organization_name: {
                        remote: jQuery.validator.format("This name already exists")
                    },
                    license_no: {
                        remote: jQuery.validator.format("License number already exists")
                    },
                    org_email: {
                        remote: jQuery.validator.format("The email already exists")
                    },
                    reg_no: {
                        remote: jQuery.validator.format("Registration Number already exists or mapped for another vehicle.")
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
                    token = $("#csrf-token").val();

                    success.show();
                    error.hide();
                    $("#supp_info").prop('disabled',true);
                    var formData = $('#suppliersinfo').serialize();
                    var formData = new FormData();
                    formData.append('_token', token);
                    formData.append('supplier_rank', $("#supplier_rank").val());
                    formData.append('organization_name', $("#organization_name").val());
                    formData.append('supplier_type', $("#supplier_type").val());
                    formData.append('organization_type', $("#organization_type").val());
                    formData.append('reference_erp_code', $("#reference_erp_code").val());
                    formData.append('date_estb', $("#date_estb").val());
                    formData.append('org_site', $("#org_site").val());
                    formData.append('vehicle_id', $("#vehicle_id").val());
                    formData.append('vehicle_model', $("#vehicle_name option:selected").val());
                    if(typeof $("#vehicle_name option:selected").attr('value_type')!='undefined'){
                    formData.append('vehicle_name',$("#vehicle_name option:selected").attr('value_type'));
                    }
                    //formData.append('org_file',$('#org_file')[0].files[0]);
                    formData.append('org_firstname', $("#org_firstname").val());
                    formData.append('org_lastname', $("#org_lastname").val());
                    formData.append('org_email', $("#org_email").val());
                    formData.append('org_mobile', $("#org_mobile").val());
                    formData.append('org_landline', $("#org_landline").val());
                    formData.append('org_extnumber', $("#org_extnumber").val());
                    formData.append('org_address1', $("#org_address1").val());
                    formData.append('org_address2', $("#org_address2").val());
                    formData.append('org_country', $("#org_country").val());
                    formData.append('org_state', $("#org_state").val());
                    formData.append('org_city', $("#org_city").val());
                    formData.append('org_pincode', $("#org_pincode").val());
                    formData.append('org_billingaddress_chk', $("#org_billingaddress_chk").val());
                    formData.append('org_billingaddress_address1', $("#org_billingaddress_address1").val());
                    formData.append('org_billingaddress_address2', $("#org_billingaddress_address2").val());
                    formData.append('org_billingaddress_country', $("#org_billingaddress_country").val());
                    formData.append('org_billingaddress_state', $("#org_billingaddress_state").val());
                    formData.append('org_billingaddress_city', $("#org_billingaddress_city").val());
                    formData.append('org_billingaddress_pincode', $("#org_billingaddress_pincode").val());
                    formData.append('org_bank_acname', $("#org_bank_acname").val());
                    formData.append('org_bank_name', $("#org_bank_name").val());
                    formData.append('org_bank_acno', $("#org_bank_acno").val());
                    formData.append('org_bank_actype', $("#org_bank_actype").val());
                    formData.append('org_bank_ifsc', $("#org_bank_ifsc").val());
                    formData.append('org_bank_branch', $("#org_bank_branch").val());
                    formData.append('org_micr_code', $("#org_micr_code").val());
                    formData.append('org_curr_code', $("#org_curr_code").val());
                    formData.append('org_rm', $("#org_rm").val());
                    formData.append('license_no', $("#license_no").val());
                    if(typeof $("#reg_no").val()!='undefined'){
                        formData.append('reg_no', $("#reg_no").val());
                    }
                    formData.append('license_exp_date', $("#license_exp_date").val());
                    formData.append('driver_contact_old', $("#driver_contact_old").val());
                    
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/suppliers/create',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (rs) {     
                            console.log(rs);

                            //$("#addSupplierWarehouseGrid").igHierarchicalGrid({"dataSource":'/suppliers/getWarehouseList'});   
                            rs = JSON.parse(rs);
                            if(rs) {
                                
                                $("#reference_erp_code").val(rs.erp_code);    
                            }                            
                            if (rs.status == "true") {
                                $("#supplier_id").val(rs.supplier_id);                              
                                $('#flass_message').text('Supplier Details Saved Successfully');                                
                                $('div.alert').show();
                                $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                                $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                                $('html, body').animate({scrollTop: '0px'}, 800);                                                     
                                $('a[href="#tab_22"]').trigger('click');
                                  if(rs.erp_code !='' && rs.erp_code!=null ) {
                                    totGrid(rs.legalentity_id);                                
                                }
                            }else if(rs.status==400){
                                $('#flass_message').text('Same Vehicle Name and Registration Number already exists');                                
                                $('div.alert').show();
                                $('div.alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
                                $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                                $('html, body').animate({scrollTop: '0px'}, 800);                                                     
                            }else if(rs.status==500){
                                $('#flass_message').text('Same Registration Number already exists');                                
                                $('div.alert').show();
                                $('div.alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
                                $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                                $('html, body').animate({scrollTop: '0px'}, 800);                                                     
                            }else if(rs.status=="false"){
                                $('#flass_message').text(rs.message);
                                $('div.alert').show();
                                $('div.alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
                                $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                                $('html, body').animate({scrollTop: '0px'}, 800);
                            }
                            $("#supp_info").prop('disabled',false);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#flass_message').text('Email already exist');                                
                            $('div.alert').show();
                            $('div.alert').removeClass('hide').addClass('alert-danger');
                            $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                            $('html, body').animate({scrollTop: '0px'}, 800);
                            $("#supp_info").prop('disabled',false);
                        }
                    });

                    //document.getElementById("submit_form").submit();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }

            });

            $('#cancelsuppinfo').on('click', function (e) {
                e.preventDefault();
                window.location = "/suppliers";
            });
            $('#Supplier_aggr_cancel').on('click', function (e) {
                e.preventDefault();
                window.location = "/suppliers";
            });
            $('#Space_Provider_aggr_cancel').on('click', function (e) {
                e.preventDefault();
                window.location = "/spaceprovider";
            });
            $('#Vehicle_aggr_cancel').on('click', function (e) {
                e.preventDefault();
                window.location = "/vehicle";
            });
            $('#Vehicle_Provider_aggr_cancel').on('click', function (e) {
                e.preventDefault();
                window.location = "/vehicleproviders";
            });
            $('#Service_Provider_aggr_cancel').on('click', function (e) {
                e.preventDefault();
                window.location = "/serviceproviders";
            });
            $('#Human_Resource_Provider_aggr_cancel').on('click', function (e) {
                e.preventDefault();
                window.location = "/humanresource";
            });
            $('#Space_aggr_cancel').on('click', function (e) {
                e.preventDefault();
                window.location = "/space";
            });
            $('#Vehicle_add_cancel').on('click', function (e) {
                e.preventDefault();
                window.location = "/vehicle";
            });
            $('#Space_add_cancel').on('click', function (e) {
                e.preventDefault();
                window.location = "/space";
            });


            //create vehicle
            var form = $('#additionalinfo');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            jQuery.validator.addMethod("valueNotEquals", function (value, element, arg) {
                return arg != value;
            }, "This field is required.");

            $.validator.addMethod("lessThan", function (value, element) {
                var year = $("#date_estb").val(); //why not $(element) ?!?
                return (new Date()).getFullYear() >= parseInt(year, 10);
            }, "Invalid year");

            jQuery.validator.addMethod("email", function (value, element) {
                return this.optional(element) || /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/.test(value);
            }, "Email Invalid");

            $.validator.addMethod("alphanumeric", function (value, element) {
                return this.optional(element) || /^[a-z0-9]+$/i.test(value);
            }, "Invalid IFSC.");

            jQuery.validator.addMethod("lettersonly", function (value, element) {
                return this.optional(element) || /^[a-z\s]+$/i.test(value);
            }, "Only alphabetical characters");

            jQuery.validator.addMethod("url", function (value, element) {
                return this.optional(element) || /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi.test(value);
            });

            jQuery.validator.addMethod("erpregexp", function (value, element) {
                return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
            }, "Only alphanumerics characters");

            form.validate({
                onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account

                    // veh_provider: {
                    //     required: true,
                    //     valueNotEquals: ""
                    // },
                    body_type: {
                        required: true,
                        valueNotEquals: ""
                    },
                    vehicle_type: {
                        required: true,
                        valueNotEquals: ""
                    },
                    //reg_exp_date: {
                    //    required: true,
                    //    valueNotEquals: ""
                    //},
                    length: {
                        required: true,
                        valueNotEquals: "",
                        number: true
                    },
                    breadth: {
                        required: true,
                        valueNotEquals: "",
                        number: true
                    },
                    height: {
                        required: true,
                        valueNotEquals: "",
                        number: true
                    },
                    veh_lbh_uom: {
                        required: true,
                        valueNotEquals: ""
                    },
//                    license_no: {
//                        required: true,
//                        valueNotEquals: ""
//                    },
//                    license_exp_date: {
//                        required: true,
//                        valueNotEquals: ""
//                    },
                    veh_weight: {
                        required: true,
                        valueNotEquals: "",
                        number: true
                    },
                    veh_weight_uom: {
                        required: true,
                        valueNotEquals: ""
                    },
                    insurance_no: {
                        required: true,
                        valueNotEquals: "",
                        remote: {
                            headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
                            url: "/suppliers/uniqueinsurance",
                            type: "post",
                            async: false
                        }
                    },
                    //insurance_exp_date: {
                    //    required: true,
                    //    valueNotEquals: ""
                    //},
                    hublist1: {
                        required: true,
                        valueNotEquals: ""
                    },
                    //fit_exp_date: {
                    //    required: true,
                    //    valueNotEquals: ""
                    //},                    
                    //poll_exp_date: {
                    //    required: true,
                    //    valueNotEquals: ""
                    //},                    
                    //safty_exp_date: {
                    //    required: true,
                    //    valueNotEquals: ""
                    //},                    

                },
                messages: {// custom messages for radio buttons and checkboxes
                    org_landline: {
                        minlength: "Please Enter Atleast 6 Digits.",
                        maxlength: "Please Enter Max 8 Digits."
                    },
                    length: {
                        required: "Required",
                        number: "Enter number" 
                    },
                    breadth: {
                        required: "Required",
                        number: "Enter number" 
                    },
                    height: {
                        required: "Required",
                        number: "Enter number" 
                    },
                    veh_lbh_uom: {
                        required: "Required"
                    },
                    // reg_no: {
                    //     remote: jQuery.validator.format("Registration Number already exists.")
                    // },
                    insurance_no: {
                        remote: jQuery.validator.format("Insurance Number already exists.")
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
                    token = $("#csrf-token").val();

                    success.show();
                    error.hide();

                    var formData = $('#additionalinfo').serialize();
                    var formData = new FormData();
                    //formData.append('_token', token);
                    //formData.append('veh_provider', $("#veh_provider").val());
                    formData.append('body_type', $("#body_type").val());
                    formData.append('vehicle_type', $("#vehicle_type").val());
                    formData.append('reg_no', $("#reg_no").val());
                    formData.append('reg_exp_date', $("#reg_exp_date").val());
                    formData.append('length', $("#length").val());
                    formData.append('breadth', $("#breadth").val());
                    formData.append('height', $("#height").val());
                    formData.append('veh_lbh_uom', $("#veh_lbh_uom").val());
//                    formData.append('license_no', $("#license_no").val());
//                    formData.append('license_exp_date', $("#license_exp_date").val());
                    formData.append('veh_weight', $("#veh_weight").val());
                    formData.append('veh_weight_uom', $("#veh_weight_uom").val());
                    formData.append('insurance_no', $("#insurance_no").val());
                    formData.append('insurance_exp_date', $("#insurance_exp_date").val());
                    formData.append('hub_id', $("#hublist1").val());
                    formData.append('fit_exp_date', $("#fit_exp_date").val());
                    formData.append('poll_exp_date', $("#poll_exp_date").val());
                    formData.append('safty_exp_date', $("#safty_exp_date").val());
                    
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/vehicle/additional',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (rs) {
                               $('#flass_message').text('Additional Details Saved Successfully');                                
                                $('div.alert').show();
                                $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                                $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                                $('html, body').animate({scrollTop: '0px'}, 800);  
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#flass_message').text(errorThrown);
                            $('div.alert').show();
                            $('div.alert').removeClass('hide').addClass('alert-danger');
                            $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                            $('html, body').animate({scrollTop: '0px'}, 800);
                        }
                    });
                }

            });

            $('#canceladditional').on('click', function (e) {
                e.preventDefault();
                window.location = "/vehicle";
            });

            //create vehicle
            var form = $('#spaceadditionalinfo');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            jQuery.validator.addMethod("valueNotEquals", function (value, element, arg) {
                return arg != value;
            }, "This field is required.");

            $.validator.addMethod("lessThan", function (value, element) {
                var year = $("#date_estb").val(); //why not $(element) ?!?
                return (new Date()).getFullYear() >= parseInt(year, 10);
            }, "Invalid year");

            jQuery.validator.addMethod("email", function (value, element) {
                return this.optional(element) || /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/.test(value);
            }, "Email Invalid");

            $.validator.addMethod("alphanumeric", function (value, element) {
                return this.optional(element) || /^[a-z0-9]+$/i.test(value);
            }, "Invalid IFSC.");

            jQuery.validator.addMethod("lettersonly", function (value, element) {
                return this.optional(element) || /^[a-z\s]+$/i.test(value);
            }, "Only alphabetical characters");

            jQuery.validator.addMethod("url", function (value, element) {
                return this.optional(element) || /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi.test(value);
            });

            jQuery.validator.addMethod("erpregexp", function (value, element) {
                return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
            }, "Only alphanumerics characters");

            form.validate({
                onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    hublist1: {
                        required: true,
                        valueNotEquals: ""
                    },
                    area: {
                        required: true,
                        valueNotEquals: ""
                    },
                    space_provider: {
                        required: true,
                        valueNotEquals: ""
                    },
                },
                messages: {// custom messages for radio buttons and checkboxes
                    veh_lbh_uom: {
                        required: "Required"
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
                    token = $("#csrf-token").val();

                    success.show();
                    error.hide();

                    var formData = $('#spaceadditionalinfo').serialize();
                    var formData = new FormData();
                    //formData.append('_token', token);
                    formData.append('hub_id', $("#hublist1").val());
                    formData.append('area', $("#area").val());
                    formData.append('space_provider', $("#space_provider").val());
                  
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/space/additional',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (rs) {
                            $('#flass_message').text('Additional Details Saved Successfully');
                            $('div.alert').show();
                            $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                            $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                            $('html, body').animate({scrollTop: '0px'}, 800);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#flass_message').text(errorThrown);
                            $('div.alert').show();
                            $('div.alert').removeClass('hide').addClass('alert-danger');
                            $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                            $('html, body').animate({scrollTop: '0px'}, 800);
                        }
                    });
                }

            });

            $('#canceladditional').on('click', function (e) {
                e.preventDefault();
                window.location = "/space";
            });

            //create suppliers documents
            var form = $('#supplierdocs');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            jQuery.validator.addMethod("erpregexp", function (value, element) {
                return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
            }, "Only alphanumerics characters");

            jQuery.validator.addMethod("erpregexpan", function (value, element) {
                return this.optional(element) || /^[A-Z]{5}\d{4}[A-Z]{1}$/.test(value);
            }, "Invalid PAN Number");

            jQuery.validator.addMethod("erpregexpcin", function (value, element) {
                return this.optional(element) || /^[L,U]{1}\d{5}[A-Z]{2}\d{4}[PLC,PTC,FTC]{3}\d{6}$/.test(value);
            }, "Invalid CIN Number");

            jQuery.validator.addMethod("erpregexptinvat", function (value, element) {
                return this.optional(element) || /^[0-9]{11}$/.test(value);
            }, "Invalid TIN/VAT Number");

            jQuery.validator.addMethod("erpregexpcst", function (value, element) {
                return this.optional(element) || /^[0-9]{11}$/.test(value);
            }, "Invalid CST Number");

            form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    pan_number: {
                        required: false,
                        erpregexpan: true
                    },
                    pan_files: {
                        required: false,
                        extension: "png|jpeg|jpg|pdf|doc|docx"
                    },
                    cin_number: {
                        required: false,
                        erpregexpcin: true
                    },
                    cin_files: {
                        required: false,
                        extension: "png|jpeg|jpg|pdf|doc|docx"
                    },
                    tinvat_number: {
                        required: false,
                        erpregexptinvat: true
                    },
                    tinvat_files: {
                        required: false,
                        extension: "png|jpeg|jpg|pdf|doc|docx"
                    },
                    cst_number: {
                        required: false,
                        erpregexpcst: true
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
                messages: {// custom messages for radio buttons and checkboxes
                    pan_files: {
                        extension: "Please Upload Valid Format."
                    },
                    cin_files: {
                        extension: "Please Upload Valid Format."
                    },
                    tinvat_files: {
                        extension: "Please Upload Valid Format."
                    },
                    cst_files: {
                        extension: "Please Upload Valid Format."
                    },
                    cheque_files: {
                        extension: "Please Upload Valid Format."
                    },
                    mou_files: {
                        extension: "Please Upload Valid Format."
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
                    token = $("#csrf-token").val();

                    success.show();
                    error.hide();

                    var formData = $('#supplierdocs').serialize();
                    var formData = new FormData();
                    formData.append('_token', token);

                    formData.append('pan_number', $("#pan_number").val());
                    formData.append('cin_number', $("#cin_number").val());
                    formData.append('tinvat_number', $("#tinvat_number").val());
                    formData.append('cst_number', $("#cst_number").val());
                    formData.append('pan_files', $('#pan_files')[0].files[0]);
                    formData.append('cin_files', $('#cin_files')[0].files[0]);
                    formData.append('tinvat_files', $('#tinvat_files')[0].files[0]);
                    formData.append('cst_files', $('#cst_files')[0].files[0]);
                    formData.append('cheque_files', $('#cheque_files')[0].files[0]);
                    formData.append('mou_files', $('#mou_files')[0].files[0]);
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/suppliers/supplierdocs',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (rs) {


                            /*var googleformData = new FormData();
                             googleformData.append('_token', token);
                             googleformData.append('upload_file',$('#pan_files')[0].files[0]);
                             googleformData.append('filename','PAN');
                             $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);
                             $.ajax({
                             headers: {'X-CSRF-TOKEN': token},
                             method: "POST",
                             url: '/driveupload/image',
                             processData: false,
                             contentType: false,                                             
                             data: googleformData,
                             success: function (rs) {
                             $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
                             //alert('document uploaded to google.');
                             },
                             error: function(xhr, textStatus, errorThrown){
                             $.ajax({
                             headers: {'X-CSRF-TOKEN': token},
                             method: "POST",
                             url: '/driveupload/authurl',
                             processData: false,
                             contentType: false,                                             
                             data: formData,
                             success: function (rs) {
                             window.location=rs;
                             }});
                             }
                             });       */


                            if (rs == "true")
                            {
                                $("#success").css("display", "block");
                                $("#success").html('<li style="display:inline-block;text-align:center;color: #428bca;" class="success">Supplier Details Saved Successfully.</li>');
                                setTimeout(function () {
                                    $("#success").css("display", "none");
                                }, 10000);
                                $('a[href="#tab_22_1"]').trigger('click');
                            }
                            if (rs == "No Supplier")
                            {
                                $("#success").css("display", "block");
                                $("#success").html('<li style="display:inline-block;text-align:center;color: #428bca;" class="success">Please Create Supplier.</li>');
                                setTimeout(function () {
                                    $("#success").css("display", "none");
                                }, 100000);
                                $('a[href="#tab_11"]').trigger('click');
                            }
                        }
                    });

                    //document.getElementById("submit_form").submit();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }

            });

            $('#canceldocs').on('click', function (e) {
                e.preventDefault();
                window.location = "/suppliers";
            });

            //create suppliers required docs
            var form = $('#createdocs');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            jQuery.validator.addMethod("valueNotEquals", function (value, element, arg) {
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
                    token = $("#csrf-token").val();

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
                    formData.append('tinvat_file', $("#tinvat_file:checked").val());
                    formData.append('cst_input', $("#cst_input:checked").val());
                    formData.append('cst_file', $("#cst_file:checked").val());
                    formData.append('cheque_file', $("#cheque_file:checked").val());
                    formData.append('mou_fyl', $("#mou_fyl:checked").val());
                    formData.append('org_country', $("#org_country").val());
                    formData.append('organization_type', $("#organization_type").val());

                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/suppliers/requireddocscreate',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (rs) {
                            if (rs == "true")
                            {
                                alert('Supplier Required Documents Details Saved Successfully.');
                                window.location.reload();
                                //$("#success").css("display","block");
                                //$("#success").html('<li style="display:inline-block;text-align:center;color: #428bca;" class="success">Supplier Required Documents Details Saved Successfully.</li>');
                                //setTimeout(function() { $("#success").css("display","none"); }, 10000); 
                            }
                        }
                    });

                }

            });

            $('#cancelreqdocs').on('click', function (e) {
                e.preventDefault();
                window.location = "/";
            });


            //create suppliers terms
            var form = $('#agr_terms');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            jQuery.validator.addMethod("valueNotEquals", function (value, element, arg) {
                return arg != value;
            }, "This field is required.");

            $.validator.addMethod("lessThan", function (value, element) {
                var year = $("#date_estb").val(); //why not $(element) ?!?
                return (new Date()).getFullYear() >= parseInt(year, 10);
            }, "Invalid year");

            jQuery.validator.addMethod("email", function (value, element) {
                return this.optional(element) || /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/.test(value);
            }, "Email Invalid");

            $.validator.addMethod("alphanumeric", function (value, element) {
                return this.optional(element) || /^[a-z0-9]+$/i.test(value);
            }, "Invalid IFSC.");

            jQuery.validator.addMethod("lettersonly", function (value, element) {
                return this.optional(element) || /^[a-z\s]+$/i.test(value);
            }, "Only alphabetical characters");

            jQuery.validator.addMethod("url", function (value, element) {
                return this.optional(element) || /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi.test(value);
            });

            jQuery.validator.addMethod("erpregexp", function (value, element) {
                return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
            }, "Only alphanumerics characters");

            jQuery.validator.addMethod("greaterThan", function (value, element, params) {
                if (!/Invalid|NaN/.test(new Date(value))) {
                    return new Date(value) > new Date($(params).val());
                }
                return isNaN(value) && isNaN($(params).val()) || (Number(value) > Number($(params).val()));
            }, 'Must be greater than {0}.');


            form.validate({
                onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    vendorreg_charges: {
                        required: false,
                        number: true
                    },
                    skureg_charges: {
                        required: false,
                        number: true
                    },
                    dclinking_charges: {
                        required: false,
                        number: true
                    },
                    btbchannel_supportassistance: {
                        required: false,
                        number: true
                    },
                    ecp_visibilityassistance: {
                        required: false,
                        number: true
                    },
                    po_days: {
                        required: false
                    },
                    delivery_tat: {
                        required: false
                    },
                    delivery_tatuom: {
                        required: false
                    },
                    invoice_days: {
                        required: false
                    },
                    delivery_frequency: {
                        required: false
                    },
                    credit_period: {
                        required: true,
                        number: true
                    },
                    payment_days: {
                        required: false
                    },
                    negotiation: {
                        required: false
                    },
                    rtv: {
                        required: false
                    },
                    rtv_timeline: {
                        required: false,
                        lettersonly: true
                    },
                    rtv_scope: {
                        required: false
                    },
                    rtv_location: {
                        required: false
                    },
                    start_date: {
                        required: false
                    },
                    end_date: {
                        required: false
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
                submitHandler: function (form,event) {
                    event.preventDefault();
                    $("#supp_agrtrms_info").prop('disabled',true);
                    token = $("#csrf-token").val();

                    success.show();
                    error.hide();

                    var formData = $('#suppliersinfo').serialize();
                    var formData = new FormData();
                    formData.append('_token', token);
                    formData.append('vendorreg_charges', $("#vendorreg_charges").val());
                    formData.append('skureg_charges', $("#skureg_charges").val());
                    formData.append('dclinking_charges', $("#dclinking_charges").val());
                    formData.append('btbchannel_supportassistance', $("#btbchannel_supportassistance").val());
                    formData.append('ecp_visibilityassistance', $("#ecp_visibilityassistance").val());
                    formData.append('po_days', $("#po_days").val());
                    formData.append('delivery_tat', $("#delivery_tat").val());
                    formData.append('delivery_tatuom', $("#delivery_tatuom").val());
                    formData.append('invoice_days', $("#invoice_days").val());
                    formData.append('delivery_frequency', $("#delivery_frequency").val());
                    formData.append('credit_period', $("#credit_period").val());
                    formData.append('payment_days', $("#payment_days").val());
                    formData.append('negotiation', $("#negotiation").val());
                    formData.append('rtv', $("#rtv").val());
                    formData.append('rtv_timeline', $("#rtv_timeline").val());
                    formData.append('rtv_scope', $("#rtv_scope").val());
                    formData.append('rtv_location', $("#rtv_location").val());
                    formData.append('start_date', $("#start_date").val());
                    formData.append('end_date', $("#end_date").val());
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/suppliers/agrterms',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (rs) {
                            if (rs == "true")
                            {
                               $('#flass_message').text('Aggrement Details Saved Successfully');                                
                                $('div.alert').show();
                                $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                                $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                                $('html, body').animate({scrollTop: '0px'}, 800);
                                
                                $('a[href="#tab_33"]').trigger('click');
                            }
                            if (rs == "No Supplier")
                            {
                               $('#flass_message').text('Supplier Details Saved Successfully');                                
                                $('div.alert').show();
                                $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                                $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                                $('html, body').animate({scrollTop: '0px'}, 800);
                                
                                $('a[href="#tab_11"]').trigger('click');
                            }
                            $("#supp_agrtrms_info").prop('disabled',false);
                        }
                    });

                    //document.getElementById("submit_form").submit();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }

            });

        }

    };

}();
