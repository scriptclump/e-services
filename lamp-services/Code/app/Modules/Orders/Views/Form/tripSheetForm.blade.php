<div class="modal modal-scroll fade in" id="downloadTripSheet" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Download Trip Sheet</h4>
            </div>
            <div class="modal-body">
                <form id="downloadTripSheetForm" class="text-center" method="get" href="" style="text-align: left;">
                    <div class="row">
                        <div class="">

                            <div class="col-md-12">



                                <div class="form-group">
                                <div class="col-md-3 pad1">Vehicle Number</div>
                                <div class="col-md-1 pad1">:</div>
                                <div class="col-md-8 pad2">
                                    <div class="input-icon left">
                                        <select class="form-control select2me" name="trpsh_vehicle" id="trpsh_vehicle">
                                        <option selected value="">Select Vehicle</option>
                                        <?php if($status=='ofd') {
                                                echo $hub_vehicles;
                                                } else if($status=='stocktransit') {
                                                echo $dc_vehicles;
                                                } else if($status=='stocktransitdc') { ?>
<optgroup label="Hub Vehicles">
                                            <?php echo $hub_vehicles;?>
                                            </optgroup>
                                            <optgroup label="DC Vehicles">
                                            <?php echo $dc_vehicles; ?>
                                            </optgroup>                             <?php 
                                                              }
                                              ?>
                                        </select>
                                    </div>
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
