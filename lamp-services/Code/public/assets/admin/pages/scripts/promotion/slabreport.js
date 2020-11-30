$(function () {
     //for startdate 
    var date = new Date();
    $('#from_date').datepicker({
        dateFormat: 'yy/mm/dd'
    });
 
    //fro end date
    var date = new Date();
    $('#to_date').datepicker({
        dateFormat: 'yy/mm/dd'
    });

});

    $(function () {
        var url = window.location.href;
        var urlArr = url.split("/");
        var status  = urlArr[5];
        if(status == null)
        {
            status == "";
        }

        $("#slab_report").igGrid({
            dataSource: '/promotions/reportdata',
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false, 
            enableUTCDates: true, 
            width: "100%",
            height: "100%",
            columns: [
                { headerText: "HUB Name", key: "HUBName", dataType: "string", width: "10%" },
                { headerText: "Order Date", key: "order_date", dataType: "string",dataType: "date",format:"dd-MM-yyyy", width: "10%", template: "<div>${OrderDate}</div>" },
                { headerText: "Produt Name", key: "ProdutName", dataType: "string", width: "14%" },
                { headerText: "Article No.", key: "ProductSKU",  width: "10%" },
                { headerText: "MRP", key: "mrp", dataType: "number",width: "10%"},
                { headerText: "CFC Qty", key: "NoOfEaches", dataType: "number",width: "6%",template: "<div style='text-align:center'>${NoOfEaches}</div>"},
                { headerText: "ESU Qty", key: "ESU_qty", dataType: "number",width: "6%",template: "<div style='text-align:center'>${ESU_qty}</div>"},
                { headerText: "Slab Rates", key: "Slabrates", dataType: "number",width: "10%"},
                { headerText: "Order Qty ", key: "OrderQty", dataType: "number",width: "6%",template: "<div style='text-align:center'>${OrderQty}</div>"},
                { headerText: "Order Value", key: "total", dataType: "number", width: "10%"},
                { headerText: "Order Status", key: "OrderStatus", dataType: "string", width: "10%"},
                 ],
             features: [
                 {
                    name: "Sorting",
                    type: "remote",
                    columnSettings: [
                    {columnKey: 'HUBName', allowSorting: true },
                    {columnKey: 'order_date', allowSorting: true },
                    {columnKey: 'ProdutName', allowSorting: true },
                    {columnKey: 'ProductSKU', allowSorting: true },
                    {columnKey: 'mrp', allowSorting: true },
                    {columnKey: 'NoOfEaches', allowSorting: true },
                    {columnKey: 'ESU_qty', allowSorting: true },
                    {columnKey: 'Slabrates', allowSorting: true },
                    {columnKey: 'OrderQty', allowSorting: true },
                    {columnKey: 'OrderStatus', allowSorting: true },
                    {columnKey: 'total', allowSorting: true },
                    ]
                },
                {
                    name: "Filtering",
                    type: "remote",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                    {columnKey: 'HUBName', allowSorting: true },
                    {columnKey: 'order_date', allowSorting: true },
                    {columnKey: 'ProdutName', allowSorting: true },
                    {columnKey: 'ProductSKU', allowSorting: true },
                    {columnKey: 'mrp', allowSorting: true },
                    {columnKey: 'NoOfEaches', allowSorting: true },
                    {columnKey: 'ESU_qty', allowSorting: true },
                    {columnKey: 'Slabrates', allowSorting: true },
                    {columnKey: 'OrderQty', allowSorting: true },
                    {columnKey: 'OrderStatus', allowSorting: true },
                    {columnKey: 'total', allowSorting: true },
                    ]
                },
                { 
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 20,
                    name: 'Paging', 
                    loadTrigger: 'auto', 
                    type: 'local' 
                }
                
            ],
            primaryKey: 'prmt_tmpl_Id',
            width: '100%',
            height: '500px',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            
        });
    });     
