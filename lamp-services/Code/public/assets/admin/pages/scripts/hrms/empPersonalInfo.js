$("#emp_personal_info").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
//        valid: 'glyphicon glyphicon-ok',
//        invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            ref_one_relation: {
                validators: {
                    regexp: {
                        regexp:  /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    }
                }
            },
            ref_two_relation: {
                validators: {
                    regexp: {
                        regexp:  /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    }
                }
            },
            ref_one_contact_no: {
                validators: {
                    regexp: {
                        regexp: '^[0-9]{10,10}$',
                        message: "Please enter 10 digits only."
                    } 
                }
            },
            ref_two_contact_no: {
                validators: {
                    regexp: {
                        regexp: '^[0-9]{10,10}$',
                        message: "Please enter 10 digits only."
                    } 
                }
            },
            ref_one_pin_code: {
                validators: {
                    regexp: {
                        regexp: '^[0-9]{6,6}$',
                        message: "Please enter 6 digits."
                    }  
                }
            },
            pe_zip_code: {
                validators: {
                    regexp: {
                        regexp: '^[0-9]{6,6}$',
                        message: "Please enter 6 digits."
                    }  
                }
            },
            pe_city: {
                validators: {
                    regexp: {
                        regexp:  /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    }
                }
            },
            ref_two_city:{
                validators: {
               
                    regexp: {
                        regexp:  /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    }
                }
            }
            ,
            ref_one_city:{
                validators: {
                    regexp: {
                        regexp:  /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    }
                }
            },
            cu_city :{
                validators: {
                    regexp: {
                        regexp:  /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    }
                }
            },
            cu_zip_code:{
                validators: {
                    regexp: {
                        regexp: '^[0-9]{6,6}$',
                        message: "Please enter 6 digits."
                    }  
                }
            },
            ref_two_pin_code:{
                validators: {
                    regexp: {
                        regexp: '^[0-9]{6,6}$',
                        message: "Please enter 6 digits."
                    }  
                }
            },
            cu_address2:{
                validators: {
                }
            },
            cu_address:{
                validators: {
                }
            },
            emergency_contact_one: {
                validators: {
                    notEmpty: {
                        message: "Mobile number is required."
                    },
                    regexp: {
                        regexp: '^[0-9]{10,10}$',
                        message: "Please enter 10 digits only."
                    },
                }
            },
            emergency_contact_two: {
                validators: {
                    regexp: {
                        regexp: '^[0-9]{10,10}$',
                        message: "Please enter 10 digits only."
                    },
                }
            },
            emergency_name: {
                validators: {
                    notEmpty: {
                        message: "Name is required."
                    },
                    regexp: {
                        regexp:  /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    },
                }
            },
            emergency_relation: {
                validators: {
                    regexp: {
                        regexp:  /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    },
                }
            },
        }}).on('success.form.bv', function (event) {
        event.preventDefault();
        var datastring = '';
        var datastring = $("#emp_personal_info").serialize();
        
        console.log(datastring);
        $.ajax({
            url: '/employee/updateemployeepersonaldetails',
            data: datastring,
            type: 'get',
            beforeSend: function () {
            $('[class="basicInfoLoader"]').show();
            $(".basicInfoOverlay").show();
            },
            complete: function () {
            $('[class="basicInfoLoader"]').hide();
            $(".basicInfoOverlay").hide();
            },
            success: function (response) {

                var data = $.parseJSON(response);
                if (data.status) {
                    $('#flass_message').text(data.message);
                    $('#alert_msg_div').show();
                    $('#alert_msg_div').removeClass('hide');
                    $('#alert_msg_div').not('.alert-important').delay(3000).fadeOut(350);
                    $('html, body').animate({scrollTop: '0px'}, 500);

                    $("#email_error").html('');
                    $('#emp_personal_info_show1').show();
                    $('#edit_personal_info').hide();

                    $("#preview_cu_address").text(data.data.cu_address);
                    $("#preview_cu_address2").text(data.data.cu_address2);
                    $("#preview_cu_city").text(data.data.cu_city);
                    if(data.data.cu_state == null){
                    $("#preview_cu_state").text("");
                    }else{
                        $("#preview_cu_state").text(data.data.cu_state);
                    }
                    if(data.data.cu_country==null){
                        $("#preview_cu_country").text("");
                    }else{
                    $("#preview_cu_country").text(data.data.cu_country);
                    }
                    if(data.data.cu_zip_code == 0){
                        $("#preview_cu_zip_code").text("");
                    }else{
                    $("#preview_cu_zip_code").text(data.data.cu_zip_code);
                }
                    $("#preview_pe_address").text(data.data.pe_address);
                    $("#preview_pe_address2").text(data.data.pe_address2);
                    $("#preview_pe_city").text(data.data.pe_city);
                    $("#preview_pe_state").text(data.data.pe_state);
                    $("#preview_pe_country").text(data.data.pe_country);
                    $("#preview_designation").text(data.data.designation_name);
                    $("#preview_pe_zip_code").text(data.data.pe_zip_code);
                    $("#preview_ref_one_relation").text(data.data.ref_one_relation);
                    $("#preview_ref_one_contact_no").text(data.data.ref_one_contact_no);
                    $("#preview_ref_one_address").text(data.data.ref_one_address);
                    $("#preview_ref_one_city").text(data.data.ref_one_city);
                    $("#preview_ref_one_state").text(data.data.ref_one_state);
                    $("#preview_ref_one_country").text(data.data.ref_one_country);
                    $("#preview_ref_one_pin_code").text(data.data.ref_one_pin_code);
                    $("#preview_ref_two_relation").text(data.data.ref_two_relation);
                    
                    $("#preview_ref_two_contact_nos").text(data.data.ref_two_contact_no);
                    $("#preview_ref_two_address").text(data.data.ref_two_address);
                    $("#preview_ref_two_city").text(data.data.ref_two_city);
                    if(data.data.ref_two_state == null){
                        $("#preview_ref_two_state").text("");
                    }else{
                    $("#preview_ref_two_state").text(data.data.ref_two_state);
                    }
                    if(data.data.ref_two_country == null){
                        $("#preview_ref_two_country").text("");
                    }else{
                        $("#preview_ref_two_country").text(data.data.ref_two_country);    
                    }
                    
                    $("#preview_ref_two_pin_code").text(data.data.ref_two_pin_code);

                    $("#preview_emergency_name").text(data.data.emergency_name);
                    $("#preview_emergency_relation").text(data.data.emergency_relation);
                    $("#preview_emergency_contact1").text(data.data.emergency_contact_one);
                    $("#preview_emergency_contact2").text(data.data.emergency_contact_two);
                }
            }
        });
    });

  $("#empCertification").validate({
    rules:
    {
        certification:
        {
            required: true,
        },
        institute:
        {
            required: true,
        },
        certified:{
            required: true,
            date: true 
        },
        valid_upto:
        {
            required: true
        }
    },
    messages: {
        certification: {
          required: "This field is required.",
        },
        valid_upto:{
            required: "This field is required.",
        },
        certified:
        {
            required: "This field is required.",
        },
        institute:
        {
            required: "This field is required.",
        }
    },
    submitHandler: function (form)
    {
        var datastring = $("#empCertification").serialize();
        var emp_id = $("#user_id").val();
        $.ajax({
            url: '/employee/saveCertificationDetails/'+emp_id,
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
                document.getElementById("empCertification").reset();
                $('#emp_certification_table tbody').html(response.cerText);
                $("#alert_msg").html('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><span id="flass_message">Saved successfully</span></div>');
                $("#alert_msg").show();
               $('#alert_msg').delay(2000).hide(0);
                if(response.count!=0)
                {
                    $("#cer_table_msg").hide();
                }
            }
        });

    }
});


$("#emp_ensurance_info_form").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
//        valid: 'glyphicon glyphicon-ok',
//        invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            spouse_name:{
                validators: {
                    regexp: 
                    {
                        regexp: /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    }
                    
                }
            },
            child_one_name:{
                validators: {
                    regexp: 
                    {
                        regexp: /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    }
                    
                }
            },
            child_two_name:{
                validators: {
                    regexp: 
                    {
                        regexp: /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    }
                    
                }
            },
            tpa_contact_number:{
                validators: {
                    regexp: 
                    {
                        regexp: '^[0-9]{10,10}$',
                        message: "Please enter 10 digits."
                    }
                }
            },
            card_number:{
                validators: {
                    regexp: {
                        regexp: /^[0-9]+$/i,
                        message: "Please enter digits."
                    }
                }
            },
            tpa:{
                validators: {
                    regexp: {
                        regexp:  /^[a-z0-9\s]+$/i,
                        message: "Please enter alphabets and numbers."
                    }
                }
            }
           
        }}).on('success.form.bv', function (event) {
        event.preventDefault();
        var datastring = $("#emp_ensurance_info_form").serialize();
        var empId= $("#user_id").val();
        $.ajax({
            url: '/employee/saveInsuranceDetails/'+empId,
            data: datastring,
            type: 'get',
            success: function (response) {
                var data = response;
                if (data.status == "200") {
                    console.log(data);
                    $('#flass_message').text(data.message);
                    $('#alert_msg_div').show();
                    $('#alert_msg_div').removeClass('hide');
                    $('#alert_msg_div').not('.alert-important').delay(3000).fadeOut(350);
                    $('html, body').animate({scrollTop: '0px'}, 500);

                    $("#email_error").html('');
                    $('#insurance_edit').hide();
                    $('#insurance_preview').show();
                    spouse_dob = "";
                    child1 = "";
                    spouse_name ="";
                    child1_name = "";
                    child2_name = "";
                    child2 ="";
                    tpa_cno ="";
                    tpa ="";
                    cno ="";
                    if(data.data.spouse_dob != null)
                    {
                        spouse_dob = data.data.spouse_dob;
                    }
                    if(data.data.child_one_dob != null)
                    {
                        child1 = data.data.child_one_dob;
                    }
                    if(data.data.child_two_dob != null)
                    {
                        child2 = data.data.child_two_dob;
                    }
                    if(data.data.spouse_name != null)
                    {
                        spouse_name = data.data.spouse_name;
                    }
                    if(data.data.child_one_name != null)
                    {
                        child1_name = data.data.child_one_name;
                    }
                    if(data.data.child_two_name != null)
                    {
                        child2_name = data.data.child_two_name;
                    }
                     if(data.data.tpa_contact_number != null)
                    {
                        tpa_cno = data.data.tpa_contact_number;
                    }
                    if(data.data.tpa != null)
                    {
                        tpa = data.data.tpa;
                    }
                    if(data.data.card_number != null)
                    {
                        cno = data.data.card_number;
                    }

                    $("#preview_spouse_name").text(spouse_name);
                    $("#preview_spouse_dob").text(spouse_dob);
                    $("#preview_child_one_name").text(child1_name);
                    $("#preview_child_one_dob").text(child1);
                    $("#preview_child_two_name").text(child2_name);
                    $("#preview_child_two_dob").text(child2);
                    $("#preview_tpa").text(tpa);                    
                    $("#preview_tpa_contact_number").text(tpa_cno);
                    $("#preview_card_number").text(cno);
                }else
                {
                    $('#insurance_edit').hide();
                    $('#insurance_preview').show();
                }
            }
        });
    });


