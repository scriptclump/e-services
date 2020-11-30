              <div class="tab-pane" id="tab_33">
                <!--<div class="row">
                  <div class="col-md-12 text-right"> <a class="btn green-meadow" data-toggle="modal" href="#addbrand">Add Brand</a> </div>
                </div>-->
                <div class="row">
                  <div class="col-md-12"> &nbsp;</div>
                </div>
                <div class="row">
                    <div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Warehouse</label>
							<select class="form-control spd_whid" name="spd_whid" id="spd_whid">
                                <option value="">Select Warehouse</option>
                                                                @if(isset($legalentity_warehouses))                    
								@foreach($legalentity_warehouses as $Val )
									<option value="{{$Val['le_wh_id']}}">{{$Val['lp_wh_name']}}</option>
								@endforeach
                                                                @endif
								</select>
						</div>
					</div>
                  <div class="col-md-4">
						<div class="form-group">
							<!--<label class="control-label">Brand</label>
                                                        <select class="form-control select2me brandProductSelect" id="brand_id" name="brand_id">
                                                        </select>
                                                        <span id="showLoader" style="position: relative; left: -5%; top: -27px; display: none;">
                                                        <img src="../img/ajax-loader2.gif">
                                                        </span>-->
                                                        <label class="control-label">Manufacturer</label>
                                                        <select name="manufacturer_name" id="manufacturer_name" class="form-control select2me manuProductSelect">
                                                        </select>
							<!--<select class="form-control manuProductSelect" id="manu_id" name="manu_id">
									<option value="">Select Manufacturer</option>
								@foreach($manufacturerList as $key => $value )
									<option value="{{$key}}" >{{$value}}</option>
								@endforeach
							</select>-->
						</div>
					</div>
				  <div class="col-md-12" style="height: 483px;">
					
					<div id="product_choose_grid"></div>
					<!--<div id="brands_grid"></div>-->
				</div>
                </div>
                <!--<hr>
                <div class="row">
                  <div class="col-md-12 text-center">
                      <button type="submit" id="savebrands" class="btn green-meadow">Save & Continue</button>
                    <button type="submit" id="cancelbrands" class="btn green-meadow">Cancel</button>
                  </div>
                </div>-->
              </div>
