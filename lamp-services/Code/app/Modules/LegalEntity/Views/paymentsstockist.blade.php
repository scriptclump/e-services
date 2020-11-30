<?php 
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Modules\Orders\Models\MasterLookup;
use App\Central\Repositories\RoleRepo;
use App\Modules\LegalEntity\Models\LegalEntityModel;


$this->_roleRepo = new RoleRepo();
$addPaymentFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO0011');

$this->_masterLookup = new MasterLookup();
$this->_poModel = new PurchaseOrder();
$this->legalentityModel = new LegalEntityModel();
$ledgerAccounts = $this->_poModel->getTallyLedgerAccounts();
$paymentType = $this->_masterLookup->getAllOrderStatus('Payment Type', [2, 3]);
// $stockistpaymentdetails = $this->legalentityModel->getPaymentDetailsFromView($userlegalentityid );

/*$stockist  = $this->legalentityModel->getStockistDetails();*/

?>
<div class="tab-pane" id="tab33">
    <div class="row">
        <div class="col-md-10">
        @if($paymentDetails != "0")
            @foreach($paymentDetails as $key => $value)
                @if($key != "cust_le_id" && $key!= 'le_wh_id')
                    <span><strong> {{ str_replace('_', ' ', $key) }} </strong>:&nbsp;{{ $value }}&nbsp;|&nbsp;</span>
                @endif
            @endforeach
        @endif
        <!-- <div class="col-md-2">
            @if(isset($addPaymentFeature) && $addPaymentFeature)
            <button type="button" class="btn green-meadow" href="#addPaymentModel" data-toggle="modal" style="float:right">Add Payment</button>
            @endif
        </div> -->
    </div>
    <br/>
    <div class="row">
        <div class="col-md-12 text-right">
            &nbsp;
            <span style="float:right;font-size: 11px;font-weight: bold;">* All Amounts in (₹)</span>
        </div>
        <div class="col-md-12">
            <table id="stockistPaymentGrid" class="table-scrolling" ></table>
        </div>
    </div>
</div>
</div>
<!-- @if(isset($addPaymentFeature) && $addPaymentFeature)
@if(isset($ledgerAccounts) && count($ledgerAccounts)>0) -->
<!-- <div class="modal modal-scroll fade in" id="addPaymentModel" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                <h4 class="modal-title" id="basicvalCode">Add Payment Stockist</h4>
            </div>
            <div class="modal-body">
                <form id="addpaymentforStockist">
                    <div class="row">
                        <div class="col-md-12">
                            <div style="display:none;" id="error-msg2" class="alert alert-danger"></div>
                            <div class="form-group">
                                <label class="control-label">Paid Through</label>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <select class="form-control select2me" data-live-search="true" id="paid_through_stockist" name="paid_through_stockist">
                                    <option value="">Select Account</option>
                                    @foreach($ledgerAccounts as $account)
                                    <option value="{{ $account->tlm_name.'==='.$account->tlm_group }}">{{ $account->tlm_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Transaction&nbsp;Type</label>
                                <select class="form-control select2me" id="payment_type_stockist" name="payment_type_stockist" >
                                    <option value="">Select Type</option>
                                    @foreach($paymentType as $key=>$payment)
                                    <option value="{{ $key }}">{{ $payment }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                     
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Amount(₹)</label>
                                <input type="number" min="1" class="form-control" id="payment_amount_stockist" name="payment_amount_stockist" value="" required=""/>
                                <input type="hidden" class="form-control" id="payment_hidden_sessionid" name="payment_hidden_sessionid" value="{{ $userlegalentityid }}" required=""/>
                                 <input type="hidden" class="form-control" id="userid" name="userid" value="{{ $userid }}"/>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Transaction Date</label>
                                <input type="text" class="form-control" id="transmission_date" name="transmission_date" value=""/>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Transaction Ref. No</label>
                                <input type="text" class="form-control" id="payment_ref" name="payment_ref" value=""/>
                            </div>
                        </div> 
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn green-meadow" id="addPaymentbtn">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> -->
<!-- @endif
@endif -->