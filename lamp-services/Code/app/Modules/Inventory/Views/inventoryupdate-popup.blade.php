             <div class="modal modal-scroll fade" id="edit-products" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <span id="success_message_popup_box"></span>
                                    <h4 class="modal-title" id="myModalLabel"><h4><span id="dcname"></span></h4></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="portlet box">
                                                <div class="portlet-body">
                                                    {{ Form::open(['id' => 'updateproducts']) }}
                                                    
                                                   

<div class="row">
                                                        <div class="col-md-8">
                                                        <p><span id="product_title_inventory"></span></p>
                                                        </div>

                                                        <div class="col-md-4">
                                                        <p>{{ trans('inventorylabel.gridLevel_2_popup_sku') }} : <span id="skuID"></span></p>
                                                        </div>
</div>


                                                    

                                                     <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">{{ trans('inventorylabel.gridLevel_2_popup_soh') }}</label>
                                                                <input type="number" name="soh_update" id="soh_update" class="form-control" min="0" step="1" disabled="disabled">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">{{ trans('inventorylabel.gridLevel_2_popup_excess') }}</label>
                                                                <input type="number" name="excess_qty" id="excess_qty" class="form-control" min="0" step="1">
                                                                <input type="hidden" name="prod" id="prod">
                                                                <input type="hidden" name="warehouse_id" id="warehouse_id">
                                                            </div>
                                                        </div>
                                                    </div>


                                                 <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">{{ trans('inventorylabel.gridLevel_2_popup_dit_qty') }} <span class="right-align-labels" id="curr_ditqty">Current Qty : <span id="current_dit_qty"> </span></span> </label>
                                                                <input type="number" name="dit_qty" id="dit_qty" class="form-control" min="0" step="1">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">{{ trans('inventorylabel.gridLevel_2_popup_dnd_qty') }} <span class="right-align-labels" id="curr_dndqty">Current Qty : <span id="current_dnd_qty"> </span></span> </label>
                                                                <input type="number" name="dnd_qty" id="dnd_qty" class="form-control" min="0" step="1">
                                                                
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">{{ trans('inventorylabel.gridLevel_2_popup_reason') }}</label>
                                                                <select name="reason" id="reason" class="form-control">
                                                                    <option value="">{{ trans('inventorylabel.gridLevel_2_popup_select_reason') }}</option>
                                                                    @foreach ($inventory_reason_Codes as $key => $value)
                                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                                    @endforeach
                                                                </select>
                                                                
                                                            </div>
                                                        </div>

                                                         <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">{{ trans('inventorylabel.gridLevel_2_popup_comments') }}</label>
                                                                <textarea name="inventory_comments" id="inventory_comments" rows="4" cols="35" class="form-control"></textarea>
                                                            </div>
                                                        </div>
                                                        </div>

                                                    
                                                   
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <button class="btn green-meadow" type="submit" id="update_products">{{ trans('inventorylabel.gridLevel_2_update_btn') }}</button>
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