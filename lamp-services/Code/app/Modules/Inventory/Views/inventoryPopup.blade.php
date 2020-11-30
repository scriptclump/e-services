<div class="modal modal-scroll fade in" id="inventory" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Inventory Snapshot</h4>
            </div>
            <div class="modal-body">
                <form id="inventoryForm" action="/inventory/snapshotexport" class="text-center" method="POST">
                    <div class="row">
                        <div class="" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="text" id="start_date" name="fdate" class="form-control" placeholder="From Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="end_date" name="tdate" class="form-control" placeholder="To Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" align="">
                            <div class="form-group">
                                <select name="snp_dc_id" id="snp_dc_id" class="form-control select2me dc_reset" placeholder="{{ trans('inventorylabel.filters.dc') }}">
                                    @foreach ($filter_options['dc_name'] as $dc_id => $dc_name)
                                    <option value="{{ $dc_id }}" >{{ $dc_name }}</option>                                        
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" align="">
                            <div class="form-group">
                            <select name="invproduct_id" id="product_id" class="form-control select2me">
                                <option value="">Select Products</option>
                                @foreach($productdetail as $productdetails)           
                                <option value="{{$productdetails['product_id']}}">{{$productdetails['product_title']}}</option> 
                                @endforeach  

                                </select>
                            </div>
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
        </div>
    </div>
</div>
<script type="text/javascript">
    
</script>
