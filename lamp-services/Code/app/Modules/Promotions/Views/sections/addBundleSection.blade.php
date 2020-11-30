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
                        <select id = "offertype"  name =  "offertype" class="form-control" onChange="changeOffer(this);">
                            <option value = "">--Please Select--</option>
                            <option value = "Discount"> Discount </option>
                            <option value = "FreeQty"> Free Quantity </option>
                        </select>
                    </div>
                </div>

                <div class="for_discount">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="promotion_name">Discount</label>
                        <input type="text" class="form-control" name="discount_offer" id="discount_offer" onclick="handleChange()"/>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="mt-checkbox-list margt">
                            <label class="mt-checkbox">
                                <input type="checkbox" id="offon_percent" value="1" name = "offon_percent"> is_percent
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
                </div>

                <div class="for_free_product" style = "display:none;">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="promotion_name">Select Product</label>
                        <select id="select2_sample2" name =  "select_product[]" class="form-control multi-select-search-box" multiple="multiple">
                                @foreach($select_product as $selectProduct)
                                    <option value = "{{$selectProduct->product_id}}">{{$selectProduct->product_title}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="promotion_name">Free Qty</label>
                        <input type="text" class="form-control" name="free_qty" id="free_qty"/>
                    </div>
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
