$(document).ready(function(){
    
    $("#manufacturer_name").change(function()
    {
        var getManfId= $("#manufacturer_name").val(); 
        $("#getManfId").val(getManfId); 
        $("#getBrandId").val('');       
        brandDropDown();

    });
   brandDropDown();

function brandDropDown()
{
    var getBrandId="";
    var getBrandId= $("#getBrandId").val();

    if(getBrandId=="")
    {
        getBrandId=0;
    }
    var getManfId="";
    var getManfId= $("#getManfId").val();
    if(getManfId=="" || getManfId==0)
    {
        getManfId=01;
    }
    if($("#getManfId").length == 0)
    {
        getManfId=0;

    }
    if($("#getBrandId").length == 0)
    {
        getBrandId=0;

    }
    
    
    $("#showLoader").show();
     $.ajax({
             headers: { 
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/getBrandsList/'+getManfId,
            type: 'GET',                                             
            success: function (rs) 
            {
               
                $("#brand_id").html(rs);
                $("#brand_id2").html(rs);
                $("#brand_id3").html(rs);
                $("#brand_id4").html(rs);
                $("#brand_id").select2().select2('val',getBrandId);
                $("#brand_id2").select2().select2('val',getBrandId);
                $("#brand_id3").select2().select2('val',getBrandId);
                $("#brand_id4").select2().select2('val',getBrandId);
                $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
                $("#showLoader").hide();
                
                 
            }
        });
		
		
	    $.ajax({
             headers: { 
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/getwhlist',
            type: 'GET',                                             
            success: function (rs) 
            {
               var getWhId = '';
                $("#wh_list_id").html(rs);
                $("#wh_list_id2").html(rs);
                $("#wh_list_id3").html(rs);
                $("#wh_list_id4").html(rs);
                $("#wh_list_id").select2().select2('val',getWhId);
                $("#wh_list_id2").select2().select2('val',getWhId);
                $("#wh_list_id3").select2().select2('val',getWhId);
                $("#wh_list_id4").select2().select2('val',getWhId);
                $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
                $("#showLoader").hide();
                
                 
            }
        });

    $.ajax({
        headers: { 
               'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
           },
       url: '/getbintypelist',
       type: 'GET',                                             
       success: function (rs) 
       {
          var getWhId = '';
           $("#bin_type_id").html(rs);
           $("#bin_type_id2").html(rs);
           $("#bin_type_id3").html(rs);
           $("#bin_type_id4").html(rs);
           $("#bin_type_id").select2().select2('val',getWhId);
           $("#bin_type_id2").select2().select2('val',getWhId);
           $("#bin_type_id3").select2().select2('val',getWhId);
           $("#bin_type_id4").select2().select2('val',getWhId);
           $('body').spinnerQueue({showSpeed: 'fast', hideSpeed:'fast'}).spinnerQueue('finished', 'box2Load' , true);
           $("#showLoader").hide();


       }
   });		
}
});