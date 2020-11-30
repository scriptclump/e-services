<div class="condition_container">
    <div class="row">
        <div class="col-md-12">
            <h5><strong>Offer Condition</strong></h5>
            <hr/>
        </div>
    </div>
    <div class="row">
    	<div class="col-md-3">
            <div class="form-group">
                <label>Description</label>
                <input type="text"  class="form-control" id = "freeqty_description" name = "freeqty_description"/>
            </div>
        </div>   
        <div class="col-md-3">
            <div class="form-group">
                <label for="Select offer tmpl">Warehouse</label>
                 <select id = "free_wareHouseId"  name =  "free_wareHouseId[]" class="form-control" >
                    <option value = "">--Please Select--</option>
                    <option value="0">All</option>
                    @foreach($warehouse as $data)
                        <option value = "{{$data['le_wh_id']}}">{{$data['lp_wh_name']}}</option>
                    @endforeach
                </select>                
            </div>
        </div>


        <div class="col-md-2">
            <div class="form-group">
                <label for="promotion_name">From</label>
                <input type="text" class="form-control" name="freeqty_from" id="freeqty_from"/> 
            </div>    
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="promotion_name">To</label>
                <input type="text" class="form-control" name="freeqty_to" id="freeqty_to"/> 
            </div>    
        </div>
    </div>
    <div class="row">   

        <div class="col-md-3">
	        <div class="form-group">
	            <label for="Select offer tmpl">Product</label>
	            <select id = "freeqty_product_id"  name =  "freeqty_product_id" class="form-control" >

	                <option value = "">--Please Select--</option>
	                @foreach($products as $data)
	                    <option value = "{{$data->product_id}}">{{$data->product_title}}</option>
	                @endforeach
	            </select>
	        </div>
	    </div>

        <div class="col-md-1">
            <div class="form-group">
                <label for="promotion_name">Quantity</label>
                    <input type="text" class="form-control" name="product_quantity" id="product_quantity"/>
                    <!-- <div class="cust-error-product_quantity"></div> -->
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="Select offer tmpl">Pack</label>
                <select id = "freeqty_pack"  name =  "freeqty_pack" class="form-control" >
                    <option value = "">--Please Select--</option>
                  
                </select>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="mt-checkbox-list">
                    <label class="mt-checkbox">
                        <input type="checkbox" id="is_sample" name="is_sample"> is_sample
                    </label>
                 </div>
            </div>
        </div>


    </div> 
</div>