function productTax(productId)
{
    if ($.trim($('.productmappingdetailss').html()) != '')
    {
        $('.productmappingdetailss').igHierarchicalGrid('destroy');
    }

    $('.productmappingdetailss').igHierarchicalGrid({
        dataSource: '/tax/onlystatenames?country_id=99',
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'states',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [
            {headerText: "States", key: "name", dataType: "string", width: "50%"},
            {headerText: "Actions", key: "mappingactions", dataType: "string", width: "50%"}
        ],
        columnLayouts: [
            {
                dataSource: '/tax/alltaxcodesbystateproductid?productId=' + productId,
                autoGenerateColumns: false,
                mergeUnboundColumns: false,
                responseDataKey: 'result',
                generateCompactJSONResponse: false,
                enableUTCDates: true,
                columns: [
                    {headerText: "Tax Class Code", key: "tax_class_code", dataType: "string", width: "30%"},
                    {headerText: "Tax Rate", key: "tax_percentage", dataType: "string", width: "15%"},
                    {headerText: "Tax Type", key: "tax_class_type", dataType: "string", width: "15%"},
                    {headerText: "Effective Date", key: "date_start", dataType: "date", width: "30%"},
                    {headerText: "Status", key: "mappingstatus", dataType: "string", width: "30%"}
                ],
                primaryKey: 'map_id',
                width: '100%',
                height: '200px',
                initialDataBindDepth: 0,
                localSchemaTransform: false
            }],
        primaryKey: 'zone_id',
        width: '100%',
        height: '430px',
        initialDataBindDepth: 0,
        localSchemaTransform: false,
        rendered: function (evt, ui) {
        }
    });

    call(productId);

}

var productIdGlobal='';
    $('#create-mapping').on('shown.bs.modal', function (e) {
        var token = $("#token_value").val();
        var stateid = $(e.relatedTarget).data('stateid');
        $.ajax({
            type: "GET",
            url: "/tax/avaliabletaxesforstateandproduct?_token=" + token,
            data: "product_id=" + productIdGlobal + "&state_id=" + stateid,
            dataType: "json",
            success: function (data)
            {
                if (data == undefined || data == null || data.length == 0 || (data.length == 1 && data[0] == ""))
                {
                    $('#error').css("display", "none");
                    $('#nodata').css("display", "block");
                    $('#savetax-mapping').prop("disabled", true);
                    $('#assign-maping').prop($('<option>', {
                        value: "",
                        text: "No Data"
                    }));
                } else {
                    $('#error').css("display", "none");
                    $('#nodata').css("display", "none");
                    $('#savetax-mapping').prop("disabled", false);
                    $('#assign-maping')
                            .find('option')
                            .remove()
                            .end().val('');
                    $('#assign-maping').append($('<option>', {
                            value: "novalue",
                            text: "Select Tax Class"
                        }));
                    $.each(data, function (index, value) {
                        $('#assign-maping').append($('<option>', {
                            value: index + "_" + productIdGlobal,
                            text: value
                        }));
                    });
                }
            }
        });
    });


function call(productId) {
    productIdGlobal = productId;
}

function statuschaging(id)
{
    var token = $("#token_value").val();
    $.ajax({
        type:"POST",
        url:"/tax/statusupdateformapping?_token=" + token,
        data:'map_id='+id,
        success: function(data)
        {
            alert(data);
            var firstChildGrid = $('.productmappingdetailss').igHierarchicalGrid('allChildrenWidgets')[0];
            firstChildGrid.dataBind();
        }
    });
}

$("#savetax-mapping").click(function(){
    var token = $("#token_value").val();
    var taxmapId = $("#assign-maping").val();
    if(taxmapId == 'novalue'){
        $('#error').css("display", "block");
    } else {
        $('#error').css("display", "none");
    }
    var arr = taxmapId.split('_');
    var productId = arr[1];
    var taxclassId = arr[0];
    $.ajax({
        type:"POST",
        url:"/tax/taxMapByPermissionGrid?_token=" + token,
        data:"productId="+productId+"&taxclassId="+taxclassId,
        success:function(data)
        {
            console.log("chk data 34"+data+" test");
            $('#create-mapping').modal('toggle');
            $(".productmappingdetailss").igHierarchicalGrid("dataBind");
        }
    });
});