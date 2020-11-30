@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="portlet box" id="form_wizard_1">
            <div class="portlet-body form">
                <div id="formDiv">
                    <form action="#" class="submit_form" id="submit_form" method="get">
                        <input type="hidden" name="legal_entity_id" value="{{ $legal_entity_id }}" />
                        @if(isset($seller_id))
                        <input type="hidden" name="seller_id" id="seller_id" value="{{ $seller_id }}" />
                        @endif
                        <div class="form-wizard">
                            <div class="form-body" style="min-height:612px;">
                                <ul class="nav nav-pills nav-justified steps">
                                    <li>
                                        <a href="#tab1" data-toggle="tab" class="step tab1 active" >
                                            <span class="number">
                                                <i class="fa fa-building-o"></i> </span>
                                            <span class="desc">
                                                Select Marketplace</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#tab2" data-toggle="tab"  class="step tab2">
                                            <span class="number">
                                                <i class="fa fa-gear"></i> </span>
                                            <span class="desc">
                                                Configure Seller</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#tab3" data-toggle="tab" class="step tab3">
                                            <span class="number">
                                                <i class="fa fa-check"></i> </span>
                                            <span class="desc">
                                                Complete </span>
                                        </a>
                                    </li>
                                </ul>
                                <div id="bar" class="progress progress1 progress-striped" role="progressbar">
                                    <div class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                                <div class="tab-content">
                                    <div class="alert alert-danger display-none hideerror validata">
                                        <button class="close" data-dismiss="alert"></button>
                                        You have some form errors. Please check below.
                                    </div>
                                    <div class="alert  alert-channel-selection display-none hideerror barhide"  style="color: #a94442;background-color: #f2dede;border-color: #ebccd1;padding: 15px;margin-bottom: 20px;border: 1px solid transparent;border-radius: 4px;">
                                        <button class="close" data-dismiss="alert"></button>
                                        Please select Market Place to continue.
                                    </div>
                                    <!--                                <div class="alert alert-success display-none">
                                                                        <button class="close" data-dismiss="alert"></button>
                                                                        Your form validation is successful!
                                                                    </div>-->
                                    <div class="tab-pane active" id="tab1">                                    
                                        <div class="row">
                                            <div class="col-md-10">
                                                <h3 class="">Select Marketplace</h3>
                                            </div>



                                            <div class="col-md-2 pull-right" id="channel_names">
                                                <div class="inputs">
                                                    <div class="portlet-input input-small input-inline" id="channel_names">
                                                        <div class="input-icon right">
                                                            <i class="icon-magnifier"></i>
                                                            <input type="text" class="form-control form-control-solid" placeholder="search...">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" id="theList">

                                            @foreach($channel_info as $channel_infos)
                                            
                                            <div class="col-sm-6 col-md-2"><a href="javascript:void(0);" class="thumbnail"><img class="mp_logos" src="{{$channel_infos->mp_logo}}" itemid="{{$channel_infos->mp_id}}" accesskey="{{$channel_infos->mp_key}}" width="68px" height="30px"> <p class="channel_name text-center">{{$channel_infos->mp_name}}</p></a></div>

                                            @endforeach

                                        </div>
                                        <div id="nomarketplace"> </div>
                                        <div class="row" style="margin-top:40px;">
                                            <div class="col-md-12 text-center">
                                                <a href="javascript:void(0);" class="btn green-meadow button-next" id="seller-continue-button">Continue <i class="m-icon-swapright m-icon-white"></i></a>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="tab2">                                    
                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="note note-info">
                                                    <div class="image-center text-center"></div>  
                                                    <p class="channelDescription"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Seller Name
                                                                <span data-original-title="Seller Name" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                                                            </label>

                                                            <input type="text" class="form-control step-1" placeholder="Seller Name" name="sellername" id="sellername" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Select Fulfillment Center
                                                                <span data-original-title="Fulfillment Center" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                                                            </label>
                                                            <select class="form-control step-1" name="wharehouseId" id="wharehouseId" >
                                                                <option value="">Please Select..</option>
                                                                @foreach($warehouse_list as $warehouse_lists)
                                                                @if(!empty($warehouse_lists->lp_wh_name))
                                                                <option value="{{$warehouse_lists->lp_wh_id}}">{{$warehouse_lists->lp_wh_name}}</option>
                                                                @endif
                                                                @endforeach
                                                            </select>
                                                            <label id="error"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Channel Reference Name
                                                                <span data-original-title="Channel Reference Name " data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                                                            </label>
                                                            <input type="text" class="form-control" placeholder="Channel Reference Name" name="channelreferancename" id="channel_referance_name" >

                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Description
                                                                <span data-original-title="Description" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                                                            </label>
                                                            <input type="text" class="form-control" placeholder="Description" name="description" id="description" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Market Place User Name
                                                                <span data-original-title="Market Place User Name" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                                                            </label>
                                                            <input type="text" class="form-control" placeholder="Market Place User Name" name="marketplaceusername" id="market_place_username">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Market Place Password
                                                                <span data-original-title="Market Place Password" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                                                            </label>
                                                            <input type="password" class="form-control" placeholder="Market Place Password" name="password" id="marketplace_password" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="input_fields_wrap"></div>

                                                </div>
                                                <div class="input_hidden_wrap"></div>   
                                            </div>
                                        </div>
                                        <hr />
                                        <div class="row" style="margin-top:40px;">
                                            <div class="col-md-12 text-center">
                                                <input type="button" class="btn green-meadow keystest" value="Test & Save" id="keystest" />
                                                <a href="javascript:void(0);" class="btn green-meadow goBack" ><i class="m-icon-swapleft m-icon-white"></i> Back </a>

                                                <i class="m-icon-swapright m-icon-white"></i>
                                                <span class="loader" id="authloader" style="display:none"><img src="/img/ajax-loader.gif" style="width:25px" class="" /></span>

                                            </div>
                                        </div>

                                    </div>
                                    <div class="tab-pane" id="tab3">

                                        <div class="row">
                                            <div class="col-md-12">

                                                <h3 class="block" style="text-align:center;">You have successfully registerd <span class="font-green sellername"></span> as a seller for <span class="font-green channelname"></span>.Now you can see all the orders and process them.</h3><h3 class="update"></h3>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('style')
<style type="text/css">
    .actionss{font-size:24px !important;}

    .code {
        font-size: 24px !important;
    }
    .note {
        border-left: 0px !important;
    }
    .note.note-info.note-shadow {
        box-shadow:none !important;
    }
    a.thumbnail.active, a.thumbnail:focus, a.thumbnail:hover {
        border-color: #a0c9f5 !important;
        background-color:#e9f3fd;
        /*padding: 10px 10px 0px 10px;*/
    }
    .form-actions {
        background:none !important;
        border-top: 1px solid #e5e5e5;
    }
    .btn {
        border-radius: 0px !important;
    }
    .form-control {
        .border-radius: 0px !important;
    }

    .nofile{background:none !important; border:none !important;}

    .fileUpload {
        position: relative;
        overflow: hidden;
        margin: 0px;
        float: left;
    }
    .help-block{

        width: 300px !important;

    }
    .green-meadow {
        color: #1BBC9B;
    }

    [class^="fa-"]:not(.fa-stack), [class^="glyphicon-"], [class^="icon-"], [class*=" fa-"]:not(.fa-stack), [class*=" glyphicon-"], [class*=" icon-"] {
        font-size: 15px !important;

    }
    .fileUpload input.upload {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 13px;
        cursor: pointer;
        opacity: 0;
        filter: alpha(opacity=0);
        float: left;
    }
    #upload_file, #pan_file {
        background-color: #fff !important;
        border: 0px;
        float: left;
        position: absolute;
        margin-left: 20px;
        line-height: 30px;
    }
    .progress1 {
        height: 2px !important;
        margin-bottom: 0px !important;
        overflow: hidden  !important;
        background-color: #f5f5f5 !important;
        border-radius: 4px !important;
        -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.1) !important;
        box-shadow: inset 0 1px 2px rgba(0,0,0,.1) !important;
        position: relative !important;
        top: -82px !important;
        width: 65% !important;
        margin: 0 auto !important;
    }
    .form-wizard .steps > li > a.step > .number {
        z-index: 9 !important;
        position: relative !important;
        border:2px solid #dfdfdf !important;
        color:#dfdfdf !important;
    }
    .form-wizard .steps > li > a.step > .desc{display:block !important; color:#dfdfdf !important;}


    #upload_file, #pan_file {
        background-color: #fff !important;
        border: 0px;
        float: left;
        position: absolute;
        margin-left: 20px;
        line-height: 30px;
        right:-280px;
    }

    .has-feedback label~.form-control-feedback {
        top: 40px !important;
    }
    .note {
        border-left:none !important;
    }
    .mp_logos{width:180px !important; height:80px !important;}
    .channelDescription{padding-top:15px !important; text-align:left;}
    .thumbnail{border-radius:0px !important;}

    .form-wizard .steps > li.active > a.step .desc {
        color: #5b9bd1 !important;
    }
    .form-wizard .steps > li.active > a.step .number {
        background-color:#fff !important;
        color: #5b9bd1;
        border: 2px solid #5d9ad0 !important;
        color:#5d9ad0 !important;
    }
	.progress > .progress-bar-success {
    background-color: #75a9d7 !important;
}
    /*.badge {
        padding: 3px 4px !important;
        -webkit-border-radius: 50% !important;
        -moz-border-radius: 50% !important;
        border-radius: 50% !important;
    }
    */
</style>
@stop

@include('includes.validators')
@section('userscript')
<script src="{{ URL::asset('/assets/global/plugins/list.min.js') }}"></script> 
<script src="{{ URL::asset('assets/admin/pages/scripts/addseller-form-wizard.js') }}" type="text/javascript"></script>
{{HTML::script('assets/global/plugins/validator/formValidation.min.js')}}
{{HTML::script('assets/global/plugins/validator/validator.bootstrap.min.js')}}
{{HTML::script('assets/global/plugins/validator/jquery.bootstrap.wizard.min.js')}}
<script>
    jQuery(document).ready(function () {
    Metronic.init(); // init metronic core componets
    Layout.init(); // init layout
    Demo.init(); // init demo features
    FormWizard.init();
    QuickSidebar.init(); // init quick sidebar
    Index.init(); // init index page
    Tasks.initDashboardWidget(); // init tash dashboard widget 
    ComponentsFormTools.init();
    FormFileUpload.init();
    });</script> 

<script type="text/javascript">
    jQuery(document).ready(function () {
    $('#done').addClass('disabled');
    FormWizard.init();
    });
    $('.progress-bar-success').width('0%');
    $('[href="#tab1"]').parent('li').addClass('active');
    $(function () {
    var sellerId = $('#seller_id').val();
    if (sellerId > 0)
    {
    $('a.thumbnail.active img').trigger('click');
    $('#seller-continue-button').trigger('click');
    }
    $('[href="#tab1"]').parent('li').addClass('active');
    $('.progress-bar-success').width('33.33%');
    var options = {
    valueNames: ['channel_name']
    };
    var userList = new List('channel_names', options);
    });
    var cahnnel_Id = '';
    $('#seller-continue-button').click(function () {
    if ($('a.thumbnail.active').length == 1)
    {
    $('.progress-bar-success').width('50.66%');
    $('.alert.alert-channel-selection').hide();
    $('li.active').removeClass('active');
    $('[href="#tab1"]').parent('li').addClass('active');
    $('[href="#tab2"]').parent('li').addClass('active');
    $('#tab1').removeClass('active');
    $('#tab2').addClass('active');
    } else {
    $('.alert.alert-channel-selection').show();
    }
    //checkbtndisabled();
    });
    var dynamicFields = [];
    $('.mp_logos').click(function () {
    $('a.thumbnail').removeClass('active');
    $(this).parent('a').addClass('active');
    var channelId = $(this).attr('itemid');
    var accesskey = $(this).attr('accesskey');
    
    cahnnel_Id = channelId;
    var sellerId = $('#seller_id').val();
    $.get("/seller/sellerconfig/" + channelId + "/" + sellerId, function (data) {
    var response = $.parseJSON(data);
    var wrapper = $(".input_fields_wrap");
    var channelKeys = '';
    //var dynamicFields = [];

    $.each(response, function (index, value) {
    var fieldName = value.field_name.split("_").join(" ");
    //var fieldName = value.field_name;
    channelKeys += '<div class="col-md-6 testing' + " " + value.field_code + '"><div class="form-group"><label class="control-label">' + fieldName + '</label>&nbsp;<span title="' + fieldName + '" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span><input type="text" class="form-control dynamic" value="" name="' + value.field_code + '" placeholder="' + fieldName + '" required style="width: 100%;" id="' + value.field_code + '" ></div></div>';
    //alert(value.field_code);
    dynamicFields.push(value.field_code); 
    
    });
    $(wrapper).html(channelKeys);
    $.each(dynamicFields, function(i, v){
    $('#submit_form').formValidation('addField', v);
    $("#" + v).keyup(function(){        
    if ($("#" + v).val() == ''){
    $('#submit_form').find("." + v).addClass('has-error');
    } else{
    $('#submit_form').find("." + v).removeClass('has-error');
    $('#submit_form').find("." + v).addClass('has-success');
    //$('#keystest').prop('disabled', false);
    }

    });
    });
    $(".keystest").on("click", function(){
    if ($('#submit_form').data('formValidation').isValid())
    {
    $(".goBack").addClass('disabled');
    var namesValues = '';
    $(".dynamic").each(function(){
    namesValues += $(this).attr("name") + "=" + $(this).val() + '&';
    });
    $("#authloader").show();
    $.ajax({
    url: '/seller/authenticationkeys',
            data:'channel=' + accesskey + '&' + namesValues,
            type: 'get',
            dataType: 'json',
            success:function(data){
            if (data.status == 200)
            {
            $("#authloader").hide();
            alert(data.message);            
            $(".goBack").removeClass('disabled');
            $("#keystest").addClass('disabled');
            var datastring = $("#submit_form").serialize();
            $.ajax({
            url: '/seller/savesellerdata',
                    data: datastring,
                    type: 'get',
                    success: function (response) {
                    var mp_referance_name = '';
                    var prePopulate = $.parseJSON(response);
                    $.each(prePopulate, function (index, values) {
                    mp_referance_name += values.mp_referance_name;
                    //$("." + index + "_error").html(values).css('color', '#a94442');
                    if ($("#" + index).val() != '')
                    {
                    $('#tab2').removeClass('active');
                    $('#tab3').addClass('active');
                    $('.progress-bar-success').width('100%');
                    $('[href="#tab3"]').parent('li').addClass('active');
                    }
                    });
                    $(".sellername").html(mp_referance_name);
                    }
            });
            }
            else
            {
            alert(data.message);
            $("#authloader").hide();            
            $(".goBack").removeClass('disabled');
            }
            }

    });
    $("#keystest").removeClass('disabled');
    } else{
    $('#submit_form').data('formValidation').validate();
    $("#keystest").removeClass('disabled');
    }
    });
    var getChannelId = '<div class="col-md-6"><input type="hidden" name="channelId" value="' + channelId + '"></div>'
            $(".input_hidden_wrap").html(getChannelId);
    });
    $.ajax({
    url: '/seller/channelImage',
            data: 'channelId=' + channelId,
            type: 'get',
            success: function (data) {
            var response = $.parseJSON(data);
            var channelName = '';
            $.each(response, function (index, value) {
            //console.log(value);
            var chanenlImage = '<img style="height:75px; width:150px;" src="' + value.mp_logo + '" /><h4 style="text-transform:capitalize;padding-top:15px;">' + value.mp_name + '</h4>';
            channelName = value.mp_name;
            var channel_description = value.mp_description;
            $(".image-center").html(chanenlImage);
            $(".channelDescription").html(channel_description);
            });
            $(".channelname").html(channelName + ' ' + 'Marketplace');
            }
    });
    });
    /* Bootsrap From Validations */
    $("#submit_form").formValidation({
    framework: 'bootstrap',
            button: {
            selector: '#keystest',
                    disabled: 'disabled'
            },
            icon: {
            valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
            channelreferancename: {
            validators: {
            notEmpty: {
            message: 'Channel Reference Name is required'
            }
            }
            },
                    description: {
                    validators: {
                    notEmpty:{
                    message: 'Description filed is required'
                    },
                            regexp: {
                            regexp: /^[a-zA-Z0-9 "!?.-\_\,\\]+$/,
                                    message: 'The full Description can consist of alphabetical characters ,comma,underscore and slash'
                            }
                    }
                    },
                    sellername: {
                    validators: {
                    notEmpty:{
                    message:'Sellername Should not empty'
                    },
                            stringLength: {
                            min:5,
                                    max: 20,
                                    message: 'The seller name minim 5 and maxim 20 characters'
                            }
                    }
                    },
                    marketplaceusername: {
                    validators:{
                    notEmpty:{
                    message:'Marketplaceusername filed is required'
                    },
                            regexp: {
                            regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                                    message: 'The value is not a valid email address'
                            }
                    }
                    },
                    password: {
                    validators: {
                    notEmpty: {
                    message: 'Please enter password'
                    },
                            stringLength: {
                            min: 5,
                                    max: 14,
                                    message: 'The password must be more than 5 and less than 14 characters long'
                            }
                    }
                    },
                    wharehouseId: {
                    validators: {
                    notEmpty: {
                    message : 'Fulfillment Center should not empty'
                    }
                    }
                    },
                    'option[]': {
                    validators: {
                    notEmpty: {
                    message: 'The option required and cannot be empty'
                    },
                            stringLength: {
                            max: 100,
                                    message: 'The option must be less than 100 characters long'
                            }
                    }
                    }
            }

    });
    $(".goBack").click(function () {
        //$(".keystest").prop('disabled', false);
        $("#done").css("visibility", "hidden");
        $("#keystest").removeClass('disabled');
        $('#submit_form').bootstrapValidator('resetForm', true);
        $('#tab2').removeClass('active');
        $('#tab1').addClass('active');
        $('.progress-bar-success').width('0%');
        $('[href="#tab2"]').parent('li').removeClass('active');
        if ($('#tab1').addClass('active'))
        {
            $(".barhide").hide();
            $(".validata").hide();
        }

        /*dynamicFields Validations rest*/
        $.each(dynamicFields, function(i, v){
            if ($("#" + v).val() == '')
            {
             $('#submit_form').find("." + v).removeClass('has-error');
            }
        });
    });
    $('.tab1').click(function() { return false; });
    $('.tab2').click(function() { return false; });
    $('.tab3').click(function() { return false; });
    $('input').keyup(function(){
    var value = $(this).val();
    $("#theList > div").each(function() {
    if ($(this).text().search(new RegExp(value, "i")) > - 1) {
    $(this).show();
    $("#nomarketplace").hide();
    }
    else {
    $(this).hide();
//                $("#nomarketplace").html( '<h4><b>"'+value+'" '+ 'this arketplace not there</b></h4>');
    }
    });
    });

</script>

@stop
@extends('layouts.footer')