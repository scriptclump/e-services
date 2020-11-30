<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{$title}}</title>
<link href="{{URL::asset('assets/global/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('assets/global/css/custom-ebutor.css')}}" rel="stylesheet" type="text/css" />
<link href="{{URL::asset('assets/global/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css" />
<style>
.notific{font-size: 11px;}

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
</style>

</head>
<body>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <table width="100%" border="0" cellspacing="5" cellpadding="5">
        <tr>
          <td width="65%" align="left" valign="middle"><img src="/img/ebutor.png" alt="" height="42" width="42" > <strong> {{$leInfo->business_legal_name}}</strong></td>
          <td width="35%" align="right" valign="middle"><div style="padding-left:20px; padding-top:10px; font-size:12px; float:right;"> {{$leInfo->address1}}, {{$leInfo->address2}},<br>
              {{$leInfo->city}}, {{$leInfo->state_name}}, {{$leInfo->country_name}}, {{$leInfo->pincode}}.<br>
              <!-- <strong>Email :</strong> {{$companyInfo->email_id}} | <strong>Phone :</strong> {{$companyInfo->mobile_no}}  --></div></td>
        </tr>
      </table>
    </div>
  </div>

   <div class="row">
      <div class="col-md-12 text-right">
          <p class="notific">* <b>All Amounts in</b> <i class="fa fa-inr"></i></p>
      </div>    
  </div>

  <div class="row" style="margin:5px 0px;">
    <div class="col-md-12 text-center">
      <h4>INDENT</h4>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <table class="table table-bordered thline printtable ">
        <thead>
          <tr style="background-color:#e7ecf1 !important; font-size:14px;">
            <th width="33%" bgcolor="#e7ecf1">Supplier</th>
            <th width="33%" bgcolor="#e7ecf1">Delivery Address</th>
            <th width="33%" bgcolor="#e7ecf1">Indent Details</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td valign="top">

            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:11px; line-height:23px; border:1px solid #fff;">
            <tr>
            <td><strong> Name </strong></td>
            <td>: {{$supplier->business_legal_name}}</td>
            </tr>
            <tr>
            <td valign="top"><strong> Address </strong></td>
            <td valign="top">: {{$supplier->address1}}
                @if(!empty($supplier->address2)) 
                , {{$supplier->address2}}<br>
                @endif
                {{$supplier->city}}, {{$supplier->state_name}}<br>
                {{$supplier->country_name}}, {{$supplier->pincode}}</td>
            </tr>
            @if($supContact->mobile_no!='')
            <tr>                
            <td><strong> Phone </strong></td>
            <td>: {{$supContact->mobile_no}}</td>
            </tr>
            @endif
            @if($supContact->email_id!='')
            <tr>
            <td><strong> Email </strong></td>
            <td>: {{$supContact->email_id}}</td>
            </tr>
            @endif
            </table>


            </td>
            <td valign="top">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:11px; line-height:23px; border:1px solid #fff;">
            <tr>
            <td><strong> Name </strong></td>
            <td>: {{$warehouse->lp_wh_name}}</td>
            </tr>
            <tr>
            <td valign="top"><strong> Address </strong></td>
            <td valign="top">: {{$warehouse->address1}}, {{$warehouse->address2}}, {{$warehouse->city}}, {{$warehouse->pincode}}</td>
            </tr>
            <tr>
            <td><strong> Phone </strong></td>
            <td>: {{$warehouse->phone_no}}</td>
            </tr>
            <tr>
            <td><strong> Email </strong></td>
            <td>: {{$warehouse->email}}</td>
            </tr>
            </table>
            </td>
            <td valign="top">
    
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:11px; line-height:23px; border:1px solid #fff;">
                <tr>
                  <td><strong> Indent ID </strong></td>
                  <td>: {{$indentArr[0]->indent_code}}</td>
                </tr>
                <tr>
                  <td><strong> Indent Date </strong></td>
                  <td>: {{date('d-m-Y', strtotime($indentArr[0]->indent_date))}}</td>
                </tr>
                <tr>
                  <td><strong> Indent Type </strong></td>
                  <td>: {{($indentArr[0]->indent_type == 1 ? 'Manual' : 'Auto')}}</td>
                </tr>
              </table></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <h4 style="margin:10px 0px;">Product Description</h4>
        <table cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-advance table-hover" style="font-size:11px;">
          <thead>
            <tr style="background-color:#e7ecf1 !important;">
              <th bgcolor="#e7ecf1">S No</th>
              <th bgcolor="#e7ecf1">SKU</th>             
              <!-- <th bgcolor="#e7ecf1">EAN</th> -->
              <th bgcolor="#e7ecf1">Product Name</th>
              <th bgcolor="#e7ecf1">MRP</th>
              <th bgcolor="#e7ecf1">Indent Qty&nbsp;(CFC)</th>
              <th bgcolor="#e7ecf1">CFC LP</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $slno = 1;
            $sumOfIndentQty = 0;
            ?>
          @foreach($indentArr as $product)
          <tr>
            <td>{{$slno}}</td>
            <td>{{$product->sku}}</td>
            <!-- <td>{{(isset($product->upc) ? $product->upc : $product->seller_sku)}}</td> -->
            <td>{{$product->pname}}</td>            
            <td>{{number_format($product->mrp, 2)}}</td>
            <td>{{(int)$product->qty}}<span style="margin-left: 5px; margin-top:5px;">(@if($product->prod_eaches!='') {{$product->prod_eaches}} @else {{0}} @endif Eaches)</span></td>
            <td>{{number_format($product->max_elp,2)}}</td>
          </tr>
          <?php 
          $slno = ($slno +1);
          $sumOfIndentQty = ($sumOfIndentQty + $product->qty); 
          ?>
          @endforeach
          <!--<tr>
            <!-- <td height="25" style="border-bottom:1px solid #ccc;" colspan="4"></td> -->
            <!--<td style="border-bottom:1px solid #ccc;" align="right" colspan="4"><strong>Total</strong></td>
            <td style="border-bottom:1px solid #ccc;" align="">{{(int)$sumOfIndentQty}}</td>
            </tr>-->
          </tbody>
          
        </table>
    </div>
  </div>
<?php /*  
  <div class="row">
    <div class="col-md-12">
      <div class="table-scrollable">
        <table class="table table-striped table-bordered table-advance table-hover">
          <thead>
            <tr style="font-size:14px;">
              <th width="33%">Total Qty</th>
              <th width="33%">Total</th>
              <th width="33%">Grand Total</th>
            </tr>
          </thead>
          <tbody>
            <tr class="odd gradeX">
              <td>{{$sumOfIndentQty}}</td>
              <td>{{$product->symbol}} 0.0</td>
              <td>{{$product->symbol}} 0.0</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>*/?>
</div>
</body>
</html>
