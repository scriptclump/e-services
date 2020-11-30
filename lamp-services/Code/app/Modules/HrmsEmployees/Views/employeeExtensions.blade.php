@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="alert alert-success hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="#">HR Policies</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Employee Extensions</li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> Employee Extensions </div>
            </div>
            <br />

            <div class="portlet-body">
                <table id="extensionListGrid"></table>
            </div>
        </div>
    </div>
</div>

{{HTML::style('css/switch-custom.css')}}
<style>
    .cpenabled {
        margin-left: 25px !important;
    }
</style>

@stop

@section('userscript')
@include('includes.ignite')
@include('includes.group_repo')
<script>
    $(function () {
    empExtensions();
    function empExtensions() {
    $('#extensionListGrid').igHierarchicalGrid({
        dataSource: '/employee/getextensions',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        expandColWidth: 0,
        enableUTCDates: true,
        columns: [
            {headerText: 'Employee Name', key: 'Employee_Name', dataType: 'string', width: '30%'},
            {headerText: 'Designation', key: 'Designation', dataType: 'string', width: '30%'},
            {headerText: 'Department', key: 'Department', dataType: 'string', width: '20%'},
            {headerText: 'Landline Extension', key: 'Landline_Extension', dataType: 'string', width: '20%'}
        ],
        features: [
            {
                name: "Filtering",
                type: "local",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                ]

            },
            {
                name: 'Sorting',
                type: 'local',
                mode: "simple",
                columnSettings: [
                ]

            },
            {
                name: 'Paging',
                loadTrigger: 'auto',
                type: 'local',
                pageSize: 10
            },
        ],
        primaryKey: 'firstname',
        width: '100%',
        height: '500px',
        initialDataBindDepth: 0,
        localSchemaTransform: false,
        rendered: function (evt, ui) {
                    $("#extensionListGrid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();    
                    $("#extensionListGrid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
                    $("#extensionListGrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
                }
    });
}

    });
</script>
@stop
@extends('layouts.footer')