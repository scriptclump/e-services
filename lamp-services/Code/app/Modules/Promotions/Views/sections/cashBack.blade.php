<div class="condition_container">
        <div class="col-md-12">
            <h5><strong>Offer Condition</strong></h5>
            <hr/>
        </div>
    </div>
 <!-- Offer Condition Part Goes here -->
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label>Description</label>
                <input type="text"  class="form-control" id = "cashback_description" name = "cashback_description"/>
                <div class="cust-error-desc"></div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label for="Select offer tmpl">Beneficiary Type </label>
                 <select id = "Benificiary"  name =  "Benificiary[]" class="form-control multi-select-search-box" >

                    <option value = "">--Please Select--</option>
                    @foreach($order as $data)
                        <option value = "{{$data['role_id']}}">{{$data['name']}}</option>
                    @endforeach
                </select> 
                <div class="cust-error-cash-benificiary"></div>                
            </div>
        </div>

        <div class="col-md-2 cashback_cls">
            <div class="form-group">
                <label for="Select offer tmpl">Manufacturer</label>
                <select id = "offertypemanf"  name =  "offertypemanf[]" class="form-control multi-select-search-box" multiple="multiple" style="height: 30px">

                    <option value = "">--Please Select--</option>
                    <option value = "0">All</option>
                    @foreach($manufac as $manu)
                        <option value = "{{$manu->legal_entity_id}}">{{$manu->business_legal_name}}</option>
                    @endforeach
                </select>
                <div class="cust-error-cash-manuf"></div>
            </div>
        </div>
        <div class="col-md-2 cashback_cls">
            <div class="form-group">
                <label for="Select offer tmpl">Brand</label>
                <select id = "offertypbrand"  name =  "offertypbrand[]" class="form-control  multi-select-search-box" multiple="multiple" style="height: 30px">

                    <option value = "">--Please Select--</option>
                    <option value="0">All</option>
                    @foreach($brand as $data)
                        <option value = "{{$data->brand_id}}">{{$data->brand_name}}</option>
                    @endforeach
                </select>
                <div class="cust-error-cash-brand"></div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="Select offer tmpl">Product Star</label>
                <select id = "ProductStar"  name =  "ProductStar[]" class="form-control">

                    <option value = "">--Please Select--</option>
                    <option value = "0">All</option>

                    @foreach($product as $data)
                        <option value = "{{$data['value']}}">{{$data['master_lookup_name']}}</option>
                    @endforeach
                </select>
                 <div class="cust-error-ProductStar"></div>

            </div>
        </div>
        
        
        <div class="col-md-2 incentive_cls">
            <div class="form-group">
                <label for="Select offer tmpl">Product Group</label>
                <select id = "prd_grp"  name ="prd_grp[]" class="form-control multi-select-search-box" multiple="multiple">
                    <option value = "0">All</option>

                    @foreach($product_groups as $prd)
                        <option value = "{{ $prd->product_grp_ref_id }}">{{ $prd->product_grp_name }}</option>
                    @endforeach
                </select>
                 <div class="cust-error-ProductGRP"></div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label for="promotion_name">From</label>
                <input type="text" class="form-control" name="cash_back_from" id="cash_back_from"/> 
                <div class="cust-error-cash_back_from"></div>
            </div>    
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="promotion_name">To</label>
                <input type="text" class="form-control" name="cash_back_to" id="cash_back_to"/> 
                <div class="cust-error-cash_back_to"></div>
            </div>    
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="promotion_name">Discount</label>
                    <input type="text" class="form-control" name="discount_offer_on_bill" id="discount_offer_on_bill"/>
                    <div class="cust-error-discount_offer_on_bill"></div>
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="mt-checkbox-list">
                    <label class="mt-checkbox">
                        <input type="checkbox" id="offon_percent_cashback" > %
                    </label>
                 </div>
            </div>
        </div>

        <div class="col-md-2">
                      <div class="form-group">
                        <label>Cap Limit</label>
                        <input type="text" id="cap_limit" name = "cap_limit" class="form-control" autocomplete="off"> 
                        <div class="cust-error-cash_cap_limit"></div>
                     </div>  
        </div>
        <div class="col-md-2 cashback_cls"  >
                <div class="form-group">
                    <label>Product Value</label>
                    <input type="text" id="product_value" name = "product_value" class="form-control" autocomplete="off">
                    <div class="cust-error-cash_product_value"></div>
                </div>
        </div>
    </div>
    <div class="row">

        <div class="col-md-2 cashback_cls"  >
                <div class="form-group">
                    <label>Type</label>
                    <select id="order_type" name="order_type" class="form-control col-md-2">
                        <option value="">Select Type</option>
                        <option value="1">Self</option>
                        <option value="0">Manual</option>
                        <option value="2">Both</option>
                    </select>
                    <div class="cust-error-cash_order_type"></div>
                </div>
        </div> 
        <div class="col-md-2">
            <div class="form-group">
                <label for="Select offer tmpl">Exclude Brands</label>
                <select id = "excludebrand"  name =  "excludebrand[]" class="form-control  multi-select-search-box" multiple="multiple" style="height: 30px">

                    <option value = "">--Please Select--</option>
                    @foreach($brand as $data)
                        <option value = "{{$data->brand_id}}">{{$data->brand_name}}</option>
                    @endforeach
                </select>
                <div class="cust-error-cash-brand"></div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="multiple" class="control-label">Exclude Product group's</label>
                <select id="excl_prod_group_id" name = "excl_prod_group_id[]" class="form-control multi-select-search-box" multiple style="height:30px">
                    @foreach($product_groups as $product)
                        <option value = "{{ $product->product_grp_ref_id }}">{{ $product->product_grp_name }}</option>
                    @endforeach
                </select>                    
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group incentive_cls">
                <label for="multiple" class="control-label">Exclude Categories</label>
                <select id="excl_Category_id" name = "excl_Category_id[]" class="form-control multi-select-search-box" multiple style="height:30px">
                    @foreach($categorie_groups as $categories)
                        <option value = "{{ $categories->category_id }}">{{ $categories->cat_name }}</option>
                    @endforeach
                </select>                    
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="form-group incentive_cls">
                <label for="multiple" class="control-label">Exclude Manufacturers</label>
                <select id="excl_manf_id" name = "excl_manf_id[]" class="form-control multi-select-search-box" multiple style="height:30px">
                    @foreach($manufacture_groups as $manufactures)
                        <option value = "{{ $manufactures->legal_entity_id }}">{{ $manufactures->business_legal_name }}</option>
                    @endforeach
                </select>                    
            </div>
        </div> 
        <div class="col-md-1">
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="but-add-promotion">
                    <label class="ad-button">
                        <button type="button" id="adding_cashback" name="adding_cashback" class="btn green-meadow">Add</button>
                    </label>
                 </div>
            </div>
        </div> 
    </div>
    <div class="row">         
        <div  class="col-md-12">
            <div class="cust-error-no-cb-lines"></div>
            <table class="table table-striped table-bordered table-hover table-advance" id="cashback_table" name = "cashback_table[]" style="font-size: 12px">
                <thead>
                    <tr>
                        <th>State</th>
                        <th>CustGrp</th> 
                        <th>Desc</th>                       
                        <th>Mnf</th>
                        <th>Brand</th>
                        <th>excl Brand</th>
                        <th>Prd group</th>
                        <th>Benf</th>
                        <th>Prd Star</th>
                        <th>WH</th>
                        <th>From</th>
                        <th>To</th>                        
                        <th>CB</th>
                        <th>%</th>
                        <th>Cap Limit</th>
                        <th>Product Value</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </div>

<style type="text/css">
    div[class*='cust-error']{
        color: #a94442;
    }
</style>