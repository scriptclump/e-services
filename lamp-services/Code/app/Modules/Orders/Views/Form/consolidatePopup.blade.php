<div class="modal modal-scroll fade in" id="consolidateOrders" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode"> Line Item Sales Report</h4>
            </div>
            <div class="modal-body">
                <form id="consolidatedOrdersForm" action="/salesorders/downloadConsolidateOrders" class="text-center" method="get">
                    <div class="row">
                        <div class=" col-md-6">
                            <label class="col-md-4" style="padding-top: 10px;"><strong>By Date:</strong></label>
                            <div class="col-md-8">
                                <select name="report_id" id="report_id" class="form-control select2me" placeholder="Report(s)" required="required">
                                <option value="1">Orders Report</option>
                                <option value="2">Invoice Report</option>
                                <option value="3">Cancelled Report</option>
                                <option value="4">Returns Report</option>
                                <option value="5">Delivered Report</option>
                                <option value="6">Test Live Orders Report</option>
                                </select>
                            </div>   
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <select name="loc_dc_id[]" id="loc_dc_id" class="form-control multi-select-search-box avoid-clicks"
                                    multiple="multiple" placeholder="{{ trans('inventorylabel.filters.dc') }}" required="required">
                                @if($allaccess == 1 || $globalaccess == 1)
                                <option value="0">ALL</option>
                                @endif
                                @foreach ($filter_options['dc_data'] as $dc_data)
                                    <option value="{{ $dc_data->le_wh_id }}" >{{ $dc_data->name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="text" id="cons_order_fdate" name="fdate" class="form-control" placeholder="From Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="cons_order_tdate" name="tdate" class="form-control" placeholder="To Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="left">
                            <span style="color:red">*</span> Note: default current month data will download
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
</div>'

<script type="text/javascript">
      window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
</script>