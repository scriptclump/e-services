<div class="row">
<div class="mflender"> 
    <a class="btn green-meadow" data-toggle="modal" data-target="#mfcdetailsUpdate" href=""></a> <span data-placement="top"></span> 
</div>
<div class="col-md-12">
        <div style="height: 500px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
            <div class="table-responsive">
                <table id="edit_lender_grid"></table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mfcdetailsUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                   </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">
                                   {{ Form::open(array('url' => '/retailers/updateUser', 'id' => 'mfc_mapping_edit_user'))}}
                                        <div class="row">
                                           <div class="form-group row">
									            <div class="col-md-5">
									            <label class="control-label">MFC Mapping</label>
									                <select class="form-control select2me" id="mfc_mapping_dropdown" name="mfc_mapping_dropdown"> 

                                                      
									                </select>
									            </div>
									               <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Credit Limit</label>
                                                    <input type="text" name="edit_c_limit" id="edit_c_limit" class="form-control">
                                                    <input type="hidden" name="edit_mfc_id" id="edit_mfc_id" class="form-control">

                                               </div>
                                            </div>                                                                                                                               
                                        </div>
                                        <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">                                             
                                                <label>
                                                <input type="checkbox" id="mfc_is_active" value="" name = "mfc_is_active">Active
                                                </label>              
                                            </div>
                                       </div>                                              
                                        </div> 
                                         <div class="col-md-11 text-center">
                                                <div class="form-group">
                                                    <button type="submit" class="btn green-meadow">Update</button>
                                                </div>
                                            </div>
                                        {{ Form::close() }}                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>