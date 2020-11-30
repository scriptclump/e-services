<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <title>Email Template</title>
    <style>
        .ReadMsgBody { width: 100%; }
        .ExternalClass { width: 100%; }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
        body { width:100% !important; margin: 0; padding: 0; -webkit-text-size-adjust: none; -ms-text-size-adjust: none; }
        table { border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        table td { border-collapse: collapse; } 
        h1, h2, h3, h4, h5, h6, p, a { margin: 0; padding: 0; }
        img { display: block; border: none; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
        .eventDate a, .eventDate a:visited { color: inherit; text-decoration: none; }
        /* Responsive Styles */
        @media only screen and (max-width: 640px){
            table.container { width: 650px !important; }
            table.columnContainer { width: 100% !important; }
            table.columnSpacing { width: 100% !important; }
            table.columnSpacing1 { width: 100% !important; }
            td.footerContent { padding-left: 0 !important; }
            table.smIcons td { padding-left: 0 !important; padding-right: 10px !important; }
            img.columnImage { height: auto !important; max-width: 450px !important; width: 100% !important; }
            td.testimonialImg { padding-right: 15px !important; }
            td.testimonialImg img { width: 50px !important; height: 50px !important; }
            td.heightAuto { height: auto !important; }
            table.mobileHide { display: none !important; }
            tr.mobileHide { display: none !important; }
            td.mobileHide { display: none !important; }
        }
        @media only screen and (max-width: 479px){
            table.container { width: 100% !important; }
            table.columnContainer { width: 100% !important; }
            table.columnSpacing { width: 100% !important; }
            table.columnSpacing1 { width: 100% !important; }
            td.footerContent { padding-left: 0 !important; }
            table.smIcons td { padding-left: 0 !important; padding-right: 10px !important; }
            img.columnImage { height: auto !important; max-width: 450px !important; width: 100% !important; }
            td.testimonialImg { padding-right: 15px !important; }
            td.testimonialImg img { width: 50px !important; height: 50px !important; }
            td.heightAuto { height: auto !important; }
            table.mobileHide { display: none !important; }
            tr.mobileHide { display: none !important; }
            td.mobileHide { display: none !important; }
            td.heroSpacing { height: 60px !important; }
            td.boxedContent { padding-left: 20px !important; padding-right: 20px !important; }      
        }
    </style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#e2e5e7" style="width: 100% !important; margin: 0; padding: 0;">

    <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
        <tr><td align="center" width="100%" bgcolor="#e2e5e7">
            <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" style="max-width: 1020px;">
                <tr>
                    <td valign="top" bgcolor="#2e2e61">
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="width: 1020px;">
                            <tr><td valign="top" align="left">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr><td valign="top" align="left" width="100%" style="padding-top: 10px; padding-bottom: 10px;">
                                        <!-- Preheader -->
                                        <table border="0" cellpadding="0" cellspacing="0" align="left" width="100%" class="columnContainer">
                                            <tr>
                                                <td width="60%" align="left" valign="top" style="padding-top: 8px; padding-bottom: 8px; font-size: 16px; line-height: 24px; font-family: Arial,Helvetica,sans-serif; color: #ffffff; text-align: left;">
                                                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                            <td width="7%" align="left" valign="top"><img src="{{$imageUrl}}" width="30" height="36" /></td>
                                                            <td width="93%" align="left" valign="middle"><strong>Ebutor Distribution Private Limited</strong></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="40%" align="left" valign="top" style="padding-top: 8px; padding-bottom: 8px; font-size: 16px; line-height: 24px; font-family: Arial,Helvetica,sans-serif; color: #ffffff; text-align: left;">
                                                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="padding:20px; font-size:12px;font-family: Arial,Helvetica,sans-serif; color:#fff;">
                                                        <tr>
                                                          <td style="padding-left:10px;"><strong>Name:</strong> {{$warehouse->lp_wh_name}}
                                                          </td>
                                                        </tr>
                                                        <tr>
                                                          <td style="padding-left:10px;"><strong>Address:</strong>  {{$warehouse->address1}}
                                                            @if(!empty($warehouse->address2)) 
                                                            , {{$warehouse->address2}}
                                                            @endif
                                                            <br>{{$warehouse->city}},{{$warehouse->state_name}}, {{$warehouse->country_name}}, {{$warehouse->pincode}}</td>
                                                        </tr>
                                                        <tr>
                                                          <td style="padding-left:10px;"><strong>Phone:</strong>  {{$warehouse->phone_no}}</td>
                                                        </tr>
                                                        <tr>
                                                          <td style="padding-left:10px;"><strong>Email:</strong>  {{$warehouse->email}}</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                
                                            </tr>
                                        </table>

                                    </td></tr>

                                </table>
                            </td></tr>
                        </table>
                    </td></tr>
                </table>
            </td></tr>
        </table>    



        <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" style="max-width: 1020px;">
            <tr>
                <td valign="top" bgcolor="#ffffff" style="padding:20px;font-family: Arial,Helvetica,sans-serif; font-size:14px;"><table width="100%" border="1" cellspacing="2" cellpadding="2" style="border:1px solid #ccc;">


                    <thead>
                        <tr>
                            <th width="25%" height="30" align="left" valign="middle">Order ID</th>
                            <th width="25%" height="30" align="left" valign="middle">Order Date</th>
                            <th width="25%" height="30" align="left" valign="middle">Order Placed On</th>
                            <th width="25%" height="30" align="left" valign="middle">Jurisdiction Only</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="25%" height="30" align="left" valign="middle">{{$order_details->order_code}}</td>
                            <td width="25%" height="30" align="left" valign="middle">{{$order_details->order_date}}</td>
                            <td width="25%" height="30" align="left" valign="middle">{{$order_details->mp_name}}</td>
                            <td width="25%" height="30" align="left" valign="middle">Hyderabad</td>
                        </tr>
                    </tbody>
                </table></td>
            </tr>
            <tr><td valign="top" bgcolor="#ffffff" style="padding:20px;">

                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family: Arial,Helvetica,sans-serif; font-size:12px;">
                    <tr>
                        <td width="50%" align="left" valign="middle"><table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
                            <tr>
                                <td><strong style=" border-bottom:1px solid; padding-bottom:2px; font-size:14px;">Billing Address:</strong></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td><strong>Company:</strong></td>
                                <td>{{$billing->company}}</td>
                            </tr>
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{$billing->fname}}</td>
                            </tr>
                            <tr>
                                <td><strong>Address Line1:</strong></td>
                                <td>{{$billing->addr1}}</td>
                            </tr>
                            <tr>
                                <td><strong>Address Line2:</strong></td>
                                <td>{{$billing->addr2}}</td>
                            </tr>
                            <tr>
                                <td><strong>Mobile:</strong></td>
                                <td>{{$billing->mobile}}</td>
                            </tr>
                            <tr>
                                <td><strong>Telephone:</strong></td>
                                <td>{{$billing->telephone}}</td>
                            </tr>
                            <tr>
                                <td><strong>City:</strong></td>
                                <td>{{$billing->city}}</td>
                            </tr>
                            <tr>
                                <td><strong>State:</strong></td>
                                <td>{{$billing->state_name}}</td>
                            </tr>
                            <tr>
                                <td><strong>Country:</strong></td>
                                <td>{{$billing->country}}</td>
                            </tr>
                            <tr>
                                <td><strong>Postcode:</strong></td>
                                <td>{{$billing->postcode}}</td>
                            </tr>
                        </table></td>
                        <td width="50%" align="left" valign="middle"><table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
                            <tr>
                                <td><strong style=" border-bottom:1px solid; padding-bottom:2px; font-size:14px;">Shipping Address:</strong></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td><strong>Company:</strong></td>
                                <td>{{$shipping->company}}</td>
                            </tr>
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{$shipping->fname}}</td>
                            </tr>
                            <tr>
                                <td><strong>Address Line1:</strong></td>
                                <td>{{$shipping->addr1}}</td>
                            </tr>
                            <tr>
                                <td><strong>Address Line2:</strong></td>
                                <td>{{$shipping->addr2}}</td>
                            </tr>
                            <tr>
                                <td><strong>Mobile:</strong></td>
                                <td>{{$shipping->mobile}}</td>
                            </tr>
                            <tr>
                                <td><strong>Telephone:</strong></td>
                                <td>{{$shipping->telephone}}</td>
                            </tr>
                            <tr>
                                <td><strong>City:</strong></td>
                                <td>{{$shipping->city}}</td>
                            </tr>
                            <tr>
                                <td><strong>State:</strong></td>
                                <td>{{$shipping->state_name}}</td>
                            </tr>
                            <tr>
                                <td><strong>Country:</strong></td>
                                <td>{{$shipping->country}}</td>
                            </tr>
                            <tr>
                                <td><strong>Postcode:</strong></td>
                                <td>{{$shipping->postcode}}</td>
                            </tr>
                        </table></td>
                    </tr>
                </table>

            </td></tr>
        </table>


        <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
            <tr>
                <td align="center" width="100%" bgcolor="#e2e5e7">
                    <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" style="max-width: 1020px;">
                        <tr>
                            <td valign="top" bgcolor="#2e2e61" style="padding:10px;">

                                <table width="96%" border="1" align="center" cellpadding="5" cellspacing="5" style="padding:20px; font-size:12px;font-family: Arial,Helvetica,sans-serif; color:#fff; padding:10px; border-color:#fff;">


                                    <thead>
                                        <tr>
                                            <th align="left" valign="middle">SNO</th>
                                            <th align="left" valign="middle">SKU</th>
                                            <th align="left" valign="middle">Product Name</th>
                                            <th align="left" valign="middle">MRP</th>
                                            <th align="left" valign="middle">Base Price</th>
                                            <th align="left" valign="middle">Qty.</th>
                                            <th align="left" valign="middle">Discount</th>
                                            <th align="left" valign="middle">Tax %</th>
                                            <th align="left" valign="middle">Tax Value</th>
                                            <th align="left" valign="middle">Sub Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $main_tot = 0;
                                        $net_amt_tot = 0;
                                        $dis_amt_tot = 0;
                                        $net_dis_amt_tot = 0;
                                        $shipping = 0;
                                        $total_tax_price = '';
                                        ?>

                                        @foreach($final_product_array as $result)
                                        <tr>

                                            <td align="left" valign="middle">{{$result['sno'] }}</td>

                                            <td align="left" valign="middle">{{$result['sku'] }}</td>

                                            <td align="left" valign="middle">{{$result['product_name'] }}</td>

                                            <td align="left" valign="middle">{{$result['mrp']}}</td>

                                            <td align="left" valign="middle">{{number_format($result['unit_price'],2,'.',',')}}</td>

                                            <td align="left" valign="middle">{{$result['quantity']}}</td>

                                            <td align="left" valign="middle">{{number_format($result['discount_price'],2,'.',',')}}
                                            </td>
                                            <td align="left" valign="middle">{{number_format($result['tax_percent'],2,'.',',')}}%</td>

                                            <td align="left" valign="middle">{{number_format($result['tax'],2,'.',',')}}</td>

                                            <td align="left" valign="middle">{{number_format($result['total'],2,'.',',')}}</td>
                                        </tr>
                                        @endforeach

                                        <tr>
                                            <td align="left" valign="middle"></td>
                                            <td align="left" valign="middle"><b>Total: </b></td>
                                            <td align="left" valign="middle"></td>
                                            <td align="left" valign="middle"></td>
                                            <td align="left" valign="middle"></td>
                                            <td align="left" valign="middle">{{$qty_total}}</td>    

                                            <td align="left" valign="middle"></td>
                                            <td align="left" valign="middle"></td>
                                            <td align="left" valign="middle">{{number_format($tax_net_amt_tot,2,'.',',')}}</td>
                                            <td align="left" valign="middle">{{number_format($tol,2,'.',',')}}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="text-right small1" valign="middle"><b><label class="small1"> Shipping Amount:</lable></b> </td>
                                            <td colspan="4" class="text-right small1"><b><lable class="small1"> {{$shipping}}</lable></b> </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="text-right small1" valign="middle"><b><label class="small1"> Tax Summary:</lable></b> </td>
                                            <td colspan="4">
                                                <table width="100%">
                                                    @if(isset($taxSummary) && is_array($taxSummary) && count($taxSummary) > 0)
                                                    @foreach($taxSummary as $tax)
                                                    <tr>                     
                                                    <td class="col-md-8 name">{{$tax['name'].' ('.(float)$tax['tax'].'%)'}} : </td>
                                                    <td class="col-md-3 value">{{$orderdata->symbol}} {{$tax['tax_value']}} </td>
                                                    </tr>
                                                    @endforeach
                                                    @endif
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="text-right small1"><b><lable class="small1" valign="middle"> Grand Total ({{$currency}}):</lable></b> </td>
                                            <td colspan="4" class="text-right small1" style="font-size:16px;"><b><lable class="small2"> {{number_format($tol,2,'.',',')}}</lable></b> </td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>


        <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
            <tr><td align="center" width="100%" bgcolor="#e2e5e7">  
                <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" style="max-width: 1020px;">
                    <tr><td valign="top" bgcolor="#ffffff">
                        <table border="0" cellpadding="0" cellspacing="0" align="center" class="container" style="width: 1020px;">
                            <tr><td valign="top" align="left" style="padding: 60px 10px 60px 10px;">

                                <table border="0" cellpadding="0" cellspacing="0" width="100%" align="left" class="columnContainer">
                                    <tr>
                                        <td width="2" bgcolor="#2e2e61" style="font-size: 1px; line-height: 1px;" class="mobileHide">&nbsp;</td>
                                        <td align="left" valign="top" style="padding-left: 40px;" class="footerContent">
                                            <table border="0" cellpadding="0" cellspacing="0" align="left" width="100%">

                                                        <tr>
                                                          <td style="padding-left:10px;"><strong>Name:</strong> {{$warehouse->lp_wh_name}}
                                                          </td>
                                                       
                                                          <td style="padding-left:10px;"><strong>Address:</strong>  {{$warehouse->address1}}
                                                            @if(!empty($warehouse->address2)) 
                                                            , {{$warehouse->address2}}
                                                            @endif
                                                            <br>{{$warehouse->city}},{{$warehouse->state_name}}, {{$warehouse->country_name}}, {{$warehouse->pincode}}</td>
                                                        
                                                          <td style="padding-left:10px;"><strong>Phone:</strong>  {{$warehouse->phone_no}}</td>
                                                        
                                                          <td style="padding-left:10px;"><strong>Email:</strong>  {{$warehouse->email}}</td>
                                                        </tr>
                                                    </table>
                                            </td>
                                    </tr>
                                </table>    
                                </td></tr>
                                </table>
                            </td></tr>
                        </table>
                    </td></tr>
                </table>    

            </body>
            </html>