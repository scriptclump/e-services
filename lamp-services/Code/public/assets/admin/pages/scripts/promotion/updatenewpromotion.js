var inData = new Array();
inData = $('#applied_ids').val().split(',');

var calltype = new Array();
calltype = $("#gridCallType").val().split(',');

function myFunc(){
    if(calltype!="Bill"){
        $("#product_grid").igGrid({
            dataSource: "/promotions/productgrid?calltype="+calltype+"&notindata="+inData
        });
    }
}

// show-hide condition box as per the condition selection
function changetextbox(select){
    if (select.value == 'Range') {  
        $('.first_condition').show();
        $('.second_condition').show();
        $('.offer_value').show();
    } else {
        $('.first_condition').show();
        $('.offer_value').show();
        $('.second_condition').hide();
        $('#value_two').val("");
    }
}
function NotAllowKey(event){
        if ( event.keyCode != 8){ 
            event.preventDefault();
        }    
}


function allowNumber(event){
    if (event.shiftKey == true) {
        event.preventDefault();
    }
    if ((event.keyCode >= 48 && event.keyCode <= 57) || 
        (event.keyCode >= 96 && event.keyCode <= 105) || 
        event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
        event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {
    } else {
        event.preventDefault();
    }
    if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
        event.preventDefault(); 
}

function NotAllowKey(event){
        if ( event.keyCode != 8){ 
            event.preventDefault();
        }    
}

$("#value_two").keydown(function (event) {
        allowNumber(event);
});

$("#update_bill_value").keydown(function (event) {
        allowNumber(event);
});

$("#discount_offer").keydown(function (event) {
    allowNumber(event);
});

$("#offer_value").keydown(function (event) {
        allowNumber(event);
});
$("#prmt_lock_qty").keydown(function (event) {
    allowNumber(event); 
});
$("#update_from_cashback").keydown(function (event) {
    allowNumber(event); 
});
$("#update_to_cashback").keydown(function (event) {
    allowNumber(event); 
});
$("#discount_offer_cashback").keydown(function (event) {
    allowNumber(event); 
});
$("#pack_value_update").keydown(function (event){
    NotAllowKey(event);
});

function changeOffer(select)
{
    if (select.value == 'FreeQty') {  
        $('.for_free_product').show();
        $('.for_discount').hide(); 
    } else {
        $('.for_free_product').hide();
        $('.for_discount').show();
    }
}  

$( document ).ready(function() {


    $('#offon_percent_cashback').prop('checked',false).uniform('refresh');   

    window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});

    // assign the data
    var prmt_id = $("#select_offer_tmpl").val();
    console.log(prmt_id);

    // condition for slab promotion
    if(prmt_id == '1'){
        $('.second_condition').show();
        $('.offer_value').show();
        $('.slab_rates').show();
        $('#condition').val("Range");
        $('#condition').attr('disabled', true)
        $('.plus-icon').show();
        $('.discount_ProductCondition').hide();
        $('.condition_container').show();
        $('.DiscountOnTotalBill').hide();

        // remove the Multiple option for SlabBalsed
        $('#state').removeAttr('multiple');
        $('#customer_group').removeAttr('multiple');
        $('.tradeCashbackSection').hide();
    }else if(prmt_id=='2' || prmt_id=='3'){
        if($('#condition').val()=='FreeQty'){
            $('.for_free_product').show();
            $('.for_discount').hide(); 
            $('.DiscountOnTotalBill').hide();
            $('.tradeCashbackSection').hide();
    }else if(prmt_id=='4'){
            $('.for_free_product').hide();
            $('.for_discountbill').show();
            $('.for_discount').show(); 
            $('.DiscountOnTotalBill').show();
            $('.tradeCashbackSection').hide();

        }
    else{
        $('.for_free_product').hide();
        $('.for_discount').show();
    }
    }else if(prmt_id == '6'){

        $('.second_condition').hide();
        $('.offer_value').hide();
        $('.slab_rates').hide();
        $('.plus-icon').hide();
        $('.discount_ProductCondition').hide();
        $('.condition_container').hide();
        $('.DiscountOnTotalBill').hide();
        $('.freeSampleSection').show();
        $('.tradeCashbackSection').hide();

    }else if(prmt_id == '7'){
        console.log('7');
        $('.second_condition').hide();
        $('.offer_value').hide();
        $('.slab_rates').hide();
        $('.plus-icon').hide();
        $('.discount_ProductCondition').hide();
        $('.condition_container').hide();
        $('.DiscountOnTotalBill').hide();
        $('.freeSampleSection').hide();
        $('.tradeCashbackSection').show();

    }

    // show-hide ItemGrid part along with Data
    if(calltype!='Bill'){
        $("#product_grid").igGrid({
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [     
                {headerText: "item_id", key: "item_id", dataType: "number", width: "10%",hidden:true},
                { headerText: "Assign Promotion to the List", key: "list_details", dataType: "string", width: "100%",template: ""},
                
            ],
            jQueryTemplating: true,
            dataSource: '/promotions/productgrid?calltype='+calltype+'&notindata='+inData,
            responseDataKey: "results",
            renderCheckboxes: true,
            features: [
                {
                    name: "RowSelectors",
                    enableCheckBoxes: true,
                    enableRowNumbering: false,
                },
                {
                    name: 'Selection',
                    multipleSelection: true,
                    mode: "row",
                },
                {
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 20,
                    name: 'AppendRowsOnDemand',
                    loadTrigger: 'auto', 
                    type: 'remote'
                },

                {
                    name: "Filtering",
                    type: "remote",
                    mode: "simple",
                    filterDialogContainment: "window",
                    dataFiltering: myFunc,
                    columnSettings: [
                        {columnKey: 'image', allowFiltering: false},
                    ]
                },

                {
                    name: 'Sorting',
                    
                },

            ],
            primaryKey: 'item_id',
            width: '100%',
            height: '500px',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
        });
    }else{
        $('#gridSection').hide();
    }
        var num=0;
    $("#slabrate_validate").click(function () {
        $('#no_slab_span').html('');
        var value_two = $('#value_two').val();
        var offer_value = $('#offer_value').val();
         num=num+1;
        // check for old Qty
        var oldFlag = 0;
        $('.cust-error-no-tr-lines').html("");

        var star_code=$("#pack_number_update option:selected").attr("star_code");
        var product_star_color= $("#pack_number_update option:selected").attr("prd_star_color");
        var pack_level=$("#pack_number_update option:selected").attr("pack_level");
        var pack_number_update=$('#pack_number_update option:selected').html();
        var pack_value_update=$("#pack_value_update").val();


        $('#slab_table tbody tr').each(function() {


            var PackConfig = $(this).find("td:eq(0) input[id='pack_number_update']").val();
            var EsuConfig = $(this).find("td:eq(2) input[id='pack_value_update']").val();
           
           
            if(PackConfig==pack_level&& EsuConfig==pack_value_update){
                oldFlag=1;  
                 $(".error_messege").html('check slab Maximum Quantity');
            }
        });

        if(value_two !='' & offer_value!='' & oldFlag==0){
           

            $(".error_messege").html('');
           

            var condition_tr = '<tr class="gradeXSlab odd list-head">\
            <td data-val="cond_to">'+pack_number_update+'<input type="hidden" value="'+pack_level+'" id="pack_number_update" name="pack_number_update[]" class="form-control" readonly></td>\
              <td data-val="cond_to"><span class="fa fa-star" id="product_star_color_table'+num+'" name="product_star_color_table'+num+'" aria-hidden="true" style="font-size: 15px;"></span><input type="hidden" value="'+star_code+'" id="product_star_color_table'+num+'" name="product_star_color_table[]" ></td>\
              <td data-val="cond_to">'+pack_value_update+'<input type="hidden" value="'+pack_value_update+'" id="pack_value_update" name="pack_value_update[]" class="form-control" readonly></td>\
              <td data-val="cond_to">'+value_two+'<input type="hidden" value="'+value_two+'" id="value_two" name="value_two[]" class="form-control" readonly></td>\
              <td data-val="offer_value">'+offer_value+'<input type="hidden" value="'+offer_value+'" id="offer_value" name="offer_value[]" class="form-control" readonly></td>\
              <td><a href="" class="btn btn-icon-only default delcondition"><i class="fa fa-trash-o"></i></a></td>\
            </tr>';
            $('#slab_table').append(condition_tr);

        $("#product_star_color_table"+num+"").css("color",product_star_color);
            var secondVal = $('#value_two').val();
            secondVal = parseInt(secondVal)+1;
            $('#value_one').val(secondVal);
            $('#value_one').attr('readonly', true);
            $('#value_two').val('');
            $('#offer_value').val('');
            $('#pack_number_update').val('');
            $('#pack_value_update').val('');
            $("#product_star_color").css("color",'');

        }
        
    });

    // set default dates
    var fromData = $("#start_date").val().split("-");
    var start = new Date(fromData);
    var end = new Date(new Date().setYear(start.getFullYear() + 1));

    $('#start_date').datepicker({
        endDate: end,
        autoclose: true,
        format: 'yyyy-mm-dd'
    }).on('changeDate', function () {
        stDate = new Date($(this).val());
        $('#frm_add_new_tmpl').bootstrapValidator('revalidateField', 'start_date');
        $('#end_date').val('');
        $('#end_date').datepicker('setStartDate', stDate);
    });

    $('#end_date').datepicker({
        startDate: start,
        endDate: end,
        autoclose: true,
        format: 'yyyy-mm-dd'
    }).on('changeDate', function () {
        $('#frm_add_new_tmpl').bootstrapValidator('revalidateField', 'end_date');
        $('#start_date').datepicker('setEndDate', new Date($(this).val()));
    });

    $("#click_Sunday").click(function(){
        if($("#click_Sunday").is(':checked'))
            $("#show_Sunday").css({"display":"block"});
        else
             $("#show_Sunday").css({"display":"none"});
    });
    
    $("#click_Monday").click(function(){
        if($("#click_Monday").is(':checked'))
            $("#show_Monday").css({"display":"block"});
        else
             $("#show_Monday").css({"display":"none"});
    });
    $("#click_Tuesday").click(function(){
        if($("#click_Tuesday").is(':checked'))
            $("#show_Tuesday").css({"display":"block"});
        else
             $("#show_Tuesday").css({"display":"none"});
    });
    $("#click_Wednesday").click(function(){
        if($("#click_Wednesday").is(':checked'))
            $("#show_Wednesday").css({"display":"block"});
        else
             $("#show_Wednesday").css({"display":"none"});
    });
    $("#click_Thursday").click(function(){
        if($("#click_Thursday").is(':checked'))
            $("#show_Thursday").css({"display":"block"});
        else
             $("#show_Thursday").css({"display":"none"});
    });
    $("#click_Friday").click(function(){
        if($("#click_Friday").is(':checked'))
            $("#show_Friday").css({"display":"block"});
        else
             $("#show_Friday").css({"display":"none"});
    });


    $("#click_Saturday").click(function(){
        if($("#click_Saturday").is(':checked'))
            $("#show_Saturday").css({"display":"block"});
        else
             $("#show_Saturday").css({"display":"none"});
    });


    // show / hide the select all time box
    $("#all_days").click(function(){
        if($("#all_days").is(':checked')){
            $("#select_all_down_button").hide();

            $("#select_to").show();
            $("#select_all").show();


        }else{
             $("#select_all_down_button").show();

            $("#select_to").hide();
            $("#select_all").hide();

         }
    });

    //dropdown search for products
    $( "#product_srch" ).autocomplete({
        minLength:2,
        source: '/promotions/getfreeproductlist',  
        select: function (event, ui) {
            var label = ui.item.label;
            var sku = ui.item.sku;
            var product_id = ui.item.product_id;
            $('#free_product_id').val(product_id);
        }
    });

    $('#frm_update_new_tmpl').formValidation({
        message: 'This value is not valid',
        icon: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            promotion_name: {
                message: 'Promotion Name is required',
                validators: {
                    notEmpty: {
                        message: 'Promotion Name is required'
                    },
                stringLength: {
                    min: 6,
                    max: 100,
                    message: 'Promotion Name is required must be more than 6 and less than 30 characters long'
                },
            }
        },
            select_offer_tmpl: {
                validators: {
                    notEmpty: {
                        message: 'Select offer is required'
                    }
                }
            },
            'warehouse_details[]':{
                validators:{
                    notEmpty:{
                        message:'please select warehouse'
                    }
                } 
            },
            'state[]': {
                validators: {
                    notEmpty: {
                        message: 'Please Select the Business location'
                    }
                }
            },

            'customer_group[]': {
                validators: {
                    notEmpty: {
                        message: 'Please Select the Customer Group'
                    }
                }
            },
            condition: {
                validators: {
                    notEmpty: {
                        message: 'please select offer promotion'
                    }
                }
            },

            value_one: {
                validators: {
                    numeric: {
                        message: 'Only number allowed.'
                    },
                }
            },
            value_two: {
                validators: {
                    numeric: {
                        message: 'Only number allowed.'
                    },
                }
            },
            offer_value: {
                validators: {
                    numeric: {
                        message: 'Only number allowed.'
                    },
                }
            },

            prmt_lock_qty: {
                validators: {
                    numeric: {
                        message: 'Please enter numeric value'
                    },
                }
            },
            'offertypeman[]':{
                validators: {
                    notEmpty: {
                        message: 'Please select manufacturer'
                    }
                }
            },
            'offertypebrand[]':{
                validators: {
                    notEmpty: {
                        message: 'Please select manufacturer'
                    }
                }
            },
            update_bill_value: {
                validators: {
                    notEmpty: {
                        message: 'Please enter bill value'
                    }
                }
            },
            discount_offer_on_bill: {
                validators: {
                    notEmpty: {
                        message: 'Please enter discount'
                    }
                }
            },   
            discount_offer_on_billvalue:{
                validators: {
                    notEmpty: {
                        message: 'Please enter discount'
                    }
                }
            },
           
           
            update_from:{
                validators: {
                    notEmpty: {
                        message: 'Please enter value'
                    }
                }
            },
             update_to:{
                validators: {
                    notEmpty: {
                        message: 'Please enter value'
                    }
                }
            },
            discount_offer:{
                 validators: {
                    notEmpty: {
                        message: 'Please enter value'
                    }
                }
            },   
            'ProductStar_on_bill_update[]':{
                 validators: {
                    notEmpty: {
                        message: 'Please Select product star'
                    }
                }
            },

            update_freeqty_description:{
                validators:{
                    notEmpty:{
                        message:'Description is required'
                    }
                }
            },
            'update_wareHouseId[]':{
                validators: {
                    notEmpty: {
                        message: 'Please Select the brand'
                    }
                }
            },
            'update_freeqty_from':{
                validators: {
                    notEmpty: {
                        message: 'Please Select the from'
                    }
                }
            },
            'update_freeqty_to':{
                validators: {
                    notEmpty: {
                        message: 'Please Select the to range'
                    }
                }
            },
            update_freeqty_product_id:{
                validators: {
                    notEmpty: {
                        message: 'Please Select the product'
                    }
                }
            },
            update_product_quantity:{
                validators: {
                    notEmpty: {
                        message: 'Please Select the product qty'
                    }
                }
            },
            update_freeqty_pack:{
                validators: {
                    notEmpty: {
                        message: 'Please Select the Pack'
                    }
                }
            },
            update_trade_type:{
                validators:{
                    notEmpty:{
                        message: 'Please select trade type'
                    }
                }
            },
            /*'update_trade_warehouse[]':{
                validators:{
                    notEmpty:{
                        message:'Please select warehouse type'
                    }
                }
            },*/
            'update_promotion_on[]':{
                validators:{
                    notEmpty:{
                        message:'Please select Promotion on'
                    }
                }
            },
            'update_pack_type[]':{
                validators:{
                    notEmpty:{
                        message:'Please select Pack type'
                    }
                }
            },
            update_trade_from_range:{
                validators:{
                    notEmpty:{
                        message:'Please Enter from range'
                    }
                }
            },
            update_trade_to_range:{
                validators:{
                    notEmpty:{
                        message:'Please Enter to range'
                    }
                }
            },
            update_tradeoffer_on_bill:{
                validators:{
                    notEmpty:{
                        message:'Please Enter discount'
                    },
                    numeric:{
                        message: 'Please enter numeric value'
                    }
                }
            },
            update_tradeoffer_type:{
                validators:{
                    notEmpty:{
                        message:'Please select offer type'
                    }
                }
            }


        }
    })
.on('success.form.fv', function(e){

        e.preventDefault();
        fv = $(e.target).data('formValidation');
        fv.disableSubmitButtons(false);
        if($('#slab_table').val()===null){
            $('#no_slab_span').html("Please Select Slab Data");
            $('#frm_update_new_tmpl').formValidation('revalidateField', 'promotion_name');
        }else if ($('#select_offer_tmpl').val()==1 && $('#slab_table tr').length<=1){
            $('.cust-error-no-tr-lines').html('Please add slab details below!');
            
            return false;
        }else if($('#select_offer_tmpl').val()==5 && $('#cashback_table_update tr').length<=1 ){
            $('.cust-error-update_lines').html('Please add Cashback details below!');
            return false;
        }else if($('#select_offer_tmpl').val()==7){
            let trade_to_range=$('#update_trade_to_range').val();
            let trade_from_range=$('#update_trade_from_range').val();
            if( trade_to_range.trim()=='' || parseFloat(trade_to_range.trim())==0 || parseFloat(trade_to_range.trim()) < parseFloat(trade_from_range.trim()) ){
                $('.cust-error-trade_to_range').html("Not a valid Input!");
            }else{
                fv.defaultSubmit();
            }
        }else{
           
            fv.defaultSubmit();
        }
    }); 

    var prod_tr_with_qty = '<tr class="gradeX odd">\
              <td data-val="list_details"></td>\
              <td style = "display:none;"><input type="hidden" data_item_id="item_id" class="form-control input-sm" value="" id="item_id" name = "item_id[]"></td>\
              <td><input type="text"  class="form-control input-sm" value="1" id="product_qty" name ="product_qty[]"></td>\
              <td><a href="" class="btn btn-icon-only default delList"><i class="fa fa-trash-o"></i></a></td>\
        </tr>';

    var prod_tr = '<tr class="gradeX odd">\
              <td data-val="list_details"></td>\
              <td style = "display:none;"><input type="hidden" data_item_id="item_id" class="form-control input-sm" value="" id="item_id" name = "item_id[]"></td>\
              <td><a href="" class="btn btn-icon-only default delList"><i class="fa fa-trash-o"></i></a></td>\
        </tr>';


        $("#value_two").keydown(function (event) {
            allowNumber(event);
        });

        $("#offer_value").keydown(function (event) {
            allowNumber(event); 
        });  
        $("#discount_offer_on_billvalue").keydown(function (event) {
            allowNumber(event); 
        });    

    $('#add_product_table').on('click', '.delList', function(e){
        e.preventDefault();
        var removeItem = $(this).closest('td').siblings(':nth-child(2)').find('input[data_item_id]').val();
        inData = jQuery.grep(inData, function(value) {
            return value != removeItem;
        });
        $(this).closest('tr').remove();
        $('#pack_number_update').html('');
        $("#slab_table tbody tr").remove(); 
        $('#pack_value_update').val('');
        $("#product_star_color").css("color",'');
        $('#value_two').val('');
        $('#offer_value').val('');
    });

    $('.moveLeft').click(function(e){

        var promotionType = $("#select_offer_tmpl").val();
        
        // Reject multiple selection for Slab Based
        if( promotionType==1 && (typeof $('#item_id').val()!='undefined') && $('#item_id').val()!==null ) {
            return alert("One product could be selected for Slab Based Promotion!");   
        }

        e.preventDefault();
        var rows = '';
        rows = $("#product_grid").igGridSelection('selectedRows');
        var dataview = $("#product_grid").data('igGrid').dataSource.dataView();
        var gridToDelete = $("#product_grid").data("igGrid");

        // Reject multiple selection for Slab Based
        if( promotionType==1 && rows.length >1 ){
            return alert("One product could be selected for Slab Based Promotion!");
        }

        for (i = 0; i < rows.length; i++) {
            var tr = $(this).closest('tr');
            var data = {};
            var item_id = dataview[rows[i].index]["item_id"];
            $('#product_id').val(item_id);
            $("#value_two").val();
            $("#pack_value_update").val();
            getPack(item_id);

            // push the Product ID in the global array
            inData.push(item_id);
            data.list_details = dataview[rows[i].index]["list_details"];

            var new_tr = $(prod_tr);
            if(promotionType==1){
                new_tr = $(prod_tr);
            }else{
                new_tr = $(prod_tr_with_qty);
            }
            
            var sNo = $('#add_product_table').find('tbody').find('tr:not(".list-head")').length + 1;

            new_tr.find('input[data_item_id]').val(item_id);

            new_tr.find('td[data-val]').each(function () {
                var index = $(this).data('val');
                $(this).html(data[index] || '');
            });

            new_tr.data('html', tr.html());
                $('#add_product_table').append(new_tr);
                $("#product_grid").igGridSelection("clearSelection", rows[i].index);

            gridToDelete.dataSource.deleteRow(item_id);
        }

        gridToDelete.commit();
        myFunc();
    });

    $('#slab_table').on('click', '.delcondition', function(e){
       e.preventDefault();
        var removeItem = $(this).closest('td').siblings(':nth-child(2)').find('input[value_one]').val();
        $(this).closest('tr').remove();
    });

    $('#cashback_table_update').on('click', '.delconditionforcashback', function(e){
       e.preventDefault();
        $(this).closest('tr').remove();
    });

    $('#adding_cashback_update').click(function(e){

        var desc = $('#update_cashback_description').val();
        var offertypemanf = $('#offertypemanf_cashback option:selected').text()=='--Please Select--' ? '--' : $('#offertypemanf_cashback option:selected').text();
        var offertypbrand = $('#offertypbrand_cashback option:selected').text()=='--Please Select--' ? '--' : $('#offertypbrand_cashback option:selected').text();
        var offertypexclbrand = $('#update_excludebrand option:selected').text()=='--Please Select--' ? '--' : $('#update_excludebrand option:selected').text();
        var prdgrp = $('#offertyp_prdgrp option:selected').text()=='--Please Select--' ? '--' : $('#offertyp_prdgrp option:selected').text();
        var ProductStar = $('#Product_star option:selected').text()=='--Please Select--' ? '--' : $('#Product_star option:selected').text();
        var Benificiary = $('#benificiary_update option:selected').text()=='--Please Select--' ? '--' : $('#benificiary_update option:selected').text();
        //var wareHouseId = $('#wareHouseId_update option:selected').text()=='--Please Select--' ? '--' : $('#wareHouseId_update option:selected').text();
        var wareHouseId = $('#warehouse_details option:selected').text()=='--Please Select--' ? '--' : $('#warehouse_details option:selected').text();
        var leWhIds = $('#warehouse_details').val()=='' ? '' : $('#warehouse_details').val();
        var cash_back_from = $('#update_from_cashback').val();
        var cash_back_to = $('#update_to_cashback').val();
        var discount_offer_on_bill = $('#discount_offer_cashback').val();
        
        var offon_percent = $('#offon_percent_cashback').is(":checked") ? '1' : '0';
        var offon_percent_txt = $('#offon_percent_cashback').is(":checked") ? '%' : '&#8377;';

        var customer_group = $('#customer_group option:selected').text();
        var state = $('#state option:selected').text();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var caplimit=$('#cap_limit').val();
        var productvalue=$('#product_value').val();
        var ordertype=$('#order_type').val();

         $('.cust-error-cash_back_from_update').html("");
        $('.cust-error-cash_back_to_update').html("");
        $('.cust-error-update_lines').html("");
        $('.cust-error-discount_update').html("");
        $('.cust-error-ProductStar_on_bill_update').html("");
        $('.cust-error-update_cashback_description').html("");

         var AddFlag = 0;

        if(cash_back_from.trim()==''){
            console.log('fromdate');
            $('.cust-error-cash_back_from_update').html("Can't be empty!");
            AddFlag=1;
        }
        if( cash_back_to.trim()=='' || parseFloat(cash_back_to.trim())==0 || parseFloat(cash_back_to.trim()) < parseFloat(cash_back_from.trim() )  ){
            
            console.log('toomdate');
            $('.cust-error-cash_back_to_update').html("Not a valid Input!");
            AddFlag=1;
        }
        if(discount_offer_on_bill.trim()=='' || parseFloat(discount_offer_on_bill.trim())==0){
            $('.cust-error-discount_update').html("Can't be empty or '0'!");
            AddFlag=1;
        }
         if($('#Product_star').val() == ''){
            console.log('Product_star');
            $('.cust-error-ProductStar_on_bill_update').html("Can't be empty!");
            AddFlag=1;
        }
        if(desc.trim()==''){
            console.log('update_cashback_description');
             $('.cust-error-update_cashback_description').html("Can't be empty!");
            AddFlag=1;
        }
        if(caplimit.trim()==''){
                        console.log('cash_cap_limit');

            $('.cust-error-cash_cap_limit').html("Can't be empty!");
            AddFlag=1; 
        }
        if(productvalue.trim()==''){
                        console.log('cash_product_value');
            $('.cust-error-cash_product_value').html("Can't be empty!");
            AddFlag=1; 
        }
        if(ordertype.trim()==''){
                                    console.log('cash_order_type');

            $('.cust-error-cash_order_type').html("Can't be empty!");
            AddFlag=1; 
        }

        var benificiary_val=$('#benificiary_update').val();
        if(benificiary_val.trim()==''){
                                    console.log('benificiary');
            $('.cust-error-cash-benificiary').html("Can't be empty!");
            AddFlag=1; 
        }
        var brand_val=$('#offertypbrand_cashback').val();
        if((brand_val=='' || brand_val==null) && (benificiary_val!=53)){
                                    console.log('cash-brand');
            $('.cust-error-cash-brand').html("Can't be empty!");
            AddFlag=1; 
        }
        var manuf_val=$('#offertypemanf_cashback').val();
        if((manuf_val=='' || manuf_val== null) && (benificiary_val!=53)){
                                                console.log('cash-manuf');

            $('.cust-error-cash-manuf').html("Can't be empty!");
            AddFlag=1; 
        }
        var prod_grp=$('#offertyp_prdgrp').val();
        if((prod_grp=='' || prod_grp==null)  && (benificiary_val==53)){
            console.log('cash-manf');
            $('.cust-error-ProductGRP').html("Can't be empty!");
            AddFlag=1; 
        }
        if(start_date=='' || start_date==null){

         $('.cust-error-start-date').html("Can't be empty!");
            AddFlag=1;   
        }
        if(end_date=='' || end_date==null){
         $('.cust-error-to-date').html("Can't be empty!");
            AddFlag=1;   
        }
        if($('#warehouse_details').val()=='' || $('#warehouse_details').val()==null){
                                                console.log('cash-warehouse');
            $('.cust-error-warehouse').html("Can't be empty!");
            AddFlag=1; 

        }
        var WareHouseName = $('#warehouse_details option:selected').toArray().map(item => item.text).join();

        if(AddFlag==0){

        var condition_tr = '<tr class="gradeXSlab odd list-head">\
          <td><input type="hidden" value="'+$('#state').val()+'" id="state_table_update" name="state_table_update[]" class="form-control">'+state+'</td>\
          <td><input type="hidden" value="'+$('#customer_group').val()+'" id="customer_group_table_update" name="customer_group_table_update[]" class="form-control">'+customer_group+'</td>\
          <td><input type="hidden" value="'+$('#update_cashback_description').val()+'" id="description_table_update" name="description_table_update[]" class="form-control">'+desc+'</td>\
          <td><input type="hidden" value="'+$('#offertypemanf_cashback').val()+'" id="offertypemanf_table_update" name="offertypemanf_table_update[]" class="form-control">'+offertypemanf+'</td>\
          <td><input type="hidden" value="'+$('#offertypbrand_cashback').val()+'" id="offertypbrand_table_update" name="offertypbrand_table_update[]" class="form-control">'+offertypbrand+'</td>\
          <td><input type="hidden" value="'+$('#update_excludebrand').val()+'" id="offertypexclbrand_table_update" name="offertypexclbrand_table_update[]" class="form-control">'+offertypexclbrand+'</td><input type="hidden" value="'+$('#offertyp_prdgrp').val()+'" id="prdgrp_tbl" name="prdgrp_tbl[]" class="form-control">'+prdgrp+'</td>\
          <td><input type="hidden" value="'+$('#benificiary_update').val()+'" id="Benificiary_table_update" name="Benificiary_table_update[]" class="form-control">'+Benificiary+'</td>\
          <td><input type="hidden" value="'+$('#Product_star').val()+'" id="ProductStar_table_update" name="ProductStar_table_update[]" class="form-control">'+ProductStar+'</td>\
          <td><input type="hidden" value="'+leWhIds+'" id="wareHouseId_table_update" name="wareHouseId_table_update[]" class="form-control">'+WareHouseName+'</td>\
          <td><input type="text" value="'+cash_back_from+'" id="cash_back_from_table_update" name="cash_back_from_table_update[]" class="form-control" readonly></td>\
          <td><input type="text" value="'+cash_back_to+'" id="cash_back_to_table_update" name="cash_back_to_table_update[]" class="form-control" readonly></td>\
          <td><input type="text" value="'+discount_offer_on_bill+'" id="discount_offer_on_bill_table_update" name="discount_offer_on_bill_table_update[]" class="form-control" readonly></td>\
          <td><input type="hidden" value="'+offon_percent+'" id="offon_percent_table_update" name="offon_percent_table_update[]" class="form-control">'+offon_percent_txt+'</td>\
          <td><input type="text" id="cap_limit_to_update_table" name="cap_limit_to_update_table[]" class="form-control" readonly value="'+caplimit+'"></td>\
          <td><input type="text" id="product_value_to_update_table" name="product_value_to_update_table[]" class="form-control" readonly value="' + productvalue + '"></td>\
          <input type="hidden" id="order_type_to_update_table" name="order_type_to_update_table[]" class="form-control" readonly value="' + ordertype + '">\
          <input type="hidden" id="update_excl_manf" name="update_excl_manf[]" value="'+$("#excl_manf_id").val()+'" /> <input type="hidden" id="update_excl_prdgrp" name="update_excl_prdgrp[]" value="'+$("#excl_prod_group_id").val()+'" /><input type="hidden" id="update_excl_category" name="update_excl_category[]" value="'+$("#excl_Category_id").val()+'" /><td><a href="" class="btn btn-icon-only default delconditionforcashback"><i class="fa fa-trash-o"></i></a></td>\
        </tr>';
            $('#cashback_table_update').append(condition_tr);

            $('#Product_star')[0].sumo.selectItem("");
            $('#benificiary_update')[0].sumo.selectItem("");
            $('#update_cashback_description').val('');
            $('#update_from_cashback').val('');
            $('#update_to_cashback').val('');
            $('#discount_offer_cashback').val('');
            $('#offon_percent_cashback').prop('checked',false).uniform('refresh');
            $('#cap_limit').val('');
            $('#product_value').val('');
            //$('#warehouse_details')[0].sumo.unSelectAll();
            $('#benificiary_update')[0].sumo.unSelectAll();
            
            $('#offertypemanf_cashback')[0].sumo.unSelectAll();
            $('#offertypbrand_cashback')[0].sumo.unSelectAll();
            $("#order_type").val('');
        }
        
    });
});

// Fro Slab based promoriton set Pack configuration drop down
function stepValue(){
    $('#pack_value_update').val("");
    $('#value_two').val("");
    $('#offer_value').val("");


    var esu = $("#pack_number_update option:selected").attr("esu");
    $("#pack_value_update").attr("step",esu);

    // Change the color of the star
    var prdColor = $("#pack_number_update option:selected").attr("prd_star_color");
    $("#product_star_color").css("color",prdColor);

}
function maxQty(){     
    var packvalue=$('#pack_value_update').val();
    var packnumber = $('#pack_number_update').val();
    var maxqty  = packvalue*packnumber;
    $("#value_two").val(maxqty);
}

function getPack(id){

  $productid = id;
   var options = "<option value=''>Please select</option>";
    $.ajax({
        method: "GET",
        url: "/promotions/getpack/"+$productid,

        success:function(data)
        {
            $("#pack_number_update").empty(); 
            $("#step_count_update").val(data.esu);       
            for (var i = 0; i < data.length; i++) {
                options += '<option  star_code='+data[i].star+'  pack_level='+data[i].level+' esu='+data[i].esu+' prd_star_color='+data[i].StarColor+' value = "' + data[i].no_of_eaches + '">' + data[i].DPValue + '</option>';              
            }
            $("#pack_number_update").append(options);
        }      
    });
}
$('#update_freeqty_product_id').change(function(){
   $.ajax({
    url:'/promotions/getproductPackData/'+$('#update_freeqty_product_id').val(),
    success:function(results){
        console.log(results);
        $("#update_freeqty_pack").empty();
        $('#update_freeqty_pack').append($("<option>").attr('value','').text("--select--"));
        $.each(results,function(key,value){
            console.log(key);
            console.log(value);
            /*alert(value.level);
            alert(value.master_lookup_name);*/

            $('#update_freeqty_pack').append($("<option>").attr('value',value.level).text(value.master_lookup_name))
        });
    }
   });
});
$('#update_trade_type').change(function(){
    let token  = $("#csrf-token").val();
    $('#updateloaddata').show();
    console.log($('#update_trade_type').val());
    $.ajax({
        type:"GET",
        headers: {'X-CSRF-TOKEN':token},
        url:"/promotions/discounton/"+$('#update_trade_type').val(),
        success: function(result){
        /*
            $('#update_trade_warehouse').val('');
            $('#update_trade_warehouse')[0].sumo.reload();*/
            $('#update_promotion_on')[0].sumo.unSelectAll();
            $('#update_promotion_on').html('');
            $('#update_promotion_on')[0].sumo.reload();
            $('#update_pack_type').val('');
            $('#update_pack_type')[0].sumo.reload();
            if(result.status){
                $('#update_promotion_on').append(`<option value='0'>All</option>`);
                $('#update_promotion_on').append(result.data);
                $('#update_promotion_on')[0].sumo.reload();
                $('#updateloaddata').hide();              
            }else{
                $('#updateloaddata').hide();              
            }
        }
    });

});
$('#offertypemanf_cashback').change(function(){
    console.log($('#offertypemanf_cashback').val());
    let token  = $("#csrf-token").val();
    let manf=$('#offertypemanf_cashback').val();
    let formData = new FormData();
    formData.append('data', manf);
    $.ajax({
        type:"POST",
        headers: {'X-CSRF-TOKEN': token},
        url:"/promotions/getbrandsbymanufac",
        data:formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success:function(result){
            console.log(result);
            $('#offertypbrand_cashback').html('');
            $('#offertypbrand_cashback')[0].sumo.reload();
            if(manf.indexOf("0")!=-1){
                console.log('in brand all');
                $('#offertypbrand_cashback').append(`<option value='0'>All</option>`);
            }
            $('#offertypbrand_cashback').append(result.data);
            console.log(result.data);
            $('#offertypbrand_cashback')[0].sumo.reload();
        }
    });
    });
$('#cap_limit,#product_value').keypress(function(event) {
  if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
    event.preventDefault();
  }
});


$('#benificiary_update').change(function(){
    var beneficiary = $('#benificiary_update').val();
    console.log('beneficiary change',beneficiary, typeof beneficiary);

    if(beneficiary == 53){       
        $('#product_value').val(1);
        $('#order_type').val(0);
        $('.cashback_cls').css('display','none');
        $('.incentive_cls').css('display','block');
    }else{
        $('.cashback_cls').css('display','block');
        $('.incentive_cls').css('display','none');
    }  
});