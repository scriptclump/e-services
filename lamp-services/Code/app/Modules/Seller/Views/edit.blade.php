@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php //echo "<pre>";print_r($seller_info);echo "</pre>"; ?>
<?php //print_r($seller_info->config_details);?> 
<div class="row">
    <div class="col-md-12">
        <div class="portlet box" id="form_wizard_1">
            <div class="portlet-body form">
                <form action="#" class="submit_form" id="submit_form" method="GET">
                    <input type="hidden" name="legal_entity_id" value="{{ $legal_entity_id }}" />
                    @if(isset($seller_id))
                    <input type="hidden" name="seller_id" id="seller_id" value="{{ $seller_id }}" />
                    @endif
                    <div class="form-wizard">
                        <div class="form-body" style="min-height:612px;">
                            <ul class="nav nav-pills nav-justified steps">
                                <li>
                                    <a href="#tab1" data-toggle="tab" class="step tab1">
                                        <span class="number">
                                            <i class="fa fa-building-o"></i> </span>
                                        <span class="desc">
                                            Seller Mapping</span>
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
                                    <a href="#tab3" data-toggle="tab" class="step tab3 active">
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
                                <div class="alert alert-danger display-none hideerror">
                                    <button class="close" data-dismiss="alert"></button>
                                    You have some form errors. Please check below.
                                </div>
                                <div class="alert alert-danger alert-channel-selection display-none hideerror">
                                    <button class="close" data-dismiss="alert"></button>
                                    Please select Market Place to continue.
                                </div>
                                <div class="alert alert-success display-none">
                                    <button class="close" data-dismiss="alert"></button>
                                    Your form validation is successful!
                                </div>
                                <div class="tab-pane active" id="tab1">                                    
                                    <div class="row">
                                        <div class="col-md-10"><h3 class="">Select Marketplace</h3></div>
                                        <div class="row" id="channel_names">
                                            <div class="col-md-2 pull-right">
                                                <div class="form-group">
                                                    <div class="input-icon right margin-top-10">
                                                        <i class="fa fa-search"></i>
                                                        <input type="text" class="search form-control" placeholder="Search here" />
                                                    </div>
                                                </div>
                                            </div>
                                            <ul class="list" style=" list-style-type: none;">
                                                <?php //echo "<pre>";print_R($channel_info);die; ?>
                                                @foreach($channel_info as $channel_infos)  
                                                <div class="col-sm-6 col-md-2">
                                                    <li>
                                                        <a href="javascript:void(0);" class="thumbnail <?php if (property_exists($seller_info, 'mp_id') && $seller_info->mp_id == $channel_infos->mp_id) { ?> active <?php } ?>"><img  class="mp_logos" src="{{$channel_infos->mp_logo}}" itemid="{{$channel_infos->mp_id}}"  accesskey="{{$channel_infos->mp_key}}" style="height: 136px;"/></a>
                                                        <p class="channel_name" style="margin:-17px 0 10px;margin-left: 40px;">{{$channel_infos->mp_name}}</p>
                                                    </li>
                                                </div>
                                                @endforeach
                                            </ul>   
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top:40px;">
                                        <div class="col-md-12 text-center">
                                            <a href="javascript:void(0);" class="btn blue button-next" id="seller-continue-button">Continue <i class="m-icon-swapright m-icon-white"></i></a>
                                            <!--<a><input type="submit" id="myBtn" value="Continue"  class="btn blue button-next"><i class="m-icon-swapright m-icon-white"></i></a>-->
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab2">                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="note note-info">
                                                <div class="text-center image-center"></div>                                                
                                                <p class="channelDescription">
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Seller Name</label>
                                                        @if(property_exists($seller_info, 'sellername'))
                                                        <input type="text" class="form-control step-1" placeholder="Seller Name" name="sellername" id="sellername" value="{{ $seller_info->sellername }}">
                                                        @else 
                                                        <input type="text" class="form-control step-1" placeholder="Seller Name" name="sellername" id="sellername">
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Select Fulfillment Center</label>
                                                        <select class="form-control step-1" name="wharehouseId" id="wharehouseId">
                                                            <option value="">Please Select..</option>
                                                            @foreach($warehouse_list as $warehouse_lists)
                                                            @if(property_exists($seller_info, 'warehouse_id') && $seller_info->warehouse_id == $warehouse_lists->lp_wh_id)

                                                            <option value="{{$warehouse_lists->lp_wh_id}}" selected="true">
                                                                @if(!empty($warehouse_lists->lp_wh_name))
                                                                {{$warehouse_lists->lp_wh_name}}
                                                                @endif
                                                            </option>														
                                                            @else
                                                            @if(!empty($warehouse_lists->lp_wh_name))
                                                            <option value="{{$warehouse_lists->lp_wh_id}}">{{$warehouse_lists->lp_wh_name}}
                                                                @endif
                                                            </option>

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
                                                        <label class="control-label">Channel Reference Name</label>
                                                        @if(property_exists($seller_info, 'mp_referance_name'))
                                                        <input type="text" class="form-control" placeholder="Channel Referance Name" name="channelreferancename" value="{{ $seller_info->mp_referance_name }}" id="channel_referance_name">
                                                        @else 
                                                        <input type="text" class="form-control" placeholder="Channel Referance Name" name="channelreferancename" value="" id="channel_referance_name">
                                                        @endif
                                                        <span class="channel_referance_name_error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Description</label>
                                                        @if(property_exists($seller_info, 'description'))
                                                        <input type="text" class="form-control" placeholder="Description" name="description" id="description" value="{{ $seller_info->description }}">
                                                        @else
                                                        <input type="text" class="form-control" placeholder="Description" name="description" id="description">
                                                        @endif
                                                        <span class="description_error"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Market Place User Name</label>
                                                        @if(property_exists($seller_info, 'market_place_user_name'))
                                                        <input type="text" class="form-control" placeholder="Market Place User Name" name="marketplaceusername" id="market_place_username" value="{{ $seller_info->market_place_user_name }}">
                                                        @else
                                                        <input type="text" class="form-control" placeholder="Market Place User Name" name="marketplaceusername" id="market_place_username">
                                                        @endif
                                                        <span class="market_place_username_error"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Market Place Password</label>
                                                        @if(property_exists($seller_info, 'market_place_password'))
                                                        <input type="password" class="form-control" placeholder="Market Place Password" name="password" value="{{ $seller_info->market_place_password }}" id="marketplace_password">
                                                        @else
                                                        <input type="password" class="form-control" placeholder="Market Place Password" name="password" value="" id="marketplace_password">
                                                        @endif
                                                        <span class="marketplace_password_error"></span>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="input_fields_wrap"></div>
                                                <div id="dynmickeys"></div>

                                            </div>
                                            <div class="input_hidden_wrap"></div>   
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top:40px;">
                                        <div class="col-md-12 text-center">
<!--                                            <a href="javascript:void(0);" class="btn blue goBack" ><i class="m-icon-swapleft m-icon-white"></i> Back </a>-->
                                        <!--<input type="submit" value="Done" class="btn blue button-next submit" /> -->
                                            <input type="button" class="btn green-meadow keystest" value="Test & Update" id="keystest" />
                                            <span class="loader" id="authloader" style="display:none"><img src="/img/ajax-loader.gif" style="width:25px" class="" /></span>
                                            <i class="m-icon-swapright m-icon-white"></i>

                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane" id="tab3">

                                    <div class="row">
                                        <div class="col-md-12">

                                            <h3 class="block" style="text-align:center;">You have succesfully registerd <span class="font-green sellername"></span> as a seller for <span class="font-green channelname"></span>.Now you can see all the orders and process them.</h3><h3 class="update"></h3>

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
@stop
@include('includes.validators')
@section('userscript')
<script src="{{ URL::asset('/assets/global/plugins/list.min.js') }}"></script> 
<script src="{{ URL::asset('assets/admin/pages/scripts/addseller-form-wizard.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    var configDetails = '<?php print_r(json_encode($seller_info->config_details)); ?>';
    $(function () {
    var sellerId = $('#seller_id').val();
    if (sellerId > 0)
    {
    $('a.thumbnail.active img').trigger('click');
    $('#seller-continue-button').trigger('click');
    }
    //$('.progress-bar-success').width('33.33%');
    var options = {
    valueNames: ['channel_name']
    };
    var userList = new List('channel_names', options);
    });
    var cahnnel_Id = '';
    $('#seller-continue-button').click(function(){
    if ($('a.thumbnail.active').length == 1)
    {
    $('.progress-bar-success').width('50%');
    $('.alert.alert-channel-selection').hide();
    $('li.active').removeClass('active');
    $('[href="#tab1"]').parent('li').addClass('active');
    $('[href="#tab2"]').parent('li').addClass('active');
    $('#tab1').removeClass('active');
    $('#tab2').addClass('active');
    } else{
    $('.alert.alert-channel-selection').show();
    }
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
    if (configDetails != '[]')
    {
    var i = 0;
    var data = $.parseJSON(configDetails)
            $.each(data, function(index, values){            
            var fieldName = index.split("_").join(" ");
            channelKeys += '<div class="col-md-6' + " " + index + '"><label class="control-label">' + fieldName + '</label><input type="text" class="form-control dynamic" id="'+ index +'" accesskey="' + response[i].field_code + '"  value="' + values + '" name="' + index + '" placeholder="' + fieldName + '" required="true" style="width: 100%;" \n\
    id="" ></div>';
            //alert(values.field_code);            
            dynamicFields.push(index);
            i++;
            });
            
    $.each(dynamicFields, function(i, v){                
    $('#submit_form').formValidation('addField', v);
    $("#" + v).keyup(function(){
    if ($("#" + v).val() == ''){
    $('#submit_form').find("." + v).addClass('has-error');
    } else{
    $('#submit_form').find("." + v).removeClass('has-error');
    $('#submit_form').find("." + v).addClass('has-success');
    $('#keystest').prop('disabled', false);
    }
    });
    });
    $(".keystest").on("click", function(){   
    if ($('#submit_form').data('formValidation').isValid())
    {
    $(".goBack").addClass('disabled');
    var namesValues = '';
    $(".dynamic").each(function(){
    namesValues += $(this).attr("accesskey") + "=" + $(this).val() + '&';
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
            var datastring = $("#submit_form").serialize();
            $.ajax({
            url: '/seller/update',
                    data: datastring,
                    type: 'get',
                    success: function (response) {
                    var mp_referance_name = '';
                    var prePopulate = $.parseJSON(response);
                    //console.log(prePopulate[0].mp_referance_name);
                    $.each(prePopulate, function (index, values) {
                    mp_referance_name += values.mp_referance_name;
                    $("." + index + "_error").html(values).css('color', '#a94442');
                    $("#" + index).keyup(function () {
                    if ($("#" + index).val() != '')
                    {
                    $("." + index + "_error").html('');
                    }
                    else
                    {
                    $("." + index + "_error").html(values).css('color', '#a94442');
                    }

                    });
                    });
                    if (prePopulate[0].mp_referance_name != '')
                    {
                    $(".sellername").html(prePopulate[0].mp_referance_name);
                    $('.progress-bar-success').width('100%');
                    $('#tab2').removeClass('active');
                    $('#tab3').addClass('active');
                    $('[href="#tab3"]').parent('li').addClass('active');
                    $(".block").hide();
                    $(".update").html('Updated Succesfully');
                    window.location = "/seller/index";
                    }
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
    }
    $(wrapper).html(channelKeys);
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
            var chanenlImage = '<img style="height:75px; width:150px;" src="' + value.mp_logo + '" /><h4 style="text-transform:capitalize;">' + value.mp_name + '</h4>';
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
            message: 'Channel Referance Name is required'
            }
            }
            },
                    description: {
                    validators: {
                    notEmpty:{
                    message: 'Description filed is required'
                    }
                    }
                    },
                    sellername: {
                    validators: {
                    notEmpty:{
                    message:'Sellername Should not empty'
                    }
                    }
                    },
                    marketplaceusername: {
                    validators:{
                    notEmpty:{
                    message:'Marketplaceusername filed is required'
                    }
                    }
                    },
                    password: {
                    validators: {
                    notEmpty: {
                    message: 'Please enter password'
                    }
                    },
                            stringLength: {
                            min: 5,
                                    max: 14,
                                    message: 'The password must be more than 4 and less than 14 characters long'
                            }
                    },
                    wharehouseId: {
                    validators: {
                    notEmpty: {
                    message : 'Fulfillment Center should not empty'
                    }
                    }
                    },
            }  });
//$('.help-block.help-block-error').is(":visible")
    $(':input[required=""],:input[required]').keyup(function(){
    $(this).parent('div .col-md-6').removeClass('has-error');
    });
    $('.tab1').click(function() { return false; });
    $('.tab2').click(function() { return false; });
    $('.tab3').click(function() { return false; });
    
</script>

@stop
@extends('layouts.footer')