token = $("#csrf-token").val();
var att_code ="";
var product_id=$('#product_id').val();
var cat_id= $("#getcategory_id").val();
var kvi_id = $("#kvi_id").val();
if (kvi_id == "")
{
    kvi_id = 69001;
}
var check_is_sellable=0;
if($("#product_is_sellable").prop('checked') == true)
{
    check_is_sellable=1;
}
 $("#product_is_sellable").click(function(){

         var formData = new FormData();
         var is_sellable= ($("#product_is_sellable").prop('checked')==true)?1:0;         
         $("#product_is_sellble").val(is_sellable);
         formData.append('product_id', product_id);   
          formData.append('is_sellable',is_sellable); 
          $.ajax({
                  headers: {'X-CSRF-TOKEN': token},
                  method: "POST",
                  url:  '/products/saveProductIsSellable',
                  processData: false,
                  contentType: false,                                             
                  data: formData,
                  success: function (rs)
                  {
                      alert(rs);
                      if(rs=="Consumer Pack Outside Should be configured with Freebie else Sellable Status cannot be enabled" || rs=="Improper Offer Pack Configuration"){
                        $("#product_is_sellable").prop('checked',false);
                      }
                  }
              });
       }); 
//$("#addFreebie_model").hide();
$("#kvi_name").select2().select2('val', kvi_id);
var parent_id = $('#product_id').val();
$("#duplicate_product").validate({
        rules: {
            title: {
                required: true
            }
        },
        submitHandler: function (form) {
            $("#create_duplicate").removeClass("blue");
            $("#create_duplicate").addClass("btn-disabled");
            $("#create_duplicate").attr("disabled", true);
        $('.close').trigger('click');            
        //$('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);        
        product_id = $('#product_id').val();
        $('#pid').val(product_id);
         token = $("#csrf-token").val();
        $.ajax({
        headers: {'X-CSRF-TOKEN': token},
                url: '/product/duplicate/' + product_id,
                data: $('#duplicate_product').serialize(),
                async: false,
                type: 'POST',
                success: function (rs)
                {
                    if(rs != "false" || rs!= "false1"){
                        $("#success_flash_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Product duplicated sucessfully</div></div>');
                        $(".alert-success").fadeOut(40000);
                        location.href = '/editproduct/'+rs;                
                    } else {
                        if(rs == "false"){
                            $("#danger_flash_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>You don\'t have permission to create duplicate product</div></div>');
                            $(".alert-danger").fadeOut(40000);
                        }else{
                            $("#danger_flash_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Failed to update the product.Please try again!</div></div>');
                            $(".alert-danger").fadeOut(40000);
                        }
                        location.href = '/editproduct/' + product_id;
                    }
                }
        });
        }
    });             
//this is for only product related js start
 $("#edit_product_button").click(function()
{
    $('#edit_product_button').attr('disabled',true);
    if($('#kvi_name').val()==69010 && $('#attribute_name_q9_check').val()!='Freebie'){
        
            alert('Q9 products should always be Freebie');
            $('#edit_product_button').attr('disabled',true);    
            return false;
    }else if($('#kvi_name').val()!=69010 && $('#attribute_name_q9_check').val()=='Freebie'){
            alert('Improper Offer Pack configuration');
            $('#edit_product_button').attr('disabled',true);    
            return false;
    }

    var product_id=$('#product_id').val();
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
    type: 'POST',
    url: '/checkconsumerpackoutsideforproduct',
    data:{product_id:product_id},
    success:function(consumerpack){
        
            if(consumerpack==1 && $('#attribute_name_q9_check').val()!='Consumer Pack Outside'){

               alert('Products having Freebie should be Consumer pack Outside');
               $('#edit_product_button').attr('disabled',true);    
                return false;
            }else if(consumerpack==0 && $('#attribute_name_q9_check').val()=='Consumer Pack Outside'){
                alert('Improper Offer Pack configuration');
                $('#edit_product_button').attr('disabled',true);    
                return false;
            }else{
                    var form_data = $("#edit_product_form_id").serializeArray();
                        formdat = new FormData();                             
                        var form_attributes = new Array();
                       $('[name^="attribute_name"]').each(function()
                        {
                            var key = $(this).attr('name').split('-');
                            var val = $(this).val();
                            form_attributes.push({'attribute_id':key[1],'attribute_val':val});
                        });
                        $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('started', 'box2Load' , true);
                        att= JSON.stringify(form_attributes);
                        product_data= JSON.stringify(form_data);                              
                        $.ajax({
                            headers: {'X-CSRF-TOKEN': token},
                            type: 'POST',
                            url: '/products',
                            data:{product_data:product_data,att_data:att},
                            success:function(attRs){
                                $('#edit_product_button').attr('disabled',false); 
                                $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
					            if(attRs =='false2'){
					                alert("Failed to edit the product!");   
					            }
					            else if(attRs=='false1')
                                {
                                    alert("This Product Group already associated with bins. Please un-map bins respective product Group.");
                                }
                                else if(attRs=='false')
                                {
                                    alert("It you want change offer pack staus? Before, please remove freebie products in freebie configuration tab.");
                                }else
                                {
                                    if(attRs==1)
                                    {
                                        $("#addFreebie_model").show();
                                    }else
                                    {
                                        $("#addFreebie_model").hide();
                                    }
                                    alert("Successfully Saved Your Changes.");
                                }
                                
                            }

                        });
            }
        }
    });
});
$('#brand_id').change(function ()
{
    $.get("/product/getBrandProducts/" + $(this).val(), function(data) 
    {
        $('#get_products').html($("<option>").attr('value', '').text('Please Select'));
        $.each(data, function (k, v) 
        {
            $('#get_products').append($("<option>").attr('value', v.product_id).text(v.product_title));
        });
    });
});

//this is for show attributes with group
$.ajax({
    headers: {'X-CSRF-TOKEN': token},
    url: '/getAllAttributes/'+cat_id+'/'+product_id,
    type: 'POST',
    context: document.body,
    success: function(rs)
    {
        attRs= rs['att_group'];
        offer_pack = rs['offer_pack'];
        rs=rs['attribute_data'];
      /*  console.log(attRs);
        console.log(offer_pack);*/
            $.each(attRs, function (attname, attvalue) {
                att_code+='<div class="table-scrollable"><table class="table table-bordered table-advance"><thead><tr><th class="col-md-3"  colspan="2"><b>'+attvalue.name+'</b></th></tr></thead><tbody>';                                                                              
                    $.each(rs, function (name, value)
                    {
                        if(attvalue.attribute_group_id == value.attribute_group_id)
                        {
                           
                            if(value.attribute_code =='offer_pack')
                            {
                                att_code+= '<tr><input type="hidden" id="attributesetid" value="'+value.value+'"><td class="col-md-3">'+value.name+'</td><td><div class="col-md-6"><select class="form-control" name="attribute_name-'+value.attribute_id+'" id="attribute_name_q9_check"><option value="0">Please Select...</option>';
                                    if($("#permission_level").val() == 1)
                                    {
                                        if(value.value !=null){
                                            if(value.value == "Consumer Pack Inside"){
                                                att_code+= '<option value="Regular">Regular</option>                                            <option value="Consumer Pack Inside" selected>Consumer Pack Inside</option>                                            <option value="Consumer Pack Outside">Consumer Pack Outside</option>'; 
                                            }else if(value.value == "Regular"){
                                                 att_code+= '<option value="Regular" selected>Regular</option>                                            <option value="Consumer Pack Inside">Consumer Pack Inside</option>                                            <option value="Consumer Pack Outside">Consumer Pack Outside</option>'; 
                                            }else if(value.value == "Consumer Pack Outside"){
                                                 att_code+= '<option value="Regular">Regular</option>                                            <option value="Consumer Pack Inside">Consumer Pack Inside</option>                                            <option value="Consumer Pack Outside" selected>Consumer Pack Outside</option>'; 
                                            }
                                           
                                        }else{
                                            att_code+= '<option value="Regular">Regular</option>                                            <option value="Consumer Pack Inside">Consumer Pack Inside</option>                                            <option value="Consumer Pack Outside">Consumer Pack Outside</option>'; 
                                        }
                                       
                                    }else{
                                        $.each(offer_pack, function (offerName, offerValue)
                                        {
                                            if(offerValue.name=='freebie' || offerValue.name=='Consumer Pack Outside')
                                            {
                                                //if(check_is_sellable==1)
                                                {
                                                    $("#addFreebie_model").show();
                                                }                                           
                                            }
                                            if(value.value==offerValue.name)
                                            {
                                               att_code+= '<option value="'+offerValue.name+'" selected>'+offerValue.name+'</option>'; 
                                            }else
                                            {
                                                att_code+= '<option value="'+offerValue.name+'">'+offerValue.name+'</option>'; 
                                            }
                                        });
                                    }
                                    att_code+= '</select></div><div class="col-lg-1 pull-right text-center"></div> </td></tr>';
                            }
                            else
                            {
                                att_code+= buildAttView(value);       
                            }
                                                                         
                        }
                    }); 
                att_code+= '</tbody></table></div>';
            });
        
         $("#add_att_with_group").append(att_code);
         $('#kvi_name').trigger('change');
    }
});
$('[data-toggle="tooltip"]').tooltip();
$('#basic').on('hidden.bs.modal', function ()
{
    $('.modal-body').find('lable,input,textarea').val('').end();
    location.reload();
    var product_id = $('#product_id').val();
});
$('#brand_id').change(function ()
{
    $.get("/product/getBrandProducts/" + $(this).val(), function(data) 
    {
        $('#get_products').html($("<option>").attr('value', '').text('Please Select'));
        $.each(data, function (k, v) 
        {
            $('#get_products').append($("<option>").attr('value', v.product_id).text(v.product_title));
        });
    });
});

function buildAttView(attvalue)
{
    if(attvalue.attribute_id == 26){
        return '<tr><td class="col-md-3">'+attvalue.name+'</td><td><div class="col-md-6"><input type="text" name="attribute_name-'+attvalue.attribute_id+'" value="'+attvalue.value+'"  class="form-control"></div><div style="font-size: 13px;color: #0566ae;">Ex:- Buy 3 products get 1 product</div></td></tr>';
    }else{
        return '<tr><td class="col-md-3">'+attvalue.name+'</td><td><div class="col-md-6"><input type="text" name="attribute_name-'+attvalue.attribute_id+'" value="'+attvalue.value+'"  class="form-control"></div> <div class="col-lg-1 pull-right text-center"></div> </td></tr>'; 
    }
    
}

// $('#kvi_name').change(function(){
//     if($('#kvi_name').val()==69010){
      
//         if($('#attribute_name_q9_check').val()!='Freebie'){
//             alert('Q9 products should always be Freebie');
//             $('#edit_product_button').attr('disabled',true);    
//         }else{
//             $('#edit_product_button').attr('disabled',false);
//         }
//     }
//     // else{
//     //     if($('#attributesetid').val()==''){
//     //         $('#attribute_name_q9_check').val('Regular');
//     //     }else{
//     //         $('#attribute_name_q9_check').val($('#attributesetid').val());    
//     //     }
//     //     $('#attribute_name_q9_check').attr('disabled', false);
//     // }
    
// })

$(document).on('change','#kvi_name,#attribute_name_q9_check',function(){
  
        $('#edit_product_button').attr('disabled', false);                  
                   
});