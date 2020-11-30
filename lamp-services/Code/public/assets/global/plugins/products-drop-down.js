$(document).ready(function(){
    
    $("#brand_id").change(function()
    {
        
        var getBrandId= $("#brand_id").val(); 
        $("#getBrandId").val(getBrandId); 
        var parent_id = $("#parent_id").val();
    
        $("#showLoader").show();
         $.ajax({
                 headers: { 
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                url: '/getProductsList/'+getBrandId,
                data:'parent_id='+parent_id,
                type: 'GET',                                             
                success: function (rs) 
                {                 
                    $("#get_products").html(rs);
                    $("#get_products").select2().select2('val',0);
                    $("#showLoader").hide();                 
                }
            });

        });
   //productsDropDown();
});
