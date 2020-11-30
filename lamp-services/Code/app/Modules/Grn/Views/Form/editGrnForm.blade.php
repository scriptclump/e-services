<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label">Select PO <span class="required" aria-required="true">*</span></label>
            <select class="form-control select2me disabled" name="po_id" id="po_id">
                @if(!empty($grnDetails) && property_exists($grnDetails, 'po_code') && $grnDetails->po_code != '')
                    <option value="{{ $grnDetails->po_code }}" selected="true">{{ $grnDetails->po_code }}</option>
                @else
                    <option value="Manual">Direct GRN</option>
                @endif
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label">Supplier <span class="required" aria-required="true">*</span></label>
            <select class="form-control select2me disabled" name="grn_supplier" id="grn_supplier">
                @if(!empty($grnDetails) && property_exists($grnDetails, 'business_legal_name') && $grnDetails->business_legal_name != '')
                    <option value="{{ $grnDetails->business_legal_name }}" selected="true">{{ $grnDetails->business_legal_name }}</option>
                @endif
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label">Received At <span class="required" aria-required="true">*</span></label>
            <div id="warehouse_list">
                <select class="form-control select2me disabled" name="warehouse" id="warehouse">
                    @if(!empty($grnDetails) && property_exists($grnDetails, 'lp_wh_name') && $grnDetails->lp_wh_name != '')
                    <option value="{{ $grnDetails->lp_wh_name }}" selected="true">{{ $grnDetails->lp_wh_name }}</option>
                    @endif
                </select></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label">Invoice No#</label>
            <input type="text" class="form-control" maxlength="20" name="invoice_id" value="{{ $grnDetails->inward_ref_no }}" id="invoice_id"/>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label">Invoice Date</label>
            <div class="input-icon right">
                <i class="fa fa-calendar"></i>
                <?php 
                $originalDate = $grnDetails->invoice_date;
                $newDate = date("m/d/Y", strtotime($originalDate));
                ?>
                <input type="text" class="form-control" name="invoice_date" id="invoice_date" value="{{ $newDate }}">
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label">Ref No#</label>
            <input type="text" class="form-control" maxlength="20" name="reference_id" value="{{ $grnDetails->invoice_no }}" id="reference_id"/>
        </div>
    </div>    
</div>