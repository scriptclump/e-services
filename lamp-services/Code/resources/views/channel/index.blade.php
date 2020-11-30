@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')


<ul class="page-breadcrumb breadcrumb">
    <li><a href="#">Dashboard</a><i aria-hidden="true" class="fa fa-angle-right"></i>
    </li><li><a class="active">{{trans('cp_headings.cp')}}</a></li></ul>


<div class="row">
    <div class="col-md-12">

        <div class="portlet light tasks-widget" id="form_wizard_1">
            <div class="portlet-title">
                <div class="caption"> {{trans('cp_headings.cp')}}<a href="Commerceplatform/create" class="btn green-meadow">{{trans('cp_headings.cp_add')}}</a> </div>

            </div>
            <div class="portlet-body form">

                <div class="form-wizard">
                    <div class="form-body">
                        <div class="box">
                            <div class="tile-body nopadding">
                                <input id="csrf_token" type="hidden" name="_token" value="{{csrf_token()}}">
                                <div id="hierarchicalGrid"></div>
                            </div>                         
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('script')
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>

<script type='text/javascript'>
var csrf_token = $('#csrf_token').val();
$(document).ready(function () {
    getChannelGrid();
    $('#channeldeact').click(function () {

        alert('jo');
    });

    
});
// Start Channel---ignite Grid
function getChannelGrid() {
    console.log('getchannel intiated');
    $('#hierarchicalGrid').igGrid({
        dataSource: '/Commerceplatform/getAllChannels',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [
            {headerText: "<?php echo trans('cp_headings.cp_grid_logo'); ?>", key: "mp_logo", dataType: "string", width: "15%"},
            {headerText: "<?php echo trans('cp_headings.cp_grid_title'); ?>", key: "mp_name", dataType: "string", width: "20%"},
            {headerText: "<?php echo trans('cp_headings.cp_grid_url'); ?>", key: "mp_url", dataType: "string", width: "30%"},
            {headerText: "<?php echo trans('cp_headings.cp_grid_type'); ?>", key: "mp_type", dataType: "string", width: "10%"},
            //{headerText: "<?php echo trans('cp_headings.status'); ?>", key: "is_active", dataType: "string", width: "10%"},
            // {headerText: "OrderSync", key: "is_active", dataType: "string", width: "10%"},
            // {headerText: "Inventory sync", key: "is_active", dataType: "string", width: "10%"},
            {headerText: "<?php echo trans('cp_headings.action'); ?>", key: "actions", dataType: "string", width: "10%"},
        ],
        columnLayouts: [
            {
            }],
        features: [
            {
                name: "Filtering",
                type: 'remote',
                persist: false,
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: "mp_logo", allowFiltering: false},
                    {columnKey: "is_active", allowFiltering: false},
                    {columnKey: "actions", allowFiltering: false}
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false,
                columnSettings: [
                    {columnKey: 'mp_logo', allowSorting: false},
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
        primaryKey: 'mp_id',
        width: '100%',
        height: '550px',
        initialDataBindDepth: 0,
        localSchemaTransform: false
    });
}
//End Channel --ignite Grid 
function deleteChannel(channel_id)
{
    var decission = confirm("Are you sure you want to Delete.");
    if (decission == true) {
        $.ajax({
            headers: {'X-CSRF-TOKEN': csrf_token},
            type: "post",
            dataType: "json",
            data: {channel_id: channel_id},
            url: "/Commerceplatform/deleteChannel",
            success: function (data)
            {
                $("#hierarchicalGrid").igGrid("dataBind");
            }
        });
    }

}
function channelStatuschange(channel_id, status)
{
    if (status == 1) {
        var decission = confirm("Are you sure you want to deactivate.");
    } else {
        var decission = confirm("Are you sure you want to activate.");
    }
    if (decission == true) {


        $.ajax({
            headers: {'X-CSRF-TOKEN': csrf_token},
            type: "post",
            dataType: "json",
            data: {channel_id: channel_id, status: status},
            url: "/Commerceplatform/channelStatuschange",
            success: function (data) {
                $("#hierarchicalGrid").igGrid("dataBind");
            }
        });
    }


}
</script>
@stop