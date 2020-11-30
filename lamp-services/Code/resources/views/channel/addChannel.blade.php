
@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('style')
<style>
    .container{
    margin-top:20px;
    }
    .image-preview-input {
        position: relative;
        overflow: hidden;
        margin: 0px;    
        color: #333;
        background-color: #fff;
        border-color: #ccc;    
    }
    .image-preview-input input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 20px;
        cursor: pointer;
        opacity: 0;
        filter: alpha(opacity=0);
    }
    .image-preview-input-title {
        margin-left:2px;
    }
    .row{margin-left:0px !important; margin-right:0px !important; }
    .table-scrollable{border:0px;}
    .table > thead > tr > th {border-bottom: 0px solid #ddd;}
    .fileUpload {
        position: relative;
        overflow: hidden;
    }
    .btn-group, .btn-group-vertical {display: -moz-box;}
    .form-group{margin-bottom:0px;}
    .fileUpload input.upload {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 20px;
        cursor: pointer;
        opacity: 0;
        filter: alpha(opacity=0);
    }
    .form-group label{padding-bottom:0px; padding-top:7px !important;}
    .form-group .sub-input{
        border:0px;
        background-color:#fff;
        float:right;margin-top: 5px;
    }
    .col-md-6 .col-md-6{ padding-left:0px;}
    .review_tab {
        width: 50%;
        text-align: center;
        margin:auto;


    }
    .review_tab  td{
        border:1px solid #ccc;
    }
    .review_tab table {
        float:left;
    }
    .review_tab table tr:first-child {
        background:#ccc;

    }
    .lock{
        display: none;
    }
    .has-feedback label ~ .form-control-feedback {top:38px !important; right: 1px !important;} 
    .input-medium {margin: 15px  auto !important;}
    .clear{margin-left:0px !important; margin-right:0px !important;}
</style>
@stop

@section('content')
<ul class="page-breadcrumb breadcrumb">
    <li><a href="#">Dashboard</a><i aria-hidden="true" class="fa fa-angle-right"></i>
    </li><li><a class="active" href="/Commerceplatform">{{trans('cp_headings.cp')}}</a></li></ul>

<?php //print_r($page_prop) ?>
<div class="portlet box" id="form_wizard_1">

    <div class="portlet light bordered">

        <div class="portlet-title">
            <div class="caption">  <?php echo $page_prop['heading'] ?></div>
        </div>
        <div class=" tabbable-line">
            <?php
            /* Managing disabled in edit area */
            $disabled = 'disabled';
            if ($page_prop['flag'] == 'edit') {
                $disabled = '';
            }
            ?>
            <ul class="nav nav-tabs  main_list ">
                <li class="active ch_tabs basic_tab"> <a data-toggle="tab" href="#tab_basic" aria-expanded="true" data-serial="1"> {{trans('cp_headings.cp_tab_info')}}</a> </li>
                <li class="ch_tabs <?php echo $disabled; ?>"> <a data-toggle="tab"  id="ccharges" href="#tab_charges" aria-expanded="false" data-serial="2"> {{trans('cp_headings.cp_tab_charges')}} </a> </li>
                <li class="ch_tabs <?php echo $disabled; ?>"> <a data-toggle="tab" href="#tab_cat" aria-expanded="false" id='ccat' data-serial="3">{{trans('cp_headings.cp_tab_categories')}}</a> </li>
                <li class="ch_tabs <?php echo $disabled; ?>"> <a data-toggle="tab" href="#tab_map_cat" aria-expanded="false" id='mapcat' data-serial="4"> {{trans('cp_headings.cp_tab_map_categories')}} </a> </li>
                <li class="ch_tabs <?php echo $disabled; ?>"> <a data-toggle="tab" href="#tab_order_map" aria-expanded="false" data-serial="5"> {{trans('cp_headings.cp_tab_map_status')}} </a> </li>


            </ul>
            <div class="tab-content">

                <input type="hidden" id="csrf_token" name="_token" value="{{ Session::token() }}">
                <!--<input type='hidden' id='channel_id' name='channel_id' value='{{ $data['channel_id'] }}'/>-->
                <input type='hidden' id='channel_id' name='channel_id' value='{{ Session::get('channel_maxid')}}'/>

                <!--Channel Basic Start-->
                <div id="tab_basic" class="tab-pane fade in active">
                    @include('channel.tabs.BasicInformation')
                </div>
                <!--Channel Basic End-->
                <div id="tab_charges" class="tab-pane fade in">
                    @include('channel.tabs.ChannelCharges')
                </div>
                <div id="tab_cat" class="tab-pane fade in">
                    @include('channel.tabs.AddChannelCategories')
                </div>
                <div id="tab_map_cat" class="tab-pane fade in">
                    @include('channel.tabs.MapCategories')
                </div>
                <div id="tab_order_map" class="tab-pane fade in">
                    @include('channel.tabs.Mapchannelorderstatus')
                </div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-4 col-md-8">
                        <button class="btn green-meadow" type="button" href="javascript:;"  id="btn_save_edit" data-tab-id="">Save & Continue</button>
                        <button class="btn green-meadow" type="button" href="javascript:;" id="Cancel_btn">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@include('includes.jqx-validators')
@section('style')
<link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('script') 
<script src="{{ URL::asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script>

<!-- Basic Information -->
<script type="text/javascript">
var csrf_token = $('#csrf_token').val();
$(document).ready(function () {

    LoadAddChannelScripts();
    channelCharges();
    loadCategoryScripts();
    AutoLoadChannelCategories();
    AutoLoadEbutorCategories();
    addChannelCategory();
    editChannelCategory();
    autoLoadParentCategories();
    selectAttribute();

    getOrderStatusList();
    mapStatusValidation();

    $('#Cancel_btn').click(function () {
        window.location.href = "/Commerceplatform";
    });
    $("#btn_save_edit").click(function () {
        $('#add_channel_form').submit();
    });
    $('.ch_tabs').click(function (event) {
        if ($(this).hasClass('disabled')) {
            return false;
        }
    });
    $(".modal").on('hide.bs.modal', function () {
        var form_id = $(this).find('form').attr('id');
        $('#' + form_id).bootstrapValidator('resetForm', true);
        $('#' + form_id)[0].reset();
    });
    $('#up_new_logo').click(function () {
        var type = $(this).attr('data-type');
        if (type == 'close') {
            $('.show_new_logo').show();
            $('#showlogo').hide();
            $(this).removeClass('fa-times');
            $(this).addClass('fa-undo');
            $(this).attr('data-type', 'undo');
        } else {
            $('.show_new_logo').hide();
            $('#showlogo').show();
            $(this).addClass('fa-times');
            $(this).removeClass('fa-undo');
            $(this).attr('data-type', 'close');
        }
    });
    $('#ccat').click(function () {
        var channel_id = $('#channel_id').val();
        autoLoadParentCategories(channel_id);
        getCategoryGrid(channel_id);
    });
    $('#mapcat').click(function () {
        var channel_id = $('#channel_id').val();
        getMapCategoryGrid(channel_id);
    });
});
//Basic Information
function LoadAddChannelScripts() {
    $('#add_channel_form').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            channel_name: {
                validators: {
                    notEmpty: {
                        message: 'Please enter Channel name'
                    },
                    remote: {
                        message: 'Channel is already exist',
                        url: '/Commerceplatform/checkMPExist?_token=' + csrf_token,
                        type: 'POST',
                    }
                }
            },
            channel_description: {
                validators: {
                    notEmpty: {
                        message: 'Please enter Channel Description'
                    }
                }
            },
            channel_url: {
                validators: {
                    uri: {
                        message: 'The URL is invalid'
                    },
                    notEmpty: {
                        message: 'Please enter Channel URL'
                    }
                    /*   ,remote: {
                     message: 'Channel is already exist',
                     url: '/Commerceplatform/checkMPUrlExist?_token=' + csrf_token,
                     type: 'POST',
                     }*/
                }
            },
            tnc_url: {
                validators: {
                    uri: {
                        message: 'The URL is invalid'
                    },
                    notEmpty: {
                        message: 'Please enter T&C URL'
                    }
                }
            },
            price_url: {
                validators: {
                    uri: {
                        message: 'The URL is invalid'
                    },
                    notEmpty: {
                        message: 'Please enter Price URL'
                    }
                }
            },
            shipping_url: {
                validators: {
                    uri: {
                        message: 'The URL is invalid'
                    },
                    notEmpty: {
                        message: 'Please enter Shipping URL'
                    }
                }
            },
            location: {
                validators: {
                    notEmpty: {
                        message: 'Please select Location'
                    }
                }
            },
            'channel_type[]': {
                validators: {
                    notEmpty: {
                        message: 'Please select Channel Type'
                    }
                }
            },
            channel_logo: {
                validators: {
                    callback: {
                        message: 'Please select valid image',
                        callback: function (value, validator, $field) {
                            var exts = ['jpg', 'jpeg', 'png', 'gif', 'tiff'];
                            var get_ext = value.split('.');
                            // reverse name to check extension
                            get_ext = get_ext.reverse();
                            // check file type is valid as given in â€˜extsâ€™ array
                            if ($.inArray(get_ext[0].toLowerCase(), exts) > -1) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                    notEmpty: {
                        message: 'Please select Channel Logo'
                    }
                }
            },
        }
    }).on('success.form.bv', function (event) {
        event.preventDefault();
        var val = $('div .main_list li.active').find('a').attr('data-serial');
        if (val == 1)
        {
            var formData = new FormData($(this)[0]);
            var channel_id = $('#channel_id').val();
            $.ajax({
                headers: {'X-CSRF-TOKEN': csrf_token},
                method: "POST",
                dataType: 'json',
                url: '/Commerceplatform/storeChannelData?channel_id=' + channel_id,
                data: formData,
                processData: false,
                contentType: false,
                success: function (data)
                {
                    $("#error_message").text('');
                    $('#error_message').removeClass('alert-success alert-danger');
                    if (data.code != 0) {
                        $("#error_message").text(data.message);
                        $("#error_message").addClass('alert-success');
                        $('#channel_id').val(data.code);
                        $('.ch_tabs').removeClass('disabled');
                        $('[href="#tab_charges"]').click();
                        //$('.basic_tab').attr('class', 'disabled');
                    } else {
                        $("#error_message").text(data.message);
                        $("#error_message").addClass('alert-danger');
                    }
                },
                fail: function (data)
                {
                    //alert(data);
                }
            });
        }
        if (val == 2)
        {
            $('[href="#tab_cat"]').click();
        }
        if (val == 3)
        {
            $('[href="#tab_map_cat"]').click();
            // $('#btn_save_edit').html('Done');
        }
        if (val == 4)
        {
            var channel_id = $('channel_id').val();
            $('[href="#tab_order_map"]').click();
            getMapCategoryGrid(channel_id);
        }
        if (val == 5)
        {
            window.location = "/Commerceplatform";
        }
    });
}
//End of Basic Information--------------------------------------------------------------------------------------------- 
//Channel Charges Tab-----------------------------------------------------

function channelCharges() {
    $('#ccharges').click(function () {
        getChannelChargesData();
        var currentDate = new Date();
    //alert($('#start_date').length);
        $('#start_date').datepicker({
            dateFormat: 'dd-mm-yyyy',
            minDate: new Date(),
            onSelect : function(selected_date){
                var selectedDate = new Date(selected_date);
                var msecsInADay  = 86400000;
                var endDate      = new Date(selectedDate.getTime() + msecsInADay);

                $("#end_date").datepicker( "option", "minDate", endDate );
            }
        });
        $('#end_date').datepicker({
            dateFormat: 'dd-mm-yyyy',
        });

        $(document).on("click", '#add_charge_button', function (event) {
            var channel_id = $('#channel_id').val();
            var formData = $('#channel_level_charges').serialize();

            var startDate = document.getElementById("start_date").value;
            var endDate = document.getElementById("end_date").value;
        if ((Date.parse(startDate) >= Date.parse(endDate))) {
            alert("End date should be greater than Start date");
        }else{
            $.ajax({
                headers: {'X-CSRF-TOKEN': csrf_token},
                url: '/Commerceplatform/channelChargesStore?channel_id=' + channel_id,
                type: 'POST',
                data: formData,
                success: function (data)
                {

                    alert(data);
                    getChannelChargesData();
                    return false;
                    // $('[href="#tab_images"]').click();
                }, fail: function (data)
                {
                    alert("failed");
                }

            });
        }
        });
    });
    $(document).on("click", ".edit_charges", function () {
        $channel_charge_id = $(this).attr('data-channel-idd');
        $.ajax({
            headers: {'X-CSRF-TOKEN': csrf_token},
            type: "post",
            dataType: "json",
            url: "/Commerceplatform/getChannelCharges?charge_id=" + $channel_charge_id,
            success: function (data)
            {
                $("#service_type_id").val(data.service_type_id);
                $("#recurring_interval").val(data.recurring_interval);
                $("#charges").val(data.charges);
                $("#charge_type").val(data.charge_type);
                $("#currency_id").val(data.currency_id);
                $("#is_recurring").val(data.is_recurring);
                $("#charge_idd").val(data.mp_charges_id);
                $("#start_date").val(data.charges_from_date);
                $("#end_date").val(data.charges_to_date);
                $('#add_charge_button').text('Update');
                //$("select#service_type_id option").filter(":selected").text(data.service_type_id);
            }
        });
    });
    $(document).on("click", ".delete_charges", function () {
        $channel_charge_id = $(this).attr('data-channel-idd');
        var con = confirm("are you sure you want to delete?");
        if (con == true) {
            $.ajax({
                headers: {'X-CSRF-TOKEN': csrf_token},
                type: "post",
                dataType: "json",
                url: "/Commerceplatform/delteChannelCharges?charge_id=" + $channel_charge_id,
                success: function (data)
                {
                    getChannelChargesData();
                }
            });
        }
    });
}
function getChannelChargesData() {
    var channel_id = $('#channel_id').val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': csrf_token},
        url: '/Commerceplatform/getChannelChargesData?channel_id=' + channel_id,
        type: 'POST',
        success: function (data) {
            $('#charges_list').html(data);
        },
        fail: function (data) {
            //alert("failed");
        }
    });
}
//EndChannel Charges Tab-----------------------------------------------

//Category Upload--------------------------------------------------------------------------------------------- 
function loadCategoryScripts() {

    $('#download_template_type').change(function () {
        var template_link = $(this).val();
        var template_type = $('option:selected', this).attr('id');
        $('#template_type').val(template_type);
        $('#download_categories').attr('href', template_link);
        $('#download_categories').text('Download ' + template_type + ' Template');
        $('#up_text').text('Upload Your ' + template_type + ' Template');
    })
    $('#import_file').change(function () {
        $('#import_category').submit();
    });
    $('#import_category').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            import_file: {
                validators: {
                    callback: {
                        message: 'The selected file is not valid',
                        callback: function (value, validator, $field) {
                            var exts = ['xlsx', 'xls'];
                            // split file name at dot
                            var get_ext = value.split('.');
                            // reverse name to check extension
                            get_ext = get_ext.reverse();
                            // check file type is valid as given in â€˜extsâ€™ array
                            if ($.inArray(get_ext[0].toLowerCase(), exts) > -1) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                }
            }
        }
    }).on('success.form.bv', function (event) {
        event.preventDefault();
        $('#loadercats').show();
        var channel_id = $('#channel_id').val();
        url = $(this).attr('action');
        var formData = new FormData($(this)[0]);

        $.ajax({
            url: url + '?channel_id=' + channel_id,
            type: 'POST',
            data: formData,
            async: false,
            dataType: 'json',
            success: function (data) {
                message = $.parseJSON(data);
                //console.log(message.error);
                //console.log(message.error);
                if(message.hasOwnProperty('error')){
                alert(message.error);
            }
            if(message.hasOwnProperty('success')){
                alert(message.success);
            }
                $('.close').trigger('click');
                $('#loadercats').hide();
                getCategoryGrid(channel_id);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
}
//End of Category Upload---------------------------------------------------------------------------------------------
</script>
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>

<script type='text/javascript'>
//ChannelCategories --Hierarchial Grid 
function getCategoryGrid(channel_id) {
    $('#hierarchicalGrid').igHierarchicalGrid({
        dataSource: '/Commerceplatform/getChannelCategoriesGrid?channel_id=' + channel_id,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [
            {headerText: "<?php echo trans('cp_headings.cp_cat_name'); ?>", key: "category_name", dataType: "string", width: "15%"},
            {headerText: "<?php echo trans('cp_headings.cp_cat_id'); ?>", key: "mp_category_id", dataType: "string", width: "15%"},
            {headerText: "<?php echo trans('cp_headings.cp_charges'); ?>", key: "mp_commission", dataType: "number", width: "15%"},
            {headerText: "<?php echo trans('cp_headings.cp_chargetype'); ?>", key: "charge_type", dataType: "string", width: "15%"},
            {headerText: "<?php echo trans('cp_headings.cp_attributes'); ?>", key: "attribute_count", dataType: "number", width: "15%"},
            {headerText: "<?php echo trans('cp_headings.action'); ?>", key: "actions", dataType: "string", width: "15%"},
        ],
        columnLayouts: [
            {
                dataSource: '/Commerceplatform/getCannelAttributesGrid?channel_id=' + channel_id,
                autoGenerateColumns: false,
                autoGenerateLayouts: false,
                mergeUnboundColumns: false,
                responseDataKey: 'Records',
                generateCompactJSONResponse: false,
                enableUTCDates: true,
                columns: [
                    {headerText: "<?php echo trans('cp_headings.cp_attr_name'); ?>", key: "feature_name", dataType: "string", width: "55%"},
                    {headerText: "<?php echo trans('cp_headings.cp_attr_id'); ?>", key: "feature_id", dataType: "string", width: "15%"},
                    {headerText: "<?php echo trans('cp_headings.cp_var'); ?>", key: "variant_count", dataType: "string", width: "15%"},
                    {headerText: "<?php echo trans('cp_headings.action'); ?>", key: "actions", dataType: "string", width: "15%"}
                ],
                columnLayouts: [
                    {
                        dataSource: '/Commerceplatform/getCannelVariantsGrid?channel_id=' + channel_id,
                        autoGenerateColumns: false,
                        autoGenerateLayouts: false,
                        mergeUnboundColumns: false,
                        responseDataKey: 'Records',
                        generateCompactJSONResponse: false,
                        enableUTCDates: true,
                        columns: [
                            {headerText: "<?php echo trans('cp_headings.cp_var_name'); ?>", key: "mp_option_name", dataType: "string", width: "70%"},
                            {headerText: "<?php echo trans('cp_headings.cp_var_id'); ?>", key: "mp_option_id", dataType: "string", width: "15%"},
                            {headerText: "<?php echo trans('cp_headings.action'); ?>", key: "actions", dataType: "string", width: "15%"}
                        ],
                        features: [
                            {
                                recordCountKey: 'TotalRecordsCount',
                                chunkIndexUrlKey: 'page',
                                chunkSizeUrlKey: 'pageSize',
                                chunkSize: 5,
                                name: 'AppendRowsOnDemand',
                                loadTrigger: 'auto',
                                type: 'remote'
                            }
                        ],
                        primaryKey: 'mp_option_id',
                        width: '80%',
                        height: '250px',
                        localSchemaTransform: false
                    }],
                features: [
                    {
                        recordCountKey: 'TotalRecordsCount',
                        chunkIndexUrlKey: 'page',
                        chunkSizeUrlKey: 'pageSize',
                        chunkSize: 5,
                        name: 'AppendRowsOnDemand',
                        loadTrigger: 'auto',
                        type: 'remote'
                    }
                ],
                primaryKey: 'feature_id',
                width: '80%',
                height: '250px',
                localSchemaTransform: false
            }],
        features: [
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: "actions", allowFiltering: false}
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false,
                columnSettings: [
                    {columnKey: 'actions', allowSorting: false},
                ]

            },
            {
                recordCountKey: 'TotalRecordsCount',
                chunkIndexUrlKey: 'page',
                chunkSizeUrlKey: 'pageSize',
                chunkSize: 15,
                name: 'AppendRowsOnDemand',
                loadTrigger: 'auto',
                type: 'remote'
            }
        ],
        primaryKey: 'mp_category_id',
        width: '100%',
        height: '550px',
        initialDataBindDepth: 0,
        localSchemaTransform: false
    });
}
//End ChannelCategories --Hierarchial Grid 
// MAP ChannelCategories --Hierarchial Grid 
function getMapCategoryGrid(channel_id) {
    $('#MapCategoryGrid').igHierarchicalGrid({
        dataSource: '/Commerceplatform/getChannelCategoriesMapGrid?channel_id=' + channel_id,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [
            {headerText: "Channel Category", key: "mp_category_name", dataType: "string", width: "25%"},
            {headerText: "Ebutor Category", key: "eb_category_name", dataType: "string", width: "25%"},
            {headerText: "Total Atribute", key: "attribute_count", dataType: "number", width: "10%"},
            {headerText: "Required Attributes", key: "attribute_count", dataType: "string", width: "10%"},
            {headerText: "Qc Status", key: "attribute_count", dataType: "number", width: "10%"},
            {headerText: "Approved Attribute", key: "attribute_count", dataType: "number", width: "10%"},
            {headerText: "Actions", key: "actions", dataType: "string", width: "10%"},
        ],
        columnLayouts: [
            {
                dataSource: '/Commerceplatform/getCannelAttributesMapGrid?channel_id=' + channel_id,
                autoGenerateColumns: false,
                autoGenerateLayouts: false,
                mergeUnboundColumns: false,
                responseDataKey: 'Records',
                generateCompactJSONResponse: false,
                enableUTCDates: true,
                columns: [
                    {headerText: "Channel Attribute", key: "mp_attr_name", dataType: "string", width: "25%"},
                    {headerText: "Ebutor Attribute", key: "eb_attr_name", dataType: "string", width: "25%"},
                    {headerText: "Total Values", key: "variant_count", dataType: "string", width: "10%"},
                    {headerText: "Required Values", key: "variant_count", dataType: "string", width: "10%"},
                    {headerText: "Qc Status", key: "variant_count", dataType: "string", width: "10%"},
                    {headerText: "Approved Values", key: "variant_count", dataType: "string", width: "10%"},
                    {headerText: "Actions", key: "actions", dataType: "string", width: "10%"}
                ],
                columnLayouts: [
                    {
                        dataSource: '/Commerceplatform/getCannelVariantsMapGrid?channel_id=' + channel_id,
                        autoGenerateColumns: false,
                        autoGenerateLayouts: false,
                        mergeUnboundColumns: false,
                        responseDataKey: 'Records',
                        generateCompactJSONResponse: false,
                        enableUTCDates: true,
                        columns: [
                            {headerText: "Channel Value", key: "mp_option_name", dataType: "string", width: "55%"},
                            {headerText: "Ebutor Value", key: "eb_option_name", dataType: "string", width: "15%"},
                            {headerText: "Qc Status", key: "mp_option_id", dataType: "string", width: "15%"},
                            {headerText: "Actions", key: "actions", dataType: "string", width: "15%"}
                        ],
                        features: [
                            {
                                recordCountKey: 'TotalRecordsCount',
                                chunkIndexUrlKey: 'page',
                                chunkSizeUrlKey: 'pageSize',
                                chunkSize: 5,
                                name: 'AppendRowsOnDemand',
                                loadTrigger: 'auto',
                                type: 'remote'
                            }
                        ],
                        primaryKey: 'mp_option_id',
                        width: '90%',
                        height: '250px',
                        localSchemaTransform: false
                    }],
                features: [
                    {
                        recordCountKey: 'TotalRecordsCount',
                        chunkIndexUrlKey: 'page',
                        chunkSizeUrlKey: 'pageSize',
                        chunkSize: 5,
                        name: 'AppendRowsOnDemand',
                        loadTrigger: 'auto',
                        type: 'remote'
                    }
                ],
                primaryKey: 'mp_att_id',
                width: '80%',
                height: '250px',
                localSchemaTransform: false
            }],
        features: [
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: "actions", allowFiltering: false}
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false,
                columnSettings: [
                    {columnKey: 'actions', allowSorting: false},
                ]

            },
            {
                recordCountKey: 'TotalRecordsCount',
                chunkIndexUrlKey: 'page',
                chunkSizeUrlKey: 'pageSize',
                chunkSize: 15,
                name: 'AppendRowsOnDemand',
                loadTrigger: 'auto',
                type: 'remote'
            }
        ],
        primaryKey: 'mp_category_id',
        width: '100%',
        height: '550px',
        initialDataBindDepth: 0,
        localSchemaTransform: false
    });
}

//End MAP ChannelCategories --Hierarchial Grid 
//Delete Category
$(document).on('click', 'a.cat_delete', function () {
    var channel_id = $('#channel_id').val();
    var con = confirm("are you sure you want to delete?");
    if (con == true) {
        var link = $(this).attr('attr-type');
        var id = $(this).attr('atrr-catid');
        $.ajax({
            headers: {'X-CSRF-TOKEN': csrf_token},
            url: '/Commerceplatform/' + link + '/' + id + '?channel_id=' + channel_id,
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                getCategoryGrid(channel_id);
            },
            fail: function (data) {
                //alert("failed");
            }
        });
    }
});
//End Delete Category
//Delete MapCategory
$(document).on('click', 'a.mapcat_delete', function () {
    var channel_id = $('#channel_id').val();
    var con = confirm("are you sure you want to delete?");
    if (con == true) {
        var link = $(this).attr('attr-type');
        var id = $(this).attr('atrr-catid');
        $.ajax({
            headers: {'X-CSRF-TOKEN': csrf_token},
            url: '/Commerceplatform/' + link + '/' + id + '?channel_id=' + channel_id,
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                getMapCategoryGrid(channel_id);
            },
            fail: function (data) {
                //alert("failed");
            }
        });
    }
});
//End Delete MapCategory
//Add Channel Category 
function autoLoadParentCategories(channel_id) {
    $("#parent_catgory").autocomplete({
        minLength: 1,
        source: '/Commerceplatform/getparentcategories?channel_id=' + channel_id,
        appendTo: myModal1,
        select: function (event, ui) {
            var label = ui.item.label;
            $("#hidden_parent_id").val(ui.item.mp_category_id);
        }
    });
}
//add Category Form validation
function addChannelCategory() {
    $('#update_channel_fee').formValidation({
        //        live: 'disabled',
        framework: 'bootstrap',
        button: {
            selector: '#channel_save',
            disabled: 'disabled'
        },
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            channel_catgory: {
                message: 'Category exists',
                validators: {
                    notEmpty: {
                        message: 'Categoryname is Required'
                    },
                    remote: {
                        url: '/Commerceplatform/checkUniquevalue',
                        data: {channel_catgory: $('[name="channel_catgory"]').val()},
                        type: 'GET',
                    },
                    // message: 'This email is not available'
                }
            },
            parent_catgory: {
                validators: {
                    notEmpty: {
                        message: 'Parent Category is required'
                    }
                }
            },
            channel_cat_fee: {
                validators: {
                    notEmpty: {
                        message: 'Channel category fee is required'
                    }
                }
            },
            category_chargeType: {
                validators: {
                    notEmpty: {
                        message: 'CategoryChargeType is required'
                    }
                }
            },
            category_ID: {
                message: 'Categoryid exists',
                validators: {
                    notEmpty: {
                        message: 'category_ID is required'
                    },
                    remote: {
                        url: '/Commerceplatform/checkUniquecategoryid',
                        data: {category_ID: $('[name="category_ID"]').val()},
                        type: 'GET',
                    },
                }
            }
        }
    }).on('success.form.fv', function (event) {
        event.preventDefault();
        var channel_id = $('#channel_id').val();
        var inputs = $('#update_channel_fee').serialize();
        $.ajax({
            headers: {'X-CSRF-TOKEN': csrf_token},
            url: '/Commerceplatform/addchannelcategories?channel_id=' + channel_id,
            data: inputs,
            type: 'POST',
            success: function (data) {
                $('.close').trigger('click');
                getCategoryGrid(channel_id);
            },
            fail: function (data) {
                //alert("failed");
            }
        });
    });
}
//add Category Form validation
$(document).on("click", ".edit_category", function () {
    var mp_cat_id = $(this).attr('id');
    var channel_id = $('#channel_id').val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': csrf_token},
        type: "post",
        dataType: "json",
        url: "/Commerceplatform/getCategorydata?mp_cat_id=" + mp_cat_id + '&channel_id=' + channel_id,
        success: function (data)
        {
            $("#edit_channel_catgory_id").val(data.id);
            $("#edit_channel_catgory").val(data.category_name);
            $("#edit_channel_cat_fee").val(data.mp_commission);
            $("#edit_category_chargeType").val(data.charge_type);
        }
    });
});
//edit category validation
function editChannelCategory() {
    $('#edit_channel_cat').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            edit_channel_cat_fee: {
                validators: {
                    notEmpty: {
                        message: 'Channel category fee is required'
                    },
                    numeric: {
                        message: 'The value is not a number',
                        // The default separators
                        thousandsSeparator: '',
                        decimalSeparator: '.'
                    }
                }
            },
            edit_category_chargeType: {
                validators: {
                    notEmpty: {
                        message: 'CategoryChargeType is required'
                    }
                }
            }
        }
    }).on('success.form.bv', function (event) {
        event.preventDefault();
        var channel_id = $('#channel_id').val();
        var inputs = $(this).serialize();
        $.ajax({
            headers: {'X-CSRF-TOKEN': csrf_token},
            url: '/Commerceplatform/editChannelCategories?channel_id=' + channel_id,
            data: inputs,
            type: 'POST',
            success: function (data) {
                $('.close').trigger('click');
                getCategoryGrid(channel_id);
            },
            fail: function (data) {
                //alert("failed");
            }
        });
    });
}
//edit Category Form validation
//--------------------------------Ends Add Channel Category----------------------------------


//--------------------------------------Mapping Screen Scripts----------------------
//Mapping Screen Auto Load --channel categories
function AutoLoadChannelCategories() {
    var channel_id = $('#channel_id').val();
    var channel_url = '/Commerceplatform/getChannelCategories/' + channel_id;

    $("#getchannel_categories2").autocomplete({
        minLength: 1,
        source: channel_url,
        appendTo: myModal3,
        select: function (event, ui) {
            var label = ui.item.label;

            $("#results").html('<img src="/img/ajax-loader.gif" style="width:25px" class="pull-right" />');
            $("#hidden-channel_id").val(ui.item.mp_category_id);
            var categoryid = $("#hidden-channel_id").val();
            $.ajax({
                headers: {'X-CSRF-TOKEN': csrf_token},
                url: '/Commerceplatform/getchannelattributes/' + channel_id,
                data: 'category_name=' + label + '&categoryid=' + categoryid,
                type: 'GET',
                dataType: 'html',
                success: function (data) {

                    $("#results").show();
                    $('#results').html(data);
                    if (data == 'No Attributes available for this Category') {
                        $('#hidden-ebutor_id').val('');
                        $('#getebutor_categories2').val('');
                        $('#goalkeeper').prop('disabled', 'disabled');
                        $('#goalpoint').hide();
                        $('#goalpoint').html('');
                        $('#desc-issue').html('');

                    } else {
                        $('.acc-block select').prop('disabled', 'disabled');
                        //Code to restart isSufficent checking proces --Running same ebutor attribute values
                        var ebcatid = $("#hidden-ebutor_id").val();
                        if (ebcatid != '') {
                            checkEbCategoryAttributes(ebcatid);
                        }
                    }
                    //End of code 
                },
                fail: function (data) {
                    //alert("failed");
                    $("#results").show();

                }
            });
        }
    });
}
function AutoLoadEbutorCategories() {

    $("#getebutor_categories2").autocomplete({
        minLength: 1,
        source: '/Commerceplatform/getebutorcategories',
        appendTo: myModal3,
        select: function (event, ui) {
            var label = ui.item.label;
            $("#hidden-ebutor_id").val(ui.item.category_id);
            var categoryid = $("#hidden-ebutor_id").val();
            checkEbCategoryAttributes(categoryid);

        }
    });
}
function checkEbCategoryAttributes(catid) {
    if (catid != '') {
        $.ajax({
            headers: {'X-CSRF-TOKEN': csrf_token},
            url: '/Commerceplatform/getebutorCategoryattributes',
            data: 'categoryid=' + catid,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                if ($('#result').html() != 'No Attributes available for this Category') {
                    isSufficent(data);
                }

                // comparing categories 

            },
            fail: function (data) {
                //alert("failed");
                $("#results").html('Something went wrong !!');
            }
        });
    } else {
        console.log('Eb Attributes Error : categories not loaded');
    }
}

function isSufficent(data) {
    var mp_cats = $('#accordion3 .acc-head-tab').length;
    var eb_cats = data.count;


    if (eb_cats < mp_cats) {
        $('#accordion6').show();
        showMeMsg('Insufficient Attributes to Map ,Please create  required attributes ');
        //Error Report ___________________________________________
        $('#desc-issue').html('');
        var ch_at = '<table class="review_tab"><tr><td><table>';
        ch_at += '<tr><td><b>Channel Attributes</b></td></tr>';
        $('.acc-head-tab .panel-title').each(function () {

            ch_at += '<td style="text-align:left">' + $(this).text() + '</td></tr>';
        });

        ch_at += '</table></td>';


        // 
        var rec = "";
        ch_at += '<td><table>';
        ch_at += '<tr><td><b>Ebutor Attributes</b></td></tr>';

        $(data.attributes).each(function () {

            ch_at += '<tr><td style="text-align:left">' + this.name + '</td></tr>';
        });
        ch_at += '<tr><td style="color:#ccc;font-weight:italic"><a href="http://fbedev.local/attributes" target="_blank">Add New</a><br><i>(This will open in New tab)</i></td></tr></table></td></tr></table>';
        $('#desc-issue').html(ch_at);
        $('#goalkeeper').prop('disabled', 'disabled');
        $('.lock').hide();
        //End of Error Report___________________________________________-
    } else {
        $('#desc-issue').html('');
        $('#goalpoint').hide();
        $('#goalpoint').html('');
        $('#accordion6').hide();
        $('#goalkeeper').removeProp('disabled');
        //Setting Values for main selct boxes
        var options = $('.acc-head-tab select');
        var att_sel = "<select>";
        att_sel += '<option value="" >select</option>';
        $(data.attributes).each(function () {
            att_sel += '<option value="' + this.attribute_id + '" >' + this.name + '</option>';
        });
        att_sel += "</select>";
        options.replaceWith(att_sel);
        $('.acc-head-tab select').prop('disabled', 'disabled');
        //End of select box values
    }
}
function showMeMsg(msg) {
    var link = '<a aria-expanded="false" href="#collapse_0" data-parent="#accordion6" data-toggle="collapse" class="accordion-toggle collapsed">Review</a>';
    $('#goalpoint').show();
    $('#goalpoint').html(msg + link);
    $('#goalpoint').addClass('alert alert-danger fade in');
    $('#accordion6').show();



}
function desc_issue() {
    $('#accordion6').show();
}

function map() {
    $('#getchannel_categories2').prop('disabled', 'disabled');
    $('#getebutor_categories2').prop('disabled', 'disabled');
    $('.lock').show();
    $('#revert').show();
    $('#goalkeeper').hide();
    $('.acc-head-tab select').removeProp('disabled');


}
function revert_map() {
    $('#getchannel_categories2').val('');
    $('#channel_category_id').val('');

    $('#getchannel_categories2').removeProp('disabled');
    $('.acc-head-tab select').prop('disabled', 'disabled');

    $('#hidden-ebutor_id').val('');
    $('#getebutor_categories2').val('');
    $('#getebutor_categories2').removeProp('disabled');
    $('#revert').hide();
    $('.lock').hide();

    $('#goalkeeper').show();
    $('#results').html('Please select Channel category..!');
    $('.acc-head-tab select').replaceWith('<select></select>');
    $('.acc-head-tab select').prop('disabled', 'disabled');


}

function selectAttribute() {

}
//End of Mapping Screen Auto Load --channel categories
//--------END------------------------------Mapping Screen Scripts----------------------

//--------START------------------------------Channel Order Status Scripts----------------------
function getOrderStatusList() {
    var channel_id = $('#channel_id').val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': csrf_token},
        url: '/Commerceplatform/getOrderstatusList?channel_id=' + channel_id,
        type: 'POST',
        success: function (data) {
            $('#status_list').html(data);
        }
    });
}
$(document).on("click", ".edit_stat", function () {
    $stat_id = $(this).attr('data-map-id');
    var token = $("#cstoken").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': csrf_token},
        type: "post",
        dataType: "json",
        url: "/Commerceplatform/getmapdetails?stat_id=" + $stat_id,
        success: function (data)
        {
            $("#update_status_id").val(data.id);
            $("#status_type").val(data.status_type);
            $("#channel_status").val(data.mp_status);
            $("#ebutor_status").val(data.ebutor_status);
            $('#active_status').val(data.active_status);
            $('#status_save').text('Update');
            //$("select#service_type_id option").filter(":selected").text(data.service_type_id);*/
        }
    });
});
function getupdatedstatus() {
    var inputs = $('#channel_status_add').serialize();
    var channel_id = $('#channel_id').val();
    var token = $("#cstoken").val();
    var id = $("#mapping_id").val();

    $.ajax({
        headers: {'X-CSRF-TOKEN': csrf_token},
        url: '/Commerceplatform/getupdatedstatus?channel_id=' + channel_id,
        type: 'GET',
        data: inputs,
        success: function (data) {
            alert(data);
            getOrderStatusList();
        },
        fail: function (data) {

            //alert("failed");
        }
    });
}
function mapStatusValidation() {
    $('#status_save').click(function () {
        var formValid = $('#channel_status_add').formValidation('validate');
        $('#channel_status_add').submit();
    });
    var channel_id = $('#channel_id').val();
    $('#channel_status_add').formValidation({
        //        live: 'disabled',
        framework: 'bootstrap',
        button: {
            selector: '#channel_save',
            disabled: 'disabled'
        },
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            'channel_status': {
                message: 'Channel Status exists',
                validators: {
                    notEmpty: {
                        message: 'Channel Status is Required'
                    },
                    remote: {
                        url: '/Commerceplatform/checkUniquchannelstatus?channel_id=' + channel_id,
                        data: {channel_status: $('[name="channel_status"]').val()},
                        type: 'GET',
                        delay: 2000,
                    },
                    // message: 'This email is not available'
                }
            },
            'ebutor_status': {
                validators: {
                    notEmpty: {
                        message: 'Ebutor Status is required'
                    }
                }
            },
            'status_type': {
                validators: {
                    notEmpty: {
                        message: 'Status Type is required'
                    }
                }
            }
        }
    }).on('success.form.fv', function (event) {
        event.preventDefault();
        var channel_id = $('#channel_id').val();
        var inputs = $('#channel_status_add').serialize();
        $buttonval = $('#status_save').text();
        if ($buttonval == 'Update') {
            getupdatedstatus();
        } else {
            var url = '/Commerceplatform/addchannelstatus?channel_id=' + channel_id;

            $.ajax({
                url: url,
                data: inputs,
                type: 'GET',
                success: function (data) {

                    if (data) {
                        alert('Product or Order Channel Status already exists');
                    } else {
                        alert('added successfully');
                    }
                    getOrderStatusList();
                },
                fail: function (data) {
                    //alert("failed");
                    alert('channelstatus already exists')
                }
            });
        }
    });
}
$(document).on("click", ".delete_stat", function () {
    $id = $(this).attr('data-mapp-id');
    var token = $("#cstoken").val();
    var con = confirm("Are you sure you want to delete?");
    if (con == true) {

        $.ajax({
            headers: token,
            type: "post",
            dataType: "json",
            url: "/Commerceplatform/delChannelstatus?id=" + $id + "&_token=" + token,
            success: function (data)
            {
                alert('Sucessfully deleted');
                getOrderStatusList();
            }
        });
    }
});
//--------END------------------------------Channel Order Status Scripts----------------------
$(document).ready(function () {

    $(document).on('blur change focus', '#channel_charges_table tbody tr:first .chr_ctrl', function () {

        var validatecount = 0;
        $('#channel_charges_table .chr_ctrl').each(function () {
            if ($(this).val() == "") {
                // console.log( "this is emtpy");
                validatecount = validatecount + 1;
            } else {
                //  console.log( "this value---"+$(this).val());
            }

        });
        if (validatecount != 0) {
            $('#add_charge_button').attr('disabled', 'disabled');

        } else {
            $('#add_charge_button').removeProp('disabled');


        }

    });
    $(document).on('blur change focus', '#status_list tbody tr:first .chr_ctrl', function () {

        var validatecount = 0;
        $('#status_list .chr_ctrl').each(function () {
            if ($(this).val() == "") {
                // console.log( "this is emtpy");
                validatecount = validatecount + 1;
            } else {
                //  console.log( "this value---"+$(this).val());
            }

        });
        if (validatecount != 0) {
            $('#status_save').attr('disabled', 'disabled');

        } else {
            $('#status_save').removeProp('disabled');


        }

    });

});
$(document).on('click', '#close-preview', function(){ 
    $('.image-preview').popover('hide');
    // Hover befor close the preview
    $('.image-preview').hover(
        function () {
           $('.image-preview').popover('show');
        }, 
         function () {
           $('.image-preview').popover('hide');
        }
    );    
});

$(function() {
    // Create the close button
    var closebtn = $('<button/>', {
        type:"button",
        text: 'x',
        id: 'close-preview',
        style: 'font-size: initial;',
    });
    closebtn.attr("class","close pull-right");
    // Set the popover default content
    $('.image-preview').popover({
        trigger:'manual',
        html:true,
        title: "<strong>Preview</strong>"+$(closebtn)[0].outerHTML,
        content: "There's no image",
        placement:'bottom'
    });
    // Clear event
    $('.image-preview-clear').click(function(){
        $('.image-preview').attr("data-content","").popover('hide');
        $('.image-preview-filename').val("");
        $('.image-preview-clear').hide();
        $('.image-preview-input input:file').val("");
        $(".image-preview-input-title").text("Browse"); 
    }); 
    // Create the preview image
    $(".image-preview-input input:file").change(function (){     
        var img = $('<img/>', {
            id: 'dynamic',
            width:250,
            height:200
        });      
        var file = this.files[0];
        var reader = new FileReader();
        // Set preview image into the popover data-content
        reader.onload = function (e) {
            $(".image-preview-input-title").text("Change");
            $(".image-preview-clear").show();
            $(".image-preview-filename").val(file.name);            
            img.attr('src', e.target.result);
            $(".image-preview").attr("data-content",$(img)[0].outerHTML).popover("show");
        }        
        reader.readAsDataURL(file);
    });  
});


</script>
@stop