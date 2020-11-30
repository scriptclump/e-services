<div class="modal modal-scroll fade in" id="orderSummary" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">OrderSummary Report</h4>
            </div>
            <div class="modal-body">
                <form id="orderSummary" action="/salesorders/orderSummary" class="text-center" method="post">
                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <?php echo csrf_field(); ?>
                                       <input type="text" id="os_fdate" name="fdate" class="form-control" placeholder="From Date" autocomplete="off">
                                    </div>
                                </div>
                                <span id="span_id_os_fdata" style="font-size: 13px; color: #bb1010; display: none">Please Select From Date</span>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="os_tdate" name="tdate" class="form-control" placeholder="To Date" autocomplete="off">
                                    </div>
                                </div>
                                 <span id="span_id_data" style="font-size: 13px; color: #bb1010; display: none">Please Select To Date</span>
                            </div>

                            <div class="col-md-6" align="">
                                <div class="form-group">
                                    <select name="loc_dc_id" id="loc_dc_id" class="form-control select2me dc_reset" placeholder="{{ trans('inventorylabel.filters.dc') }}">
                                            @foreach ($filter_options['dc_data'] as $dc_data)
                                        <option value="{{ $dc_data->le_wh_id }}" >{{ $dc_data->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                          <div class="col-md-6">
                                 <div class="form-group">
                                         <div class="mt-checkbox-list">
                                            <label class="mt-checkbox">
                                              <input type="checkbox" id="is_active" name="is_active">Please Check To Download Again 
                                             </label>
                                         </div>
                                </div>
                         </div> 
                        </div>

                        
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="left">
                            <span style="color:red">*</span> Note: Please Select The Above Dates
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="dwn_file" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>