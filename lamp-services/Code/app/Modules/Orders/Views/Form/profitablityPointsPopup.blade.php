<div class="modal modal-scroll fade in" id="profitablityPoints" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode"> Profitability Points Report</h4>
            </div>
            <div class="modal-body">
                <form id="profitablityPointsForm" action="/profitablityPointsReport" class="text-center" method="get">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-icon right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    @if($allaccess == 1)
                                        <input type="hidden" name="all_access" value="1">
                                    @else
                                        <input type="hidden" name="all_access" value="0">
                                    @endif
                                    <input type="text" id="pp_fdate" name="fdate" class="form-control" placeholder="From Date" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-icon right">
                                    <i class="fa fa-calendar"></i>
                                    <input type="text" id="pp_tdate" name="tdate" class="form-control" placeholder="To Date" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <select name="type_id" id="center_type" class="form-control" placeholder="Center Type" required="required">
                                    <option value="">Please Select</option>
                                    <option value="1">DC</option>
                                    <option value="2">FC</option>
                                    <option value="3">FF</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <select name="loc_dc_id[]" id="loc_option_list" class="form-control select2me" autocomplete="Off" required="required"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="left">
                            <span style="color:red">*</span> Note: default current month data will download
                        </div>
                    </div>
                    <hr>
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
    window.asd = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
    window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
    window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});    
</script>