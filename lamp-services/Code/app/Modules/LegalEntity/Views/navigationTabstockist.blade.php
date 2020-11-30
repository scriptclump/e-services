<div class="tabbable-line">
    <ul class="nav nav-tabs nav-tabs-lg">
           
        <li class="active"><a href="#tab11" class="potabs" data-type="po" data-id="" data-toggle="tab" >Details</a></li>
        
        <li class=""><a href="#tab33" class="potabs" data-type="po" data-id="" data-toggle="tab" onclick="StockistPaymentGrid(<?php echo $userlegalentityid;?>);" aria-expanded="true">Orders
                <span class="badge badge-success" id="totalPayments">@if(isset($totalPayments)){{$totalPayments}} @endif</span></a>
        </li>
        <li class=""><a href="#tab44" class="potabs" data-type="po" data-id="" data-toggle="tab" onclick="StockistPaymentHistory(<?php echo $userid;?>);" aria-expanded="true">Payments History ({{$details[0]->business_legal_name}})
                <span class="badge badge-success" id="totalPayments">@if(isset($totalPayments)){{$totalPayments}} @endif</span></a>
        </li>
        @if(isset($paymentGrid) && $paymentGrid)
        <li class=""><a href="#tab55" class="potabs" data-type="po" data-id="" data-toggle="tab" onclick="StockistPaymentLedger(<?php echo $userid;?>);" aria-expanded="true">Payment Ledger</a>
        </li>
        @endif
        @if(isset($creditlimiteditView) && $creditlimiteditView)
        <li class=""><a href="#tab66" class="potabs" data-toggle="tab" onclick="CreditlimitHistory(<?php echo $userid;?>);" aria-expanded="true">Credit Limit History</a>
        </li>
        @endif
    </ul>
    <div class="tab-content">
        
           
<div class="tab-pane active" id="tab11">
<div class="row">
    <div class="col-md-12 col-sm-12">     
        <form id="update_form_info" action="#" method="POST">
        <div class="portlet light tasks-widget">
            <div class="portlet-body">
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
               
                @include('LegalEntity::signupadminview')
                <input type="hidden" id="csrf_token" name="_Token" value="{{ csrf_token() }}">
            </div>
            @if(isset($view) && $view=='' && $updateFeature)
            
            <div class="row">
                <div class="col-md-12 text-center">

                    <button type="submit" class="btn green-meadow btnn supp_info" id="customer_info">Update</button>
                    @if($creditlimiteditfeature)
                    <a class="btn green-meadow" data-toggle="modal" data-target="#addMfc" id="add_stockist">Add Temp. Credit Limit</a>
                    @endif
                </div>
            </div>
            @endif
        </div>
        </form>
        <div class="modal fade" id="legalentity_view_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                         </button>
                        <h4 class="modal-title" id="legalentityallgrid">Edit DC/FC</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">
                                   {{ Form::open(array('url' => '/legalentity', 'id' => 'update_legalentity_data'))}}
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Name</label>
                                                    <input type="text" name="f_name" id="f_name" class="form-control">
                                                    <input type="hidden" name="le_hidden1_id" id="le_hidden1_id" class="form-control">
                                                    <input type="hidden" name="user_id_data" id="user_id_data">

                                                </div>
                                                
                                            </div>
                                             <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Email ID</label>
                                                    <input type="text" name="email_id" id="email_id" class="form-control">
                                                </div>
                                            </div>                                           
                                        </div>
                                 
                                        <div class="row">
                                           <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Mobile Number</label>
                                                    <input type="text" name="mobile_no" id="mobile_no" class="form-control">
                                                </div>
                                            </div> 
                                              <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">OTP</label>
                                                    <input type="text" name="OTP_id" id="OTP_id" class="form-control" disabled>
                                                </div>
                                            </div>                                          
                                        </div>
                                        <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">                                             
                                                    <label>
                                                       <input type="checkbox" id="le_check_active" value="1" name = "le_check_active">Active
                                                    </label>              
                                            </div>
                                       </div>                                              
                                        </div> 
                                         <div class="col-md-11 text-center">
                                                <div class="form-group">
                                                    <button type="submit" class="btn green-meadow">Update</button>
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
    </div>
</div>

<div class="row" style="margin-top:10px;">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light">
            <div class="portlet-body">
                <div class="tabbable-line">                        
                    <ul class="nav nav-tabs" >
                        <li class="active"><a href="#tab_11" data-toggle="tab">Users</a></li>
                        <li><a href="#tab_22" data-toggle="tab">Documents</a></li>
                        <li><a href="#tab_77" data-toggle="tab">Warehouse Mapped</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_11">  
                            @include('LegalEntity::users')
                        </div>
                        <div class="tab-pane" id="tab_22">
                            @include('LegalEntity::documents')
                        </div>
                        <div class="tab-pane" id="tab_77">
                            @include('LegalEntity::warehouse_mapping')
                        </div>
                        
                    </div>
                </div>
             
            </div>            
        </div>
    </div>
</div>
</div>   
  
       
        @include('LegalEntity::paymentsstockist')

        @include('LegalEntity::paymentsstockisthistory')
                             
                               
    </div>
</div>

<div class="modal fade" id="addMfc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_creditlimit_popup">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>


                    <div class="modal-body" id="addMfc">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="portlet light">
                                    <div class="portlet-body">
                                        <div class="tabbable-line">                        
                                            <ul class="nav nav-tabs" >
                                                <li class="active"><a href="#tab_15_1" data-toggle="tab">Add Credit Limit</a></li>
                                                <li><a href="#tab_15" data-toggle="tab">Credit/Debit</a></li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_15_1">  
                                                    {{ Form::open(array('url' => 'legalentity/creditlimitapproval', 'id' => 'creditlimitapproval'))}}
                                    <input type="hidden" name='stockist_user_id' id='stockist_user_id' value="@if(isset($details[0]->user_id)){{$details[0]->user_id}}@endif" class="form-control">
                                   <input type="hidden" name="stockist_le_id" id="stockist_le_id"  value="@if(isset($details[0]->legal_entity_id)){{$details[0]->legal_entity_id}}@endif" class="form-control">
                                    <div class="row">
                                        <!-- <div class="col-md-3"> -->
                                            <div class="form-group">
                                                <label class="col-md-4 control-label rowlinht">Temporary Credit Limit &nbsp;&nbsp;<b>:</b></label>
                                                <div class="col-md-8 rowbotmarg">
                                                    <input class="form-control" name="credit_limit" id="credit_limit" type="text" value="" autocomplete="off" />
                                                </div>
                                            </div>
                                        </div>
                                    <div class="row">
                                       <!--  <div class="col-md-3"> -->
                                            <div class="form-group">
                                                <label class="col-md-4 control-label rowlinht">Valid From &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>:</b></label>
                                                <div class="col-md-8 rowbotmarg">
                                                    <input class="form-control" name="fromdate" id="fromdate" autocomplete="off" type="text" value="<?php echo date('d-m-Y');?>"/>
                                                </div>
                                            </div>
                                    </div>
                                     <div class="row">
                                     <!-- <div class="col-md-3"> -->
                                        <div class="form-group">
                                            <label class="col-md-4 control-label rowlinht">Valid To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>:</b></label>
                                            <div class="col-md-8 rowbotmarg">
                                                <input class="form-control" name="todate" id="todate" autocomplete="off" type="text" value=""/>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- </div> -->  
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                                <div class="form-group">
                                                    <button type="submit"  class="btn green-meadow">Submit</button>
                                                </div>
                                        </div>
                                    </div>

                                        {{ Form::close() }}
                                                </div>
                                                <div class="tab-pane" id="tab_15">

                                                <form id="creditdebit">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Transaction&nbsp;Type</label>
                                                                <select class="form-control select2me" id="payment_type" name="payment_type" >
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
                                                                <label class="control-label">Amount(₹)<span class="required">*</span></label>
                                                                <input type="number" min="1" class="form-control" id="payment_amount_stockist" name="payment_amount_stockist" value="" required=""/>
                                                                <input type="hidden" class="form-control" id="payment_hidden_sessionid" name="payment_hidden_sessionid" value="{{ $userlegalentityid }}" required=""/>
                                                                <input type="hidden" name='stockist_user_id' id='stockist_user_id' value="@if(isset($details[0]->user_id)){{$details[0]->user_id}}@endif" class="form-control">
                                                                <input type="hidden" name="stockist_le_id" id="stockist_le_id"  value="@if(isset($details[0]->legal_entity_id)){{$details[0]->legal_entity_id}}@endif" class="form-control">
                                                                <input type="hidden" class="form-control" id="userid" name="legalentity_id" value="{{ $userid }}"/>
                                                            </div>
                                                        </div> 
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Transaction Date</label>
                                                                <input type="text" class="form-control" id="trans_date" name="trans_date" autocomplete="off" value=""/>
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
                                                                <label class="control-label">Mode of Deposit<span class="required">*</span></label>
                                                                <select class="form-control select2me" id="mode_payment" name="mode_payment" >
                                                                    <option value="">Select Type</option>
                                                                    @foreach($modeofpayments as $payment)
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
                                            </div>
                                        </div>
                                     
                                    </div>            
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>