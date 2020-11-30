jQuery.validator.setDefaults({
  debug: true,
  success: "valid"
});
$("#emp_bank_info").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
//        valid: 'glyphicon glyphicon-ok',
//        invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            acc_name: {
                validators: {
                    notEmpty: {
                        message: "This field is required."
                    },
                    regexp: {
                        regexp:  /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    }
                }
            },
            bank_name: {
                validators: {
                    notEmpty: {
                        message: "This field is required."
                    },
                    regexp: {
                        regexp: /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    }
                }
            },
            branch_name: {
                validators: {
                    notEmpty: {
                        message: "This field is required."
                    },
                    regexp: {
                        regexp: /^[a-z\s-]+$/i,
                        message: "Please enter letters only."
                    }
                }
            },
            acc_type: {
                validators: {
                    callback: {
                        message: "Please select ",
                        callback: function (value, validator) {
                            // console.log(value);
                            return value > 0;
                        }
                    }
                }
            },
            acc_no: {
                validators: {
                   notEmpty: {
                        message: "This field is required."
                    },
                    regexp: {
                        regexp: '^[0-9]{10,16}$',
                        message: "Please enter 10 to 16 digits."
                    }

                }
            },
            micr_code: {
                validators: {
                    notEmpty: {
                        message: "This field is required"
                    },
                    regexp: {
                        regexp: '^[0-9]{9,9}$',
                        message: "Please enter 9 digits."
                    }                    
                }
            },
            ifsc_code: {
                validators: {
                    notEmpty: {
                        message: "This field is required."
                    },
                    stringLength: {
                        min: 11,
                        max: 11
                    },
                    regexp: {
                        regexp:  /^[A-Z]{4}/i,
                        message: "Please enter IFSC format (first 4 letters)."
                    }

                }
            },
            currency_code: {
                validators: {
                   callback: {
                        message: "Please select ",
                        callback: function (value, validator) {
                            // console.log(value);
                            return value > 0;
                        }
                    }

                }
            }
            
        }}).on('success.form.bv', function (event) {
        event.preventDefault();
        var datastring = '';
        var datastring = $("#emp_bank_info").serialize();
        var emp_id = $("#user_id").val();
        $.ajax({
            url: '/employee/updateEmpBankInfo/'+emp_id,
            data: datastring,
            type: 'get',
             beforeSend: function () {
            $('[class="loader"]').show();
            $(".overlay").show();
            },
            complete: function () {
            $('[class="loader"]').hide();
            $(".overlay").hide();
            },
            success: function (response) {
                
                var data = $.parseJSON(response);
                if (data.status) {
                    console.log(data.data);
                    $('#flass_message').text(data.message);
                    $('#alert_msg_div').show();
                    $('#alert_msg_div').removeClass('hide');
                    $('#alert_msg_div').not('.alert-important').delay(3000).fadeOut(350);
                    $('html, body').animate({scrollTop: '0px'}, 500);

                    $("#email_error").html('');
                    $('#bank_edit').hide();
                    $('#bank_preview').show();

                    $("#preview_acc_name").text(data.data.acc_name);
                    $("#preview_bank_name").text(data.data.bank_name);
                    $("#preview_branch_name").text(data.data.branch_name);

                    $("#preview_acc_type").text(data.data.acc_type);
                    $("#preview_acc_no").text(data.data.acc_no);
                    $("#preview_ifsc_code").text(data.data.ifsc_code);

                    $("#preview_micr_code").text(data.data.micr_code);
                    $("#preview_currency_code").text(data.data.currency_code);

                }
            }
        });
    });
jQuery.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || /^[a-z\s]+$/i.test(value);
}, "Please enter only letters."); 
    $("#empEducation").validate({
    ignore: '*:not([name])',
    rules:
    {
        educationType:
        {
            required: {
                depends: function(element) {
                    return $("#educationType").val() == '';
                }
            }
        },
        year:
        {
            required: true,
            digits: true,
            minlength:4,
            maxlength:4
        },
        university:
        {
            required: true,
            lettersonly: true 
        },
        institute:{
            required: true,
            lettersonly: true 
        },
        percentage:
        {
            required: true,
            number: true,
            maxlength:5
        }
    },
    messages: {
        year: {
          required: "Please enter year.",
          maxlength: jQuery.validator.format("Please enter 4 digits."),
          minlength: jQuery.validator.format("Please enter 4 digits.")
        },
        percentage:{
            required: "Please enter percentage.",
            maxlength: jQuery.validator.format("Please enter max 2 decimal.")
        },
        educationType:
        {
            required: "Please select education type."
        }
    },
    submitHandler: function (form)
    {
        var datastring = $("#empEducation").serialize();
        var emp_id = $("#user_id").val();
        var currentTime = new Date();
        var year = currentTime.getFullYear();
        var edu_year = $("#year").val();
        if(edu_year <= year)
        {
            $.ajax({
            url: '/employee/uploadEductionDetails/'+emp_id,
            data: datastring,
            type: 'get', 
            beforeSend: function (xhr) {
            $('body').spinnerQueue({showSpeed: 'fast', hideSpeed: 'fast'}).spinnerQueue('started', 'box2Load', true);
            },
            complete: function (jqXHR, textStatus) {
            $('body').spinnerQueue({showSpeed: 'fast', hideSpeed: 'fast'}).spinnerQueue('finished', 'box2Load', true);
            },
            success: function (response) 
            {
                $("#educationType").select2('val', '');
                document.getElementById("empEducation").reset();
                $('#emp_education_table tbody').html(response.eduText);
                
                $("#alert_msg").html('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><span id="flass_message">Saved successfully</span></div>');
                $("#alert_msg").show();
               $('#alert_msg').delay(2000).hide(0);
                
                //alert('Saved successfully');
                console.log(response.count);
                if(response.count!=0)
                {
                    $("#table_msg").hide();
                }
            }
        });
        }else
        {
            
            $("#alert_msg").html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><span id="flass_message">Please enter proper education year</span></div>');
            $("#alert_msg").show();
            $('#alert_msg').delay(2000).hide(0);
        }
    }
});

//ajax search for ifsc code
$( "#ifsc_code" ).autocomplete({   
        minLength:1,
        source: '/employee/getifsclist',  
        select: function (event, ui) {
            var label = ui.item.label;
            var ifsc = ui.item.ifsc;
            var branch = ui.item.branch;
            var bank = ui.item.bank_name;
            var micr = ui.item.micr_code;
            $("#ifsc_code").val(ifsc);
            $("#bank_name").val(bank);
            $("#branch_name").val(branch);
            $("#micr_code").val(micr);
            $('#emp_bank_info').bootstrapValidator('revalidateField', 'ifsc_code');

        }
});
