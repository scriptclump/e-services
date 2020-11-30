<!-- Add / Edit Model -->
        <div class="modal modal-scroll fade" id="save_price" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">


                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">ADD / EDIT PRICE <span style="color: blue;
    font-weight: bold;" id="product_name_heading"></span></h4>
                </div>

            <div class="modal-body">

<div class="tabbable"> <!-- Only required for left/right tabs -->
<ul class="nav nav-tabs nav-justified">
<li class="active" id="pricing_tab"><a href="#tab1" data-toggle="tab" >Pricing</a></li>
<li class="disabled" id="cashback_tab"><a id="cashback_tab_" href="#tab2" >Cashback</a></li>
</ul>
<div class="tab-content">
<div class="tab-pane fade in active" id="tab1">




            <div class="row">
            <div class="col-md-12">
            <div class="portlet box">
            <div class="portlet-body">
            {{ Form::open(array('url' => '/pricing/addeditslabdata', 'method' => 'POST', 'id' => 'frm_price_tmpl'))}}

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Product Name:</label>
                            <span id="prd_name"></span>
                            <input type="hidden" name="add_prd_id" id="add_prd_id" value="">
                            <input type="hidden" name="add_edit_flag" id="add_edit_flag" value="0">
                            <input type="hidden" name="product_tax_flag" id="product_tax_flag" value="1" >
                            <input type="hidden" name="product_price_id" id="product_price_id" value="0">
                            <input type="hidden" name="product_name" id="product_name" value="">
                            <input type="hidden" name="hidden_add_dc" id="hidden_add_dc" value="">

                            
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="control-label">MRP</label>
                    <div class="form-group">
                        <span id="mrp"></span>
                    </div>
                </div>

                <!-- <div class="col-md-1">
                    <label class="control-label">PTR</label>
                    <div class="form-group">
                        <span id="ptr"></span>
                    </div>
                </div> -->

                <div class="col-md-3">
                    <label class="control-label">Margin Type</label>
                    <div class="form-group">
                        <span id="margin_type"></span>
                    </div>
                </div>
                </div>

                <div class="row">
                   <!--  <div class="col-md-6">
                        <div class="form-group" id="add_state_revalid">
                            <label class="control-label">State</label>
                                <select id = "add_state"  name = "add_state" class="form-control" onchange="loadRightSideData();" disabled="disabled">
                                    @foreach($getStateDetails as $stateData)
                                    <option value = "{{$stateData->zone_id}}">{{$stateData->name}}</option>
                                    @endforeach
                                </select>
                        </div>
                    </div> -->
                     <div class="col-md-6">
                        <div class="form-group" id="add_state_revalid">
                            <label class="control-label">DC</label>
                                <select id = "add_dc"  name = "add_dc" class="form-control" onchange="loadRightSideData();" disabled="disabled">
                                    @foreach($dcs as $alldcs)
                                    <option value = "{{$alldcs->le_wh_id}}">{{$alldcs->lp_wh_name}} - ({{$alldcs->name}})</option>
                                    @endforeach
                                </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group" id="add_custgroup_revalid">
                            <label for="multiple" class="control-label">Customer Group</label>
                                <select id = "add_custgroup"  name = "add_custgroup" class="form-control" disabled="disabled">
                                    @foreach($getCustomerGroup as $customerData)
                                    <option value = "{{$customerData->value}}">{{$customerData->master_lookup_name}}</option>
                                    @endforeach
                                </select>
                        </div>
                    </div>
                </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" id="date_revalid">
                        <label class="control-label">Effective Date</label>
                        <div class="input-icon input-icon-sm">
                            <i class="fa fa-calendar"></i>
                            <input type="text" class="form-control" name="date" id="date" />
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group" id="selling_price_revalid">
                            <label class="control-label">{{Lang::get('headings.SP')}}</label>
                                <input type="text" id="selling_price" name="selling_price" class="form-control" onblur="loadRightSideData();">
                            </div>
                        </div>

                         <div class="col-md-4">
                            <div class="form-group" id="price_ptr_revalid">
                                <label class="control-label">PTR</label>
                                <input type="text" id="price_ptr" name="price_ptr" class="form-control">
                                    
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Margin</label>
                                    <span id="margin_val"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                <label class="control-label" style="display:block;">Billing Illustration</label>
                    <table class="table table-striped table-bordered table-hover table-advance" id="price_details" name = "price_details" style="font-size:12px;">
                        <thead>
                            <tr>
                                <th>Price Type</th>
                                <th>State Billing</th>
                                <th>Inter State Billing</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>                   
                </div>
                
            </div>

            <div class="row">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn green-meadow" id="price-save-button">Save</button>
            </div>
            </div>
            {{ Form::close() }}

            </div>
            </div>
        </div>
            </div>

</div>
<div class="tab-pane fade" id="tab2">

            <div class="col-md-12">
            {{ Form::open(array('url' => '', 'method' => 'POST', 'id' => 'cashback_form'))}}

                <div class="row">

                    <div class="col-md-12">

                        <div class="row">
                            
                            <input type="hidden"  name="cashback_product_id" id="cashback_product_id" value="">
                            <input type="hidden"  name="cashback_ref_id" id="cashback_ref_id" value="">

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="cashback_for">Warehouses</label>
                                    <select id = "cashback_warehouse"  name =  "cashback_warehouse" class="form-control" >
                                    <option value="">Please select</option>
                                    @foreach($wareHouses as $wareHouse)
                                        <option value = "{{$wareHouse->le_wh_id}}">{{$wareHouse->lp_wh_name}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">State</label>
                                        <select id = "cashback_state"  name = "cashback_state" class="form-control">
                                            <option value="">Please select</option>
                                            @foreach($getStateDetails as $stateData)
                                            <option value = "{{$stateData->zone_id}}">{{$stateData->name}}</option>
                                            @endforeach
                                        </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="multiple" class="control-label">Customer Group</label>
                                        <select id = "cashback_custgroup"  name = "cashback_custgroup" class="form-control">
                                            <option value="">Please select</option>
                                            @foreach($getCustomerGroup as $customerData)
                                            <option value = "{{$customerData->value}}">{{$customerData->master_lookup_name}}</option>
                                            @endforeach
                                        </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group" id="cashback_start_date_">
                                    <label class="control-label">Start Date</label>
                                    <div class="input-icon input-icon-sm">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" class="form-control" name="cashback_start_date" id="cashback_start_date" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group" id="cashback_end_date_">
                                    <label class="control-label">End Date</label>
                                    <div class="input-icon input-icon-sm">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" class="form-control" name="cashback_end_date" id="cashback_end_date" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label >Description</label>
                                    <input type="text" class="form-control" name="cashback_text" id="cashback_text" placeholder="Enter Description"/>
                                </div>
                            </div>


                            <div class="col-md-2">
                                    <div class="form-group">
                                        <label >Benificiary</label>
                                        <select id = "cashback_for"  name =  "cashback_for" class="form-control" >
                                        <option value="">Please select</option>
                                        @foreach($beneficiaryName as $benificiaryData)
                                            <option value = "{{$benificiaryData->role_id}}">{{$benificiaryData->name}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label >Product Star</label>
                                    <select id ="cashback_product_star"  name = "cashback_product_star" class="form-control" >
                                    <option value="">Please select</option>
                                    <option value="0">All</option>
                                    @foreach($product_stars as $product_star)
                                        <option value = "{{$product_star->value}}">{{$product_star->master_lookup_name}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>

                            

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Quantity </label>
                                    <input type="text" class="form-control" name="cashback_quantity" id="cashback_quantity" placeholder="Only Number Allowed"/>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label >Offer Value </label>
                                    <input type="text" class="form-control" name="offer_value" id="offer_value" placeholder="Only Number Allowed"/>
                                </div>
                            </div>

                            


                            <div class="col-md-1" style="margin-top: 23px;">
                                <div class="form-group">
                                    <input type="checkbox" name="is_percent" id="is_percent" value="1">&nbsp;&nbsp;(%)
                                </div>
                            </div>

                            <div class="col-md-1 text-left plus-icon" style="margin-top: 23px;">
                                <div class="form-group addbut">
                                <input class="btn btn-icon-only green fa fa-plus" type="submit" value="Add"></input>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                    
            </div> 
                {{ Form::close() }}
                    <div class="col-md-12">
                    <span id="cashback_response" style=""></span>
                    <table class="table table-striped table-bordered table-hover table-advance">
                    
                    </table>
                    
                    
                    
                        <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                            <table class="table table-striped table-bordered table-hover table-advance" id="cashback_table" name = "cashback_table[]">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Beneficiary</th>
                                        <th>Product Star</th>
                                        <th>Start date</th>
                                        <th>End date</th>
                                        <th>State</th>
                                        <th>Customer Group</th>
                                        <th>Offer Value</th>
                                        <th>Quantity</th>
                                        <th>Ware house</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>                        
                    </div>
                </div>








</div>
</div>
</div>


            </div>
        </div>
    </div>
</div>