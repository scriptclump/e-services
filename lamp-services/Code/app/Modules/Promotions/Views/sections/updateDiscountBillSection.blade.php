<div class="condition_container">
    <div class="row">
        <div class="col-md-12">
            <h5><strong>Offer Condition</strong></h5>
            <hr />
        </div>
    </div> 
    <!-- Offer Condition Part Goes here -->
    <div class="row">
        <div class="col-md-12">
            <div class="row" class="for_discountbill">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="Select offer tmpl">Manufacturer</label>
                        <select id = "offertypeman_discount"  name =  "offertypeman_discount[]" class="form-control multi-select-search-box" multiple="multiple" onChange="changeOffer(this);">
                        {{$getpromotionData->prmt_manufacturers}}
                            @foreach($mandata as $manu)
                                <option value = "{{ $manu->legal_entity_id}}" @if (in_array($manu->legal_entity_id, explode(',', $getpromotionData->prmt_manufacturers))) {{ "selected" }} @endif> {{ $manu->business_legal_name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                 <div class="col-md-3">
                    <div class="form-group">
                        <label for="Select offer tmpl">Brand</label>
                        <select id = "offertypebrand_discount"  name =  "offertypebrand_discount[]" class="form-control multi-select-search-box" multiple="multiple" onChange="changeOffer(this);">
                            @foreach($branddata as $data)
                                <option value = "{{ $data->brand_id }}" @if (in_array($data->brand_id, explode(',', $getpromotionData->prmt_brands))) {{ "selected" }} @endif> {{ $data->brand_name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>                
            <div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="promotion_name">Bill Value</label>
                        <input type="text" class="form-control" name="update_bill_value" id="update_bill_value" value = "{{$getpromotionData->prmt_condition_value2}}" maxlength="100"/>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="promotion_name">Discount</label>
                        <input type="text" class="form-control" name="discount_offer_on_billvalue" id="discount_offer_on_billvalue" value = "{{$getpromotionData->prmt_offer_value}}" maxlength="100"/>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="Select offer tmpl">Product Star</label>
                        <select id = "ProductStar_on_bill_update"  name =  "ProductStar_on_bill_update[]" class="form-control multi-select-search-box" multiple="multiple">
                            <option value="0" @if (in_array('0', explode(',', $getpromotionData->product_star))) {{ "selected" }} @endif>All</option>
                            @foreach($productBill as $data)
                                <option value = "{{ $data->value }}" @if (in_array($data->value, explode(',', $getpromotionData->product_star))) {{ "selected" }} @endif> {{ $data->master_lookup_name }} </option>
                            @endforeach
                        </select>
                        <div class="cust-error-ProductStar"></div>
                    </div>
                </div>

                <div class="col-md-1">
                    <div class="form-group">
                    <label>&nbsp;</label>
                        <div class="mt-checkbox-list margt">
                        <label class="mt-checkbox">
                        <input type="checkbox" id="offon_percent_onbill" value="1" name = "offon_percent_onbill" @if($getpromotionData->is_percent_on_free == '1') {{'checked'}}@endif> Percentage
                        <span></span>
                        </label>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    </div> 
