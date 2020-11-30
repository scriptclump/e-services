<?php 
foreach($bulkPrintData as $data) {
  echo $data;
  //echo '<div class="page-break"></div>';
}
?>
<?php /*<html dir="ltr" lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice</title>
<link href="{{URL::asset('assets/global/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('assets/global/css/custom-ebutor.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('assets/global/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css" />
<style>
@media print {body {-webkit-print-color-adjust: exact;}}
body{-webkit-print-color-adjust: exact;}
table th {
   background-color: #e7ecf1 !important;
   color: 333 !important;   
}
table {
    border-collapse: collapse;
}

.printmartop {margin-top: 10px;}
.container {margin-top: 20px;}
.th {background-color: #999 !important;color: white !important;}
.small1 {font-size: 73%;}
.small2 {font-size: 65.5%;}
.bg {background-color: #efefef;padding: 8px 0px;}
.bold{font-weight: bold;}
.line {border-top: 1px solid #e2e2e2;border-bottom: 1px solid #e2e2e2;}
.table {border: 1px solid #000;}
.table-bordered>tbody>tr>td{border: 1px solid #000 !important;}
.table-bordered>thead>tr>th{border: 1px solid #000 !important;}
.table-bordered {border: 1px solid #000 !important;}
th {background-color: #efefef;font-weight: bold;}
.page-break { display: block; clear: both; page-break-before: always; }
</style>

</head>
<body>
@foreach($bulkPrintData as $data)
<?php
$leInfo = $data['leInfo'];
$companyInfo = $data['companyInfo'];
$legalEntity = is_object($data['legalEntity']) ? $data['legalEntity'] : '';
$orderDetails = $data['orderDetails'];
$billing = $data['billing'];
$shipping = $data['shipping'];
$lewhInfo = $data['lewhInfo'];
$products = $data['products'];

?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <table width="100%" border="0" cellspacing="5" cellpadding="5">
        <tr>
          <td width="65%" align="left" valign="middle"><img src="/img/ebutor.png" alt="" height="42" width="42" > <strong>{{$leInfo->business_legal_name}}</strong></td>
          <td width="35%" align="right" valign="middle"><div style="padding-left:20px; padding-top:10px; font-size:12px; float:right;">{{$leInfo->address1}}, {{$leInfo->address2}},<br>
              {{$leInfo->city}}, {{$legalEntity->state_name}}, {{$leInfo->country_name}}, {{$leInfo->pincode}}</td>
        </tr>
      </table>
    </div>
  </div>
  <div class="row" style="margin:5px 0px;">
    <div class="col-md-12 text-center">
      <h4>INVOICE</h4>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <table class="table table-bordered thline printtable ">
        <thead>
          <tr style="font-size:14px;background-color:#e7ecf1 !important;">
            <th width="33%" bgcolor="#e7ecf1">Customer</th>
            <th width="33%" bgcolor="#e7ecf1">Shipping Address</th>
            <th width="33%" bgcolor="#e7ecf1">Invoice Details</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td valign="top" style="font-size:11px;">
                  <strong>Name:</strong> {{$orderDetails->firstname}} {{$orderDetails->lastname}} <br>
                  
                  <strong>Billing Address</strong><br>
                  @if(is_object($billing))   
                    {{$billing->addr1}} {{$billing->addr2}}, {{$billing->city}}, {{$billing->state_name}}, {{$billing->country_name}}, {{$billing->postcode}}<br>
                    <strong>Phone:</strong> {{$orderDetails->phone_no}} <br>
                    @endif                  
            </td>
            <td valign="top" style="font-size:11px;">
            @if(is_object($shipping))
                      {{$orderDetails->shop_name}}<br>  
                      {{$shipping->fname}} {{$shipping->mname}} {{$shipping->lname}}<br>
                      {{$shipping->addr1}} {{$shipping->addr2}}<br>
                      {{$shipping->city}}, {{$shipping->state_name}}, {{$shipping->country_name}}, {{$shipping->postcode}}<br>
                      <strong>Telephone:</strong> {{$shipping->telephone}} <strong>Mobile:</strong> {{$shipping->mobile}}
                      @endif 
                  
            </td>
            <td valign="top" style="font-size:11px;">
                    <strong>Invoice No.:</strong> {{$products[0]->gds_order_invoice_id}}<br>
                    <strong>Invoice Date:</strong> {{date('d-m-Y h:i A', strtotime($products[0]->invoice_date))}}<br>
                    <strong>SO No. / Date:</strong> {{$orderDetails->order_code}} / {{date('d-m-Y h:i A', strtotime($orderDetails->order_date))}}<br>
                    @if(!empty($lewhInfo->le_wh_code)) <strong>DC No:</strong> {{$lewhInfo->le_wh_code}}<br> @endif
                    <strong>DC Name:</strong> {{$lewhInfo->lp_wh_name}}<br>
                    <strong>Jurisdiction Only</strong> : Hyderabad
                    @if(isset($userInfo->firstname) && isset($userInfo->lastname))
                    <br><strong>Created By</strong>: {{$userInfo->firstname}} {{$userInfo->lastname}} (M: {{isset($userInfo->mobile_no) ? $userInfo->mobile_no : ''}})
                    @endif
                    </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <table cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-advance table-hover" style="font-size:11px;">
        <thead>
          <tr>
            <th bgcolor="#e7ecf1" class="  text-center ">SNO</th>
            <th bgcolor="#e7ecf1" class="col-xs-1 text-center ">SKU</th>
            <th bgcolor="#e7ecf1" class="col-md-2 text-center col-xs-4">Product Name</th>
            <th bgcolor="#e7ecf1" class="col-xs-1 text-center">MRP(Rs.)</th>
            <th bgcolor="#e7ecf1" class="col-xs-1 text-center">Unit Price(Rs.)</th>
            <th bgcolor="#e7ecf1" class="col-xs-1 text-center ">Ordered Qty</th>
            <th bgcolor="#e7ecf1" class="col-xs-1 text-center ">Invoiced Qty</th>
            <th bgcolor="#e7ecf1" class="col-xs-1 text-center">Tax %</th>
            <th bgcolor="#e7ecf1" class="col-xs-1 text-center">Tax Amt(Rs.)</th>
            <th bgcolor="#e7ecf1" class="col-xs-1 text-center">Net Amt(Rs.)</th>
            <th bgcolor="#e7ecf1" class="col-xs-1 text-center">Sch. Disc.</th>
            <th bgcolor="#e7ecf1" class="col-xs-1 text-center">Total Amt(Rs.)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <?php
$sno = 1;
$sub_total = 0;
$total_qty = 0;
$InvoicedQty = 0;
$total_unit_price = 0;
$total_mrp = 0;
$total_net = 0;
$total_discount = 0;
$total_tax = 0;
$total_tax_value = 0;

$sno = 1; 
$tax = 0;
$discount = 0;
$shippingAmount = 0;
$otherDiscount = 0;
$grandTotal = 0;
$totInvoicedQty = 0;
?>
            @foreach($products as $product)
            <?php
$taxPer = (isset($taxArr[$product->product_id]) ? $taxArr[$product->product_id] : 0);

$taxValue = (int)((($product->price*$taxPer)/100) / $product->qty);
$netValue = (int)$product->qty*$product->price;
$discountValue = (int)($product->price*$discount)/100;
$totalValue = (int)(($netValue+$taxValue)-($discountValue));
$grandTotal +=$totalValue; 
?>
            <td><p>{{$sno}}</p></td>
            <td><p>{{$product->sku}}</p></td>
            <td><p>{{$product->pname}} {{!empty($product->seller_sku) ? '('.$product->seller_sku.')' : ''}}</p></td>
            <td><p>{{$product->mrp}}</p></td>
            <td><p>{{$product->price}}</p></td>
            <td><p>{{(int)$product->qty}}</p></td>
            <td><p>{{(int)$product->invoicedQty}}</p></td>
            <td><p>{{number_format($taxPer, 2)}}</p></td>
            <td><p>{{number_format($taxValue, 2)}}</p></td>
            <td><p>{{number_format($netValue, 2)}}</p></td>
            <td><p>{{number_format($discountValue, 2)}}</p></td>
            <td><p>{{number_format($totalValue, 2)}}</p></td>
            <?php
$total_unit_price+=$product->single_price;
$total_mrp+=$product->mrp;
$total_net+=$netValue;
$total_discount+=$discountValue;
$total_tax+=$taxValue;
$total_tax_value+=$tax;
$total_qty+=$product->qty;
$InvoicedQty+=$product->invoicedQty;
$sub_total+=$totalValue;
$sno = $sno + 1;
?>
          </tr>
        @endforeach
        <tr>
          <td><label>&nbsp;</label></td>
          <td><label>&nbsp;</label></td>
          <td><label>&nbsp; </label></td>
          <td>&nbsp;</td>
          <td align="right"><label>Total:</label></td>
          <td><label>{{$total_qty}}</label></td>
          <td><label>{{$InvoicedQty}}</label></td>
          <td><label></label></td>
          <td><label>{{number_format($total_tax, 2)}}</label></td>
          <td><label>{{number_format($total_net, 2)}}</label></td>
          <td><label>{{number_format($total_discount, 2)}}</label></td>
          <td><label>{{number_format($sub_total, 2)}}</label></td>
        </tr>
        </tbody>
        
      </table>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <table cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-advance table-hover" style="font-size:11px;">
        <thead>
          <tr>
            <th bgcolor="#e7ecf1">Tot. Invoiced Qty</th>
            <th bgcolor="#e7ecf1">Sub Total</th>
            <th bgcolor="#e7ecf1">Shipping Amount</th>
            <th bgcolor="#e7ecf1">Total Scheme Discount</th>
            <th bgcolor="#e7ecf1">Other Discount</th>
            <th bgcolor="#e7ecf1">Total Discount</th>
            @if(isset($taxSummaryArr) && is_array($taxSummaryArr))                                     
            @foreach($taxSummaryArr as $tax)
            <th bgcolor="#e7ecf1">{{$tax->name}} ({{isset($tax->tax) ? (float)$tax->tax : 0}}%)</th>
            @endforeach
            @endif
            <th bgcolor="#e7ecf1">Total Tax</th>
            <th bgcolor="#e7ecf1">Grand Total</th>
          </tr>
        </thead>
        <tbody>
          <tr class="odd gradeX">
            <td>{{$InvoicedQty}}</td>
            <td>{{$orderDetails->symbol}} {{ number_format($sub_total, 2) }}</td>
            <td>{{$orderDetails->symbol}} 0.00</td>
            <td>{{$orderDetails->symbol}} {{number_format($total_discount, 2)}}</td>
            <td>{{$orderDetails->symbol}} {{number_format($orderDetails->discount, 2)}}</td>
            <td>{{$orderDetails->symbol}} {{number_format(($total_discount + $orderDetails->discount), 2)}}</td>

            @if(isset($taxSummaryArr) && is_array($taxSummaryArr))                                     
            @foreach($taxSummaryArr as $tax)
            <td>{{$orderDetails->symbol}} {{number_format((isset($tax->tax_value) ? ($tax->tax_value / $tax->qty) : 0), 2)}}</td>
            @endforeach
            @endif
            <td>{{number_format($total_tax, 2)}}</td>
            <td>{{$orderDetails->symbol}} {{number_format($grandTotal, 2)}}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="page-break"></div>
@endforeach
</body>
</html>*/