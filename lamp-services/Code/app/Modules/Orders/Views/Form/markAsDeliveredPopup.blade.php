<div class="modal modal-scroll fade in" id="markAsDelivered" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Mark As Delivered</h4>
            </div>
            <div class="modal-body">
                <form id="markAsDeliveredForm" class="" method="get">
                    <div class="row">

                            <div class="col-md-12">

                                <div class="form-group">
                                <div class="col-md-4 pad1">Delivery Executive Name</div>
                                <div class="col-md-1 pad1">:</div>
                                <div class="col-md-7 pad2">

                                    <div class="input-icon left">
                                        <select class="form-control select2me" name="delivered_by" id="delivered_by">

                                        <option value="">Please select</option>    
                                        @foreach($deliveryUsers as $User)
                                        <option value="{{ $User->user_id }}">{{ $User->firstname.' '.$User->lastname }}</option>
                                        @endforeach


                                        </select>
                                    </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                <div class="col-md-4 pad1">Delivery Date</div>
                                <div class="col-md-1 pad1">:</div>
                                <div class="col-md-7 pad2">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="delivereddate" name="delivereddate" class="form-control" value="{{date('Y-m-d')}}">
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
