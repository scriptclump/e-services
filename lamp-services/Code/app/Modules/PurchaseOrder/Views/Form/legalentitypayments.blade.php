<?php 
use App\Central\Repositories\RoleRepo;

$this->_roleRepo = new RoleRepo();
$addPaymentFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO0011');
?>
<div class="tab-pane" id="tabpayments">
    <div class="row">
        <div class="col-md-10">&nbsp;
        </div>
        <div class="col-md-2">
            @if(isset($addPaymentFeature) && $addPaymentFeature)
            <button type="button" class="btn" style="color: #FFFFFF;background-color: #1BBC9B; float: right;" href="#addPaymentModel" data-toggle="modal" style="float:right">Add Payment</button>
            @endif
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-md-12 text-right">
            &nbsp;
            <span style="float:right;font-size: 11px;font-weight: bold;">* All Amounts in (₹)</span>
        </div>
        <div class="col-md-12">
            <table id="PaymentList" class="table-scrolling" ></table>
        </div>
    </div>
</div>
@include('PurchaseOrder::Form.legalentityPaymentPopup')