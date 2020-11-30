<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8" >
    </head>
    <body>
        <div>
            @foreach($ebutor_address as $ebutorKey )
            <div class="row">
                <div class="col-sm-5 col-xs-4 ">
                    <table width="90%">
                        <tr>
                            <td>
                                <table width="100%" >
                                    <tr>
                                        <td align="left" class="small" valign="middle">
                                            <b><lable class="small1" >{{$ebutorKey->seller_company_name}}</label></b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" class="small" valign="middle">
                                            <b><lable class="small1" >{{$ebutorKey->seller_address_1}}</label></b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" class="small" valign="middle">
                                            <b><lable class="small1" >{{$ebutorKey->seller_address_2}}</label></b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" class="small" valign="middle">
                                            <b><lable class="small1" >{{$ebutorKey->seller_city}}, {{$ebutorKey->seller_state}}, {{$ebutorKey->seller_country}},  {{$ebutorKey->seller_zipcode}}.</label></b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" class="small" valign="middle">
                                            <b><lable class="small1">EMAIL ID: </lable><lable class="small1" >{{$ebutorKey->email}}, </label></b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" class="small" valign="middle">
                                            <b><lable class="small1">PHONE NO: </lable><lable class="small1" > {{$ebutorKey->mobile_no}}. </label></b>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="float: right;">
                                <img src="{{$image->imageUrl}}" alt="Ebutor" height="72" /> 
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            @endforeach
            <br />
            <br />
            <table width="25%" >
                <tr>
                    <td align="left" class="small" valign="middle"><b><lable class="small1" >Order ID</lable></b></td>
                    <td align="left" valign="middle"><span class="small2">: {{$order_details[0]->gdsorderid}}</span></td>
                </tr>
                <tr>
                    <td align="left" class="small" valign="middle"><lable class="small1" ><b>Order Date</b></lable></td>
                <td align="left" valign="middle"><span class="small2">: {{$order_details[0]->order_date}}</span></td>
                </tr>
                <tr>
                    <td align="left" class="small" valign="middle"><lable class="small1" ><b>Order Placed On </b></lable></td>
                <td align="left" valign="middle"><span class="small2">: {{$order_details[0]->channnel_name}}</span></td>
                </tr>
                <tr>
                    <td align="left" class="small" valign="left"><lable class="small1" ><b>Jurisdiction Only</b></label></td>
                    <td align="left" valign="middle"><span class="small2">: Hyderabad</span></td>
                </tr>
            </table>
            <br />
            <br />
            <table width="90%">
                <tr>
                    <td><b><u>Billing Address</u></b></td>
                    <td><b><u>Shipping Address</u></b></td>
                </tr>
                <tr>
                    <td>
                        @foreach($address as $det)
                            @if($det->address_type=='billing')
                            <table>
                                <tr><td><b>Company:</b></td><td>{{$det->company}}</td></tr>
                                <tr><td><b>Name:</b></td><td>{{$det->fname}}</td></tr>
                                <tr><td><b>Address Line1:</b></td><td>{{$det->addr1}}</td></tr>
                                <tr><td><b>Address Line2:</b></td><td>{{$det->addr2}}</td></tr>
                                <tr><td><b>Mobile:</b></td><td>{{$det->mobile}}</td></tr>
                                <tr><td><b>Telephone:</b></td><td>{{$det->telephone}}</td></tr>
                                <tr><td><b>City:</b></td><td>{{$det->city}}</td></tr>
                                <tr><td><b>State:</b></td><td>{{$det->state}}</td></tr>
                                <tr><td><b>Country:</b></td><td>{{$det->country}}</td></tr>
                                <tr><td><b>Postcode:</b></td><td>{{$det->postcode}}</td></tr>
                            </table>
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach($address as $det)
                            @if($det->address_type=='shipping')
                                <table>
                                    <tr><td><b>Company:</b></td><td>{{$det->company}}</td></tr>
                                    <tr><td><b>Name:</b></td><td>{{$det->fname}}</td></tr>
                                    <tr><td><b>Address Line1:</b></td><td>{{$det->addr1}}</td></tr>
                                    <tr><td><b>Address Line2:</b></td><td>{{$det->addr2}}</td></tr>
                                    <tr><td><b>Mobile:</b></td><td>{{$det->mobile}}</td></tr>
                                    <tr><td><b>Telephone:</b></td><td>{{$det->telephone}}</td></tr>
                                    <tr><td><b>City:</b></td><td>{{$det->city}}</td></tr>
                                    <tr><td><b>State:</b></td><td>{{$det->state}}</td></tr>
                                    <tr><td><b>Country:</b></td><td>{{$det->country}}</td></tr>
                                    <tr><td><b>Postcode:</b></td><td>{{$det->postcode}}</td></tr>
                                </table>
                            @endif
                        @endforeach
                    </td>
                </tr>
<!--            </table>
            <br />            
            <br />
            <table class="table table-bordered" border="1px solid" width="80%">-->
                <!--<tbody>-->
                <tr>
                    <td colspan="10">
                        <table class="table table-bordered" border="1px solid" style="width: 100%;">
                    <?php $currency = "INR"; ?>
                    <tr style="background-color:#efefef;">
                        <th class="col-xs-1 text-center small1"><label class="small">SNO</label></th>
                        <th class="col-xs-1 text-center small1"><label class="small">SKU</label></th>
                        <th class="col-md-4 text-center col-xs-4 small1 rssixze"  ><label class="small">Product Name</label></th>
                        <th class="col-xs-1 text-center small1"> <label class="small">MRP<br />({{$currency}})</lable></th>
                        <th class="col-xs-1 text-center small1"> <label class="small">Unit Price<br />({{$currency}})</lable></th>
                         <!-- <th>Cont <br /><p>Pack</p></th>
                         <th>Pack. Price <br /><p>(INR)</p></th>
                        --><th class="col-xs-1 text-center small1"> <label class="small">Qty.</lable></th>
                        <th class="col-xs-1 text-center small1"> <label class="small">Net Amt <br />({{$currency}})</label></th>
                        <th class="col-xs-1 text-center  small1"><label class="small">Net Disc. Amt. <br /> ({{$currency}})</label></th>
                        <th class="col-xs-1 text-center small1"><label class="small">Tax Amt. <br />({{$currency}})</label></th>
                        <th class="col-xs-1 text-center small1"><label class="small">Total Amt.</label><br />({{$currency}})</p></th>
                    </tr>
                    <?php
                    $main_tot = 0;
                    $qty_total = 0;
                    $net_amt_tot = 0;
                    $dis_amt_tot = 0;
                    $net_dis_amt_tot = 0;
                    $tax_net_amt_tot = 0;
                    $tol = 0;
                    $shipping = 0;
                    $total_tax_price = '';
                    ?> 
                    @foreach($final_product_array as $result)
                    <tr>
                        <td class="small1"><p>{{$result['sno'] }}</p></td>
                        <td class="small1"><p>{{$result['sku'] }}</p></td>

                        <td class="small1"><p>{{$result['title'] }}</p></td>

                        <td  class="text-right small1 "><p> {{$result['mrp']}}</p></td>
                        <td  class="text-right small1 "><p> {{$result['unit_price']}}</p></td>
                        <td  class="text-center small1"><p>{{$result['quantity']}}</p></td>
                        <td  class="text-right small1"><p> {{number_format($result['cost'],2,'.',',')}}</p></td>
                        <td  class="text-right small1"><p>{{number_format($result['discount_price'],2,'.',',')}} ({{number_format($result['discount_price'],2,'.',',')}}%)</p></td>
                        <td class="text-right small1"><p>{{number_format($result['tax'],2,'.',',')}} ({{number_format($result['tax_percentage'],2,'.',',')}}%)</p></td>
                        <td  class="text-right small1"><p> {{number_format($result['subtotal'],2,'.',',')}}</p></td>
                        <?php
                        $qty_total = $qty_total + $result['quantity'];
                        $net_amt_tot = $net_amt_tot + $result['cost'];
                        $dis_amt_tot = $dis_amt_tot + $result['discount_price'];
                        $net_dis_amt_tot = $net_dis_amt_tot + $result['discount_price'];
                        $tax_net_amt_tot = $tax_net_amt_tot + $result['tax'];
                        $tol = $tol + $result['subtotal'];
                        $shipping = $order_details[0]->ship_total
                        ?>

                        <?php
                        $sub_tot = $result['subtotal'] + $order_details[0]->ship_total;
                        $main_tot = $main_tot + $sub_tot;
                        ?> 
                    </tr>

                    @endforeach

                    <tr>
                        <td colspan="1"  class="text-right small1"></td>
                        <td colspan="2" class="text-right small1"><lable class="small"><b>Total: </b></lable></td>
                <td colspan="1"></td>
                <td colspan="1"></td>
                <td colspan="1" class="text-center small1"><lable class="small">{{$qty_total}}</lable> </td>
                <td colspan="1" class="text-right small1"><lable class="small"> {{number_format($net_amt_tot,2,'.',',')}}</lable> </td>
                <td colspan="1" class="text-right small1"><lable class="small"> {{number_format($net_dis_amt_tot,2,'.',',')}}</lable> </td>
                <td colspan="1" class="text-right small"><lable class="small"> {{number_format($tax_net_amt_tot,2,'.',',')}} </lable></td>
                <td colspan="1" class="text-right"><lable class="small1"> {{number_format($tol,2,'.',',')}}</lable> </td>
                </tr>
                <tr>
                    <td colspan="7" class="text-right small1"><b><lable class="small1"> Shipping Amount:</lable></b> </td>
                    <td colspan="4" class="text-right small1"><b><lable class="small1"> {{$shipping}}</lable></b> </td>
                </tr>   
                <tr>
                    <td colspan="7" class="text-right small1"><b><lable class="small1"> Grand Total ({{$currency}}):</lable></b> </td>
                    <td colspan="4" class="text-right small1" style="font-size:16px;"><b><lable class="small2"> {{number_format($tol,2,'.',',')}}</lable></b> </td>
                </tr>
                <tr>
                    <td colspan="7" align="right" class="small2" valign="middle"><strong>Tax Summary</strong></td>
                    <td colspan="7">
                        <table width="100%">
                            @if(isset($tax_price_cal))
                                @foreach($tax_price_cal as $taxPriceKey)
                                <tr>
                                    <td width="50%" height="25" class="  small2" align="left" valign="middle" >{{$taxPriceKey->tax_price}}%</td>
                                    <td width="50%" height="25" class=" small2" align="left" valign="middle">{{$taxPriceKey->tax}}</td>
                                    <td width="50%" height="25" class="small2" align="left" valign="middle">{{$taxPriceKey->tax}}</td>
                                </tr>
                                @endforeach
                                <tr class="line">
                                    <td height="25" class="small2" align="left" class="col-md-4 col-xs-4" valign="middle"><strong>Total ({{$currency}})</strong></td>
                                    <td height="25" align="left" class="small" valign="middle">&nbsp;</td>
                                    <td height="25" align="left" class="small1" valign="middle"><b>{{number_format($tax_net_amt_tot+$net_amt_tot,2,'.',',')}}</b></td>
                                </tr>
                            @endif
                        </table>
                    </td>
                </tr>
                </table>
                </td>
                </tbody>
            </table>
            <br />
        </div>
    </body>
</html>