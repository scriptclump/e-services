<style>
    .modal-body {
        display: -webkit-inline-box;
    }
    .row {
        margin-left: 15px !important;
        margin-right: 15px !important;
    }

    .ui-autocomplete{ z-index: 99999999 !important;
                      width : 250px;}
    #ui-id-1{ z-index: 99999999 !important;}
</style>
<div class="row">

    <div class="table-container">

        <div class="actions pull-right">
            <div data-toggle="buttons" class="btn-group btn-group-devided" style="position:relative; z-index:99999999">
                <a href="#myModal3" role="button" id="upload_categories" class="btn grey-salsa btn-sm" data-toggle="modal">Map New Category</a>
            </div>
        </div>
        <div class="row" >
            <div class="col-md-12">
                <table id="hierarchicalGrid1"></table>                  
            </div>
        </div>



        <div id="myModal3" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">CATEGORY MAPPING</h4>
                    </div>
                    <div class="">
                        <form class="eventInsForm" id="channel_category_map" method="post">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Channel Categories *</label>
                                        <input type="hidden" name="hidden-channel_id" id="hidden-channel_id" value="">
                                        <input id= "getchannel_categories2" name= "category_name" type="text" class="form-control select2me">
                                        <input id= "channel_category_id" name= "channel_category_name" type="hidden" form="form-control ">
                                        <input type="hidden" name="hidden-ebutor_id" id="hidden-ebutor_id" value="">
                                        <input type="hidden" name="channel_id" value="">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Ebutor Categories *</label>
                                        <input type="hidden" name="hidden-catid" id="hidden-catid" value="0">
                                        <input id="getebutor_categories2" name="ebutor_category_name" type="text" class="form-control "> <!-- <br>
                                        <!-- <select name="ebutor_category_name" style="width: 250px; height: 35px;">
                                            @foreach ($data['ebutor_cat'] as $key => $val)
      <option value="{{$key}}">{{$val->cat_name}}</option>
      @endforeach
      
    </select> -->
                                    </div>
                                </div>
                            </div>





                            <div id="results"></div>

                            <div class="row">
                                <div class="col-md-12 text-center" style="margin:20px 0px;">
                                    <input type="submit" id="ebutor_category_name" name="ebutor_catego r_map" value="Add" class="btn btn-info">
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>



    </div>
</div>
@section('script')
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/northwind.js') }}" type="text/javascript"></script> 
<script>
$(document).ready(function () {
    $('#hierarchicalGrid1').igHierarchicalGrid({
        dataSource: '/channel/getTreechannelcategorymapping',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'Records',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [
            {headerText: "ID", key: "id", dataType: "number", width: "15%"},
            {headerText: "CategoryID", key: "mp_category_id", dataType: "number", width: "20%"},
            {headerText: "CategoryName", key: "category_name", dataType: "string", width: "40%"},
            {headerText: "Leaf", key: "is_leaf_category", dataType: "bool", width: "15%"}
        ],
        columnLayouts: [
            {
                dataSource: '/channel/getTreechannelcategorymapping',
                autoGenerateColumns: false,
                autoGenerateLayouts: false,
                mergeUnboundColumns: false,
                responseDataKey: 'Records',
                generateCompactJSONResponse: false,
                enableUTCDates: true,
                columns: [
                    {headerText: "ID", key: "id", dataType: "number", width: "15%"},
                    {headerText: "CategoryID", key: "mp_category_id", dataType: "number", width: "20%"},
                    {headerText: "CategoryName", key: "category_name", dataType: "string", width: "40%"},
                    {headerText: "Leaf", key: "is_leaf_category", dataType: "bool", width: "15%"}
                ],
                columnLayouts: [
                    {
                        dataSource: '/channel/getTreechannelcategorymapping',
                        autoGenerateColumns: false,
                        autoGenerateLayouts: false,
                        mergeUnboundColumns: false,
                        responseDataKey: 'Records',
                        generateCompactJSONResponse: false,
                        enableUTCDates: true,
                        columns: [
                            {headerText: "ID", key: "id", dataType: "number", width: "15%"},
                            {headerText: "CategoryID", key: "mp_category_id", dataType: "number", width: "20%"},
                            {headerText: "CategoryName", key: "category_name", dataType: "string", width: "40%"},
                            {headerText: "Leaf", key: "is_leaf_category", dataType: "bool", width: "15%"}
                        ],
                        key: 'Products',
                        foreignKey: 'mp_category_id',
                        primaryKey: 'id',
                        width: '100%'
                    }],
                key: 'Products',
                foreignKey: 'mp_category_id',
                primaryKey: 'id',
                width: '100%'
            }],
        features: [
            {
                name: "Filtering",
                type: "local",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {
                        columnKey: "category_name",
                        condition: "startsWith"

                    }]
            },
            {
                name: 'Paging',
                type: 'local',
                inherit: true,
                pageSize: 5

            },
            {
                name: 'Sorting',
                type: 'local',
                persist: false

            }
        ],
        primaryKey: 'id',
        width: '100%',
        initialDataBindDepth: 0,
        localSchemaTransform: false
    });
    $("#getchannel_categories2").autocomplete({
        minLength: 1,
        source: '/channel/getchannelcategories',
        appendTo: myModal3,
        select: function (event, ui) {
            var label = ui.item.label;
            $("#hidden-channel_id").val(ui.item.mp_category_id);
            var categoryid = $("#hidden-channel_id").val();

            $.ajax({
                url: '/channel/getchannelattributes',
                data: 'category_name=' + label + '&categoryid=' + categoryid,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    //get_channel_category_charges(channel_id);
                    //alert(data.featureName)
                    $("#results").html('');
                    if (data.length == 0) {
                        alert('there is no data available with this category');
                        $("#results").hide();
                    } else {
                        $("#results").show();
                        var accord = '';
                   // $("#results")




                    }
                },
                fail: function (data) {
                    //alert("failed");
                }
            });
        }
    });


    $("#getebutor_categories2").autocomplete({
        minLength: 1,
        source: '/channel/getebutorcategories',
        appendTo: myModal3,
        select: function (event, ui) {
            var label = ui.item.label;
            $("#hidden-catid").val(ui.item.cat_name);
            $.ajax({
                url: '/channel/getebutorattributes',
                data: 'category_name=' + label,
                type: 'GET',
                success: function (data) {
                    //get_chssannel_category_charges(channel_id);
                    console.log(data);
                    alert('data');
                },
                fail: function (data) {
                    //alert("failed");
                }
            });
            //var samp = $("#hidden-catid").val();
            //alert(label)
            /*$.post('channel/getebutorattributes', // endpoint URL
             { someParameterToTransmit: label  }, // some data to transmit
             // on complete function
             ); // post*/
        } // on select (autocomplete)
    });
    function attributeaccord() {
        //alert('heya');
    }


});

</script>
@stop


