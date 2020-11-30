var FormWizard = function(){


    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

			         //save approval
		
		
            var form = $('#save_attribute_set_form');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

// creating product packge configurations
            jQuery.validator.addMethod("notEqual", function(value, element, param) {
              return this.optional(element) || value != param;
            }, "Please specify a different (non-default) value");
            form.validate({
			    onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    add_attribute_set_name:{
                        required: true,
                    },
                    attribute_set_category_id:
                    {
                        required: true,
                        notEqual: "0"     
                    }
                },
                errorPlacement: function (error, element) { // render error placement for each input type
                    error.appendTo(element.closest('.err1'));
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
                    var selectedAttr = new Array();
                    var selectedAttrArray = new Array();
                    $('#attribute_id div').each(function (i, v) {
                        selectedAttr.push($(v).attr('value'));
                        selectedAttrArray.push($(v).attr('key'));
                    });
                    
                    var category_id=$('[name="attribute_set_category_id"]').val();
                    var inherit = $('[name="attribute_set[inherit_from]"]').prop('checked');
                    if ( inherit )
                    {
                        inherit = 1;
                    } else {
                        inherit = 0;
                    }  
                    var temp = {
                        attribute_set_name: $('[name="add_attribute_set_name"]').val(),
                        category_id: category_id,
                        is_active:'1',
                        attribute_id: selectedAttr,
                        inherit_from: inherit
                    };
                    var url = '/product/saveattributeset';
                    var token  = $("#csrf-token").val(); 
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        url: url,
                        data:temp,
                        type: 'POST',                                          
                        success: function (data) 
                        {
                            if (data['status'] == true )
                            {        
                                var attribut_options="<option value='1'>General</option>"; 
                                var attribut_group_var="";                      
                                $.ajax({
                                  headers: {'X-CSRF-TOKEN': token},
                                  url: '/product/getattributelistdata',
                                  data: {attribute_set: data['set_id']},
                                  type: 'POST',
                                    success: function (dataRs)
                                    { 
                                      $("#att_set_model_table").empty();
                                       $.ajax({
                                        headers: {'X-CSRF-TOKEN': token},
                                        url: '/product/getAllAttributeGroup/'+category_id,
                                        processData: true,
                                            success: function (attRs)
                                            {   

                                                 $.each(attRs, function(l,el)
                                                    {
                                                         attribut_options+= "<option value='"+el.attribute_group_id+"'>"+el.name+"</option>";
                                                    });
                                                

                                                 $.each(dataRs, function (index, value) {
                                                 
                                                 
                                                   $("#att_set_model_table").append('<tr><td>'+value.name+'</td><td><select name="attribute_group_id" id="attribute_group_id'+value.attribute_id+   '" onchange="addAttributGroup('+value.attribute_id+','+value.attribute_set_id+');" class="form-control">'+attribut_options+'</select></td><td><label class="switch"><input class="switch-input vr_status'+value.attribute_id+'" onclick="vr_enabled('+value.attribute_id+','+value.attribute_set_id+');" type="checkbox" id="vr_enabled_id'+value.attribute_id+'"/><span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label></td><td><label class="switch"><input class="switch-input vr_secondary_status'+value.attribute_id+'" onclick="vr_secondary_enabled('+value.attribute_id+','+value.attribute_set_id+');" type="checkbox" id="vr_secondary_enabled_id'+value.attribute_id+'"/><span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label></td><td><label class="switch"><input class="switch-input vr_third_status'+value.attribute_id+'" onclick="vr_third_enabled('+value.attribute_id+','+value.attribute_set_id+');" type="checkbox" id="vr_third_enabled_id'+value.attribute_id+'"/><span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label></td><td><label class="switch"><input class="switch-input is_searchble'+value.attribute_id+'" id="is_searchble'+value.attribute_id+'" onclick ="switchAttributeSearchable('+value.attribute_id+','+value.attribute_set_id+',1);" type="checkbox" /><span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label></td><td><label class="switch"><input class="switch-input filter_status'+value.attribute_id+'" id="is_filterable_id'+value.attribute_id+'" onclick ="checkIsFilterble('+value.attribute_id+','+value.attribute_set_id+');" type="checkbox"/><span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label></td></tr>');
                                                });
                                                 $("#next_tab").click();   
                                            }
                                        });
                                        
                                        
                                       
                                     //ajaxCall(); 
                                    },
                                    error: function (err) {
                                        console.log('Error: ' + err);
                                    },
                                    complete: function (data) {
                                        console.log(data);
                                    }
                                });
                            }
                            else
                            {
                                alert(data['message']);
                            }                          
                        }
                    });
                
                }
            });



        //this is for attribute group 
         var form_attribute_group = $('#save_attribute_form');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

// creating product packge configurations
            jQuery.validator.addMethod("notEqual", function(value, element, param) {
              return this.optional(element) || value != param;
            }, "Please specify a different (non-default) value");
            form_attribute_group.validate({
                onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    attribute_group_name:{
                        required: true,
                    },
                    attribute_group_category_id:
                    {
                        required: true,
                        notEqual: "0"     
                    }
                },
                errorPlacement: function (error, element) { // render error placement for each input type
                    error.appendTo(element.closest('.err1'));
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
                        token  = $("#csrf-token1").val();
                        var att_group=$("#attribute_group_name").val();
                        var cat_id= $("#category4").val();
                        var data={att_group:att_group,cat_id:cat_id};
                     $.ajax({
                            headers: {'X-CSRF-TOKEN': token},
                            url: '/product/saveAttributeGroup',
                            data: data,
                            type: 'POST',
                            success: function (dataRs)
                            {   
                                if(dataRs.status == true)
                                {
                                    alert(dataRs.message);
                                    $(".close").click(); 
                                    attributes_grid();
                                }else
                                {
                                     alert(dataRs.message);
                                }
                                                                 
                            },
                            error: function (err) {
                              console.log('Error: ' + err);
                            },
                            complete: function (data) {
                              console.log(data);
                            }
                          });      
                }
            });

            //this is for edit attribute set 

            var form_edit = $('#editAttributeset_form');
            var error_edit = $('.alert-danger', form);
            var success_edit = $('.alert-success', form);

// creating product packge configurations
            jQuery.validator.addMethod("notEqual", function(value, element, param) {
              return this.optional(element) || value != param;
            }, "Please specify a different (non-default) value");
            form_edit.validate({
                onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    attribute_set_name:{
                        required: true,
                    },
                    category3:
                    {
                        required: true,
                        notEqual: "0"     
                    }
                },
                errorPlacement: function (error, element) { // render error placement for each input type
                    error.appendTo(element.closest('.err1'));
                },

                invalidHandler: function (event, validator) { //display error alert on form submit   
                    success_edit.hide();
                    error_edit.show();
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

                       var formattributes1 = '';
                     
                      //alert("checking"+formattributes1.length);
                       token  = $("#csrf-token1").val();
                        $('#attribute_id1 div').each(function (i, v) {
                            formattributes1 += ',' + $(v).attr('value');
                        });
                    formattributes1 = formattributes1.substr(1, formattributes1.length);
                    $('#formattributes1').val(formattributes1);
                    var attribute_name=$("#attribute_set_name").val();
                    var category_id=$("#category3").val();
                    var att_set_id= $("#attribute_set_id").val();
                      var editCategorySerialize= $("#editAttributeset_form").serialize();
                        $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        url: '/product/updateattributeset',
                        data: editCategorySerialize,
                        type: 'POST',
                        success: function (dataRs)
                        {     
                            if(dataRs.status== true)
                            {
                                
                                getAttributeSetName(attribute_name,category_id,att_set_id);
                                $("#edit_next_tab").click();
                            }
                            else 
                            {
                                alert(dataRs.message);
                            }
                                   
                          },
                          error: function (err) {
                              console.log('Error: ' + err);
                          },
                          complete: function (data) {
                              console.log(data);
                          }
                      });               
                }
            });
        }

    };

}();

