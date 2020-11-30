<a class="btn green-meadow set_prrice" data-toggle="modal" href="#setPrice" data-type="add" id="addTot" style="display: none;"></a>
<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
<div class="modal fade modal-scroll in" id="setPrice" tabindex="-1" role="addlp" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="close"></button>
                <h4 class="modal-title add_totprd" id="add_totprd">Set Price</h4>
            </div>
            <form action="" method="post" id="set_price_form">                   
                <input type="hidden" class="form-control" name="set_price_productId" id="set_price_productId">
                <input type="hidden" class="form-control" name="set_price_whId" id="set_price_whId">
                <input type="hidden" class="form-control" name="set_price_supId" id="set_price_supId">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Supplier</label>
                                <input type="text" class="form-control" name="set_supplier_id" id="set_supplier_id">                                    
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Warehouse</label>
                                 <select class="form-control set_price_whid" name="set_price_whid" id="set_price_whid">
                                    <option value="">Select Warehouse</option>
                                    @if(isset($legalentity_warehouses))                    
                                    @foreach($legalentity_warehouses as $Val )
                                    <option value="{{$Val['le_wh_id']}}">{{$Val['lp_wh_name']}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Product Title</label>                                
                                <input type="text" class="form-control" name="set_product_id" id="set_product_id">                                
                            </div>
                        </div>
                        </div>                                        
                    
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">MRP</label>
                                <input type="text" class="form-control" name="price_mrp" id="price_mrp">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="control-label">{{ trans('headings.LP') }} <span class="required" aria-required="true">*</span></label>
                                <input type="text" class="form-control" name="set_price_elp" id="set_price_elp">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="control-label">DATE <span class="required" aria-required="true">*</span></label>
                                <input type="text" class="form-control" name="set_price_date" id="set_price_date">
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <input type="submit" class="btn green-meadow" value="submit">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.modal-content --> 
    </div>
    <!-- /.modal-dialog --> 
</div>
