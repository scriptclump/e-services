@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">                        
                        <div class="table-responsive">
                            <table id="all_notifications"></table>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('style')
<style type="text/css">
.ui-iggrid .ui-iggrid-footer{
    padding: 1.6em .8em !important;

}    
</style>
@stop

@section('script') 
@include('includes.ignite')
{{HTML::script('assets/global/plugins/select2/select2.min.js') }}
@include('includes.validators')
<script type="text/javascript">
    $('#all_notifications').igGrid({
        dataSource: "/notification/all_notifications",
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        recordCountKey: 'totalCount',
        columns: [
            {headerText: 'Notification Template Id', key: '_id', dataType: 'int', width: '0%'},
            {headerText: 'Notification Code', key: 'message_code', dataType: 'string', width: '10%'},
            {headerText: 'Notification Message', key: 'message', dataType: 'string', width: '40%'},
            {headerText: 'Notification Link', key: 'link', dataType: 'string', width: '20%'},
            {headerText: 'Notification Created on', key: 'created_at', dataType: 'date', width: '20%'},
            {headerText: 'Actions', key: 'actions', dataType: 'string', width: '10%'}
        ],
        features: [
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: '_id', allowFiltering: false},
                    {columnKey: 'created_at', allowFiltering: false},
                    {columnKey: 'actions', allowFiltering: false}
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: true,
                columnSettings: [
                    {columnKey: '_id', allowSorting: false},
                    {columnKey: 'actions', allowSorting: false}
                ]
            },
            {
                recordCountKey: 'totalCount',
                chunkIndexUrlKey: 'page',
                chunkSizeUrlKey: 'pageSize',
                chunkSize: 10,
                name: 'AppendRowsOnDemand',
                loadTrigger: 'auto',
                type: 'remote',
                initialDataBindDepth: 0,
                localSchemaTransform: false,
                showHeaders: true,
                fixedHeaders: true
            }
        ],
        primaryKey: '_id',
        width: '100%',
        height: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false,
        showHeaders: true,
        fixedHeaders: true,
        rendered: function (evt, ui) {
            $("#all_notifications").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();
            $("#all_notifications").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();
            $("#all_notifications").find(".ui-iggrid-filtericonthisyear").closest("li").remove();
            $("#all_notifications").find(".ui-iggrid-filtericonlastyear").closest("li").remove();
            $("#all_notifications").find(".ui-iggrid-filtericonnextyear").closest("li").remove();
            $("#all_notifications").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
            $("#all_notifications").find(".ui-iggrid-filtericonyesterday").closest("li").remove();
            $("#all_notifications").find(".ui-iggrid-filtericontoday").closest("li").remove();
            $("#all_notifications").find(".ui-iggrid-filtericonafter").closest("li").remove();
            $("#all_notifications").find(".ui-iggrid-filtericonbefore").closest("li").remove();
            $(".ui-iggrid-filtericonnextmonth").each(function(){
                $(this).closest("li").remove();
            });     
            $(".ui-iggrid-filtericonlastmonth").each(function(){
                $(this).closest("li").remove();
            });
            $(".ui-iggrid-filtericonthisyear").each(function(){
                $(this).closest("li").remove();
            });     
            $(".ui-iggrid-filtericonlastyear").each(function(){
                $(this).closest("li").remove();
            });
            $(".ui-iggrid-filtericonnextyear").each(function(){
                $(this).closest("li").remove();
            });     
            $(".ui-iggrid-filtericonthismonth").each(function(){
                $(this).closest("li").remove();
            });
            $(".ui-iggrid-filtericonyesterday").each(function(){
                $(this).closest("li").remove();
            });
            $(".ui-iggrid-filtericontoday").each(function(){
                $(this).closest("li").remove();
            });
            $(".ui-iggrid-filtericonafter").each(function(){
                $(this).closest("li").remove();
            });
            $(".ui-iggrid-filtericonbefore").each(function(){
                $(this).closest("li").remove();
            });
        }
    });
</script>    
@stop
@extends('layouts.footer')