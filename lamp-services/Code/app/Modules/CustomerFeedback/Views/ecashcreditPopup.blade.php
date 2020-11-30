
<div class="modal modal-scroll fade in" id="addEcashCreditlimit" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">DC Retailer Config</h4>
            </div>
            <div class="modal-body">
                <form id="addingEcashCreditLimitForm" method="POST">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Type</label>
                                     <input type="hidden" name="ecash_id" id="ecash_id">
                              <select name="cust_mas_id" id="cust_mas_id"class="form-control select2me" placeholder="Select Customer">
                                  <option value=""></option>  
                                    @foreach($customerDetails as $value)
                                    <option value="{{$value->value }}">{{ $value->master_lookup_name}}</option>
                                    @endforeach
                             </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Please Select DC</label>
                                <div class="form-group">
                                  <select  name="dcDetail_id" id="dcDetail_id" class="form-control select2me" placeholder="Please Select DC">
                                    <!-- <option value="0" >All DC'S</option> -->
                                        @foreach ($filter_options['dc_data'] as $dc_data)
                                        <option value="{{ $dc_data->le_wh_id }}" >{{ $dc_data->name }}</option>
                                        @endforeach
                                 </select>
                                </div>
                            </div>
                        </div>
                            <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group"><label>Self Order MOV</label>
                                    <input type="text" class="form-control" id="self_order_mov" name="self_order_mov" placeholder=""  autocomplete="nope">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label>FF Order MOV</label>
                                    <input type="text" class="form-control" id="minimum_order_value" name="minimum_order_value" placeholder="" autocomplete="nope">
                                </div>
                            </div>
                        </div>
                            <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group"><label>Mov Order Count</label>
                                    <input type="text" class="form-control" id="mov_ordercount" name="mov_ordercount" placeholder="" autocomplete="nope">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label>Select State</label>
                             <select name="add_state_id" id="add_state_id" class="form-control select2me " placeholder="Select State">
                                  <option value=""></option>  
                                    @foreach($stateCode as $value)
                                    <option value="{{$value->zone_id }}">{{ $value->name}}</option>
                                    @endforeach
                             </select>
                            </div> 
                        </div>
<!--                             <div class="col-md-6">
                                <div class="form-group"><label>Credit Limit</label>
                                    <input type="text" class="form-control" id="Credit_Limit" name="Credit_Limit" placeholder="">
                                </div>
                            </div> -->
                        </div>
<!--                         <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group"><label>Order Value</label>
                                    <input type="text" class="form-control" id="Order_Value_id" name="Order_Value_id" placeholder="">
                                </div>
                            </div>
                        </div> -->
                        <hr/>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" id="Edit_Each_limit" class="btn green-meadow">Submit</button>
                            </div>
                        </div>
                     </div>
                  </div>
                </div>
             </form>
        </div>
    </div>
<!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>



