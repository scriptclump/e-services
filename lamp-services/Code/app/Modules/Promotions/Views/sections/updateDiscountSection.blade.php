<div class="discount_container">
    <div class="row">
        <div class="col-md-12">
            <h5><strong>Product Section</strong></h5>
            <hr />
        </div>
    </div> 
    <!-- Offer Condition Part Goes here -->
    <div class="row">
        <div class="col-md-10">
            <div class="row">
                <div class="discount_free_product">
                <div class="col-md-4">
                    <label for="promotion_name">Product</label>
                    <select id="item_id" name =  "item_id[]" class="form-control multi-select-search-box" multiple="multiple">
                            @foreach($getProductAndCategory as $select_free)
                            <option value = "{{ $select_free->ItemID }}" @if (in_array($select_free->ItemID, explode(',', $getpromotionData->applied_ids))) {{ "selected" }} @endif> {{ $select_free->ItemName }} </option>
                            @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="promotion_name">Set Qty</label>
                    <input type="text" class="form-control" name="set_qty" id="set_qty" value = "{{$getpromotionData->prmt_condition_value1}}"/>
                </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            &nbsp;
        </div>
    </div>
    </div>
