
@extends('layouts.default')
@extends('layouts.header')
@section('content')





<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<html>

<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</head>

<body>
      

  <div class="row">
      <input type="button" name="submit" id="delivery_all_order" style="display: none;float: right;" class="btn btn-info submit" value="Submit Orders" onclick="deliverAllOrders()" />
  </div> 
  <div class="row">

  <div style="display:none; margin-top:5px;" id="ajaxResponse" class="col-md-12 alert alert-danger"></div>

    <div class="table-responsive">
      <!-- <input type="button" name="submit" id="delivery" class="btn btn-info submit" value="Full Deliver" onclick="fulldelivery()" /> -->
      <table class="table table-bordered" id="orderList">
      </table>
    </div>
      
  </div>

    <div class="modal modal-scroll fade in" id="deliveryMismatchModal" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true" style="top: 15%;" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" style="width:90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button> -->
                    <h4 class="modal-title" id="basicvalCode">Order:<span id="order_code" style="font-weight: 700;"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" value="" name="" id="gds_order_id">
                        <div class="col-md-12">
                            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="table table-striped table-bordered table-advance table-hover table-scrolling">
                                <tbody id="partialOrderData">
                                    
                                </tbody>
                            </table>
                                        
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                  <div class="row">
                      <div class="col-md-12 text-center">
                          <button class="btn" id="okayPricingMismatch" data-dismiss="modal" onclick="saveOrderDeliveryinTemp()" style="color: #FFFFFF;
                            background-color: #1BBC9B;">Ok</button>
                      </div>
                  </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div> 

</body>

</html>

@stop


@section('userscript')
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{{URL::asset('assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.theme.css" rel="stylesheet" />
<link href="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.css" rel="stylesheet" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />

<script src="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.core.js"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui')}}/infragistics.lob.js"></script>
<?php /*<script src="{{ URL::asset('assets/global/plugins/igniteui/grid_script.js') }}" type="text/javascript"></script>*/?>
<script src="{{ URL::asset('assets/admin/pages/scripts/orders/orders_grid.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/orders/orders.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/consignment/consignment_script.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function(){
        loadGrid();
    });

function reloadOpener() {
  if (top.opener && !top.opener.closed) {
    try {
      opener.location.reload(1); 
    }
    catch(e) {
    }
    window.close();
  }
}
window.onunload=function() {
  reloadOpener();
}

    function changeretrnqty(gds_order_id,product_id){

        var invoiced_qty = $('#'+gds_order_id+'_invoice_qty_'+product_id).val();
        var return_qty = $('#'+gds_order_id+'_return_qty_'+product_id).val();
        var deliver_qty = invoiced_qty - return_qty;

        if(deliver_qty < 0){
            alert("Rerurn Qty Should be less than Invoiced Qty");
            $('#'+gds_order_id+'_return_qty_'+product_id).val(0);
            $('#'+gds_order_id+'_deliver_qty_'+product_id).val(0);
            return true;
        }

        $('#'+gds_order_id+'_deliver_qty_'+product_id).val(deliver_qty);

        return true;

    }

    function saveOrderDeliveryinTemp(){
        var gds_order_id = $("#gds_order_id").val();
        let productArr = {};
        $("input[name^='deliver_qtyArr']").each(function(){
            var product_id = $(this).attr('product_id');
            var invoiced_qty = $('#'+gds_order_id+'_invoice_qty_'+product_id).val();
            var return_qty = $('#'+gds_order_id+'_return_qty_'+product_id).val();
            var return_id = $('#'+gds_order_id+'_return_reason_'+product_id).val();
            var deliver_qty = invoiced_qty - return_qty;
            if(return_qty > 0)
                productArr[product_id] = {product_id:product_id,invoiced_qty:invoiced_qty,return_qty:return_qty,deliver_qty:deliver_qty,return_id:return_id};
        });


        if(productArr.length == 0){
          $('#returnReason_'+gds_order_id).val("");
          alert("Please do atleast one product return Qty");
          return false;
        }

        $.ajax({
              headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
              type: "POST",
              url: '/salesorders/saveorderdeliveryintemp',
              data: {gds_order_id:gds_order_id,productArr:productArr},
              dataType: 'json',
              beforeSend: function () {
                 $('#loader1').show();
              },
              complete: function (data) {
                  $('#loader1').hide();
                },
              success: function (data) {
                  alert(data.message);
              }
        });

    }


    function partialDeliver(gds_order_id){
        var status = $('#returnReason_'+gds_order_id).val();
        if(status == 17023){
            getOrderData(gds_order_id);
        }
    }

    function deliverAllOrders(){
        let orderArr = [];
        var selected = [];


        $("input[name='chk[]']").each(function () {

            if ($(this).val() != 'on') {
                if ($(this).prop('checked') == true) {
                    selected.push($(this).val());
                    var gds_order_id = $(this).val();
                    var order_status = $('#returnReason_'+gds_order_id).val();
                    if(order_status!="")
                        orderArr.push({gds_order_id:gds_order_id,order_status:order_status});

                }
            }
        });
        //console.log(orderArr);
        if(selected.length == 0){
            alert('Please select at least one order.');
            return false;
        }

        if(orderArr.length == 0){
            alert('Please select at least one order status.');
            return false;
        }

        // $("select[name^='returnReasons']").each(function(){
        //     var gds_order_id = $(this).attr('gds_order_id');
        //     var order_status = $(this).val();
        //     orderArr.push({gds_order_id:gds_order_id,order_status:order_status});
        // });

        $.ajax({
              headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
              type: "POST",
              url: '/salesorders/deliverallorder',
              data: {orderArr:orderArr},
              dataType: 'json',
              beforeSend: function () {
                 $('#loader1').show();
              },
              complete: function (data) {
                  $('#loader1').hide();
                  
                },
              success: function (data) {
                  if(data.status == 200){
                    alert(data.message);
                    window.close();
                  }else{
                    alert(data.message);
                    if(data.status == 401)
                      location.reload();
                  }
                         
              }
        });


    }
    function getOrderData(gds_order_id){
        $("#gds_order_id").val(gds_order_id);
        $.ajax({
              headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
              type: "POST",
              url: '/salesorders/getorderdetails',
              data: {gds_order_id:gds_order_id},
              dataType: 'json',
              beforeSend: function () {
                 $('#loader1').show();
              },
              complete: function (data) {
                  $('#loader1').hide();
                  
                },
              success: function (data) {
                  $('#deliveryMismatchModal').modal("show");
                  if(data.status == 200){
                    $("#partialOrderData").html(data.data);
                    $("#order_code").html(data.order_code);
                  }else{
                    alert('Server Error');
                  }
                         
              }
        });
    }

    function loadGrid(){
        var selected = <?php echo "'".$_GET['gds_order_ids']."'"; ?>;
        $.ajax({
              headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
              type: "POST",
              url: '/salesorders/getOFDDeliveryDetails',
              data: {ids: selected},
              dataType: 'json',
              beforeSend: function () {
                 $('#loader1').show();
              },
              complete: function (data) {
                  $('#loader1').hide();
                  
                },
              success: function (data) {
                  
                   getOutForDeliveryOrders(data);
                $("#delivery_all_order").show();
              }
        });
    }

    function fulldelivery(){
        var selected = getChkVal();
        if(selected.length>0){
            $.ajax({
                  headers: {'X-CSRF-TOKEN': $("#csrf-token").val()},
                  type: "POST",
                  url: '/salesorders/fulldeliver',
                  data: {ids: selected},
                  dataType: 'json',
                  beforeSend: function () {
                     $('#loader1').show();
                  },
                  complete: function (data) {
                      $('#loader1').hide();
                      
                    },
                  success: function (data) {
                        alert("Selected orders has been delivered!");
                        location.reload();      
                  }
            });
        }else{
            alert("Please select atleast one order!");
            return false;
        }
    }
</script>
@stop
<style type="text/css">
    
</style>