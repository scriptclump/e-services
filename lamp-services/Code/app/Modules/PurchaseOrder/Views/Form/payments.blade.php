<?php 
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Modules\Orders\Models\MasterLookup;
use App\Central\Repositories\RoleRepo;

$this->_roleRepo = new RoleRepo();
$addPaymentFeature = $this->_roleRepo->checkPermissionByFeatureCode('PO0011');

$this->_masterLookup = new MasterLookup();
$this->_poModel = new PurchaseOrder();
$poDetailArr = $this->_poModel->getPoCodeById($po_id);
//print_r($poDetailArr);die;
$parent_id = $poDetailArr->parent_id;
//if($parent_id==0){
    $ledgerAccounts = $this->_poModel->getTallyLedgerAccounts();
    $paymentType = $this->_masterLookup->getAllOrderStatus('Payment Type', [2, 3]);
//}
$povalInfo = $this->_poModel->getPOGRNValueByPoId($po_id);
$poValue = isset($povalInfo->po_value)?$povalInfo->po_value:0;
$grnValue = (isset($povalInfo->grn_value) && $povalInfo->grn_value!='')?$povalInfo->grn_value:0;
$payments = $this->_poModel->getTotalPaymentsBySupplier($leId);
$poPaid = $this->_poModel->getTotalPaymentsByPO($po_id);
$poPaidAmount = isset($poPaid->amount)?$poPaid->amount:0;
$totPaidAmount = isset($payments->amount)?$payments->amount:0;
$totGrn = $this->_poModel->getTotalGRNValBySupplier($leId);
$totGrnAmount = isset($totGrn->tot_grn_val)?$totGrn->tot_grn_val:0;
$totDebitNotes = $this->_poModel->getTotalPaymentsBySupplier($leId,22014);
$debitNoteAmount = isset($totDebitNotes->amount)?$totDebitNotes->amount:0;
//echo $totPaidAmount.'==='.$totGrnAmount.'==='.$debitNoteAmount;
$unsettledAmount = $totPaidAmount - $totGrnAmount; //Amount to be pay by supplier
//$outstandingAmount = $totPaidAmount - ($totGrnAmount+$debitNoteAmount); //Amount which has to pay to supplier
$outstandingAmount = $totPaidAmount - $totGrnAmount; //Amount which has to pay to supplier
?>
<div class="tab-pane" id="tab33">
    <div class="row">
        <div class="col-md-10">
            <span><strong>PO Value : </strong>{{ $poValue }} &nbsp;|&nbsp;</span>
            <span><strong>GRN Value : </strong>{{ $grnValue }} &nbsp;|&nbsp;</span>
            <span title="Current PO Outstanding"><strong>PO Outstanding: </strong>{{ number_format(($poPaidAmount-$grnValue),2) }}
            <span data-original-title="minus(-): Due, plus(+): Excess" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
             &nbsp;|&nbsp;</span>
            <span><strong>Unsettled : </strong>{{ number_format($unsettledAmount,2) }}
            <span data-original-title="minus(-): Due, plus(+): Excess" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
             &nbsp;|&nbsp;</span>
            <span title="Supplier Outstanding"><strong>Outstanding : </strong>{{ number_format($outstandingAmount,2) }}
            <span data-original-title="minus(-): Due, plus(+): Excess" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
             &nbsp;&nbsp;</span>
            </div>
        <div class="col-md-2">
            @if(isset($addPaymentFeature) && $addPaymentFeature)
            <button type="button" class="btn green-meadow" href="#addPaymentModel" data-toggle="modal" style="float:right">Add Payment</button>
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
            <table id="poPaymentList" class="table-scrolling" ></table>
        </div>
    </div>
</div>
@if(isset($addPaymentFeature) && $addPaymentFeature)
@if(isset($ledgerAccounts) && count($ledgerAccounts)>0)
<div class="modal modal-scroll fade in" id="addPaymentModel" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                <h4 class="modal-title" id="basicvalCode">Add Payment</h4>
            </div>
            <div class="modal-body">
                <form id="addpaymentform" autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div style="display:none;" id="error-msg2" class="alert alert-danger"></div>
                            <div class="form-group">
                                <label class="control-label">Paid Through</label>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" id="po_id" name="po_id" value="{{$po_id}}"/>
                                <select class="form-control select2me" data-live-search="true" id="paid_through" name="paid_through">
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
                                <select class="form-control select2me" id="payment_type" name="payment_type" required="">
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
                                <input type="number" min="1" class="form-control" id="payment_amount" name="payment_amount" value="" required=""/>
                                <input type="checkbox" class="" name="check_payment" id="check_payment" value="1" title="checking the transaction" > Excess Payment

                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Transaction Date</label>
                                <input type="text" class="form-control" id="transmission_date" name="transmission_date" value="" required=""/>
                                <input type="checkbox" class="" name="autoinit" id="autoinit" value="1" title="auto initializes the transaction"> Auto Init
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Transaction Ref. No</label>
                                <input type="text" class="form-control" id="payment_ref" name="payment_ref" value=""/>
                                 <input type="checkbox" class="" name="check_dupli" id="check_dupli" value="1" title="checking the transaction" > Allow Duplicates
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
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@endif
@endif