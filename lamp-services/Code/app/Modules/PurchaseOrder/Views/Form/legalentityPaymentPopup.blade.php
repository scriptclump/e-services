<?php 
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Modules\Orders\Models\MasterLookup;
use App\Modules\Orders\Models\GdsBusinessUnit;
use App\Central\Repositories\RoleRepo;

$this->_roleRepo = new RoleRepo();
$addPaymentFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO0011');

$this->_masterLookup = new MasterLookup();
$this->Bunit = new GdsBusinessUnit();
$this->_poModel = new PurchaseOrder();

$ledgerAccounts = $this->_poModel->getTallyLedgerAccounts();
$businessUnits = $this->Bunit->getAllBusinesUnits();
$paymentType = $this->_masterLookup->getAllOrderStatus('Payment Type', [2, 3]);
$paymentFor = $this->_masterLookup->getAllOrderStatus('Expences Type', [1]);
$item = $this->_masterLookup->getAllOrderStatus('Sponsor Category Types', [1]);
$banner_sponser_type = $this->_masterLookup->getAllOrderStatus('Sponsor and Banner Types', [1]);
$paymentFor=$paymentFor+$banner_sponser_type;

//$payments = $this->_poModel->getTotalPaymentsBySupplier($leId);
//$totPaidAmount = isset($payments->amount)?$payments->amount:0;
$leId = isset($leId)?$leId:'';
?>
<style>
    .error{color: red;}
</style>
@if(isset($addPaymentFeature) && $addPaymentFeature)
@if(isset($ledgerAccounts) && count($ledgerAccounts)>0)
<div class="modal modal-scroll fade in" id="addPaymentModel" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                <h4 class="modal-title" id="popuphead">Add Payment</h4>
            </div>
            <div class="modal-body">
                <form id="addlegalpaymentform">
                    <div class="row">
                        <div class="col-md-12">
                            <div style="display:none;" id="error-msg2" class="alert alert-danger"></div>
                            <div class="form-group">
                                <label class="control-label">Business Unit</label>
                                <input type="hidden" class="enableit" id="edit_payid" name="edit_payid"/>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" id="le_id" name="le_id" value="{{$leId}}"/>
                                <input type="hidden" id="config_mapping_id" name="config_mapping_id" value=""/>
                                <select class="form-control select2me enableit" id="cost_center" name="cost_center" required="">
                                    <option value="">Select Business Unit</option>
                                    @foreach($businessUnits as $key=>$bu)
                                    <option value="{{ $bu->cost_center }}">{{ $bu->bu_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Payment For</label>
                                <select class="form-control select2me enableit" id="payment_for" name="payment_for" required="">
                                    <option value="">Select Type</option>
                                    @foreach($paymentFor as $key=>$payfor)
                                    <option value="{{ $key }}">{{ $payfor }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="showbannerpopupdetails" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Item</label>
                                <select class="form-control select2me enableit" id="item" name="item" required="">
                                    <option value="">Select Type</option>
                                    @foreach($item as $key=>$value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Banner Name</label>
                                <select class="form-control select2me enableit" id="banner_name" name="banner_name" required="">
                                    <option value="">Select Type</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Clicks</label>
                                <input type="text" class="form-control" id="clicks" name="clicks" value="" readonly="readonly">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Click Cost</label>
                                <input type="text" class="form-control" id="clicks_cost" name="clicks_cost" value="" readonly="readonly">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Clicks Amt</label>
                                <input type="text" class="form-control" id="clicks_amt" name="clicks_amt" value="" readonly="readonly">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Impressions</label>
                                <input type="text" class="form-control" id="impressions" name="impressions" value="" readonly="readonly">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Impression Cost</label>
                                <input type="text" class="form-control" id="impressions_cost" name="impressions_cost" value="" readonly="readonly">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Impressions Amt</label>
                                <input type="text" class="form-control" id="impressions_amt" name="impressions_amt" value="" readonly="readonly">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Total Amt</label>
                            <input type="text" class="form-control" id="click_impressions_amt" name="click_impressions_amt" value="" readonly="readonly">
                        </div>
                    </div>
                    </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Paid Through</label>                                
                                <select class="form-control select2me enableit" data-live-search="true" id="paid_through" name="paid_through">
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
                                <select class="form-control select2me enableit" id="payment_type" name="payment_type" required="">
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
                                <input type="number" min="1" class="form-control enableit" id="payment_amount" name="payment_amount" value="" required=""/>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Transaction Date</label>
                                <input type="text" class="form-control enableit" id="transmission_date" name="transmission_date" value="" required=""/>
                                <?php /*<input type="checkbox" class="" name="autoinit" id="autoinit" value="1" title="auto initializes the transaction"> Auto Init*/ ?>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Transaction Ref. No</label>
                                <input type="text" class="form-control enableit" id="payment_ref" name="payment_ref" value=""/>
                                <input type="checkbox" class="" name="check_dupli" id="check_dupli" value="1" title="checking the transaction" > Allow Duplicates
                            </div>
                        </div> 
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <button type="submit" class="btn green-meadow enableit" style="float: right" id="addPaymentbtn">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@endif
@else
<p>You don't have Access,please contact administrator</p>
@endif