$(document).ready(function(){
    var getManfId= $("#getManfId").val();
    if(getManfId=="")
    {
        getManfId=0;
    }
    if($("#getManfId").length== 0)
    {
         getManfId=0;
    }
    $.ajax({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/getManufacturersList',
            type: 'GET',                                             
            success: function (rs) 
            {
               
                $("#manufacturer_name").html(rs);
                $("#manufacturer_name2").html(rs);
                $("#manufacturer_name3").html(rs);
                $("#manufacturer_name4").html(rs);
                $("#manufacturer_name").select2().select2('val',getManfId);
                $("#manufacturer_name2").select2().select2('val',getManfId);
                $("#manufacturer_name3").select2().select2('val',getManfId);
                 $("#manufacturer_name4").select2().select2('val',getManfId);

                
            }
        }); 
}); 
