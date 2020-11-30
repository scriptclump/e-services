$(document).ready(function(){
    $.ajax({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/getCategoryList',
            type: 'GET',                                             
            success: function (rs) 
            {
                $("#category").html(rs);
                $("#category2").html(rs);
                $("#category3").html(rs);
                $("#category4").html(rs);
                $('.prod_class').css('color','#0174DF !important');
                 $("#category").select2().select2('val',0);
                 $("#category2").select2().select2('val',0);
                 $("#category3").select2().select2('val',0);
                 $("#category4").select2().select2('val',0);
            }
        });
});
