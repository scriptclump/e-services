    $('#title').val($('#product_title').val());     
    $('#freeBieProduct_id').select2();
    $('#freebie_warehouse_id').select2();
    $('#freebie_state_id').select2();                
    $('#freebie_start_date').datepicker();
    $('#freebie_end_date').datepicker();
     var dateFormat = "mm/dd/yy",
                  from = $( "#freebie_start_date" ).datepicker({
                      //defaultDate: "+1w",
                        changeMonth: true,          
                      }).on( "change", function() {
                        to.datepicker( "option", "minDate", getDate( this ) );
                      }),
                  to = $( "#freebie_end_date" ).datepicker({
                        //defaultDate: "+1w",
                        changeMonth: true,        
                      }).on( "change", function() {
                        from.datepicker( "option", "maxDate", getDate( this ) );
                      });
                 function getDate( element ) {
                    var date;
                    try {
                      date = $.datepicker.parseDate( dateFormat, element.value );
                    } catch( error ) {
                      date = null;
                    } 
                  return date;
                }
    token = $("#csrf-token").val();
    var html_code="";
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/product/getAllProducts/'+$("#product_id").val(),
            type: 'POST',
            success: function (rs)
            {
                $.each(rs, function (name, value) {
                    html_code+= "<option value='"+value.product_id+"'>"+value.product_title+" ("+value.sku+") </option>";
                });                                    
                $("#freeBieProduct_id").append(html_code);
            }
        });
    $("#freebie_state_id").change(function()
    {
        var warehouse_code="<option value=''>Please select ...</option>";
        $('#freebie_warehouse_id')
        .find('option')
        .remove()
        .end()
        .append('<option value="">Please select ...</option>')
        .val('');
        var state_id= $("#freebie_state_id").val();
        $.ajax({
        headers: {'X-CSRF-TOKEN': token},
                url: '/products/getAllWareHouse/'+state_id,
                type: 'POST',
                success: function (rs)
                {
                    if(rs!="")
                    {
                       $.each(rs, function (name, value) {
                          warehouse_code+= "<option value='"+value.le_wh_id+"'>"+value.lp_wh_name+"</option>";
                       });                                    
                        $("#freebie_warehouse_id").html(warehouse_code);
                    }
                }
        });
    });
    $("#enable_stock_limit").change(function()
    {
       var status=$("#enable_stock_limit").prop('checked');
       if(status== true)
        {            
            $('#freebie_stock_limit').attr('disabled', false);
        }
        if(status== false)
        {
            $('#freebie_stock_limit').attr('disabled', true);
        }
    });
    $("#addFreebie_model").on('click', function(e)
    {
               
        $('form[id="freebie_configuration"]')[0].reset();
        $('#freebie_warehouse_id').find('option')
                                .remove()
                                .end()
                                .append('<option value="">Please select ...</option>')
                                .val('');
        if (e.originalEvent !== undefined)
        {
            $("#freeBieProduct_id").select2().select2('val', '');
            $("#freebie_warehouse_id").select2().select2('val','');
            $("#freebie_id").val('');
            $('#freebie_warehouse_id')
                    .find('option')
                    .remove()
                    .end()
                    .append('<option value="">Please select ...</option>')
                    .val('');
            $('#freebie_configuration')[0].reset();
            $("#freebie_state_id").select2().select2('val','');
        }
    });
    function editFreebieProduct(freebie_id)
    {

        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/editFreebieConfiguration/' + freebie_id,
            processData: false,
            contentType: false,
            success: function (rs)
            {

                $("#addFreebie_model").click();
                $("#freebie_id").val(freebie_id);

                //var obj = jQuery.part(rs);

                $("#freebie_mpq").val(rs[0].mpq);
                //console.log(obj.value);
                ///$("#freeBieProduct_id").val(rs.free_prd_id);
                $("#freeBieQty").val(rs[0].qty);
                $("#freeBieProduct_id").select2().select2('val', rs[0].free_prd_id);
                var enable_stock_limit= rs[0].is_stock_limit;
                if(enable_stock_limit==1)
                {
                    $('#enable_stock_limit').prop('checked', true);
                     $('#freebie_stock_limit').attr('disabled', false);
                }else
                {
                     $('#freebie_stock_limit').attr('disabled', true);
                }
                 $("textarea#freebie_product_description").val(rs[0].freebee_desc);
                
              //  $("#freeBieQty").val(rs[0].stock_limit);
                $("#freebie_state_id").select2().select2('val', rs[0].state_id);
                var state_id= rs[0].state_id;
               
                $('#freebie_warehouse_id')
                .find('option')
                .remove()
                .end()
                .append('<option value="">Please select ...</option>')
                .val('');
                var warehouse_code="";
                $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                        url: '/products/getAllWareHouse/'+state_id,
                        type: 'POST',
                        success: function (ajaxRs)
                        {
                            if(ajaxRs!="")
                            {
                               $.each(ajaxRs, function (name, value) {
                               
                                 warehouse_code+= "<option value='"+value.le_wh_id+"'>"+value.lp_wh_name+"</option>";
                                
                               });                                    
                                $("#freebie_warehouse_id").html(warehouse_code);
                            }
                            $("#freebie_warehouse_id").select2().select2('val', rs[0].le_wh_id);
                        }
                });
               
              //  $("#freebie_warehouse_id").val(rs[0].length);
                $("#freebie_start_date").val(rs[0].start_date);
                 $("#freebie_end_date").val(rs[0].end_date);
                $("#freebie_stock_limit").val(rs[0].stock_limit);
            
            }
        });        
    }     
    function deleteFreebieProduct(freebie_id)
    {
        if (confirm('Do You want Delete This Freebie Product ...?'))
        {
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/deleteFreebieProduct/' + freebie_id,
            async: false,
            type: 'POST',
            success: function (rs)
            {
            alert("Successfully Deleted.");
             $("#freeBieConfigGrid").igHierarchicalGrid({"dataSource":'/freeBieProducts/'+$('#product_id').val()});
            }
        });
        }
    }