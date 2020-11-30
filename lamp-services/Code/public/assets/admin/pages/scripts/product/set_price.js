$(document).on('click', '.set_price', function(e) {         
    $('a[href="#setPrice"]').trigger('click');
    $("#set_price_elp").val('');
    $("#set_price_date").val('');
    
    $('#set_price_date').datepicker("option", "minDate", new Date());
    $("#set_price_date").keydown(function() {
        return false;
    });
    //$("#set_price_form").data('validator').resetForm();
    $('#set_price_form .has-error').each(function(){
     $(this).removeClass('has-error');
    });
     var product_price_id = $(this).attr('href');     
     var token = $("#csrf-token").val();     
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/suppliers/editSetPrice/' + product_price_id,
            type: 'POST',
            success:  function(response) {
               var data     = $.parseJSON(response);
               var supId    = data[0].supp_id;               
               var prod_id  = data[0].prd_id;
                $("#set_price_whid").val(data[0].wh_id);                
                $("#set_supplier_id").val(data[0].supp_name);
                $("#set_product_id").val(data[0].prod_title);
                $("#price_mrp").val(data[0].mrp); 
                
                $("#set_price_productId").val(prod_id);
                $("#set_price_whId").val(data[0].wh_id);
                $("#set_price_supId").val(supId);
                console.log(response);
            }
            
        });
        $(".set_price_whid").attr('disabled',true);
        $("#set_supplier_id").attr('disabled',true);
        $("#set_product_id").attr('disabled',true);
        $("#price_mrp").attr('disabled',true);
      
   });