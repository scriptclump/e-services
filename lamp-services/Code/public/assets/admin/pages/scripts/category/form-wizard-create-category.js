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

// creating product packge configurations

           var form_wh = $('#category_configuration');
            var error_wh = $('.alert-danger', form_wh);
            var success_wh = $('.alert-success', form_wh);
  jQuery.validator.addMethod("url", function(value, element) {
            return this.optional(element) || /[h,t,p,s]{4,5}[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[".jpg", ".png"]{3,4}?/gi.test(value);
            });
            form_wh.validate({
			    onkeyup: false,
                onclick: false,
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: [],
                rules: {
                    name:{
                        required: true,
                    },
/*                    category_image:
                    {
                        required:true,
                        url: true                        
                    },*/
                    'segments[]':
                    {
                        required:true,
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
              token  = $("#csrf-token").val();

//                    var data = $('form#category_configuration').serialize();
                    var data = new FormData(form);
                    data.append('brow_image', $('#brow_image')[0].files[0]);
                   $.ajax({
                            headers: {'X-CSRF-TOKEN': token},
                            url:'/categories/saveparentcategory',
                            type: 'post',
                            data: data,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            success:function(response){ 
                                                            
                            $.ajax({
                                headers: {'X-CSRF-TOKEN': token},
                                url:'/getAddCategoryList',
                                success:function(rs)
                                {
                                    $("#category").html(rs);
                                }
                            });
                                 categoryGrid();
                                //$("#addSupplierWarehouseGrid").igHierarchicalGrid({"dataSource":'/suppliers/getWarehouseList'});
                                alert('' + response.message);
                                $(".close").trigger('click'); 
                                $('form[id="category_configuration"]')[0].reset();
                               
                              },
                          });
                    return false;
                  
                    //document.getElementById("submit_form").submit();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }

            });
   /* $(document).on('click', '.save_package', function (event) {
        
         if(form_wh.validate())
         {
              token  = $("#csrf-token").val();

                    
                    



         }
    });     */       
			
        }

    };

}();