<div class="condition_container">
	<div class="row">
		<h5><strong>Offercondition</strong></h5>
	</div>
	<div class="row">
		<div class="col-md-3 form-group">
			<label>Discount type</label>
			<select id = "trade_type"  name ="trade_type" class="form-control multi-select-search-box" >
			<option value="">Please select</option>
			@foreach($trademasterlookup as $trade)
				<option value="{{$trade->value}}">{{$trade->name}}</option>
			@endforeach
			</select>
			<div class="cust-error-trade_type"></div>
		</div>
		<!-- <div class="col-md-3 form-group">
			<label>Warehouse</label>
			<select id="trade_warehouse" name="trade_warehouse[]" multiple="multiple" class="form-control multi-select-search-box">
				<option value="">Please select</option>
				<option value='0'>All</option>
				@foreach($warehouse as $wh)
					<option value="{{$wh['le_wh_id']}}">{{$wh['lp_wh_name']}}</option>
				@endforeach
			</select>
			<div class="cust-error-trade_warehouse"></div>
		</div> -->
		<div class="col-md-3 form-group">
			<label>Promotion on</label>
			<select id="promotion_on" name="promotion_on[]" multiple="multiple" class="form-control multi-select-search-box" style="height: 30px">
			<option value="">Please select</option>

			</select>
			<div class="cust-error-promotion_on"></div>
		</div>
		<div class="col-md-3 form-group">
			<label>Pack type</label>
			<select id="pack_type" name="pack_type[]" multiple="multiple" class="form-control multi-select-search-box" style="height: 30px">
                <option value = "">--Please Select--</option>
				<option value="0">All</option>
				@foreach($packs as $pack)
					<option value="{{$pack->value}}">{{$pack->name}}</option>
				@endforeach
			</select>
			<div class="cust-error-pack_type"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-2 form-group">
			<label>From Range</label>
			<input type="number" name="trade_from_range" id="trade_from_range" class="form-control" />
			<div class="cust-error-trade_from_range"></div>
		</div>
		<div class="col-md-2 form-group">
			<label>To Range</label>
			<input type="number" name="trade_to_range" id="trade_to_range" class="form-control" />
			<div class="cust-error-trade_to_range"></div>
		</div>
		<div class="col-md-2">
            <div class="form-group">
                <label >Discount</label>
                    <input  class="form-control" name="tradeoffer_on_bill" id="tradeoffer_on_bill"/>
                    <div class="cust-error-tradeoffer_on_bill"></div>
            </div>
        </div>
		<div class="col-md-1">
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="mt-checkbox-list">
                    <label class="mt-checkbox">
                        <input type="checkbox" id="trade_percent_cashback" name="trade_percent_cashback" > %
                    </label>
                 </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>Type</label>
                <select id="tradeoffer_type" name="tradeoffer_type" class="form-control">
					<option value="">Please select</option>
					<option value="0">Manual</option>
					<option value="1">Self</option>
					<option value="2">Manual & self</option>
				</select>
                <div class="cust-error-tradeoffer_type"></div>
            </div>
        </div>
	</div>
</div>