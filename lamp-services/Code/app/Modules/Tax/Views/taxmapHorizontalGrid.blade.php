<div class="modal fade" id="mapping-board" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">{{ trans('taxMapLabels.grid_popup_level2_assign_tax') }}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-body">
                                {{ Form::open(['id' => 'taxclass']) }}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label"><b>{{ trans('taxMapLabels.grid_popup_level2_hsncode') }}</b>&nbsp;<span class="text-danger">*</span></label> &nbsp;&nbsp;
                                            <span id="hsncodes"></span>
                                            <span id="hsncodes_sel">
                                                <!-- <select name='hsn_codes_select' id='hsn_codes_select' class="form-control">
                                                    <option value="">{{ trans('taxMapLabels.grid_popup_level2_select_hsncode') }}</option>
                                                </select> -->

                                                <input type="number" name="hsn_codes_auto_search" id="hsn_codes_auto_search" class="form-control" placeholder="Type HSN codes">
                                            </span>
                                        </div>
                                    </div>

                                    <a href="/tax/hsncodesinfo" id="hsnallinfo" target="_blank">Click for HSN Info</a>
                                    <input type="hidden" name="hidden_hsn_code" id="hidden_hsn_code"  value="">
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" id="hsn_desc_label"><b></b></label> &nbsp;&nbsp;
                                            <span id="hsn_desc_span"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" id="hsn_percentage_label"><b></b></label> &nbsp;&nbsp;
                                            <span id="hsn_percentage_span"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="text-center"><span class="alert alert-warning" id="nodata" style="display: none">All available taxes already applied.</span></div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"><b>{{ trans('taxMapLabels.grid_popup_level2_state') }}</b>&nbsp;<span class="text-danger">*</span></label> &nbsp;&nbsp;
                                            <select name='states' id='states' class="form-control">
                                                <option value="">{{ trans('taxMapLabels.grid_popup_level2_select_state') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"><b>{{ trans('taxMapLabels.grid_popup_level2_available_taxes') }}</b>&nbsp;<span class="text-danger">*</span></label> &nbsp;&nbsp;
                                            <select name='assign-maping-rules' id='assign-maping-rules' class="form-control" disabled>
                                                <option value="">{{ trans('taxMapLabels.grid_popup_level2_select_tax_class') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"><b>{{ trans('taxMapLabels.grid_popup_level2_effective_date') }}</b>&nbsp;<span class="text-danger">*</span></label> &nbsp;&nbsp;
                                            <div class="input-icon input-icon-sm right">
                                                <i class="fa fa-calendar"></i>
                                                <input type="text" name="tax_effective_date" id="tax_effective_date" class="form-control" placeholder="Effective Date" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center"><span class="alert alert-warning" id="error" style="display: none">Please select one tax class to assign.</span></div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button type="button"  class="btn green-meadow" id="savetax-mapping-prods">{{ trans('taxMapLabels.grid_popup_level2_assign_tax_btn') }}</button>
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