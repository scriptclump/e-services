
<?php 
use App\Modules\PurchaseOrder\Models\PurchaseOrder;
use App\Modules\Orders\Models\MasterLookup;
use App\Central\Repositories\RoleRepo;
use App\Modules\LegalEntity\Models\LegalEntityModel;


$this->_roleRepo = new RoleRepo();
$addPaymentFeature = $this->_roleRepo->checkPermissionByFeatureCode('STP0011');
$addPaymentExport = $this->_roleRepo->checkPermissionByFeatureCode('PAYEXPORT01');

$this->_masterLookup = new MasterLookup();
$this->_poModel = new PurchaseOrder();
$this->legalentityModel = new LegalEntityModel();
$ledgerAccounts = $this->_poModel->getTallyLedgerAccounts();
$paymentType = $this->_masterLookup->getAllOrderStatus('Payment Type', [2, 3]);
$modeofpayment = $this->legalentityModel->getpaymenttypes();
?>
<div class="tab-pane" id="tab44">

<div class="portlet-body">
    <div class="row">
        <div class="col-md-2" style ="margin-top:-34px">
                <div class="form-group">
                    <label for="month" class="control-label">From Date</label>
                    <input type="text" class="form-control " name="from_date_report" id="from_date_report" placeholder="From Date" autocomplete="off">
                
                </div>
        </div>
            <div class="col-md-2" style ="margin-top:-34px">
                <div class="form-group">
                    <label for="year" class="control-label">To Date</label>
                    <input type="text" class="form-control " name="to_date_report" id="to_date_report" placeholder="To Date" autocomplete="off">
                        
                </div>
            </div>

            <div class="col-md-2" style ="margin-top:-18px">
            <div class="form-group genra">
                <button type="button" id="filter_button" class="btn green-meadow">Go</button>
            </div>
          </div>
        <div class="col-md-2" style ="margin-top:-18px">
            @if(isset($addPaymentFeature) && $addPaymentFeature && (isset($view) && $view=='') && (Session::get('legal_entity_id'))!=$details[0]->legal_entity_id)
            <button type="button" class="btn green-meadow" href="#addPaymentModel" data-toggle="modal">Add Payment</button>
            @endif
        </div>
    </div>
</div>
     <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover table-advance" id="stockist_history" name = "stockist_history">
         </table>
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
@if(isset($addPaymentFeature) && $addPaymentFeature)
<div class="modal modal-scroll fade in" id="addPaymentModel" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                <h4 class="modal-title" id="basicvalCode">Add Payment </h4>
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
                                 <input type="hidden" class="form-control" id="userid" name="legalentity_id" value="{{ $userid }}"/>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Transaction Date</label>
                                <input type="text" class="form-control" id="transmission_date" name="transmission_date" autocomplete="off" value=""/>
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

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Mode of Deposit</label>
                                <select class="form-control select2me" id="mode_payment_type" name="mode_payment_type" >
                                    <option value="">Select Type</option>
                                    @foreach($modeofpayment as $payment)
                                    <option value="{{ $payment->value }}">{{ $payment->master_lookup_name }}</option>
                                    @endforeach
                                </select>
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
<div class="tab-pane" id="tab55">

<div class="portlet-body">
</div>
@if(isset($addPaymentExport) && $addPaymentExport)
    <div class="row">
        <form method="post" action="{{ URL::to('/legalentity/exportData') }}" id="payment_ledger_export">
            <div class="col-md-2" style ="margin-top:-34px">
                    <div class="form-group">
                        <label for="month" class="control-label">From Date</label>
                        <input type="text" class="form-control " name="from_date_ledger" id="from_date_ledger" placeholder="From Date" autocomplete="off">               
                    </div>
            </div>
            <div class="col-md-2" style ="margin-top:-34px">
                <div class="form-group">
                    <label for="year" class="control-label">To Date</label>
                    <input type="text" class="form-control " name="to_date_ledger" id="to_date_ledger" placeholder="To Date" autocomplete="off">
                     <input type="hidden" class="form-control" id="legalentity_id_ledger" name="legalentity_id_ledger" value="{{ $userid }}"/>                        
                </div>
            </div>
            <div class="col-md-1" style ="margin-top:-18px">
                <div class="form-group genra">
                    <button type="button" id="filter_button_ledger" class="btn green-meadow" onclick="return callTrigger();">Search</button>
                </div>
            </div>
            <div class="col-md-1" style ="margin-top:-18px">
                <div class="form-group genra">
                    
                    <input type="hidden" name="_token" id = "csrf-token" value="{{ Session::token() }}">
                    <button type="button" id="download_payment_ledger" class="btn green-meadow" onclick="return callTriggerDownload();">Export</button>
                    </form>
                </div>
            </div>
        <div class="col-md-12">
            <table id="stockist_fc_ledger" class="table-scrolling" ></table>
        </div>


    </div>
</div>
@endif


<div class="tab-pane" id="tab66">
    <div class="portlet-body">
    </div>
    <div class="row">
        <form method="post" action="{{ URL::to('/legalentity/creditLimitHistory') }}" id="">
           <div class="col-md-2" style="margin-top:-34px">
              <div class="form-group">
                 <label for="month" class="control-label">From Date</label>
                 <input type="text" class="form-control " name="creditlimit_from_date" id="creditlimit_from_date" placeholder="From Date" autocomplete="off">
              </div>
           </div>
           <div class="col-md-2" style="margin-top:-34px">
              <div class="form-group">
                 <label for="year" class="control-label">To Date</label>
                 <input type="text" class="form-control " name="creditlimit_to_date" id="creditlimit_to_date" placeholder="To Date" autocomplete="off">
                 <input type="hidden" class="form-control" id="legalentity_id_credit" name="legalentity_id_credit" value="{{ $userid }}" />
              </div>
           </div>
           <div class="col-md-1" style="margin-top:-18px">
              <div class="form-group genra">
                 <button type="button" id="filter_button_credit" class="btn green-meadow" onclick="return callTriggerForDates();">Search</button>
              </div>
           </div>
           <div class="col-md-1" style="margin-top:-18px">
              <div class="form-group genra">
                 <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                 <!-- <button type="button" id="download_payment_ledger" class="btn green-meadow" onclick="return callTriggerDownload();">Export</button> -->
        </form>
    </div>
</div>
<div class="col-md-12">
   <table id="creditlimit_history" class="table-scrolling"></table>
</div>
<div class="modal fade" id="edit_Credit_History" tabindex="-1" role="dialog" aria-labelledby="editCreditHistory" aria-hidden="true">
    <div class="modal-dialog wide">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="editOrderCode">Edit</h4>
            </div>
            <div class="modal-body" id="popupLoader" align="center" style="display: none">
                <img src="/img/ajax-loader.gif" >
            </div>
            <div class="modal-body" id="userDiv">
                <form action="#" class="submit_form" id="creditlimit_edit_form" method="post">
                    <input type="hidden" id="csrf_token" value="{{ Session::token() }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">From Date</label>
                                <input type="text" class="form-control" name="from_date" id="from_date" readonly="readonly" value=""/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">To Date</label>
                                <input type="text" class="form-control" name="to_date" id="to_date" value=""/>
                                <input type="hidden" class="form-control" id="user_ecash_details_id" name="user_ecash_details_id" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <hr />
                        <div class="col-md-12 text-center">
                            <button class="btn green-meadow" name="Update">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>








































