<div class="modal modal-scroll fade in" id="ReassignOrders" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="basicvalCode">Assign To Verification Officer</h4>
                </div>
                <div class="modal-body">
                    <form id="reassigOrdersForm" class="" method="get">
                        <div class="row">

                            <div class="col-md-12">

                                <div class="form-group">
                                <div class="col-md-4 pad1">Verification Officer Names</div>
                                <div class="col-md-1 pad1">:</div>
                                <div class="col-md-7 pad2">

                                    <div class="input-icon left">
                                        <select class="form-control select2me" name="checker_id" id="checker_id">

                                        <option value="">Please select</option>    
                                        @foreach($checkersList as $checker)
                                        <option value="{{ $checker->user_id }}">{{ $checker->username }}</option>
                                        @endforeach


                                        </select>
                                    </div>
                                    </div>
                                </div>
                            </div> 
                        </div> 
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="button" id="assignBtn" class="btn green-meadow">Assign</button>
                            </div>
                        </div>
                    </form>          
                </div>                       
                
        </div>      
    </div>