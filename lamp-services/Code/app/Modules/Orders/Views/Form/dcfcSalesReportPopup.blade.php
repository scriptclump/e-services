<div class="modal modal-scroll fade in" id="dcfcSalesReport" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">DC/FC Sales Report</h4>
            </div>
            <div class="modal-body">
                <form id="dcfcSalesReport" action="/salesorders/dcfcSalesReport" class="text-center" method="post">
                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <?php echo csrf_field(); ?>
                                       <input type="text" id="dcfc_fdate" name="dcfc_fdate" class="form-control" placeholder="From Date" autocomplete="off">
                                    </div>
                                </div>
                                <span id="span_id_dcfc_fdata" style="font-size: 13px; color: #bb1010; display: none">Please Select From Date</span>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="dcfc_tdate" name="dcfc_tdate" class="form-control" placeholder="To Date" autocomplete="off">
                                    </div>
                                </div>
                                 <span id="span_id_dcfc_tdata" style="font-size: 13px; color: #bb1010; display: none">Please Select To Date</span>
                            </div> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="left">
                            <span style="color:red">*</span> Note: Please select the dates
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="dcfc_dwn_file" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>