@extends('layouts.default')

@extends('layouts.header')

@extends('layouts.sideview')

@section('content')
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><strong>User </strong>  Controller</h3>
        @if($addPermission) 
        <a href="javascriot:void(0)"  data-toggle="modal" id="addUser" class="pull-right" data-target="#wizardCodeModal" data-url="{{URL::asset('users/add')}}"><i class="ion-android-person-add"></i> <span style="font-size:11px;">Add User</span></a>
        @endif
    </div>

    <div class="col-sm-12">
        <div class="tile-body nopadding">                  
            <div id="jqxgrid"  style="width:100% !important;"></div>
            <button data-toggle="modal" id="edit" class="btn btn-default" data-target="#wizardCodeModal" style="display: none"></button>
        </div>
    </div>

</div>  
<div class="modal fade" id="wizardCodeModal" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="basicvalCode">Create User</h4>
            </div>
            <div class="modal-body" id="popupLoader" align="center" style="display: none">
                <img src="/img/ajax-loader.gif" >
            </div>
            <div class="modal-body" id="userDiv"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->   

<div id="jqxgrid"  style="width:100% !important;"></div>

@stop
@include('includes.jqx')
@section('userscript') 
<script type="text/javascript">
    $(document).ready(function ()
    {
        var url = "users/usersList";
        var source =
        {
            datatype: "json",
            datafields: [
                {name: 'profile_picture', type: 'string'},
                {name: 'user_name', type: 'string'},
                {name: 'firstname', type: 'string'},
                {name: 'lastname', type: 'string'},
                {name: 'email_id', type: 'string'},
                {name: 'phone_no', type: 'integer'},
                {name: 'is_active', type: 'string'},
                {name: 'actions', type: 'string'}
            ],
            id: 'user_id',
            url: url
        };
    });
    function deleteEntityType(user_id)
    {
        var decission = confirm("Are you sure you want to Delete.");
        if (decission == true)
            window.location.href = 'users/delete/' + user_id;
    }

    $("#addUser").click(function () {
        $.get($(this).attr('data-url'), function (response) {
            $("#basicvalCode").html('Create New User');
            $("#userDiv").html(response);
        });
    });

    $("#addNewUser").click(function () {
        //alert($(this).attr('data-url'));
        $.get($(this).attr('data-url'), function (response) {
            $("#basicvalCode").html('Add New User');
            $("#userDiv").html(response);
        });
    });

    function editUser(id)
    {

        $.get('users/edit/' + id, function (response) {
            $("#basicvalCode").html('Edit User');
            $("#userDiv").html(response);
            $("#edit").click();
        });
    }

</script>
@stop