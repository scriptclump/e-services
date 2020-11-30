$(document).ready(function(){ 
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/getManufacturersList',
            type: 'POST',                                            
            success: function (rs) 
            {
               
                $("#manufacturer_name").html(rs);
            }
        });
});
