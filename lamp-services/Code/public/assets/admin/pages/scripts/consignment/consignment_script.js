function getDocketOrders(docket,type) {

            
            $('#allDocket').html('');

            $.ajax({
                 headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
                 url: '/salesorders/getdocketdetails',
                 type: "POST",
                 data: {docket_no:docket,transfer_type:type},
                 dataType: 'json',
                  beforeSend: function () {
                     $('#loader1').show();
                  },
                  complete: function () {
                      $('#loader1').hide();
                  },
                 success: function (response) {
                   if (response.status == 200) {
                        $('#loader1').hide();
                        $('#allDocket').html('<tr><th><input type="checkbox" value="" onclick="checkAllContainer(this)"/></th><th>Crate Id</th><th>Order No</th><th>Weight(Kg)</th></tr>');  
                        if(response.data.length>0) {
                          $.each(response.data,function(key,val) {
                            var row = '<tr><td>'+val.chk+'</td><td>'+val.container_id+'</td><td>'+val.order_code+'</td><td>'+val.weight+'</td></tr>';
                            $('#allDocket').append(row);
                          });      
                        }

                   } else {
                      $('.loderholder').hide();
                   }
                      $('input[name="container[]"]').trigger('change');
                 },
                 error: function (response) {             }
             });
}

function checkAllContainer(ele) {
     var checkboxes = $('input[name="container[]"]');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
    $('input[name="container[]"]').trigger('change');
 }


 $(document).on('change','.alldock', function() {

    $('#pendingDocket,#scannedDocket,#partialDocket,#completedDocket').html('<tr><th><input type="checkbox" value=""/></th><th>Crate Id</th><th>Order No</th><th>Weight(Kg)</th></tr>');  

    $('input[name="container[]"]').each(function(){
        
        var checked = '';

        if($(this).is(':not(:checked)')) {

            $('#pendingDocket').append('<tr><th><input type="checkbox" class="nonall" row-count="'+$(this).attr('row-count')+'" value="'+$(this).attr('gds_order_id')+'"/></th><th>'+$(this).attr('container_id')+'</th><th>'+$(this).attr('order_code')+'</th><th>'+$(this).attr('weight')+'</th></tr>');

        } else {
            var checked = 'checked';
            $('#scannedDocket').append('<tr><th><input type="checkbox" checked class="nonall" row-count="'+$(this).attr('row-count')+'" value="'+$(this).attr('gds_order_id')+'"/></th><th>'+$(this).attr('container_id')+'</th><th>'+$(this).attr('order_code')+'</th><th>'+$(this).attr('weight')+'</th></tr>');
        }

        if($('input[name="container[]"][order_code="'+$(this).attr('order_code')+'"]:not(:checked)').length==0) {

            $('#completedDocket').append('<tr><th><input type="checkbox" class="nonall" '+checked+' row-count="'+$(this).attr('row-count')+'" value="'+$(this).attr('gds_order_id')+'"/></th><th>'+$(this).attr('container_id')+'</th><th>'+$(this).attr('order_code')+'</th><th>'+$(this).attr('weight')+'</th></tr>');

        } else if($('input[name="container[]"][order_code="'+$(this).attr('order_code')+'"]:not(:checked)').length>0 && $('input[name="container[]"][order_code="'+$(this).attr('order_code')+'"]:checked').length>0){
            $('#partialDocket').append('<tr><th><input type="checkbox" class="nonall partialDock" '+checked+' row-count="'+$(this).attr('row-count')+'" value="'+$(this).attr('gds_order_id')+'"/></th><th>'+$(this).attr('container_id')+'</th><th>'+$(this).attr('order_code')+'</th><th>'+$(this).attr('weight')+'</th></tr>');
        }

    });

 });

function getCompleteScannedOrders() {

    var completedDocket = [];

    $('input[name="container[]"]').each(function(){

        if($('input[name="container[]"][order_code="'+$(this).attr('order_code')+'"]:not(:checked)').length==0 && jQuery.inArray($(this).val(),completedDocket) == -1) {

            completedDocket.push($(this).val());
            
        }

    });

    return completedDocket;
}

$(document).on('change','.nonall', function(){

    if($(this).is(':checked')) {
        $('.alldock[row-count='+$(this).attr('row-count')+']').prop('checked', true);
    }else {
        $('.alldock[row-count='+$(this).attr('row-count')+']').prop('checked', false);
    }
    $('input[name="container[]"]').trigger('change');
});