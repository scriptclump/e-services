<div class="modal modal-scroll fade in" id="inv_summ" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Inventory Summary</h4>
            </div>
            <div class="modal-body">
                <form id="inventorySummForm" action="/inventory/summaryexport" class="text-center" method="POST">
                    <div class="row">
                        <div class="" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}" >
                                        <input type="text" id="sum_start_date" name="sum_start_date" class="form-control" placeholder="From Date" required autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="sum_end_date" name="sum_end_date" class="form-control" placeholder="To Date" required autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-6" align="">
                        <div class="form-group">
                        <select name="invSumProduct_id[]" id="invSumProduct_id" class="form-control multi-select-search-box"  multiple="multiple">
                            <option value="">Select All Products</option>
                            @foreach($summaryProdDetails as $productdetails)           
                            <option value="{{$productdetails['product_id']}}">{{$productdetails['product_title']}}</option> 
                            @endforeach  

                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">                        
                        <div class="form-group">
                            <select name="dc_name" id="dc_name" class="form-control select2me" placeholder="{{trans('inventorylabel.filters.dc') }}" required>
                                @foreach ($filter_options['dc_name'] as $dc_id => $dc_name)
                                <option value="{{ $dc_id }}">{{ $dc_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>                  
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="inv_sum_button" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
.SumoSelect > .optWrapper > .options {
    text-align: left;
}
#dc_name{
    text-align: left;
}

</style>