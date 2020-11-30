$(function () {
     //for date 
    var date = new Date();
    $('#date').datepicker({
        dateFormat: 'yy-mm-dd'
    });

    var date = new Date();
    $('#allocation_date').datepicker({
        dateFormat: 'yy/mm/dd',
        onSelect: function(datesel) {
            $('#allocate_data').formValidation('revalidateField', 'allocation_date');
        }
    });

    $('#import_asset_button').click(function () {
    
        token  = $("#csrf-token").val();
        var stn_Doc = $("#import_asset_file")[0].files[0];
        var formData = new FormData();
        formData.append('asset_data', stn_Doc);
        $.ajax({
            type: "POST",
            headers: {'X-CSRF-TOKEN': token},
            url: "/assets/importasset",
            data: formData,
            processData: false,
            contentType: false,
            success: function (data){

            $('#import_asset').modal('toggle');
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data+'</div></div>' );
            $(".alert-success").fadeOut(80000)
                
            }
        });
    });
    
    // Load Grid Data 
    AssetGrid();
});

function calculatedate(){

    var year =0;
    if($("#year").val()!="" && $("#year").val()!==null ){
        year = $("#year").val();
    }
    var month =0;
    if($("#month").val()!="" && $("#month").val()!==null ){
        month = $("#month").val();
    }
    

    var totMonth = (year * 12) + parseInt(month);

    var purchasedate = $("#purchase_date").val();

    purchasedate = purchasedate.split("-");

    var convertedDate = new Date(purchasedate[0], purchasedate[1], purchasedate[2]);

    var totWarranty = convertedDate.setMonth(convertedDate.getMonth()+totMonth);


    totWarranty = convertedDate.getTime();

    //alert(convertedDate.getFullYear() + "-" + convertedDate.getMonth() + "-" + convertedDate.getDate());
    var getMonth = 12;
    var fullYear = convertedDate.getFullYear();
    if(convertedDate.getMonth() != 0){
        getMonth = convertedDate.getMonth();
    }else{
        fullYear = fullYear-1;
    }
    
    $("#update_warranty_amc_date").val( fullYear + "-" + getMonth + "-" + convertedDate.getDate());
}


function calculatedepresiation(){
    var month =0;
    if($("#depresiation_age").val()!=""){
        month = $("#depresiation_age").val();
    }
    var mrp = $("#asset_mrp").val();

    var totMonth =  parseInt(month*12);
    var purchasedate = $("#purchase_date").val();
    purchasedate = purchasedate.split("-");
    var convertedDate = new Date(purchasedate[0], purchasedate[1], purchasedate[2]);

    var residual_value=parseInt(mrp*5/100);

    var amount = parseInt(mrp) / parseInt(month);

    var totWarranty = convertedDate.setMonth(convertedDate.getMonth()+totMonth);


    if(convertedDate.getMonth() != 0){
        $("#depresiation_date").val(convertedDate.getFullYear() + "-" + convertedDate.getMonth() + "-" + convertedDate.getDate());
    }else{
        var getMonth = 12;
        $("#depresiation_date").val(convertedDate.getFullYear()-1 + "-" + getMonth + "-" + convertedDate.getDate());
    }


   // $("#depresiation_date").val(convertedDate.getFullYear() + "-" + convertedDate.getMonth() + "-" + convertedDate.getDate());

    $("#depression_amount").val(residual_value);


}

function loadDatatype(select)
{

    //alert(select.value);

    // For allocation
    if (select.value == '0') {  

        $("#allocate_to_lbl").html("Allocate To");
        $("#allocation_date_lbl").html("Allocate Date");
        $('.allocation').show(); 

    // for Repair
    } else if ( select.value == '1') {

        $("#allocate_to_lbl").html("De-Allocate To");
        $("#allocation_date_lbl").html("De-Allocate Date");
        $('.allocation').show();

    // For De-Allocation
    }else if(select.value=='2'){
        
        $("#allocate_to_lbl").html("Repair To");
        $("#allocation_date_lbl").html("Repair Date");
        $('.allocation').show();
  
    // Defalut show nothing
    }else{
        $('.allocation').hide();  
        $('.repair').hide();
    }
}


// for show the grid
function AssetGrid()
{  
       $("#assetsdashboardgrid").igHierarchicalGrid({
            dataSource: '/assets/assetData',
           dataSourceType: "json",
            responseDataKey: "result",
            initialDataBindDepth: 1,
            autoGenerateColumns: false,
            primaryKey: "product_id",
            height:"100%",
            dataRendered: function() {           

               
            },
            columns: [
                {headerText: "product id", key: "product_id", dataType: "number",width: "5%", hidden: true},
                { headerText: "Product Name", key: "AssetDetails", dataType: "string", width: "25%" },
                {headerText:"Movable",key:"is_movable",dataType:"string",width:"10%"},
                { headerText: "Total Assets", key: "TotalAsset", dataType: "number",columnCssClass: "alignRight",headerCssClass: "alignRight", width: "10%" },
                { headerText: "Allocated", key: "TotalAllocated", dataType: "number",columnCssClass: "alignRight",headerCssClass: "alignRight", width: "10%" },
                { headerText: "Repaired", key: "TotalRepaired", dataType: "number",columnCssClass: "alignRight",headerCssClass: "alignRight", width: "10%" },
                { headerText: "Available", key: "TotalAvail", dataType: "number",columnCssClass: "alignRight",headerCssClass: "alignRight", width: "10%" },
                { headerText: "Warranty", key: "TotalWarranty", dataType: "number",columnCssClass: "alignRight",headerCssClass: "alignRight", width: "10%" },
                { headerText: "Out Of Warranty", key: "TotalOutOfWarranty", dataType: "number",columnCssClass: "alignRight",headerCssClass: "alignRight", width: "10%" },
                { headerText: "Asset Category", key: "AssetCategory", dataType: "string", width: "10%"},
                { headerText: "Asset Value", key: "AssetCatMrp", dataType: "number",columnCssClass: "alignRight",headerCssClass: "alignRight",  width: "15%" },  
                { headerText: "Actions", key: "CustomAction", dataType: "string", width: "10%"},
            ],                
                
             features: [               

                 {
                    name: "Sorting",
                    type: "remote",
                    columnSettings: [
                    {columnKey: 'CustomAction', allowSorting: false },
                    {columnKey: 'AssetDetails', allowSorting: true },
                    {columnKey: 'TotalAsset', allowSorting: true },
                    {columnKey: 'TotalWarranty', allowSorting: true },
                    {columnKey: 'AssetCategory', allowSorting: true },
                    {columnKey: 'AssetCatMrp',allowSorting:true},
                    {columnKey: 'is_movable',allowSorting:true},

                    ]
                },
                {
                    name: "Filtering",
                    type: "remote",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'CustomAction', allowFiltering: false },
                        {columnKey: 'AssetDetails', allowFiltering: true },
                        {columnKey: 'TotalAsset', allowFiltering: true },
                        {columnKey: 'TotalWarranty', allowFiltering: true },
                        {columnKey: 'AssetCategory', allowFiltering: true },
                        {columnKey: 'AssetCatMrp',allowFiltering:true},
                        {columnKey: 'is_movable',allowFiltering:true},

                    ]
                },

                {
                   name: "Summaries",
                   columnSettings:  [
                        {columnKey: "product_id", allowSummaries: false},
                        {columnKey: "AssetDetails", allowSummaries: false},
                        {columnKey: "is_movable", allowSummaries: false},
                        {columnKey: "TotalAsset",allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                        {columnKey: "TotalAllocated",allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                        {columnKey: "TotalRepaired",allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                        {columnKey: "TotalAvail",allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                        {columnKey: "TotalWarranty",allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                        {columnKey: "TotalOutOfWarranty", allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                        {columnKey: "AssetCategory", allowSummaries: false},
                        {columnKey: "AssetCatMrp",allowSummaries: true, summaryOperands: [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                        {columnKey: "CustomAction", allowSummaries: false},
                       
                        
                    ]

                },
                { 
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 10,
                    name: 'Paging', 
                    loadTrigger: 'auto', 
                    type: 'local' 
                }
                
            ],
            autoGenerateLayouts: false,
                columnLayouts: [{
                    key: "asset_details",
                    responseDataKey: "results",
                    autoGenerateColumns: false,
                    primaryKey: "product_id",
                    foreignKey: "asset_id",
                    columns: [
                        { headerText: "Asset Code", key: "company_asset_code", dataType: "string", width: "13%" },
                        { headerText: "Purchase Date", key: "purchase_date", dataType: "date",format:"dd-MM-yyyy", width: "10%" },
                        { headerText: "Asset Value", key: "mrp", dataType: "string", width: "10%" },
                        { headerText: "Invoice Number", key: "invoice_number", dataType: "int", width: "10%" },
                        { headerText: "Serial Number", key: "serial_number", dataType: "int", width: "15%" },
                        { headerText: "Status", key: "AssetStatus", dataType: "int", width: "10%" },
                        { headerText: "Allocted Date", key: "asset_allocated_date", dataType: "date",format:"dd-MM-yyyy",width: "10%" },
                        { headerText: "Allocated To", key: "allocated_to_name", dataType: "string", width: "10%" },
                        { headerText: "Deprc date", key: "depresiation_date", dataType: "date", width: "10%",format:"dd-MM-yyyy" },
                        { headerText: "Deprc Age", key: "depresiation_month", dataType: "int", width: "10%" },
                        { headerText: "Residual Value", key: "depresiation_per_month", dataType: "int", width: "10%" },
                        { headerText: "Actions", key: "CustomAction", dataType: "string", width: "10%"},
                    ]
                }]
            
        });

        $("#assetsdashboardgrid").on("iggriddatarendered", function (event, args) {
             $("#assetsdashboardgrid_summaries_footer_row_icon_container_sum_TotalAsset, "+
            "#assetsdashboardgrid_summaries_footer_row_icon_container_sum_TotalAllocated, "+
            "#assetsdashboardgrid_summaries_footer_row_icon_container_sum_TotalRepaired, "+
            "#assetsdashboardgrid_summaries_footer_row_icon_container_sum_TotalAvail, "+
            "#assetsdashboardgrid_summaries_footer_row_icon_container_sum_TotalWarranty, "+
            "#assetsdashboardgrid_summaries_footer_row_icon_container_sum_AssetCatMrp, "+
            "#assetsdashboardgrid_summaries_footer_row_icon_container_sum_TotalOutOfWarranty"
            ).remove();

        var id_text = "#assetsdashboardgrid_summaries_footer_row_text_container_sum_"; 
        $(id_text+"TotalAsset").attr("class","summariesStyle").text($(id_text+"TotalAsset").text().replace(/\s=\s/g, ''));
        $(id_text+"TotalAllocated").attr("class","summariesStyle").text($(id_text+"TotalAllocated").text().replace(/\s=\s/g, ''));
        $(id_text+"TotalRepaired").attr("class","summariesStyle").text($(id_text+"TotalRepaired").text().replace(/\s=\s/g, ''));
        $(id_text+"TotalAvail").attr("class","summariesStyle").text($(id_text+"TotalAvail").text().replace(/\s=\s/g, ''));
        $(id_text+"TotalWarranty").attr("class","summariesStyle").text($(id_text+"TotalWarranty").text().replace(/\s=\s/g, ''));
        $(id_text+"TotalOutOfWarranty").attr("class","summariesStyle").text($(id_text+"TotalOutOfWarranty").text().replace(/\s=\s/g, ''));
        $(id_text+"AssetCatMrp").attr("class","summariesStyle").text($(id_text+"AssetCatMrp").text().replace(/\s=\s/g, ''));
        });
}

// Reload the grid after some action with updated Data
function reloadGridData(){

    $("#assetsdashboardgrid").igGrid("dataBind");
}


// ajax search by name
$("#allocate_to").autocomplete({
        minLength:2,
        source: '/assets/getuserlist',  
        select: function (event, ui) {
            var label = ui.item.label;
            var firstname = ui.item.firstname;
            var user_id = ui.item.user_id;
            $("#asset_user_id").val(user_id);
        }
});  

// Ajax call for Asset History
function viewAsset(id){

    $('#view-asset-document').modal('toggle');

    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/assets/getassethistorydetailsbyid/' + id,
        success: function (data)
        {
            $('#historyContainerData').html(data.historyHTML);

            $('#sr_no').html(data.srno);

            $('#assetproduct_name').html(data.assetname);

            $('#prof_image').attr('src', data.profImage);
            $('#a_prof_image').attr('href', data.profImage);
            

        }
    });
}

function loadBrand(){
    var manufac = $("#mdl_manufac").val();

    if( manufac == ""){
        $('#mdl_brand').empty();
    }

    token  = $("#csrf-token").val(); 
    $('#mdl_brand').val('');
    // prepare the ajax call to get the brand information
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/assets/getbrandsasmanufac/'+manufac,
        success: function( data ) {
                if(data){
                    var brand = $('#mdl_brand');
                    brand.find('option').remove().end();
                    brand.append(
                            $('<option></option>').val('all').html("All")
                        );
                    for(var i=0; i<data.length; i++){
                        brand.append(
                            $('<option></option>').val(data[i].brand_id).html(data[i].brand_name)
                        );
                    }
                }
                $('#mdl_brand').val('');
            }
    });
}




function loadBrandInModal(){
    var manufac = $("#exp_manufac").val();

    if( manufac == ""){
        $('#exp_brand').empty();
    }

    token  = $("#csrf-token").val(); 
    $('#exp_brand').val('');
    // prepare the ajax call to get the brand information
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/assets/getbrandsasmanufac/'+manufac,
            success: function( data ) {
                    if(data){
                        var brand = $('#exp_brand');
                        brand.find('option').remove().end();
                        brand.append(
                                $('<option></option>').val('all').html("All")
                            );
                        for(var i=0; i<data.length; i++){
                            brand.append(
                                $('<option></option>').val(data[i].brand_id).html(data[i].brand_name)
                            );
                        }
                    }
                    $('#exp_brand').val('');
                }
        });
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

$("#prd_mrp").keydown(function (event) {
            allowNumber(event);
});


function updateAsset(id){
    var update_product_id  = document.getElementById('update_product_id').value = id;
    $("#update-document_asset").modal('toggle');
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/assets/getdetailsfromassets/'+id,
        success: function (data)
        {
           

            var checkbox = $('input:checkbox[name=is_active_asset]');
            checkbox.filter('[value=' + data[0].isactive + ']').prop('checked', true);
            $("#update_asset_name").val(data[0].product_title);
            $("#asset_id").val(data[0].asset_id);
            $("#update_company_asset_code").val(data[0].company_asset_code);
            $("#update_asset_user_id").val(data[0].allocated_to);
            $("#update_allocate_to").val(data[0].UserName);
            $("#purchase_date").val(data[0].purchase_date);
            $("#update_invoice_number").val(data[0].invoice_number);
            $("#update_serial_no").val(data[0].serial_number);
            $("#asset_mrp").val(data[0].mrp);
            $("#update_business_unit").val(data[0].business_unit_id);

            $("#depresiation_age").val(data[0].depresiation_month);
            $("#depresiation_date").val(data[0].depresiation_date);
            $("#depression_amount").val(data[0].depresiation_per_month);
            $("#asset_category_id").val(data[0].asset_category);
            $("#product_id_update").val(data[0].product_id);


            var warrantyRadio = $('input:radio[name=update_warranty]');
            warrantyRadio.filter('[value=' + data[0].warranty_status + ']').prop('checked', true);

            $("#update_warranty_amc_date").val(data[0].warranty_end_date);

            $("#year").val(data[0].warranty_year);
            $("#month").val(data[0].warranty_month);

            var radios = $('input:radio[name=update_is_working]');
            radios.filter('[value=' + data[0].is_working + ']').prop('checked', true);
            $("#update_notes").val(data[0].notes);

            $('#update_asset_data').formValidation('revalidateField', 'update_serial_no');

        }
    });
}

function allocateasset(id){

    $('#select_part').val(0);
    $("#allocate_to_lbl").html("Allocate To");
    $("#allocation_date_lbl").html("Allocate Date");

    $("#allocate_asset").modal('toggle');

    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/assets/getinwardproducttabledatawithid/'+id,
        success: function (data)
        {

            //alert(JSON.stringify(data));
            $("#allocate_to").val("");
            $("#allocation_date").val("");

            
            $("#allocation_comment").val("");
            $("#repair_to").val("");$("#repair_date").val("");$("#repair_comment").val("");

            $("#add_asset_name").val(data.productData[0].product_title);
            $("#company_asset_code").val(data.productData[0].company_asset_code);
            $("#invoice_number").val(data.productData[0].invoice_number);
            $("#date").val(data.productData[0].purchase_date);
            $("#hidden_product_id").val(data.productData[0].product_id);
            $("#hidden_asset_id").val(data.productData[0].asset_id);

            if(data.productCount==0 && data.productData[0].is_manual_import==0){

                $("#htmldata").html("Product Not Purchased");
                $("#asset-save-button").hide();
                $(".allocation").hide();

            }else{

                $('.changetype').show();
                $(".allocation").show();
                $("#htmldata").hide();
                $("#asset-save-button").show();
            
            }

            $('#allocate_data').formValidation('resetField', 'allocate_to');
            $('#allocate_data').formValidation('resetField', 'repair_to');
            $('#allocate_data').formValidation('resetField', 'allocation_date');
            $('#allocate_data').formValidation('resetField', 'repair_date');
        }
    });
}


function loaddata(){

    var category = $("#exp_category").val();
    var brand = $("#exp_brand").val();
    if(category == ""){
        category = "0";

    }
    if(brand==""){
        brand = "0";
    }

  if( category == ""){
        $('#exp_asset_name').empty();
    }

    token  = $("#csrf-token").val(); 
        $('#exp_asset_name').val('');

    // prepare the ajax call to get the brand information
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/assets/loadproductinlist/'+category+'/'+brand,

        success: function( data ) {
                if(data){
                    var brand = $('#exp_asset_name');
                    brand.find('option').remove().end();
                    
                    for(var i=0; i<data.length; i++){
                        brand.append(
                            $('<option></option>').val(data[i].product_id).html(data[i].product_title)
                        );
                    }
                }
                $('#exp_asset_name').val('');
            }
    });
}

function depreciationdata(){

    var category = $("#dep_category").val();

    var brand ="";
    if(category==""){
        category = "0";
    }
    if(brand==""){
        brand = "0";
    }

    if( category == ""){
        $('#dep_asset_name').empty();
    }

    token  = $("#csrf-token").val(); 
    $('#dep_asset_name').val('');

    // prepare the ajax call to get the brand information
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "GET",
        url: '/assets/loadproductinlist/'+category+'/'+brand,

        success: function( data ) {
                if(data){
                    var brand = $('#dep_asset_name');
                    brand.find('option').remove().end();
                    
                    for(var i=0; i<data.length; i++){
                        brand.append(
                            $('<option></option>').val(data[i].product_id).html(data[i].product_title)
                        );
                    }
                }
                $('#dep_asset_name').val('');
            }
    });
}


$('#allocate_data').formValidation({

    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        allocate_to: {
            validators: {
                notEmpty: {
                    message: 'Please select Allocate name '
                }
            }
        },

         allocation_date: {
            validators: {
                    notEmpty: {
                        message: 'Allocate date is required',
                    },
                    date: {
                        format: 'YYYY/MM/DD',
                        message: 'Allocation Date is not a valid'
                    }
                }
        },
        
    }

}).on('success.form.fv', function(e){
    
    e.preventDefault();
    var frmData = $('#allocate_data').serialize();
    var token  = $("#csrf-token").val();


    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
       
        url: '/assets/saveallocatedata',
        data: frmData, 
        success: function (respData)
        {
           
            $('#allocate_asset').modal('toggle');
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+respData+'</div></div>' );
            $(".alert-success").fadeOut(20000);

            reloadGridData();
            
        }
    });
});

$(".modal").on('hidden.bs.modal', function () {

        $('#allocate_data').formValidation('resetForm', true);

        //Removing the error elements from the from-group
        $('.form-group').removeClass('has-error has-feedback');
        $('.form-group').find('small.help-block').hide();
        $('.form-group').find('i.form-control-feedback').hide();
        $('#mdl_brand').empty();
});

$('#add_asset_product').on('show.bs.modal', function (e) {



    // Clear out the fields
    $("#business_unit_asset").select2('val', '');
    $("#mdl_category").select2('val', '');
    $("#mdl_manufac").select2('val', '');
    $("#mdl_brand").select2('val', '');
    $("#asset_name").val("");
    $("#asset_quantity").val("");
    $("#proof_image").val("");
    $("#prd_mrp").val("");
    $("#asset_type").select2('val','');
    $("#ast_category").select2('val','');
    $("#asset_type").select2('val','');

    // Removing the success Class (Basically removing the Green color)
    $("#feidls_revalid").removeClass('has-success');
    $("#feidls_revalid1").removeClass('has-success');
    $("#feidls_revalid2").removeClass('has-success');
    $("#feidls_revalid3").removeClass('has-success');
    $("#feidls_revalid4").removeClass('has-success');
    $("#feidls_revalid5").removeClass('has-success');
    $("#fields_revalid6").removeClass('has-success');
    $("#fields_revalid7").removeClass('has-success');
    $("#fields_revalid8").removeClass('has-success');

    $("#add_refresh").removeClass('fa-refresh');
    $("#add_refresh").addClass('fa-plus');
    
    // Revalidating the field
    $("#addAsset_revalid").click(function(){
        $('#save_asset_product').formValidation('revalidateField', 'mdl_manufac');
    });
});


$('#import_asset').on('show.bs.modal', function (e) {
    $("#import_asset_file").val('');
    $("#feidls_revalid").removeClass('has-success');
    
});

$('#upload-document-asset-download').on('show.bs.modal', function (e) {
    $("#exp_manufac").select2('val', '');$("#exp_asset_name").select2('val', '');$("#exp_category").select2('val', '');$("#exp_brand").select2('val', '');
    $("#feidls_revalid").removeClass('has-success');
     $("#exp_brand").empty();
    
});

$('#download-depreciation_data').on('show.bs.modal', function (e) {
    $("#dep_category").select2('val', '');
    $("#feidls_revalid").removeClass('has-success');
    $("#dep_asset_name").empty();
    $('#dep_asset_name').select2('val','');
    $('#field_category').removeClass('has-success');
    $('#field_asset').removeClass('has-success');


    
    
});

$('#allocate_asset').on('show.bs.modal', function (e) {
        $("#allocate_to").val("");
        $("#asset_user_id").val("");
});



$('#downloadexcel_asset').formValidation({

     fields: {
        dep_category: {
                validators: {
                    notEmpty: {
                        message: ' Please Select Category'
                    }
                }
            },

        dep_asset_name: {
            validators: {
                notEmpty: {
                    message: ' Please Select Asset name'
                }
            }
        }
    }

})
//form validation and save the data into main table
$('#save_asset_product').formValidation({

    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        mdl_manufac: {
                validators: {
                    notEmpty: {
                        message: ' Please Select Manufacturer'
                    }
                }
            },

            mdl_brand: {
                validators: {
                    notEmpty: {
                        message: ' Please Select Brand'
                    }
                }
            },

             mdl_category: {
                validators: {
                    notEmpty: {
                        message: ' Please Select Category'
                    }
                }
            },

            asset_name: {
                validators: {
                    notEmpty: {
                        message: ' Please Enter Product Name'
                    }
                }
            },

            asset_quantity: {
                validators: {
                    notEmpty: {
                        message: ' Please Select The Quantity'
                    }
                }
            },

            business_unit_asset:{
                validators:{
                     notEmpty: {
                        message: ' Please Select The business unit'
                    }
                }
            },

            prd_mrp: {
                validators: {
                    notEmpty: {
                        message: ' Enter Price'
                    }
                }
            },
            ast_category:{
                validators:{
                    notEmpty:{
                        message:'Please select asset category'
                    }
                }
            },
            asset_type:{
                validators:{
                    notEmpty:{
                        message:'Please select asset type'
                    }
                }
            },
        
    }
}).on('success.form.fv', function(e){
    e.preventDefault();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        processData: false,
        contentType: false,
        url: '/assets/saveassetintoproductstable',
        data: new FormData($("#save_asset_product")[0]),
        success: function (respData)
        {
            $('#add_asset_product').modal('toggle');
            reloadGridData();
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+respData+'</div></div>' );
            $(".alert-success").fadeOut(60000)
            
        }
    });
});


//form validation and save the data into main table
$('#update_asset_data').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
    }
}).on('success.form.fv', function(e){
    e.preventDefault();

    var frmData = $('#update_asset_data').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/assets/updateassetdata',
        data: frmData,
        success: function (respData)
        {
            $('#update-document_asset').modal('toggle');
            reloadGridData();
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+respData+'</div></div>' );
            $(".alert-success").fadeOut(20000)
            
        }
    });
});

// Write Function for Add and Refresh
function func_add_refresh(){

    var token  = $("#csrf-token").val();
    var class_name = $('#add_refresh').attr('class');
    var env = "http://"+window.location.hostname;
    if(class_name == 'fa fa-plus'){ 
 
        $( "#add_refresh" ).removeClass( 'fa fa-plus' );
        $('#add_refresh').addClass('fa fa-refresh');

        window.open(''+env+'/brands/add#add_manu','', "width=600,height=600");

    }else if(class_name == 'fa fa-refresh'){

        $( "#add_refresh" ).removeClass( 'fa fa-refresh' );
        $('#add_refresh').addClass('fa fa-plus');
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "get",
            url: '/assets/loadmanufacturedata',
            success: function (data)
            {
                if(data){
                    var manufacture = $('#mdl_manufac');
                    manufacture.find('option').remove().end();
                    
                    for(var i=0; i<data.length; i++){
                        manufacture.append(
                            $('<option></option>').val(data[i].legal_entity_id).html(data[i].business_legal_name)
                        );
                    }
                }
            }
    });
    }
}

// Write Function for Add and Refresh
function func_cat_refresh(){

    var token  = $("#csrf-token").val();
    var class_name = $('#cat_refresh').attr('class');
    var env = "http://"+window.location.hostname;


    if(class_name == 'fa fa-plus'){ 

        $( "#cat_refresh" ).removeClass( 'fa fa-plus' );
        $('#cat_refresh').addClass('fa fa-refresh');

        window.open(''+env+'/categories/index','', "width=600,height=600");

    }else if(class_name == 'fa fa-refresh'){

        $( "#cat_refresh" ).removeClass( 'fa fa-refresh' );
        $('#cat_refresh').addClass('fa fa-plus');
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "get",
            url: '/assets/loadcategories',
            success: function (data)
            {
                if(data){
                    var category = $('#mdl_category');
                    category.find('option').remove().end();
                    
                    for(var i=0; i<data.length; i++){
                        category.append(
                            $('<option></option>').val(data[i].category_id).html(data[i].cat_name)
                        );
                    }
                }
            }
        });
    }
}

$( "#asset_category_id" ).change(function() {
  alert("Asset Category will change for all the Assets under this Product");
});
