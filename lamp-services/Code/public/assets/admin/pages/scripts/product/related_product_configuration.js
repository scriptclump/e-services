$("#taxTypes_headers").removeAttr("style");
    
$(document).on('click', '.deletechild',function() {      
    var productname = $(this).attr('data_attr_productName');
    var chaildId    = $(this).attr('data_attr_chaild_id');             
    var parentId    = $("#product_id").val();            
    var token       = $("#csrf-token").val();
    var conFirm     =  confirm('Are you sure you want to delete this product (' + productname + ') ?');
    if(conFirm == true) {
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
                url: '/productlist/deleteChildproducts',                    
                type: 'POST',
                data: 'parentId='+parentId+'&chaildId='+chaildId,
                success: function(response) {
                    $('#flass_message').text('You have successfully deleted this product (' +  productname + ')' )
                    $('div.alert').show();
                    $('div.alert').removeClass('hide');
                    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);                            
                    $("#productsListGrid").igGrid("dataBind");
                }
        }); 
    }
}); 
 $(document).on('click', '.cp_enabled', function () {
    var product_name = $(this).attr('name')
    var cp_flag = '';
    if ($(this).is(":checked") === true) {
        cp_flag = 1;
    } else {
        cp_flag = 0;
    }            
    var productId       = $(this).attr('data_attr_productid');
    var cpPricing       = $(this).attr('value');
    var supplierId      = $(this).attr('data_attr_supplierId');  
    var tax_val         = $(this).attr('data_attr_tax');
    //var is_sellable     = $(this).attr('data_is_sellable'); 
    var is_sellble  = $("#product_is_sellble").val();    
        if( cp_flag == 1) { 
            if (cpPricing == '' || tax_val == '' || is_sellble == '0') {
                $('#flass_message').text('Tax/Pricing/is Sellable  not available for this product ' + ' ( ' + product_name + ' )');
                $('div.alert').show();
                $('div.alert').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
                $('div.alert').not('.alert-important').delay(5000).fadeOut("450");                    
                return false;
            }
        }
    var token = $("#csrf-token").val();
    if (productId != '') {
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/productlist/cpStatus',
            type: 'POST',
            data: {ProductId: productId, flag: cp_flag},
            
            beforeSend: function (xhr) {
              $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);  
            },
            complete: function (jqXHR, textStatus) {
                $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
            },
            success: function (response) {
                if (response) {
                    if (cp_flag == 1) {
                        $('#flass_message').text('CP enabled for this product' + ' ( ' + product_name + ' ) ');
                    } else {
                        $('#flass_message').text('CP disabled for this product' + ' ( ' + product_name + ' ) ');
                    }
                    $('div.alert').show();
                    $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                    $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                    $("#product_cp_enable"+productId).trigger('click');
                    //$('#productsListGrid').igGrid('dataBind');
                }
            }
        })
    }
}); 
$("#rlated_product").on('click', function () {
    $(".enableDisableProduct").attr('disabled', true);
}); 
function deleteRelatedProduct(pid) {    
    if (confirm('Are you sure you want to delete?')) {
        $.ajax({
        headers: {'X-CSRF-TOKEN': token},
                url: '/deleterelatedproduct/' + pid,
                processData: false,
                contentType: false,
                success: function (rs) {
                $("#relatedProductsGrid").igHierarchicalGrid({"dataSource":'/relatedproducts/' + $('#product_id').val()});                
                }
        });
    }
}

$(document).on('click','.product_cp_enabled', function() {
   var productId  = $(this).attr('data_product_id');
   var pricing     = $(this).attr('data_product_pricing'); 
   var tax         = $(this).attr('data_product_tax'); 
   var is_sellble  = $("#product_is_sellble").val();
   
   var product_name = $(this).attr('data_product_name');
   
    var cp_flag = '';
    if ($(this).is(":checked") === true) {
        cp_flag = 1;
    } else {
        cp_flag = 0;
    }
    if( cp_flag == 1) {         
        if (pricing == '' || tax == '' || is_sellble == '0') {            
            $('#flash_message').text('Tax/Pricing/is Sellable  not available for this product' + ' ( ' + product_name + ' )');
            $('div.alerts').show();
            $('div.alerts').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
            $('div.alerts').not('.alert-important').delay(5000).fadeOut("450");  
            $('html, body').animate({scrollTop: '0px'}, 800);
            return false;
        }
        /*if (tax == 2) {            
            $('#flash_message').text('One/More Tax approval is pending for this product' + ' ( ' + product_name + ' )');
            $('div.alerts').show();
            $('div.alerts').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
            $('div.alerts').not('.alert-important').delay(5000).fadeOut("450");  
            $('html, body').animate({scrollTop: '0px'}, 800);
            return false;
        }*/
    }
    
    var token = $("#csrf-token").val();
    if (productId != '') {
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/productlist/cpStatus',
            type: 'POST',
            data: {ProductId: productId, flag: cp_flag},
            beforeSend: function (xhr) {
              $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);  
            },
            complete: function (jqXHR, textStatus) {
                $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
            },
            success: function (response) {
                if (response) {
                    if (cp_flag == 1) {
                        $('#flash_message').text('CP enabled for this product' + ' ( ' + product_name + ' ) ');
                    } else {
                        $('#flash_message').text('CP disabled for this product' + ' ( ' + product_name + ' ) ');
                    }
                    $('div.alerts').show();
                    $('div.alerts').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                    $('div.alerts').not('.alert-important').delay(5000).fadeOut("450"); 
                    $('html, body').animate({scrollTop: '0px'}, 800);
                    $("#cp_chaild"+productId).trigger('click');
                    if(cp_flag==0){
                            $("#cpEnableTableGrid").igGrid({dataSource: '/products/cpenabledcfcproducts?product_id=' + $('#product_id').val()}).igGrid("dataBind");
                        }
                }
            }
        });
    }   
});
