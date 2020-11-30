var inData = new Array();
var gridCheckFlag = 0;

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

function myFunc(){

    calltype = $('#gridCallType').val();
    $("#product_grid").igGrid({
        dataSource: "/promotions/productgrid?calltype="+calltype+"&notindata="+inData
    });
}

// show-hide condition box as per the condition selection
function changetextbox(select)
{
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

function loadPromotionData(){
    $('.item-inner-container').hide();

    $('#gridCallType').val('Product');
    if($('#select_offer_tmpl').val()=='4'){        
       // alert($('#select_offer_tmpl').val());
        $('#gridCallType').val('Bill');
    }
   

    // assign the data
    var prmt_id = $("#select_offer_tmpl").val();       
    $('#select2_sample2')[0].sumo.unSelectAll();
    $('#select2_sample1')[0].sumo.unSelectAll();
    $('#warehouse_details')[0].sumo.unSelectAll();

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
        $('.freeSamplePromotion').hide();
        
        $('.itemSection').show();
        $('.slabCondition').show();
        $('.bundleCondition').hide();
        $('.DiscountOnTotalBill').hide();

        trData = "<thead>\
            <tr>\
                <th>Product Name</th>\
                <th>Action</th>\
            </tr>\
        </thead>";
        $("#add_product_table").empty();
        $("#add_product_table").append(trData);

        // Refresh the selection and remove the Multiple option for SlabBalsed
        $('#select2_sample2').removeAttr('multiple');
        $('#select2_sample1').removeAttr('multiple');
        $('#warehouse_details').removeAttr('multiple');

    }else if(prmt_id=='2'){
        $('.itemSection').show();
        $('.discount_ProductCondition').hide();
        $('.slabCondition').hide();
        $('.bundleCondition').show();
        $('.DiscountOnTotalBill').hide();

        trData = "<thead>\
            <tr>\
                <th>Product Name</th>\
                <th>QTY</th>\
                <th>Action</th>\
            </tr>\
        </thead>";
        $("#add_product_table").empty();
        $("#add_product_table").append(trData);

        // Refresh the selection and Adding the Multiple option 
        var select2_sample2 = document.getElementById("select2_sample2");
        select2_sample2.setAttribute("multiple", "multiple");
        var select2_sample1 = document.getElementById("select2_sample1");
        select2_sample1.setAttribute("multiple", "multiple");
        var wh = document.getElementById("warehouse_details");
        wh.setAttribute("multiple", "multiple");

    }else if(prmt_id=='3'){
        $('.itemSection').hide();
        $('.slabCondition').hide();
        $('.discount_ProductCondition').show();
        $('.bundleCondition').show();
        $('.DiscountOnTotalBill').hide();
        $('.freeSamplePromotion').hide();
        $('.tradesection').hide();
        // Refresh the selection and Adding the Multiple option 
        var select2_sample2 = document.getElementById("select2_sample2");
        select2_sample2.setAttribute("multiple", "multiple");
        var select2_sample1 = document.getElementById("select2_sample1");
        select2_sample1.setAttribute("multiple", "multiple");
        var wh = document.getElementById("warehouse_details");
        wh.setAttribute("multiple", "multiple");
    }else if(prmt_id=='4'){
        $('.itemSection').hide();
        $('.slabCondition').hide();
        $('.discount_ProductCondition').hide();
        $('.bundleCondition').hide();
        $('.DiscountOnTotalBill').show();
        $('.cashBackonbill').hide();
        $('.freeSamplePromotion').hide();
        $('.tradesection').hide();
        // Refresh the selection and Adding the Multiple option 
        var select2_sample2 = document.getElementById("select2_sample2");
        select2_sample2.setAttribute("multiple", "multiple");
        var select2_sample1 = document.getElementById("select2_sample1");
        select2_sample1.setAttribute("multiple", "multiple");
        var wh = document.getElementById("warehouse_details");
        wh.setAttribute("multiple", "multiple");
    }else if(prmt_id=='5'){
        $('.cashBackonbill').show();
        $('.itemSection').hide();
        $('.slabCondition').hide();
        $('.discount_ProductCondition').hide();
        $('.bundleCondition').hide();
        $('.DiscountOnTotalBill').hide();         

        $('.freeSamplePromotion').hide();
        $('.tradesection').hide();
        // Refresh the selection and Adding the Multiple option 
        var select2_sample2 = document.getElementById("select2_sample2");
        select2_sample2.setAttribute("multiple", "multiple");
        var select2_sample1 = document.getElementById("select2_sample1");
        select2_sample1.setAttribute("multiple", "multiple");
        var wh = document.getElementById("warehouse_details");
        wh.setAttribute("multiple", "multiple");
    }else if(prmt_id == '6'){
        $('.cashBackonbill').hide();
        $('.itemSection').hide();
        $('.slabCondition').hide();
        $('.discount_ProductCondition').hide();
        $('.bundleCondition').hide();
        $('.DiscountOnTotalBill').hide();
        $('.freeSamplePromotion').show();
        $('.tradesection').hide();        
        var select2_sample2 = document.getElementById("select2_sample2");
        select2_sample2.setAttribute("multiple", "multiple");
        var select2_sample1 = document.getElementById("select2_sample1");
        select2_sample1.setAttribute("multiple", "multiple");
        var wh = document.getElementById("warehouse_details");
        wh.setAttribute("multiple", "multiple");
    }else if(prmt_id == '7'){
        console.log('----------',7);
        $('.cashBackonbill').hide();
        $('.itemSection').hide();
        $('.slabCondition').hide();
        $('.discount_ProductCondition').hide();
        $('.bundleCondition').hide();
        $('.DiscountOnTotalBill').hide();
        $('.freeSamplePromotion').hide();
        $('.tradesection').show();        
        var select2_sample2 = document.getElementById("select2_sample2");
        select2_sample2.setAttribute("multiple", "multiple");
        var select2_sample1 = document.getElementById("select2_sample1");
        select2_sample1.setAttribute("multiple", "multiple");
        var wh = document.getElementById("warehouse_details");
        wh.setAttribute("multiple", "multiple");
    }
    $('#select2_sample1')[0].sumo.reload();
    $('#select2_sample2')[0].sumo.reload();
    $('#warehouse_details')[0].sumo.reload();
}

// show-hide ItemGrid part along with Data
function getItemGrid(){

    calltype = $('#gridCallType').val();

    if(calltype!='Bill'){

        $('.item-inner-container').show();
        //gridCheckFlag=1;

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
                    columnSorting: myFunc
                },

            ],
            primaryKey: 'item_id',
            width: '100%',
            height: '500px',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
        });
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

$(document).ready(function() {

     window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});


    // set default dates
    var start = new Date();
    var end = new Date(new Date().setYear(start.getFullYear() + 1));

    $('#start_date').datepicker({
        daysOfWeekDisabled: [0],
        endDate: end,
        autoclose: true,
        format: 'yyyy-mm-dd'
    }).on('changeDate', function () {
        stDate = new Date($(this).val());
        $('#frm_add_new_tmpl').bootstrapValidator('revalidateField', 'start_date');
        $('#end_date').datepicker('setStartDate', stDate);
    });

    $('#end_date').datepicker({
        daysOfWeekDisabled: [0],
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

    $('#frm_add_new_tmpl').formValidation({
        message: 'This value is not valid',
        icon: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            promotion_name: {
                
                validators: {
                    notEmpty: {
                        message: 'Promotion Name is required'
                    },
                    stringLength: {
                        min: 6,
                        max: 100,
                        message: 'Promotion Name is required must be more than 6 and less than 30 characters long'
                    }
                }
            },            
            select_offer_tmpl: {
                validators: {
                    notEmpty: {
                        message: 'Offer Type is required'
                    }
                }
            },
            'warehouse_details[]':{
                validators:{
                    notEmpty:{
                        message:'Please select Warehouse'
                    }
                } 
            },
            offertype: {
                validators: {
                    notEmpty: {
                        message: 'Please select offer promotion'
                    }
                }
            },

            prmt_lock_qty: {
                validators: {
                    numeric: {
                        message: 'Please enter numeric value'
                    }
                }
            },
            start_date: {
                validators: {
                    notEmpty: {
                        message: 'Please select Start Date'
                    },
                }
            },
            end_date: {
                validators: {
                    notEmpty: {
                        message: 'Please select End Date'
                    },
                }
            },

            'state[]': {
                validators: {
                    notEmpty: {
                        message: 'Please select the Business Location'
                    }
                }
            },
           
          
            bill_value:{
                validators: {
                    notEmpty: {
                        message: 'Please enter the amount'
                    }
                }
            },
            discount_offer:{
                validators: {
                    notEmpty: {
                        message: ' Enter discount '
                    }
                }
            },
            'item_id[]':{
                validators: {
                    notEmpty: {
                        message: 'select Product'
                    }
                }
            },
            
            'select_product[]':{
                validators: {
                    notEmpty: {
                        message: 'select Product'
                    }
                }
            },
            'free_qty':{
                 validators: {
                    notEmpty: {
                        message: 'Enter Quantity'
                    }
                }
            },
            set_qty:{
                 validators: {
                    notEmpty: {
                        message: 'Enter Quantity'
                    }
                }
            },
            discount_offer_bill:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter discount'
                    }
                }
            },
            'customer_group[]': {
                validators: {
                    notEmpty: {
                        message: 'Please select the Customer Group'
                    }
                }
            },
            'ProductStar_on_bill[]':{
                 validators: {
                    notEmpty: {
                        message: 'Please Select product star'
                    }
                }
            },
            freeqty_description:{
                validators:{
                    notEmpty:{
                        message:'Description is required'
                    }
                }
            },
            'wareHouseId[]':{
                validators: {
                    notEmpty: {
                        message: 'Please Select the brand'
                    }
                }
            },
            'freeqty_from':{
                validators: {
                    notEmpty: {
                        message: 'Please Select the from'
                    }
                }
            },
            'freeqty_to':{
                validators: {
                    notEmpty: {
                        message: 'Please Select the to range'
                    }
                }
            },
            freeqty_product_id:{
                validators: {
                    notEmpty: {
                        message: 'Please Select the product'
                    }
                }
            },
            product_quantity:{
                validators: {
                    notEmpty: {
                        message: 'Please Select the product qty'
                    }
                }
            },
            freeqty_pack:{
                validators: {
                    notEmpty: {
                        message: 'Please select pack type'
                    }
                }
            },
            trade_type:{
                validators:{
                    notEmpty:{
                        message: 'Please select Discount type'
                    }
                }
            },
            /*'trade_warehouse[]':{
                validators:{
                    notEmpty:{
                        message:'Please select warehouse type'
                    }
                }
            },*/
            'promotion_on[]':{
                validators:{
                    notEmpty:{
                        message:'Please select Promotion on'
                    }
                }
            },
            'pack_type[]':{
                validators:{
                    notEmpty:{
                        message:'Please select Pack type'
                    }
                }
            },
            trade_from_range:{
                validators:{
                    notEmpty:{
                        message:'Please enter From Range'
                    }
                }
            },
            trade_to_range:{
                validators:{
                    notEmpty:{
                        message:'Please enter To Range'
                    }
                }
            },
            tradeoffer_on_bill:{
                validators:{
                    numeric:{
                        message: 'Please enter numeric value'
                    },
                    notEmpty:{
                        message:'Please enter Discount'
                    }
                }
            },
            tradeoffer_type:{
                validators:{
                    notEmpty:{
                        message:'Please select Type'                    
                    }
                }
            }
        }
    })
    .on('success.form.fv', function(e, data){
        e.preventDefault();

        fv = $(e.target).data('formValidation');
        fv.disableSubmitButtons(false);

        if( ($('#item_id').val()===null || $('#slab_table').val()===null) && $('#select_offer_tmpl').val==1){
            $('#no_product_span').html("Please Select Product");
            $('#no_slab_span').html("Please Select Slab Data");
            $('#frm_add_new_tmpl').formValidation('revalidateField', 'promotion_name');

        }else if ($('#select_offer_tmpl').val()==1 && $('#slab_table tr').length<=1){
            $('.cust-error-no-tr-lines').html('Please add slab details below!');
            
            return false;
        }
        else if($('#select_offer_tmpl').val()==5 && $('#cashback_table tr').length<=1 ){
            $('.cust-error-no-cb-lines').html('Please add Cashback details below!');
            
            return false;
        }else if($('#select_offer_tmpl').val()==7){

            let trade_to_range=$('#trade_to_range').val();
            let trade_from_range=$('#trade_from_range').val();
            if( trade_to_range.trim()=='' || parseFloat(trade_to_range.trim())==0 || parseFloat(trade_to_range.trim()) < parseFloat(trade_from_range.trim()) ){
                $('.cust-error-trade_to_range').html("Not a valid Input!");
            }else{
                fv.defaultSubmit();
            }
        }
        else{
            
            fv.defaultSubmit();
        }
    }); 

    $("#value_two").keydown(function (event) {
        allowNumber(event);
    });

    $("#bill_value").keydown(function (event) {
        allowNumber(event);
    });

    $("#offer_value").keydown(function (event) {
        allowNumber(event); 
    });
    $("#discount_offer_on_bill").keydown(function (event) {
        allowNumber(event);
    });

    $("#prmt_lock_qty").keydown(function (event) {
        allowNumber(event); 
    });
     $("#cash_back_from").keydown(function (event) {
        allowNumber(event);
     });
      $("#cash_back_to").keydown(function (event) {
        allowNumber(event);
     });
     $("#discount_offer_bill").keydown(function (event) {
        allowNumber(event);
     });
     $("#pack_value").keydown(function (event) {
        NotAllowKey(event);
     });




    $('#add_product_table').on('click', '.delList', function(e){
        e.preventDefault();

        var removeItem = $(this).closest('td').siblings(':nth-child(2)').find('input[data_item_id]').val();
        inData = jQuery.grep(inData, function(value) {
            return value != removeItem;
        });

        $(this).closest('tr').remove(); 
        $("#slab_table tbody tr").remove(); 
        $('#pack_number').html('');
        $('#pack_value').val('');
        $("#product_star_color").css("color",'');
        $('#value_two').val('');
        $('#offer_value').val('');
        $('.cust-error-no-product').html('');

    });

    $('.moveLeft').click(function(e){

        var promotionType = $("#select_offer_tmpl").val();

        // Reject multiple selection for Slab Based
        if(promotionType==1 && (typeof $('#item_id').val()!='undefined') && $('#item_id').val()!==null){
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
            $("#pack_value").val();
            $("#value_two").val();
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
        $('#no_product_span').html('');
        myFunc();
    });
    var num=0;
    $('.addslab').click(function(e){
         num=num+1;
        var value_two = $('#value_two').val();
        var offer_value = $('#offer_value').val();
        $('.cust-error-no-tr-lines').html("");
        var pack_number=$('#pack_number option:selected').html();
        var product_star_color= $("#pack_number option:selected").attr("prd_star_color");
        var star_code=$("#pack_number option:selected").attr("star_code");
        var pack_level=$("#pack_number option:selected").attr("pack_level");


        var pack_value=$('#pack_value').val();
        var oldFlag = 0;
        var oldQty = 0;
        $('#slab_table tbody tr').each(function() {

            var PackConfig = $(this).find("td:eq(0) input[id='pack_number_table']").val();
            var EsuConfig = $(this).find("td:eq(2) input[id='pack_value_table']").val();           


            if(PackConfig==pack_level&& EsuConfig==pack_value){

                oldFlag=1;  
                
                
                $(".error_messege").html('check slab Maximum Quantity');
            }
        });

    
        if(value_two !='' & offer_value!='' & oldFlag==0){
            
            $(".error_messege").html('');

            var condition_tr = '<tr class="gradeXSlab odd list-head">\
            <td data-val="cond_to">'+pack_number+'<input type="hidden" value="'+pack_level+'" id="pack_number_table" name="pack_number_table[]" class="form-control" readonly></td>\
            <td data-val="cond_to"><span class="fa fa-star" id="product_star_color_table'+num+'" name="product_star_color_table'+num+'" aria-hidden="true" style="font-size: 30px;"></span><input type="hidden" value="'+star_code+'" id="product_star_color_table'+num+'" name="product_star_color_table[]" class="form-control" readonly></td>\
            <td data-val="cond_to">'+pack_value+'<input type="hidden" value="'+pack_value+'" id="pack_value_table" name="pack_value_table[]" class="form-control" readonly></td>\
              <td data-val="cond_to">'+value_two+'<input type="hidden" value="'+value_two+'" id="value_two" name="value_two[]" class="form-control" readonly></td>\
              <td data-val="offer_value">'+offer_value+'<input type="hidden" value="'+offer_value+'" id="offer_value" name="offer_value[]" class="form-control"></td>\
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
                $('#no_slab_span').html('');
                $('#pack_value').val('');
                $('#pack_number').val('');
                $('#product_star_color').val('');
                $("#product_star_color").css("color",'');

        }
    });


    $('#slab_table').on('click', '.delcondition', function(e){
       e.preventDefault();

        var removeItem = $(this).closest('td').siblings(':nth-child(2)').find('input[value_one]').val();

        $(this).closest('tr').remove();
    });



    $('#adding_cashback').click(function(e){

        var desc          = $('#cashback_description').val();
        var offertypemanf = $('#offertypemanf option:selected').text()=='--Please Select--' ? '--' : $('#offertypemanf option:selected').text();
        var offertypbrand = $('#offertypbrand option:selected').text()=='--Please Select--' ? '--' : $('#offertypbrand option:selected').text();
        var excludebrand = $('#excludebrand option:selected').text()=='--Please Select--' ? '--' : $('#excludebrand option:selected').text();
        var ProductStar = $('#ProductStar option:selected').text()=='--Please Select--' ? '--' : $('#ProductStar option:selected').text();
        var Benificiary = $('#Benificiary option:selected').text()=='--Please Select--' ? '--' : $('#Benificiary option:selected').text();
        var wareHouseId = $('#warehouse_details option:selected').text()=='--Please Select--' ? '--' : $('#warehouse_details option:selected').text();
        var prd_grp = $('#prd_grp option:selected').text()=='--Please Select--' ? '--' : $('#prd_grp option:selected').text();

        var leWhIds = $('#warehouse_details').val()=='' ? '' : $('#warehouse_details').val();
        var cash_back_from = $('#cash_back_from').val();
        var cash_back_to = $('#cash_back_to').val();
        var discount_offer_on_bill = $('#discount_offer_on_bill').val();
        var offon_percent = $('#offon_percent_cashback').is(":checked") ? 1 : 0;
        var offon_percent_txt = $('#offon_percent_cashback').is(":checked") ? '%' : '&#8377;';

        var customer_group = $('#select2_sample1 option:selected').text();
        var state = $('#select2_sample2 option:selected').text();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var caplimit=$('#cap_limit').val();
        var productvalue=$('#product_value').val();
        var ordertype=$('#order_type').val();

        var excl_category = $('#excl_Category_id option:selected').text()=='--Please Select--' ? '--' : $('#excl_Category_id option:selected').text();
        var excl_product = $('#excl_prod_group_id option:selected').text()=='--Please Select--' ? '--' : $('#excl_prod_group_id option:selected').text();
        var excl_manf = $('#excl_manf_id option:selected').text()=='--Please Select--' ? '--' : $('#excl_manf_id option:selected').text();

        console.log(excl_category);
        console.log(excl_product);
        console.log(excl_manf);
        

        $('.cust-error-cash_back_from').html("");
        $('.cust-error-cash_back_to').html("");
        $('.cust-error-no-cb-lines').html("");
        $('.cust-error-discount_offer_on_bill').html("");
        $('.cust-error-ProductStar').html("");
        $('.cust-error-desc').html("");
        var AddFlag = 0;

        if(state.trim()== ''){
            console.log('state');
           $('.cust-error-state').html("Can't be empty!");  
            AddFlag=1;      
        }
        if(customer_group.trim() == ''){
            console.log('customer_group');
           $('.cust-error-customer_group').html("Can't be empty!");
            AddFlag=1;          
        }
        if(desc.trim()==''){
            console.log('err');
            $('.cust-error-desc').html("Can't be empty!");
            AddFlag=1; 
        }
        if($('#ProductStar').val() == ''){
            console.log('prdstar');
            $('.cust-error-ProductStar').html("Can't be empty!");
            AddFlag=1;
        }
        if(cash_back_from.trim()==''){
            console.log('cb from');
            $('.cust-error-cash_back_from').html("Can't be empty!");
            AddFlag=1;
        }
        if( cash_back_to.trim()=='' || parseFloat(cash_back_to.trim())==0 || parseFloat(cash_back_to.trim()) < parseFloat(cash_back_from.trim() )  ){
            console.log('cb to');
            $('.cust-error-cash_back_to').html("Not a valid Input!");
            AddFlag=1;
        }
        if(discount_offer_on_bill.trim()=='' || parseFloat(discount_offer_on_bill.trim())==0){
            console.log('discount to');
            $('.cust-error-discount_offer_on_bill').html("Can't be empty or '0'!");
            AddFlag=1;
        }
        if(caplimit.trim()==''){
            console.log('caplimit');
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

        var benificiary_val=$('#Benificiary').val();
        if(benificiary_val.trim()==''){
            console.log('cash_order_type');
            $('.cust-error-cash-benificiary').html("Can't be empty!");
            AddFlag=1; 
        }
        var brand_val=$('#offertypbrand').val();
        if((brand_val=='' || brand_val==null) && (benificiary_val!=53)){
            console.log('cash-brand');
            $('.cust-error-cash-brand').html("Can't be empty!");
            AddFlag=1; 
        }
        var manuf_val=$('#offertypemanf').val();
        if((manuf_val=='' || manuf_val==null)  && (benificiary_val!=53)){
            console.log('cash-manf');
            $('.cust-error-cash-manuf').html("Can't be empty!");
            AddFlag=1; 
        }
        var prod_grp=$('#prd_grp').val();
        if((prod_grp=='' || prod_grp==null)  && (benificiary_val==53)){
            console.log('cash-manf');
            $('.cust-error-ProductGRP').html("Can't be empty!");
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
          <td><input type="hidden" value="'+$('#select2_sample2').val()+'" id="state_table" name="state_table[]" class="form-control">'+state+'</td>\
          <td><input type="hidden" value="'+$('#select2_sample1').val()+'" id="customer_group_table" name="customer_group_table[]" class="form-control">'+customer_group+'</td>\
          <td><input type="hidden" value="'+$('#cashback_description').val()+'" id="cashback_description_table" name="cashback_description_table[]" class="form-control">'+desc+'</td>\
          <td><input type="hidden" value="'+$('#offertypemanf').val()+'" id="offertypemanf_table" name="offertypemanf_table[]" class="form-control">'+offertypemanf+'</td>\
          <td><input type="hidden" value="'+$('#offertypbrand').val()+'" id="offertypbrand_table" name="offertypbrand_table[]" class="form-control">'+offertypbrand+'</td>\
          <td><input type="hidden" value="'+$('#excludebrand').val()+'" id="excludebrand_table" name="excludebrand_table[]" class="form-control">'+excludebrand+'</td>\
          <td><input type="hidden" value="'+$('#prd_grp').val()+'" id="prd_grp_table" name="prd_grp_table[]" class="form-control">'+prd_grp+'</td>\
          <td><input type="hidden" value="'+$('#Benificiary').val()+'" id="Benificiary_table" name="Benificiary_table[]" class="form-control">'+Benificiary+'</td>\
          <td><input type="hidden" value="'+$('#ProductStar').val()+'" id="ProductStar_table" name="ProductStar_table[]" class="form-control">'+ProductStar+'</td>\
          <td><input type="hidden" value="'+leWhIds+'" id="wareHouseId_table" name="wareHouseId_table[]" class="form-control">'+WareHouseName+'</td>\
          <td><input type="text" value="'+cash_back_from+'" id="cash_back_from_table" name="cash_back_from_table[]" class="form-control" readonly></td>\
          <td><input type="text" value="'+cash_back_to+'" id="cash_back_to_table" name="cash_back_to_table[]" class="form-control" readonly></td>\
          <td><input type="text" value="'+discount_offer_on_bill+'" id="discount_offer_on_bill_table" name="discount_offer_on_bill_table[]" class="form-control" readonly></td>\
          <td><input type="hidden" value="'+offon_percent+'" id="offon_percent_table" name="offon_percent_table[]" class="form-control" readonly>' + offon_percent_txt + '</td>\
          <td><input type="text" id="table_cap_limit" name="table_cap_limit[]" class="form-control" readonly value="'+caplimit+'"></td>\
          <td><input type="text" id="table_product_value" name="table_product_value[]" class="form-control" readonly value="' + productvalue + '" /><input type="hidden" id="table_order_type" name="table_order_type[]" class="form-control" readonly value="' + ordertype + '" /><input type="hidden" id="table_excl_manf" name="table_excl_manf[]" value="'+$("#excl_manf_id").val()+'" /> <input type="hidden" id="table_excl_product" name="table_excl_product[]" value="'+$("#excl_prod_group_id").val()+'" /><input type="hidden" id="table_excl_category" name="table_excl_category[]" value="'+$("#excl_Category_id").val()+'" /> </td>\
          <td><a href="" class="btn btn-icon-only default delcondition_cashback"><i class="fa fa-trash-o"></i></a></td>\
            </tr>';
            console.log(condition_tr);
            $('#cashback_table').append(condition_tr);
            $('#cashback_description').val('');
            $('#ProductStar').val('');
            $('#offertypemanf').val('');
            $('#offertypbrand').val('');
            $('#Benificiary').val('');
            $('#wareHouseId').val('');
            $('#cash_back_from').val('');
            $('#cash_back_to').val('');
            $('#discount_offer_on_bill').val('');
            $('#offon_percent_cashback').prop('checked',false).uniform('refresh');
            $('#cap_limit').val('');
            $('#product_value').val('');
            //$('#warehouse_details')[0].sumo.unSelectAll();
            $('#Benificiary')[0].sumo.unSelectAll();
            $("#order_type").val('');
            $('#offertypemanf')[0].sumo.unSelectAll();
            $('#offertypbrand')[0].sumo.unSelectAll();

        }
        
    });


    $('#cashback_table').on('click', '.delcondition_cashback', function(e){
       e.preventDefault();
        $(this).closest('tr').remove();
    });


});


function getPack(id){

  $productid = id;
   var options = "<option value=''>Please select</option>";
    $.ajax({
        method: "GET",
        url: "/promotions/getpack/"+$productid,
        success:function(data)
        {

            if(data.length==0){

                $('.cust-error-no-product').html('Please select another Product.This product is not sellable');

            }
            $("#pack_number").empty();        
            $("#step_count").val(data.esu);
            for (var i = 0; i < data.length; i++) {
                options += '<option  star_code='+data[i].star+'  pack_level='+data[i].level+' esu='+data[i].esu+' prd_star_color='+data[i].StarColor+' value = "' + data[i].no_of_eaches + '">' + data[i].DPValue + '</option>';              
            }

            $("#pack_number").append(options);
        }      
    });
}

function maxQty(){     
    var packvalue=$('#pack_value').val();
    var packnumber = $('#pack_number').val();
    var maxqty  = packvalue*packnumber;
    $("#value_two").val(maxqty);
}

function stepValue(){
    $('#pack_value').val("");
    $('#value_two').val("");
    $('#offer_value').val("");


   var esu = $("#pack_number option:selected").attr("esu");
   $("#pack_value").attr("step",esu);

   // Change the color of the star
   var prdColor = $("#pack_number option:selected").attr("prd_star_color");
   $("#product_star_color").css("color",prdColor);

}
$('#freeqty_product_id').change(function(){
   // alert($('#freeqty_product_id').val());
   $.ajax({
    url:'/promotions/getproductPackData/'+$('#freeqty_product_id').val(),
    success:function(results){
        $("#freeqty_pack").empty();
        $('#freeqty_pack').append($("<option>").attr('value','').text("--select--"));
        $.each(results,function(key,value){
            console.log(key);
            console.log(value);
            //alert(value.level);
            //alert(value.master_lookup_name);

            $('#freeqty_pack').append($("<option>").attr('value',value.level).text(value.master_lookup_name))
        });
    }
   });
});
$('#offertypemanf').change(function(){
    console.log($('#offertypemanf').val());
    let token  = $("#csrf-token").val();
    let manf=$('#offertypemanf').val();
    let formData = new FormData();
    formData.append('data', manf);

    console.log('manuf',manf);
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
            $('#offertypbrand').html('');
            $('#offertypbrand')[0].sumo.reload();
            $('#offertypbrand').append(`<option value = "">--Please Select--</option>`);
            if(manf.indexOf("0")!=-1){
                $('#offertypbrand').append(`<option value='0'>All</option>`);
            }
            $('#offertypbrand').append(result.data);
            $('#offertypbrand')[0].sumo.reload();
            
        }
    });
});
$('#trade_type').change(function(){
    let token  = $("#csrf-token").val();
    $('#loaddata').show();
    console.log($('#trade_type').val());
    $.ajax({
        type:"GET",
        headers: {'X-CSRF-TOKEN':token},
        url:"/promotions/discounton/"+$('#trade_type').val(),
        success: function(result){
            $('#promotion_on').html('');
            $('#promotion_on')[0].sumo.unSelectAll();
            $('#promotion_on')[0].sumo.reload();
            $('#pack_type')[0].sumo.unSelectAll();
            $('#pack_type')[0].sumo.reload();
            if(result.status){
                $('#promotion_on').append(`<option value = "">--Please Select--</option>`);
                $('#promotion_on').append(`<option value='0'>All</option>`);
                $('#promotion_on').append(result.data);
                $('#promotion_on')[0].sumo.reload();
                $('#frm_add_new_tmpl').bootstrapValidator('revalidateField', 'promotion_on');
              /*  result['data'].forEach(function(data){
                    console.log(`<option value=${data['id']}>${data['i_name']}</option>`);
                    $('#promotion_on').append(`<option value=${data['id']}>${data['i_name']}</option>`);
                    $('#promotion_on')[0].sumo.reload();
                });*/
                $('#loaddata').hide();
              
            }else{
                $('#loaddata').hide();
            }
        }
    });

});


$('#cap_limit,#product_value').keypress(function(event) {
  if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
    event.preventDefault();
  }
});

$('#Benificiary').change(function(){
    var beneficiary = $('#Benificiary').val();
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