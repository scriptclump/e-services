@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
@if (Session::has('flash_message'))            
<div class="alert alert-info">{{ Session::get('flash_message') }}
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="float: right;">&times;</button>
</div>
@endif
<div class="box">
    <div class="box-header">   

        @if(Session::has('successMsg'))
        <?PHP
        $successMsg = Session::get('successMsg');
        ?>
        <div style="color:#008a00"><h4>
                <?PHP echo $successMsg; ?></h4>
        </div>
        @endif

<!--<a href="{{URL::asset('roles/add')}}" class="pull-right" style="float: right;margin-top: -38px;padding-right: 20px;"><i class="fa fa-user-plus"></i> <span style="font-size:11px;">Add New Role</span></a>-->
<!--<a href="{{URL::asset('rbac/add')}}"  style="float: right;margin-top: -38px;padding-right: 20px;"><i class="fa fa-user-plus"></i> <span style="font-size:11px;">Add New Role</span></a>-->
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> Roles </div>
                <div class="actions">
                    @if($addRole)
                    <a href="/roles/add" class="btn green-meadow"><i class="fa fa-plus-circle"></i> Add Role</a>
                    @if(Session::has('invalidrole'))            
                    @endif
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="addroles"></table>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('style')
<style>
    .fa-pencil {
        color: #3598dc !important;
    }
    .fa-trash-o {
        color: #3598dc !important;
    }
    .textRight{
        text-align: right !important;
    }
    #addroles > tbody > tr > td {padding: 0px 5px 0px 5px !important;}
    #addroles_headers > thead > tr > th {padding: 0px 5px 0px 5px !important;}
    .alert-info {
        background-color: #00c0ef !important;
        border-color: #00c0ef !important;
        color: #fff !important;
    }  
</style>
@stop
@section('script') 
@include('includes.ignite')
<script type="text/javascript">

    $(document).ready(function ()
    {
        $('#addroles').igGrid({
            dataSource: "/roles/getRoles/0",
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'Records',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {headerText: 'Role Code', key: 'short_code', dataType: 'string', width: '10%'},
                {headerText: 'Role Name', key: 'name', dataType: 'string', width: '30%'},
                {headerText: 'Number Of Users', key: 'userscount', dataType: 'number', columnCssClass: "textRight", headerCssClass: "textRight", width: '13%'},
                {headerText: 'Parent Role', key: 'parent_role', dataType: 'string', width: '25%'},
                {headerText: 'Created By', key: 'created_by', dataType: 'string', width: '10%'},
                {headerText: 'Is Support Role', key: 'is_support_role', dataType: 'boolean', width: '10%'},
                {headerText: 'Actions', key: 'actions', dataType: 'string', width: '10%'}
            ],
            features: [
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'actions', allowFiltering: false}
                    ]
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false
                },
                {
                    name: 'Paging',
                    type: "local",
                    pageSize: 10
                },
                {
                    name : 'Resizing',
                }
            ],
            primaryKey: 'role_id',
            width: '100%',
            height: '100%',
            initialDataBindDepth: 0,
            localSchemaTransform: false
        });
    });</script>
<script type="text/javascript">
    $(document).ready(function () {
        window.setTimeout(function () {
            $(".alert").hide();
        }, 3000);
    });
    function CheckedFeature(val, fromid, toid) {
        if ( val == false ) {
            for (i = fromid; i <= toid; i++) {
                $("#feature_name" + i).prop("checked", false);
            }
        } else {
            for (i = fromid; i < toid; i++) {
                $("#feature_name" + i).prop("checked", true);
            }
        }
    }
    function getCustomerUser(id) {
        $.get('getUserDetail/' + id, function (data) {
            var dataArr = $.parseJSON(data);
            var Str = '<table border="0">';
            Str += '<tr><th><input type="checkbox" value="1" name="multiSelect" id="multiSelect"> </th>';
            Str += '<th>User Name</th><th>Email</th></tr>';
            for (i = 0; i < dataArr.length; i++) {
                Str += '<tr><td><input type="checkbox" value="' + dataArr[i].user_id + '" name="user_id[]" id="user_id_' + dataArr[i].user_id + '"> </td>';
                Str += '<td>' + dataArr[i].username + '</td>';
                Str += '<td>' + dataArr[i].email + '</td>';
                Str += '</tr>'
            }
            Str += '</table>';
            $("#userTab").html(Str);
        });
    }
</script>  
@stop
@extends('layouts.footer')