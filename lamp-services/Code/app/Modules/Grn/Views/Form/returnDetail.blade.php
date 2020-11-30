<div class="row">
    <div class="col-md-4">
        <h4>Supplier Details</h4>
        <div class="well1 margin-top-10">
            <div class="table-scrollable table-scrollable-borderless">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><strong> Name </strong></td>
                            <td>{{$returnProductArr[0]->business_legal_name}} </td>
                        </tr>
                        <tr>
                            <td><strong> Address </strong></td>
                            <td>{{$returnProductArr[0]->address1}}, {{$returnProductArr[0]->address2}},
                                {{$returnProductArr[0]->state_name}}, {{$returnProductArr[0]->city}} - {{$returnProductArr[0]->pincode}} </td>
                        </tr>
                        <tr>
                            <td><strong> Phone </strong></td>
                            <td>{{$returnProductArr[0]->legalMobile}}</td>
                        </tr>
                        <tr>
                            <td><strong> Email </strong></td>
                            <td>{{$returnProductArr[0]->legalEmail}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <h4>Warehouse Address</h4>
        <div class="well1 margin-top-10">
            <div class="table-scrollable table-scrollable-borderless">
                <table class="table">
                    <tbody>
                        @if(!empty($whInfo->le_wh_code))
                        <tr>
                            <td><strong>Code:</strong></td>
                            <td> {{$whInfo->le_wh_code}} </td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td> {{$whInfo->lp_wh_name}} </td>
                        </tr>
                        <tr>
                            <td valign="top"><strong> Received At: </strong></td>
                            <td valign="top">{{$returnProductArr[0]->dc_address1}}<br />{{$returnProductArr[0]->dc_address2}} </td>
                        </tr>
                        <tr>
                            <td><strong> Phone: </strong></td>
                            <td>{{$whInfo->phone_no}}</td>
                        </tr>
                        <tr>
                            <td><strong> Email:</strong></td>
                            <td> {{$whInfo->email}}</td>
                        </tr>
                        <tr>
                            <td><strong> Contact&nbsp;Person:</strong></td>
                            <td> {{$whInfo->contact_name}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <h4>Return Details</h4>
        <div class="well1 margin-top-10">
            <div class="table-scrollable table-scrollable-borderless">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><strong>Return Code</strong></td>
                            <td> {{$returnProductArr[0]->pr_code}} </td>
                        </tr>
                        <tr>
                            <td><strong> Date </strong></td>
                            <td> {{Utility::dateTimeFormat($returnProductArr[0]->created_at)}} </td>
                        </tr>
                        <tr>
                            <td><strong>GRN Code</strong></td>
                            <td> {{$returnProductArr[0]->inward_code}} </td>
                        </tr>
                        <tr>
                            <td><strong> Created By </strong></td>
                            <td>{{$returnProductArr[0]->createdBy}}</td>
                        </tr>
                        <?php /*
                        <tr>
                            <td><strong> Approval&nbsp;Status </strong></td>
                            <td> {{$approvedStatus}} </td>
                        </tr>*/?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h4>Returned Products</h4>
        <div class="table-scrollable">
            <table class="table table-striped table-bordered table-advance table-hover" id="sample_2">
                <thead>
                    <tr>
                        <th>SKU#</th>
                        <th width="20%">Product Title</th>
                        <th>MRP (Rs.)</th>
                        <th>Unit&nbsp;Base&nbsp;Price&nbsp;(Rs.)</th>
                        <th>Return QTY(Ea)</th>
                        <th>Sub&nbsp;Total&nbsp;(Rs.)</th>
                        <th>Tax&nbsp;%</th>
                        <th>Tax&nbsp;Value (Rs.)</th>
                        <th>Total&nbsp;Value&nbsp;(Rs.)</th>
                        <th>Return Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $slno = 0;
                    $totQty = 0;
                    $totalBaseValue = 0;
                    $totalTax = 0;
                    $subTotal = 0;
                    $totSubTotal = 0;
                    ?>
                    @foreach($returnProductArr as $product)
                    <?php $totQty = $totQty + (int) $product->qty; ?>
                    <tr>
                        <td align="center">{{$product->sku}}</td>
                        <td>{{$product->product_title}}</td>
                        <td>{{number_format($product->mrp, 5)}}</td>
                        <td>{{number_format($product->unit_price, 5)}}</td>
                        <td>{{$product->qty}}</td>                        
                        <td>{{number_format($product->sub_total, 5)}}</td>
                        <td>
                            {{(float)$product->tax_per}}%
                        </td>
                        <td>{{number_format($product->tax_total, 5)}}</td>
                        <td>{{number_format($product->total, 5)}}</td>
                        <td>{{(isset($reasonsArr[$product->reason]))?$reasonsArr[$product->reason]:''}}</td>
                    </tr>
                    <?php
                    $totalBaseValue +=$product->sub_total;
                    $totalTax +=$product->tax_total;
                    ?>
                    @endforeach
                </tbody>
            </table>
        </div>
        <table class="table table-striped table-bordered table-advance table-hover" id="sample_2">
            <thead>
                <tr>
                    <th width="10%">Total Qty</th>
                    <th width="10%">Total Base value</th>
                    <th width="10%">Total Tax</th>
                    <th width="10%">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                <tr class="odd gradeX">
                    <td>{{$returnProductArr[0]->pr_total_qty}}</td>
                    <td>{{number_format(($totalBaseValue), 5)}}</td>
                    <td>{{number_format(($totalTax), 5)}}</td>
                    <td>{{number_format($returnProductArr[0]->pr_grand_total, 5)}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@if(isset($returnProductArr[0]->pr_remarks) && $returnProductArr[0]->pr_remarks!='')
<div class="row">
    <div class="col-md-4">
        <h4>Comments</h4>
        <p>{{(isset($returnProductArr[0]->pr_remarks))?$returnProductArr[0]->pr_remarks:''}}</p>
    </div>
</div>
@endif
<div class="row">
    <div class="col-md-4">
        @include('PurchaseOrder::Form.approvalForm')
    </div>
</div>