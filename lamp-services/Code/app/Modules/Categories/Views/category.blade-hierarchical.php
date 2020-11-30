@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')


<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">


            <div class="portlet-title">
                <div class="caption">CATEGORY LIST</div>
                <div class="tools">


                    @if(isset($allowed_buttons['add_new_parent_category']) && $allowed_buttons['add_new_parent_category'] == 1)
                    <a href="javascriot:void(0)"  data-toggle="modal" id="addUser" class="btn blue" data-target="#basicvalCodeModalAddParent"><i class="fa fa-plus-circle"></i> Add New Parent Category</a>
                    @endif


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




















<!-- Modal - Popup for ADD Parent Category -->
<div class="modal modal-scroll fade" id="addCategory" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Add Category</h4>
            </div>
            <div class="modal-body">

                {{ Form::open(array('url' => 'categories/savecategory', 'class' => 'form1','id'=>'add_category_validation' )) }}
                {{ Form::hidden('_method','POST') }}

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text"  id="name" name="name" placeholder="name" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Parent</label>
                            <input type="text" disabled name="parent_name" class="form-control" id="addCategory_parent_id">
                            <input type="hidden"  name="parent_id" class="form-control" id="category_parent_id">

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" required class="form-control">
                                <option  value="1">Active</option>
                                <option  value="0">In-Active</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Product Class</label>
                            <select name="is_product_class"  class="form-control">
                                <option  value="0">No</option>
                                <option  value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 text-center">
                        {{ Form::submit('Add', array('class' => 'btn btn-primary')) }}
                    </div>
                </div>


                {{Form::close()}}

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<!-- Modal - Popup for Edit Parent Category -->
<div class="modal modal-scroll fade" id="editCategory" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Edit Category</h4>
            </div>
            <div class="modal-body">

                {{ Form::open(array('url' => 'categories/updatecategory/','data-url' => 'categories/updatecategory/', 'id'=>'update_category_validation' )) }}
                {{ Form::hidden('_method','PUT') }}


                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text"  id="cat_name" name="cat_name" placeholder="name" class="form-control">
                            <input type="hidden" name="category_id" id="category_id">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Parent</label>
                            <input type="text" class="form-control"  disabled name="parent_name" id="parent_name"> 
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="is_active" required class="form-control">
                                <option  value="1">Active</option>
                                <option  value="0">In-Active</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Product Class</label>
                            <select name="is_product_class" required class="form-control">
                                <option  value="0">No</option>
                                <option  value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 text-center">
                        <input type="button" name="update_categ" id="update_categ" value="Update" class="btn btn-primary">
                    </div>
                </div>




                {{Form::close()}}

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Modal - Popup for ADD Parent Category -->
<div class="modal modal-scroll fade" id="basicvalCodeModalAddParent" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Add Parent Category</h4>
            </div>
            <div class="modal-body">

                {{ Form::open(array('url' => 'categories/saveparentcategory', 'class' => 'form1','id'=>'add_parent_validation' )) }}
                {{ Form::hidden('_method','POST') }}

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text"  id="name" name="name" placeholder="name" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Parent</label>
                            <input type="text"  id="parent_id" name="parent_id" placeholder="parent_id" class="form-control" value="0" disabled="disabled" >
                        </div>
                    </div>
                </div>

<div class="row">
<div class="col-sm-6">
<div class="form-group">
<label>Status</label>
<select name="status" required class="form-control">
<option  value="1">Active</option>
<option  value="0">In-Active</option>
</select>
</div>
</div>
<div class="col-sm-6">
<div class="form-group">
<label>Product Class</label>
<select name="is_product_class"  class="form-control">
<option  value="0">No</option>
<option  value="1">Yes</option>
</select>
</div>
</div>
</div>


                <div class="row">
                    <div class="col-sm-12 text-center">
                        {{ Form::submit('Add', array('class' => 'btn btn-primary' )) }}
                    </div>
                </div>




                {{Form::close()}}

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal -->



@stop

@section('style')
<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />

<style type="text/css">

    .fa-pencil{ color:blue !important;}
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
    .fa-plus{font-size: 11px !important;}
    .fa-pencil{font-size: 11px !important;}
    .fa-trash-o{font-size: 11px !important;}
</style>
@stop



@section('userscript')
@include('includes.jqx')
@include('includes.validators')
@stop

@section('script')
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/getcategorylist.js') }}" type="text/javascript"></script>

<script src="/js/helper.js"></script>
<script type="text/javascript">
    $(document).ready(function (){
        var prod_class = $('#prod_class').val();
        console.log(prod_class);
    });
</script>
<script type="text/javascript">
    $(document).ready(function (){
        
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
            ajaxCallPopup($('#add_parent_validation'));
        return true;
        }).validate({
        submitHandler: function (form) {
            return false;
        }
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
    $('#update_categ').on('click', function (){
        var form = $("#update_category_validation");
        var id = $('#category_id').val();
        $.ajax({
            type: "POST",
            url: '/categories/updatecategory/'+id,
            data: $("#update_category_validation").serialize(),
            success: function (response) {
                console.log(response);
                alert(response['message']);
                $('.close').trigger('click');
                location.reload();
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function (){
        
       
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
                        }/*,
                        remote: {
                            url: '/customer/uniquevalidation',
                            type: 'POST',
                            data: {
                                table_name: 'categories', 
                                field_name: 'name', 
                                field_value: $('#add_category_validation #name').val(),
                                pluck_id: 'category_id', 
                            },
                            delay: 2000,     // Send Ajax request every 2 seconds
                            message: 'Name already exists.'
                        },*/
                    }
                }
        }
    }).on('success.form.bv', function(event) {
            //ajaxCallPopup($('#add_category_validation'));
        return true;
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
           // ajaxCallPopup($('#update_category_validation'));
        return true;
        });  
});
</script>
<script type="text/javascript">
function getcategoriesName(el,cid,cname) {
    //alert(cid+'______'+cname);
    $("#addCategory_parent_id").val(cname);
    $("#category_parent_id").val(cid);
    var parentCategory = $(el).closest('tr').find('td:eq(0) span:last').text();
    $('#addCategory_parent_id').find('option').prop('disabled', true);
    $('#addCategory_parent_id').find('option').filter(function () {
        return ($(this).text() == parentCategory);
    }).prop({'selected': true, 'disabled': false});


/*$(document).ready(function ()
 {
 $('#manufacturer_id').trigger('change');
 $.ajax(
 {
 url: "/categories/getcategoriestree",
 success: function (result)
 {
 
 
 var employees = result;
 // prepare the data
 var source =
 {
 datatype: "json",
 datafields: [
 {name: 'id', type: 'varchar'},
 {name: 'pname', type: 'varchar'},
 {name: 'is_active', type: 'string'},
 {name: 'ebutor_commission', type: 'string'},
 {name: 'product_class', type: 'varchar'},
 {name: 'actions', type: 'varchar'},
 {name: 'children', type: 'array'},
 {name: 'expanded', type: 'bool'}
 ],
 hierarchy:
 {
 root: 'children'
 },
 id: 'id',
 localData: employees
 };
 var dataAdapter = new $.jqx.dataAdapter(source);
 $("#treeGrid").jqxTreeGrid(
 {
 width: "100%",
 source: dataAdapter,
 sortable: true,
 columns: [
 //{text: 'Parent', datafield: 'name', width: 150},
 {text: 'Category Name', datafield: 'pname', width: "55%"},
 {text: 'Status', datafield: 'is_active', width: "10%"},
 {text: 'Product Class', datafield: 'product_class', width: "10%"},
 {text: 'Actions', datafield: 'actions', width: "25%"}
 ]
 });
 
 }
 
 });
 makePopupAjax($('#basicvalCodeModalAddParent'));
 makePopupAjax($('#addCategory'));
 makePopupEditAjax($('#editCategory'));
 getCategories();
 });*/

function getCategories()
{
    url = '/categories/getcategorieslist';
    // Send the data using post
    var posting = $.get(url);
    // Put the results in a div
    posting.done(function (data) {
        console.log(data);

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
                if (resp.message)
                    alert(resp.message);
                if (resp.status == true)
                {
                    self.parents('td').remove();
                    location.reload();
                }

            },
            error: function (error) {
                console.log(error.responseText);
            },
            complete: function () {

            }
        });
    }
}
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

$(function () {
    $('#treeGriddata').igHierarchicalGrid({
        dataSource: '/categories/getparentcats',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'results',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [
            {headerText: "Category Name", key: "cat_name", dataType: "string", width: "50%"},
            {headerText: "Status", key: "status", dataType: "string", width: "16%"},
            {headerText: "Product Class", key: "prodclass", dataType: "string", width: "16%"},
            {headerText: "actions", key: "actions", dataType: "string", width: "16%"},
        ],
        columnLayouts: [
            {
                dataSource: '/categories/getchildcats',
                autoGenerateColumns: false,
                autoGenerateLayouts: false,
                mergeUnboundColumns: false,
                responseDataKey: 'resultschild',
                generateCompactJSONResponse: false,
                enableUTCDates: true,
                columns: [
                    {headerText: "Category Name", key: "cat_name", dataType: "string", width: "50%"},
                    {headerText: "Status", key: "status", dataType: "string", width: "16%"},
                    {headerText: "Product Class", key: "prodclass", dataType: "string", width: "16%"},
                    {headerText: "actions", key: "actions", dataType: "string", width: "16%"},
                ],
                columnLayouts: [
                    {
                        dataSource: '/categories/getchildcats',
                        autoGenerateColumns: false,
                        autoGenerateLayouts: false,
                        mergeUnboundColumns: false,
                        responseDataKey: 'resultschild',
                        generateCompactJSONResponse: false,
                        enableUTCDates: true,
                        columns: [
                            {headerText: "Category Name", key: "cat_name", dataType: "string", width: "50%"},
                            {headerText: "Status", key: "status", dataType: "string", width: "16%"},
                            {headerText: "Product Class", key: "prodclass", dataType: "string", width: "16%"},
                            {headerText: "actions", key: "actions", dataType: "string", width: "16%"},
                        ],
                        columnLayouts: [
                            {
                                dataSource: '/categories/getchildcats',
                                autoGenerateColumns: false,
                                autoGenerateLayouts: false,
                                mergeUnboundColumns: false,
                                responseDataKey: 'resultschild',
                                generateCompactJSONResponse: false,
                                enableUTCDates: true,
                                columns: [
                                    {headerText: "Category Name", key: "cat_name", dataType: "string", width: "50%"},
                                    {headerText: "Status", key: "status", dataType: "string", width: "16%"},
                                    {headerText: "Product Class", key: "prodclass", dataType: "string", width: "16%"},
                                    {headerText: "actions", key: "actions", dataType: "string", width: "16%"},
                                ],
                                columnLayouts: [
                                    {
                                        dataSource: '/categories/getchildcats',
                                        autoGenerateColumns: false,
                                        autoGenerateLayouts: false,
                                        mergeUnboundColumns: false,
                                        responseDataKey: 'resultschild',
                                        generateCompactJSONResponse: false,
                                        enableUTCDates: true,
                                        columns: [
                                            {headerText: "Category Name", key: "cat_name", dataType: "string", width: "50%"},
                                            {headerText: "Status", key: "status", dataType: "string", width: "16%"},
                                            {headerText: "Product Class", key: "prodclass", dataType: "string", width: "16%"},
                                            {headerText: "actions", key: "actions", dataType: "string", width: "16%"},
                                        ],
                                        columnLayouts: [
                                            {
                                                dataSource: '/categories/getchildcats',
                                                autoGenerateColumns: false,
                                                autoGenerateLayouts: false,
                                                mergeUnboundColumns: false,
                                                responseDataKey: 'resultschild',
                                                generateCompactJSONResponse: false,
                                                enableUTCDates: true,
                                                columns: [
                                                    {headerText: "Category Name", key: "cat_name", dataType: "string", width: "50%"},
                                                    {headerText: "Status", key: "status", dataType: "string", width: "16%"},
                                                    {headerText: "Product Class", key: "prodclass", dataType: "string", width: "16%"},
                                                    {headerText: "actions", key: "actions", dataType: "string", width: "16%"},
                                                ],
                                                features: [
                                                    {
                                                        name: 'Paging',
                                                        type: 'remote',
                                                        pageSize: 10,
                                                        recordCountKey: 'TotalRecordsCount',
                                                        pageIndexUrlKey: "page",
                                                        pageSizeUrlKey: "pageSize"
                                                    }
                                                ],
                                                primaryKey: 'category_id',
                                                width: '100%',
                                                height: '400px',
                                                initialDataBindDepth: 0,
                                                localSchemaTransform: false
                                            }],
                                        features: [
                                            {
                                                name: 'Paging',
                                                type: 'remote',
                                                pageSize: 10,
                                                recordCountKey: 'TotalRecordsCount',
                                                pageIndexUrlKey: "page",
                                                pageSizeUrlKey: "pageSize"
                                            }
                                        ],
                                        primaryKey: 'category_id',
                                        width: '100%',
                                        height: '450px',
                                        initialDataBindDepth: 0,
                                        localSchemaTransform: false
                                    }],
                                features: [
                                    {
                                        name: 'Paging',
                                        type: 'remote',
                                        pageSize: 10,
                                        recordCountKey: 'TotalRecordsCount',
                                        pageIndexUrlKey: "page",
                                        pageSizeUrlKey: "pageSize"
                                    }
                                ],
                                primaryKey: 'category_id',
                                width: '100%',
                                height: '450px',
                                initialDataBindDepth: 0,
                                localSchemaTransform: false
                            }],
                        features: [
                            {
                                name: 'Paging',
                                type: 'remote',
                                pageSize: 10,
                                recordCountKey: 'TotalRecordsCount',
                                pageIndexUrlKey: "page",
                                pageSizeUrlKey: "pageSize"
                            }
                        ],
                        primaryKey: 'category_id',
                        width: '100%',
                        height: '480px',
                        initialDataBindDepth: 0,
                        localSchemaTransform: false
                    }],
                features: [
                    {
                        name: 'Paging',
                        type: 'remote',
                        pageSize: 10,
                        recordCountKey: 'TotalRecordsCount',
                        pageIndexUrlKey: "page",
                        pageSizeUrlKey: "pageSize"
                    }
                ],
                primaryKey: 'category_id',
                width: '100%',
                height: '100%',
                initialDataBindDepth: 0,
                localSchemaTransform: false
            }],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                    {columnKey: 'status', allowSorting: false},
                    {columnKey: 'prodclass', allowSorting: false},
                    {columnKey: "actions", allowSorting: false}

                ]
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'actions', allowFiltering: false},
                    {columnKey: 'prodclass', allowFiltering: false},
                    {columnKey: 'status', allowFiltering: false},
                ]
            },
            {
                recordCountKey: 'TotalRecordsCount',
                chunkIndexUrlKey: 'page',
                chunkSizeUrlKey: 'pageSize',
                chunkSize: 10,
                name: 'AppendRowsOnDemand',
                loadTrigger: 'auto',
                type: 'remote'
            }

        ],
        primaryKey: 'category_id',
        width: '100%',
        height: '500px',
        initialDataBindDepth: 0,
        localSchemaTransform: false
    });
});


</script>    
@stop   
