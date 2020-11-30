@foreach($products as $product)
  <tr>
    <td align="center">{{$product->sku}}</td>
    <td>{{$product->seller_sku}}</td>
    <td>{{$product->product_title}}</td>
    <td>{{(int)$product->received_qty}}</td>
    <td><input class="form-control" min="0" max="{{(int)$product->rem_qty}}" type="number" size="3" value="{{(int)$product->rem_qty}}" name="qty[]">
    </td>



    <td>{{number_format($product->mrp, 2)}}</td>

    <td>{{number_format($product->base_price, 2)}}</td>
    <td>{{number_format(($product->received_qty*$product->base_price), 2)}}</td>

    <input name='product_info[]' type='hidden' value='{{json_encode($product)}}'/></td>

  </tr>

  @if(isset($packArr[$product->inward_prd_id]) && count($packArr[$product->inward_prd_id]) > 0)
  <tr style="display:none;" id="packinfo-{{$product->inward_prd_id}}">
    <td colspan="12">
      <table class="table table-striped">
        <thead>
          <tr>
            <th></th>
            <th style="font-size:10px;"><strong>Pack Size</strong></th>
            <th style="font-size:10px;"><strong>Received</strong></th>
            <th style="font-size:10px;"><strong>Tot. Rec. Qty</strong></th>
            <th style="font-size:10px;"><strong>MFG Date</strong></th>
            <th style="font-size:10px;"><strong>EXP Date</strong></th>
            <th style="font-size:10px;"><strong>Freshness</strong></th>
          </tr>
        </thead>
        @foreach($packArr[$product->inward_prd_id] as $pack)
          <tr>
            <td></th>
            <td><span style="font-size:10px;">{{$pack->pack_qty}} {{$pack->pack_level}}</span></td>
            <td><span style="font-size:10px;">{{$pack->received_qty}}</span></td>
            <td><span style="font-size:10px;">{{$pack->tot_rec_qty}}</span></td>
            <td><span style="font-size:10px;">@if(!empty($pack->mfg_date) && $pack->mfg_date != '0000-00-00' && $pack->mfg_date != '1970-01-01') {{ date('d-m-Y', strtotime($pack->mfg_date)) }}@endif</span></td>
            <td><span style="font-size:10px;">@if(!empty($pack->exp_date) && $pack->exp_date != '0000-00-00' && $pack->exp_date != '1970-01-01') {{ date('d-m-Y', strtotime($pack->exp_date)) }}@endif</span></td>
            <td><span style="font-size:10px;">{{round($pack->freshness_per)}} %</span></td>
          </tr>
        @endforeach
      </table>
    </td>
  </tr>
  @endif

@endforeach
