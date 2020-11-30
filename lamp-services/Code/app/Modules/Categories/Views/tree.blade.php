@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message">@include('flash::message')</span>
<span id="success_message_ajax"></span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
          <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">

            <div class="portlet-title">
                <div class="caption">Category List</div>
                 <div class="actions"> 
                 <!--  <a href="javascriot:void(0)"  data-toggle="modal" id="addUser" class="btn green-meadow" data-target="#addCategory">{{trans('category.lables.add_category')}}</a> -->
                 @if($marginsPerm == 1)
                  <a href="#" data-id="#" data-toggle="modal" data-target="#upload-document" class="btn green-meadow">Category Margins</a>
                 @endif
                  <button  type="button" class="btn green-meadow" id="addFreebie_model" data-toggle="modal" href="#addFreeBie"> {{trans('category.lables.add_category')}}</button>
                   </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="scroller" style=" height:550px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                            <!-- <div id="treeGrid"></div> -->
                            <table id="treeGriddata"></table>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

<div class="modal fade" id="upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">CATEGORY MARGIN</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="portlet box">
                                            <div class="portlet-body">

                                                {{ Form::open(array('url' => 'categories/downloadexcelforcategorymargins', 'id' => 'downloadexcel-slabpricing'))}}
                                                <div class="row">
                                                    <div class="col-md-12 text-center">
                                                        <div class="form-group">
                                                            <button type="submit" class="btn green-meadow" id="download-excel">Download Margins Template</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{ Form::close() }}
                                                {{ Form::open(['id' => 'frm_category_margins']) }}
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-6">

                                                        <div class="form-group">
                                                            <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">
                                                                    <input type="file" name="upload_categoryfile" id="upload_categoryfile" value=""/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <button type="button"  class="btn green-meadow" id="category-upload-button">Upload Margins Template</button>
                                                    </div>
                                                </div>
                                                {{ Form::close() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
  
  
<form action=""  id="category_configuration" method="POST"   enctype='multipart/form-data'>
<input type="hidden" name="_token" id="csrf-token1" value="{{ Session::token() }}" />
<div class="modal fade modal-scroll" id="addFreeBie" tabindex="-1" role="basic" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
<h4 class="modal-title">CATEGORY CONFIGURATION</h4>
</div>
<div class="modal-body model_style">

<div class="row">
<div class="col-md-6">
<div class="form-group err">
  <label class="control-label">{{trans('category.lables.name')}}  </label>
  <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
  <input type="text"  id="name" name="name" placeholder="name" class="form-control">

</div>
</div>

<div class="col-md-6">
<div class="form-group err">
<label class="control-label">{{trans('category.lables.parent')}}  </label>
              <input type="hidden" id="edit_category_id" name="edit_category_id" value="">
                <input type="hidden" id="edit_category_name" name="edit_category_name" value="">
                 <input type="hidden" id="edit_category_id1" name="edit_category_id1" value="">
                
                <input type="hidden" id="parent_category_id" name="parent_category_id" value="">              
              
              <!-- <input type="text"  id="parent_id" name="parent_id" placeholder="parent_id" class="form-control" value="0" disabled="disabled" > -->
              <select class="form-control select2me" name="parent_id" id="category">
                <option value="">{{trans('category.lables.pls_select')}} </option>
              </select>
</div>
</div>


</div>
<div class="row">

<div class="col-md-6">
  <div class="form-group err">
 <label class="control-label">{{trans('category.lables.status')}}</label>
              <span class="custom-dropdown custom-dropdown--white">    
              <select name="is_active" id="is_active" required class="form-control custom-dropdown__select custom-dropdown__select--white">
                <option  value="1">{{trans('category.lables.active')}}</option>
                <option  value="0">{{trans('category.lables.in_active')}}</option>
              </select>
              </span>
</div>
</div>
<div class="col-md-6">
  <div class="form-group err">
 <label class="control-label">{{trans('category.lables.product_class')}}</label>
              <span class="custom-dropdown custom-dropdown--white"> 
              <select name="is_product_class" id="is_product_class"  class="form-control custom-dropdown__select custom-dropdown__select--white">
                <option  value="0">{{trans('category.lables.no')}}</option>
                <option  value="1">{{trans('category.lables.yes')}}</option>
              </select>
              </span>
</div>
</div>


</div>


 <div class="row">
          <div class="col-sm-6">
              <div class="form-group">
                  <label class="control-label">{{trans('category.lables.category_image')}}</label>
                  <input name="category_image" id="category_image_id" placeholder="Category image URL here" class="form-control" />
                   <input type="hidden" name="category_image_local" id="category_image_local" placeholder="Category image URL here" class="form-control" />
                  <h5 align="center">OR</h5>
                  <input name="brow_image" id="brow_image"  class="form-control" type="file"  accept="image/*"   >
              </div>

          </div>
          <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label">{{trans('category.lables.preview')}}</label>    
                <img src="" class="preview" />
              </div>
          </div>
        </div>
         <div class="row">
          <div class="col-sm-6">
              <div class="form-group">
                  <label class="control-label">Business Segment</label>
                   <select name="segments[]" id="segments" class="form-control multi-select-box" multiple="multiple" placeholder="Please select">
                      @foreach($segments as $segmentsValue)
                      <option value="{{$segmentsValue->value}}" >{{$segmentsValue->name}}  </option>
                      @endforeach
                  </select>
              </div>
          </div>
        </div>
<div class="row">


</div>
</div>
<div class="modal-footer">
<div class="row">
<div class="col-md-12 text-center">
<button type="submit" class="btn green-meadow save_freebie" >Save</button>
</div>
</div>
</div>
</div>
<!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
</form>






@stop

@section('style')

<style type="text/css">

    .fa-times{ color: red !important;}

    .jqx-widget-header {
        background: #f2f2f2 !important;
    }
    .fc-field {
        cursor:pointer !important;
    }

    .up-down{width:264px !important; margin:3px !important;}
    .portlet.light > .portlet-title > .tools {
        padding:0px !important;
    }
    .portlet > .portlet-title > .tools > a {
        height: auto !important;
    }
    .has-feedback label~.form-control-feedback {
        top: 40px !important;
        right:10px !important;
    }
    .fa-plus{font-size: 14px !important;}
    .fa-pencil{font-size: 14px !important;}
    .fa-trash-o{font-size: 14px !important;}
    
  .preview {
  display: block;
  max-width:100px;
  max-height:100px;
  width: auto;
  height: auto;
}
</style>
@stop


@section('userscript')

<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<!--Sumoselect CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<!-- select 2 drop down -->
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script> 
<!-- <script src="{{ URL::asset('assets/global/plugins/getcategorylist.js') }}" type="text/javascript"></script> --> 
<script src="/js/helper.js"></script>

<script src="{{ URL::asset('assets/admin/pages/scripts/category/form-wizard-create-category.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>

<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />

@stop

@section('script')
@include('includes.validators')

<script>
    jQuery(document).ready(function() {
    
    FormWizard.init();
    });</script>


<script type="text/javascript">
    
$('[name="category_image"]').on('change', function() {
     $('img.preview').prop('src', this.value);
      $('#category_image_local').val('');
});
$(document).on("change","#brow_image",function(){
   var token  = $("#csrf-token").val();
   $('#category_image_id').val('');
    var input = document.getElementById("brow_image");
    file = input.files[0];
    if(file != undefined){
      formData= new FormData();
      if(!!file.type.match(/image.*/)){
        formData.append("brow_image", file);
        formData.append("name", $("#name").val());
        formData.append("check",1);
        $.ajax({
           headers: {'X-CSRF-TOKEN': token},
          url: "/categories/saveparentcategory",
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          success: function(data){
              $("#category_image_local").val(data);
              $('img.preview').attr('src',data);
          }
        });
      }else{
        alert('Not a valid image!');
      }
    }else{
      alert('Input something!');
    }
});


    $(document).ready(function (){
      //add segment when we change the parent category
      $(document).on("change","#category",function()
        {
          $("#segments option").each(function()
          {
            $('.multi-select-box')[0].sumo.unSelectItem($(this).val());
          });
           $.ajax({
            type: 'GET',
            url: '/categories/getSegments/'+$(this).val(),
            success: function (resp) {
              $.each(resp, function(segKey, segValue ) 
              {
                var temp = segValue.value;
                $('.multi-select-box')[0].sumo.selectItem(temp.toString());
              });
            },
            error: function (error) {
                console.log(error.responseText);
            },
            complete: function () {
            }
          });
        });
       window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
        $("#toggleFilter").click(function () {
            $("#filters").toggle("fast", function () {});
        });
        /*$("#category").change(function(){
            token  = $("#csrf-token").val();
            var name=$("#name").val(); 
            if(name!="")
            {
                var cat_id= $(this).val();
                data={cat_id:cat_id,name:name};
                url="/categories/checkCategoryId"; 
                $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url: url,
                    data:data,
                    type: 'GET',                                          
                    success: function (rs) 
                    {
                        if(rs!="")
                        {
                            alert("This name is already exists...");
                           $( ".test" ).removeClass( "btn btn-primary" ).addClass( "btn btn-primary disabled" );
                            $( ".test" ).prop("disabled", true);
                        }else
                        {
                           $( ".test" ).removeClass( "btn btn-primary disabled" ).addClass( "btn btn-primary " );
                            $( ".test" ).prop("disabled", false);
                        }
                        
                      
                    }
                });
            }
            //$("#AssignTPM").attr( disabled, disabled );
               // alert("kljasdf_______"+$(this).val());
        });*/
        $('#addFreebie_model').on('click',function(e) {
            $('#edit_category_id').val('');
            $('#edit_category_name').val('');
             $('select.multi-select-box')[0].sumo.unSelectAll();
          if (e.originalEvent !== undefined)
          {
            $('select.multi-select-box')[0].sumo.unSelectAll();
            $('#edit_category_id').val('');
            $(".preview").attr('src', '');
            $('#edit_category_name').val('');
              $('#edit_category_name').val('');
            $("#basicvalCode").text("{{trans('category.lables.category')}}");
           $('#category_configuration')[0].reset();
           $("#category").select2().select2('val',0);
          }
         });
        //$("#edit_category_id").val('');
         url='/getAddCategoryList';
        var localCache = {
            data: {},
            remove: function (url) {
                delete localCache.data[url];
            },
            exist: function (url) {
                return localCache.data.hasOwnProperty(url) && localCache.data[url] !== null;
            },
            get: function (url) {
                console.log('Getting in cache for url' + url);
                return localCache.data[url];
            },
            set: function (url, cachedData, callback) {
                localCache.remove(url);
                localCache.data[url] = cachedData;
                if ($.isFunction(callback)) callback(cachedData);
            }
        };

       function doSomething(data) {
        console.log(data);
        }
        $.ajax({
             headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
            url: url,
            type: 'GET', 
            cache: true, 
            beforeSend: function () {
                if (localCache.exist(url)) {
                    doSomething(localCache.get(url));
                    return false;
                }
                return true;
            },
            complete: function (jqXHR, textStatus) {
                localCache.set(url, jqXHR, doSomething);
            },                                           
            success: function (rs) 
            {
                $("#category").html(rs);
                $('.prod_class').css('color','#0174DF !important');
                 $("#category").select2().select2('val',0);
            }
        });


         token  = $("#csrf-token").val();  
                           
        $('#add_parent_validation').bootstrapValidator({
//        live: 'disabled',
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
          name: {
                    validators: {
                        field: {
                              required: true,
                              minlength: 1
                            },
                        notEmpty: {
                            message: "{{trans('category.validate.name')}}"
                        },
                        regexp: {
                            regexp: /^[a-zA-Z,.&? ]*$/,
                            message: "{{trans('category.validate.alphabets')}}"
                        },remote: {
                            headers: {'X-CSRF-TOKEN': token},
                            url: '/categories/uniqueNameValidation',
                            type: 'POST',
                            data: function(validator) {
                                return{
                                    table_name: 'categories', 
                                    field_name: 'name',
                                    field_value: $('#add_parent_validation #name').val(),
                                    edit_name: $('[id="edit_category_name"]').val(),
                                    pluck_id: 'category_id'
                                }
                            },
                            delay: 2000,     // Send Ajax request every 2 seconds
                            message: "{{trans('category.validate.name_exist')}}"
                        }
                    }
                }
        }
    }).on('success.form.bv', function(event) {
        
        var name= $('#add_parent_validation #name').val();
        var cat_id= $('#add_parent_validation #category').val();
        var edit_cat_name=$("#edit_category_name").val();
        var edit_cat_id=$("#edit_category_id").val();
        var edit_cat_id1=$("#edit_category_id1").val();
        datObj={name:name,cat_id:cat_id,edit_cat_id:edit_cat_id,edit_cat_id1:edit_cat_id1};

        if(edit_cat_name.toLowerCase() != name.toLowerCase())
        {

        
            $.ajax({
                data: datObj,
                url: '/categories/uniqueValidation',
                success: function (resp) {
                    
                  if(resp!="")
                  {
                    alert(name+" already exists.");
                    $(".close_model").click();
                    location.reload();
                  }else
                  {
                    ajaxCallPopup($('#add_parent_validation'));
                  }

                },
                error: function (error) {
                    console.log(error.responseText);
                },
                complete: function () {

                }
            });
         }else
         {
            ajaxCallPopup($('#add_parent_validation'));
         }
       
            //
        return true;
        }).validate({
        submitHandler: function (form) {

            return false;
        }
    });

        $("#category-upload-button").click(function () {
          token  = $("#csrf-token").val();
          var stn_Doc = $("#upload_categoryfile")[0].files[0];
          var formData = new FormData();
          formData.append('category_data', stn_Doc);
          formData.append('test', "sample");
          $.ajax({
            type: "POST",
            headers: {'X-CSRF-TOKEN': token},
            url: "/categories/uploadcatmargin",
            data: formData,
            processData: false,
            contentType: false,
            success: function (data){
                console.log(data);
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">'+data+'</div></div>' );
                $(".alert-success").fadeOut(20000)
            }
          });
          $('#upload-document').modal('toggle');
        });
    $('#upload-document').on('hidden.bs.modal', function (e) {
        $("#upload_categoryfile").val("");
    });

    $('#add_category_validation').bootstrapValidator({
//        live: 'disabled',
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
          
            name: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter Category name.'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z_ ]*$/,
                            message: 'Enter only letters.'
                        }
                    }
                }
        }
    }).on('success.form.bv', function(event) {
            ajaxCallPopup($('#add_category_validation'));
        return true;
        }).validate({
        submitHandler: function (form) {
            return false;
        }
    });

    $('#update_category_validation').bootstrapValidator({
//        live: 'disabled',
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            name: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter Category name.'
                        }
                    }
                }
        }
    }).on('success.form.bv', function(event) {
            ajaxCallPopup($('#update_category_validation'));
        return true;
        }).validate({
        submitHandler: function (form) {
            return false;
        }
    });  
});
</script>

<script type="text/javascript">

 
function editCategory(cat_name,cat_id,is_active,is_parent_class)
{
  $("#segments option").each(function()
  {
    $('.multi-select-box')[0].sumo.unSelectItem($(this).val());
  });
  $("#addFreebie_model").click();
  //this is for segment mapping data
    $.ajax({
            type: 'GET',
            url: '/categories/getSegments/'+cat_id,
            success: function (resp) {
              $.each(resp, function(segKey, segValue ) 
              {
                var temp = segValue.value;
                $('.multi-select-box')[0].sumo.selectItem(temp.toString());
              });
            },
            error: function (error) {
                console.log(error.responseText);
            },
            complete: function () {
            }
        });

    $.ajax({
            type: 'GET',
            url: '/categories/getParentCategory/'+cat_id,
            success: function (resp) {
                if(resp!=0)
                {
                    $("#parent_category_id").val(resp.category_id);
                     $("#edit_category_id1").val(resp.category_id);
                    $("#category").select2().select2('val',resp.category_id);
                }
                else
                {
                    $("#category").select2().select2('val',resp);
                }
            },
            error: function (error) {
                console.log(error.responseText);
            },
            complete: function () {
            }
        });
        $.ajax({
            type: 'GET',
            url: '/categories/getCategoryImage/'+cat_id,
            success: function (resp) {
                //$('img.preview').css('display','none'); 
                $("#category_image_id").val('');
                if(resp.image_url != null)
                {
                  if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(resp.image_url)){
                    $("#category_image_id").val(resp.image_url);
                  }else
                  {
                    $("#category_image_local").val(resp.image_url);
                  }
                $('img.preview').css('display','block');
                $('img.preview').attr('src', resp.image_url);   
                }
				else
				{
					$('img.preview').css('display','none');
				}
            },
            error: function (error) {
                console.log(error.responseText);
            },
            complete: function () {
            }
        });

    $("#basicvalCode").text("{{trans('category.lables.edit_category')}}");
    $("#edit_category_id").val(cat_id);
    $("#edit_category_name").val(cat_name);
    $('#addCategory').modal('show'); 
    $("#name").val(cat_name);    
    $("#is_product_class").val(is_parent_class);
    $("#is_active").val(is_active);
    

    //$("#createrule-modal").click();
}
function getCategories()
{
    url = '/categories/getcategorieslist';
    // Send the data using post
    var posting = $.get(url);
    // Put the results in a div
    posting.done(function (data) {
        //console.log(data);

    });
}

function deleteEntityType(category_id)
{
    var deletecategory = confirm("Are you sure you want to Delete ?"), self = $(this);
    if (deletecategory == true) {
        $.ajax({
            data: '',
            type: 'GET',
            datatype: "JSON",
            url: '/categories/deletecategory/' + category_id,
            success: function (resp) {
                //alert(resp);
                alert(resp.message);
                categoryGrid();
              
            },
            error: function (error) {
                console.log(error.responseText);
            },
            complete: function () {

            }
        });
    }
}
/*
$('[name="category_name[]"]').click(function (event) {
    var $checkbox = $(this);
    if ($checkbox.is(':checked'))
    {
        $('.' + $checkbox.attr('id')).prop('checked', true);
    } else {
        $('.' + $checkbox.attr('id')).prop('checked', false);
    }
});
$('#add_category_form #manufacturer_id').change(function (event) {
    $('[name="category_name[]"]').each(function (event) {
        $(this).prop('checked', false);
    });
    var url = '/categories/getcustomercategorylist';
    var manufacturerId = $(this).val();
    var posting = $.get(url, {manufacturer_id: manufacturerId});
    // Put the results in a div
    posting.done(function (data) {
        if (data.status == true)
        {
            if (data.categories.category_id != null)
            {
                var categories = data.categories.category_id.split(',');
                $.each(categories, function (id, category) {
                    $('#category_' + category).prop('checked', true);
                });
            }
        }
    });
});
$('#add_category_form').submit(function (event) {
    event.preventDefault();
});
$("div .navbar-fixed-bottom .btn-primary").on("click", function (e) {
    $(this).prop('disabled', true);
    url = $('#add_category_form').attr('action');
    var manufacturerId = $('#add_category_form #manufacturer_id').val();
    var categoryList = new Array();
    $('input:checkbox[name="category_name[]"]').each(function () {
        var cat = this.checked ? $(this).val() : "";
        if (cat != "")
        {
            categoryList.push(cat);
        }
    });
    // Send the data using post
    var posting = $.post(url, {manufacturer_id: manufacturerId, category_list: categoryList});
    // Put the results in a div
    posting.done(function (data) {
        if (data.status == true)
        {
            alert('Sucessfully added categories');
            $("div .navbar-fixed-bottom .btn-primary").prop('disabled', false);
        } else {
            alert('Unable to add categories, please try again');
            $("div .navbar-fixed-bottom .btn-primary").prop('disabled', false);
        }
    });
});

$('#add_category_form').submit(function (event) {
    event.preventDefault();
});
$("div .navbar-fixed-bottom .btn-primary").on("click", function (e) {
    $(this).prop('disabled', true);
    url = $('#add_category_form').attr('action');
    var manufacturerId = $('#add_category_form #manufacturer_id').val();
    var categoryList = new Array();
    $('input:checkbox[name="category_name[]"]').each(function () {
        var cat = this.checked ? $(this).val() : "";
        if (cat != "")
        {
            categoryList.push(cat);
        }
    });
    // Send the data using post
    var posting = $.post(url, {manufacturer_id: manufacturerId, category_list: categoryList});
    // Put the results in a div
    posting.done(function (data) {
        if (data.status == true)
        {
            alert('Sucessfully added categories');
            $("div .navbar-fixed-bottom .btn-primary").prop('disabled', false);
        } else {
            alert('Unable to add categories, please try again');
            $("div .navbar-fixed-bottom .btn-primary").prop('disabled', false);
        }
    });
});
*/
$(function () {
    
    categoryGrid();
});
function categoryGrid()
{
  var token = $("#token_value").val();
     $.ajax({
        url:"/categories/treeCats?_token=" + token,
        type:"POST",
        dataType:"json",
        success:function(data)
        {
            console.log(typeof(data));

            //catDetails = data;

            $('#treeGriddata').igTreeGrid({
                //dataSource: '/categories/treeCats',
                dataSource: data,
                //responseDataKey: 'result',
                autoGenerateColumns: false,
                primaryKey: "category_id",
                height:"100%",
                columns: [
                    { headerText: "Category ID", key: "category_id", width: "50%", dataType: "string", hidden: 'true' },
                    { headerText: "Category Name", key: "cat_name", width: "50%", dataType: "string" },
                    { headerText: "Status", key: "is_active", width: "16%", dataType: "string"},
                    { headerText: "Product Class", key: "is_product_class", width: "16%", dataType: "string" },
                    { headerText: "Actions", key: "actions", width: "16%", dataType: "string" }
                ],
                childDataKey: "cats",
                initialExpandDepth: 0,
                features: [
                {
                    name: "Sorting"
                },
                {
                    name: "Filtering",
                    columnSettings: [
                        {columnKey: 'cat_name', allowFiltering: true},
                        {columnKey: 'is_active', allowFiltering: false},
                        {columnKey: 'is_product_class', allowFiltering: false},
                        {columnKey: 'actions', allowFiltering: false},
                    ]
                },
                {
                    name: "Paging",
                    pageSize: 10
                    /*recordCountKey: 'TotalRecordsCount',
                    chunkIndexUrlKey: 'page',
                    chunkSizeUrlKey: 'pageSize',
                    chunkSize: 10,
                    name: 'AppendRowsOnDemand',
                    loadTrigger: 'auto',
                    type: 'remote'*/
                }]
            });
            
        }
    });

}

</script>    
@stop   
