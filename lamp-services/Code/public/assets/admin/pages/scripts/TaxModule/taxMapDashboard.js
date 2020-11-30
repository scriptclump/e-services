$(function () {
    var token = $("#token_value").val();
    $("#grid").igGrid({
        dataSource: '/tax/products',
//        updateUrl: '/tax/products/update?_token=' + token,
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
            {headerText: "Action", key: "actions", dataType: "string", width: "60px"},
            {headerText: "CatID", key: "category_id", dataType: "number", hidden: "true", width: "0px"},
            {headerText: "All States", key: "_4035", dataType: "object", width: "200px", formatter: allFormatCombo},
            {headerText: "TS", key: "_4033", dataType: "object", width: "200px", formatter: wbFormatCombo},
            {headerText: "AN", key: "_1475", dataType: "object", width: "200px", formatter: anFormatCombo},
            {headerText: "AP", key: "_1476", dataType: "object", width: "200px", formatter: apFormatCombo},
            {headerText: "AR", key: "_1477", dataType: "object", width: "200px", formatter: arFormatCombo},
            {headerText: "AS", key: "_1478", dataType: "object", width: "200px", formatter: asFormatCombo},
            {headerText: "BI", key: "_1479", dataType: "object", width: "200px", formatter: biFormatCombo},
            {headerText: "CH", key: "_1480", dataType: "object", width: "200px", formatter: chFormatCombo},
            {headerText: "DA", key: "_1481", dataType: "object", width: "200px", formatter: daFormatCombo},
            {headerText: "DM", key: "_1482", dataType: "object", width: "200px", formatter: dmFormatCombo},
            {headerText: "DE", key: "_1483", dataType: "object", width: "200px", formatter: deFormatCombo},
            {headerText: "GO", key: "_1484", dataType: "object", width: "200px", formatter: goFormatCombo},
            {headerText: "GU", key: "_1485", dataType: "object", width: "200px", formatter: guFormatCombo},
            {headerText: "HA", key: "_1486", dataType: "object", width: "200px", formatter: haFormatCombo},
            {headerText: "HP", key: "_1487", dataType: "object", width: "200px", formatter: hpFormatCombo},
            {headerText: "JA", key: "_1488", dataType: "object", width: "200px", formatter: jaFormatCombo},
            {headerText: "KA", key: "_1489", dataType: "object", width: "200px", formatter: kaFormatCombo},
            {headerText: "KE", key: "_1490", dataType: "object", width: "200px", formatter: keFormatCombo},
            {headerText: "LI", key: "_1491", dataType: "object", width: "200px", formatter: liFormatCombo},
            {headerText: "MP", key: "_1492", dataType: "object", width: "200px", formatter: mpFormatCombo},
            {headerText: "MA", key: "_1493", dataType: "object", width: "200px", formatter: maFormatCombo},
            {headerText: "MN", key: "_1494", dataType: "object", width: "200px", formatter: mnFormatCombo},
            {headerText: "ME", key: "_1495", dataType: "object", width: "200px", formatter: meFormatCombo},
            {headerText: "MI", key: "_1496", dataType: "object", width: "200px", formatter: miFormatCombo},
            {headerText: "NA", key: "_1497", dataType: "object", width: "200px", formatter: naFormatCombo},
            {headerText: "OR", key: "_1498", dataType: "object", width: "200px", formatter: orFormatCombo},
            {headerText: "PO", key: "_1499", dataType: "object", width: "200px", formatter: poFormatCombo},
            {headerText: "PU", key: "_1500", dataType: "object", width: "200px", formatter: puFormatCombo},
            {headerText: "RA", key: "_1501", dataType: "object", width: "200px", formatter: raFormatCombo},
            {headerText: "SI", key: "_1502", dataType: "object", width: "200px", formatter: siFormatCombo},
            {headerText: "TN", key: "_1503", dataType: "object", width: "200px", formatter: tgFormatCombo},
            {headerText: "TR", key: "_1504", dataType: "object", width: "200px", formatter: tnFormatCombo},
            {headerText: "UP", key: "_1505", dataType: "object", width: "200px", formatter: trFormatCombo},
            {headerText: "WB", key: "_1506", dataType: "object", width: "200px", formatter: upFormatCombo}
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
                    {
                        columnKey: "actions",
                        isFixed: true,
                        allowFixing: false
                    },
                    {
                        columnKey: "_4035",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1475",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1476",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1477",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1478",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1479",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1480",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1481",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1482",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1483",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1484",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1485",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1486",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1487",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1488",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1489",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1490",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1491",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1492",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1493",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1494",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1495",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1496",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1497",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1498",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1499",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1500",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1501",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1502",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1503",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1504",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1505",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1506",
                        allowFixing: false
                    },
                    {
                        columnKey: "_4033",
                        allowFixing: false
                    }
                ]
            },
//            {
//                name: "Selection",
//                mode: "row",
//                multipleSelection: true
//            },
            {
                name: 'Updating',
                editMode: 'none',
                enableAddRow: false,
                enableDeleteRow: false,
                startEditTriggers: 'dblclick',
                editCellEnded: function (evt, ui) {
                    $("#grid").igGrid("saveChanges");
                },
                columnSettings: [
                    {
                        columnKey: "product_title",
                        editorType: 'string',
                        readOnly: true
                    },
                    {
                        columnKey: "cat_name",
                        editorType: 'string',
                        readOnly: true
                    },
                    {
                        columnKey: "brand_name",
                        editorType: 'string',
                        readOnly: true
                    },
                    {
                        columnKey: "sku",
                        editorType: 'string',
                        readOnly: true
                    },
                    {
                        columnKey: "actions",
                        editorType: 'string',
                        readOnly: true
                    },
                    {
                        columnKey: "_4035",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[4035],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1475",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1475],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1476",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1476],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1477",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1477],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1478",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1478],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1479",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1479],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1480",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1480],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1481",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1481],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1482",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1482],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1483",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1483],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1484",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1484],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1485",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1485],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1486",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1486],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1487",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1487],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1488",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1488],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1489",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1489],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1490",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1490],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1491",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1491],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1492",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1492],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1493",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1493],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1494",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1494],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1495",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1495],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1496",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1496],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1497",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1497],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1498",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1498],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1499",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1499],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1500",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1500],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1501",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1501],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1502",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1502],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1503",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1503],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1504",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1504],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1505",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1505],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_1506",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[1506],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
                    },
                    {
                        columnKey: "_4033",
                        editorType: 'combo',
                        editorOptions: {
                            mode: "dropdown",
                            dataSource: state_wise_tax_classes[4033],
                            textKey: "text",
                            valueKey: "value",
                            multiSelection: {
                                enabled: true,
                                showCheckboxes: true
                            },
                            selectionChanged: function (evt, ui) {
                                if ((ui.oldItems !== null) && (ui.items !== null)) {
                                    if (ui.oldItems.length >= ui.items.length) {
                                        var rowId = $(ui.owner.element[0].parentElement.parentElement.outerHTML).data('id');
                                        urlfunction(ui.oldItems, ui.items, rowId);
                                    }
                                }
                            }
                        }
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
                    {
                        columnKey: "actions",
                        allowSorting: false
                    },
                    {
                        columnKey: "_4035",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1475",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1476",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1477",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1478",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1479",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1480",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1481",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1482",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1483",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1484",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1485",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1486",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1487",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1488",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1489",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1490",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1491",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1492",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1493",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1494",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1495",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1496",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1497",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1498",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1499",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1500",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1501",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1502",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1503",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1504",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1505",
                        allowSorting: false
                    },
                    {
                        columnKey: "_1506",
                        allowSorting: false
                    },
                    {
                        columnKey: "_4033",
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
                    {columnKey: 'actions', allowFiltering: false},
                    {columnKey: '_4035', allowFiltering: false},
                    {columnKey: '_1475', allowFiltering: false},
                    {columnKey: '_1476', allowFiltering: false},
                    {columnKey: '_1477', allowFiltering: false},
                    {columnKey: '_1478', allowFiltering: false},
                    {columnKey: '_1479', allowFiltering: false},
                    {columnKey: '_1480', allowFiltering: false},
                    {columnKey: '_1481', allowFiltering: false},
                    {columnKey: '_1482', allowFiltering: false},
                    {columnKey: '_1483', allowFiltering: false},
                    {columnKey: '_1484', allowFiltering: false},
                    {columnKey: '_1485', allowFiltering: false},
                    {columnKey: '_1486', allowFiltering: false},
                    {columnKey: '_1487', allowFiltering: false},
                    {columnKey: '_1488', allowFiltering: false},
                    {columnKey: '_1489', allowFiltering: false},
                    {columnKey: '_1490', allowFiltering: false},
                    {columnKey: '_1491', allowFiltering: false},
                    {columnKey: '_1492', allowFiltering: false},
                    {columnKey: '_1493', allowFiltering: false},
                    {columnKey: '_1494', allowFiltering: false},
                    {columnKey: '_1495', allowFiltering: false},
                    {columnKey: '_1496', allowFiltering: false},
                    {columnKey: '_1497', allowFiltering: false},
                    {columnKey: '_1498', allowFiltering: false},
                    {columnKey: '_1499', allowFiltering: false},
                    {columnKey: '_1500', allowFiltering: false},
                    {columnKey: '_1501', allowFiltering: false},
                    {columnKey: '_1502', allowFiltering: false},
                    {columnKey: '_1503', allowFiltering: false},
                    {columnKey: '_1504', allowFiltering: false},
                    {columnKey: '_1505', allowFiltering: false},
                    {columnKey: '_1506', allowFiltering: false},
                    {columnKey: '_4033', allowFiltering: false},
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
        localSchemaTransform: false,
        //Removing filter columns in Grid
        rendered: function (evt, ui) {
            $("#grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
            $("#grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
            $("#grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
        }
    });
    $("#grid__1475").attr("title", "Andaman and Nicobar Islands");
    $("#grid__1476").attr("title", "Andhra Pradesh");
    $("#grid__1477").attr("title", "Arunachal Pradesh");
    $("#grid__1478").attr("title", "Assam");
    $("#grid__1479").attr("title", "Bihar");
    $("#grid__1480").attr("title", "Chandigarh");
    $("#grid__1481").attr("title", "Dadra and Nagar Haveli");
    $("#grid__1482").attr("title", "Daman and Diu");
    $("#grid__1483").attr("title", "Delhi");
    $("#grid__1484").attr("title", "Goa");
    $("#grid__1485").attr("title", "Gujarat");
    $("#grid__1486").attr("title", "Haryana");
    $("#grid__1487").attr("title", "Himachal Pradesh");
    $("#grid__1488").attr("title", "Jammu and Kashmir");
    $("#grid__1489").attr("title", "Karnataka");
    $("#grid__1490").attr("title", "Kerala");
    $("#grid__1491").attr("title", "Lakshadweep Islands");
    $("#grid__1492").attr("title", "Madhya Pradesh");
    $("#grid__1493").attr("title", "Maharashtra");
    $("#grid__1494").attr("title", "Manipur");
    $("#grid__1495").attr("title", "Meghalaya");
    $("#grid__1496").attr("title", "Mizoram");
    $("#grid__1497").attr("title", "Nagaland");
    $("#grid__1498").attr("title", "Orissa");
    $("#grid__1499").attr("title", "Pondicherry");
    $("#grid__1500").attr("title", "Punjab");
    $("#grid__1501").attr("title", "Rajasthan");
    $("#grid__1502").attr("title", "Sikkim");
    $("#grid__1503").attr("title", "Tamil Nadu");
    $("#grid__1504").attr("title", "Tripura");
    $("#grid__1505").attr("title", "Uttar Pradesh");
    $("#grid__1506").attr("title", "West Bengal");
    $("#grid__4033").attr("title", "Telangana");
    $('.ui-iggrid-header').css('cssText', 'text-align: center !important');

    $( document ).ajaxSuccess(function( event, xhr, settings ) {
      if ( settings.url.indexOf("/tax/products/" ) != -1 )  {
        setTimeout(function(){
            $("#filtered_grid_container").hide();
        }, 1000);
      }
    });
});