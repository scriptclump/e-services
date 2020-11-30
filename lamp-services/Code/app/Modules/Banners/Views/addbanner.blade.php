@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="alert alert-info hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> {{$addoredit}} </div><span style="margin: 13px 10px 5px 6px;position: absolute;">
                
            </div>            
                <div class="portlet-body">
                                                                   
                                    <form action="/banners/addbanners" class="submit_form" id="submit_form" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" id="csrf_token" name="_token" value="{{ Session::token() }}">
                                        <input type="hidden" id="banner_id" name="banner_id" value="@if(isset($editdata[0]['banner_id']) && $editdata[0]['banner_id']!='') {{ $editdata[0]['banner_id'] }} @elseif(isset($editdata[0]['sponsor_id']) && $editdata[0]['sponsor_id']!=''){{ $editdata[0]['sponsor_id'] }}@endif">
                                        <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.warehouse_label')}} <span class="required">*</span></label>
                                                <select name="warehouse_id[]" id="warehouse_id" autocomplete="off" class="form-control select2me" {{ isset($editdata[0]['le_wh_id'])?'':'multiple'}}>
                                                	<option value="0">All</option>
                                                	@foreach($dcs as $dc)
                                                	@if($dc->lp_wh_name!='')
                                                	<option value="{{ $dc->le_wh_id}}" @if(isset($editdata[0]['le_wh_id']) && $editdata[0]['le_wh_id']==$dc->le_wh_id) selected @endif> {{ $dc->lp_wh_name }}</option>
                                                	@endif
                                                	@endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.hub_label')}} <span class="required">*</span></label>
                                                <select name="hub_id[]" id="hub_id" autocomplete="off" class="form-control select2me" {{ isset($editdata[0]['hub_id'])?'':'multiple'}}>
                                                	<option value="0">All</option>
                                                	
                                                </select>
                                            </div>
                                            <div id="selectdchub" style="color:red"></div>
                                        </div>
                                        </div>
                                        <input type="hidden" id="hidden_hubid" name="hidden_hubid" value="{{ isset($editdata[0]['hub_id'])?$editdata[0]['hub_id']:''}}">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.beat_label')}} <span class="required">*</span></label>
                                                <select name="beat_id[]" id="beat_id" class="form-control select2me" {{ isset($editdata[0]['beat_id'])?'':'multiple'}}>
                                                    <option value="0">All</option>
                                                    
                                                </select>
                                            </div>
                                            <div id="selecthubbeat" style="color:red"></div>
                                        </div>
                                         <input type="hidden" name="hdn_beatid" id="hdn_beatid" value="{{ isset($editdata[0]['beat_id'])?$editdata[0]['beat_id']:''}}">
                                         <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.type')}} <span class="required">*</span></label>
                                                <select class="form-control select2me" id="type" name="type" autocomplete="off" @if(isset($editdata[0]['display_type']) && $editdata[0]['display_type']!=''){{'disabled'}}@endif>
                                                    <option value=''>Select</option>
                                                    @foreach($type as $type)
                                                    <option value="{{ $type->value}}" @if(isset($editdata[0]['display_type']) && $editdata[0]['display_type']==$type->value) selected @endif> {{ $type->master_lookup_name }}</option>
                                                    @endforeach
                                                </select>
                                                @if(isset($editdata[0]['display_type']) && $editdata[0]['display_type']!='')
                                                <input type="hidden" name="type" value="{{isset($editdata[0]['display_type'])?$editdata[0]['display_type']:''}}" />
                                                @endif
                                            </div>
                                        </div>
                                         </div>
                                         <input type="hidden" name="hdn_type" id="hdn_type" value="@if(isset($editdata[0]['display_type']) && $editdata[0]['display_type']!='') {{ $editdata[0]['display_type'] }} @else @endif">   
                                        <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.banner_label')}} <span class="required">*</span></label>
                                                 <input type="text" class="form-control" id="bannername" name="bannername" autocomplete="off" value="@if(isset($editdata[0]['banner_name']) && $editdata[0]['banner_name']!=''){{$editdata[0]['banner_name']}}@elseif(isset($editdata[0]['sponsor_name']) && $editdata[0]['sponsor_name']!=''){{$editdata[0]['sponsor_name']}}@else @endif"/>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6" id="hide_img_fld">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.image_label')}} <span class="required">*</span></label>
                                                <input type="file" class="form-control" id="bannerimage" name="bannerimage" value="" accept="image/*"/>
                                            </div>
                                        </div>
                                          </div>

                                          <?php
                                             if(isset($editdata[0]['banner_url']) && $editdata[0]['banner_url']!=''){
                                          ?>
                                          <div class="row">
                                              <div class="col-md-6"></div>
                                              
                                                <?php 
                                                  $url = public_path();
                                                $img='';
                                                if ($editdata[0]['banner_url'] != '') {
                                                     $img = $editdata[0]['banner_url'];
                                                     }
                                                ?>
                                                <div class="col-md-6">
                                                   <a><img class="timeline-badge-userpic" src="{{$img}}" height="300px" width="450px" id="updatedurl" data-toggle="modal" data-target="#imagebnr"></a>
                                              </div>
                                              
                                          </div><br/>
                                          <div id="imagebnr" class="modal fade" role="dialog">
                                          <div class="modal-dialog">

                                            <!-- Modal content-->
                                            <div class="modal-content" style="height:550px;width:800px">
                                              <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                
                                              </div>
                                              <div class="modal-body">
                                                <img class="timeline-badge-userpic" id="updatedurl" src="{{$img}}" height="400px" width="750px">
                                              </div>
                                              <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                              </div>
                                            </div>

                                          </div>
                                        </div>
                                          <?php
                                            }
                                          ?>
                                          <input type="hidden" name="bannerurl_edited" id="bannerurl_edited" value={{ !empty($editdata[0]['banner_url'])?$editdata[0]['banner_url']:''}}>
                                        <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.banner_type')}} <span class="required">*</span></label>
                                                <select class="form-control select2me" id="banner_type" name="banner_type">
                                                	<option value="">Select</option>
                                                    @foreach($bnrtype as $banneritem)
                                                     <option value="{{ $banneritem->value}}" @if(isset($editdata[0]['navigator_objects']) && $editdata[0]['navigator_objects']==$banneritem->value) selected @endif>{{ $banneritem->master_lookup_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.item_list')}} <span class="required">*</span></label>
                                                <select class="form-control select2me" id="banner_list" name="banner_list">
                                                	<option value=''>Select</option>
                                                      
                                                </select>
                                            </div>
                                            <input type="hidden" id="hidden_item_list" value="{{isset($editdata[0]['navigate_object_id'])?$editdata[0]['navigate_object_id']:''}}">
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.impression_cost')}} <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="impression_cost" name="impression_cost" value="{{ isset($editdata[0]['impression_cost'])?$editdata[0]['impression_cost']:''}}" autocomplete="off"/>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.click_cost')}} <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="click_cost" name="click_cost" value="{{ isset($editdata[0]['click_cost'])?$editdata[0]['click_cost']:''}}" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                        <div class="row">
                                        <div id="banner_frequencyhide">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.banner_frequency')}} <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="banner_frequency" name="banner_frequency" value="{{ isset($editdata[0]['frequency'])?$editdata[0]['frequency']:''}}" autocomplete="off" />
                                            </div>
                                        </div>
                                         </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.from_date')}} <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="from_date" name="from_date" autocomplete="off" value="{{ isset($editdata[0]['from_date'])?$editdata[0]['from_date']:''}}" />
                                            </div>
                                        </div>
                                         </div>

                                         <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.to_date')}} <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="to_date" name="to_date" autocomplete="off" value="{{ isset($editdata[0]['to_date'])?$editdata[0]['to_date']:''}}" />
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.banner_sts')}} <span class="required">*</span></label>
                                                <select class="form-control" id="status" name="status">
                                                    <option value=''>Select</option>
                                                    <option value='1' @if(isset($editdata[0]['is_active']) && $editdata[0]['is_active']==1 && ($editdata[0]['display_type']==16601 || $editdata[0]['display_type']==16602 )) selected @elseif(isset($editdata[0]['status']) && $editdata[0]['status']==1 && $editdata[0]['display_type']==16603) selected @endif>Active</option>
                                                    <option value='0' @if(isset($editdata[0]['is_active']) && $editdata[0]['is_active']==0 && ($editdata[0]['display_type']==16601 || $editdata[0]['display_type']==16602)) selected @elseif(isset($editdata[0]['status']) && $editdata[0]['status']==0 && $editdata[0]['display_type']==16603) selected @endif>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                       <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.sort_order')}} <span class="required">*</span></label>
                                                <select class="form-control" id="sort_order" name="sort_order">
                                                	<option value=''>Select</option>
                                                	@for($i=1;$i<=100;$i++)
                                                	<option value="{{ $i }}" @if(isset($editdata[0]['sort_order']) && $editdata[0]['sort_order']==$i) selected @endif>{{ $i }}</option>
                                                	@endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="sponsored">
                                            <div class="form-group">
                                                <label class="control-label">&nbsp;</label>
                                                <input type="checkbox" id="is_sponsor" name="is_sponsor" value=1 @if(isset($editdata[0]['status']) && $editdata[0]['status']==1) checked @endif>&nbsp;&nbsp;&nbsp;&nbsp;Is Sponsor
                                            </div>
                                        </div>
                                        </div>
                                        <div class="row" style="margin-top:50px;">
                                        <hr />
                                        <div class="col-md-12 text-center"> 
                                            <input type="submit" class="btn green-meadow savebanners" value="Save" id="savebanners"/> 
                                            
                                        </div>
                                    </div>
                                    </form>
                </div>
            
        </div>
    </div>
</div>
@stop
@section('script')
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    #redeemForm .has-error .control-label,
    #redeemForm .has-error .help-block,
    #redeemForm .has-error .form-control-feedback {
        color: #f39c12;
    }

    #redeemForm .has-success .control-label,
    #redeemForm .has-success .help-block,
    #redeemForm .has-success .form-control-feedback {
        color: #18bc9c;
    }
    .rightAlign{
        text-align: right;
    }
</style>
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/components-select2.min.js') }}" type="text/javascript"></script>
@include('includes.ignite')
@include('includes.validators') 
@include('includes.jqx')
<script>
/* Bootsrap From Validations */
$("#submit_form").bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        'warehouse_id[]' : {
            validators: {
                notEmpty: {
                    message: "{{trans('banners.banner_form_validate.warehouse_name')}}"
                },
            }
        },
        'hub_id[]': {
            validators: {
                notEmpty: {
                    message: "{{trans('banners.banner_form_validate.hub_name')}}"
                },  
            }
        },
        
        /*'beat_id[]': {
            validators: {
                notEmpty: {
                    message: "{{trans('banners.banner_form_validate.beat_name')}}"
                },  
            }
        },*/
        banner_type: {
            validators: {
                notEmpty: {
                    message: "{{trans('banners.banner_form_validate.banner_type')}}"
                },  
            }
        },

        bannerimage: {
            validators: {
                    file: {
                        extension: 'jpeg,png,jpg',
                        type: 'image/jpeg,image/png,image/jpg',
                        maxSize: 2048 * 1024,
                        message: 'The selected file is not valid'
                    }
                }
        },

        banner_list: {
            validators: {
                notEmpty: {
                    message: "{{trans('banners.banner_form_validate.banner_list')}}"
                },  
            }
        },

        impression_cost: {
            validators: {
                notEmpty: {
                    message: "{{trans('banners.banner_form_validate.impression_cost')}}"
                }, 
                 regexp: {
                    regexp: /^\d+(\.\d{1,5})?$/i,
                    message: "{{trans('banners.banner_form_validate.impression_decimal')}}"
                }, 
            }
        },

        type: {
            validators: {
                notEmpty: {
                    message: "{{trans('banners.banner_form_validate.type')}}"
                },
            }
        },

        bannername:{
             validators:{
                 notEmpty:{
                    message:"{{trans('banners.banner_form_validate.banner_name')}}"
                 },
                  regexp: {
                    regexp: /^[a-z0-9\s]+$/i,
                    message: "{{trans('banners.banner_form_validate.banner_name_string')}}"
                },
                stringLength: {
                    min: 4,
                    max: 50,
                    message: "{{trans('banners.banner_form_validate.banner_name_length')}}"
                }
             }
        },

        click_cost:{
            validators:{
                notEmpty:{
                   message:"{{trans('banners.banner_form_validate.click_cost')}}" 
                },
                 regexp: {
                    regexp: /^\d+(\.\d{1,5})?$/i,
                    message: "{{trans('banners.banner_form_validate.clickcostdecimal')}}"
                }
            }
        },
       
       banner_frequency:{
            validators:{
                notEmpty:{
                   message:"{{trans('banners.banner_form_validate.banner_frequency')}}" 
                },
                regexp: {
                    regexp: /^\d+(\.\d{1,5})?$/i,
                    message: "{{trans('banners.banner_form_validate.frequencydecimal')}}"
                },
            }
        },

        from_date:{
            validators:{
                notEmpty:{
                   message:"{{trans('banners.banner_form_validate.from_date')}}" 
                },
            }
        },

         to_date:{
            validators:{
                notEmpty:{
                   message:"{{trans('banners.banner_form_validate.to_date')}}" 
                },
            }
        },

        status:{
            validators:{
                notEmpty:{
                   message:"{{trans('banners.banner_form_validate.status')}}" 
                },
            }
        },

        sort_order:{
            validators:{
                notEmpty:{
                   message:"{{trans('banners.banner_form_validate.sort_order')}}" 
                },
            }
        },
        
    }});

$("#warehouse_id").change(function() {
        
    var token = $('#csrf-token').val();
    var warehouse_id = $('#warehouse_id').val();
    var hdnhubid= $('#hidden_hubid').val();
    if(warehouse_id!='' && warehouse_id!=0){
        $.ajax({
               headers: {'X-CSRF-TOKEN': token},
                url:"/banners/gethubs",
                type:"POST",
                data: 'warehouseid='+warehouse_id+'&hdnhubid='+hdnhubid,
                success:function(data){
                
                $("#hub_id").html(data);
                $("#hub_id").select2("val", hdnhubid);
                $("select#hub_id").change();

                }
        });
    }else{
        $('#hub_id').empty();
        $('#hub_id').select2().append('<option value="0">All</option>').trigger('change');
    }
    
    });


$("#hub_id").change(function() {
        
    var token = $('#csrf-token').val();
    var warehouseid=$('#warehouse_id').val();

    if($('#hub_id').val()!='' && $('#hub_id').val()!=null  && $('#hub_id').val()!=0){
    var hub_id = $('#hub_id').val();
    }else if( $('#hidden_hubid').val()!=''){
    var hub_id= $('#hidden_hubid').val();
    }
    var hdnbeatid=$('#hdn_beatid').val();
    if(hub_id!='' && hub_id!=0){
        $.ajax({
               headers: {'X-CSRF-TOKEN': token},
                url:"/banners/getbeats",
                type:"POST",
                data: 'hubid='+hub_id+'&hdnbeatid='+hdnbeatid+'&warehouseid='+warehouseid,
                success:function(data){

                 if(data!=0){   
                
                $("#beat_id").html(data);     
                $("#beat_id").select2("val", hdnbeatid);
                 $('#submit_form').formValidation('revalidateField', 'hub_id');
                 $("#selectdchub").empty();
                 $('#savebanners').prop('disabled', false);
             }else{
                  if(hub_id!=0 && data!=0){
                 $("#selectdchub").text("Please Select Atleast One Hub Related to DC");
                 $('#savebanners').prop('disabled', true);
             }else{
                    $('#beat_id').empty();
                    $('#beat_id').select2().append('<option value="0">All</option>').trigger('change');
                }
             }
                }
        });
    }else{
        $('#beat_id').empty();
        $('#beat_id').select2().append('<option value="0">All</option>').trigger('change');
    }
    
    });

$('#beat_id').change(function() {
        
    var token = $('#csrf-token').val();

    var hub_id = $('#hub_id').val();
    var beatid=  $('#beat_id').val();
    
    if(beatid!='' && beatid!=0 && beatid!=null){
        $.ajax({
               headers: {'X-CSRF-TOKEN': token},
                url:"/banners/hubbeatsmap",
                type:"POST",
                data: 'hubid='+hub_id+'&beatid='+beatid,
                success:function(data){

                 //if(data!=0){   
                 $("#selecthubbeat").empty();
                 $('#savebanners').prop('disabled', false);
             /*}else{
                
                 $("#selecthubbeat").text("Please Select Atleast One Beat Related to Hub");
                 $('#savebanners').prop('disabled', true);
             }*/
                }
        });
    }
    
    });


$("#banner_type").change(function() {
        
    var token = $('#csrf-token').val();
    var bannertype = $('#banner_type').val();
    var listitem=$('#hidden_item_list').val();
    if(bannertype!=''){
        $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/banners/bannerType",
                type:"POST",
                data: 'bannertype='+bannertype+'&listitem='+listitem,
                success:function(data){
                $("#banner_list").html(data);     
                $("#banner_list").select2("val", listitem);
                }
        });
    }
    
    });
$('#from_date').datepicker({
         dateFormat: 'yy-mm-dd',
         minDate: 0,
            onSelect: function() {
                var select_date = $(this).datepicker('getDate');
                $('#to_date').datepicker('setDate', null);
                $('#submit_form').bootstrapValidator('revalidateField', 'from_date');
                var selectedDate = new Date(select_date);
                var msecsInADay = 0;
                var endDate = new Date(selectedDate.getTime() + msecsInADay);

       //Set Minimum Date of EndDatePicker After Selected Date of StartDatePicker
        $("#to_date").datepicker( "option", "minDate", endDate );
            }
        });
$('#to_date').datepicker({
    dateFormat: 'yy-mm-dd',
    minDate: 0,
     onSelect: function() {
                $('#submit_form').bootstrapValidator('revalidateField', 'to_date');
            }
});
$(function() { 
    var bannertype = $('#banner_type').val();

    if(bannertype!=''){
    $("select#banner_type").change();
    }

    var hdnhubid= $('#hidden_hubid').val();

    if(hdnhubid!=''){
    $("select#warehouse_id").change();
    }

     var hdnbeatid=$('#hdn_beatid').val();

     if(hdnbeatid!=''){
     $("select#hub_id").change();

     }
});
$(document).ready(function(){

    $('#type').on('change',function(){
        if(this.value==16601 || this.value==16602){
         $('#hide_img_fld').show();
         $('#sponsored').show();
         $('#banner_frequencyhide').show();
        }else{
         $('#hide_img_fld').hide();
         $('#sponsored').hide();
         $('#banner_frequencyhide').hide();
        }
    });

    if($('#hdn_type').val()==16603){
        $('#hide_img_fld').hide();
        $('#sponsored').hide();
        //$('#type').attr('readonly', false);
    }
})
$(function() {
     $("#bannerimage").change(function (){
        var token = $('#csrf-token').val();
        var bnrtype=$('#type').val();
        var editedurl=$('#bannerurl_edited').val();
        var bannerid=$('#banner_id').val();
       var bannerimg = document.getElementById("bannerimage");
       bannerimg = bannerimg.files[0];
       formData= new FormData();
       formData.append("bannerimg", bannerimg);
        formData.append("bannerid", $("#banner_id").val());
       if((bnrtype==16601 || bnrtype==16602) && bannerid!='' && bannerimg!=''){
       $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/banners/imageupload",
                type:"POST",
                /*data: {
                       'bannerimg':bannerimg,
                       'bannerid':bannerid,
                       },*/
                data: formData,
                 processData: false,
                 contentType: false,            
                success:function(data){
                if(data!=''){
                $("#updatedurl").attr("src", data);
                $("#bannerurl_edited").val(data);
                }else{
                 alert('failed to update image');
                }

                }
        });
         }
     });
  });

$("#status,#warehouse_id").change(function (){

    var sts=$("#status").val();
    var dcs=$("#warehouse_id").val();
    var type=$("#type").val(); 
    var token = $('#csrf-token').val();

    if(type==16602 && sts==1 && (dcs!='' || dcs!=null)){
     $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/banners/checkpopupsts",
                type:"POST",
                data: 'sts='+sts+'&dcs='+dcs+'&type='+type,
                success:function(data){
                  
                  if(data==1){
                    alert("Please In-active existing Pop-up");
                    $('#savebanners').prop('disabled', true);
                  }else{
                    $('#savebanners').prop('disabled', false);
                  }
                }
        });   
    }else{
        
        $('#savebanners').prop('disabled', false);
    }
});

$("#type").change(function (){

   
    var type=$("#type").val(); 
    var token = $('#csrf-token').val();

     $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/banners/getitemsbytype",
                type:"POST",
                data: 'type='+type,
                success:function(data){
                 $("#banner_type").empty(); 
                 $("#banner_type").html(data);
                }
        });   
    
});
</script>
<script type="text/javascript">
  
</script>
@stop
