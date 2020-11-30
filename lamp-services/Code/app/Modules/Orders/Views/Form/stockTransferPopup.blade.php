<div class="modal modal-scroll fade in" id="stockTransfer" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Stock Transfer</h4>
            </div>
            <div class="modal-body">
                <form id="stockTransferForm" class="" method="get">
                    <div class="row">

                            <div class="col-md-12">

                                <div class="form-group">
                                <div class="col-md-4 pad1">DE Name</div>
                                <div class="col-md-1 pad1">:</div>
                                <div class="col-md-7 pad2">

                                    <div class="input-icon left">
                                        <input type="hidden" value="" class="transfer_type" />
                                        <select class="form-control select2me" name="stock_delivered_by" id="stock_delivered_by">

                                        <option value="">Please select</option>    
                                        @foreach($deliveryUsers as $User)
                                        <option value="{{ $User->user_id }}" mobile="{{ $User->mobile_no}}">{{ $User->firstname.' '.$User->lastname }}</option>
                                        @endforeach


                                        </select>
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">

                                <div class="form-group">
                                <div class="col-md-4 pad1">DE Mobile</div>
                                <div class="col-md-1 pad1">:</div>
                                <div class="col-md-7 pad2">

                                    <div class="input-icon left">
                                        
                                        <input type="text" id="stock_delivered_mobile" maxlength="11" name="stock_delivered_mobile" class="form-control"/>    
                                    </div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-12">

                                <div class="form-group">
                                <div class="col-md-4 pad1">Vehicle Number</div>
                                <div class="col-md-1 pad1">:</div>
                                <div class="col-md-7 pad2">

                                    <div class="input-icon left">
                                        <select class="form-control select2me" name="stock_vehicle_number" id="stock_vehicle_number">
                                        <option selected value="">Select Vehicle</option>
                                        <?php 
                                        if($status == 'rah' || $status == 'stocktransitdc') {
                                        ?>
                                            <optgroup label="Hub Vehicles">
                                            <?php echo $hub_vehicles;?>
                                            </optgroup>
                                            <optgroup label="DC Vehicles">
                                            <?php echo $dc_vehicles; ?>
                                            </optgroup>    
                                        <?php
                                        } else {
                                            echo $dc_vehicles;
                                        }
                                        ?>

                                        </select>
                                        <?php /*<input type="text" id="stock_vehicle_number" name="stock_vehicle_number" class="form-control"/>*/?>    
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">

                                <div class="form-group">
                                <div class="col-md-4 pad1">Driver Name</div>
                                <div class="col-md-1 pad1">:</div>
                                <div class="col-md-7 pad2">

                                    <div class="input-icon left">
                                        
                                        <input type="text" id="stock_driver_name" name="stock_driver_name" class="form-control"/>    
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">

                                <div class="form-group">
                                <div class="col-md-4 pad1">Driver Mobile</div>
                                <div class="col-md-1 pad1">:</div>
                                <div class="col-md-7 pad2">

                                    <div class="input-icon left">
                                        
                                        <input type="text" id="stock_driver_mobile" name="stock_driver_mobile" class="form-control" maxlength="11" />    
                                    </div>
                                    </div>
                                </div>
                            </div>


                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn green-meadow">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
