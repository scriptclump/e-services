function inventoryProductGrid1(productid)
{
	$("#inventorygrid").igGrid({
            dataSource: '/inventory/getProductsForProductPage?productid=' + productid,
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                
                       
                        {headerText: "", key: "primary_image", dataType: "image",width: "3%", template: "<img class='img-responsive' width='48px' height='48px' src='${primary_image}'>"},
                        {headerText: "Product Title", key: "product_title", dataType: "string", width: "14%", template: '<div class="textLeftAlign"> ${product_title} </div>'},
                        {headerText: "DC Name", key: "dcname", dataType: "string", width: "8%"},
                        {headerText: "Product Id", key: "product_id", dataType: "string", width: "5%", template: '<div style="text-align : right"> ${product_id} </div>'},
                        {headerText: "SKU", key: "sku", dataType: "string",width: "8%", template: '<div class="textLeftAlign"> ${sku} </div>'},
                        {headerText: "KVI", key: "kvi", dataType: "string", width: "3%"},
                        // {headerText: "EAN", key: "upc", dataType: "number", width: "10%", template: '<div class="textLeftAlign"> ${upc} </div>'},
                        {headerText: "MRP", key: "mrp", dataType: "number",width: "5%", template: '<div style="text-align : right"> ${mrp} </div>'},
                        {headerText: "SOH", key: "soh", dataType: "number",width: "3%", template: '<div style="text-align : right"> ${soh} </div>'},
                        {headerText: "ATP", key: "atp", dataType: "number",width: "3%", template: '<div style="text-align : right"> ${atp} </div>'},
                        {headerText: "DIT", key: "dit_qty", dataType: "number", width: "2%", template: '<div style="text-align : right"> ${dit_qty} </div>'},
                        {headerText: "Missing", key: "dnd_qty", dataType: "number", width: "4%", template: '<div style="text-align : right"> ${dnd_qty} </div>'},
                        {headerText: "MAP", key: "map", dataType: "number", width: "4%", template: '<div style="text-align : right"> ${map} </div>'},
                        {headerText: "Orders", key: "order_qty", dataType: "number", width: "4%", template: '<div style="text-align : right"> ${order_qty} </div>'},
                        {headerText: "Inventory", key: "available_inventory", dataType: "number", width: "4%", template: '<div style="text-align : right"> ${available_inventory} </div>'},
                        {headerText: "Rev Qty", key: "reserved_qty", dataType: "string", width: "4%", template: '<div style="text-align : right"> ${reserved_qty} </div>'},
                        {headerText: "Bin Location", key: "bin_location", dataType: "string", width: "6%"},
                        {headerText: "Actions", key: "actions", dataType: "string", width: "4%"},
            ],
            features: [
                // {
                //     name: "Sorting",
                //     type: "remote",
                //     columnSettings: [
                //         {columnKey: 'actions', allowSorting: false},
                //         // {columnKey: 'TotalQuantity', allowSorting: false},
                //         // {columnKey: "PrimaryKEY", allowSorting: true, currentSortDirection: "descending"}

                //     ]
                // },
                // {
                //     name: "Filtering",
                //     type: "remote",
                //     mode: "simple",
                //     filterDialogContainment: "window",
                //     // columnSettings: [
                //     //     {columnKey: 'actions', allowFiltering: false},
                //     //     // {columnKey: 'TotalQuantity', allowFiltering: false},
                //     // ]
                // },
                // {
                //     recordCountKey: 'TotalRecordsCount',
                //     chunkIndexUrlKey: 'page',
                //     chunkSizeUrlKey: 'pageSize',
                //     chunkSize: 10,
                //     name: 'AppendRowsOnDemand',
                //     loadTrigger: 'auto',
                //     type: 'remote'
                // }

            ],
            primaryKey: 'product_id',
            height: '100%',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
           

        });
}

$('#edit-products').on('shown.bs.modal', function (e) {
    console.log("product one");
        var soh = $(e.relatedTarget).data('soh');
        var atp = $(e.relatedTarget).data('atp');
        var warehouseid = $(e.relatedTarget).data('warehouseid');
        var product_id = $(e.relatedTarget).data('prodid');
        var warehousename = $(e.relatedTarget).data('dcname');
        var product_title = $(e.relatedTarget).data('producttitle');
        var skuid = $(e.relatedTarget).data('skuid');
        var ditqty = $(e.relatedTarget).data('ditqty'); 
        var dndqty = $(e.relatedTarget).data('dndqty'); 
        $("#success_message_popup_box").html("");
        $("#excess_qty").val(0);
        $("#soh_update").val(soh);
        $("#prod").val(product_id);
        $("#warehouse_id").val(warehouseid);
        $("#dit_qty").val(0);
        $("#dnd_qty").val(0);

        $("#current_dit_qty").html(ditqty);
        $("#current_dnd_qty").html(dndqty);
        $("#dcname").html(warehousename);
        $("#product_title_inventory").html(product_title);
        $("#skuID").html(skuid);
    });

