var FormWizard = function () {


    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

            // Brand save scripts

            var form_brand = $('#add_brand_form');
            var error = $('.alert-danger', form_brand);
            var success = $('.alert-success', form_brand);


            brand_validation_var = form_brand.validate({
                onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    brand_name: {
                        required: true,
                        remote: {
                            headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
                            data: {'brand_name': function () {
                                    return $('#brand_name').val();
                                }},
                            url: "/brands/brandfuniq",
                            type: "post",
                            async: false
                        }
                    },
                    brand_desc: {
                        required: true,
                    },
                    manufacturer_name: {
                        required: true,
                    },
                    brand_logo: {
                        required: true,
                        extension: "png|jpeg|jpg"
                    },
                    brand_id: {
                        required: false,
                    }
                },
                messages: {// custom messages for radio buttons and checkboxes
                    'brand_logo': {
                        extension: jQuery.validator.format("Please choose images only")
                    },
                    brand_name: {
                        remote: jQuery.validator.format("The brand name already exists")
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
                    //console.log(form);    

                    //var value = $("input[type=submit][clicked=true]").val(); 
                    //alert(value);	
                    success.show();
                    error.hide();
                    var formData = new FormData();

                    var token = $("#csrf-token").val();
                    if ($('#brandLogo').val())
                        formData.append('logo', $('#brandLogo')[0].files[0]);


                    formData.append('_token', token);
                    formData.append('legal_entity_id', $("#manufacturer_name").val());
                    formData.append('brand_id', $('#edit_brand_ids').val());
                    formData.append('brand_name', $('#brand_name').val());
                    formData.append('brand_desc', $('#brand_desc').val());
                    formData.append('brand_id', $('#brand_id').val());
                    //console.log(formData);
//			formData+='&_token=' + token;
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/brands/save',
                        processData: false,
                        contentType: false,
                        data: formData,
                        beforeSend: function () {
                           $('#loader1').show();
                        },
                        complete: function () {
                            $('#loader1').hide();
                        },
                        success: function (rs) {
                            if ($('#edit_brand_ids').val()) {                               
                                $('#flass_message').text(rs);
                                $('div.alert').show();
                                $('div.alert').removeClass('hide');
                                $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                                $('html, body').animate({scrollTop: '0px'}, 500);                                       
                            } else {                                
                                $('#flass_message').text(rs);
                                $('div.alert').show();
                                $('div.alert').removeClass('hide');
                                $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                                $('html, body').animate({scrollTop: '0px'}, 500);                                    
                            }
                             window.setTimeout(function () {
                                window.location.href = '/brands';
                               }, 1000);
                            //window.location = "/brands";
                            // $('a[href="#tab_44"]').trigger('click');							 
                        }
                    });

                }

            });


            //save manufacturer

            var form_brand = $('#add_manu_form');
            var error = $('.alert-danger', form_brand);
            var success = $('.alert-success', form_brand);


            brand_validation_var = form_brand.validate({
                onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    manu_org_name: {
                        required: true,
                        remote: {
                            headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
                            data: {'manu_id': function () {
                                    return $('#manu_id').val();
                                }},
                            url: "/brands/manfuniq",
                            type: "post",
                            async: false
                        }
                    },
                    manu_logo: {
                        required: true,
                        extension: "png|jpeg|jpg"
                    },
                    manu_org_type: {
                        required: true
                    },
                    manu_segment: {
                        required: true
                    },
                    pur_mgr: {
                        required: true
                    },
                    manu_id: {
                        required: false
                    }
                },
                messages: {// custom messages for radio buttons and checkboxes
                    'brand_logo': {
                        extension: jQuery.validator.format("Please choose images only")
                    },
                    manu_logo: {
                        extension: jQuery.validator.format("Please choose images only")
                    },
                    manu_org_name: {
                        remote: jQuery.validator.format("The manufacturer name already exists")
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
                    //console.log(form);    
                    $('#addmanuform').css('display', 'none');
                    $('#ajaxloader').css('display', 'block');
                    //var value = $("input[type=submit][clicked=true]").val(); 
                    var img_value = $('#manu_logo_name').attr('src');
                    //var img_name = img_value.replace('/uploads/manufacturer_logos/','');
                    success.show();
                    error.hide();
                    var formData = new FormData();
                    var message = "Manufacturer Details Sucessfully!";
                    var token = $("#csrf-token").val();
                    if ($('#manuLogo').val())
                        formData.append('logo', $('#manuLogo')[0].files[0]);

                    if ($("#manu_id").val())
                    {
                        formData.append('existing_logo', img_value);
                        message = "Manufacturer Details Saved Successfully!";
                    } else
                    {
                        message = "Manufacturer Details Saved Successfully!";
                    }
                    formData.append('manu_id', $("#manu_id").val());
                    formData.append('manu_org_type', $("#manu_org_type").val());
                    formData.append('_token', token);
                    formData.append('legal_entity_id', $("#manu_org_name").val());
                    formData.append('manu_segment', $("#manu_segment").val());
                    formData.append('manu_id', $("#manu_id").val());
                    formData.append('pur_mgr', $("#pur_mgr").val());
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/manu/save',
                        processData: false,
                        contentType: false,
                        data: formData,
                        beforeSend: function () {
                           $('#loader').show();
                        },
                        complete: function () {
                            $('#loader').hide();
                        },
                        success: function (rs) {
                            //alert(JSON.stringify(rs));                            
                            $('#ajaxloader').css('display', 'none');
                            $('#addmanuform').css('display', 'none');
                            $('.modal-backdrop').css('display', 'none');
                                
                                $('#flass_message').text(rs);
                                $('div.alert').show();
                                $('div.alert').removeClass('hide');
                                $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                                $('html, body').animate({scrollTop: '0px'}, 500); 
                                $("#add_manu").modal('toggle');
                                
                                $.ajax({
                                    headers: {'X-CSRF-TOKEN': token},
                                    url: '/getManufacturersList',
                                    type: 'POST',                                            
                                    success: function (rs) {                                           
                                       var manid = $("#manu_id").val();                                      
                                        $("#manufacturer_name").html(rs);
                                        $("#manufacturer_name").select2('val',manid);
                                    }
                                 });                                                    }
                            });
                         }
            });

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
                    formData.append('_token', token);
                    formData.append('approval_select_id', $("#approval_select_id").val());
                    formData.append('approval_product_id', $("#approval_product_id").val());

                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        method: "POST",
                        url: '/approvalsave',
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function (rs) {
                            $('#approval_save').css('display', 'none');
                            $('#approva_comments').css('display', 'none');
                            $('#approval_row_id').css('display', 'none');
                            alert('Approval submitted Successfully.');
                            window.location = "/products";
                        }
                    });
                }

            });
            $('#cancelmaninfo').on('click', function (e) {
                e.preventDefault();
                window.location = "/brands";
            });


        }

    };

}();