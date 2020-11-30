$(function () {
    $("#filtered_grid").igGrid({
        dataSource: '/tax/taxtypegrid',
        autoGenerateColumns: false,
        mergeUnboundColumns: false,
        responseDataKey: "results",
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [
            {headerText: "ProdID", key: "product_id", dataType: "number", hidden: "true", width: "0px"},
            {headerText: "Category Name", key: "cat_name", dataType: "string", width: "150px"},
            {headerText: "Product Title", key: "product_title", dataType: "string", width: "150px"},
            {headerText: "SKU ID", key: "sku", dataType: "string", width: "100px"},
            {headerText: "Brand", key: "brand_name", dataType: "string", width: "100px"},
//            {headerText: "Action", key: "actions", dataType: "string", width: "60px"},
            {headerText: "CatID", key: "category_id", dataType: "number", hidden: "true", width: "0px"},
            {headerText: "All States", key: "__4035", dataType: "string", width: "200px"},
            {headerText: "TS", key: "__4033", dataType: "string", width: "200px"},
            {headerText: "AN", key: "__1475", dataType: "string", width: "200px"},
            {headerText: "AP", key: "__1476", dataType: "string", width: "200px"},
            {headerText: "AR", key: "__1477", dataType: "string", width: "200px"},
            {headerText: "AS", key: "__1478", dataType: "string", width: "200px"},
            {headerText: "BI", key: "__1479", dataType: "string", width: "200px"},
            {headerText: "CH", key: "__1480", dataType: "string", width: "200px"},
            {headerText: "DA", key: "__1481", dataType: "string", width: "200px"},
            {headerText: "DM", key: "__1482", dataType: "string", width: "200px"},
            {headerText: "DE", key: "__1483", dataType: "string", width: "200px"},
            {headerText: "GO", key: "__1484", dataType: "string", width: "200px"},
            {headerText: "GU", key: "__1485", dataType: "string", width: "200px"},
            {headerText: "HA", key: "__1486", dataType: "string", width: "200px"},
            {headerText: "HP", key: "__1487", dataType: "string", width: "200px"},
            {headerText: "JA", key: "__1488", dataType: "string", width: "200px"},
            {headerText: "KA", key: "__1489", dataType: "string", width: "200px"},
            {headerText: "KE", key: "__1490", dataType: "string", width: "200px"},
            {headerText: "LI", key: "__1491", dataType: "string", width: "200px"},
            {headerText: "MP", key: "__1492", dataType: "string", width: "200px"},
            {headerText: "MA", key: "__1493", dataType: "string", width: "200px"},
            {headerText: "MN", key: "__1494", dataType: "string", width: "200px"},
            {headerText: "ME", key: "__1495", dataType: "string", width: "200px"},
            {headerText: "MI", key: "__1496", dataType: "string", width: "200px"},
            {headerText: "NA", key: "__1497", dataType: "string", width: "200px"},
            {headerText: "OR", key: "__1498", dataType: "string", width: "200px"},
            {headerText: "PO", key: "__1499", dataType: "string", width: "200px"},
            {headerText: "PU", key: "__1500", dataType: "string", width: "200px"},
            {headerText: "RA", key: "__1501", dataType: "string", width: "200px"},
            {headerText: "SI", key: "__1502", dataType: "string", width: "200px"},
            {headerText: "TN", key: "__1503", dataType: "string", width: "200px"},
            {headerText: "TR", key: "__1504", dataType: "string", width: "200px"},
            {headerText: "UP", key: "__1505", dataType: "string", width: "200px"},
            {headerText: "WB", key: "__1506", dataType: "string", width: "200px"}
        ],
        features: [
            {
                name: "ColumnFixing",
                fixingDirection: "left",
                columnSettings: [
                    {
                        columnKey: "cat_name",
                        isFixed: true,
                        allowFixing: false
                    },
                    {
                        columnKey: "product_title",
                        isFixed: true,
                        allowFixing: false
                    },
                    {
                        columnKey: "sku",
                        isFixed: true,
                        allowFixing: false
                    },
                    {
                        columnKey: "brand_name",
                        isFixed: true,
                        allowFixing: false
                    },
//                    {
//                        columnKey: "actions",
//                        isFixed: true,
//                        allowFixing: false
//                    },
                    {
                        columnKey: "__4035",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1475",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1476",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1477",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1478",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1479",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1480",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1481",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1482",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1483",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1484",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1485",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1486",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1487",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1488",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1489",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1490",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1491",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1492",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1493",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1494",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1495",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1496",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1497",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1498",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1499",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1500",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1501",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1502",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1503",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1504",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1505",
                        allowFixing: false
                    },
                    {
                        columnKey: "__1506",
                        allowFixing: false
                    },
                    {
                        columnKey: "__4033",
                        allowFixing: false
                    }
                ]
            },
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {
                        columnKey: "cat_name",
                        allowSorting: true
                    },
                    {
                        columnKey: "product_title",
                        allowSorting: true
                        , currentSortDirection: "ascending"
                    },
                    {
                        columnKey: "brand_name",
                        allowSorting: true
                    },
//                    {
//                        columnKey: "actions",
//                        allowSorting: false
//                    },
                    {
                        columnKey: "__4035",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1475",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1476",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1477",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1478",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1479",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1480",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1481",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1482",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1483",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1484",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1485",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1486",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1487",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1488",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1489",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1490",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1491",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1492",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1493",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1494",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1495",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1496",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1497",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1498",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1499",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1500",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1501",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1502",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1503",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1504",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1505",
                        allowSorting: false
                    },
                    {
                        columnKey: "__1506",
                        allowSorting: false
                    },
                    {
                        columnKey: "__4033",
                        allowSorting: false
                    }
                ]
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
//                    {columnKey: 'actions', allowFiltering: false},
                    {columnKey: '__4035', allowFiltering: false},
                    {columnKey: '__1475', allowFiltering: false},
                    {columnKey: '__1476', allowFiltering: false},
                    {columnKey: '__1477', allowFiltering: false},
                    {columnKey: '__1478', allowFiltering: false},
                    {columnKey: '__1479', allowFiltering: false},
                    {columnKey: '__1480', allowFiltering: false},
                    {columnKey: '__1481', allowFiltering: false},
                    {columnKey: '__1482', allowFiltering: false},
                    {columnKey: '__1483', allowFiltering: false},
                    {columnKey: '__1484', allowFiltering: false},
                    {columnKey: '__1485', allowFiltering: false},
                    {columnKey: '__1486', allowFiltering: false},
                    {columnKey: '__1487', allowFiltering: false},
                    {columnKey: '__1488', allowFiltering: false},
                    {columnKey: '__1489', allowFiltering: false},
                    {columnKey: '__1490', allowFiltering: false},
                    {columnKey: '__1491', allowFiltering: false},
                    {columnKey: '__1492', allowFiltering: false},
                    {columnKey: '__1493', allowFiltering: false},
                    {columnKey: '__1494', allowFiltering: false},
                    {columnKey: '__1495', allowFiltering: false},
                    {columnKey: '__1496', allowFiltering: false},
                    {columnKey: '__1497', allowFiltering: false},
                    {columnKey: '__1498', allowFiltering: false},
                    {columnKey: '__1499', allowFiltering: false},
                    {columnKey: '__1500', allowFiltering: false},
                    {columnKey: '__1501', allowFiltering: false},
                    {columnKey: '__1502', allowFiltering: false},
                    {columnKey: '__1503', allowFiltering: false},
                    {columnKey: '__1504', allowFiltering: false},
                    {columnKey: '__1505', allowFiltering: false},
                    {columnKey: '__1506', allowFiltering: false},
                    {columnKey: '__4033', allowFiltering: false},
                ]
            },
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            }
        ],
        primaryKey: 'product_id',
        width: '100%',
        height: '600px',
        initialDataBindDepth: 0,
        localSchemaTransform: false
    });
    $("#filtered_grid___1475").attr("title", "Andaman and Nicobar Islands");
    $("#filtered_grid___1476").attr("title", "Andhra Pradesh");
    $("#filtered_grid___1477").attr("title", "Arunachal Pradesh");
    $("#filtered_grid___1478").attr("title", "Assam");
    $("#filtered_grid___1479").attr("title", "Bihar");
    $("#filtered_grid___1480").attr("title", "Chandigarh");
    $("#filtered_grid___1481").attr("title", "Dadra and Nagar Haveli");
    $("#filtered_grid___1482").attr("title", "Daman and Diu");
    $("#filtered_grid___1483").attr("title", "Delhi");
    $("#filtered_grid___1484").attr("title", "Goa");
    $("#filtered_grid___1485").attr("title", "Gujarat");
    $("#filtered_grid___1486").attr("title", "Haryana");
    $("#filtered_grid___1487").attr("title", "Himachal Pradesh");
    $("#filtered_grid___1488").attr("title", "Jammu and Kashmir");
    $("#filtered_grid___1489").attr("title", "Karnataka");
    $("#filtered_grid___1490").attr("title", "Kerala");
    $("#filtered_grid___1491").attr("title", "Lakshadweep Islands");
    $("#filtered_grid___1492").attr("title", "Madhya Pradesh");
    $("#filtered_grid___1493").attr("title", "Maharashtra");
    $("#filtered_grid___1494").attr("title", "Manipur");
    $("#filtered_grid___1495").attr("title", "Meghalaya");
    $("#filtered_grid___1496").attr("title", "Mizoram");
    $("#filtered_grid___1497").attr("title", "Nagaland");
    $("#filtered_grid___1498").attr("title", "Orissa");
    $("#filtered_grid___1499").attr("title", "Pondicherry");
    $("#filtered_grid___1500").attr("title", "Punjab");
    $("#filtered_grid___1501").attr("title", "Rajasthan");
    $("#filtered_grid___1502").attr("title", "Sikkim");
    $("#filtered_grid___1503").attr("title", "Tamil Nadu");
    $("#filtered_grid___1504").attr("title", "Tripura");
    $("#filtered_grid___1505").attr("title", "Uttar Pradesh");
    $("#filtered_grid___1506").attr("title", "West Bengal");
    $("#filtered_grid___4033").attr("title", "Telangana");
    $('.ui-iggrid-header').css('cssText', 'text-align: center !important');

    $("#filtered_grid_container").hide();
});