<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label">Select PO <span class="required" aria-required="true">*</span></label>
            <select class="form-control select2me" name="po_id" id="po_id">
                <option value="Manual">Direct GRN</option>
                @foreach($grnData['poList'] as $po)
                <option value="{{ $po->po_id }}">{{ $po->po_code }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label">Supplier <span class="required" aria-required="true">*</span></label>
            <select class="form-control select2me" name="grn_supplier" id="grn_supplier">
                <option value="0">Select Supplier</option>
                @foreach($grnData['suppliers'] as $supplier)
                <option value="{{$supplier['legal_entity_id']}}">{{$supplier['business_legal_name']}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label">Received At <span class="required" aria-required="true">*</span></label>
            <div id="warehouse_list">
                <select class="form-control select2me" name="warehouse" id="warehouse">
                    <option value="0">Select Warehouse</option>
                </select></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4" id="InvoiceDateID">
        <div class="form-group">
            <label class="control-label">Invoice No#</label>
            <input type="text" class="form-control" maxlength="20" name="invoice_id" id="invoice_id"/>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label">Invoice Date</label>
            <div class="input-icon right">
                <i class="fa fa-calendar"></i>
                <input type="text" class="form-control" name="invoice_date" id="invoice_date" value="{{date('m/d/Y')}}">
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label">Ref No#</label>
            <input type="text" class="form-control" maxlength="20" name="reference_id" id="reference_id"/>
        </div>
    </div>    
</div>

<script>
    function getInvoiceDate(){
        var supplierId = document.getElementById("grn_supplier").value;

        $.ajax({
            headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
            url: '/grn/getInvoiceDate',
            type: 'POST',
            data: {'supplierId': supplierId},
            beforeSend: function() {
                document.getElementById('invoice_date').value = 'Loading...';
            },
            success: function (data) {
                document.getElementById('invoice_date').value = data;
            },
            error: function (response) {
                alert('Credit Period is not yet setup for this supplier !!');
            }
        });
    }
</script>