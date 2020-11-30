$(document).ready(function(){
    $.ajax({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: '/bussinessunits',
            type: 'GET',                                             
            success: function (rs) 
            {
                $("#businessUnit1").html(rs);
                $("#businessUnit2").html(rs);
                $("#businessUnit3").html(rs);
                $("#businessUnit4").html(rs);
                 $("#businessUnit1").select2().select2('val','');
                 $("#businessUnit2").select2().select2('val','');
                 $("#businessUnit3").select2().select2('val','');
                 $("#businessUnit4").select2().select2('val','');
            }
        });
});
