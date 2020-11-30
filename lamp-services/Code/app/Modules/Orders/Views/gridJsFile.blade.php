<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<?php /*<script src="{{ URL::asset('assets/global/plugins/igniteui/grid_script.js') }}" type="text/javascript"></script>*/?>
<script src="{{ URL::asset('assets/admin/pages/scripts/orders/orders_grid.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/orders/orders.js') }}" type="text/javascript"></script>
<script type="text/javascript">
getStats('{{$orderdata->gds_order_id}}');
@if($actionName == 'orderDetail')
getOrderDetail('{{$orderdata->gds_order_id}}');
@endif
</script>

<style type="text/css">

.modal-body {
    padding: 10px !important;
}
h4{
   font-weight: 400;
}
.error{color:red;}
.centerAlignment { text-align: center;}
#invoiceList_invoiceId {text-align:center !important;}
#invoiceList_orderId {text-align:center !important;}
#invoiceList_invoiceDate {text-align:center !important;}
#invoiceList_totalAmount {text-align:center !important;}
#invoiceList_Status {text-align:center !important;}
#invoiceList_Actions {text-align:center !important;}

#shipmentList_shipmentId {text-align:center !important;}
#shipmentList_orderId {text-align:center !important;}
#shipmentList_orderDate {text-align:center !important;}
#shipmentList_shipmentDate {text-align:center !important;}
#shipmentList_shippedQty {text-align:center !important;}
#shipmentList_Status {text-align:center !important;}
#shipmentList_shipmentActions {text-align:center !important;}

#cancelList_cancelId {text-align:center !important;}
#cancelList_orderId {text-align:center !important;}
#cancelList_orderDate {text-align:center !important;}
#cancelList_cancelDate {text-align:center !important;}
#cancelList_qtyCancelled {text-align:center !important;}
#cancelList_Actions {text-align:center !important;}

#commentList_SNo {text-align:center !important;}
#commentList_commentDate {text-align:center !important;}
#commentList_Status {text-align:center !important;}
#commentList_commentBy {text-align:center !important;}
#commentList_commentType{text-align:center !important;}
#invoiceList_TotalQty{text-align:center !important;}
#invoiceList_status{text-align:center !important;}

</style>
