@if(count($productArr))
<form id="order_return_form"  method="POST">
    <div class="tabbable-line">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="portlet">
                    <h4>Order Items</h4>
                    <div class="portlet-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped" id="returnTbl">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" name="retchk[]" class="check_box" onclick="checkAll(this)"></th>
                                        <th> SKU# </th>
                                        <th> Product Name </th>
                                        <th> Invoiced QTY </th>
                                        <th> Returned QTY </th>
                                        <th> Good QTY </th>
                                        <th> Bad QTY </th>
                                        <th> DIT  QTY </th>
                                        <th> Missing QTY </th>
                                        <th> Excess QTY </th>
                                        <th> Return Reason </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productArr as $k=>$product)
                                    @if((int)$product->qty > 0)
                                    <tr>
                                        <td><input type="checkbox" class="check_box" name="retchk[{{$product->product_id}}]" data-attrib="{{$product->product_id}}" id="retchk{{$product->product_id}}"></td>
                                        <td>{{$product->sku}}</td>
                                        <td>{{$product->product_title}}</td>
                                        <td>{{$product->qty}}</td>
                                        <td>
                                        <input type="number"  min="0" max="{{$product->qty}}" value="0" name="return_qty[{{$product->product_id}}]" id="return_qty{{$product->product_id}}" onchange="qtyCalculation({{$product->product_id}})" class="return_qty">                                        
                                        </td>
                                        <td>                                          
                                            <input type="number"  min="0" max="0" value="0" name="good_qty[{{$product->product_id}}]" id="good_qty{{$product->product_id}}" onchange="getGoodQty({{$product->product_id}})">
                                        </td>
                                        <td>
                                            <input type="number"  min="0" max="0" value="0" name="bad_qty[{{$product->product_id}}]" id="bad_qty{{$product->product_id}}" onchange="getBadQty({{$product->product_id}})">
                                        </td>
                                        <td>
                                            <input type="number"  min="0" max="0" value="0" name="dit_qty[{{$product->product_id}}]" id="dit_qty{{$product->product_id}}" class="dit_qty" onchange="getDitQty({{$product->product_id}})">
                                        </td>
                                        <td >
                                            <input type="number"  min="0" max="0" value="0" name="dd_qty[{{$product->product_id}}]" id="dd_qty{{$product->product_id}}" class="dd_qty" onchange="getDDQty({{$product->product_id}})">
                                        </td>
                                        <td >
                                            <input type="number"  style="width:50px;" value="0" name="excess_qty[{{$product->product_id}}]" id="excess_qty{{$product->product_id}}" class="excess_qty">
                                        </td>

                                        <td>
                                            <select id="return_reason_{{$product->product_id}}" name="return_reason[{{$product->product_id}}]" class="form-control " required>
                                                @if(count($returnReason)==0)
                                                <option value="0">Select Reason</option>
                                                @else
                                                <option value="0">Select Reason</option>
                                                @foreach($returnReason as $key=>$return)
                                                <option value="{{$key}}">{{$return}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                            <span id="select_error_msg_{{$product->product_id}}" class="alert-danger"></span>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="gds_order_id" id="cancel_order_id" value="{{$orderdata->gds_order_id}}">
                <input type="hidden" name="le_wh_id" id="le_wh__id" value="{{$orderdata->le_wh_id}}">

                <div class="box2">
                    <strong>Return Status</strong>
                    @if($approvals['status'] == 1)
                    <div class="form-group">
                    <input type="hidden" name="approval_status" value="{{$approvals['status']}}" id="approval_status">
                        
                        <select id="return_status" name="return_status" class="form-control">
                            <!-- @if(count($returnStatus))
                            <option value="">Select Status</option>
                            @foreach($returnStatus as $status=>$retstat)
                                @if($status!= '67003')
                                    <option value="{{$status}}">{{$retstat}}</option>
                                @endif
                            @endforeach
                            @else
                            <option value="">Select Status</option>
                            @endif -->
                            <option value="">Select Status</option>
                            <option value="{{$approvals['data'][0]['nextStatusId']}},{{$approvals['data'][0]['isFinalStep']}}"> {{$approvals['data'][0]['condition']}} </option>
                            <input type="hidden" name="currentStatusID" value="{{$approvals['currentStatusId']}}">
                            <input type="hidden" name="nextStatusId" value="{{$approvals['data'][0]['nextStatusId']}}">
                        </select>
                        </div>

                    <div class="form-group">
                        <p class="text-danger" id="returnAjaxResponse"></p>
                        <textarea class="form-control" rows="3" id="cancel_comment" name="order_comment" placeholder="Enter your comment"></textarea>
                    </div>
                        @else
                        <div class="form-group">
                        <select disabled="true" id="return_status" name="return_status" class="form-control">
                        </select>
                        </div>

                    <div class="form-group">
                        <p class="text-danger" id="returnAjaxResponse"></p>
                        <textarea class="form-control" disabled="true" rows="3" id="cancel_comment" name="order_comment" placeholder="Enter your comment"></textarea>
                    </div>
                        @endif
                    

                    <div class="form-group">
                        <input type="submit" id="btnReturnSubmit" class="btn green-meadow" value="Submit">

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="portlet-body">
                    <div class="table-responsive">
                        <h4>Comment History</h4>
                        @include('Orders::comments')
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@section('script')
<style>
    .error{color:red;}
    .error-style
    {
        border:1px solid #b94a48;
    }
    .success-style
    {
        border:1px solid #0000ff;
    }  
</style>
<script type="text/javascript">

    function checkAll(ele) {
        var checkboxes = document.getElementsByClassName('check_box');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
        }
    }

    function qtyCalculation(productid){
        productarr = <?php echo json_encode($productArr); ?>;
        jQuery.each(productarr, function(i, val) {
            if (val.product_id == productid){
                var retqty = parseInt($("#return_qty" + productid).val());
                var goodqty = parseInt($("#good_qty" + productid).val());
                var badqty = parseInt($("#bad_qty" + productid).val());
                var ditqty = parseInt($('#dit_qty' + productid).val());
                var ddqty = parseInt($('#dd_qty' + productid).val());
        //alert(ditqty);
        // $("#good_qty" + productid).val(retqty);
        // $("#bad_qty" + productid).val(0);
        var checkboxes = document.getElementById('retchk'+productid);
        if(retqty > 0){
            checkboxes.checked = true;
        }else{
            checkboxes.checked = false;

        }
        if(isNaN(retqty) == true){
            var goodqty = 0;
            var badqty = 0;
            var ditqty = 0;
            var ddqty = 0; 
        }else{
            $("#good_qty" + productid).attr({
                "max" : retqty,        
            });
            $("#bad_qty" + productid).attr({
                "max" : retqty,        
            });
            $("#dit_qty" + productid).attr({
                "max" : retqty,        
            });
            $("#dd_qty" + productid).attr({
                "max" : retqty,        
            });
            
            if (retqty > val.qty){
                var goodqty = retqty;
                var badqty = 0;
                var ditqty = 0;
                var ddqty = 0;
            } else {
                var goodqty = retqty;
                var badqty = 0;
                var ditqty = 0;
                var ddqty = 0;

            }
        }
        $("#good_qty" + productid).val(goodqty);
        $("#bad_qty" + productid).val(badqty);
        $("#dit_qty" + productid).val(ditqty);
        $("#dd_qty" + productid).val(ddqty);


    }
    
});
    }

    function getGoodQty(productid){
        var goodqty = parseInt($("#good_qty" + productid).val());
        var retqty = parseInt($("#return_qty" + productid).val());
        var badqty = parseInt($("#bad_qty" + productid).val());
        var ddqty = parseInt($("#dd_qty" + productid).val());
        var ditqty = parseInt($('#dit_qty' + productid).val());
        if(isNaN(goodqty) == true){
            var goodqty = 0;
            var badqty = retqty;
            var ditqty = 0;
            var ddqty = 0; 
        }else if(goodqty > retqty || goodqty < 0){
            var badqty = badqty;
            var ditqty = ditqty;
            var ddqty = ddqty; 
        }else{
            var badqty = retqty-goodqty-(ditqty+ddqty);
            var ditqty = retqty-badqty-(goodqty+ddqty);
            var ddqty = retqty-badqty-(goodqty+ditqty);
            if(badqty <0){
                var badqty = 0;
                var ditqty = retqty-(goodqty+ddqty);
                var ddqty = retqty-(goodqty+ditqty);

            }
            if(ditqty<0){
                var ditqty = 0;
                var badqty = 0;
                var ddqty = retqty - goodqty;
            }
           

        }   

        $("#good_qty" + productid).val(goodqty);
        $('#dit_qty' + productid).val(ditqty);
        $("#bad_qty" + productid).val(badqty);
        $("#dd_qty" + productid).val(ddqty);

    }
    function getBadQty(productid){
        var badqty = parseInt($("#bad_qty" + productid).val());
        var retqty = parseInt($("#return_qty" + productid).val());
        var goodqty = parseInt($("#good_qty" + productid).val());
        var ditqty = parseInt($("#dit_qty"+ productid).val());
        var ddqty = parseInt($("#dd_qty"+ productid).val());

        if(isNaN(badqty) == true){
            var goodqty = 0;
            var badqty = retqty;
            var ditqty = 0;
            var ddqty = 0; 
        }else if(badqty > retqty){
            var goodqty = goodqty;
            var ditqty = ditqty;
            var ddqty = ddqty;
        }else{
            var goodqty = retqty-badqty-(ditqty+ddqty);
            var ditqty = retqty-badqty-(goodqty+ddqty);
            var ddqty = retqty-badqty-(goodqty+ditqty);
            if(goodqty <0){
                var goodqty = 0;
                var ditqty = retqty-(badqty+ddqty);
                var ddqty = retqty-(badqty+ditqty);

            }
            if(ditqty<0){
                var ditqty= 0;
                var goodqty= 0;
                var ddqty = retqty - badqty;
            }
            


        }
        $("#good_qty" + productid).val(goodqty);
        $('#dit_qty' + productid).val(ditqty);
        $("#bad_qty" + productid).val(badqty);
        $("#dd_qty" + productid).val(ddqty);

    }
/*function getReasonId(productid){
var reasonval = $('#return_reason_'+productid).val();
var badqty = parseInt($('#bad_qty'+productid).val()); 
var ditqty = parseInt($('#dit_qty'+productid).val());
var ddqty = parseInt($('#dd_qty'+productid).val());
if(reasonval == 59005){
     
    $('#dit_qty'+productid).prop('disabled', false);
    $('#dd_qty'+productid).prop('disabled', true);
    $('#bad_qty'+productid).val(badqty+ddqty);
    $('#dd_qty'+productid).val(0);        
}else if(reasonval == 59004){ 
    $('#dd_qty'+productid).prop('disabled', false);
    $('#dit_qty'+productid).prop('disabled', true);
    $('#bad_qty'+productid).val(badqty+ditqty);
    $('#dit_qty'+productid).val(0);        
}
else{
 $('#dit_qty'+productid).prop('disabled', true);
 $('#dd_qty'+productid).prop('disabled', true);
 if(ddqty !=0){
  $('#bad_qty'+productid).val(badqty+ddqty);  
}else if(ditqty !=0){
$('#bad_qty'+productid).val(badqty+ditqty);
}
 $('#dd_qty'+productid).val(0);
 $('#dit_qty'+productid).val(0); 

}     
}*/
function getDitQty(productid){
    var badqty = parseInt($("#bad_qty" + productid).val());
    var retqty = parseInt($("#return_qty" + productid).val());
    var goodqty = parseInt($("#good_qty" + productid).val());
    var ditqty = parseInt($("#dit_qty" + productid).val());
    var ddqty = parseInt($("#dd_qty" + productid).val());
    if(isNaN(ditqty) == true){
        var goodqty = retqty;
        var badqty = 0;
        var ditqty = 0;
        var ddqty = 0; 
    }else if(ditqty > retqty || ditqty < 0 ){
        var goodqty = goodqty;
        var badqty = badqty;
        var ddqty = ddqty;
    }else{
        var badqty = retqty-ditqty-(goodqty+ddqty);
        var goodqty = retqty-badqty-(ditqty+ddqty);
        var ddqty = retqty-(goodqty+badqty+ditqty);
        if(badqty <0){
            var badqty = 0;
            var goodqty = retqty-(ditqty+ddqty);
            var ddqty = retqty-(goodqty+ditqty);

        }
        if(goodqty<0){
            var goodqty=0;
            var badqty=0;
            var ddqty =retqty - ditqty;
        }
        


    }
    $("#good_qty" + productid).val(goodqty);
    $('#dit_qty' + productid).val(ditqty);
    $("#bad_qty" + productid).val(badqty);
    $("#dd_qty" + productid).val(ddqty);

}
function getDDQty(productid){
    var badqty = parseInt($("#bad_qty" + productid).val());
    var retqty = parseInt($("#return_qty" + productid).val());
    var goodqty = parseInt($("#good_qty" + productid).val());
    var ditqty = parseInt($("#dit_qty" + productid).val());
    var ddqty = parseInt($("#dd_qty" + productid).val());
    if(isNaN(ddqty) == true){
        var goodqty = retqty;
        var badqty = 0;
        var ditqty = 0;
        var ddqty = 0;  
    }else if(ddqty > retqty || ddqty < 0 ){
        var goodqty = goodqty;
        var badqty = badqty;
        var ditqty = ditqty;
    }else{
        var badqty = retqty-ddqty-(goodqty+ditqty);
        var goodqty = retqty-badqty-(ditqty+ddqty);
        var ditqty = retqty-(goodqty+badqty+ddqty);
        if(badqty <0){
            var badqty = 0;
            var goodqty = retqty-(ddqty+ditqty);
            var ditqty = retqty-(goodqty+ddqty);

        }
        if(goodqty<0){
            var goodqty = 0;
            var badqty = 0;
            var ditqty = retqty - ddqty;
        }
        
    }
    $("#good_qty" + productid).val(goodqty);
    $('#dit_qty' + productid).val(ditqty);
    $("#bad_qty" + productid).val(badqty);
    $("#dd_qty" + productid).val(ddqty);

}


$(document).ready(function () {
    $(".return_qty").on("keypress keyup blur paste", function(event) {
        var that = this;

//paste event 
if (event.type === "paste") {
    setTimeout(function() {
        $(that).val($(that).val().replace(/[^\d].+/, ""));
    }, 100);
} else {

    if (event.which < 48 || event.which > 57) {
        event.preventDefault();
    } else {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
    }
}
});
function getCheckedBox() {
   var checked = false;
   $("input[class='check_box']").each(function () {
    if ($(this).prop('checked') == true) {
        checked = true;
    }
});
   return checked;
}
function getCheckedId(){
    var check = 1;
    $("input:checkbox:checked", "#returnTbl").each(function() {
        var id = $(this).attr('data-attrib');
        var val = $("#return_reason_"+id).val();
        if(val == 0){
            check = 0;
        }
    });
    return check;
}
/*$("#order_return_form").submit(function(e){
var check  = getCheckedId();
if(check == 0){
alert('Please select Reason');
e.preventDefault();
}                   
});*/
$("#order_return_form").validate({

//returnTbl
rules: {
    return_status: "required"
},
submitHandler: function (form) {
    if (getCheckedBox() == false) {
        $('#returnAjaxResponse').html('Please select at least one product.');
    } 
    else if(getCheckedId() == 0){
        $("input:checkbox:checked", "#returnTbl").each(function() {
            var id = $(this).attr('data-attrib');
            var val = $("#return_reason_"+id).val();
            if(val == 0){                        
                $('#select_error_msg_'+id).html('Please select at least one reason.');
            }
        });
    }
    else {

        if (confirm('Are you Sure! You want to Save?')) {
            $('#btnReturnSubmit').prop('disabled', true);
            var form = $('#order_return_form');
            $.ajax({
                url: "/salesorders/saveReturnActionAjax",
                type: "POST",
                data: form.serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.status == 200) {                       
                        $('#returnAjaxResponse').removeClass('text-danger').addClass('text-success').html("Return Sucessfully saved");
                        window.location.href='/salesorders/returndetail/'+data.message;
                    } else {
                        $('#returnAjaxResponse').removeClass('text-success').addClass('text-danger').html(data.message);
                    }

                },
                error: function (response) {
                    $('#returnAjaxResponse').html('Unable to save return');
                }
            });

        }
    }
}
});
});
</script>
<script type="text/javascript">
$(document).ready(function (){
     disableDropdown();
        function disableDropdown(){
            var approval_status = $('#approval_status').val();
            if(approval_status != 1){
                $('#return_status').prop('disabled', true); 
                $('#return_comment').prop('disabled', true);                    
                $('#btnReturnSubmit').prop('disabled', true);
            }
            else{
                $('#return_status').prop('disabled', false);    
            }
        }
});

</script>
@stop   
@else
<div class="row">
    <div class="col-md-12 col-sm-12">No product for return</div>
</div>
@endif