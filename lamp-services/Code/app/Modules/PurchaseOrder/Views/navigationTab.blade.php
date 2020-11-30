<div class="tabbable-line">
    <ul class="nav nav-tabs nav-tabs-lg">
        <?php $actionName = Request::segment(1) . '/' . Request::segment(2); ?>        
        <li class="{{($actionName == 'po/details' ? 'active' : '')}}"><a href="#tab11" class="potabs" data-type="po" data-id="{{$productArr[0]->po_id}}" data-toggle="tab" onclick="getPoDetail('{{$productArr[0]->po_id}}')" aria-expanded="false">Details</a></li>
        <li class=""><a href="#tab22" class="potabs" data-type="po" data-id="{{$productArr[0]->po_id}}" data-toggle="tab" onclick="poInvoiceList({{$productArr[0]->po_id}});" aria-expanded="true">Invoices
                <span class="badge badge-success" id="totalInvoices">@if(isset($invoiceCount)){{$invoiceCount}} @endif</span></a>
        </li>
        <li class=""><a href="#tab33" class="potabs" data-type="po" data-id="{{$productArr[0]->po_id}}" data-toggle="tab" onclick="poPaymentList({{$leId}});" aria-expanded="true" id="leid">Payments
                <span class="badge badge-success" id="totalPayments">@if(isset($totalPayments)){{$totalPayments}} @endif</span></a>
        </li>
        @if($actionName== 'po/invoiceDetail')
        <li class="active"><a title="invoiceDetail" class="potabs" data-type="invoice" data-id="{{$invoiceId}}" href="#tab00" data-toggle="tab">Invoice Detail</a></li>
        <?php /*<li class=""><a href="#tab33" data-toggle="tab" aria-expanded="true">Invoice Approval History</a></li>*/?>
        @endif

        @if(isset($is_Supplier) && $is_Supplier == 0)
            @if($actionName== 'po/details')
            <li class=""><a href="#tab34" class="potabs" data-type="po" data-id="{{$productArr[0]->po_id}}" data-toggle="tab" aria-expanded="true">PO Approval History</a></li>
            @endif
        @endif

    </ul>
    <div class="tab-content">
        <div class="tab-pane {{($actionName == 'po/details' ? 'active' : '')}}" id="tab11">
        </div>
        <div class="tab-pane active" id="tab00">            
            @if($actionName == 'po/invoiceDetail')
            @include('PurchaseOrder::Form.invoiceDetailForm')
            @endif
        </div>
        <div class="tab-pane" id="tab22">
            <div class="row">
                <div class="col-md-12 text-right">
                    &nbsp;
                    <span style="float:right;font-size: 11px;font-weight: bold;">* All Amounts in (₹)</span>
                </div>
                <div class="col-md-12">
                    <table id="poInvoiceList"></table>
                </div>
            </div>
        </div>
        @include('PurchaseOrder::Form.payments')
        <?php /*
        <div class="tab-pane" id="tab33">
            @include('PurchaseOrder::Form.approvalHistory')
        </div>  */ ?>
        @if(isset($is_Supplier) && $is_Supplier == 0)                      
            <div class="tab-pane" id="tab34">
                @include('PurchaseOrder::Form.poapprovalHistory')
            </div>       
        @endif                 
    </div>
</div>