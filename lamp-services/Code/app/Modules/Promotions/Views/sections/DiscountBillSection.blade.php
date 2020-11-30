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
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="Select offer tmpl">Manufacturer</label>
                        <select id = "offertypeman"  name =  "offertypeman[]" class="form-control multi-select-search-box" multiple="multiple" onChange="changeOffer(this);">
                            @foreach($manufac as $manu)

                                <option value = "{{$manu->legal_entity_id}}">{{$manu->business_legal_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="Select offer tmpl">Brand</label>
                        <select id = "offertypebrand"  name =  "offertypebrand[]" class="form-control multi-select-search-box" multiple="multiple" onChange="changeOffer(this);">
                            @foreach($brand as $data)
                                <option value = "{{$data->brand_id}}">{{$data->brand_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="for_discont">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="promotion_name">Bill Value</label>
                        <input type="text" class="form-control" name="bill_value" id="bill_value"/>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label for="promotion_name">Discount</label>
                        <input type="text" class="form-control" name="discount_offer_bill" id="discount_offer_bill"/>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="Select offer tmpl">Product Star</label>
                        <select id = "ProductStar_on_bill"  name =  "ProductStar_on_bill[]" class="form-control multi-select-search-box" multiple="multiple" >
                        <option value="0">All</option>
                            @foreach($product as $data)
                            <option value = "{{$data['mas_cat_id']}}">{{$data['master_lookup_name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>

                <div class="col-md-1">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="mt-checkbox-list margt">
                            <label class="mt-checkbox">
                                <input type="checkbox" id="offon_percent" value="1" name = "offon_percent"> Percentage
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    </div> 
