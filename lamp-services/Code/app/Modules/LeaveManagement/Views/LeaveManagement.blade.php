@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="portlet-body">
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">{{trans('leavehistory.leavehistory_heads.caption')}}</div>
            </div>
            <div class="portlet-body">
                <div role="alert" id="alertStatus"></div>
                <div class="row">
                    <div class="col-md-12">                        
                        <div class="table-responsive">
                            <table id="leaveHistoryGrid"></table>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<style>
    .alignRight{
        text-align: right !important;
        padding: 10px 10px 10px 10px;
    }
    .actionsStyle{
        padding-left: 20px;
    }
</style>
@stop
@section('script')
@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script>
<script type="text/javascript">

    $(document).ready(function ()
    {
        $(function(){
            leaveHistoryGrid();
            $('#leaveHistoryGrid_dd_from_date').find('.ui-iggrid-filtericonthismonth').parents('li').remove();
            $('#leaveHistoryGrid_dd_from_date').find('.ui-iggrid-filtericonlastmonth').parents('li').remove();
            $('#leaveHistoryGrid_dd_from_date').find('.ui-iggrid-filtericonnextmonth').parents('li').remove();
            $('#leaveHistoryGrid_dd_from_date').find('.ui-iggrid-filtericonthisyear').parents('li').remove();
            $('#leaveHistoryGrid_dd_from_date').find('.ui-iggrid-filtericonlastyear').parents('li').remove();
            $('#leaveHistoryGrid_dd_from_date').find('.ui-iggrid-filtericonnextyear').parents('li').remove();
            $('#leaveHistoryGrid_dd_from_date').find('.ui-iggrid-filtericonbefore').parents('li').remove();
            $('#leaveHistoryGrid_dd_from_date').find('.ui-iggrid-filtericonafter').parents('li').remove();
            $('#leaveHistoryGrid_dd_to_date').find('.ui-iggrid-filtericonafter').parents('li').remove();
            $('#leaveHistoryGrid_dd_to_date').find('.ui-iggrid-filtericonbefore').parents('li').remove();
            $('#leaveHistoryGrid_dd_to_date').find('.ui-iggrid-filtericonthismonth').parents('li').remove();
            $('#leaveHistoryGrid_dd_to_date').find('.ui-iggrid-filtericonlastmonth').parents('li').remove();
            $('#leaveHistoryGrid_dd_to_date').find('.ui-iggrid-filtericonnextmonth').parents('li').remove();
            $('#leaveHistoryGrid_dd_to_date').find('.ui-iggrid-filtericonthisyear').parents('li').remove();
            $('#leaveHistoryGrid_dd_to_date').find('.ui-iggrid-filtericonlastyear').parents('li').remove();
            $('#leaveHistoryGrid_dd_to_date').find('.ui-iggrid-filtericonnextyear').parents('li').remove();
            $('#leaveHistoryGrid_dd_emergency_mail').find('.ui-iggrid-filtericondoesnotcontain').parents('li').remove();
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          });
        function leaveHistoryGrid()
        {   
            $('#leaveHistoryGrid').igGrid({
                dataSource: '/leavemanagement/list',
                responseDataKey: 'results',
                height:'100%',
                columns: [
                    {headerText: "{{trans('leavehistory.leavehistory_side_heads.emp_ep_id')}}", key: "emp_ep_id", dataType: "string", width: '5%',template: "<div style='text-align:right;padding-right:10px'>${emp_ep_id}</div>"},
                    {headerText: "Emp Type", key: "emp_type", dataType: "string", width: '8%',template: "<div style='text-align:right;padding-right:10px'>${emp_type}</div>"},
                    {headerText: "{{trans('leavehistory.leavehistory_side_heads.emp_name')}}", key: "emp_name", dataType: "string", width: '10%',template: "<div style='padding-left:10px'>${emp_name}</div>"},
                
                   {headerText: "{{trans('leavehistory.leavehistory_side_heads.contact_number')}}", key: "contact_number", dataType: "string", width: '6%',template: "<div style='text-align:right;padding-right:10px'>${contact_number}</div>"},
                    {headerText: "{{trans('leavehistory.leavehistory_side_heads.from_date')}}", key: "from_date", dataType: "date",format: "MM/dd/yyyy", width: '6%',template: "<div style='padding-left:10px'>${from_date}</div>"},
                     {headerText: "{{trans('leavehistory.leavehistory_side_heads.to_date')}}", key: "to_date", dataType: "date",format: "MM/dd/yyyy", width: '6%',template: "<div style='padding-left:10px'>${to_date}</div>"},
                    {headerText: "{{trans('leavehistory.leavehistory_side_heads.no_of_days')}}", key: "no_of_days", dataType: "number", width: '4%',template: "<div style='text-align:right;padding-right:10px'>${no_of_days}</div>"},
                    {headerText: "{{trans('leavehistory.leavehistory_side_heads.leave_type')}}", key: "leave_type", dataType: "string",width: '7%',template: "<div style='text-align:left;padding-left:10px'>${leave_type}</div>"},
                    {headerText: "{{trans('leavehistory.leavehistory_side_heads.reason')}}", key:"reason",dataType: "string", width: '9%'},
                    {headerText: "{{trans('leavehistory.leavehistory_side_heads.status')}}", key:"status",dataType: "string", width: '8%'},
                    ],
                features: [
                    {
                        name: "Filtering",
                        mode: "simple",
                        columnSettings: [
                    
                            {columnKey: 'from_date', allowFiltering: true},
                            {columnKey: 'to_date', allowFiltering: true},
                            {columnKey: 'emp_ep_id', allowFiltering: true},
                            {columnKey: 'emp_name', allowFiltering: true},
                            {columnKey: 'emergency_mail', allowFiltering: true},
                            {columnKey: 'contact_number', allowFiltering: true},
                            {columnKey: 'no_of_days', allowFiltering: true},
                            {columnKey: 'leave_type', allowFiltering: true},
                            {columnKey: 'reason', allowFiltering: true},
                            {columnKey: 'status', allowFiltering: true},

                        ]
                    },
                    {
                        name: "Sorting",
                        type: "remote",
                        persist: false,
                        columnSettings: [
                            {columnKey: 'from_date', allowFiltering: true},
                            {columnKey: 'to_date', allowFiltering: true},
                            {columnKey: 'emp_ep_id', allowFiltering: true},
                            {columnKey: 'emp_name', allowFiltering: true},
                            {columnKey: 'emergency_mail', allowFiltering: true},
                            {columnKey: 'contact_number', allowFiltering: true},
                            {columnKey: 'no_of_days', allowFiltering: true},
                            {columnKey: 'leave_type', allowFiltering: true},
                            {columnKey: 'reason', allowFiltering: true},
                            {columnKey: 'status', allowFiltering: true},
                        ],
                    },
                    {
                         name: 'Paging',
                         type: 'remote',
                         pageSize: 10,
                         recordCountKey: 'TotalRecordsCount',
                         pageIndexUrlKey: "page",
                         pageSizeUrlKey: "pageSize"
                    },
                    {
                        name: "Resizing",
                    }
                ]
            }); 
        }
    });
        
</script>
@stop
@extends('layouts.footer')