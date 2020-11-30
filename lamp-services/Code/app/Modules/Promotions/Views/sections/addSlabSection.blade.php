<div class="condition_container" style="display:none;">
    <div class="row">
        <div class="col-md-12">
            <h5><strong>Offer Condition</strong></h5>
            <span id="no_slab_span" style ="color:red"></span>
            <hr />
        </div>
    </div> 

    <!-- Offer Condition Part Goes here -->
    <div class="row">


        <div class="col-md-6">
            <div class="form-group">
            <label for="Select offer tmpl">Condition</label>
                <select id = "condition"  name =  "condition" class="form-control" onChange="changetextbox(this);">
                    <option value = "">--Please Select--</option>
                    <option value = "="> = </option>
                    <option value = ">"> > </option>
                    <option value = "Range"> Range </option> 
                </select>
            </div>
        </div>

      <div class="col-md-6">
        <div class="row">
            <input type="hidden" id="product_id" name="product_id"/>
            <div class="col-md-4">
                <div class="form-group">
                <label for="promotion_name">Pack</label>
                    <select id = "pack_number"  name =  "pack_number" class="form-control" onChange="stepValue();" >
                        <option value = "">--Please Select--</option>
                    </select>
                </div> 
                 <span class="fa fa-star" id="product_star_color" aria-hidden="true" style="position: absolute;top: 31px;font-size: 25px;right: -11px;"></span>                
            </div>
       
               

           <div class="Pack">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="promotion_name">ESU</label>
                        <input type="hidden" name="step_count" id="step_count"/>
                        <input type="Number" step="" min="0" class="form-control" name="pack_value" id="pack_value" onChange="maxQty();"placeholder="Only Number Allowed" />
                    </div>
                </div>
            </div>

            <div class="second_condition">
                <div class="col-md-2">
                <div class="error_messege" style="color:red"></div>
                    
                    <div class="form-group">
                        <label for="promotion_name">Max Qty</label>
                        <input type="text" class="form-control" name="value_two" id="value_two"  value="" placeholder="Only Number Allowed" readonly/>
                    </div>
                </div>
            </div>
            <div class="offer_value">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="promotion_name">Off Val / Unit price</label>
                        <input type="text" class="form-control" name="offer_value" id="offer_value" placeholder="Only Number Allowed"/>
                    </div>
                </div>
            </div>
            <div class="col-md-1 text-left plus-icon" style="margin-left:-13px; display:none;">
                <div class="form-group addbut">
                    <span class="btn btn-icon-only green addslab" id="slabrate_validate" name ="slabrate_validate"><i class="fa fa-plus"></i></span>
                 </div>
            </div>
        </div>
    </div>
    </div>

    <div class="row slab_rates">
        <div class="col-md-6">&nbsp;
        </div>
        <div class="col-md-6">
        <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">

             <div class="cust-error-no-tr-lines"></div>
                <table class="table table-striped table-bordered table-hover table-advance" id="slab_table" name = "slab_table[]">
                <thead>
                    <tr>
                        <th>Pack</th>
                        <th>Star</th>
                        <th>ESU</th>
                        <th>Maximum Quantity</th>
                        <th>Offer Value</th>
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