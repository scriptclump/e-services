<style>
body {
    margin: 0px;
    padding: 0px;
    color: #333;
    font-family: "Open Sans", sans-serif;
}
</style>
<?php $curSymbol = isset($productArr[0]->symbol) ? trim($productArr[0]->symbol) : 'Rs.'; ?>
<?php if(is_object($leInfo)): ?>
<table width="100%" border="0" cellspacing="5" cellpadding="5">
  <tr>
    <td width="50%" align="left" valign="middle"><img src="<?php echo e(url('/')); ?>/img/ebutor.png" alt="" height="42" width="42" > <strong><?php echo e($leInfo->business_legal_name); ?></strong></td>
    <td width="50%" align="right" valign="middle"><div style="padding-left:20px; padding-top:10px; font-size:12px; float:right;"><?php echo e($leInfo->address1); ?>, <?php if(!empty($leInfo->address2)): ?><?php echo e($leInfo->address2); ?>,<br><?php endif; ?>
        <?php echo e($leInfo->city); ?>, <?php echo e($legalEntity->state_name); ?>, <?php echo e(empty($leInfo->country_name) ? 'India' : $leInfo->country_name); ?>, <?php echo e($leInfo->pincode); ?></td>
  </tr>
</table>
<?php endif; ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center"><h4>INVOICE</h4></td>
    </tr>
</table>

<table width="100%" style="border:1px solid #ccc;font-size:10px !important;" cellspacing="0" cellpadding="2">
    <tr style="font-size:14px;background-color:#e7ecf1 !important; font-weight:bold;">
      <td height="30" width="33%" bgcolor="#e7ecf1">Customer</td>
      <td width="33%" bgcolor="#e7ecf1">Shipping Address</td>
      <td width="33%" bgcolor="#e7ecf1">Invoice Details</td>
    </tr>

    <tr>
      <td valign="top" style="border-right:1px solid #ccc; font-size:11px;">
          <strong>Name:</strong> <?php echo e($orderDetails->firstname); ?> <?php echo e($orderDetails->lastname); ?> <br>
            
            <strong>Billing Address</strong><br>
            <?php if(is_object($billing)): ?>   
              <?php echo e($billing->fname); ?> <?php echo e($billing->mname); ?> <?php echo e($billing->lname); ?> <br>
              <?php echo e($billing->addr1); ?> <?php echo e($billing->addr2); ?><br>
              <?php echo e($billing->city); ?>, <?php echo e($billing->state_name); ?>, <?php echo e($billing->country_name); ?>, <?php echo e($billing->postcode); ?><br>
              <strong>Phone:</strong> <?php echo e($orderDetails->phone_no); ?> <br>
              <?php endif; ?>
            
      </td>
      <td valign="top" style="border-right:1px solid #ccc; font-size:11px;">
              <?php if(is_object($shipping)): ?>
              <?php echo e($orderDetails->shop_name); ?><br> 
                <?php echo e($shipping->fname); ?> <?php echo e($shipping->mname); ?> <?php echo e($shipping->lname); ?><br>
                <?php echo e($shipping->addr1); ?> <?php echo e($shipping->addr2); ?><br>
                <?php echo e($shipping->city); ?>, <?php echo e($shipping->state_name); ?>, <?php echo e($shipping->country_name); ?>, <?php echo e($shipping->postcode); ?><br>
                <strong>Telephone:</strong> <?php echo e($shipping->telephone); ?>&nbsp;<strong>Mobile:</strong> <?php echo e($shipping->mobile); ?>

                <?php endif; ?> 
            
      </td>
      <td valign="top" style="font-size:11px;">
              <strong>Invoice No.:</strong> <?php echo e(isset($products[0]->invoice_code) ? $products[0]->invoice_code : $products[0]->gds_invoice_grid_id); ?><br>
              <strong>Invoice Date:</strong> <?php echo e(date('d-m-Y h:i A', strtotime($products[0]->invoice_date))); ?><br>
              <strong>SO No. / Date:</strong> <?php echo e($orderDetails->order_code); ?> / <?php echo e(date('d-m-Y h:i A', strtotime($orderDetails->order_date))); ?><br>
              <?php if(!empty($lewhInfo->le_wh_code)): ?> <strong>DC No:</strong> <?php echo e($lewhInfo->le_wh_code); ?><br> <?php endif; ?>
              <strong>DC Name:</strong> <?php echo e($lewhInfo->lp_wh_name); ?><br>
              <strong>Jurisdiction Only:</strong> Hyderabad
              <?php if(isset($userInfo->firstname) && isset($userInfo->lastname)): ?>
              <br>      <strong>Created By</strong>: <?php echo e($userInfo->firstname); ?> <?php echo e($userInfo->lastname); ?> (M: <?php echo e(isset($userInfo->mobile_no) ? $userInfo->mobile_no : ''); ?>)
                    <?php endif; ?>
              </td>
    </tr>
</table>

<strong style="font-size:13px !important; ">Product Description</strong>

<table cellspacing="0" cellpadding="3" width="100%" style="margin-top:10px; border:1px solid #ccc; font-size:11px;">
          <tr style="font-weight:bold;">
            <td bgcolor="#e7ecf1" height="30">SKU</td>
            <td bgcolor="#e7ecf1">Product Name</td>
            <td bgcolor="#e7ecf1">MRP(Rs.)</th>
            <td bgcolor="#e7ecf1">Unit Price(Rs.)</td>
            <td bgcolor="#e7ecf1">Invoiced Qty</td>
            <td bgcolor="#e7ecf1">Tax %</td>
            <td bgcolor="#e7ecf1" align="right">Tax Amt(Rs.)</td>
            <td bgcolor="#e7ecf1" align="right">Net Amt(Rs.)</td>
            <td bgcolor="#e7ecf1" align="right">Sch. Disc.</td>
            <td bgcolor="#e7ecf1" align="right">Total(Rs.)</td>
          </tr>
<?php if(is_array($products)): ?>          
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
$finalTaxArr = array();
?>
            <?php foreach($products as $product): ?>
            <?php
//print_r($product);
$prodTaxes = json_decode(json_encode($prodTaxes), true);           
$taxName = (isset($prodTaxes[$product->product_id]['name']) ? $prodTaxes[$product->product_id]['name'] : 0);
$taxPer = (isset($prodTaxes[$product->product_id]['tax']) ? $prodTaxes[$product->product_id]['tax'] : 0);
$tax_value =  (isset($prodTaxes[$product->product_id]['tax_value']) ? $prodTaxes[$product->product_id]['tax_value'] : 0);


$singleUnitPrice = (($product->total / (100+$taxPer)*100) / $product->qty);

$unitPrice = ($singleUnitPrice * $product->invoicedQty);
$taxValue = (($singleUnitPrice * $taxPer) / 100 ) * $product->invoicedQty;
$netValue = ($singleUnitPrice * $product->invoicedQty);;
$subTotal = $taxValue + $netValue;
$discount = 0;
$taxkey = $taxName.'-'.$taxPer;
if($taxkey != '0-0') {
  $finalTaxArr[$taxkey][] = array('tax'=>$taxPer, 'name'=>$taxName, 'qty'=>$product->qty, 'tax_value'=>$tax_value, 'taxamtPer'=>($tax_value/$product->qty), 'taxamt'=>(($tax_value/$product->qty)*$product->invoicedQty));
}
?>
            <tr>
            <td style="border-bottom:1px solid #ccc;" height="30"><?php echo e($product->sku); ?></td>
            <td style="border-bottom:1px solid #ccc;"><?php echo e($product->pname); ?></td>
            <td style="border-bottom:1px solid #ccc;"><?php echo e(number_format($product->mrp, 2)); ?></td>
            <td style="border-bottom:1px solid #ccc;"><?php echo e(number_format($singleUnitPrice, 2)); ?></td>
            <td style="border-bottom:1px solid #ccc;"><?php echo e((int)$product->invoicedQty); ?><br>
            Ordered: <?php echo e((int)$product->qty); ?></td>
            <td style="border-bottom:1px solid #ccc;"><?php echo e((float)$taxPer); ?></td>
            <td style="border-bottom:1px solid #ccc;" align="right"><?php echo e(number_format($taxValue, 2)); ?></td>
            <td style="border-bottom:1px solid #ccc;" align="right"><?php echo e(number_format($netValue, 2)); ?></td>
            <td style="border-bottom:1px solid #ccc;" align="right"><?php echo e(number_format($discount, 2)); ?></td>
            <td style="border-bottom:1px solid #ccc;" align="right"><?php echo e(number_format($subTotal, 2)); ?></td>
            <?php
$sub_total = $sub_total + $subTotal;
$total_discount = $total_discount + $discount;
$total_net = $total_net + $netValue;
$total_qty = $total_qty + $product->qty;
$InvoicedQty = $InvoicedQty + $product->invoicedQty;
$total_tax = $total_tax + $taxValue;
$sno = $sno + 1;
?>
          </tr>
        <?php endforeach; ?>
        
        <tr style="font-weight:bold;">
         <td>&nbsp; </td>
          <td>&nbsp;</td>
          <td align="right">Total:</td>
          <td>&nbsp;</td>
          <td><?php echo e($total_qty); ?></td>
          <td></td>
          <td align="right"><?php echo e(number_format($total_tax, 2)); ?></td>
          <td align="right"><?php echo e(number_format($total_net, 2)); ?></td>
          <td align="right"><?php echo e(number_format($total_discount, 2)); ?></td>
          <td align="right"><?php echo e(number_format($sub_total, 2)); ?></td>
        </tr>
<?php //print_r($finalTaxArr); 

$finalNewTaxArr = array();
foreach ($finalTaxArr as $key => $taxArr) {
  $finalNewTaxArr[$key] = array();
  $totAmt = 0;
  foreach ($taxArr as $tax) {
    $totAmt = $totAmt + $tax['taxamt'];
    $finalNewTaxArr[$key]['name'] = $tax['name'];
    $finalNewTaxArr[$key]['tax'] = $tax['tax'];
  }

  $finalNewTaxArr[$key]['tax_value'] = $totAmt;
}
?> 
<?php endif; ?>        
      </table>


<table cellpadding="3" cellspacing="0" width="100%" style="margin-top:10px; border:1px solid #ccc; font-size:11px;">
          <tr style="font-weight:bold;">
            <td height="30" bgcolor="#e7ecf1">Tot. Invoiced Qty</td>
            <td bgcolor="#e7ecf1">Sub Total</td>
            <td bgcolor="#e7ecf1">Shipping Amount</td>
            <td bgcolor="#e7ecf1">Total Scheme Discount</td>
            <td bgcolor="#e7ecf1">Other Discount</td>
            <td bgcolor="#e7ecf1">Total Discount</td>
           <?php if(isset($finalNewTaxArr) && is_array($finalNewTaxArr)): ?>                                     
            <?php foreach($finalNewTaxArr as $tax): ?>
            <td bgcolor="#e7ecf1"><?php echo e($tax['name']); ?> (<?php echo e(isset($tax['tax']) ? (float)$tax['tax'] : 0); ?>%)</td>
            <?php endforeach; ?>
            <?php endif; ?>
            <td bgcolor="#e7ecf1">Total Tax</td>
            <td bgcolor="#e7ecf1">Grand Total</td>
          </tr>
        

          <tr>
            <td height="30"><?php echo e($InvoicedQty); ?></td>
            <td><?php echo e($orderDetails->symbol); ?> <?php echo e(number_format($sub_total, 2)); ?></td>
            <td><?php echo e($orderDetails->symbol); ?> 0.00</td>
            <td><?php echo e($orderDetails->symbol); ?> <?php echo e(number_format($total_discount, 2)); ?></td>
            <td><?php echo e($orderDetails->symbol); ?> <?php echo e(number_format($orderDetails->discount, 2)); ?></td>
            <td><?php echo e($orderDetails->symbol); ?> <?php echo e(number_format(($total_discount + $orderDetails->discount), 2)); ?></td>

          <?php //echo '<pre>';print_r($taxBreakup);print_r($finalNewTaxArr); ?>
          <?php if(isset($finalNewTaxArr) && is_array($finalNewTaxArr)): ?>                                     
          <?php foreach($finalNewTaxArr as $tax): ?>
          <td><?php echo e($orderDetails->symbol); ?> <?php echo e(number_format((isset($tax['tax_value']) ? ($tax['tax_value']) : 0), 2)); ?></td>
          <?php endforeach; ?>
          <?php endif; ?>
            <td><?php echo e(number_format($total_tax, 2)); ?></td>
            <?php $grandTotal = $sub_total; ?>
            <td><?php echo e($orderDetails->symbol); ?> <?php echo e(number_format($grandTotal, 2)); ?></td>
          </tr>
      </table>
<?php if(is_object($trackInfo)): ?>
<br>
<table cellpadding="1" cellspacing="1" width="40%" style="margin-top:10px; border:1px solid #ccc; font-size:11px;">
<tr class="odd gradeX">
<td><?php echo e((int)$trackInfo->cfc_cnt); ?> CFC</td>
<td><?php echo e((int)$trackInfo->bags_cnt); ?> Bags</td>
<td><?php echo e((int)$trackInfo->crates_cnt); ?> Crates</td>
</tr>
</table>
<?php endif; ?>