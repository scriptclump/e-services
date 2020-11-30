<div class="modal modal-scroll fade in" id="inventory_new" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Inventory Opening/Closing Report</h4>
            </div>
            <div class="modal-body">
                <form id="inventoryNewForm" action="/inventory/openclosesnapshot" class="text-center" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="row">
                        <div class="col-md-6" align="">
                            <div class="customDateArea" id="customDatesView">
                                <div class="customDateWidth">
                                    <div class="form-group" id="customDatePickerZone">
                                        <div class="input-daterange input-group" id="datepicker">
                                            <span class="input-group-addon">Select Month</span>
                                            <input type="text" class="form-control" autocomplete="off" name="from_date" id="from_date"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" align="">
                            <div class="form-group">
                                <select name="snp_new_dc_id" id="snp_new_dc_id" class="form-control select2me dc_reset" placeholder="{{ trans('inventorylabel.filters.dc') }}">
                                    <option value="0">Please select a DC</option>
                                        @foreach ($filter_options['dc_name'] as $dc_id => $dc_name)
                                    <option value="{{ $dc_id }}" >{{ $dc_name }}</option>                                        
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <span style="margin-left:-180px">*By default current month data of all DC's will be downloaded!</span>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="uploadnewfile" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    
</script>
