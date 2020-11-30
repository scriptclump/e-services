<div class="condition_container">
    <div class="row">
        <div class="col-md-12">
            <h5><strong>Offer Condition</strong></h5>
            <hr />
        </div>
    </div> 
    <!-- Offer Condition Part Goes here -->
    
            <div class="row" class="for_discountbill" style="padding: 15px">
                <div class="row ">
                    <div class="col-md-2">
                        <div class="form-group">
                        <label>Description</label>
                        <input type="text"  class="form-control" id = "update_cashback_description" name = "update_cashback_description"/>
                        <div class="cust-error-update_cashback_description"></div> 
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="Select offer tmpl">Beneficiary Type </label>
                             <select id = "benificiary_update"  name =  "benificiary_update[]" class="form-control multi-select-search-box" >
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
                            <select id = "offertypemanf_cashback"  name =  "offertypemanf_cashback[]" class="form-control multi-select-search-box" multiple="multiple" onChange="changeOffer(this);" style="height: 30px">
                            <option value = "">--Please Select--</option>
                            <option value = "0">All</option>
                                @foreach($mandata as $manu)
                                    <option value = "{{ $manu->legal_entity_id}}"> {{ $manu->business_legal_name }} </option>
                                @endforeach
                            </select>
                            <div class="cust-error-cash-manuf"></div>
                        </div>
                    </div>
                    <div class="col-md-2 cashback_cls">
                        <div class="form-group">
                            <label for="Select offer tmpl">Brand</label>
                            <select id = "offertypbrand_cashback"  name =  "offertypbrand_cashback[]" class="form-control multi-select-search-box" multiple="multiple"  onChange="changeOffer(this);" style="height: 30px">
                                <option value = "">--Please Select--</option>
                                <option value="0">All</option>
                                    @foreach($branddata as $data)
                                        <option value = "{{ $data->brand_id }}">{{ $data->brand_name }} </option>
                                    @endforeach
                            </select>
                            <div class="cust-error-cash-brand"></div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="Select offer tmpl">Product Star</label>
                            <select id = "Product_star"  name =  "Product_star[]" class="form-control multi-select-search-box" >
                            <option value = "">--Please Select--</option>
                            <option value="0">All</option>
                        
                                @foreach($product as $data)
                                   <option value = "{{ $data['value'] }}"> {{ $data['master_lookup_name'] }} </option>
                                @endforeach
                            </select>
                            <div class="cust-error-ProductStar_on_bill_update"></div>
                        </div>
                    </div>
                    <div class="col-md-2 incentive_cls">
                        <div class="form-group">
                            <label for="Select offer tmpl">Product group</label>
                            <select id = "offertyp_prdgrp"  name =  "offertyp_prdgrp[]" class="form-control multi-select-search-box" multiple="multiple" style="height: 30px">
                                <option value="0">All</option>
                                @foreach($product_groups as $product)
                                    <option value = "{{ $product->product_grp_ref_id }}">{{ $product->product_grp_name }}</option>
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
                                <input type="text" class="form-control" name="update_from_cashback" id="update_from_cashback"  maxlength="100"/>
                                 <div class="cust-error-cash_back_from_update"></div>
                            </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="promotion_name">To</label>
                            <input type="text" class="form-control" name="update_to_cashback" id="update_to_cashback"  maxlength="100"/>
                             <div class="cust-error-cash_back_to_update"></div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="promotion_name">Discount</label>
                            <input type="text" class="form-control" name="discount_offer_cashback" id="discount_offer_cashback" maxlength="100"/>
                             <div class="cust-error-discount_update"></div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        <label>&nbsp;</label>
                            <div class="mt-checkbox-list margt">
                            <label class="mt-checkbox">
                            <input type="checkbox" id="offon_percent_cashback" value="1" name = "offon_percent_cashback" @if($getpromotionData->is_percent_on_free == '1') {{'checked'}}@endif> Percentage
                            <span></span>
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
                    <div class="col-md-2 cashback_cls">
                            <div class="form-group">
                                <label>Product Value</label>
                                <input type="text" id="product_value" name = "product_value" class="form-control" autocomplete="off">
                                <div class="cust-error-cash_product_value"></div>
                            </div>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-md-2 cashback_cls">
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
                        <label for="multiple" class="control-label">Exclude Product group's</label>
                        <select id="excl_prod_group_id" name = "excl_prod_group_id[]" class="form-control multi-select-search-box" multiple style="height:30px">
                            @foreach($product_groups as $product)
                                <option value = "{{ $product->product_grp_ref_id }}">{{ $product->product_grp_name }}</option>
                            @endforeach
                        </select>                    
                        </div>                    
                    </div>
                    <div class="col-md-2 incentive_cls">
                        <div class="form-group">
                            <label for="multiple" class="control-label"> Exclude Categories</label>
                            <select id="excl_Category_id" name = "excl_Category_id[]" class="form-control multi-select-search-box" multiple style="height:30px">
                                @foreach($categorie_groups as $categories)
                                    <option value = "{{ $categories->category_id }}">{{ $categories->cat_name }}</option>
                                @endforeach
                            </select>                    
                        </div>
                    </div>
                    <div class="col-md-2 incentive_cls">
                        <div class="form-group">
                            <label for="multiple" class="control-label"> Exclude Manufacturers</label>
                            <select id="excl_manf_id" name = "excl_manf_id[]" class="form-control multi-select-search-box" multiple style="height:30px">
                                @foreach($manufacture_groups as $manufactures)
                                    <option value = "{{ $manufactures->legal_entity_id }}" >{{ $manufactures->business_legal_name }}</option>
                                @endforeach
                            </select>                    
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="Select offer tmpl">Exclude Brands</label>
                            <select id = "update_excludebrand"  name =  "update_excludebrand[]" class="form-control  multi-select-search-box" multiple="multiple" style="height: 30px">
                                @foreach($branddata as $data)
                                        <option value = "{{ $data->brand_id }}">{{ $data->brand_name }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div  class="col-md-12 text-center">
                        <span id="adding_cashback_update" name="adding_cashback_update" class="btn green-meadow">Add</span>
                    </div>
            </div>
        


            </div>
            <div class="row">
                
            </div>
            <div class="cust-error-update_lines"></div>
            <table class="table table-striped table-bordered table-hover table-advance" id="cashback_table_update" name = "cashback_table_update[]" style="font-size:12px">
                <thead>
                    <tr>
                        <th>State</th>
                        <th>CustGrp</th>
                        <th>Desc</th>                        
                        <th>Mnf</th>
                        <th>Brand</th>
                        <th>excl Brand</th>
                        <th>excl Prd Grps</th>
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

                    @if(isset($cashbackdata))
                    @foreach( $cashbackdata as $cashbackdatadetails )                   
                   
                    <tr class="gradeXSlab odd list-head">
                    <td><input type="hidden" value="{{$cashbackdatadetails->state_id}}" id="state_table_update" name="state_table_update[]" class="form-control" >{{$cashbackdatadetails->StateName}}</td>
                    <td><input type="hidden" value="{{$cashbackdatadetails->customer_type}}" id="customer_group_table_update" name="customer_group_table_update[]" class="form-control" >{{$cashbackdatadetails->CustomerType}}</td>
                    <td><input type="hidden" value="{{$cashbackdatadetails->cbk_label}}" id="description_table_update" name="description_table_update[]" class="form-control" >{{$cashbackdatadetails->Description}}</td>
                     <td><input type="hidden" value="{{$cashbackdatadetails->manufacturer_id}}" id="offertypemanf_table_update" name="offertypemanf_table_update[]" class="form-control" >{{$cashbackdatadetails->manfName}}</td>
                    <td><input type="hidden" value="{{$cashbackdatadetails->brand_id}}" id="offertypbrand_table_update" name="offertypbrand_table_update[]" class="form-control" >{{$cashbackdatadetails->brandName}}</td>
                    <td><input type="hidden" value="{{$cashbackdatadetails->excl_brand_id}}" id="offertypexclbrand_table_update" name="offertypexclbrand_table_update[]" class="form-control" >{{$cashbackdatadetails->excl_brandName}}
                     <td>{{$cashbackdatadetails->excl_prod_group_name}}   
                    <input type="hidden" value="{{$cashbackdatadetails->product_group_id}}" id="prdgrp_tbl" name="prdgrp_tbl[]" class="form-control" >{{$cashbackdatadetails->prod_group_name}}</td>
                    <td><input type="hidden" value="{{$cashbackdatadetails->benificiary_type}}" id="Benificiary_table_update" name="Benificiary_table_update[]" class="form-control" >{{$cashbackdatadetails->Benificiary}}</td>
                    <td><input type="hidden" value="{{$cashbackdatadetails->product_star}}" id="ProductStar_table_update" name="ProductStar_table_update[]" class="form-control" >{{$cashbackdatadetails->ProductStar}}</td>
                    <td><input type="hidden" value="{{$cashbackdatadetails->wh_id}}" id="wareHouseId_table_update" name="wareHouseId_table_update[]" class="form-control" >{{$cashbackdatadetails->WareHouse}}</td>
                    <td><input type="text" value="{{$cashbackdatadetails->range_from}}" id="cash_back_from_table_update" name="cash_back_from_table_update[]" class="form-control" readonly></td>
                    <td><input type="text" value="{{$cashbackdatadetails->range_to}}" id="cash_back_to_table_update" name="cash_back_to_table_update[]" class="form-control" readonly></td>
                    <td><input type="text" value="{{$cashbackdatadetails->cbk_value}}" id="discount_offer_on_bill_table_update" name="discount_offer_on_bill_table_update[]" class="form-control" readonly ></td>
                    <td><input type="hidden" value="{{$cashbackdatadetails->cbk_type}}" id="offon_percent_table_update" name="offon_percent_table_update[]" class="form-control">{{$cashbackdatadetails->cbk_type_txt}}</td>
                    <td><input type="text" value="{{$cashbackdatadetails->cap_limit}}" id="cap_limit_to_update_table" name="cap_limit_to_update_table[]" class="form-control" readonly></td>
                    <td><input type="text" value="{{$cashbackdatadetails->product_value}}" id="product_value_to_update_table" name="product_value_to_update_table[]" class="form-control" readonly></td>
                    <input type="hidden" id="order_type_to_update_table" name="order_type_to_update_table[]" class="form-control" readonly value="{{$cashbackdatadetails->is_self}}">
                    <input type="hidden" id="update_excl_prdgrp" name ="update_excl_prdgrp[]" value="{{$cashbackdatadetails->excl_prod_group_id}}">
                    <input type="hidden" id="update_excl_manf" name ="update_excl_manf[]" value="{{$cashbackdatadetails->excl_manf_id}}">
                    <input type="hidden" id="update_excl_category" name ="update_excl_category[]" value="{{$cashbackdatadetails->excl_category_id}}">
                    <td><a href="" class="btn btn-icon-only default delconditionforcashback"><i class="fa fa-trash-o"></i></a></td>
                    </tr>
                    @endforeach
                    @endif   
                </tbody>
            </table>
    </div> 

<style type="text/css">
    div[class*='cust-error']{
        color: #a94442;
    }
</style>