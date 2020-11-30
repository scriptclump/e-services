@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div id="loadingmessage" class=""></div>
<div class="row">
    <div class="col-md-12">
        <div class="portlet box">
            <div class="portlet-body">
                <h3 class="form-section">INBOUND REQUEST</h3>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Pickup Location</label>
                            <select class="form-control" name="pickup_location">
                                @foreach ($pickup_location as $pickup_location_each)
                                <option value="{{ $pickup_location_each['address1'] }} , {{ $pickup_location_each['address2'] }} , {{ $pickup_location_each['city'] }}">{{ $pickup_location_each['address1'] }} , {{ $pickup_location_each['address2'] }} , {{ $pickup_location_each['city'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Delivery Location</label>
                            <select class="form-control" name="delivery_location">
                                @foreach ($delivery_location as $delivery_location_each)
                                <option value="{{ $delivery_location_each['le_wh_id'] }}">{{ $delivery_location_each['address1'] }} , {{ $delivery_location_each['address2'] }}, {{ $delivery_location_each['city'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Select Available Slots</label>
                            <select class="form-control" name="time_slots">
                                <option value="9:00 AM - 9:00 PM | 26-06-2016">9:00 AM - 9:00 PM | 26-06-2016</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">STN Number</label>
                            <input type="text" name="stn_number" class="form-control" placeholder="46456HJK987" value="46456HJK987">
                            <input type="hidden" name="request_type" value="inward" />
                            <input type="hidden" name="request_status" value="In Queue" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Upload STN Document</label>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="input-group input-medium" style="width:247px !important;">
                                    <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                        <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                        </span>
                                    </div>
                                    <span class="input-group-addon btn btn green-meadow btn-file">
                                        <span class="fileinput-new">
                                            Select file </span>
                                        <span class="fileinput-exists">
                                            Change </span>
                                        <input type="file" name="stn_docs" id="stn_docss">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <h3 class="form-section">Select Products</h3>
                        <div class="portlet light tasks-widget">
                            <div class="portlet-title">
                                <div class="row">
                                    <div class="col-md-6">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="btn-group pull-right">
                                            <a href="#aa" data-toggle="tab" class=" btn green pull-right dropdown-toggle filters">Filter <i class="fa fa-angle-down"></i></a>
                                            <div class="tabbable">
                                                <div class="tab-content">
                                                    <div class="tab-pane filtertab" id="aa">
                                                        <div class="tabbable tabs-left" style="width:330px; padding-top:50px;">
                                                            <ul class="nav nav-tabs">
                                                                <li class="active"><a href="#a" data-toggle="tab">Brand</a></li>
                                                                <li><a href="#b" data-toggle="tab">Category</a></li>
                                                            </ul>
                                                            <div class="tab-content">
                                                                <div class="tab-pane active" id="a">
                                                                    <div class="scroller" style="height: 350px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                @foreach ($brands as $brand_each)
                                                                                <div class="checkbox-list">
                                                                                    <label class="checkbox-inline">
                                                                                        <input type="checkbox" id="inlineCheckbox1" name="brand" class="uncheck_brands" value="{{ $brand_each['brand_id'] }}"> {{ $brand_each['brand_name'] }}
                                                                                    </label>
                                                                                </div>
                                                                                @endforeach
                                                                                <br>
                                                                                <button type="button" class="btn btn-success" id="clear_all_brands">Clear All</button>
                                                                                <button type="button" class="btn btn-success" id="brand_apply">Apply</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane" id="b">
                                                                    <div class="scroller" style="height: 350px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                @foreach ($categories as $category_each)
                                                                                <div class="checkbox-list">
                                                                                    <label class="checkbox-inline">
                                                                                        <input type="checkbox" id="inlineCheckbox1" name="categories" class="uncheck_brands" value="{{ $category_each['category_id'] }}"> {{ $category_each['category_name'] }}
                                                                                    </label>
                                                                                </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="portlet-body">
                                <div class="scroller" style="height: 442px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list1">   
                                    <table id="sample_grid"></table>
                                </div>                    
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 col-sm-12" style="position:relative; top:300px;">
                        <a href="javascript:void(0)" class="btn btn-icon-only green moveLeft"><i class="fa fa-angle-double-right"></i></a>
                    </div>
                    <div class="col-md-7 col-sm-12">
                        <h3 class="form-section">Selected Products</h3>
                        <div class="portlet light tasks-widget">
                            <div class="portlet-body">
                                <div class="scroller" style="height: 500px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#505152" id="list2">                
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover" id="sample_3">
                                        <thead>
                                            <tr>
                                                <th>S No</th>
                                                <th>Product Details</th>
                                                <th>MRP</th>
                                                <th>Avl Qty</th>
                                                <th>Qty</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>                    

                            </div>
                        </div>
                        <!-- END PORTLET-->
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-center">
                        <button type="button" class="btn green-meadow" id="create_inbound">Create Inbound</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-center">
                        &nbsp;
                    </div>
                </div>

            </div>

        </div>
    </div>
    <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
</div>

@stop


@section('style')
<style type="text/css">
    /* custom inclusion of right, left and below tabs */

    .dataTables_filter{display:none;}
    .dataTables_length{display:none;}
    .dataTables_paginate .paging_bootstrap_number{display:none;}
    #sample_2_paginate{display:none;}
    #sample_3_paginate{display:none;}
    .dataTables_info{display:none;}

    .tabs-below > .nav-tabs,
    .tabs-right > .nav-tabs,
    .tabs-left > .nav-tabs {
        border-bottom: 0;
    }

    .tab-content > .tab-pane,
    .pill-content > .pill-pane {
        display: none;
    }
    .no-search .select2-search {
        display:none
    }

    .tab-content > .active,
    .pill-content > .active {
        display: block;
    }

    .tabs-below > .nav-tabs {
        border-top: 1px solid #ddd;
    }

    .tabs-below > .nav-tabs > li {
        margin-top: -1px;
        margin-bottom: 0;
    }

    .tabs-below > .nav-tabs > li > a {
    }

    .tabs-below > .nav-tabs > li > a:hover,
    .tabs-below > .nav-tabs > li > a:focus {
        border-top-color: #ddd;
        border-bottom-color: transparent;
    }

    .tabs-below > .nav-tabs > .active > a,
    .tabs-below > .nav-tabs > .active > a:hover,
    .tabs-below > .nav-tabs > .active > a:focus {
        border-color: transparent #ddd #ddd #ddd;
    }

    .tabs-left > .nav-tabs > li,
    .tabs-right > .nav-tabs > li {
        float: none;
    }

    .tabs-left > .nav-tabs > li > a,
    .tabs-right > .nav-tabs > li > a {
        min-width: 74px;
        margin-right: 0;
        margin-bottom: 3px;
    }

    .tabs-left > .nav-tabs {
        float: left;
        margin-right: 19px;
        border-right: 1px solid #ddd;
    }

    .tabs-left > .nav-tabs > li > a {
        margin-right: -1px;
    }

    .tabs-left > .nav-tabs > li > a:hover,
    .tabs-left > .nav-tabs > li > a:focus {
        border-color: #eeeeee #dddddd #eeeeee #eeeeee;
    }

    .tabs-left > .nav-tabs .active > a,
    .tabs-left > .nav-tabs .active > a:hover,
    .tabs-left > .nav-tabs .active > a:focus {
        border-color: #ddd transparent #ddd #ddd;
        *border-right-color: #ffffff;
    }

    .tabs-right > .nav-tabs {
        float: right;
        margin-left: 19px;
        border-left: 1px solid #ddd;
    }

    .tabs-right > .nav-tabs > li > a {
        margin-left: -1px;
    }

    .tabs-right > .nav-tabs > li > a:hover,
    .tabs-right > .nav-tabs > li > a:focus {
        border-color: #eeeeee #eeeeee #eeeeee #dddddd;
    }

    .tabs-right > .nav-tabs .active > a,
    .tabs-right > .nav-tabs .active > a:hover,
    .tabs-right > .nav-tabs .active > a:focus {
        border-color: #ddd #ddd #ddd transparent;
        *border-left-color: #ffffff;
    }
    .v-center {
        min-height:200px;
        display: flex;
        justify-content:center;
        flex-flow: column wrap;
    }

    .portlet.light {
        padding: 0px !important;
        background-color: #fff;
    }

    .ui-iggrid-header {
        text-align: center !important;
    }

    .ui-iggrid-tablebody td:nth-child(2), td:nth-child(4){
        text-align: center !important;
    }

    .ui-igcheckbox-normal {
        position: relative;
        left: -30%;
    }

    .ui-iggrid-filterddlist li, .ui-iggrid-hiding-dropdown-list li,
    .ui-iggrid-columnmoving-dropdown-list li, .ui-iggrid-summaries-dropdown-listcontainer li{
        padding: 0px 25px !important;
    }

    #sample_grid_dd_available_inventory{
        left: 145px !important;
        position: absolute !important;
        top: 51.017px !important;
    }

    /*/ Absolute Center Spinner /*/
    .loading {
        position: fixed;
        z-index: 999;
        height: 2em;
        width: 2em;
        overflow: show;
        margin: auto;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    /*/ Transparent Overlay /*/
    .loading:before {
        content: '';
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.3);
    }

    /*/ :not(:required) hides these rules from IE9 and below /*/
    .loading:not(:required) {
        /*/ hide "loading..." text /*/
        font: 0/0 a;
        color: transparent;
        text-shadow: none;
        background-color: transparent;
        border: 0;
    }

    .loading:not(:required):after {
        content: '';
        display: block;
        font-size: 10px;
        width: 1em;
        height: 1em;
        margin-top: -0.5em;
        -webkit-animation: spinner 1500ms infinite linear;
        -moz-animation: spinner 1500ms infinite linear;
        -ms-animation: spinner 1500ms infinite linear;
        -o-animation: spinner 1500ms infinite linear;
        animation: spinner 1500ms infinite linear;
        border-radius: 0.5em;
        -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
        box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
    }

    /*/ Animation /*/

    @-webkit-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @-moz-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @-o-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
</style>
<link href="{{ URL::asset('assets/global/plugins/uniform/css/uniform.default.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('userscript')
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>

<script>
$(function () {
    $("#sample_grid").igGrid({
        autoGenerateColumns: false,
        mergeUnboundColumns: false,
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        width: "100%",
        columns: [
            {headerText: "ProdID", key: "product_id", dataType: "number", width: "", hidden: true},
            {headerText: " ", key: "image", dataType: "image", width: "20%"},
            {headerText: "Product Details", key: "name", dataType: "string", width: "45%"},
            {headerText: "Aval. Qty", key: "available_inventory", dataType: "number", width: "35%"},
            {headerText: "ProdFlag", key: "product_flag", dataType: "number", width: "", hidden: true},
            {headerText: "Category", key: "category_id", dataType: "number", width: "", hidden: true},
            {headerText: "Brand", key: "brand_id", dataType: "number", width: "", hidden: true}
        ],
        dataSource: '/inbound/productsmongodb',
        responseDataKey: "results",
        features: [
            {
                name: "RowSelectors",
                enableCheckBoxes: true,
                enableRowNumbering: false
            },
            {
                name: 'Selection',
                multipleSelection: true
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'image', allowFiltering: false},
                ]
            },
            {
                name: 'Sorting',
                type: 'remote',
                persist: false
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
        primaryKey: 'product_id',
        height: '440px',
        initialDataBindDepth: 0,
        localSchemaTransform: false,
        rendered: function (evt, ui) {
            $("#sample_grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
            $("#sample_grid_dd_name").find(".ui-iggrid-filtericonequals").closest("li").remove();
            $("#sample_grid_dd_name").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();
            $("#sample_grid_dd_available_inventory").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
            $("#sample_grid_dd_available_inventory").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
        }
    });
});

count = 0;
$(".filters").click(function () {
    if (count % 2 == 0)
    {
        $(".tabbable").show();
    } else
    {
        $(".tabbable").hide();
    }
    count++;
});
$("#brand_apply").click(function () {
    $(".tabbable").hide();
});
$("#brand_apply1").click(function () {
    $(".tabbable").hide();
});
$("#brand_apply2").click(function () {
    $(".tabbable").hide();
});
$("#clear_all_brands").click(function () {
    $('.uncheck_brands').removeAttr('checked').closest('span').removeClass('checked');
});
$("#clear_all_categories").click(function () {
    $('.uncheck_categories').removeAttr('checked').closest('span').removeClass('checked');
});

$(document).ready(function (e) {
    var prod_tr = '<tr class="gradeX odd">\
                        <td data-key></td>\
                        <td data-val="product_details"></td>\
                        <td data-val="price"></td>\
                        <td data-val="qty"></td>\
                        <td><input type="text" class="form-control input-sm" value="1"></td>\
                        <td><a href="" class="btn btn-icon-only default delList"><i class="icon-trash"></i></a><input type="hidden" value="" /><input type="hidden" value="" /></td>\
                        </tr>';

    $('#sample_3').on('click', '.delList', function (e) {
        var product_id = $(this).closest("td").find("input[type='hidden']").val();
        var token_val = $("#token_value").val();
        $.ajax({
            type: "GET",
            url: "/inbound/updateproductid/" + product_id + "/withproductflag/0?_token=" + token_val,
            success: function (data)
            {
                console.log(data);
                $("#sample_grid").igGrid("dataBind");
            }
        });
        e.preventDefault();
        var el = $(this).closest('tr').data('html');
        el = '<tr class="gradeX odd" role="row">' + el + '</tr>';
        el = $(el);
        el.find('input:checkbox').prop('checked', false);
        el.find('span').removeClass('checked');
        $('#sample_2').find('tbody').append(el);
        $(this).closest('tr').remove();
        $('#sample_3').find('[data-key]').each(function (i) {
            $(this).text(++i);
        });
    });

    var product_array = new Array();
    $('.moveLeft').click(function (e) {
        $('.dataTables_empty').parent().remove();
        var rows = '';
        rows = $('#sample_grid').igGridSelection('selectedRows');
        var dataview = $('#sample_grid').data('igGrid').dataSource.dataView();
        for (i = 0; i < rows.length; i++) {
            var tr = $(this).closest('tr');
            var data = {};
            var product_id = dataview[rows[i].index]["product_id"];
            product_array.push(product_id);
            var product_details_1 = dataview[rows[i].index]["name"];
            data.price = product_details_1.split(':')[1].replace('MRP', '').trim();
            data.product_details = product_details_1.split('<br>')[0].trim();
            data.qty = dataview[rows[i].index]["available_inventory"];
            data.sku = dataview[rows[i].index]["sku"];
            var new_tr = $(prod_tr);
            var sNo = $('#sample_3').find('tbody').find('tr:not(".list-head")').length + 1;
            new_tr.find('td[data-key]').text(sNo);
            new_tr.find('td[data-val]').each(function () {
                var index = $(this).data('val');
                $(this).text(data[index] || '');
            });
            new_tr.find('td:eq(5) input:eq(0)').val(product_id);
            new_tr.find('td:eq(5) input:eq(1)').val(data.sku);
            new_tr.data('html', tr.html());
            $('#sample_3').append(new_tr);
            $("#sample_grid").igGridSelection("clearSelection", rows[i].index);
            var final_expression = [];
            for (k = 0; k < product_array.length; k++) {
                final_expression.push({fieldName: 'product_id', expr: product_array[k], cond: 'doesNotEqual'});
            }
        }

        var new_table = $("#sample_3 tbody");
        new_table.find('tr').each(function (i) {
            var $new_tds = $(this).find('td');
            var product_id = $new_tds.eq(5).find('input:eq(0)').val();
            var token_val = $("#token_value").val();
            $.ajax({
                type: "GET",
                url: "/inbound/updateproductid/" + product_id + "/withproductflag/1?_token=" + token_val,
                success: function (data)
                {
                    console.log(data);
                    $("#sample_grid").igGrid("dataBind");
                }
            });
        });
    });
});

$('#create_inbound').click(function () {
        var table = $("#sample_3 tbody");
        var product_details = new Array();
        table.find('tr').each(function (i) {
            var $tds = $(this).find('td');
            var product_detail = {
                'product_id': $tds.eq(5).find('input:eq(0)').val(),
                'sku': $tds.eq(5).find('input:eq(1)').val(),
                'product_qty': $tds.eq(4).find('input').val(),
                'product_name': $tds.eq(1).text(),
                'product_avl_qty': $tds.eq(3).text()
            };
            product_details.push(product_detail);
        });
        var stn_Doc = $("#stn_docss")[0].files[0];

        
        if(stn_Doc == "" ||  stn_Doc == null )
        {
            alert("Please choose a file");
            $("#stn_docss").focus();
            return false;
        }

        if(stn_Doc != "" ||  stn_Doc != null)
        {
            var filesize = ($("#stn_docss")[0].files[0].size/1024/1024).toFixed(2);
        }
        var ext = (stn_Doc.name).split('.').pop();
        if(filesize > 4 )
        {
            alert("Your upload file should be less than 4 MB in size");
            $("#stn_docss").focus();
            return false;
        }
        if( ext != "txt" && ext!='pdf' && ext!='doc' && ext!='docx' && ext!='csv' && ext!='xls' && ext!='xlsx' && ext!='jpg' && ext!='jpeg' && ext!='png')
        {
            alert("Please Choose the valid STN file type");
            $("#stn_docss").focus();
            return false;
        }
        var formData = new FormData();
        formData.append('stn_docs', stn_Doc);
        formData.append('request_type', $('input[name = request_status]').val());

        formData.append('request_type', $('input[name = request_status]').val());
        formData.append('request_status', $('input[name = request_status]').val());
        formData.append('pick_up', $('select[name = pickup_location]').val());
        formData.append('delivery_location', $('select[name = delivery_location]').val());
        formData.append('time_slots', $('select[name = time_slots]').val());
        formData.append('stn_number', $('input[name = stn_number]').val());
        formData.append('product_details', JSON.stringify(product_details));
        
        var token = $("#token_value").val();
        $.ajax({
            type: "POST",
            url: "/inbound/create?_token=" + token,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('#loadingmessage').addClass('loading');
            },
            complete: function () {
                $('#loadingmessage').removeClass('loading');
            },
            success: function (data)
            {
                var res = JSON.parse(data);
                if(res.status == 'failed')
                {
                    alert(res.message);return false;
                }
                else if(res.status == 'success')
                {
                    alert(res.message);
                    window.location.href = "index";
                }
                
            },failed:function (data)
            {
                alert("failed");
            }
        });
    });

$('#brand_apply').click(function () {
    var brands = [];
    $.each($('input[name = brand]:checked'), function () {
        brands.push($(this).val());
    });
    var categories = [];
    $.each($('input[name = categories]:checked'), function () {
        categories.push($(this).val());
    });
    var filterURL = "/inbound/productsmongodb?brandid=" + brands + "&categoryid=" + categories;
    ds = new $.ig.DataSource({
        type: "json",
        responseDataKey: "results",
        dataSource: filterURL,
        callback: function (success, error) {
            if (success) {
                $("#sample_grid").igGrid({
                    dataSource: ds,
                    autoGenerateColumns: false
                });
            } else {
                alert(error);
            }
        },
    });
    ds.dataBind();
});
</script>

<script src="{{ URL::asset('assets/global/plugins/datatables/media/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-file-upload/js/jquery.fileupload-audio.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-file-upload/js/jquery.fileupload-video.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-file-upload/js/jquery.fileupload-ui.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/table-managed.js') }}" type="text/javascript"></script>

<script>
jQuery(document).ready(function () {
    Metronic.init(); // init metronic core componets
    Layout.init(); // init layout
    Demo.init(); // init demo features
    QuickSidebar.init(); // init quick sidebar
    TableManaged.init();
    //Index.init(); // init index page
    //FormFileUpload.init();
    Tasks.initDashboardWidget(); // init tash dashboard widget  
});
</script>
@stop
@extends('layouts.footer')