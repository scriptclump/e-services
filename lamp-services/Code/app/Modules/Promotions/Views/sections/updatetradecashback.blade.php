<div class="">
	<div class="row">
		<h5><strong>Offercondition</strong></h5>
	</div>
	<div class="row">
		<div class="col-md-3 form-group">
			<label>Discount type</label>
			<select id = "update_trade_type"  name ="update_trade_type" class="form-control multi-select-search-box" >
			<option value="">Please select</option>

			@foreach($trademasterlookup as $trade)
				<option value="{{$trade->value}}" @if($tradeData->object_type == $trade->value) {{"selected"}} @endif>{{$trade->name}}</option>
			@endforeach
			</select>
			<div class="cust-error-update_trade_type"></div>

			<input type="hidden" id="trade_hidden" name="trade_hidden" value="{{$tradeData->object_type}}">
		</div>
		<!-- <div class="col-md-3 form-group">
			<label>Warehouse</label>
			<select id="update_trade_warehouse" name="update_trade_warehouse[]" multiple="multiple" class="form-control multi-select-search-box">
				<option value='0'>All</option>
				@foreach($warehouse as $wh)
					<option value="{{$wh['le_wh_id']}}" @if (in_array($wh['le_wh_id'], explode(',', $tradeData->warehouse_ids))) {{ "selected" }} @endif >{{$wh['lp_wh_name']}}</option>
				@endforeach
			</select>
			<div class="cust-error-update_trade_warehouse"></div>
		</div> -->
		<div class="col-md-3 form-group">
			<label>Promotion on</label>
			<select id="update_promotion_on" name="update_promotion_on[]" multiple="multiple" class="form-control multi-select-search-box">
				<option value='0' @if (in_array(0, explode(',',$tradeData->object_ids))) {{ "selected" }} @endif >All</option> 
				@foreach($equivalentData as $data)
					<option value="{{$data->id}}" @if (in_array($data->id, explode(',', $tradeData->object_ids ))) {{ "selected" }} @endif>{{$data->i_name}}</option>
				@endforeach				
			</select>
			<div class="cust-error-update_trade_promotion_on"></div>

		</div>
		<div class="col-md-3 form-group">
			<label>Pack type</label>
			<select id="update_pack_type" name="update_pack_type[]" multiple="multiple" class="form-control multi-select-search-box">
				<option value="0" @if (in_array(0, explode(',', $tradeData->pack_type))) {{ "selected" }} @endif>All</option>
				@foreach($packs as $pack)
					<option value="{{$pack->value}}" @if(in_array($pack->value, explode(',',$tradeData->pack_type))) {{"selected"}} @endif >{{$pack->name}}</option>
				@endforeach
			</select>
			<div class="cust-error-update_pack_type"></div>

		</div>
	</div>
	<div class="row">
		<div class="col-md-2 form-group">
			<label>From Range</label>
			<input type="number" name="update_trade_from_range" id="update_trade_from_range" class="form-control" value="{{$tradeData->from_range}}"/>
			<div class="cust-error-update_trade_from_range"></div>

		</div>
		<div class="col-md-2 form-group">
			<label>To Range</label>
			<input type="number" name="update_trade_to_range" id="update_trade_to_range" class="form-control" value="{{$tradeData->to_range}}"/>
			<div class="cust-error-update_trade_to_range"></div>

		</div>
		<div class="col-md-2">
            <div class="form-group">
                <label >Discount</label>
                    <input  class="form-control" name="update_tradeoffer_on_bill" id="update_tradeoffer_on_bill" value="{{$tradeData->disc_value}}"/>
                    <div class="cust-error-update_tradeoffer_on_bill"></div>
            </div>
        </div>
		<div class="col-md-1">
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="mt-checkbox-list">
                    <label class="mt-checkbox">
                        <input type="checkbox" id="update_trade_percent_cashback" name="update_trade_percent_cashback" @if($tradeData->is_percent ==1) {{'checked'}} @endif> %
                    </label>
                 </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Type</label>
                <select id="update_tradeoffer_type" name="update_tradeoffer_type" class="form-control">
					<option value="">Please select</option>
					<option value="0" @if($tradeData->is_self == 0) {{"selected"}} @endif>Manual</option>
					<option value="1" @if($tradeData->is_self == 1) {{"selected"}} @endif>Self</option>
					<option value="2" @if($tradeData->is_self == 2) {{"selected"}} @endif>Manual & self</option>
				</select>
                <div class="cust-error-update_tradeoffer_type"></div>
            </div>
        </div>

	</div>
</div>