function productTax(productId)
//$(function ()
{
    $('.productmappingdetailss').igGrid({
        dataSource: '/tax/getTaxMappingStateWise?productId='+productId,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'result',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [
            {headerText: "Tax Type", key: "tax_type", dataType: "string", width: "200px", hidden: "true"},
            {headerText: "All States", key: "_4035", dataType: "object", width: "200px", formatter: allFormatCombo},
            {headerText: "TS", key: "_4033", dataType: "object", width: "200px", formatter: wbFormatCombo},
            {headerText: "AN", key: "_1475", dataType: "object", width: "200px", formatter: anFormatCombo},
            {headerText: "AP", key: "_1476", dataType: "object", width: "200px", formatter: apFormatCombo},
            {headerText: "AR", key: "_1477", dataType: "object", width: "200px", formatter: arFormatCombo},
            {headerText: "AS", key: "_1478", dataType: "object", width: "200px", formatter: asFormatCombo},
            {headerText: "BI", key: "_1479", dataType: "object", width: "200px", formatter: biFormatCombo},
            {headerText: "CH", key: "_1480", dataType: "object", width: "200px", formatter: chFormatCombo},
            {headerText: "DA", key: "_1481", dataType: "object", width: "200px", formatter: daFormatCombo},
            {headerText: "DM", key: "_1482", dataType: "object", width: "200px", formatter: dmFormatCombo},
            {headerText: "DE", key: "_1483", dataType: "object", width: "200px", formatter: deFormatCombo},
            {headerText: "GO", key: "_1484", dataType: "object", width: "200px", formatter: goFormatCombo},
            {headerText: "GU", key: "_1485", dataType: "object", width: "200px", formatter: guFormatCombo},
            {headerText: "HA", key: "_1486", dataType: "object", width: "200px", formatter: haFormatCombo},
            {headerText: "HP", key: "_1487", dataType: "object", width: "200px", formatter: hpFormatCombo},
            {headerText: "JA", key: "_1488", dataType: "object", width: "200px", formatter: jaFormatCombo},
            {headerText: "KA", key: "_1489", dataType: "object", width: "200px", formatter: kaFormatCombo},
            {headerText: "KE", key: "_1490", dataType: "object", width: "200px", formatter: keFormatCombo},
            {headerText: "LI", key: "_1491", dataType: "object", width: "200px", formatter: liFormatCombo},
            {headerText: "MP", key: "_1492", dataType: "object", width: "200px", formatter: mpFormatCombo},
            {headerText: "MA", key: "_1493", dataType: "object", width: "200px", formatter: maFormatCombo},
            {headerText: "MN", key: "_1494", dataType: "object", width: "200px", formatter: mnFormatCombo},
            {headerText: "ME", key: "_1495", dataType: "object", width: "200px", formatter: meFormatCombo},
            {headerText: "MI", key: "_1496", dataType: "object", width: "200px", formatter: miFormatCombo},
            {headerText: "NA", key: "_1497", dataType: "object", width: "200px", formatter: naFormatCombo},
            {headerText: "OR", key: "_1498", dataType: "object", width: "200px", formatter: orFormatCombo},
            {headerText: "PO", key: "_1499", dataType: "object", width: "200px", formatter: poFormatCombo},
            {headerText: "PU", key: "_1500", dataType: "object", width: "200px", formatter: puFormatCombo},
            {headerText: "RA", key: "_1501", dataType: "object", width: "200px", formatter: raFormatCombo},
            {headerText: "SI", key: "_1502", dataType: "object", width: "200px", formatter: siFormatCombo},
            {headerText: "TN", key: "_1503", dataType: "object", width: "200px", formatter: tgFormatCombo},
            {headerText: "TR", key: "_1504", dataType: "object", width: "200px", formatter: tnFormatCombo},
            {headerText: "UP", key: "_1505", dataType: "object", width: "200px", formatter: trFormatCombo},
            {headerText: "UT", key: "_4036", dataType: "object", width: "200px", formatter: upFormatCombo},
            {headerText: "WB", key: "_1506", dataType: "object", width: "200px", formatter: upFormatCombo}
        ],
        features: [
            {
                name: "ColumnFixing",
                fixingDirection: "left",
                columnSettings: [
                    /*{
                        columnKey: "tax_type",
                        isFixed: false,
                        allowFixing: false
                    },*/
                    {
                        columnKey: "_4035",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1475",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1476",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1477",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1478",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1479",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1480",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1481",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1482",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1483",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1484",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1485",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1486",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1487",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1488",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1489",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1490",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1491",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1492",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1493",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1494",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1495",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1496",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1497",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1498",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1499",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1500",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1501",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1502",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1503",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1504",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1505",
                        allowFixing: false
                    },
                    {
                        columnKey: "_1506",
                        allowFixing: false
                    },
                    {
                        columnKey: "_4033",
                        allowFixing: false
                    }
                ]

            },
        ],
        ///Old settings
        primaryKey: 'tax_type',
        width: '100%',
        //height: '770px',
        initialDataBindDepth: 0,
        localSchemaTransform: false
    });

// JS for Left side IGGRID Table

$("#taxTypes").igGrid({
               columns: [
                   { headerText: "Tax Type", key: "master_lookup_name", dataType: "string" },
                   { headerText: "Action", key: "actions", dataType: "string" }
               ],
                //width: '100%', 
                //height: '770px',
               dataSource: "/tax/getAllTaxTypes?prod_id="+productId,
           });



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

    function approveall(stateid, ProductID)
    {
        var token = $("#token_value").val();
       $.ajax({
        url:"/tax/approvealltaxes?_token=" + token,
        type:"POST",
        data:"stateId="+stateid+"&prodId="+ProductID,
        success:function(data)
        {
            // $("#productmappingdetailss").
            $("#productmappingdetailss").igGrid("dataBind");
        }
       });
    }


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
            
            $('#create-mapping').modal('toggle');
            $(".productmappingdetailss").igHierarchicalGrid("dataBind");
        }
    });
});

$('#mapping-board').on('shown.bs.modal', function (e) {
    $("#hsn_codes_auto_search").val("");
    var taxtype = $(e.relatedTarget).data('id');
    var productId = $(e.relatedTarget).data('productid');
    $('#savetax-mapping-prods').prop("disabled", true);
    var token = $("#token_value").val();
    $.ajax({
        type: "GET",
        url: "/tax/allStates?country_id=99&_token=" + token,
        dataType: "json",
        success: function (data)
        {
           $('#states').html("");
            $('#states').html("<option value=''>Select State</option>");

            $('#assign-maping-rules').html("");
            $('#assign-maping-rules').html("<option value=''>Select Tax Class</option>");
            $('#assign-maping-rules').prop("disabled", true);

           for(var i=0; i < data.states.length; i++)
           {

                $('#states').append(
                    $("<option></option>")
                      .attr("value", taxtype+"_"+data.states[i].zone_id+"_"+productId)
                      .text(data.states[i].name)
                );

           }
        }
    });
    $.ajax({
        type: 'GET',
        url: '/tax/hsncodes?type=All&productid=' + productId + '&_token=' + token,
        dataType: "json",
        success: function (data)
        {
            console.log("dataaa  chk "+data);
            if(data.exist === 'yes'){
                $('#hsncodes_sel').hide();
                $('#hsncodes').show();
                $('#hsncodes').html('');
                $('#hsncodes').append(
                        $('<input></input>')
                        .attr('id', 'hsn_code')
                        .attr('value', data.hsn_codes[0].ITC_HSCodes)
                        .attr('disabled', 'disabled')
                        .attr('class', 'form-control')
                        );
                
                $('#hsn_desc_label').html('<b>Description</b>');
                $('#hsn_desc_span').html('');
                $('#hsn_desc_span').append(
                        $('<textarea></textarea>')
                        .attr('id', 'hsn_desc_textarea')
                        .attr('wrap', 'hard')
                        .attr('disabled', 'disabled')
                        .attr('class', 'form-control')
                        .text(data.hsn_codes[0].HSC_Desc)
                        );
                
                $('#hsn_percentage_label').html('<b>Tax %</b>');
                $('#hsn_percentage_span').html('');
                $('#hsn_percentage_span').append(
                        $('<input></input>')
                        .attr('id', 'hsn_percentage')
                        .attr('value', data.hsn_codes[0].tax_percent)
                        .attr('disabled', 'disabled')
                        .attr('class', 'form-control')
                        );
            } else if(data.exist === 'no'){
                $('#hsncodes').hide();
                $('#hsncodes_sel').show();
                $('#hsn_codes_select').html('');
                $('#hsn_codes_select').html("<option value=''>Select HSN Code</option>");
                $('#hsn_desc_label').html('');
                $('#hsn_desc_span').html('');
                $('#hsn_percentage_label').html('');
                $('#hsn_percentage_span').html('');
                
                for (var i = 0; i < data.hsn_codes.length; i++) {
                    $('#hsn_codes_select').append(
                        $("<option></option>")
                        .attr("value", data.hsn_codes[i].ITC_HSCodes)
                        .text(data.hsn_codes[i].ITC_HSCodes)
                    );
                }
            }
        }
    });
});


$("#states").on('change', function () {


    $('#assign-maping-rules').prop("disabled", false);
    var selectboxval = $("#states").val();
    var Splitarr = selectboxval.split("_"); 
    var taxtype = Splitarr[0];
    var stateid = Splitarr[1];
    var product_id  = Splitarr[2];

    if(selectboxval == "")
    {
        $('#assign-maping-rules').prop("disabled", true);
    }
    else
    {
        var token = $("#token_value").val();
        $('#assign-maping-rules').html("");
        $('#assign-maping-rules').html("<option value=''>Select Tax Class</option>");

        $.ajax({
            type: "GET",
            url: "/tax/getavailabletaxesbyproductId?_token=" + token,
            data:"taxtype="+taxtype+"&stateId="+stateid+"&product_id="+product_id,
            dataType: "json",
            success: function (data)
            {
                if(data == "")
                {
                    $('#assign-maping-rules').prop("disabled", true);
                    $('#savetax-mapping-prods').prop("disabled", true);
                }
                else{
                    $('#savetax-mapping-prods').prop("disabled", true);
                }
               $.each(data, function(key, value){
                         $('#assign-maping-rules').append(
                        $("<option></option>")
                          .attr("value", key)
                          .text(value)
                    );
                    })
            }
        }); 
    }

    
});

/*$('#hsn_codes_select').on('change', function () {
    var selectboxval = $('#hsn_codes_select').val(),
            token = $('#token_value').val();
    if(selectboxval != ""){
        $.ajax({
            type: 'GET',
            url: '/tax/hsncodes?type=' + selectboxval + '&productid=NA&_token=' + token,
            dataType: "json",
            success: function (data)
            {
                $('#hsn_desc_label').html('<b>Description</b>');
                $('#hsn_desc_span').html('');
                $('#hsn_desc_span').append(
                        $('<textarea></textarea>')
                        .attr('id', 'hsn_desc_textarea')
                        .attr('wrap', 'hard')
                        .attr('disabled', 'disabled')
                        .attr('class', 'form-control')
                        .text(data.hsn_codes[0].HSC_Desc)
                        );
                $('#hsn_percentage_label').html('<b>Tax %</b>');
                $('#hsn_percentage_span').html('');
                $('#hsn_percentage_span').append(
                        $('<input></input>')
                        .attr('id', 'hsn_percentage')
                        .attr('value', data.hsn_codes[0].tax_percent)
                        .attr('disabled', 'disabled')
                        .attr('class', 'form-control')
                        );
            }
        });
    } else {
        $('#hsn_desc_label').html('');
        $('#hsn_desc_span').html('');
        $('#hsn_percentage_label').html('');
        $('#hsn_percentage_span').html('');
    }

    var availabletaxes = $("#assign-maping-rules").val();
    if (availabletaxes == "" && selectboxval == "") {
        $('#savetax-mapping-prods').prop("disabled", true);
    } else if(availabletaxes == ""){
        $('#savetax-mapping-prods').prop("disabled", true);
    } else if(selectboxval == ""){
        $('#savetax-mapping-prods').prop("disabled", true);
    } else {
        $('#savetax-mapping-prods').prop("disabled", false);
    }
});*/


// /tax/hsncodes?type=' + selectboxval + '&productid=NA&_token=' + token

function autosuggest(){ 
        $("#hidden_hsn_code").val('');     
        var token = $("#token_value").val();
        $( "#hsn_codes_auto_search" ).autocomplete({
             source: '/tax/gethsninfo?_token=' + token,
             minLength: 2,
             params: { entity_type:$('#supplier_list').val() },
             select: function( event, ui ) {
                  if(ui.item.label=='No Result Found'){
                     event.preventDefault();
                  }

               $("#hidden_hsn_code").val(ui.item.label);   
             }
         });
    }

    $("#hsn_codes_auto_search").blur(function(){
        console.log("testing");
        var HSNCode = $("#hsn_codes_auto_search").val();  //This is from what we selected into auto search field
        var hsncode = $("#hidden_hsn_code").val();   //This is from Hidden HSN field(When a value select into that auto search then hidden will upddate)
        var states = $("#states").val();
        var available_tax_classes = $("#assign-maping-rules").val();
        var token = $('#token_value').val();
        if( HSNCode != ""){
            if(states == "" && available_tax_classes == "")
            {
                $('#savetax-mapping-prods').prop("disabled", true);
            }
            $("#hsnallinfo").css("display","none");
            $.ajax({
                type: 'GET',
                url: '/tax/hsncodes?type=' + HSNCode + '&productid=NA&_token=' + token,
                dataType: "json",
                success: function (data)
                {
                    if(data.hsn_codes != "")
                    {
                        var desc = data.hsn_codes[0].HSC_Desc;
                        var tax_percnt = data.hsn_codes[0].tax_percent;        
                    }else{
                        var desc = "";    
                        var tax_percnt = "";    
                    }

                    $('#hsn_desc_label').html('<b>Description</b>');
                    $('#hsn_desc_span').html('');
                    $('#hsn_desc_span').append(
                            $('<textarea></textarea>')
                            .attr('id', 'hsn_desc_textarea')
                            .attr('wrap', 'hard')
                            .attr('disabled', 'disabled')
                            .attr('class', 'form-control')
                            .text(desc)
                            );
                    $('#hsn_percentage_label').html('<b>Tax %</b>');
                    $('#hsn_percentage_span').html('');
                    $('#hsn_percentage_span').append(
                            $('<input></input>')
                            .attr('id', 'hsn_percentage')
                            .attr('value', tax_percnt)
                            .attr('disabled', 'disabled')
                            .attr('class', 'form-control')
                            );
                }
            });
        }else{
            if(states != "" && available_tax_classes != "")
            {
                $('#savetax-mapping-prods').prop("disabled", false);
            }
        $("#hsnallinfo").show();
        $('#hsn_desc_label').html('');
        $('#hsn_desc_span').html('');
        $('#hsn_percentage_label').html('');
        $('#hsn_percentage_span').html('');
}
    });

function validateTaxForm(){
    var availabletaxes = $("#assign-maping-rules").val(),
    hsnCode = '';
    var tax_effective_date = $('#tax_effective_date').val();
    if ($('#hsncodes_sel').css("display") === "none") {
        hsnCode = $("#hsn_code").val();
    } else if ($('#hsncodes').css("display") === "none") {
        hsnCode = $("#HSNCode").val();
    }

    if (availabletaxes == ""  || hsnCode=="" || tax_effective_date=="") {
        $('#savetax-mapping-prods').prop("disabled", true);
    }
    else {
        $('#savetax-mapping-prods').prop("disabled", false);
    }
};

$('#assign-maping-rules').on('change',function(){
    validateTaxForm();
});
$('#hsn_codes_auto_search').on('change',function(){
    validateTaxForm();
});
$('#tax_effective_date').on('keyup',function(){
    validateTaxForm();
});
$('#tax_effective_date').on('change',function(){
    validateTaxForm();
});
$('#states').on('change',function(){
    validateTaxForm();
})

$("#mapping-board").on('hide.bs.modal', function () {
    $("#hidden_hsn_code").val("");

});

$('#tax_effective_date').datepicker({
    autoclose: true,
    dateFormat: 'yy-mm-dd'
});

$("#savetax-mapping-prods").on('click', function(){
    var token = $("#token_value").val();
    var states = $("#states").val();
    var prodid = states.split("_");
    var productId = prodid[2];
    var stateid = prodid[1];
    var taxclassId = $("#assign-maping-rules").val();
    var hsnCode = '';
    if($('#hsncodes_sel').css("display") === "none"){
        hsnCode = $("#hsn_code").val();
    } else if($('#hsncodes').css("display") === "none"){
        hsnCode = $("#hsn_codes_auto_search").val();
    }
    if(hsnCode == ""){
        $('#savetax-mapping-prods').prop("disabled", true);
        alert('Please fill HSN code!');
        return false;
    }
    var effectiveDate = $("#tax_effective_date").val();
    if(effectiveDate == ""){
        $('#savetax-mapping-prods').prop("disabled", true);
        alert('Please fill effective date!');
        return false;
    }
    if(stateid == ""){
        $('#savetax-mapping-prods').prop("disabled", true);
        alert('Please fill state!');
        return false;
    }
   // $('#savetax-mapping-prods').prop("disabled", true);
    $.ajax({
        type: "GET",
        url: "/tax/taxMapByPermissionGrid?_token=" + token,
        data:"productId="+productId+"&taxclassId="+taxclassId+"&hsnCode="+hsnCode+"&effectiveDate="+effectiveDate+"&stateid="+stateid,
        success: function (data)
        {
            if(data == "no permission")
            {
                $('#mapping-board').modal('toggle');
                $("html, body").animate({ scrollTop: 0 }, 600);//scroll to top automatically
                $("#work_flow_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>You are not permitted for this action</div></div>');
                
                $(".alert-danger").fadeOut(30000);
            } else if(data == "pending approval"){
                $('#mapping-board').modal('toggle');
                $("html, body").animate({ scrollTop: 0 }, 600);//scroll to top automatically
                $("#work_flow_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>One of the tax class for this product not yet approved.</div></div>');
                $(".alert-danger").fadeOut(30000);
            } else if(data == "effective date"){
                $('#mapping-board').modal('toggle');
                $("html, body").animate({ scrollTop: 0 }, 600);//scroll to top automatically
                $("#work_flow_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>With the same effective date already a tax class has been applied to this product.</div></div>');
                $(".alert-danger").fadeOut(30000);
            } else if(data == "already done"){
                $('#mapping-board').modal('toggle');
                $("html, body").animate({ scrollTop: 0 }, 600);//scroll to top automatically
                $("#work_flow_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>Request already raised, it is in approval process.</div></div>');
                $(".alert-danger").fadeOut(30000);
            } else if(data == "hsn_error"){
                //alert("HSN Code Mapping was not found in hsn master");
                $('#mapping-board').modal('toggle');
                $("html, body").animate({ scrollTop: 0 }, 600);//scroll to top automatically
                $("#work_flow_message").html('<div class="flash-message"><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>HSN code does not exist in hsn master.</div></div>');
                $(".alert-danger").fadeOut(30000);
            }else {
                alert("Your Mapping was successfully done");
                $('#mapping-board').modal('toggle');
                $("#productmappingdetailss").igGrid("dataBind");    
            }            
        }
    });
});