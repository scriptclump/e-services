$(document).ready(function(){
   groupedProductList();
});
function groupedProductList(){
     $.ajax({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/getProductGroupList',
            type: 'GET',                                             
            success: function (rs) 
            {
                $("#product_group").html(rs);
                $("#product_group2").html(rs);
                $("#product_group3").html(rs);
                $("#product_group4").html(rs);
                $('.prod_class').css('color','#0174DF !important');
                var product_group_id=$("#product_group_id").val();
                $("#product_group").select2().select2('val',product_group_id);
            }
        });
}
