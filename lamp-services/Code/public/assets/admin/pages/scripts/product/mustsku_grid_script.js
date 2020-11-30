$(document).ready(function(){
$('#mustskuproductsGrid').igGrid({
    dataSource: 'getmustskuProducts',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    rowHeight:12,
    enableUTCDates: true,
    expandColWidth: 0,
    renderCheckboxes: true,
    columns: [
        {headerText: 'Product ID', key: 'Product_ID', dataType: 'number', width: '0px'},
        {headerText: '', key: 'ProductLogo', columnCssClass: "imgalign", dataType: 'string', width: '40px', template: "<center style='border-width:1px; border-style:solid; border-color:#efefef; height:32px; width:32px; line-height:30px; display:block; background:#fff;'><img style='max-height: 32px; max-width: 32px; height:auto; width:auto;vertical-align: middle;' src ='${ProductLogo}'/></center>"},
        {headerText: 'Product Title', key: 'Product_Title', dataType: 'string', width: '300px'},
        {headerText: 'SKU', key: 'SKU', dataType: 'string', width: '100px'},
        {headerText: 'Warehouse', key: 'Display_Name', dataType: 'string', width: '100px'},
        {headerText: 'Manufacture', key: 'ManfName', dataType: 'string', width: '200px'},
        {headerText: 'Brand', key: 'Brand', dataType: 'string', width: '150px'},
        {headerText: 'Category', key: 'category_name', dataType: 'string', width: '100px'},
        {headerText: 'Status', key: 'status', dataType: 'string', width: '130px'},
        {headerText: 'Actions', key: 'Action', dataType: 'string', width: '110px'},
    ],
    features: [
                    {
                        name: "ColumnFixing",
                        fixingDirection: "right",
                        columnSettings: [
                            {
                                columnKey: "Action",
                                isFixed: true,
                                allowFixing: false
                            }
                        ]
                    },
        
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
                {columnKey: 'ProductLogo', allowFiltering: false},
                {columnKey: 'Action', allowFiltering: false},
            ]
        },
        {
            name: 'Sorting',
            type: 'remote',
            persist: false,
            columnSettings: [
                {columnKey: 'ProductLogo', allowSorting: false},
                {columnKey: 'Action', allowSorting: false},
            ]

        },
        {
           /* recordCountKey: 'TotalRecordsCount',
            chunkIndexUrlKey: 'page',
            chunkSizeUrlKey: 'pageSize',
            chunkSize: 8,
            name: 'AppendRowsOnDemand',
            loadTrigger: 'auto',
            type: 'remote'*/

            name: 'Paging',
            type: 'remote',
            pageSize: 10,
            recordCountKey: 'TotalRecordsCount',
            pageIndexUrlKey: "page",
            pageSizeUrlKey: "pageSize"  
        }
    ],
    primaryKey: 'Product_ID',
    width: '100%', 
    height: '520px',
    initialDataBindDepth: 0,
    localSchemaTransform: false
});


    $('#dcid').change(function () {
            autosuggest();
        });
    function autosuggest()
    {
        $( "#product_name_or_sku_code" ).autocomplete({
             source: '/products/getmustskusearch?&warehouse_id='+$('#dcid').val(),
             minLength: 2,
             select: function( event, ui ) {
                  if(ui.item.label=='No Result Found'){
                     event.preventDefault();
                  }
                  $('#addproduct_id').val(ui.item.product_id);
             }
         });
    }

    $('#must_sku_form').submit(function(e){
    e.preventDefault();
    var csrf_token = $('#csrf-token').val();
    var formData = new FormData($(this)[0]);
    $('#pimloader').show();
    var url = $(this).attr('action');
    $.ajax({
    headers: {'X-CSRF-TOKEN': csrf_token},
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            beforeSend: function(xhr) {
            $('#pimloader').show();
            },
            success: function (data) {
            $('.close').trigger('click');
            $('#pimloader').hide();
            var data = jQuery.parseJSON(data);
            $('#addproduct_id').val('');
            $('#dcid').select2("val", 0);
            $('#product_name_or_sku_code').val('');
            $('.product_success_msg').html('');
            $('a[href="#import_success_messages"]').trigger('click');
            //console.log(data);
            if (data.status_messages.length == 0)
            {
            $('.product_success_msg').append('<tr><td>' + data.message + '</td></tr>');
            } else {

            $('.product_success_msg').append(data.status_messages);     
            }
            $("#mustskuproductsGrid").igGrid("dataBind");
            },
            cache: false,
            contentType: false,
            processData: false
    });
    });

     
});