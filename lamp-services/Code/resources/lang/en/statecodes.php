<?php

return [

    'statecode_heads' => [
        'title' => 'State City Codes',
        'caption' => 'State City Codes',
        'add_state' => 'Add New State',
        'edit_state' => 'Edit State',
        'close' => 'Close',
        'add' => 'Add New State',
        'save' => 'Save Changes',
    ],
    'statecode_side_heads' => [
        'State_name' => 'State Name',
        'State_code' => 'State Code',
        'City_name' => 'City Name',
        'City_code'=> 'City Code',
        'dc_inc_id' => 'DC Increment ID',
        'fc_inc_id' => 'FC Increment ID',
        'is_active' => 'Is Active',
        'status' => 'Status',
        'actions' => 'Actions',
    ],
    'validation_errors' => [
        'State_name' => 'Please Enter State Name',
        'State_code' => 'Please Enter State Code',
        'City_name' => 'Please Enter City Name',
        'City_code' => 'Please Enter City Code',
        'dc_inc_id' => 'Please Enter Dc Inc Id',
        'fc_inc_id' => 'Please Enter Fc Inc Id',
        'dc_inc_id_isdigit' => 'Id Must Be Non-Negative Integer',
        'fc_inc_id_isdigit' => 'Id Must Be Non-Negative Integer',
        'State_code_isdigit' => 'Enter Valid State Code',
        'is_active' => 'Please Select Active Status',
        'State_name_exist' => 'State Name Already Exists. Please Check',
        'City_name_exist' => 'City Name Already Exists. Please Check',
    ],
    'message' => [
        'success_new' => "New State Record Added!",
        'failed_new' => "Failed To Add State Record. Please Try Again",
        'success_updated' => "State Record Updated!",
        'failed_updated' => "Failed To Update State Record. Please Try Again",
        'success_deleted' => "State Record Deleted!",
        'failed_deleted' => "Failed To Delete State Record. Please Try Again",
        'invalid' => "Invalid Data! Please Try Again!",
    ],
];