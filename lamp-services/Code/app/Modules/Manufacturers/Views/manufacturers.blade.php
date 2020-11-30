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
                <div class="caption"> <?php if (!empty(session('legalentity_id'))) {
    echo "Edit Brand";
} else {
    echo"Create Brand";
} ?></div>
                <div class="tools"> <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span> </div>
            </div>
            <div class="portlet-body" style="min-height:552px;">
                <form id="add_brand_form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                    <div class="row">
                        <div class="col-md-6"><div class="form-group">
                                <label class="control-label">Manufacturer Name <span class="required" aria-required="true">*</span>

                                    <span>
                                        <a class="text-links" data-toggle="modal" id="addmanu" href="#add_manu">( Add </a>|
                                        <a class="text-links" data-toggle="modal" id="editmanu" href="#add_manu"> Edit )</a>
                                    </span>
                                </label>
                                <input type="hidden" name="getManfId" id="getManfId" value="@if(isset($brand->mfg_id)){{$brand->mfg_id}}@endif">

                                <select name="manufacturer_name" id="manufacturer_name" class="form-control select2me">
                                    <option value="">-- Select a Manufacturer --</option>
                                </select>
                            </div></div>

                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group">
                                <label class="control-label">Parent Brand Name </label>
                                <input type="hidden" name="getBrandId" id="getBrandId" 
                                       value="@if(isset($brand->parent_brand_id)){{$brand->parent_brand_id}}@endif">
                                <select class="form-control select2me" id="brand_id" name="brand_id">
                                </select>
                                <span id="showLoader" style="display:none;position: relative;left: -5%;top: -27px;">
                                    <img src="../img/ajax-loader2.gif">
                                </span>

                            </div></div>

                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group">
                                <label class="control-label">Brand Name <span class="required" aria-required="true">*</span></label>
                                <input type="text" class="form-control" id="brand_name" value="@if(isset($brand->brand_name)){{$brand->brand_name}}@endif" name="brand_name">
                            </div></div>
                    </div>




                    <div class="row">
                        <div class="col-md-6"><div class="form-group">
                                <label class="control-label">Description <span class="required" aria-required="true">*</span></label>
                                <textarea class="form-control" id="brand_desc" name="brand_desc">@if(isset($brand->description)){{$brand->description}}@endif</textarea>

                            </div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group">
                                <label class="control-label">Upload Logo <span class="required" aria-required="true">*</span></label>
                                <div class="row">
                                    <div class="col-md-12">  

                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <span class="btn default btn-file btn green-meadow" style="width:110px !important;">

                                                <span class="fileinput-new">Choose File </span>
                                                <span class="fileinput-exists" style="margin-top:-9px !important;">Change File</span>


                                            </span>
                                            <?php
                                            if (isset($brand) && $brand->logo_url != '') {
                                                if (strstr($brand->logo_url, 'http')) {
                                                    $image = $brand->logo_url;
                                                    ?> 
                                                    <img name="brand_logo_name123"  id="brand_logo_name123" src="{{$image}}" class="org_edit_file" alt=""  hidden=""/>

    <?php
    } else {
        $image = '/uploads/brand_logos/' . $brand->logo_url;
        ?>

                                                    <img name="brand_logo_name123"  id="brand_logo_name123" src="{{$image}}" class="org_edit_file" alt=""  hidden=""/>

    <?php
    }
} else {
    $image = '/uploads/brand_logos/notfound.png';
}
?>


                                            <div class="fileinput-preview fileinput-exists thumbnail " style="width: 100px; height: 33px; margin-left:35px;">  
                                                <img name="brand_logo_name"  src="{{$image}}" class="org_edit_file" alt="" />
                                            </div>


                                            <br />
                                            <input id="brandLogo" type="file"  name="brand_logo" style="margin-top: -27px !important;  position: absolute;opacity: 0; width:106px;"/>




                                            <div class="fileinput-new thumbnail" style="width: 100px; height: 33px; display:none;  ">
                                                <img src="{{$image}}" alt="" id="org_supplier_logo"/>
                                            </div>
                                            <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>

                                        </div>


                                    </div>
                                </div>
                            </div></div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">			
                            &nbsp;
                        </div>
                    </div>

                    <div class="row">




                        <div class="col-md-12 text-center">	
                            <div>
                                <span id="loader1" style="display:none;"><img src="/img/spinner.gif" style="width:250px; padding-left:20px;" /></span>
                            </div><br/>
                            <button type="submit" class="btn green-meadow btnn supp_info" id="supp_info">Save</button>
                            <button type="button" id="cancelmaninfo" class="btn green-meadow">Cancel</button>
                        </div>
                    </div>
                </form>


                <div><img  src ='/assets/admin/layout4/img/ajax-loading.gif' id='ajaxloader' style='display:none;margin-left: 480px;'></div>


                <div class="modal fade modal-scroll in" id="add_manu" tabindex="-1" role="addl" aria-hidden="true" > 
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content" style="margin-top:90px;">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">Add Manufacturer</h4>
                            </div>
                            <form action="" method="post" id="add_manu_form">
                                <input type="hidden" class="form-control" name="edit_form_product_id" id="edit_form_product_id">
                                <input type="hidden" class="form-control" name="manu_id" id="manu_id">
                                <div class="modal-body">


                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">Manufacturer Name <span class="required" aria-required="true">*</span></label>
                                                <input type="text" class="form-control" name="manu_org_name" id="manu_org_name">
                                                <input type="hidden" class="form-control" name="manu_id" id="manu_id">

                                            </div>




                                            <div class="row">
                                                <div class="col-md-12"><div class="form-group">
                                                        <label class="control-label">Organization Type <span class="required" aria-required="true">*</span></label>
                                                        <select class="form-control" id="manu_org_type" name="manu_org_type">
                                                            <option value="">Select Organization Type</option>    
                                                            @foreach($manu_org_type as $companyVal )
                                                            <option value="{{$companyVal->id}}" >{{$companyVal->company_type}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12"><div class="form-group">
                                                        <label class="control-label">Manufacturer Rankings <span class="required" aria-required="true">*</span></label>
                                                        <select class="form-control" id="manu_segment" name="manu_segment">
                                                            <option value="">Select Manufacturer Reach</option> 
                                                            @if(isset($manufacturer_segments))
                                                            @foreach($manufacturer_segments as $Val )
                                                            <option value="{{$Val->value}}" >{{$Val->segement_name}}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                    </div></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12"><div class="form-group">
                                                        <label class="control-label">Purchase Manager<span class="required" aria-required="true">*</span></label>
                                                        <select class="form-control" id="pur_mgr" name="pur_mgr">
                                                            <option value="">Select Purchase Manager</option> 
                                                            @if(isset($purchase_manager))
                                                            @foreach($purchase_manager as $Val )
                                                            <option value="{{$Val->id}}" >{{$Val->username}}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                    </div></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12"><div class="form-group">
                                                        <label class="control-label">Upload Logo <span class="required" aria-required="true">*</span></label>

                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                            <span class="btn default btn-file btn green-meadow" style="width:110px !important;">

                                                                <span class="fileinput-new">Choose File </span>
                                                                <span class="fileinput-exists" style="margin-top:-9px !important;">Change File</span>


                                                            </span>                                                              
                                                            <?php
                                                            if (isset($manu_logo) && $manu_logo != '') {                                                                
                                                                if (strstr($manu_logo, 'http')) {
                                                                    $manuimage = $manu_logo;
                                                                } else {
                                                                    $manuimage = '/uploads/manufacturer_logos/' . $manu_logo;
                                                                }
                                                            } else {
                                                                //echo "empty images";
                                                                $manuimage = '/uploads/manufacturer_logos/notfound.png';
                                                            }
                                                            ?>
                                                            <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                                                            <div id="manu_image_view" class="fileinput-preview fileinput-exists thumbnail " style="width: 100px; height: 33px; margin-left:9px;">  
                                                                <img name="manu_logo_name" id="manu_logo_name" src="{{$manuimage}}" class="org_edit_file" alt="" />
                                                            </div>


                                                            <br />
                                                            <input id="manuLogo" type="file"  name="manu_logo" style="margin-top: -27px !important;  position: absolute;opacity: 0; width:103px;"/>
                                                            <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                                                            <div class="fileinput-new thumbnail" style="width: 100px; height: 33px; display:none;  ">
                                                                <img src="{{$manuimage}}" alt="" id="manuf_logo"/>
                                                            </div>
                                                        </div>
                                                    </div></div>
                                            </div>
                                        </div>

                                    </div>     





                                    <div>
                                        <span id="loader" style="display:none; font-size: 12px;"><img src="/img/spinner.gif" style="width:250px; padding-left:20px;" /></span>
                                    </div> 
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-12 text-center">

                                                <button type="submit" class="btn green-meadow">Save</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </form>

                        </div>
                        <!-- /.modal-content --> 

                    </div>
                    <!-- /.modal-dialog --> 

                </div>



            </div>
        </div>
    </div>
</div>
<input type="hidden" id="csrf-token" name="_Token" value="{{ csrf_token() }}">
<input type="text" id="legalentity_id" value="<?php echo Session::get('legalentity_id'); ?>" hidden />
@stop
@section('style')
<style type="text/css">

    .text-links{padding:0px 10px;}

    .thumbnail {
        padding: 0px !important;
        margin-bottom: 0px !important;
    }
    .fileinput-filename{word-wrap: break-word !important;}

    .fileinput-new .thumbnail{ width:100px !important; height:33px !important;}

    h4.block{padding:0px !important; margin:0px !important; padding-bottom:10px !important;}

    .pac-container .pac-logo{    z-index: 9999999 !important;}
    .pac-container{    z-index: 9999999 !important;}
    .pac-logo{    z-index: 9999999 !important;}
    #dvMap{height:304px !important; width:269px !important;}

    .modal-header {
        padding: 5px 15px !important;
    }

    .modal .modal-header .close {
        margin-top: 8px !important;
    }

    .form-group {
        margin-bottom: 5px !important;
    }

    .radio input[type=radio]{ margin-left:0px !important;}

    .form-control {
        margin-bottom: 10px !important;
    }
    .actions-add-edit{margin-top:25px !important;}
</style>





<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
@stop


@section('userscript')
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/grid_script.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/brands/brands_grid_script.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/form-wizard-manufactures.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/get_manufacturer_list.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/get-brands-dropdown.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/scripts/metronic.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/layout4/scripts/layout.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    jQuery(document).ready(function () {
    FormWizard.init();
    });
    /*$(function(){
     alert("{{trans('brands.brands.save_brand')}}");
     })*/
    $(document).ready(function(){
    $(".fileinput").find('div').removeClass('fileinput-exists');
    $('.reset').click(function(){$(this).closest('form').find("input[type=text],textarea").val(); });
    $('#addmanu').click(function(){
    $('#manu_org_name').val('');
    $('#manu_org_type').val('');
    $('#manu_segment').val('');
    $("#manu_id").val('');
    //$("#manu_logo_name").remove();
    $("#manu_image_view").removeClass('fileinput-preview thumbnail').addClass('fileinput-preview fileinput-exists thumbnail');
    $('input[id=manuLogo]').rules('add', 'required');
    $('#addmanuform').css('display', 'block');
    $('.modal-backdrop').css('display', 'block');
    });
    $('#editmanu').click(function(){
    var manu_id = $("#manufacturer_name").val();
    if (manu_id == 0) {
    $('#flass_message').text("{{trans('brands.manufacturers.manuf_select')}}"); ;
    $('div.alert').show();
    $('div.alert').removeClass('hide');
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
    $('html, body').animate({scrollTop: '0px'}, 500);
    return false;
    }

    var token = $("#csrf-token").val();
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: '/manu/edit/' + manu_id,
            processData: false,
            contentType: false,
            data:manu_id,
            success: function (rs)
            {
            //alert(manu_id);
            rs = JSON.parse(rs)
                    // var checkImage= $('img[name=manu_logo_name]').attr('src'); 
                    //alert(checkImage);
                    if (rs.manu_logo_name)
            {
            $('input[id=manuLogo]').rules('remove', 'required');
            $("#manu_image_view").removeClass('fileinput-preview fileinput-exists thumbnail').addClass('fileinput-preview thumbnail');
            }
            $('#manu_org_name').val(rs.manu_org_name);
            $('#manu_id').val(manu_id);
            $('#manu_segment').val(rs.org_segment_id);
			$('#pur_mgr').val(rs.pur_mgr);
            //$('#manu_org_name option:eq(47001)').prop('selected', true);
            //$('#manu_org_type option[value="47001"]');
            $('#manu_org_type').val(rs.org_type_id);
            
             if (/^(f|ht)tps?:\/\//i.test(rs.manu_logo_name)) { 
                 $("#manu_logo_name").attr("src",rs.manu_logo_name);
             } else {
                 $("#manu_logo_name").attr("src", '/uploads/manufacturer_logos/' + rs.manu_logo_name)
             }
            
            var site = document.domain;
            $.ajax({
            type: 'HEAD',
                    url: 'http://' + site + '/uploads/manufacturer_logos/' + rs.manu_logo_name,
                    success: function() {
                    $("#manu_logo_name").attr("src", '/uploads/manufacturer_logos/' + rs.manu_logo_name);
                    },
            });
            //alert(document.domain);
            //$("#manu_logo_name").attr("src",'/uploads/manufacturer_logos/'+rs.manu_logo_name);
            //$('manu_logo_name').val('uploads/brand_logos/'+rs.manu_org_name);
            //$('#manu_org_type option:eq('+rs.org_type_id+')').attr('selected', 'selected')
            /*manu_logo_name
             org_type_id*/
            $('#addmanuform').css('display', 'block');
            $('.modal-backdrop').css('display', 'block');
            }
    });
    $('.close').click(function(){
    $('#addmanuform').css('display', 'none');
    $('.modal-backdrop').css('display', 'none');
    });
    var checkImage = $('img[name=brand_logo_name]').attr('src');
    //alert(checkImage);
    if (checkImage != '')
    {
    $('input[id=brandLogo]').rules('remove', 'required');
    $(".fileinput-preview").removeClass('fileinput-preview fileinput-exists thumbnail').addClass('fileinput-preview thumbnail');
    }


   });
    var pathname = window.location.pathname;
    pathname = pathname.split("/");
    if (pathname[2] == 'add')
    {
    $("div.caption").replaceWith("<div class=" + 'caption' + ">CREATE BRAND</div>");
    }
    if (pathname[2] == 'edit')
    {
    $("div.caption").replaceWith("<div class=" + 'caption' + ">EDIT BRAND</div>");
    }
    });
    $("#manufacturer_name").change(function(){
    var man_id = $("#manufacturer_name").val();
    $("#manu_id").val(man_id);
    });
    
    $("#addmanu").click(function() {              
         $("#manu_image_view").hide();
         $("#manuLogo").val('');     
         $(".fileinput-filename").text('');
         $("h4.modal-title").replaceWith("<h4 class=" + 'modal-title' + ">Add Manufacturer</h4>");
         var validator = $("#add_manu_form").validate();
         validator.resetForm();
          $(".col-md-12").find('div').removeClass('has-error');    
    });

    $("#manuLogo").change(function(e){
        //alert($(this).val());
        var fileExtension = ['jpeg', 'jpg', 'png'];
        var fileName = e.target.files[0];        
        if (fileName  != '') {
			if($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == 1)
            $("#manu_image_view").show();            
         } else {
             $("#manu_image_view").hide();
         }
         /*if(fileName  != '') {             
            $("#manu_image_view").show();
         }*/
   });

    $("#editmanu").click(function() {
    $("h4.modal-title").replaceWith("<h4 class=" + 'modal-title' + ">Edit Manufacturer</h4>");
    var validator = $("#add_manu_form").validate();
    validator.resetForm();
    $(".col-md-12").find('div').removeClass('has-error');
    });
    $(".supp_info").on("click", function () {
    var checkImage = $('img[name=brand_logo_name123]').attr('src');
    if (checkImage)  {
    $('input[id=brandLogo]').rules('remove', 'required');
    $(".fileinput-preview").removeClass('fileinput-preview fileinput-exists thumbnail').addClass('fileinput-preview thumbnail');
    }
    var manufacturer_name = $("#manufacturer_name").val();
    if (manufacturer_name == 0) {
    $("#manufacturer_name").val('');
    }

    })


</script>

@stop
@extends('layouts.footer')