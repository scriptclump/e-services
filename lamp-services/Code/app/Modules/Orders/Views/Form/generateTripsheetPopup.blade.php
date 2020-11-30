<div class="modal modal-scroll fade in" id="printTripsheet" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Picking</h4>
            </div>
            <div class="modal-body">
                <form id="printTripsheetForm" class="text-center" method="get" style="text-align: left;">
                    <div class="row">
                        <div class="">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-3 pad1">Picker Name</div>
                                    <div class="col-md-1 pad1">:</div>
                                    <div class="col-md-8 pad2">
                                        <div class="input-icon left">
                                            <select class="form-control select2me" name="picked_by" id="tr_picked_by">
                                                <option value="">Select Picker Name</option>
                                                @foreach($pickerUsers as $User)
                                                <option value="{{ $User->user_id }}">{{ $User->firstname.' '.$User->lastname }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-3 pad1">Picking Date</div>
                                    <div class="col-md-1 pad1">:</div>
                                    <div class="col-md-8 pad2">
                                        <div class="input-icon right">
                                            <?php /* <i class="fa fa-calendar"></i> */ ?>
                                            <input type="text" id="tr_pickdate" name="pickdate" readonly="" class="form-control" value="<?php echo date('d/m/Y'); ?>" />
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
