<div class="container">
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
                <input type="text"  class="form-control" id = "update_freeqty_description" name = "update_freeqty_description" value="{{$getfreesample->description}}" />
            </div>
        </div>   
        <div class="col-md-3">
            <div class="form-group">
                <label for="Select offer tmpl">Warehouse</label>
                 <select id = "update_wareHouseId"  name =  "update_wareHouseId[]" class="form-control" >
                    <option value = "">--Please Select--</option>
                    <option value="0" @if ($getfreesample->wh_id == 0) {{ "selected" }} @endif>All</option>
                    @foreach($warehouse as $data)
                        <option value = "{{$data['le_wh_id']}}" @if ($getfreesample->wh_id == $data['le_wh_id']) {{ "selected" }} @endif>{{$data['lp_wh_name']}}</option>
                    @endforeach
                </select>                
            </div>
        </div>


        <div class="col-md-2">
            <div class="form-group">
                <label for="promotion_name">From</label>
                <input type="text" class="form-control" name="update_freeqty_from" id="update_freeqty_from" value="{{$getfreesample->range_from}}"/> 
            </div>    
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="promotion_name">To</label>
                <input type="text" class="form-control" name="update_freeqty_to" id="update_freeqty_to" value="{{$getfreesample->range_to}}"/> 
            </div>    
        </div>
    </div>
    <div class="row">   

        <div class="col-md-3">
            <div class="form-group">
                <label for="Select offer tmpl">Product</label>
                <select id = "update_freeqty_product_id"  name =  "update_freeqty_product_id" class="form-control" >

                    <option value = "">--Please Select--</option>
                    @foreach($products as $data)
                        <option value = "{{$data->product_id}}"  @if ($getfreesample->product_id == $data->product_id) {{ "selected" }} @endif>{{$data->product_title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
         <div class="col-md-2">
            <div class="form-group">
                <label for="Select offer tmpl">Pack</label>
                <select id = "update_freeqty_pack"  name =  "update_freeqty_pack" class="form-control" >
                    <option value = "">--Please Select--</option>
                    @foreach($getPackLevelData as $packlevel)
                        <option value="{{$packlevel->level}}" @if ($getfreesample->pack_level == $packlevel->level) {{ "selected" }} @endif>{{$packlevel->master_lookup_name}}</option>@endforeach

                </select>
            </div>
        </div>

        <div class="col-md-1">
            <div class="form-group">
                <label for="promotion_name">Quantity</label>
                    <input type="text" class="form-control" name="update_product_quantity" id="update_product_quantity"  value="{{$getfreesample->product_qty}}"/>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="mt-checkbox-list">
                    <label class="mt-checkbox">
                        <input type="checkbox"  @if( $getfreesample->is_sample ==1 ) {{'checked'}} @endif id="update_is_sample" name="update_is_sample"> is_sample
                    </label>
                 </div>
            </div>
        </div>

    </div> 