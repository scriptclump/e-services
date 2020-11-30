$("#empEducation_form").bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
//        valid: 'glyphicon glyphicon-ok',
//        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
    },
    excluded: [':disabled'],
    fields: {
        institute:{
            validators: {
                notEmpty: {
                    message: "This field is required."
                    }
            }
        },
        degree:{
            validators: {
                notEmpty: {
                    message: "This field is required."
                    }
            }
            
        },
        grade:{
            validators: {
                regexp: {
                    regexp: /^[a-zA-Z0-9.\+\s]{0,10}$/,
                    message: "Please enter letters or numbers and max length is 10."
                }
            }
            
        },
        from_year:{
            validators: {
                notEmpty: {
                    message: "This field is required."
                    }
            }
        }
    }
}).on('success.form.bv', function (event) 
{
    event.preventDefault();
    var datastring = '';
    var datastring = $("#empEducation_form").serialize();
    console.log(datastring);
    var emp_id = $("#user_id").val();
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
            $('#empEducation_form').bootstrapValidator('resetForm', true);
            $('#empEducation_form')[0].reset();
            $('#emp_education_table tbody').html(response.eduText);
            $("#alert_msg").html('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><span id="flass_message">Saved successfully</span></div>');
            $("#alert_msg").show();
            $('#alert_msg').delay(2000).hide(0);
            if(response.count!=0)
            {
                $("#edu_table_msg").hide();
            }
            $('#education_modal').modal('hide');   
        }
    });
});



function editEducation(id)
{
    $('#empEducation_form').bootstrapValidator('resetForm', true);
    $.ajax({
        url: '/employee/editEducation/'+id,
        type: 'get',
        beforeSend: function (xhr) {
        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed: 'fast'}).spinnerQueue('started', 'box2Load', true);
        },
        complete: function (jqXHR, textStatus) {
        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed: 'fast'}).spinnerQueue('finished', 'box2Load', true);
        },
        success: function (response) 
        {
            $("#institute").val(response.institute);
            $("#degree").val(response.degree);
            $("#specilization").val(response.specilization);
            $("#from_year").val(response.from_year);
            $("#to_year").val(response.to_year);
            $("#grade").val(response.grade);
            $("#emp_education_id").val(response.emp_education_id);
            $("#education_tab_title").text("Edit Education Information");
            $('#education_modal').modal('show');   
        }
    });
    
}
var start = new Date();
var end = new Date(new Date().setYear(start.getFullYear() + 5)); 
$('#from_year').datepicker({
    endDate: "+0d",
    autoclose: true,        
    format: 'dd-M-yyyy'    
}).on('changeDate', function () 
{        
    stDate = new Date($(this).val());    
    $('#to_year').datepicker('setStartDate', $('#from_year').val());
    $('#empEducation_form').bootstrapValidator('revalidateField', 'from_year');   
}); 
$('#to_year').datepicker({        
    endDate: "+0d",       
    autoclose: true,        
    format: 'dd-M-yyyy'    
}).on('changeDate', function () { 
    $('#from_year').datepicker('setEndDate', $('#to_year').val());
    $('#empEducation_form').bootstrapValidator('revalidateField', 'to_year');      
});



$("#add_btn_education").click(function()
{
    $("#emp_education_id").val('');
    $('#empEducation_form').bootstrapValidator('resetForm', true);
    $('#empEducation_form')[0].reset();
    $("#education_tab_title").text("Add Education Information");
});