<div class="condition_container">
    <div class="row">
        <div class="col-md-12">
            <h5><strong>Offer Condition</strong></h5>
            <hr />
        </div>
    </div> 

    <!-- Offer Condition Part Goes here -->
    <div class="row">
        <div class="col-md-10">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="Select offer tmpl">Select Offer</label>
                        <select id = "condition"  name =  "condition" class="form-control" onChange="changeOffer(this);">
                            <option value = "">--Please Select--</option>
                            <option value = "Discount" @if($getpromotionData->prmt_offer_type == 'Discount') {{'selected'}}@endif> Discount </option>
                            <option value = "FreeQty" @if($getpromotionData->prmt_offer_type == 'FreeQty') {{'selected'}}@endif> Free Quantity </option>
                        </select>
                    </div>
                </div>

                <div class="for_discount">
                <div class="col-md-4">
                    <label for="promotion_name">Discount</label>
                    <input type="text" class="from-control" name="discount_offer" id="discount_offer" value = "{{$getpromotionData->prmt_offer_value}}" maxlength="100"/>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="mt-checkbox-list margt">
                            <label class="mt-checkbox">
                                <input type="checkbox" id="offon_percent" value="1" name = "offon_percent" @if($getpromotionData->is_percent_on_free == '1') {{'checked'}}@endif> is_percent
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
                </div>

                <div class="for_free_product" style = "display:none;">
                <div class="col-md-4">
                    <label for="promotion_name">Select Product</label>
                    <select id="select_product" name =  "select_product[]" class="form-control multi-select-search-box" multiple="multiple">

                            <option value = "">--Please Select--</option>
                            @foreach($getpromotionFreeProducts as $select_free)
                            <option value = "{{ $select_free->product_id }}" @if (in_array($select_free->product_id, explode(',', $getpromotionData->prmt_free_product))) {{ "selected" }} @endif> {{ $select_free->product_title }} </option>
                            @endforeach
                        </select>
                </div>
                <div class="col-md-4">
                    <label for="promotion_name">Free Qty</label>
                    <input type="text" class="form-control" name="free_qty" id="free_qty" value = "{{$getpromotionData->prmt_free_qty}}"/>
                </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            &nbsp;
        </div>

    </div>
    </div>

</div>  
