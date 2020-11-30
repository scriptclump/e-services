jQuery.validator.setDefaults({
  debug: true,
  success: "valid"
});
$("#experience_form").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
//        valid: 'glyphicon glyphicon-ok',
//        invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            designation: {
                validators: {
                    notEmpty: {
                        message: "Please enter your title."
                    },
                    regexp: {
                        regexp:  /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    }
                }
            },
            organization_name: {
                validators: {
                    notEmpty: {
                        message: "Please enter company name."
                    },
                    regexp: {
                        regexp:  /^[a-z0-9\s]+$/i,
                        message: "Please enter letters and numbers only."
                    }
                }
            },
            location: {
                validators: {
                    notEmpty: {
                        message: "Please enter location."
                    },
                    regexp: {
                        regexp:  /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    }
                }
            },
            from_date: {
                validators: {
                    notEmpty: {
                        message: 'Please select date'
                    }
                }
            },
            to_date: {
                validators: {
                    notEmpty: {
                        message: "Please select date."
                    }
                }
            },
            reference_name: {
                validators: {
                    regexp: {
                        regexp:  /^[a-z\s]+$/i,
                        message: "Please enter letters only."
                    }
                }
            },
            reference_contact_number: {
                validators: {
                   regexp: {
                        regexp:  /^[0-9]{10,10}$/,
                        message: "Please enter 10 digits."
                    }
                }
            }
        }}).on('success.form.bv', function (event) {
        event.preventDefault();
        var datastring = '';
        var datastring = $("#experience_form").serialize();
        var emp_id = $("#user_id").val();
        $.ajax({
            url: '/employee/saveEmpExperienceInfo/'+emp_id,
            data: datastring,
            type: 'get',
            success: function (response)
            {
                console.log(response);
                var data =jQuery.parseJSON(JSON.stringify(response));
               if (data.status)
               {
                    $('#flass_message').text(data.message);
                    $('div.alert').show();
                    $('div.alert').removeClass('hide');
                    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                    $('html, body').animate({scrollTop: '0px'}, 500);
                    $('#experience_form').bootstrapValidator('resetForm', true);
                    $('#experience_form')[0].reset();
                    $('#myModal').modal('toggle');
                    $('#emp_experience_table tbody').html(data.data);
                    if(response.count!=0)
                    {
                        $("#exp_table_msg").hide();
                    }
                }
            }
        });
    });


$(document).on('click', '.delete_experience', function () {
        var docId = $(this).attr("id");
        if (confirm('Do you want to delete this record?')) {
            deleteExperience(docId);
            $(this).closest('tr').remove();
            $('#flass_message').text('Experience deleted successfully');
            $('div.alert').show();
            $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
            $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
            $('html, body').animate({scrollTop: '0px'}, 800);
            var tbody = $("#emp_experience_table tbody");
            if (tbody.children().length == 0) 
            {
                $('#exp_table_msg').html("</p>No Records Found.</p>");
            }
        }
    });
function deleteExperience(id)
{
    $.ajax({
    headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: "/employee/deleteExperience/" + id,
            type: "POST",
            data: {id: id},
            dataType: 'json',
            success: function (response) {
                $('#ajaxResponse').html(response.message);
                console.log(response);
            },
            error: function (response) {
            $('#ajaxResponse').html('Unable to delete');
            }
    });
}
    

var start = new Date();
var end = new Date(new Date().setYear(start.getFullYear() + 5)); 
$('#from_date').datepicker({
    endDate: "+0d",
    autoclose: true,        
    format: 'dd-M-yyyy'    
}).on('changeDate', function () 
{        
    stDate = new Date($(this).val());    
    $('#to_date').datepicker('setStartDate', $('#from_date').val());
    $('#experience_form').bootstrapValidator('revalidateField', 'from_date');   
}); 
$('#to_date').datepicker({        
    endDate: "+0d",       
    autoclose: true,        
    format: 'dd-M-yyyy'    
}).on('changeDate', function () { 
    $('#from_date').datepicker('setEndDate', "+0d");
    $('#experience_form').bootstrapValidator('revalidateField', 'to_date');      
});
$(".experi_close").click(function(){
    $('#experience_form').bootstrapValidator('resetForm', true);
    $('#experience_form')[0].reset();
});
function addExperience()
{
    $(".experience_title").text("Add Experience");
    //$('#myModal').modal('show');
    $("#work_experience_id").val("");
    $('#experience_form').bootstrapValidator('resetForm', true);
    $('#experience_form')[0].reset();  
}
function editExperience(id)
{
    $(".experience_title").text("Edit Experience");
    $.ajax({
        url: '/employee/getEmpExperienceInfobyid/'+id,
        type: 'get',
        success: function (response)
        {
            $('#myModal').modal('show');     
            $("#designation").val(response.data.designation);           
            $("#organization_name").val(response.data.organization_name);
            $("#location").val(response.data.location);
            $("#reference_name").val(response.data.reference_name);
            $("#reference_contact_number").val(response.data.reference_contact_number);
            $("#from_date").val(response.data.from_date);
            $("#to_date").val(response.data.to_date);
            $("#work_experience_id").val(response.data.work_experience_id);
            //console.log(response.data.work_experience_id);

        }
    });
}