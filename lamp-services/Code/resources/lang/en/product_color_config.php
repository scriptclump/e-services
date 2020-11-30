<?php

return [

    'heads' => [
        'title' => 'Product Color Configurations',
        'caption' => 'Product Color Configurations',
        'add_product_color' => 'Add Product Color Configuration',
        'edit_product_color' => 'Edit Product Color Configuration',
        'close' => 'Close',
        'add' => 'Add Color Config',
        'save' => 'Save Changes',
        'add_color' => 'Add Product Color',
        'import_color_config' => 'Import Product Colors'
    ],

    'side_heads' => [
        'WareHouse_Name' => 'Warehouse Name',
        'Product_Name' => 'Product Name',
        'Pack' => 'Pack',
        'Customer_Type' => 'Customer Type',
        'Color' => 'Color',
        'Elp' => 'ELP',
        'Esp' => 'ESP',
        'Margin' => 'Margin',
    ],

    'validation_errors' => [
        'WareHouse_Name' => 'Please select WaraHouse Name',
        'Product_Name' => 'Please select Product Name',
        'Pack' => 'Please select Pack',
        'Customer_Type' => 'Please select Customer Type',
        'Color' => 'Please select Color',
        'Color_exist' => 'Product already configured with selected Color for WareHouse,Pack and Customer. Please check',
        'Elp_isdigit' => 'Elp should be only Digits',
        'Esp_isdigit' => 'Esp should be only Digits',
        'Margin_isdigit' => 'Margin should be only Digits',
    ],

    'message' => [
        'success_new' => "New Product Color Configured Successfully!",
        'failed_new' => "Failed to Add New Product Color Configure Record. Please Try Again",
        'success_updated' => "Product Color Configuration Updated!",
        'failed_updated' => "Failed to Update Product Color Configuration. Please Try Again",
        'success_deleted' => "Product Color Configuration Record Deleted!",
        'failed_deleted' => "Failed to Delete Product Color Configuration Record. Please Try Again",
        'invalid' => "Invalid Data! Please Try Again!",
    ],
];
