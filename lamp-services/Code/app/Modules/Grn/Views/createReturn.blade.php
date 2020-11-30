@if($totalRecvedQty>$totalReturnQty)
<form id="saveReturn">
    <div class="row">
        <div class="col-md-12">
            <h4>Product Details</h4>
            <div class="table-scrollable">
                <table class="table table-striped table-bordered table-advance table-hover" id="sample_2">
                    <thead>
                        <tr>
                            <th style="text-align: center;"><input type="checkbox" id="checkall"/></th>
                            <th style="text-align: center;">SKU#</th>
                            <th width="20%">Product Title</th>
                            <th>MRP</th>
                            <th>GRN Qty</th>
                            <th title="Return from SOH">SOH Qty</th>
                            <th title="Return from Damaged Qty">DIT Qty</th>
                            <th title="Return from Missing Qty">DND Qty</th>
                            <th>Return Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $slno = 0;
                        ?>
                        <input type="hidden" value="{{(isset($grnProductArr[0]->inward_id)?$grnProductArr[0]->inward_id:0)}}" name="inward_id"/>
                        @foreach($grnProductArr as $product)
                        <?php 
                        $ret_soh = (int)$product->ret_soh_qty;
                        $ret_dit = (int)$product->ret_dit_qty;
                        $ret_dnd = (int)$product->ret_dnd_qty;
                        //echo '<pre/>';print_r($product);die;
                        $pending_soh_Qty = (int) $product->good_qty - $ret_soh;
                        $pending_dit_Qty = (int) $product->damage_qty - $ret_dit;
                        $pending_dnd_Qty = (int) $product->missing_qty - $ret_dnd;
                        ?>
                        @if($pending_soh_Qty>0)
                        <tr>
                            <td align="center"><input type="checkbox" name="selected[{{$product->product_id}}]" id="check{{$product->product_id}}" value="1" class="check"></td>
                            <td align="center">{{$product->sku}}</td>
                            <td>{{$product->product_title}}</td>
                            <td>{{$product->mrp}}</td>
                            <td>{{(int)$product->received_qty}}<br>
                                <span style="font-size:10px;">
                                <strong>Pending Qty: (SOH:{{(int)$pending_soh_Qty}},DIT:{{(int)$pending_dit_Qty}},DND:{{(int)$pending_dnd_Qty}})</strong>
                                @if(($ret_soh+$ret_dit+$ret_dnd)>0)
                                <strong>Returned Qty: (SOH:{{(int)$product->ret_soh_qty}},DIT:{{(int)$product->ret_dit_qty}},DND:{{(int)$product->ret_dnd_qty}})</strong><br/>
                                @endif
                                </span>
                            </td>
                            <td>
                                <input type="hidden" value="{{$product->product_id}}" name="product_id[{{$product->product_id}}]" class="product_id"/>
                                <input type="hidden" value="{{$product->sku}}" id="product_sku{{$product->product_id}}" class="sku"/>
                                <input type="number" style="width:85px;" min="0" max="{{$pending_soh_Qty}}" value="0" name="soh_qty[{{$product->product_id}}]" id="soh_qty{{$product->product_id}}" class="return_qty"/>
                            </td>                            
                            <td>
                                <input type="number" style="width:85px;" min="0" max="{{$pending_dit_Qty}}" value="0" name="dit_qty[{{$product->product_id}}]" id="dit_qty{{$product->product_id}}" class="return_qty"/>
                            </td>
                            <td>
                                <input type="number" style="width:85px;" min="0" max="{{$pending_dnd_Qty}}" value="0" name="dnd_qty[{{$product->product_id}}]" id="dnd_qty{{$product->product_id}}" class="return_qty"/>
                            </td>
                            <td>
                                <select name="return_reason[{{$product->product_id}}]" class="form-control">
                                    @foreach($reasonsArr as $key=>$reason)
                                    <option value="{{$key}}">{{$reason}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <textarea name="return_comment" class="form-control"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <input type="submit" value="Save" class="form-control btn green-meadow" style="width: 120px"/>
            </div>
        </div>
    </div>
</form>
@else
<div class="row">
    <div class="col-md-12">
        <div class="form-group alert alert-danger">You have already created return of all grn quantity.</div>
    </div>
</div>
@endif