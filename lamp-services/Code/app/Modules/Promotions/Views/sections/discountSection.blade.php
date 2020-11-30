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
            <div class="row" >
                <div class="discount_free_product">
                <div class="col-md-4" >
                    <div class="form-group">
                        <label for="promotion_name">Select Product</label>
                        <select id="item_id" name =  "item_id[]" class="form-control multi-select-search-box" multiple="multiple">
                                @foreach($select_product as $selectProduct)
                                    <option value = "{{$selectProduct->product_id}}">{{$selectProduct->product_title}}</option>
                                @endforeach
                            </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">  
                        <label for="promotion_name">Set Qty</label>
                        <input type="text" class="form-control" name="set_qty" id="set_qty"/>
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
