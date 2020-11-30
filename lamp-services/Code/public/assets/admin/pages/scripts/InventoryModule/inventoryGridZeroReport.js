function inventoryGrid()
{
    $("#inventorygrid").igGrid({
            dataSource: '/inventory/getProductsForProductPageZeroReport',
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                
                                    
                        {headerText: "", key: "primary_image", dataType: "image",width: "5%", template: "<center style='border-width:1px; border-style:solid; border-color:#efefef; height:32px; width:32px; line-height:30px; display:block; background:#fff;'><img style='max-height: 32px; max-width: 32px; height:auto; width:auto;vertical-align: middle;' src ='${primary_image}'/></center>"},
                        {headerText: "Product Title", key: "product_title", dataType: "string", width: "18%", template: '<div class="textLeftAlign"> ${product_title} </div>'},
                        {headerText: "DC Name", key: "dcname", dataType: "string", width: "8%"},
                        {headerText: "Product Id", key: "product_id", dataType: "string", width: "8%", template: '<div class="textRightAlign"> ${product_id} </div>'},
                        {headerText: "SKU", key: "sku", dataType: "string" },
                        {headerText: "KVI", key: "kvi", dataType: "string", width: "5%"},
                        // {headerText: "EAN", key: "upc", dataType: "number", width: "10%", template: '<div class="textLeftAlign"> ${upc} </div>'},
                        {headerText: "MRP", key: "mrp", dataType: "string", template: '<div class="textRightAlign"> ${mrp} </div>'},
                        {headerText: "SOH", key: "soh", dataType: "number",width: "5%", template: '<div class="textRightAlign"> ${soh} </div>'},
                        {headerText: "ATP", key: "atp", dataType: "string",width: "5%", template: '<div class="textRightAlign"> ${atp} </div>'},
                        {headerText: "MAP", key: "map", dataType: "string", template: '<div class="textRightAlign"> ${map} </div>'},
                        {headerText: "Orders Qty", key: "order_qty", dataType: "string", width: "6%", template: '<div class="textRightAlign"> ${order_qty} </div>'},
                        {headerText: "Inventory", key: "available_inventory", dataType: "string", width: "6%", template: '<div class="textRightAlign"> ${available_inventory} </div>'},
                        {headerText: "Reserved Qty", key: "reserved_qty", dataType: "string", width: "10%", template: '<div class="textRightAlign"> ${reserved_qty} </div>'},
                        // {headerText: "Actions", key: "actions", dataType: "string", width: "5%"},
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
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'primary_image', allowFiltering: false},
                        {columnKey: 'soh', allowFiltering: false},
                        // {columnKey: 'TotalQuantity', allowFiltering: false},
                    ]
                },
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
            height: '500px',
            width: "100%",
            // initialDataBindDepth: 0,
            localSchemaTransform: false,
            rendered: function (evt, ui) {
            $("#inventorygrid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
            $("#inventorygrid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();    
            $("#inventorygrid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
            $("#inventorygrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();    
            $("#inventorygrid_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();    
            $("#inventorygrid_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();    






            }
           

        });
}


$('#edit-products').on('shown.bs.modal', function (e) {
        var soh = $(e.relatedTarget).data('soh');
        var atp = $(e.relatedTarget).data('atp');
        var warehouseid = $(e.relatedTarget).data('warehouseid');
        var product_id = $(e.relatedTarget).data('prodid');
        var warehousename = $(e.relatedTarget).data('dcname');
        var product_title = $(e.relatedTarget).data('producttitle');
        var skuid = $(e.relatedTarget).data('skuid');
        $("#ATP_update").val(atp);
        $("#soh_update").val(soh);
        $("#product_id").val(product_id);
        $("#warehouse_id").val(warehouseid);
        $("#dcname").html(warehousename);
        $("#product_title_inventory").html(product_title);
        $("#skuID").html(skuid);
    });

    $("#update_products").click(function(){
        var token = $("#token_value").val();
        var productid  = $("#product_id").val();
        var warehouseId = $("#warehouse_id").val();
        var soh_value = $("#soh_update").val();
        var atp_value = $("#ATP_update").val();
        $.ajax({
            type:"POST",
            data:"soh_value="+soh_value+"&atp_value="+atp_value+"&prod_id="+productid+"&ware_id="+warehouseId,
            url:"/inventory/updateInventory?_token=" + token,
            success:function(data)
            {
                $("#inventorygrid").igHierarchicalGrid("dataBind");
                $('#edit-products').modal('toggle');
                console.log("updated succesfully");
                $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Updated successfully</div></div>');
                $(".alert-success").fadeOut(5000)
            }

        });
    });

    function inventoryGrid_old(productid)
{

            /*----------------- Instantiation -------------------------*/
            var token = $("#token_value").val();
            $("#inventorygrid").igHierarchicalGrid({
                width: "100%",
                autoCommit:true,
                dataSource: "/inventory/totalinventory?productid="+productid,
                dataSourceType: "json",
                responseDataKey: "results",
                autoGenerateColumns: false,
                autofitLastColumn: false,
                primaryKey: "le_wh_id",
                columns: [
                {headerText: "DC ID", key: "le_wh_id", dataType: "number", hidden: true},
                {headerText:"DC Name", key: "dcname", dataType: "string", width: "30%", template: '<div class="textLeftAlign"> ${dcname} </div>'},
                {headerText: "CP ( <div class='fa fa-inr'></div> )", key: "cpvalue", dataType: "number", width: "15%", template: '<div class="textRightAlign"> ${cpvalue} </div>', formatter: function (val, data) {
                        return val.toFixed(3);
                    }},
                {headerText: "PTR ( <div class='fa fa-inr'></div> )", key: "ptrvalue", dataType: "number", width: "15%", template: '<div class="textRightAlign"> ${ptrvalue} </div>', formatter: function (val, data) {
                        return val.toFixed(3);
                    }},
                {headerText: "MRP ( <div class='fa fa-inr'></div> )", key: "mrpvalue", dataType: "number", width: "15%", template: '<div class="textRightAlign"> ${mrpvalue} </div>', formatter: function (val, data) {
                        return val.toFixed(3);
                    }},
                {headerText: "MAP ( <div class='fa fa-inr'></div> )", key: "mapvalue", dataType: "number", width: "15%", template: '<div class="textRightAlign"> ${mapvalue} </div>', formatter: function (val, data) {
                        return val.toFixed(3);
                    }}
            ],
                childrenDataProperty: "inventory",
                autoGenerateLayouts: false,
                columnLayouts: [
                    {
                        key: "inventory",
                        responseDataKey: "results",
                        width: "100%",
                        autoGenerateColumns: false,
                        autofitLastColumn: false,
                        primaryKey: "inv_id",
                        autoCommit: false,
                        columns: [
                            { key: "le_wh_id", headerText: "warehouseid", dataType: "number", width: "0%", hidden: true },
                            { key: "product_id", headerText: "Product Id", dataType: "number", width: "0%", hidden: true },
                            { key: "inv_id", headerText: "inv_id", dataType: "number", width: "0%", hidden: true },
                            {headerText: "", key: "primary_image", dataType: "image",width: "10%", template: "<img class='img-responsive' width='48px' height='48px' src='${primary_image}'>"},
                            {headerText: "Product Title", key: "product_title", dataType: "string", width: "18%", template: '<div class="textLeftAlign"> ${product_title} </div>'},
                            {headerText: "Product Id", key: "product_id", dataType: "string", width: "8%"},
                            {headerText: "SKU-Code", key: "sku", dataType: "string", template: '<div class="textLeftAlign"> ${sku} </div>'},
                            {headerText: "KVI", key: "kvi", dataType: "string", width: "5%", template: '<div class="textLeftAlign"> ${kvi} </div>'},
                            // {headerText: "EAN", key: "upc", dataType: "string", width: "10%", template: '<div class="textLeftAlign"> ${upc} </div>'},
                            {headerText: "MRP", key: "mrp", dataType: "number", template: '<div class="textRightAlign"> ${mrp} </div>', formatter: function (val, data) {
                                return val.toFixed(3);
                            }},
                            {headerText: "SOH", key: "soh", dataType: "number",width: "5%", template: '<div class="textLeftAlign"> ${soh} </div>'},
                            {headerText: "ATP", key: "atp", dataType: "number",width: "5%", template: '<div class="textLeftAlign"> ${atp} </div>'},
                            {headerText: "MAP", key: "map", dataType: "number", template: '<div class="textRightAlign"> ${map} </div>', formatter: function (val, data) {
                                return val.toFixed(3);
                            }},
                            {headerText: "Orders Qty", key: "order_qty", dataType: "number", width: "6%"},
                            {headerText: "Inventory", key: "available_inventory", dataType: "number", width: "6%", template: '<div class="textLeftAlign"> ${available_inventory} </div>'},
                            {headerText: "Reserved Qty", key: "reserved_qty", dataType: "string", width: "10%"},
                            {headerText: "Quarantine Qty", key: "quarantine_qty", dataType: "string", width: "8%"},
                        ],
                        features: [
                           {
                                name: "Paging",                                
                                type: "local",
                                pageSize: 10
                            },
                            {
                                name: "Updating",
                                enableAddRow: false,
                                enableDeleteRow: false,
                                startEditTriggers: 'dblclick',
                                editMode: "dialog",
                                editRowEnded: function (evt, ui) {
                                
                                    var warehouse_id = ui.oldValues.le_wh_id;
                                    var prod_id = ui.oldValues.product_id;
                                    var atpval = ui.values.atp;
                                    var sohval = ui.values.soh;
                                    updateinventoryfunc(warehouse_id, prod_id, atpval, sohval);
                                },
                                rowEditDialogOptions: {
                                    height: "350px",
                                    width: "300px",
                                    containment: "window",
                                    showDoneCancelButtons: true
                                },
                                columnSettings:
                                [
                                    {
                                        columnKey: "primary_image",
                                        editorType: "image",
                                        readOnly: true
                                    },
                                    {
                                        columnKey: "product_title",
                                        editorType: "string",
                                        readOnly: true
                                    },
                                    {
                                        columnKey: "sku",
                                        editorType: "string",
                                        readOnly: true
                                    },
                                    {
                                        columnKey: "kvi",
                                        editorType: "string",
                                        readOnly: true
                                    },
                                    {
                                        columnKey: "upc",
                                        editorType: "string",
                                        readOnly: true
                                    },
                                    {
                                        columnKey: "mrp",
                                        editorType: "currency",
                                        readOnly: true
                                    },
                                    {
                                        columnKey: "soh",
                                        editorType: "number",
                                        readOnly: false
                                    },
                                    {
                                        columnKey: "atp",
                                        editorType: "number",
                                        readOnly: false
                                    },
                                    {
                                        columnKey: "map",
                                        editorType: "number",
                                        readOnly: true
                                    },
                                    {
                                        columnKey: "order_qty",
                                        editorType: "number",
                                        readOnly: true
                                    },
                                    {
                                        columnKey: "available_inventory",
                                        editorType: "number",
                                        readOnly: true
                                    },

                                ]
                            }
                        ]
                    }
                ]
            });
        
}


function updateinventoryfunc(warehouse_id, prod_id, atpval, sohval)
{
     var token = $("#token_value").val();
    $.ajax({
            type:"POST",
            data:"soh_value="+sohval+"&atp_value="+atpval+"&prod_id="+prod_id+"&ware_id="+warehouse_id,
            url:"/inventory/updateInventory?_token=" + token,
            success:function(data)
            {
                // $("#inventorygrid").igHierarchicalGrid("dataBind");
                // $('#edit-products').modal('toggle');
                console.log("updated succesfully");
                $("#success_message").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Updated successfully</div></div>');
                $(".alert-success").fadeOut(5000)
            }

        });
}




