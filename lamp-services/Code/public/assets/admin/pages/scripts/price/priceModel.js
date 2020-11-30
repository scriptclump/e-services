$(document).ready(function() {
$('#frm_price_tmpl').formValidation({
        message: 'This value is not valid',
        icon: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            /*add_state: {
                validators: {
                    notEmpty: {
                        message: 'Required'
                      }
                    }
                },*/

     add_dc: {
                validators: {
                    notEmpty: {
                        message: 'Required'
                      }
                    }
                },                   
            add_custgroup: {
                validators: {
                    notEmpty: {
                        message: 'Required'
                    }
                }
            },

            selling_price: {
                validators: {
                    notEmpty: {
                        message: 'Required',
                    },
                    numeric: {
                        message: 'Selling price is not a number',
                    },
                    greaterThan: {
                        value: 0.1,
                        message: 'Selling price must be greater than 0'
                    },
                    callback: {
                        message: 'ESP is morethan MRP!',
                        callback: function(value, validator, $field) {
                            var esp = parseFloat(value).toFixed(2);
                            var mrp = parseFloat($("#mrp").text()).toFixed(2);
                            // check for esp and mrp
                            if(!isNaN(esp) && Number(esp) > Number(mrp)){
                                return false;
                            }else{
                                return true; 
                            }

                        }
                    }
                }
            },

            price_ptr: {
                validators: {
                    notEmpty: {
                        message: 'Required',
                    },
                    numeric: {
                        message: 'PTR is not a number',
                    },
                    greaterThan: {
                        value: 0.1,
                        message: 'PTR must be greater than 0'
                    },
                    callback: {
                        message: 'PTR is morethan MRP!',
                        callback: function(value, validator, $field) {
                            var esp = parseFloat(value).toFixed(2);
                            var mrp = parseFloat($("#mrp").text()).toFixed(2);
                            // check for esp and mrp
                            if(!isNaN(esp) && Number(esp) > Number(mrp)){
                                return false;
                            }else{
                                return true; 
                            }

                        }
                    }
                }
            },

            date: {
                validators: {
                    notEmpty: {
                        message: 'Required',
                    },
                    date: {
                        format: 'DD/MM/YYYY',
                        message: 'Effective Date is not a valid'
                    }
                }
            }
        }
})
.on('success.form.fv', function(e){
    e.preventDefault();
    var token  = $("#csrf-token").val();
    var productID = $("#add_prd_id").val();
    var frmData = $('#frm_price_tmpl').serialize();

    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/pricing/addeditslabdata',
        data: frmData,
        success: function (respData)
        {
            $('#save_price').modal('toggle');
            if(respData=="rollback"){
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Sorry failed to update.</div></div>');
                $(".alert-success").fadeOut(5000);
            }else if(globalPageFlag==1){
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+respData+'</div></div>' );
                $(".alert-success").fadeOut(20000)
                filterdata();
            }else if(globalPageFlag==2){
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+respData+'</div></div>' );
                $(".alert-success").fadeOut(20000)
            }else{
                reLoadPriceGrid_forProductModule();
            }
        }
    });
}); 


$('#save_price').on('hidden.bs.modal', function (e) {


    $('#cashback_tab').addClass("disabled");
    // $('#cashback_tab').removeClass("active");
    $('#cashback_tab_').removeAttr('data-toggle');
    // $("#pricing_tab").addClass("active");
    // $('.nav-tabs a:first').tab('show');


});



/*function savePriceData(){
    alert(globalPageFlag);
    var token  = $("#csrf-token").val();
    var productID = $("#add_prd_id").val();
    var frmData = $('#frm_price_tmpl').serialize();

    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/pricing/addeditslabdata/',
        data: frmData,
        success: function (respData)
        {
            $('#save_price').modal('toggle');
            if(globalPageFlag==1){
                filterdata();
            }else{
                reLoadPriceGrid_forProductModule();
            }
        }
    });
}*/

//reload the grid (for price)
function reLoadPriceGrid_forProductModule(){

    var productID = $("#add_prd_id").val();
    var sortURL = "/products/slabPrices?product_id="+productID;

    ds = new $.ig.DataSource({
        type: "json",
        responseDataKey: "Records",
        dataSource: sortURL,
        callback: function (success, error) {
            if (success) {
                $("#slabprices").igGrid({
                        dataSource: ds,
                        autoGenerateColumns: false
                });
            } else {
                alert(error);
            }
        },
    });

    ds.dataBind();

}

$(document).ready(function(){
    // set default dates
    var date = new Date();
    $('#date').datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function(datesel) {
            $('#frm_price_tmpl').formValidation('revalidateField', 'date');
        }
    })

    $('#cashback_start_date').datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: new Date(),
        onSelect: function(datesel) {
            $('#cashback_form').formValidation('revalidateField', 'cashback_start_date');
            var selectedDate = new Date(datesel);
            var endDate = new Date(selectedDate.getTime());
            $("#cashback_end_date").datepicker( "option", "minDate", endDate );
        }
    })

    $('#cashback_end_date').datepicker({
        dateFormat: 'yy-mm-dd',
        onSelect: function(datesel) {
            $('#cashback_form').formValidation('revalidateField', 'cashback_end_date');
        }
    })

    // function written for Pricing
    $('#save_price').on('show.bs.modal', function (e) {
        // Removing the Error class
        $("#date_revalid").removeClass('has-error');
        $("#add_state_revalid").removeClass('has-error');
        $("#add_custgroup_revalid").removeClass('has-error');
        $("#selling_price_revalid").removeClass('has-error');
        $("#price_ptr_revalid").removeClass('has-error');
        $("#add_state_revalid").removeClass('has-success');
        $("#add_custgroup_revalid").removeClass('has-success');
        $("#selling_price_revalid").removeClass('has-success');
        $("#price_ptr_revalid").removeClass('has-success');
        $("#date_revalid").removeClass('has-success');
        $("#cashback_table tbody").empty();

        // empty cashback form
        $("#cashback_response").html('');
        $("#cashback_warehouse").val('');
        $("#cashback_state").val('');
        $("#cashback_custgroup").val('');
        $("#cashback_start_date").val('');
        $("#cashback_start_date_").removeClass('has-success');
        $("#cashback_end_date").val('');
        $("#cashback_end_date_").removeClass('has-success');
        $("#cashback_for").val('');
        $("#cashback_product_star").val('');
        $("#cashback_quantity").val('');
        $("#offer_value").val('');
        $("#cashback_ref_id").val('');
        $('#is_percent').prop('checked',false);

        $('#cashback_form').formValidation('resetField', 'cashback_start_date');
        $('#cashback_form').formValidation('resetField', 'cashback_end_date');
        $('#cashback_form').formValidation('resetField', 'offer_value');
        $('#cashback_form').formValidation('resetField', 'cashback_quantity');
        $('#cashback_form').formValidation('resetField', 'cashback_product_star');
    


       


        var token  = $("#csrf-token").val();
        $("#add_price_message").hide();
        var productID = '';

        // Delete the Tax table data 
        $("#price_details").empty();
        trData = "<thead>\
            <tr>\
                <th>Price Type</th>\
                <th>State Billing</th>\
                <th>Inter State Billing</th>\
            </tr>\
        </thead>";
        $("#price_details").append(trData);

        if(updatePriceID!=0){
            // UPDATE PART

            $("#add_edit_flag").val("1");
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                type: "GET",
                url: '/pricing/getupdatedata/' + updatePriceID,
                success: function (data)
                {

                    var mainData = data['Maindata'];
                    var tabledata = data['Cashback'];
                                        
                    if(mainData[0].product_id){
                        var productID = mainData[0].product_id;
                        $("#cashback_product_id").val(mainData[0].product_id);
                        $("#product_name_heading").html('[ '+mainData[0].product_title+'  -  ' +mainData[0].sku+ ' ]');
                        $("#product_price_id").val(mainData[0].product_price_id);
                        $("#cashback_ref_id").val(mainData[0].product_price_id);
                        $("#add_prd_id").val(mainData[0].product_id);
                        $("#prd_name").html(mainData[0].product_title);
                        $("#product_name").val(mainData[0].product_title);
                        $("#cashback_prd_name").html(mainData[0].product_title);

                       // $("#add_state").val(mainData[0].state_id);
                       $("#add_dc").val(mainData[0].dc_id);
                       $("#hidden_add_dc").val(mainData[0].dc_id);
                        $("#add_custgroup").val(mainData[0].customer_type);
                        $( "#add_dc" ).prop( "disabled", true );
                        $( "#add_custgroup" ).prop( "disabled", true );
                        $("#selling_price").val(mainData[0].price);
                        $("#price_ptr").val(mainData[0].ptr);


                        if( mainData[0].effective_date!='1970-01-01 00:00:00' ){
                            $("#date").val(mainData[0].effective_date);
                        }else{
                            $("#date").val('');
                        }

                        var markupTXT = "";
                        if(mainData[0].is_markup==0){
                            markupTXT = "Mark Down";
                        }else{
                            markupTXT = "Mark Up";
                        }
                        $('#margin_type').html(markupTXT);

                        $("#mrp").html(mainData[0].mrp);
                        $("#ptr").html(mainData[0].rlp);

                        // Load Cashback data
                        $('#cashback_table').append(tabledata);

                        // Revalidate all the fields at the time of modification
                        $('#price-save-button').click(function(){
                            $('#frm_price_tmpl').formValidation('revalidateField', 'add_dc');
                            $('#frm_price_tmpl').formValidation('revalidateField', 'add_custgroup');
                            $('#frm_price_tmpl').formValidation('revalidateField', 'date');
                            $('#frm_price_tmpl').formValidation('revalidateField', 'selling_price');
                            $('#frm_price_tmpl').formValidation('revalidateField', 'price_ptr');
                        });

                        loadRightSideData();
                    }else{
                        $('#save_price').modal('toggle');
                    }    
                }
            });





            updatePriceID = 0;
        }else{
            // if the product is not selected modal will not appear
            if( ($('#product_dropdown').val()=='' ||  $('#product_dropdown').val()==null) && $('#product_id').val()==''){
                $("#add_price_message").show();
                $('#save_price').modal('toggle');
            }else{

                var myId = $('#product_dropdown').val();
                $("#add_edit_flag").val("0");
                $("#product_price_id").val("0");

                if(myId=='' || myId==null){
                    myId = $('#product_id').val();
                }

                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    type: "GET",
                    url: '/pricing/getproductbyid/' + myId,
                    success: function (data)
                    {

                        if(data){
                            var productID = data[0].product_id;
                            $("#add_prd_id").val(data[0].product_id);
                            $("#prd_name").html(data[0].product_title);
                            $("#product_name").val(data[0].product_title);
                            $("#cashback_product_id").val(data[0].product_id);
                            $("#product_name_heading").html('[ '+data[0].product_title+'  -  ' +data[0].sku+ ' ]');
                            $("#mrp").html(data[0].mrp);
                            $("#ptr").html(data[0].rlp);
                            $( "#add_dc" ).prop( "disabled", false );
                            $( "#add_custgroup" ).prop( "disabled", false );
                            var markupTXT = "";
                            if(data[0].is_markup==0){
                                markupTXT = "Mark Down";
                            }else{
                                markupTXT = "Mark Up";
                            }
                            $('#margin_type').html(markupTXT);
                            $("#add_dc").val('');
                            $("#add_custgroup").val('');
                            $("#selling_price").val('');
                            $("#price_ptr").val('');

                            // Revalidating because if user fill all the details and click outside and 
                            // again click on add, then we need to recheck everything
                            $('#price-save-button').click(function(){
                                $('#frm_price_tmpl').formValidation('revalidateField', 'add_dc');
                                $('#frm_price_tmpl').formValidation('revalidateField', 'add_custgroup');
                                $('#frm_price_tmpl').formValidation('revalidateField', 'date');
                                $('#frm_price_tmpl').formValidation('revalidateField', 'selling_price');
                                $('#frm_price_tmpl').formValidation('revalidateField', 'price_ptr');
                            });
                        }


                    }
                });
            }
        }
    });
});


$('#cashback_form').formValidation({
        message: 'This value is not valid',
        icon: {
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            
            cashback_quantity: {
                validators: {
                    notEmpty: {
                        message: 'Select Quantity'
                    }
                }
            },
            
            offer_value: {
                validators: {
                    notEmpty: {
                        message: 'Select Offer Value'
                    }
                }
            },
            cashback_start_date: {
                validators: {
                    notEmpty: {
                        message: 'Select Start date'
                    },
                    date: {
                        format: 'YYYY-MM-DD',
                        message: 'Effective Date is not a valid'
                    }
                }
            },
            cashback_end_date: {
                validators: {
                    notEmpty: {
                        message: 'Select End date'
                    },

                    date: {
                        format: 'YYYY-MM-DD',
                        message: 'Effective Date is not a valid'
                    }
                }
            },
            cashback_product_star: {
                validators: {
                    notEmpty: {
                        message: 'Select Star'
                    },
                }
            },
            cashback_text: {
                validators: {
                    notEmpty: {
                        message: 'Enter description'
                    },
                }
            },
            
        }
})
.on('success.form.fv', function(e){
    e.preventDefault();
    hiddenid  = $("#cashback_ref_id").val();
    var frmData = $('#cashback_form').serialize();
    var token  = $("#csrf-token").val();



    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/pricing/savecashbackdata',
        data: frmData,
        success: function (respData)
        {

            if(respData.cashBackrefId !="none"){
                if(respData.benificiary_type == ""){
                   var benificiary_type = "All"; 
                }else{
                   var benificiary_type = $('#cashback_for option:selected').text(); 
                }
                if(respData.state_id == ""){
                   var state_id = "All"; 
                }else{
                   var state_id = $('#cashback_state option:selected').text(); 
                }
                if(respData.customer_type == ""){
                   var customer_type = "All"; 
                }else{
                   var customer_type = $('#cashback_custgroup option:selected').text(); 
                }
                if(respData.product_star == ""){
                   var product_star = "All"; 
                }else{
                   var product_star = $("#product_star option[value="+respData.product_star+"]").text(); 
                }
                if(respData.wh_id == ""){
                   var wh_id = "All"; 
                }else{
                   var wh_id = $('#cashback_warehouse option:selected').text(); 
                }
                var tableRow = tableRow + '<tr name="cashback_tr"class="gradeXSlab odd list-head">\
                                                <td id="description">'+respData.cbk_label+'</td>\
                                                <td id="benificiary">'+benificiary_type+'</td>\
                                                <td id="product_star_td">'+product_star+'</td>\
                                                <td id="start_date">'+$('#cashback_start_date').val()+'</td>\
                                                <td id="end_date">'+$('#cashback_end_date').val()+'</td>\
                                                <td id="state">'+state_id+'</td>\
                                                <td id="cust_group">'+customer_type+'</td>\
                                                <td id="offer_value">'+respData.cbk_value+'</td>\
                                                <td id="quantity">'+respData.range_to+'</td>\
                                                <td id="warehouse">'+wh_id+'</td>\
                                                <td id="cbk_type">'+respData.cbk_type+'</td>\
                                                <td ><a class="btn btn-icon-only default delcondition" onclick="DeleteCashBack('+respData.cashBackrefId+',this)"><i class="fa fa-trash-o"></i></a></td>\
                                            </tr>';

                $('#cashback_table').append(tableRow);
                $("#cashback_response").html(respData.cashbackResponse).css({"color":"green","font-weight":"bold"});

                $("#cashback_start_date").val('');
                $("#cashback_end_date").val('');
                $("#cashback_quantity").val('');
                $("#cashback_warehouse").val('');
                $("#cashback_state").val('');
                $("#cashback_custgroup").val('');
                $("#cashback_for").val('');
                $("#offer_value").val('');
                $("#cashback_product_star").val('');
                $("#cashback_text").val('');

                $('#is_percent').prop('checked',false);

                $('#cashback_form').formValidation('resetField', 'cashback_start_date');
                $('#cashback_form').formValidation('resetField', 'cashback_end_date');
                $('#cashback_form').formValidation('resetField', 'offer_value');
                $('#cashback_form').formValidation('resetField', 'cashback_quantity');
                $('#cashback_form').formValidation('resetField', 'cashback_product_star');
                $('#cashback_form').formValidation('resetField', 'cashback_text');
            }else{
                $("#cashback_response").html(respData.cashbackResponse).css({"color": "red","font-weight":"bold"});
            }


        }
    });
});


function DeleteCashBack(refId,element){
    token  = $("#csrf-token").val();
        var promotion_delete = confirm("Are you sure you want to delete this cashback Data ?"), self = $(this);
            if ( promotion_delete == true )
            {
              $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/pricing/deletecashbackdata',
        data: 'refId='+refId,
        success: function (respData)
        {
                
                if(respData == 1){
                    $(element).closest('tr').remove();
                    $("#cashback_response").html('Cashback deleted successfully!').css({"color": "red","font-weight":"bold"});
                }

            
        
        }
    });
        }
    

}
$("#cashback_quantity").keydown(function (event) {
    allowNumber(event);
 });
$("#offer_value").keydown(function (event) {
    allowNumber(event);
 });
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

});

//update the price (for price)
function updatePriceData(priceID){
    updatePriceID = priceID;
    $('#save_price').modal('toggle');
    $('#cashback_tab').removeClass("disabled");
    $('#cashback_tab_').attr('data-toggle','tab')
    $("#cashback_table tbody").empty();
    


}


//load right side data in price
function loadRightSideData(){

    var trData = '';
    var productID = $("#add_prd_id").val();
    //var stateID = $("#add_state").val();
    var token  = $("#csrf-token").val();
    var esp = parseFloat($("#selling_price").val()).toFixed(2);
    var mrp = parseFloat($("#mrp").text()).toFixed(2);

    var DCID = $("#add_dc").val();

    // markup and down calculation
    var marginVal = "0.00";
    if( $("#margin_type").text() == "Mark Down" ){
        marginVal = (mrp - esp) / mrp;
    }else if( $("#margin_type").text() == "Mark Up" ){
        marginVal = (mrp - esp) / esp;
    }else{
        marginVal = "0.00";
    }
    marginVal = marginVal*100;
    marginVal = parseFloat(marginVal).toFixed(2);
    $("#margin_val").text(marginVal + "%");

    $("#price_details").empty();

    trData = "<thead>\
        <tr>\
            <th>Price Type</th>\
            <th>State Billing</th>\
            <th>Inter State Billing</th>\
        </tr>\
    </thead>";


    $("#price_details").append(trData);
    
    if(productID!="" && esp!="" && Number(esp)){
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            //url: '/pricing/getrightsideinfo/' + productID + "/" + stateID,
            url: '/pricing/getrightsideinfo/' + productID + "/" + DCID,
            success: function (data)
            {
                //alert(JSON.stringify(data));
                if(data && data!=''){

                    if(data=='no tax'){
                        trData = "<tr>\
                            <td colspan=3><center>Tax Not Found</center></td>\
                        </tr>";
                        $("#price_details").append(trData);
                        $("#product_tax_flag").val("0");
                        }else{

                        data = data.split("==!!==");
                        // calculate for VAT
                        var taxRateVAT = parseFloat(data[0]);
                        //var taxAmountVAT = (esp*taxRateVAT) / 100;
                        //taxAmountVAT = parseFloat(taxAmountVAT).toFixed(5);
                        //var BasePriceVAT = esp - taxAmountVAT;
                        console.log(data);
                        taxRateVAT = isNaN(taxRateVAT) ? '0.00' : taxRateVAT;
                        BasePriceVAT = '0.00';
                        taxAmountVAT = '0.00';
                        if(taxRateVAT > 0){
                            var BasePriceVAT = (esp / (100 + taxRateVAT)) * 100;
                            BasePriceVAT = parseFloat(BasePriceVAT).toFixed(5);

                            BasePriceVAT = isNaN(BasePriceVAT) ? '0.00' : BasePriceVAT;

                            var taxAmountVAT = esp - BasePriceVAT;
                            taxAmountVAT = parseFloat(taxAmountVAT).toFixed(5);

                            taxAmountVAT = isNaN(taxAmountVAT) ? '0.00' : taxAmountVAT;
                        }

                        // calculate the CGST & SGST Tax val
                        var taxCGST = parseFloat(data[2]);
                        var totalCGST = 0;
                        if(taxCGST!=0){
                            totalCGST = (taxAmountVAT * parseFloat(taxCGST)) / 100; 
                            totalCGST = parseFloat(totalCGST).toFixed(5);

                            totalCGST = isNaN(totalCGST) ? '0.00' : totalCGST;

                        }
                        var taxSGST = parseFloat(data[3]);
                        var totalSGST = 0;
                        if(taxSGST!=0){
                            totalSGST = (taxAmountVAT * parseFloat(taxSGST)) / 100;
                            totalSGST = parseFloat(totalSGST).toFixed(5);

                            totalSGST = isNaN(totalSGST) ? '0.00' : totalSGST;
                        }
                        
                        // calculate for CST
                        var taxRateCST = parseFloat(data[1]);
                        //var taxAmountCST = (esp*taxRateCST) / 100;
                        //taxAmountCST = parseFloat(taxAmountCST).toFixed(5);
                        //var BasePriceCST = esp - taxAmountCST;
                        taxRateCST = isNaN(taxRateCST) ? '0.00' : taxRateCST;
                        BasePriceCST = "0.00";
                        taxAmountCST = "0.00"
                        if(taxRateCST > 0){
                            var BasePriceCST = (esp / (100 + taxRateCST)) * 100;
                            BasePriceCST = parseFloat(BasePriceCST).toFixed(5);
                            BasePriceCST = isNaN(BasePriceCST) ? '0.00' : BasePriceCST;


                            var taxAmountCST = esp - BasePriceCST;
                            taxAmountCST = parseFloat(taxAmountCST).toFixed(5);
                            taxAmountCST = isNaN(taxAmountCST) ? '0.00' : taxAmountCST;
                        }


                         // calculate for CST
                        var taxRateUTST = parseFloat(data[4]);
                        //var taxAmountCST = (esp*taxRateCST) / 100;
                        //taxAmountCST = parseFloat(taxAmountCST).toFixed(5);
                        //var BasePriceCST = esp - taxAmountCST;
                        taxRateUTST = isNaN(taxRateUTST) ? '0.00' : taxRateUTST;
                        taxAmountUTST = "0.00";
                        BasePriceUTGST = "0.00";
                        if(taxRateUTST > 0){

                            var BasePriceUTGST = (esp / (100 + taxRateUTST)) * 100;
                            BasePriceUTGST = parseFloat(BasePriceUTGST).toFixed(5);
                            BasePriceUTGST = isNaN(BasePriceUTGST) ? '0.00' : BasePriceUTGST;

                            var taxAmountUTST = esp - BasePriceUTGST;
                            taxAmountUTST = parseFloat(taxAmountUTST).toFixed(5);
                            taxAmountUTST = isNaN(taxAmountUTST) ? '0.00' : taxAmountUTST;
                            taxRateCST = taxRateUTST;
                            BasePriceCST = BasePriceUTGST;
                        }
                        trData = "<tr>\
                            <td>Base Price</td>\
                            <td>"+BasePriceVAT+"</td>\
                            <td>"+BasePriceCST+"</td>\
                        </tr>\
                        <tr>\
                            <td>Tax Amount</td>\
                            <td><div><table class='table table-striped table-bordered table-hover table-advance' style='font-size:12px;'><tr><td>CGST</td><td>SGST</td><td>Total</td></tr><tr><td>"+totalCGST+"</td><td>"+totalSGST+"</td><td>"+taxAmountVAT+"</td></tr></table></div></td>\
                            <td><table class='table table-striped table-bordered table-hover table-advance' style='font-size:12px;'><tr><td>IGST</td><td>UTGST</td></tr><tr><td>"+taxAmountCST+"</td><td>"+taxAmountUTST+"</td></tr></table></td>\
                        </tr>\
                        <tr>\
                            <td>Tax Rate</td>\
                            <td>"+taxRateVAT+"</td>\
                            <td>"+taxRateCST+"</td>\
                        </tr>\
                        <tr>\
                            <td>Effective Price</td>\
                            <td>"+esp+"</td>\
                            <td>"+esp+"</td>\
                        </tr>"
                        $("#price_details").append(trData);
                        $("#product_tax_flag").val("1");
                    }
                    
                }    
            }
        });
    }
}