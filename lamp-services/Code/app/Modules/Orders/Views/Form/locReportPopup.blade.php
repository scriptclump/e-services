<div class="modal modal-scroll fade in" id="locReport" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">LOC Report</h4>
            </div>
            <div class="modal-body">
                <form id="locReport" action="/salesorders/locReport" class="text-center" method="post">
                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <?php echo csrf_field(); ?>
                                       <input type="text" id="cc_fdate" name="fdate" class="form-control" placeholder="From Date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="cc_tdate" name="tdate" class="form-control" placeholder="To Date">
                                    </div>
                                </div>
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
                            <button type="submit" id="uploadfile" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>