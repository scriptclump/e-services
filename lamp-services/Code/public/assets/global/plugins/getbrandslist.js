$(document).ready(function(){
    $.ajax({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/getCategoryList',
            type: 'GET',                                             
            success: function (rs) 
            
                $('.prod_class').css('color','#0174DF !important');
                $("#category_id").html(rs);
            }
        });
});
