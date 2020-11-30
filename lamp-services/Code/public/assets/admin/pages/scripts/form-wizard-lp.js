var FormWizard = function () {


    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

            function format(state) {
                if (!state.id) return state.text; // optgroup
                return "<img class='flag' src='../../assets/global/img/flags/" + state.id.toLowerCase() + ".png'/>&nbsp;&nbsp;" + state.text;
            }

           /* $("#country_list").select2({
                placeholder: "Select",
                allowClear: true,
                formatResult: format,
                formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });*/

            var form = $('#submit_form');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);
            
            jQuery.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^[a-zA-Z ]+$/i.test(value);
            }, "Letters only please"); 
            
            jQuery.validator.addMethod('filesize', function(value, element, param) {
            return this.optional(element) || (element.files[0].size <= param) 
            });

            jQuery.validator.addMethod("email", function(value, element) {
            return this.optional(element) ||  /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/.test(value);
            }, "Email Invalid"); 
            
            jQuery.validator.addMethod("url", function(value, element) {
            return this.optional(element) || /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi.test(value);
            });

            form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    lp_name: {
                        required: true
                    },
                    description: {
                        required: true
                    },
                    address_1: {
                        required: true,
                    },
                    //profile
                    city: {
                        required: true,
                        lettersonly: true
                    },
                    files: {
                         required: false, 
                         extension: "png|jpeg|jpg",
                         filesize: 1048576,   
                    },
                    state: {
                        required: true,
                    },
                    country: {
                        required: true
                    },
                    pincode: {
                        required: true,
			number:true,
                        minlength: 6
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    phone: {
                        required: true,
			number:true,
                        minlength: 10,
                        maxlength:11
                    },
                    website: {
                        required: true,
			url:true
                    },
                    api_username: {
                        required: true
                    },
                    //payment
                    api_password: {
                        required: true
                    },
                    api_apikey: {
                        required: true
                    },
                    channelreferancename: {
                        required: true
                    },
                    marketplaceusername: {
                        required: true
                    },
                    password: {
                        required: true
                    },
                    wharehouseId: {
                        required: true
                    },
                    sellername: {
                        required: true
                    }
                },

                messages: { // custom messages for radio buttons and checkboxes
                    'payment[]': {
                        required: "Please select at least one option",
                        minlength: jQuery.validator.format("Please select at least one option")
                    },
                    phone: {
                        minlength:"Please Enter Atleast 10 Digits.",
                        maxlength:"Please Enter Atleast 10 Digits."
                    },
                    files: {
                        extension:"Please Upload Valid Format."
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
                    if (label.attr("for") == "gender" || label.attr("for") == "payment[]") { // for checkboxes and radio buttons, no need to show OK icon
                        label
                            .closest('.form-group').removeClass('has-error').addClass('has-success');
                        label.remove(); // remove error label here
                    } else { // display success icon for other inputs
                        label
                            .addClass('valid') // mark the current input as valid and display OK icon
                        .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                    }
                },

                submitHandler: function (form) {
                token  = $("#csrf-token").val();

                    success.show();
                    error.hide();
                     /* var lp_id = $("#lpId").val();
                        alert(lp_id); return false;*/
                     
                      var filename = $('#files').val();
                     var formData = new FormData();

                        var files = $("#files")[0].files[0];
                        var lp_name = $("#lp_name").val();
                        var lp_id = $("#lpId").val();
                        var description = $("#description").val();
                        var address_1 = $("#address_1").val();
                        var address_2 = $("#address_2").val();
                        var pincode = $("#logistic_picode").val();
                        var country = $("#logistic_country").val();
                        var state = $("#logistic_state").val();
                        var city = $("#logistic_city").val();
                        var phone = $("#phone").val();
                        var email = $("#email").val();
                        var website = $("#website").val();
                        var fullfulment_service=($('#inlineCheckbox1').prop("checked") == true)?'true':'false';
                        var forwording_service=($('#inlineCheckbox2').prop("checked") == true)?'true':'false';
                        var cod_service=($('#inlineCheckbox3').prop("checked") == true)?'true':'false';
                        //alert(fullfulment_service+'____'+forwording_service+'_____'+cod_service);
                        var formData = new FormData();
                         formData.append('_token', token);
                        formData.append('lp_name', lp_name);
                        formData.append('lp_id', lp_id);
                        formData.append('description', description);
                        formData.append('address_1', address_1);
                        formData.append('address_2', address_2);
                        formData.append('pincode', pincode);
                        formData.append('country', country);
                        formData.append('state', state);
                        formData.append('city', city);
                        formData.append('phone', phone);
                        formData.append('email', email);
                        formData.append('website', website);
                        formData.append('files', files);
                        formData.append('full_service', fullfulment_service);
                        formData.append('for_service', forwording_service);
                        formData.append('cod_service', cod_service);
                        
                        console.log(formData);

                       $.ajax({
                            headers: {'X-CSRF-TOKEN': token},
                            method: "POST",
                            url: '/logisticpartners/save',
                            data: formData,
                            processData: false,
                            contentType: false,                                             
                            success: function (rs) { 
                              
                                if(lp_id=='')
                                {
				  $("#wh_lp_id").val(rs);									
                                }
                                
                                var lpvalue = $("#lpId").val(rs);								
				//$("#wh_lp_id").val(1); 
                                    $("#success").css("display","block");
                                    $("#success").html('<li style="display:inline-block;text-align:center;color: #428bca;" class="success">Logistic Partner Details Saved Successfully.</li>');
                                    setTimeout(function() { $("#success").css("display","none"); }, 10000);
                                    $("#lpId").val(rs); 
                                    
                                $("#warehouses_id").trigger('click');
                                return false;
                            }
                        });
                    
					//document.getElementById("submit_form").submit();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }

            });
			
			
           var form_wh = $('#submit_form_wh');
            var error_wh = $('.alert-danger', form_wh);
            var success_wh = $('.alert-success', form_wh);
			
			jQuery.validator.addMethod("email", function(value, element) {
            return this.optional(element) ||  /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/.test(value);
            }, "Email Invalid");
            
            jQuery.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^[a-z\s]+$/i.test(value);
            }, "Only alphabetical characters"); 

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
                        url: "/logisticpartners/warehuniq/"+$('#lpId').val(),
                        type: "post",  
                        async: false,
                    }
                    },
                    messages:
             {
                 wh_name:
                 {
                    required: "Please enter your Warehouse Name.",
                    wh_name: "Please enter a valid Warehouse Name.",
                    remote: jQuery.validator.format("{0} is already taken.")
                 }},
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
                        number  : true,
                        minlength: 10,
                        maxlength:11
                    },
                    wh_address1: {
                        required: true
                    },    
                    wh_address2: {
                        required: false
                    },					
                    wh_pincode: {
                        required: true,
                        number  : true,
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
         
         if(form_wh.valid())
         {
              token  = $("#csrf-token").val();
                     var data = decodeURIComponent($('form#submit_form_wh').serialize());
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                    url:'/logisticpartners/savewh/',
                    data: data,
                    processData: false,
                    contentType: false, 
                    success:function(response){
                    
                        if(response === '1')
                        {
                            $("#success").css("display","block");
                            $("#success").html('<li style="display:inline-block;text-align:center;color: #428bca;" id="success">Warehouse Details Saved Successfully.</li>');
                            setTimeout(function() { $("#success").css("display","none"); }, 10000);
                        }
                        if(response == 'Warehouse Name Already Exists.')
                        {
                            $("#success").css("display","block");
                            $("#success").html('<li style="display:inline-block;text-align:center;color: #428bca;" id="success">Warehouse Name Already Exists.</li>');
                            setTimeout(function() { $("#success").css("display","none"); }, 10000);
                        }
                       
                              if(response === '1')
           {
							
							if($.trim($("#logisticPrtnersList").html())!='')
							{
								$("#logisticPrtnersList").igHierarchicalGrid({'dataSource':'logisticpartners/getLpList'});
							}							
							if($.trim($("#addWarehouseGrid").html())=='')
							{
								addWarehouseGrid();	
							}
							else
							{
								$("#addWarehouseGrid").igHierarchicalGrid({"dataSource":'/logisticpartners/getWarehouseList/'+$('#lpId').val()});								
							}
							alert('Warehouse Details Saved Successfully.')
							$(".close").trigger('click'); 
							$('form[id="submit_form_wh"]')[0].reset();           }
           else
           {
           alert("Please select a Logistic partner or create new Logistic partner.");   
           $(".close").trigger('click');
           }
                    },
                  });



         }
         
   
 });			

            if(!$('.files_edit').attr('href'))
            {
                $('input[name=files]').rules('add','required');
            }

            var displayConfirm = function() {
                $('#tab4 .form-control-static', form).each(function(){
                    var input = $('[name="'+$(this).attr("data-display")+'"]', form);
                    if (input.is(":radio")) {
                        input = $('[name="'+$(this).attr("data-display")+'"]:checked', form);
                    }
                    if (input.is(":text") || input.is("textarea")) {
                        $(this).html(input.val());
                    } else if (input.is("select")) {
                        $(this).html(input.find('option:selected').text());
                    } else if (input.is(":radio") && input.is(":checked")) {
                        $(this).html(input.attr("data-title"));
                    } else if ($(this).attr("data-display") == 'payment[]') {
                        var payment = [];
                        $('[name="payment[]"]:checked', form).each(function(){ 
                            payment.push($(this).attr('data-title'));
                        });
                        $(this).html(payment.join("<br>"));
                    }
                });
            }

            var handleTitle = function(tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                // set wizard title
                $('.step-title', $('#form_wizard_1')).text('Step ' + (index + 1) + ' of ' + total);
                // set done steps
                jQuery('li', $('#form_wizard_1')).removeClass("done");
                var li_list = navigation.find('li');
                for (var i = 0; i < index; i++) {
                    jQuery(li_list[i]).addClass("done");
                }

                if (current == 1) {
                    $('#form_wizard_1').find('.button-previous').hide();
                } else {
                    $('#form_wizard_1').find('.button-previous').show();
                }

                if (current >= total) {
                    $('#form_wizard_1').find('.button-next').hide();
                    $('#form_wizard_1').find('.button-submit').show();
                    displayConfirm();
                } else {
                    $('#form_wizard_1').find('.button-next').show();
                    $('#form_wizard_1').find('.button-submit').hide();
                }
                Metronic.scrollTo($('.page-title'));
            }

            // default form wizard
            $('#form_wizard_1').bootstrapWizard({
                'nextSelector': '.button-next',
                'previousSelector': '.button-previous',
                onTabClick: function (tab, navigation, index, clickedIndex) {
                    return false;
                    /*
                    success.hide();
                    error.hide();
                    if (form.valid() == false) {
                        return false;
                    }
                    handleTitle(tab, navigation, clickedIndex);
                    */
                },
                onNext: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    if (form.valid() == false) {
                        return false;
                    }

                    handleTitle(tab, navigation, index);
                },
                onPrevious: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    handleTitle(tab, navigation, index);
                },
                onTabShow: function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    var $percent = (current / total) * 100;
                    $('#form_wizard_1').find('.progress-bar').css({
                        width: $percent + '%'
                    });
                }
            });

            $('#form_wizard_1').find('.button-previous').hide();
            $('#form_wizard_1 .button-submit').click(function () {
                //alert('Finished! Hope you like it :)');
                //var cc = document.getElementById("submit_form")
                //alert()
                document.getElementById("submit_form").submit();
                //form.submit();
            }).hide();

            //apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
            $('#country_list', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
        }

    };

}();