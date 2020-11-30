<div class="modal modal-scroll fade in" id="stockInHub" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Confirm stock</h4>
            </div>
            <div class="modal-body">
                <form id="stockInHubForm" class="" method="get">
                    <div class="row">

                            <div class="col-md-12">

                                <div class="form-group">
                                <div class="col-md-4 pad1">Dock Number</div>
                                <div class="col-md-1 pad1">:</div>
                                <div class="col-md-7 pad2">

                                    <div class="input-icon left">
                                        

                                        <select class="form-control select2me" name="docket_number" id="docket_number">

                                        <option value="">Please select</option>
                                        </select>
                                        <input type="hidden" id="confirm_stock_type" />     
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">

                                <div class="form-group">
                                <div class="col-md-4 pad1">Received By</div>
                                <div class="col-md-1 pad1">:</div>
                                <div class="col-md-7 pad2">

                                    <div class="input-icon left">
                                        <select class="form-control select2me" name="stock_received_by" id="stock_received_by">

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
                                <div class="tabbable-line">
                                    <ul class="nav nav-tabs nav-tabs-lg">
                                        <li class="active"> <a title="All" href="#allDockTab" data-toggle="tab">All</a> </li>
                                        <li class=""> <a title="Pending" href="#pendingDockTab" data-toggle="tab">Pending</a> </li>
                                        <li class=""> <a title="Scanned" href="#scannedDockTab" data-toggle="tab">Scanned</a> </li>
                                        <li class=""> <a title="Partial" href="#partialDockTab" data-toggle="tab">Partial</a> </li>
                                        <li class=""> <a title="Partial" href="#completedDockTab" data-toggle="tab">Completed</a> </li>
                                     </ul>
                                </div>        

                              <div class="tab-content">
                                <div class="tab-pane active " id="allDockTab">
                                    <div class="table-container underline">
                                        <table width="100%" border="0" cellspacing="5" cellpadding="5" class="active1 table table-striped  table-advance table-hover" id="allDocket">
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="pendingDockTab">
                                    <div class="table-container">
                                        <table width="100%" border="0" cellspacing="5" cellpadding="5" class="table table-striped  table-advance table-hover" id="pendingDocket">
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="tab-pane" id="scannedDockTab">
                                    <div class="table-container">
                                        <table width="100%" border="0" cellspacing="5" cellpadding="5" class="table table-striped  table-advance table-hover" id="scannedDocket">
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="partialDockTab">
                                    <div class="table-container">
                                        <table width="100%" border="0" cellspacing="5" cellpadding="5" class="table table-striped  table-advance table-hover" id="partialDocket">
                                        </table>
                                    </div>
                                </div>  
                                <div class="tab-pane" id="completedDockTab">
                                    <div class="table-container">
                                        <table width="100%" border="0" cellspacing="5" cellpadding="5" class="table table-striped  table-advance table-hover" id="completedDocket">
                                        </table>
                                    </div>
                                </div>  
                            </div>
                                  
                                  
                                                          
                            </div>    
                            <span class="error containerError"></span>
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
