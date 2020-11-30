var FormWizard = function () {
    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }
            //create suppliers
            var form = $('#retailersinfo');
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
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    org_file: {
                        required: false,
                        extension: "png|jpeg|jpg"
                    },
                    organization_name: {
                        required: true
                    },
                    organization_type: {
                        required: true,
                        valueNotEquals: ""
                    },
                    reference_erp_code: {
                        required: false,
                        erpregexp: true

                    },
                    date_estb: {
                        required: true,
                        number: true,
                        minlength: 4,
                        maxlength: 4,
                        lessThan: new Date().getFullYear()
                    },
                    org_site: {
                        required: true,
                        url: true
                    },
                    org_firstname: {
                        required: true,
                        lettersonly: true
                    },
                    org_lastname: {
                        required: true,
                        lettersonly: true
                    },
                    org_email: {
                        required: true,
                        email: true
                    },
                    org_mobile: {
                        required: true,
                        number: true,
                        minlength: 11,
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
                        minlength: 2,
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
                    business_type_id: {
                        required: true,
                        valueNotEquals: ""
                    },
                    approve: {
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
                        lettersonly: true
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
                    org_mobile: {
                        minlength: "Please Enter Atleast 10 Digits.",
                        maxlength: "Please Enter Atleast 10 Digits."
                    },
                    org_file: {
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

                    var formData = $('#suppliersinfo').serialize();
                    var formData = new FormData();
                    formData.append('_token', token);
                    formData.append('business_type_id', $("#segment_type").val());
                    formData.append('approve', $("#approve").val());
                    formData.append('retailer_name', $("#retailer_name").val());
                    formData.append('legal_entity_id', $("#legal_entity_id").val());
                    formData.append('org_site', $("#org_site").val());
                    formData.append('org_file', $('#org_file')[0].files[0]);
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
                    formData.append('org_rm', $("#org_rm").val());

                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/retailers/update',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response != "") {
                                //alert('Updated successfully');
                                $('#flass_message').text('Retailer updated successfully');
                                $('div.alert').show();
                                $('div.alert').removeClass('hide');
                                $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                                $('html, body').animate({scrollTop: '0px'}, 500);
                                window.setTimeout(function () {
                                    window.location.href = '/retailers/index';
                                }, 3000);                                
                            }
                        }
                    });

                }

            });

            $('#cancelretailerinfo').on('click', function (e) {
                e.preventDefault();
                window.location = "/retailers/index";
            });

        }

    };
        
}();
