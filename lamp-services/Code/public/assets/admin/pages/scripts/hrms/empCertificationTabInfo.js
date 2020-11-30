$("#certification_form").bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
//        valid: 'glyphicon glyphicon-ok',
//        invalid: 'glyphicon glyphicon-remove',
        validating: 'glyphicon glyphicon-refresh'
    },
    excluded: [':disabled'],
    fields: {
        certification_name:{
            validators: {
                notEmpty: {
                    message: "This field is required."
                    }
            }
        },
        institution_name:{
            validators: {
                notEmpty: {
                    message: "This field is required."
                    }
            }
            
        },
        grade:{
            validators: {
                regexp: {
                    regexp:  /^[a-z\s]+$/i,
                    message: "Please enter  letters only."
                }
            }
            
        },
        certified_on:{
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
    var datastring = $("#certification_form").serialize();
    console.log(datastring);
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
            console.log("im here");
            $('#certification_form').bootstrapValidator('resetForm', true);
            $('#certification_form')[0].reset();
            $('#emp_certification_table tbody').html(response.cerText);
            $("#alert_msg").html('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><span id="flass_message">Saved successfully</span></div>');
            $("#alert_msg").show();
            $('#alert_msg').delay(2000).hide(0);
            if(response.count!=0)
            {
                $("#cer_table_msg").hide();
            }
            $('#certification_modal').modal('hide');   
        }
    });
});



function editCertifications(id)
{
    $('#certification_form').bootstrapValidator('resetForm', true);
    $.ajax({
        url: '/employee/editCertification/'+id,
        type: 'get',
        beforeSend: function (xhr) {
        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed: 'fast'}).spinnerQueue('started', 'box2Load', true);
        },
        complete: function (jqXHR, textStatus) {
        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed: 'fast'}).spinnerQueue('finished', 'box2Load', true);
        },
        success: function (response) 
        {
            $("#certification_name").val(response.certification_name);
            $("#institution_name").val(response.institution_name);
            $("#certified_on").val(response.certified_on);
            $("#valid_upto").val(response.valid_upto);
            $("#cer_grade").val(response.grade);
            $("#employee_certification_id").val(response.employee_certification_id);
            $("#certification_tab_title").text("Edit Certification Information");
            $('#certification_modal').modal('show');   
        }
    });
    
}
var start = new Date();
var end = new Date(new Date().setYear(start.getFullYear() + 5)); 
$('#certified_on').datepicker({
    endDate: "+0d",
    autoclose: true,        
    format: 'dd-M-yyyy'    
}).on('changeDate', function () 
{        
    stDate = new Date($(this).val());    
    $('#valid_upto').datepicker('setStartDate', $('#certified_on').val());
    $('#certification_form').bootstrapValidator('revalidateField', 'certified_on');   
}); 
$('#valid_upto').datepicker({        
    endDate: "+0d",       
    autoclose: true,        
    format: 'dd-M-yyyy'    
}).on('changeDate', function () { 
    $('#certified_on').datepicker('setEndDate', $('#valid_upto').val());
    $('#certification_form').bootstrapValidator('revalidateField', 'valid_upto');      
});



$("#add_btn_certification").click(function()
{
    $("#employee_certification_id").val("");
    $('#certification_form').bootstrapValidator('resetForm', true);
    $('#certification_form')[0].reset();
    $("#certification_tab_title").text("Add Certification Information");
});

