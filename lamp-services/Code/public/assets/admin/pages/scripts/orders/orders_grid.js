function getSalesOrderList(status) {    
    var sales_id=$('#primary_secoundary_sales_id').val();
    var filterURL = "/salesorders/ajax/index/" + status+"/"+sales_id;

    if(status == 'allorders') {
      getAllOrders(filterURL);
    }
    else if(status == 'open') {
      getOpenOrders(filterURL);
    }
    else if(status == 'picklist') {
      getPicklistOrders(filterURL);
    }
    else if(status == 'cancelbycust') {
      getCancelledOrdersByCust(filterURL);
    }
    else if(status == 'cancelbyebutor') {
      getCancelledOrdersByEbutor(filterURL);
    }
    else if (status == 'partialcancel') {
      getPartialCancelledOrders(filterURL);
    }
    else if(status == 'dispatch') {
      getRtdOrders(filterURL);
    }
    else if (status == 'invoiced') {          
        getInvoicedOrders(filterURL);
    }
    else if (status == 'stocktransit') {          
        getStockTransitOrders(filterURL);
    }
    else if (status == 'stockhub') {          
        getStockhubOrders(filterURL);
    }
    else if (status == 'ofd') {          
        getOutForDeliveryOrders(filterURL);
    }
    else if (status == 'delivered') {
        getDeliveredOrders(filterURL);
    } 
    else if (status == 'partialdelivered') {
        getPartialDeliveredOrders(filterURL);
    } 
    else if (status == 'completed') {
        getCompletedOrders(filterURL)
    }
    else if (status == 'nct') {
        getNCTOrders(filterURL)
    }
    else if (status == 'hold') {
        getHoldOrders(filterURL)
    }
    else if (status == 'return') {
        getReturnedOrders(filterURL);
    }
    else if (status == 'returnapproval') {
        getReturnedApprovalOrders(filterURL);
    }
    else if (status == 'missingquantities') {
        getMissingQuantitieOrders(filterURL);
    }
     else if (status == 'damagedquantities') {
        getDamagedQuantitieOrders(filterURL);
    }
     else if (status == 'approvedMissingquantities'){
        getApprovedMissingQuantitie(filterURL);
    }
     else if (status == 'approvedDamagedquantities'){
        getApprovedDamagedQuantitie(filterURL);
    }
     else if (status == 'shortcollections') {
        getShortCollections(filterURL);
    }

    else if (status == 'rah') {
        getReturnedApprovedAtHubOrders(filterURL);
    }
    else if (status == 'stocktransitdc') {
        getSITHubToDc(filterURL);
    }
    else if (status == 'stockindc') {
        getStockInDcOrders(filterURL);
    }    
    else if (status == 'unpaid') {
        getUnpaidOrders(filterURL);
    }    
    else {
      getAllOrders(filterURL);
    }
}

function igGridHideOption() {
  $("#orderList_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
  $("#orderList_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
  $("#orderList_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
  $("#orderList_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
  $("#orderList_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
  $("#orderList_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
  $("#orderList_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
  $("#orderList_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
  $("#orderList_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
  $("#orderList_container").find(".ui-iggrid-filtericonon").closest("li").remove();   
  $("#orderList_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();
  $("#orderList_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
  $("#orderList_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
  $("#orderList_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
  
  $(".ui-iggrid-fixcolumn-headerbuttoncontainer").remove();
}

function getAllOrders(ajaxURL) {
    $("#orderList").igGrid({
        columns: [  
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "150px"},
            {headerText: "SCH Date", key: "SDT", dataType: "date", format: "date", width: "100px"},
            {headerText: "Picklist No", key: "pickno", dataType: "string", width: "120px"},
            {headerText: "DEL Slot1", key: "SDS1", dataType: "string", width: "100px"},
            {headerText: "DEL Slot2", key: "SDS2", dataType: "string", width: "100px"},
            {headerText: "Created By", key: "User", dataType: "string", width: "100px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string", width: "120px"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number", format:"number", columnCssClass: "data__aliright", width: "140px"},
            {headerText: "Order Qty", key: "orderedQty", dataType: "number", width: "100px",  columnCssClass: "centerAlignment"},
            {headerText: "SKU Count", key: "skuCount", dataType: "number", width: "100px",  columnCssClass: "centerAlignment"},
            {headerText: "Fill Rate (%)", key: "FillRate", dataType: "number", columnCssClass: "centerAlignment", width: "100px"},
            {headerText: "Status", key: "Status", dataType: "string", width: "150px"},
            {headerText: "Actions", key: "Actions", columnCssClass: "centerAlignment", width: "110px"}            
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                    {columnKey: 'chk', allowSorting: false},                                             
                 ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "FillRate", allowFiltering: true},
                    {columnKey: "SDS1", allowFiltering: true},
                    {columnKey: "SDS2", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false}, 
                    {columnKey: "pickno", allowSummaries: false},
                                  
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},  
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},    
                {columnKey: "SDT", allowSummaries: false},
                {columnKey: "pickno", allowSummaries: false},
                {columnKey: "SDS1", allowSummaries: false},     
                {columnKey: "SDS2", allowSummaries: false},  
                {columnKey: "ADT", allowSummaries: false}, 
                {columnKey: "User", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "pickedby", allowSummaries: false},  
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "orderedQty", allowSummaries: false},
                {columnKey: "del_name", allowSummaries: false},
                {columnKey: "skuCount", allowSummaries: false},  
                {columnKey: "FillRate", allowSummaries: false},               
                {columnKey: "Status", allowSummaries: false}, 
                {columnKey: "invoice_code", allowSummaries: false},          
                {columnKey: "Actions", allowSummaries: false}]
            },    
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "FillRate",
                       isFixed: false,
                   },              
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxURL,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
        }
    });

}

function getOpenOrders(ajaxUrl) {
  
  $("#orderList").igGrid({
      columns: [ 
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Rank", key: "custRating", dataType: "string", width: "150px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "150px"},
            {headerText: "SCH Date", key: "SDT", dataType: "date", format: "date", width: "100px"},
            {headerText: "Created By", key: "User", dataType: "string", width: "100px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number", format:"number", columnCssClass: "data__aliright", width: "140px"},
            {headerText: "Order Qty", key: "orderedQty", dataType: "number", format:"number", columnCssClass: "dataaliright", width: "100px"},
            {headerText: "SKU Count", key: "skuCount", dataType: "number", format:"number", columnCssClass: "dataaliright", width: "100px"},
            {headerText: "DEL Slot1", key: "SDS1", dataType: "string", width: "100px"},
            {headerText: "DEL Slot2", key: "SDS2", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", columnCssClass: "centerAlignment", width: "100px"}
          ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [                 
                    {columnKey: 'Actions', allowSorting: false},
                    {columnKey: 'chk', allowSorting: false},
                    {columnKey: 'custRating', allowSorting: false},

                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [                    
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "FillRate", allowFiltering: false},
                    {columnKey: "SDS1", allowFiltering: true},
                    {columnKey: "SDS2", allowFiltering: true},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "custRating", allowSummaries: false},          
                {columnKey: "OrderDate", allowSummaries: false},  
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},    
                {columnKey: "SDT", allowSummaries: false}, 
                {columnKey: "User", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},  
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "orderedQty", allowSummaries: false},          
                {columnKey: "skuCount", allowSummaries: false},        
                {columnKey: "SDS1", allowSummaries: false},     
                {columnKey: "SDS2", allowSummaries: false},    
                {columnKey: "Actions", allowSummaries: false}]
            },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                   {
                       columnKey: "SDS2",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }

        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
        }
    });
}
  
function getPicklistOrders(ajaxUrl) {
  
  $("#orderList").igGrid({
      columns: [ 
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Picking Date", key: "pickerdate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "150px"},
            {headerText: "Picklist No", key: "pickno", dataType: "string", width: "120px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number", format: "number",  width: "140px", columnCssClass: "data__aliright"},
            {headerText: "Order Qty", key: "orderedQty", dataType: "number", format:"number", columnCssClass: "dataaliright", width: "100px"},
            {headerText: "SKU Count", key: "skuCount", dataType: "number", format:"number", columnCssClass: "dataaliright", width: "100px"},
            {headerText: "Picked By", key: "picker", dataType: "string", width: "130px"},
            {headerText: "Actions", key: "Actions", columnCssClass: "centerAlignment", width: "120px"}
          ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [                 
                    {columnKey: 'Actions', allowSorting: false},
                    {columnKey: 'chk', allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [                    
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "FillRate", allowFiltering: false},
                    {columnKey: "SDS1", allowFiltering: true},
                    {columnKey: "SDS2", allowFiltering: true},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "pickerdate", allowSummaries: false},  
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},    
                {columnKey: "pickno", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "orderedQty", allowSummaries: false},
                {columnKey: "skuCount", allowSummaries: false},  
                {columnKey: "picker", allowSummaries: false},               
                {columnKey: "Actions", allowSummaries: false}]
            },    
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "pickerdate",
                       isFixed: true,
                       allowFixing: false
                   },

                    {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
        }
    });
}

function getCancelledOrdersByCust(ajaxUrl) {
   
    $("#orderList").igGrid({
        columns: [  
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "150px"},
            {headerText: "SCH Date", key: "SDT", dataType: "date", format: "date", width: "100px"},
            {headerText: "Created By", key: "User", dataType: "string", width: "100px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number", format:"number", columnCssClass: "data__aliright", width: "140px"},
            {headerText: "Order Qty", key: "orderedQty", dataType: "number", width: "80px",  columnCssClass: "centerAlignment"},
            {headerText: "Cancel Value", key: "CancelledValue", dataType: "number",format:"number", columnCssClass: "data__aliright", width: "140px"},
            {headerText: "Cancel Qty", key:"CancelledQty",dataType: "number", width: "80px",  columnCssClass: "centerAlignment"},
            {headerText: "Reason", key: "canReason", dataType: "string", width: "100px"},
            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", columnCssClass: "centerAlignment", width: "100px"}            
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                 ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "FillRate", allowFiltering: false},
                    {columnKey: "SDS1", allowFiltering: true},
                    {columnKey: "SDS2", allowFiltering: true},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "Status", allowFiltering: false},
                    

                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},  
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},    
                {columnKey: "SDT", allowSummaries: false}, 
                {columnKey: "User", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},  
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "orderedQty", allowSummaries: false},
                {columnKey: "CancelledValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "CancelledQty", allowSummaries: false},
                {columnKey: "canReason", allowSummaries: false},        
                {columnKey: "Status", allowSummaries: false},     
                {columnKey: "Actions", allowSummaries: false}]
            },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "FillRate",
                       isFixed: false,
                   },           
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
        }
    });

}

function getCancelledOrdersByEbutor(ajaxUrl) {
   
    $("#orderList").igGrid({
        columns: [  
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "150px"},
            {headerText: "SCH Date", key: "SDT", dataType: "date", format: "date", width: "100px"},
            {headerText: "Created By", key: "User", dataType: "string", width: "100px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number", format:"number", columnCssClass: "data__aliright", width: "140px"},
            {headerText: "Order Qty", key: "orderedQty", dataType: "number", width: "80px",  columnCssClass: "centerAlignment"},
            {headerText: "Cancel Value", key: "CancelledValue", dataType: "number",format:"number", columnCssClass: "data__aliright", width: "140px"},
            {headerText: "Cancel Qty", key:"CancelledQty",dataType: "number", width: "80px",  columnCssClass: "centerAlignment"},
            {headerText: "Reason", key: "canReason", dataType: "string", width: "100px"},
            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", columnCssClass: "centerAlignment", width: "100px"}            
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                    {columnKey: 'chk', allowSorting: false},
                 ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "FillRate", allowFiltering: false},
                    {columnKey: "SDS1", allowFiltering: true},
                    {columnKey: "SDS2", allowFiltering: true},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "Status", allowFiltering: false},
                    

                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},  
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},    
                {columnKey: "SDT", allowSummaries: false}, 
                {columnKey: "User", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},  
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "orderedQty", allowSummaries: false},
                {columnKey: "CancelledValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "CancelledQty", allowSummaries: false},
                {columnKey: "canReason", allowSummaries: false},        
                {columnKey: "Status", allowSummaries: false},     
                {columnKey: "Actions", allowSummaries: false}]
            },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "FillRate",
                       isFixed: false,
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
        }
    });

}

function getPartialCancelledOrders(ajaxUrl) {
   
    $("#orderList").igGrid({
        columns: [  
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "150px"},
            {headerText: "SCH Date", key: "SDT", dataType: "date", format: "date", width: "100px"},
            {headerText: "Created By", key: "User", dataType: "string", width: "100px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number", format:"number", columnCssClass: "data__aliright", width: "140px"},
            {headerText: "Order Qty", key: "orderedQty", dataType: "number", width: "80px",  columnCssClass: "centerAlignment"},
            {headerText: "Cancel Value", key: "CancelledValue", dataType: "number",format:"number", columnCssClass: "data__aliright", width: "140px"},
            {headerText: "Cancel Qty", key:"CancelledQty",dataType: "number", width: "80px",  columnCssClass: "centerAlignment"},
            {headerText: "Reason", key: "canReason", dataType: "string", width: "100px"},
            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", columnCssClass: "centerAlignment", width: "100px"}            
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                    {columnKey: 'chk', allowSorting: false},
                 ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "FillRate", allowFiltering: false},
                    {columnKey: "SDS1", allowFiltering: true},
                    {columnKey: "SDS2", allowFiltering: true},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},                    

                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
             
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},  
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},    
                {columnKey: "SDT", allowSummaries: false}, 
                {columnKey: "User", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},  
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "orderedQty", allowSummaries: false},
                {columnKey: "CancelledValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "CancelledQty", allowSummaries: false},
                {columnKey: "canReason", allowSummaries: false},        
                {columnKey: "Status", allowSummaries: false},     
                {columnKey: "Actions", allowSummaries: false}]
            },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "FillRate",
                       isFixed: false,
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
        }
    });

}

function getRtdOrders(ajaxUrl) {

    $("#orderList").igGrid({
        columns: [   
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "150px"},
            {headerText: "Picklist No", key: "pickno", dataType: "string", width: "120px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number", format:"number", columnCssClass: "data__aliright", width: "140px"},
            {headerText: "Fill Rate (%)", key: "DisFRate", dataType: "number", columnCssClass: "centerAlignment", width: "100px"},
            {headerText: "Cartons", key: "cartons", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Bags", key: "bags", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Crates", key: "crates", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Verification Officer", key: "verifiedby", dataType: "string", width: "120px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "100px", columnCssClass: "centerAlignment"},
            
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                    {columnKey: 'chk', allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "DisFRate", allowFiltering: true},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "chk", allowFiltering: false},
                    
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},    
                {columnKey: "pickno", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "DisFRate", allowSummaries: false},
                {columnKey: "cartons", allowSummaries: false},  
                {columnKey: "bags", allowSummaries: false},   
                {columnKey: "crates", allowSummaries: false},     
                {columnKey: "pickedby", allowSummaries: false},                 
                {columnKey: "verifiedby", allowSummaries: false},                 
                {columnKey: "Actions", allowSummaries: false}]
            },    
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                   {
                       columnKey: "pickedby",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "verifiedby",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
        }
    });

}

function getInvoicedOrders(ajaxUrl) {   
    $("#orderList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},            
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "120px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "150px"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number",format: "number", width: "140px",columnCssClass: "data__aliright"},
            //{headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            //{headerText: "Delivery Date", key: "ADT", dataType: "date", width: "120px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string", width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Order Qty", key: "orderedQty", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Shipped Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},
            {headerText: "Cartons", key: "cartons", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Bags", key: "bags", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Crates", key: "crates", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "centerAlignment", width: "110px"},
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'chk', allowSorting: false},
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "crates", allowFiltering: true},
                    {columnKey: "bags", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "cartons", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},     
                {columnKey: "invoice_code", allowSummaries: false},
                {columnKey: "InvoiceDate", allowSummaries: false}, 
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "orderedQty", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},
                {columnKey: "cartons", allowSummaries: false},  
                {columnKey: "bags", allowSummaries: false},    
                {columnKey: "pickedby", allowSummaries: false},  
                {columnKey: "crates", allowSummaries: false},               
                {columnKey: "Actions", allowSummaries: false}]
            },    
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                   {
                       columnKey: "crates",
                       isFixed: true,
                       allowFixing: false,
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false,
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
                 igGridHideOption();
                 //$(".ui-iggrid-featurechooserbutton").remove();
        }
    });

}

function getStockTransitOrders(ajaxUrl) {   
    $("#orderList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},            
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "120px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "150px"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number",format: "number", width: "140px",columnCssClass: "data__aliright"},
            //{headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            //{headerText: "Delivery Date", key: "ADT", dataType: "date", width: "120px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string", width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Order Qty", key: "orderedQty", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Shipped Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},
            {headerText: "Cartons", key: "cartons", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Bags", key: "bags", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "ST DEL Name", key: "st_de_name", dataType: "string", width: "120px"},
            {headerText: "ST DEL Date", key: "st_del_date", dataType: "date", format: "date", width: "120px"},
            {headerText: "ST Vehicle No", key: "st_vehicle_no", dataType: "string", width: "120px"},
            {headerText: "ST Driver Name", key: "st_driver_name", dataType: "string", width: "120px"},
            {headerText: "ST Driver Mobile", key: "st_driver_mobile", dataType: "string", width: "125px"},
            {headerText: "ST Docket No", key: "st_docket_no", dataType: "string", width: "130px"},
            {headerText: "Crates", key: "crates", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "centerAlignment", width: "110px"},
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'chk', allowSorting: false},
                    {columnKey: 'Actions', allowSorting: false},

                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "crates", allowFiltering: true},
                    {columnKey: "bags", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "cartons", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},     
                {columnKey: "invoice_code", allowSummaries: false},
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "orderedQty", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},
                {columnKey: "cartons", allowSummaries: false},
                {columnKey: "bags", allowSummaries: false},
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "st_de_name", allowSummaries: false},
                {columnKey: "st_del_date", allowSummaries: false},
                {columnKey: "st_vehicle_no", allowSummaries: false},  
                {columnKey: "st_driver_name", allowSummaries: false},
                {columnKey: "st_driver_mobile", allowSummaries: false},
                {columnKey: "st_docket_no", allowSummaries: false},
                {columnKey: "crates", allowSummaries: false},             
                {columnKey: "Actions", allowSummaries: false}]
            },    
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                   {
                       columnKey: "crates",
                       isFixed: true,
                       allowFixing: false,
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false,
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
                 igGridHideOption();
                 //$(".ui-iggrid-featurechooserbutton").remove();
        }
    });

}

function getStockhubOrders(ajaxUrl) {   
    $("#orderList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},            
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "120px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "150px"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number",format: "number", width: "140px",columnCssClass: "data__aliright"},
            //{headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            //{headerText: "Delivery Date", key: "ADT", dataType: "date", width: "120px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string", width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Order Qty", key: "orderedQty", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Shipped Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},
            {headerText: "Cartons", key: "cartons", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Bags", key: "bags", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "ST DEL Name", key: "st_de_name", dataType: "string", width: "120px"},
            {headerText: "ST DEL Date", key: "st_del_date", dataType: "date", format: "date", width: "120px"},
            {headerText: "ST Vehicle No", key: "st_vehicle_no", dataType: "string", width: "120px"},
            {headerText: "ST Driver Name", key: "st_driver_name", dataType: "string", width: "120px"},
            {headerText: "ST Driver Mobile", key: "st_driver_mobile", dataType: "string", width: "125px"},
            {headerText: "ST Received By", key: "st_re_name", dataType: "string", width: "125px"},
            {headerText: "ST Received Date", key: "st_received_at", dataType: "date", format: "date", width: "130px"},
            {headerText: "ST Docket No", key: "st_docket_no", dataType: "string", width: "130px"},
            {headerText: "Crates", key: "crates", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "centerAlignment", width: "110px"},
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'chk', allowSorting: false},
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "crates", allowFiltering: true},
                    {columnKey: "bags", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "cartons", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},     
                {columnKey: "invoice_code", allowSummaries: false},
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "orderedQty", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},
                {columnKey: "cartons", allowSummaries: false},
                {columnKey: "bags", allowSummaries: false},
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "st_de_name", allowSummaries: false},
                {columnKey: "st_del_date", allowSummaries: false},
                {columnKey: "st_vehicle_no", allowSummaries: false},  
                {columnKey: "st_driver_name", allowSummaries: false},
                {columnKey: "st_driver_mobile", allowSummaries: false},
                {columnKey: "st_re_name", allowSummaries: false},
                {columnKey: "st_received_at", allowSummaries: false},
                {columnKey: "st_docket_no", allowSummaries: false},
                {columnKey: "crates", allowSummaries: false},  
                {columnKey: "Actions", allowSummaries: false}]
            },    
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                   {
                       columnKey: "crates",
                       isFixed: true,
                       allowFixing: false,
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false,
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
                 igGridHideOption();
                 //$(".ui-iggrid-featurechooserbutton").remove();
        }
    });

}


function getPartialDeliveredOrders(ajaxUrl) {    
    $("#orderList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format:"date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "160px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string", width: "120px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", width: "120px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Invoice Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number", format: "number", width: "140px", columnCssClass: "data__aliright"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string",  width: "120px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Returned Qty", key: "ReturnQty", dataType: "number",  width: "120px", columnCssClass: "centerAlignment"},
            {headerText: "Returned Value", key: "ReturnValue", dataType: "number", format: "number",  width: "140px", columnCssClass: "data__aliright"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "110px", columnCssClass: "centerAlignment"},
            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'Status', allowSorting: false},
                    {columnKey: "chk", allowSorting: false},                    
                    {columnKey: "Actions", allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "OrderValue", allowFiltering: true},
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: 'ReturnQty', allowFiltering: true},
                    {columnKey: 'ReturnValue', allowFiltering: true},
                    
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false, 
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
             
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},  
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},    
                {columnKey: "invoice_code", allowSummaries: false}, 
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},  
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "ADT", allowSummaries: false},
                {columnKey: "del_name", allowSummaries: false},        
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "ReturnQty", allowSummaries: false},   
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "Actions", allowSummaries: false},     
                {columnKey: "Status", allowSummaries: false}]
            },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                    {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                    },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
                        ],
                        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
               igGridHideOption();
            //$(".ui-iggrid-featurechooserbutton").remove(); 
            }
                });

}

function getDeliveredOrders(ajaxUrl) {    
    $("#orderList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format:"date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "160px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string", width: "120px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", width: "120px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Invoice Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number", format: "number", width: "140px", columnCssClass: "data__aliright"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string",  width: "120px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Returned Qty", key: "ReturnQty", dataType: "number",  width: "120px", columnCssClass: "centerAlignment"},
            {headerText: "Returned Value", key: "ReturnValue", dataType: "number", format: "number",  width: "140px", columnCssClass: "data__aliright"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "110px", columnCssClass: "centerAlignment"},
            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: "chk", allowSorting: false},
                    {columnKey: "Actions", allowSorting: false},
                    {columnKey: 'Status', allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "OrderValue", allowFiltering: true},
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: 'ReturnQty', allowFiltering: true},
                    {columnKey: 'ReturnValue', allowFiltering: true},
                    
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false, 
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },

                                   
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},  
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},    
                {columnKey: "invoice_code", allowSummaries: false}, 
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},  
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "ADT", allowSummaries: false},
                {columnKey: "del_name", allowSummaries: false},        
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "ReturnQty", allowSummaries: false},   
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "Status", allowSummaries: false},     
                {columnKey: "Actions", allowSummaries: false}]
            },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                    {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                    },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
                        ],
                        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
               igGridHideOption();
            //$(".ui-iggrid-featurechooserbutton").remove(); 
            }
                });

}

function getNCTOrders(ajaxUrl) {    
    $("#orderList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format:"date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "160px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string", width: "120px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", width: "120px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Invoice Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number", format: "number", width: "140px", columnCssClass: "data__aliright"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string",  width: "120px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Returned Qty", key: "ReturnQty", dataType: "number",  width: "120px", columnCssClass: "centerAlignment"},
            {headerText: "Returned Value", key: "ReturnValue", dataType: "number", format: "number",  width: "140px", columnCssClass: "data__aliright"},
            {headerText: "NCT Status", key: "nctTracker", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "110px", columnCssClass: "centerAlignment"},
            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: "chk", allowSorting: false},
                    {columnKey: "Actions", allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "OrderValue", allowFiltering: true},
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: 'ReturnQty', allowFiltering: true},
                    {columnKey: 'ReturnValue', allowFiltering: true},
                    {columnKey: 'Status', allowFiltering: true},
                    {columnKey: 'nctTracker', allowFiltering: true},
                    
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
                                  
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},  
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},    
                {columnKey: "invoice_code", allowSummaries: false}, 
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},  
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "ADT", allowSummaries: false},
                {columnKey: "del_name", allowSummaries: false},        
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "ReturnQty", allowSummaries: false},   
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "nctTracker", allowSummaries: false},
                {columnKey: "Actions", allowSummaries: false},     
                {columnKey: "Status", allowSummaries: false}]
            },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                    {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                    },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
                        ],
                        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
               igGridHideOption();
            //$(".ui-iggrid-featurechooserbutton").remove(); 
            }
                });

}

function getSelectedOFDRows(ajaxUrl) {

var ids = $('#checked').val();

}

function partialDeliver(gds_order_id){
    window.open('/salesorders/getDeliveryDetails?gds_order_ids='+gds_order_id);
}



function getOutForDeliveryOrders(ajaxUrl) {   
    $("#orderList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' id='checked' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "130px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "120px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "150px"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number",format: "number", width: "140px",columnCssClass: "data__aliright"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", width: "120px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string", width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Order Qty", key: "orderedQty", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Shipped Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},
            {headerText: "Cartons", key: "cartons", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Bags", key: "bags", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "ST DEL Name", key: "st_de_name", dataType: "string", width: "120px"},
            {headerText: "ST DEL Date", key: "st_del_date", dataType: "date", format: "date", width: "120px"},
            {headerText: "ST Vehicle No", key: "st_vehicle_no", dataType: "string", width: "120px"},
            {headerText: "ST Driver Name", key: "st_driver_name", dataType: "string", width: "120px"},
            {headerText: "ST Driver Mobile", key: "st_driver_mobile", dataType: "string", width: "125px"},
            {headerText: "ST Received By", key: "st_re_name", dataType: "string", width: "125px"},
            {headerText: "ST Received Date", key: "st_received_at", dataType: "date", format: "date", width: "130px"},
            {headerText: "ST Docket No", key: "st_docket_no", dataType: "string", width: "130px"},
            {headerText: "Crates", key: "crates", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "centerAlignment", width: "110px"},
            {headerText: "Delivery Actions", key: "deliveractions", dataType: "string", columnCssClass: "centerAlignment", width: "110px"},
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'chk', allowSorting: false},
                    {columnKey: 'Actions', allowSorting: false},
                ]

            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "crates", allowFiltering: true},
                    {columnKey: "bags", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "cartons", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "deliveractions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "del_name", allowSummaries: false},
                {columnKey: "ADT", allowSummaries: false},       
                {columnKey: "invoice_code", allowSummaries: false},
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "orderedQty", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},
                {columnKey: "cartons", allowSummaries: false},
                {columnKey: "bags", allowSummaries: false},
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "st_de_name", allowSummaries: false},
                {columnKey: "st_del_date", allowSummaries: false},
                {columnKey: "st_vehicle_no", allowSummaries: false},  
                {columnKey: "st_driver_name", allowSummaries: false},
                {columnKey: "st_driver_mobile", allowSummaries: false},
                {columnKey: "st_re_name", allowSummaries: false},
                {columnKey: "st_received_at", allowSummaries: false},
                {columnKey: "st_docket_no", allowSummaries: false},
                {columnKey: "crates", allowSummaries: false},  
                {columnKey: "Actions", allowSummaries: false},
                {columnKey: "deliveractions", allowSummaries: false}]
            },    
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                   {
                       columnKey: "crates",
                       isFixed: true,
                       allowFixing: false,
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false,
                   },
                   {
                       columnKey: "deliveractions",
                       isFixed: true,
                       allowFixing: false,
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
            //$(".ui-iggrid-featurechooserbutton").remove();
        }
    });

}

function getCompletedOrders(ajaxUrl) {    
    $("#orderList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "130px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format:"date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Picklist No", key: "pickno", dataType: "string",  width: "110px"},
            {headerText: "Picked Date", key: "pickedDate", dataType: "date", format: "date", width: "120px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string",  width: "120px"},
            {headerText: "Shipment Date", key: "shipmentDate", dataType: "date", format: "date", width: "120px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string",  width: "120px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number", format: "number",  width: "140px", columnCssClass: "data__aliright"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number", format: "number",  width: "140px", columnCssClass: "data__aliright"},
            {headerText: "Return Value", key: "ReturnValue", dataType: "number", format:"number", columnCssClass: "data__aliright", width: "140px"},
            {headerText: "Hold Count", key: "hold_count", dataType: "number", format:"number", columnCssClass: "dataaliright", width: "100px"},
            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "100px", columnCssClass: "centerAlignment",},

        ],
        features: [
            {   
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: "chk", allowSorting: false},
                    {columnKey: "Actions", allowSorting: false},
                    {columnKey: 'Status', allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "OrderValue", allowFiltering: true},
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                     {columnKey: "Status", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},

                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
                                    
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},  
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "pickno", allowSummaries: false}, 
                {columnKey: "pickedDate", allowSummaries: false},
                {columnKey: "pickedby", allowSummaries: false}, 
                {columnKey: "ADT", allowSummaries: false},
                {columnKey: "del_name", allowSummaries: false},
                {columnKey: "shipmentDate", allowSummaries: false},           
                {columnKey: "invoice_code", allowSummaries: false}, 
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "hold_count", allowSummaries: false},
                {columnKey: "Status", allowSummaries: false},     
                {columnKey: "Actions", allowSummaries: false}]
            },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [

                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
                        igGridHideOption();
                        //$(".ui-iggrid-featurechooserbutton").remove();
                        $(".ui-iggrid-fixcolumn-headerbuttoncontainer").remove();
                }
    });

}

function getHoldOrders(ajaxUrl) {    
    $("#orderList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},            
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "120px"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number",format: "number", width: "140px",columnCssClass: "data__aliright"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string", format: "date", width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number", format: "date", width: "140px", columnCssClass: "data__aliright"},
            {headerText: "Hold Count", key: "hold_count", dataType: "number", format: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string",  width: "110px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Reason", key: "hold_reason", dataType: "date", format: "date", width: "120px"},
            {headerText: "Next SCH Date", key: "nextschdate", dataType: "date", format: "date", width: "120px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "100px", columnCssClass: "centerAlignment"},
            
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: "chk", allowSorting: false},                 
                    {columnKey: "Actions", allowSorting: false},                 
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "OrderValue", allowFiltering: true},
                    {columnKey: "InvoiceValue", allowFiltering: true},                   
                    {columnKey: "chk", allowFiltering: false},                   
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                                       
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "invoice_code", allowSummaries: false},
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "hold_count", allowSummaries: false},     
                {columnKey: "del_name", allowSummaries: false},
                {columnKey: "ADT", allowSummaries: false},
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "hold_reason", allowSummaries: false},
                {columnKey: "nextschdate", allowSummaries: false},
                {columnKey: "Actions", allowSummaries: false}]
            },    
            {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                   {
                       columnKey: "nextschdate",
                       isFixed: true,
                       allowFixing: false                  
                   },         
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false                  
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
            //$(".ui-iggrid-featurechooserbutton").remove();
            $(".ui-iggrid-fixcolumn-headerbuttoncontainer").remove();
        }
    });

}

function getReturnedOrders(ajaxUrl) {    
    $("#orderList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Return Date", key: "ReturnDate", format: "date", dataType: "date",  width: "110px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "160px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string",  width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Invoice Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},   
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number", format: "number", columnCssClass: "data__aliright", width: "140px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format: "date", width: "120px"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Returned Qty", key: "ReturnQty", dataType: "number",  width: "120px", columnCssClass: "centerAlignment"},
            {headerText: "Returned Value", key: "ReturnValue", dataType: "number", format: "number", width: "140px", columnCssClass: "data__aliright"},
            {headerText: "Status", key: "Status", dataType: "string", width: "150px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "110px", columnCssClass: "centerAlignment"},    

        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: "chk", allowSorting: false},                 
                    {columnKey: "Actions", allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "OrderValue", allowFiltering: true},                    
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "ReturnQty", allowFiltering: true},
                    {columnKey: "Status", allowFiltering: false},
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "ReturnDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},    
                {columnKey: "invoice_code", allowSummaries: false}, 
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},  
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "ADT", allowSummaries: false},
                {columnKey: "del_name", allowSummaries: false},        
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "ReturnQty", allowSummaries: false},   
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "Status", allowSummaries: false},     
                {columnKey: "Actions", allowSummaries: false}]
            },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
           igGridHideOption();
            //$(".ui-iggrid-featurechooserbutton").remove();
        }
    });

}



function getReturnedApprovedAtHubOrders(ajaxUrl) {    
    $("#orderList").igGrid({
                        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format: "date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "160px"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number",format: "number", width: "140px",columnCssClass: "data__aliright"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string",  width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Invoice Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},   
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format: "date", width: "120px"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Returned Qty", key: "ReturnQty", dataType: "number",  width: "120px", columnCssClass: "centerAlignment"},
            {headerText: "Returned Value", key: "ReturnValue", dataType: "number", width: "140px", columnCssClass: "data__aliright"},
            {headerText: "Damaged Qty", key: "DamagedValue", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Missing Qty", key: "MissingValue", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Excess Qty", key: "ExcessValue", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "110px", columnCssClass: "centerAlignment"},    

                        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: "chk", allowSorting: false},                 
                    {columnKey: "Actions", allowSorting: false},                 
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "OrderValue", allowFiltering: true},                    
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "DamagedValue", allowFiltering: true},
                    {columnKey: "MissingValue", allowFiltering: true},
                    {columnKey: "ExcessValue", allowFiltering: true},
                    {columnKey: "ReturnQty", allowFiltering: true},
                    {columnKey: "Status", allowFiltering: true},
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},     
                {columnKey: "invoice_code", allowSummaries: false},
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},
                {columnKey: "ADT", allowSummaries: false},
                {columnKey: "del_name", allowSummaries: false},
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "ReturnQty", allowSummaries: false},
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "DamagedValue", allowSummaries: false},
                {columnKey: "MissingValue", allowSummaries: false},  
                {columnKey: "ExcessValue", allowSummaries: false},
                {columnKey: "Status", allowSummaries: false},  
                {columnKey: "Actions", allowSummaries: false}]
            },    
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
                        ],
                        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
            //$(".ui-iggrid-featurechooserbutton").remove();  
            }
                });

}

function invoiceList(order_id) {
    $("#invoiceList").igGrid({
                        columns: [
                                {headerText: "Invoice No", key: "invoiceId", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Order ID", key: "orderId", dataType: "string", columnCssClass: "centerAlignment"},
                        {headerText: "Billing Name", key: "billingName", dataType: "string"},
                        {headerText: "Invoice Date", key: "invoiceDate", dataType: "date", columnCssClass: "centerAlignment"},
            {headerText: "Total Qty", key: "TotalQty", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Total Amount", key: "totalAmount", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Status", key: "status", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "centerAlignment"}
                        ],
                        features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalInvoices'
            }
                        ],
                        primaryKey: "invoiceId",
        //width: '100%',
        type: 'remote',
        dataSource: "/salesorders/ajax/invoices/" + order_id,
        responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }
        });
}


function cancelList(order_id) {
    $("#cancelList").igGrid({
                        columns: [
            {headerText: "Cancel ID", key: "cancelId", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Order ID", key: "orderId", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Order Date", key: "orderDate", dataType: "dateTime", columnCssClass: "centerAlignment", format: "dateTime"},
            {headerText: "Cancelled Date", key: "cancelDate", dataType: "dateTime", columnCssClass: "centerAlignment", format: "dateTime"},
            {headerText: "Cancelled Qty", key: "qtyCancelled", dataType: "number", columnCssClass: "centerAlignment"},
            {headerText: "Cancelled Amount", key: "cancelledAmt", dataType: "number", columnCssClass: "centerAlignment"},
            {headerText: "Status", key: "status", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "centerAlignment"}
                        ],
                        features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalCancelled'
            }
                        ],
                        primaryKey: "cancelId",
       // width: '100%',
        type: 'remote',
        dataSource: "/salesorders/ajax/orderCancelList/" + order_id,
        responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }
        });
}

function shipmnentList(orderid) {
    $("#shipmentList").igGrid({
                        columns: [
            {headerText: "Shipment ID", key: "shipmentId", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Order ID", key: "orderId", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Order Date", key: "orderDate", dataType: "dateTime", columnCssClass: "centerAlignment", format: "dateTime"},
            {headerText: "Shipped Date", key: "shipmentDate", dataType: "dateTime", columnCssClass: "centerAlignment", format: "dateTime"},
            {headerText: "Shop Name", key: "shippedTo", dataType: "string"},
            {headerText: "Shipped Qty", key: "shippedQty", dataType: "number", columnCssClass: "centerAlignment"},
            {headerText: "Status", key: "Status", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Actions", key: "shipmentActions", dataType: "string", columnCssClass: "centerAlignment"}
                        ],
                        features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            /*{
                                     name: "Filtering",
             type: "remote",
             mode: "simple",
             filterDialogContainment: "window",
             columnSettings: [
             {columnKey: 'shipmentActions', allowFiltering: false },
             ]
                                 },*/                       
            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalShipments'
            }
                        ],
                        primaryKey: "shipmentId",
        // width: '100%',
        type: 'remote',
        dataSource: "/salesorders/ajax/shipment/" + orderid,
        responseDataKey: 'data',
        rendered: function (evt, ui) {
            //$("#shipmentList_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
            //$("#shipmentList_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
            //$("#shipmentList_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
            //$("#shipmentList_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
            //$("#shipmentList_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
            //$("#shipmentList_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
            //$("#shipmentList_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
            //$("#shipmentList_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
            //$("#shipmentList_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
            //$("#shipmentList_container").find(".ui-iggrid-filtericonon").closest("li").remove();   
            //$("#shipmentList_container").find(".ui-iggrid-filtericonnoton").closest("li").remove(); 
                    }
        });
}


function commentHistoryList(order_id) {
    $("#commentList").igGrid({
                        columns: [
            {headerText: "SNo", key: "SNo", dataType: "number", width: "5%", columnCssClass: "centerAlignment"},
            {headerText: "Comment Type", key: "commentType", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Comment Date", key: "commentDate", dataType: "dateTime", columnCssClass: "centerAlignment", format: "dateTime"},
            {headerText: "Status", key: "Status", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "By", key: "commentBy", dataType: "string", columnCssClass: "centerAlignment"},
            {headerText: "Comment", key: "Comment", dataType: "string"}
                        ],
                        features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalComment'
            }
                        ],
                        primaryKey: "returnId",
        //width: '100%',
        type: 'remote',
        dataSource: "/salesorders/commentHistory/" + order_id,
        responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }
        });
}

function verificationGrid(order_id) {
    $("#verificationList").igGrid({
                        columns: [
            {headerText: "SNo", key: "SNo", dataType: "number", width: "5%"},
            {headerText: "Container", width:"50%",key: "ContainerName", dataType: "string"},
            {headerText: "File Path", key: "FilePath", dataType: "string"},
            {headerText: "Verified By", key: "VerifiedBy", dataType: "string"},
            {headerText: "Created On", key: "CreatedOn", dataType: "dateTime", format: "dateTime"},
                        ],
                        features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                ]
            },
            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalVerification'
            }
                        ],
                        primaryKey: "returnId",
        //width: '100%',
        type: 'remote',
        dataSource: "/salesorders/verficationlist/" + order_id,
        responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }
        });
}

function returnOrderList(order_id) {
    $("#returnList").igGrid({
                        columns: [
                                {headerText: "Return ID", key: "returnId", dataType: "string"},
                                {headerText: "Order ID", key: "orderId", dataType: "string"},
                                {headerText: "Order Date", key: "orderDate", dataType: "string"},
                                {headerText: "Order Return Date", key: "returnDate", dataType: "string"},
                                {headerText: "Returned Qty", key: "qtyReturned", dataType: "number", columnCssClass: "alignRight"},
                                {headerText: "Returned Value", key: "returnValue", dataType: "number", format:'0.00',columnCssClass: "alignRight"},
                                {headerText: "Actions", key: "Actions", dataType: "string"}
                        ],
                        features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            /*{
                                     name: "Filtering",
             type: "remote",
             mode: "simple",
             filterDialogContainment: "window",
             columnSettings: [
             {columnKey: 'Actions', allowFiltering: false },
             ]
                                 }
             ,*/                                    
            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalReturns'
            }
                        ],
                        primaryKey: "returnId",
        //width: '100%',
        type: 'remote',
        dataSource: "/salesorders/getreturns/" + order_id,
        responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }
        });

    checkCreateReturns();

}

function checkCreateReturns(){
  $.get( "/salesorders/checkCreateReturns", function( result ) {
    if(result == 0){
      $('#createreturn_div').hide();
    }
    else{
      $('#createreturn_div').show(); 
    }
});
}

function refundOrderList(order_id) {
    $("#refundList").igGrid({
                        columns: [
                                {headerText: "Refund ID", key: "refundId", dataType: "number"},
            {headerText: "Order ID", key: "orderId", dataType: "number"},
                                {headerText: "Total Amount (Rs.)", key: "totAmount", dataType: "string"},
            {headerText: "Refund Amount (Rs.)", key: "refundAmount", dataType: "string"},
            {headerText: "Refund Date", key: "refundDate", dataType: "dateTime", format: "dateTime"},
            {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "centerAlignment"}
                        ],
                        features: [
            {
                name: "Sorting",
                type: "local",
                columnSettings: [
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            /*{
                                     name: "Filtering",
             type: "remote",
             mode: "simple",
             filterDialogContainment: "window",
             columnSettings: [
             {columnKey: 'Actions', allowFiltering: false },
             ]
                                 }
             ,*/                                    
            {
                name: 'Paging',
                type: "remote",
                pageSize: 25,
                recordCountKey: 'totalRefunds'
            }
                        ],
                        primaryKey: "returnId",
        //width: '100%',
        type: 'remote',
        dataSource: "/salesorders/getrefunds/" + order_id,
        responseDataKey: 'data',
        rendered: function (evt, ui) {

                    }
        });
}

function collectionGrid(orderId) {


    $('.collectionGrid').igGrid({
        dataSource: '/salesorders/getCollections/'+orderId,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'data',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [                     
            {headerText: 'Date', key: 'collected_on', dataType: 'date', width: '8%'},
            {headerText: 'Amount', key: 'amount', dataType: 'number', width: '8%'},            
            {headerText: 'Collected By', key: 'CollectedByName', dataType: 'action', width: '10%'},   
            {headerText: 'TRANS By', key: 'PaidByName', dataType: 'action', width: '8%'},   
            {headerText: 'Payment Mode', key: 'PaymentModeLookup', dataType: 'action', width: '10%'},   
            {headerText: 'Reference No', key: 'reference_no', dataType: 'action', width: '10%'},   
            {headerText: 'Proof', key: 'proof', dataType: 'string', width: '10%'},   
            {headerText: 'Remittance Code', key:'remittance_code', dataType:'string',width:'10%'},
            {headerText: 'Remittance Date', key:'created_at', dataType:'date',width:'10%'},
            {headerText: 'Remittance Status', key:'status', dataType:'number', width: '10%'},
            {headerText: 'Balance Amount', key: 'BalanceAmt', dataType: 'string', width: '0%'},   
            {headerText: 'Action', key: 'edit', dataType: 'string',width: '6%'},   
        ],
        features: [           
            {
                name: 'Paging',
                type: 'local',
                pageSize: 5,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
        ],
        primaryKey: 'Date',
        //width: '100%',
        dataRendered: function(evt, ui) {

          $('.balanceAmt').html($('td[aria-describedby="_BalanceAmt"]').html());
        },
        initialDataBindDepth: 0,
        localSchemaTransform: false});

}

function getReturnedApprovalOrders(ajaxUrl) {    
    $("#orderList").igGrid({
                        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format: "date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "120px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "160px"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number",format: "number", width: "140px",columnCssClass: "data__aliright"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string",  width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Invoice Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},   
            {headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Returned Qty", key: "ReturnQty", dataType: "number",  width: "120px", columnCssClass: "centerAlignment"},
            {headerText: "Returned Value", key: "ReturnValue", dataType: "number", format: "number", width: "150px", columnCssClass: "data__aliright"},
            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "110px", columnCssClass: "centerAlignment"},    

                        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: "chk", allowSorting: false},                 
                    {columnKey: "Actions", allowSorting: false},                 
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "OrderValue", allowFiltering: true},                    
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "ReturnQty", allowFiltering: true},
                    {columnKey: "Status", allowFiltering: true},
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false, 
               summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},     
                {columnKey: "invoice_code", allowSummaries: false},
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},
                {columnKey: "ADT", allowSummaries: false},
                {columnKey: "del_name", allowSummaries: false},
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "ReturnQty", allowSummaries: false},
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "Status", allowSummaries: false},  
                {columnKey: "Actions", allowSummaries: false}]
            },    
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
                        ],
                        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
            //$(".ui-iggrid-featurechooserbutton").remove();  
            }
                });

}

function getMissingQuantitieOrders(ajaxUrl) {    
    $("#orderList").igGrid({
                        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number",  width: "140px", columnCssClass: "dataaliright"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format: "date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "160px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string",  width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Invoice Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},   
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number", format: "0.00", columnCssClass: "data__aliright", width: "150px"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Returned Qty", key: "ReturnQty", dataType: "number",  width: "120px", columnCssClass: "centerAlignment"},
            {headerText: "Returned Value", key: "ReturnValue", dataType: "number", format: "0.00", width: "150px", columnCssClass: "data__aliright"},
            {headerText: "Missing Qty", key: "MissingQty", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Missing Value", key: "MissingValue", dataType: "number",format: "0.00",width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Damaged Qty", key: "DamagedQty", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Damaged Value", key: "DamagedValue", dataType: "number",format: "0.00", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Excess Qty", key: "ExcessQty", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Excess Value", key: "ExcessValue", dataType: "number",format: "0.00", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "110px", columnCssClass: "centerAlignment"},    

                        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: "chk", allowSorting: false},                 
                    {columnKey: "Actions", allowSorting: false},                 
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "OrderValue", allowFiltering: true},                    
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "ReturnQty", allowFiltering: true},
                    {columnKey: "DamagedValue", allowFiltering: true},
                    {columnKey: "MissingValue", allowFiltering: true},
                    {columnKey: "DamagedQty", allowFiltering: true},
                    {columnKey: "MissingQty", allowFiltering: true},
                    {columnKey: "ExcessQty", allowFiltering: true},
                    {columnKey: "ExcessValue", allowFiltering: true},
                    {columnKey: "Status", allowFiltering: true},
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false, 
               summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "invoice_code", allowSummaries: false},
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "ADT", allowSummaries: false},
                {columnKey: "del_name", allowSummaries: false},
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "ReturnQty", allowSummaries: false},
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "DamagedValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "MissingValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "DamagedQty", allowSummaries: false},
                {columnKey: "MissingQty", allowSummaries: false},
                {columnKey: "ExcessQty", allowSummaries: false},
              {columnKey: "ExcessValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "Status", allowSummaries: false},  
                {columnKey: "Actions", allowSummaries: false}]
            },    
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
                        ],
                        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
            //$(".ui-iggrid-featurechooserbutton").remove();  
            }
                });

}




function getDamagedQuantitieOrders(ajaxUrl) {    
    $("#orderList").igGrid({
                        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number",  width: "140px", columnCssClass: "dataaliright"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format: "date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "160px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string",  width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Invoice Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},   
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number", format: "0.00", columnCssClass: "data__aliright", width: "150px"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Returned Qty", key: "ReturnQty", dataType: "number",  width: "120px", columnCssClass: "centerAlignment"},
            {headerText: "Returned Value", key: "ReturnValue", dataType: "number", format: "0.00", width: "150px", columnCssClass: "data__aliright"},
            {headerText: "Missing Qty", key: "MissingQty", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Missing Value", key: "MissingValue", dataType: "number",format: "0.00", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Damaged Qty", key: "DamagedQty", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Damaged Value", key: "DamagedValue", dataType: "number",format: "0.00", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Excess Qty", key: "ExcessQty", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Excess Value", key: "ExcessValue",dataType: "number",format: "0.00",width: "130px", columnCssClass: "dataaliright"},

            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "110px", columnCssClass: "centerAlignment"},    

                        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: "chk", allowSorting: false},                 
                    {columnKey: "Actions", allowSorting: false},                 
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "OrderValue", allowFiltering: true},                    
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "ReturnQty", allowFiltering: true},
                    {columnKey: "DamagedValue", allowFiltering: true},
                    {columnKey: "MissingValue", allowFiltering: true},
                    {columnKey: "DamagedQty", allowFiltering: true},
                    {columnKey: "MissingQty", allowFiltering: true},
                    {columnKey: "ExcessQty", allowFiltering: true},
                    {columnKey: "ExcessValue", allowFiltering: true},
                    {columnKey: "Status", allowFiltering: true},
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false, 
               summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "invoice_code", allowSummaries: false},
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "ADT", allowSummaries: false},
                {columnKey: "del_name", allowSummaries: false},
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "ReturnQty", allowSummaries: false},
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "DamagedValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "MissingValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},                
                {columnKey: "DamagedQty", allowSummaries: false},
                {columnKey: "MissingQty", allowSummaries: false},
                {columnKey: "ExcessQty", allowSummaries: false},  
                {columnKey: "ExcessValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},                
                {columnKey: "Status", allowSummaries: false},  
                {columnKey: "Actions", allowSummaries: false}]
            },    
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
                        ],
                        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
            //$(".ui-iggrid-featurechooserbutton").remove();  
            }
                });

}
function getApprovedMissingQuantitie(ajaxUrl) {    
    $("#orderList").igGrid({
                        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number",  width: "140px", columnCssClass: "dataaliright"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format: "date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "160px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string",  width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Invoice Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},   
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number", format: "0.00", columnCssClass: "dataaliright", width: "120px"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Missing Qty", key: "MissingQty", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Missing Value", key: "MissingValue", dataType: "number",format: "0.00",width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Returned Qty", key: "ReturnQty", dataType: "number",  width: "120px", columnCssClass: "centerAlignment"},
            {headerText: "Returned Value", key: "ReturnValue", dataType: "number", format: "0.00", width: "120px", columnCssClass: "dataaliright"},
            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "110px", columnCssClass: "centerAlignment"},    

                        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: "chk", allowSorting: false},                 
                    {columnKey: "Actions", allowSorting: false},                 
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "OrderValue", allowFiltering: true},                    
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "MissingValue", allowFiltering: true},
                    {columnKey: "MissingQty", allowFiltering: true},
                    {columnKey: "ReturnQty", allowFiltering: true},
                    {columnKey: "Status", allowFiltering: true},
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false, 
               summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "invoice_code", allowSummaries: false},
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "ADT", allowSummaries: false},
                {columnKey: "del_name", allowSummaries: false},
                {columnKey: "MissingValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},                
                {columnKey: "MissingQty", allowSummaries: false},
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "ReturnQty", allowSummaries: false},
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                  {columnKey: "Status", allowSummaries: false},
                {columnKey: "Actions", allowSummaries: false}]
              },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
                        ],
                        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
            //$(".ui-iggrid-featurechooserbutton").remove();  
            }
                });

}
function getApprovedDamagedQuantitie(ajaxUrl) {    
    $("#orderList").igGrid({
                        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number",  width: "140px", columnCssClass: "dataaliright"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format: "date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "160px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string",  width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Invoice Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},   
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number", format: "0.00", columnCssClass: "dataaliright", width: "120px"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Damaged Qty", key: "DamagedQty", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Damaged Value", key: "DamagedValue", dataType: "number",format: "0.00", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Returned Qty", key: "ReturnQty", dataType: "number",  width: "120px", columnCssClass: "centerAlignment"},
            {headerText: "Returned Value", key: "ReturnValue", dataType: "number", format: "0.00", width: "120px", columnCssClass: "dataaliright"},
            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "110px", columnCssClass: "centerAlignment"},    

                        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: "chk", allowSorting: false},                 
                    {columnKey: "Actions", allowSorting: false},                 
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "OrderValue", allowFiltering: true},                    
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "DamagedValue", allowFiltering: true},
                    {columnKey: "DamagedQty", allowFiltering: true},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "ReturnQty", allowFiltering: true},
                    {columnKey: "Status", allowFiltering: true},
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false, 
               summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "invoice_code", allowSummaries: false},
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "ADT", allowSummaries: false},
                {columnKey: "del_name", allowSummaries: false},
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "ReturnQty", allowSummaries: false},
                 {columnKey: "DamagedValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},                
                {columnKey: "DamagedQty", allowSummaries: false},
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                   {columnKey: "Status", allowSummaries: false}, 
                {columnKey: "Actions", allowSummaries: false}]
              },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
                        ],
                        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
            //$(".ui-iggrid-featurechooserbutton").remove();  
            }
                });

}
function getShortCollections(ajaxUrl) {    
    $("#orderList").igGrid({
                        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number",  width: "140px", columnCssClass: "dataaliright"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format: "date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "160px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string",  width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "110px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Invoice Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},   
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number", format: "0.00", columnCssClass: "data__aliright", width: "150px"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "Returned Qty", key: "ReturnQty", dataType: "number",  width: "120px", columnCssClass: "centerAlignment"},
            {headerText: "Returned Value", key: "ReturnValue", dataType: "number", format: "0.00", width: "120px", columnCssClass: "dataaliright"},
            {headerText: "Due Amount", key: "DueAmount", dataType: "number", format: "0.00", width: "120px", columnCssClass: "dataaliright"},
            {headerText: "Status", key: "Status", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "110px", columnCssClass: "centerAlignment"},    

                        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: "chk", allowSorting: false},                 
                    {columnKey: "Actions", allowSorting: false},                 
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "OrderValue", allowFiltering: true},                    
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "DueAmount", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "ReturnQty", allowFiltering: true},
                    {columnKey: "Status", allowFiltering: true},
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false, 
               summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "invoice_code", allowSummaries: false},
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "ADT", allowSummaries: false},
                {columnKey: "del_name", allowSummaries: false},
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "ReturnQty", allowSummaries: false},
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "DueAmount", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "Status", allowSummaries: false},
                {columnKey: "Actions", allowSummaries: false}]
              },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
                        ],
                        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
            igGridHideOption();
            //$(".ui-iggrid-featurechooserbutton").remove();  
            }
                });

}

function getSITHubToDc(ajaxUrl) {   
    $("#orderList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},            
            {headerText: "Delivery Date", key: "ADT", dataType: "date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "150px"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number",format: "number", width: "140px",columnCssClass: "data__aliright"},
            {headerText: "Returned Value", key: "ReturnValue", dataType: "number", width: "140px", columnCssClass: "data__aliright"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string", width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Order Qty", key: "orderedQty", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Shipped Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},
            {headerText: "Cartons", key: "cartons", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Bags", key: "bags", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "ST DE Name", key: "rt_de_name", dataType: "string", width: "120px"},
            {headerText: "ST DE Date", key: "rt_del_date", dataType: "date", format: "date", width: "120px"},
            {headerText: "ST Vehicle No", key: "rt_vehicle_no", dataType: "string", width: "120px"},
            {headerText: "ST Driver Name", key: "rt_driver_name", dataType: "string", width: "120px"},
            {headerText: "ST Driver Mobile", key: "rt_driver_mobile", dataType: "string", width: "125px"},
            {headerText: "ST Docket No", key: "rt_docket_no", dataType: "string", width: "130px"},
            {headerText: "Damaged Qty", key: "DamagedValue", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Missing Qty", key: "MissingValue", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Excess Qty", key: "ExcessValue", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Crates", key: "crates", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "centerAlignment", width: "110px"},
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'chk', allowSorting: false},
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "crates", allowFiltering: true},
                    {columnKey: "bags", allowFiltering: true},
                    {columnKey: "DamagedValue", allowFiltering: true},
                    {columnKey: "MissingValue", allowFiltering: true},
                    {columnKey: "ExcessValue", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "cartons", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "del_name", allowSummaries: false}, 
                {columnKey: "ADT", allowSummaries: false},    
                {columnKey: "invoice_code", allowSummaries: false},
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "orderedQty", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},
                {columnKey: "cartons", allowSummaries: false},
                {columnKey: "bags", allowSummaries: false}, 
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "rt_de_name", allowSummaries: false},  
                {columnKey: "rt_del_date", allowSummaries: false},
                {columnKey: "rt_vehicle_no", allowSummaries: false},
                {columnKey: "rt_driver_name", allowSummaries: false},  
                {columnKey: "rt_driver_mobile", allowSummaries: false},
                {columnKey: "rt_docket_no", allowSummaries: false},
                {columnKey: "DamagedValue", allowSummaries: false},
                {columnKey: "MissingValue", allowSummaries: false},  
                {columnKey: "ExcessValue", allowSummaries: false},
                {columnKey: "crates", allowSummaries: false},  
                {columnKey: "Actions", allowSummaries: false}]
            },    
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                   {
                       columnKey: "crates",
                       isFixed: true,
                       allowFixing: false,
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false,
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
                 igGridHideOption();
                 //$(".ui-iggrid-featurechooserbutton").remove();
        }
    });

}


function getStockInDcOrders(ajaxUrl) {   
    $("#orderList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "120px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},            
            {headerText: "Delivery Date", key: "ADT", dataType: "date", width: "120px"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "Retailer Name", key: "Customer", dataType: "string", width: "150px"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number",format: "number", width: "140px",columnCssClass: "data__aliright"},
            {headerText: "Returned Value", key: "ReturnValue", dataType: "number", width: "140px", columnCssClass: "data__aliright"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string", width: "120px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string", width: "110px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Retailer Code", key: "custcode", dataType: "string", width: "110px"},
            {headerText: "Order Qty", key: "orderedQty", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Shipped Qty", key: "InvoiceQty", dataType: "number", width: "100px", columnCssClass: "centerAlignment"},
            {headerText: "Cartons", key: "cartons", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Bags", key: "bags", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Picked By", key: "pickedby", dataType: "string", width: "120px"},
            {headerText: "ST DE Name", key: "rt_de_name", dataType: "string", width: "120px"},
            {headerText: "ST DE Date", key: "rt_del_date", dataType: "date", format: "date", width: "120px"},
            {headerText: "ST Vehicle No", key: "rt_vehicle_no", dataType: "string", width: "120px"},
            {headerText: "ST Driver Name", key: "rt_driver_name", dataType: "string", width: "120px"},
            {headerText: "ST Driver Mobile", key: "rt_driver_mobile", dataType: "string", width: "125px"},
            {headerText: "ST Received By", key: "rt_re_name", dataType: "string", width: "125px"},
            {headerText: "ST Received Date", key: "rt_received_at", dataType: "date", format: "date", width: "130px"},
            {headerText: "ST Docket No", key: "rt_docket_no", dataType: "string", width: "130px"},
            {headerText: "Damaged Qty", key: "DamagedValue", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Missing Qty", key: "MissingValue", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Excess Qty", key: "ExcessValue", dataType: "number", width: "130px", columnCssClass: "dataaliright"},
            {headerText: "Crates", key: "crates", dataType: "number", width: "80px", columnCssClass: "centerAlignment"},
            {headerText: "Actions", key: "Actions", dataType: "string", columnCssClass: "centerAlignment", width: "110px"},
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'chk', allowSorting: false},
                    {columnKey: 'Actions', allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "ReturnValue", allowFiltering: true},
                    {columnKey: "DamagedValue", allowFiltering: true},
                    {columnKey: "MissingValue", allowFiltering: true},
                    {columnKey: "ExcessValue", allowFiltering: true},
                    {columnKey: "crates", allowFiltering: true},
                    {columnKey: "bags", allowFiltering: true},
                    {columnKey: "invoice_code", allowFiltering: true},
                    {columnKey: "cartons", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    
                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
              
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]}, 
                {columnKey: "del_name", allowSummaries: false}, 
                {columnKey: "ADT", allowSummaries: false},    
                {columnKey: "invoice_code", allowSummaries: false},
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "Customer", allowSummaries: false},     
                {columnKey: "custcode", allowSummaries: false},
                {columnKey: "orderedQty", allowSummaries: false},
                {columnKey: "InvoiceQty", allowSummaries: false},
                {columnKey: "cartons", allowSummaries: false},
                {columnKey: "bags", allowSummaries: false}, 
                {columnKey: "pickedby", allowSummaries: false},
                {columnKey: "rt_de_name", allowSummaries: false},  
                {columnKey: "rt_del_date", allowSummaries: false},
                {columnKey: "rt_vehicle_no", allowSummaries: false},
                {columnKey: "rt_driver_name", allowSummaries: false},  
                {columnKey: "rt_driver_mobile", allowSummaries: false},
                {columnKey: "rt_re_name", allowSummaries: false},
                {columnKey: "rt_received_at", allowSummaries: false},  
                {columnKey: "rt_docket_no", allowSummaries: false},
                {columnKey: "DamagedValue", allowSummaries: false},
                {columnKey: "MissingValue", allowSummaries: false},  
                {columnKey: "ExcessValue", allowSummaries: false},
                {columnKey: "crates", allowSummaries: false},  
                {columnKey: "Actions", allowSummaries: false}]
            },    
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [
                   {
                       columnKey: "crates",
                       isFixed: true,
                       allowFixing: false,
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false,
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
                 igGridHideOption();
                 //$(".ui-iggrid-featurechooserbutton").remove();
        }
    });

}

function PendingPaymentHistoryGrid(orderId) {
    $('#paymentHistoryList').igGrid({
        dataSource: '/salesorders/getpendingpayments/'+orderId,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'data',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
         columns: [                     
            {headerText: 'Date', key: 'hist_date', dataType: 'date', width: '10%'},            
            //{headerText: 'Previous Status', key: 'PreviousStatus', dataType: 'string', width: '10%'},
            {headerText: 'Current Status', key: 'CurrentStaus', dataType: 'string', width: '10%'},
            {headerText: 'Amount', key: 'nct_amount', dataType: 'number', width: '10%'},
            {headerText: 'Collected By', key: 'nct_collected_by', dataType: 'string', width: '10%'},
            {headerText: 'Holder Name', key: 'nct_holdername', dataType: 'string', width: '10%'}, 
            {headerText: 'Bank Name', key: 'nct_bank', dataType: 'string', width: '10%'},
            {headerText: 'Reference No', key: 'nct_ref_no', dataType: 'number', width: '10%'},
            {headerText: 'Comment', key: 'comment', dataType: 'string', width: '20%'}
        ],
        features: [           
            {
                name: 'Paging',
                type: 'local',
                pageSize: 5,
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        });

}

function getUnpaidOrders(ajaxUrl) {    
    $("#orderList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "130px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format:"date", width: "120px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number",  width: "140px", columnCssClass: "dataaliright"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string",  width: "120px"},
            {headerText: "Shipment Date", key: "shipmentDate", dataType: "date", format: "date", width: "120px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string",  width: "120px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number",  width: "120px", columnCssClass: "dataaliright"},
            {headerText: "Return Value", key: "ReturnValue", dataType: "number", format:"number", columnCssClass: "dataaliright", width: "120px"},
            {headerText: "Collection Code", key: "collection_code", dataType: "string", width: "120px"},
            {headerText: "Collection Value", key: "collected_amount", dataType: "number", format:"number", columnCssClass: "dataaliright", width: "120px"},
            {headerText: "Collected By", key: "collected_by", dataType: "string", width: "100px"},
            {headerText: "Collection Date", key: "collection_date", dataType: "date", format:"date", width: "120px"},
            {headerText: "Remittance Code", key: "remittance_code", dataType: "string", columnCssClass: "dataaliright", width: "150px"},
            {headerText: "Remittance Date", key: "remittance_date", dataType: "date", format:"date", width: "130px"},
            {headerText: "Hub Approve Date", key: "hub_appr_date", dataType: "date", format:"date", width: "135px"},
            {headerText: "Hub Approve By", key: "hub_appr_by", dataType: "string", width: "120px"},
            {headerText: "Finance Approve Date", key: "fin_appr_date", dataType: "date", format:"date", width: "135px"},
            {headerText: "Finance Approve By", key: "fin_appr_by", dataType: "string", format:"string", width: "120px"},
            {headerText: "Order Status", key: "Status", dataType: "string", width: "100px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "100px", columnCssClass: "centerAlignment",},

        ],
        features: [
            {   
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: "chk", allowSorting: false},                                       
                    {columnKey: "Actions", allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},

                ]
            },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [

                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
                        igGridHideOption();
                        //$(".ui-iggrid-featurechooserbutton").remove();
                        $(".ui-iggrid-fixcolumn-headerbuttoncontainer").remove();
                }
    });

}


function getUnpaidOrders(ajaxUrl) {    
    $("#orderList").igGrid({
        columns: [
            {headerText: "<input type='checkbox' name='chk[]' onclick='checkAll(this);' class='checkboxmarleft'>", key: "chk", dataType: "string", columnCssClass: "checkboxmarleft", width: "50px"},
            {headerText: "PLM", key: "ChannelName", dataType: "string", columnCssClass: "", width: "50px"},
            {headerText: "Order ID", key: "OrderID", dataType: "string", width: "130px"},
            {headerText: "Order Date", key: "OrderDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Delivery Date", key: "ADT", dataType: "date", format:"date", width: "120px"},
            {headerText: "Order Value", key: "OrderValue", dataType: "number", format: "number",  width: "140px", columnCssClass: "data__aliright"},
            {headerText: "Hub", key: "Hub", dataType: "string", columnCssClass: "", width: "100px"},
            {headerText: "Spoke", key: "spoke", dataType: "string", width: "150px"},
            {headerText: "Beat", key: "beat", dataType: "string", width: "150px"}, 
            {headerText: "Area", key: "Area", dataType: "string", width: "150px"},
            {headerText: "DEL Executive", key: "del_name", dataType: "string",  width: "120px"},
            {headerText: "Shipment Date", key: "shipmentDate", dataType: "date", format: "date", width: "120px"},
            {headerText: "Invoice No", key: "invoice_code", dataType: "string",  width: "120px"},
            {headerText: "Invoice Date", key: "InvoiceDate", dataType: "date", format: "date", width: "100px"},
            {headerText: "Invoice Value", key: "InvoiceValue", dataType: "number", format: "number", width: "140px", columnCssClass: "data__aliright"},
            {headerText: "Return Value", key: "ReturnValue", dataType: "number", format:"number", columnCssClass: "data__aliright", width: "140px"},
            {headerText: "Collection Code", key: "collection_code", dataType: "string", width: "120px"},
            {headerText: "Collection Value", key: "collected_amount", dataType: "number", format:"number", columnCssClass: "data__aliright", width: "140px"},
            {headerText: "Collected By", key: "collected_by", dataType: "string", width: "100px"},
            {headerText: "Collection Date", key: "collection_date", dataType: "date", format:"date", width: "120px"},
            {headerText: "Remittance Code", key: "remittance_code", dataType: "string", columnCssClass: "dataaliright", width: "155px"},
            {headerText: "Remittance Date", key: "remittance_date", dataType: "date", format:"date", width: "130px"},
            {headerText: "Hub Approve Date", key: "hub_appr_date", dataType: "date", format:"date", width: "135px"},
            {headerText: "Hub Approve By", key: "hub_appr_by", dataType: "string", width: "120px"},
            {headerText: "Fin Approve Date", key: "fin_appr_date", dataType: "date", format:"date", width: "135px"},
            {headerText: "Fin Approve By", key: "fin_appr_by", dataType: "string", format:"string", width: "120px"},
            {headerText: "Order Status", key: "Status", dataType: "string", width: "100px"},
            {headerText: "Rem Status", key: "remStat", dataType: "string",  width: "130px"},
            {headerText: "Actions", key: "Actions", dataType: "string", width: "100px", columnCssClass: "centerAlignment",},

        ],
        features: [
            {   
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: "chk", allowSorting: false},                                       
                    {columnKey: "Actions", allowSorting: false},
                ]
            },
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                columnSettings: [
                    {columnKey: "OrderValue", allowFiltering: true},
                    {columnKey: "InvoiceValue", allowFiltering: true},
                    {columnKey: "chk", allowFiltering: false},
                    {columnKey: "Actions", allowFiltering: false},
                    {columnKey: "ChannelName", allowFiltering: false},
                    {columnKey: "ReturnValue", allowFiltering: true}

                ]
            },
            { name: "Summaries",              
              type:"local",              
              showDropDownButton: false,
              summariesCalculated: function(evt, ui){                         
                var listPricesummaryCells = $("div.ui-iggrid-summaries-footer-text-container");
                listPricesummaryCells.each(function() {         
                                if ($(this).text() != "") {    
                  $(this).text($(this).text().substr(2)); 
                  $(this).css({'text-align':'right','padding-right':'10px','font-weight': '800'});
                  }
                });
              },
                                       
                                             
              columnSettings: [          
                {columnKey: "chk", allowSummaries: false},            
                {columnKey: "ChannelName", allowSummaries: false},            
                {columnKey: "OrderID", allowSummaries: false},            
                {columnKey: "OrderDate", allowSummaries: false},
                {columnKey: "OrderValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},  
                {columnKey: "Hub", allowSummaries: false},        
                {columnKey: "spoke", allowSummaries: false},      
                {columnKey: "beat", allowSummaries: false},    
                {columnKey: "Area", allowSummaries: false},
                {columnKey: "ADT", allowSummaries: false},
                {columnKey: "del_name", allowSummaries: false},        
                {columnKey: "shipmentDate", allowSummaries: false},    
                {columnKey: "invoice_code", allowSummaries: false}, 
                {columnKey: "InvoiceDate", allowSummaries: false},
                {columnKey: "InvoiceValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "ReturnValue", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "collection_code", allowSummaries: false},
                {columnKey: "collected_amount", allowSummaries: true, summaryOperands:                                       
                  [{ "rowDisplayLabel": "", "type": "SUM", "active": true }]},
                {columnKey: "collected_by", allowSummaries: false},        
                {columnKey: "collection_date", allowSummaries: false},
                {columnKey: "remittance_code", allowSummaries: false},   
                {columnKey: "remittance_date", allowSummaries: false},  
                {columnKey: "hub_appr_date", allowSummaries: false},  
                {columnKey: "hub_appr_by", allowSummaries: false},  
                {columnKey: "fin_appr_date", allowSummaries: false},  
                {columnKey: "fin_appr_by", allowSummaries: false},  
                {columnKey: "Status", allowSummaries: false},
                 {columnKey: "remStat", allowSummaries: false},      
                {columnKey: "Actions", allowSummaries: false}]
            },
           {
               name: "ColumnFixing",
               fixingDirection: "right",
               columnSettings: [

                    {
                       columnKey: "Status",
                       isFixed: true,
                       allowFixing: false
                   },
                   {
                       columnKey: "Actions",
                       isFixed: true,
                       allowFixing: false
                   },
               ]
           },

            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'totalOrders',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: "OrderID",
        width: '100%',
        type: 'remote',
        dataSource: ajaxUrl,
        responseDataKey: 'data',
        showHeaders: true,
        fixedHeaders: true,
        height : '650px',
        rendered: function (evt, ui) {
                        igGridHideOption();
                        //$(".ui-iggrid-featurechooserbutton").remove();
                        $(".ui-iggrid-fixcolumn-headerbuttoncontainer").remove();
                }
    });

}

$(document).on('click','#orderList_editorEditingInput',function() {
  $('#orderList_editorEditingInput').removeAttr('readonly');  
});

$(document).delegate('#orderList','iggriddatarendered',function(evt, ui) {

  $("#orderList").igGridPaging("option", "pageSizeList", "10,20,50,100,200,300,400,500");


});   
