@if(isset($approvalOptions) && count($approvalOptions)>0)
<input type="hidden" name="approval_unique_id" id="approval_unique_id" value="{{$approvalVal['approval_unique_id']}}">
<input type="hidden" name="approval_module" id="approval_module" value="{{$approvalVal['approval_module']}}">
<input type="hidden" name="current_status" id="current_status" value="{{$approvalVal['current_status']}}">
<input type="hidden" name="table_name" id="table_name" value="{{$approvalVal['table_name']}}">
<input type="hidden" name="unique_column" id="unique_column" value="{{$approvalVal['unique_column']}}">
<div class="row">
    <div class="col-md-6"><i><strong>Collection Details</strong></i></div>
</div>
<br>
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Amount To Be Remitted</label>
        </div>
    </div>
    <div class="col-md-6">
            {{$approvalVal['total_amount']}}    
    </div>
</div>
<div class="row margbott">

    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">E-Cash</label>
        </div>
    </div>
    <div class="col-md-6">
            <span class="by_ecash">{{$approvalVal['rem_details']->by_ecash}}</span>    
    </div>
</div>
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Cheque</label>
        </div>
    </div>
    <div class="col-md-6">
            <span class="by_cheque">{{$approvalVal['rem_details']->by_cheque}}</span>    
    </div>
</div>
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">UPI</label>
        </div>
    </div>
    <div class="col-md-6">
            <span class="by_upi">{{$approvalVal['rem_details']->by_upi}}</span>    
    </div>
</div>
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">NEFT</label>
        </div>
    </div>
    <div class="col-md-6">
            <sapn class="by_online">{{$approvalVal['rem_details']->by_upi}}</sapn>    
    </div>
</div>

@if($approvalVal['current_status']==57051)
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Actual Amount Remitted</label>
        </div>
    </div>
    <div class="col-md-6">
            <sapn class="actual_amount_rem">{{$approvalVal['rem_details']->amount_deposited}}</sapn>    
    </div>
</div>
@endif

@if($approvalVal['current_status']==57055)
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Actual Amount Deposited</label>
        </div>
    </div>
    <div class="col-md-6">
            <input class="form-control" name="submitted_amount" data-amount="{{$approvalVal['total_amount']}}" id="submitted_amount" value=""/>    
    </div>
</div>
@endif
@if($approvalVal['current_status']==57055 || $approvalVal['current_status']==57051)
<br>
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label"><i><strong>Denominations</strong></i>&nbsp;<i class="fa fa-chevron-circle-up den-show-hide" style="cursor:pointer"></i></label>
            <div class="denominations" style="display: none;">
            <?php $currency_array= array(1,2,5,10,20,50,100,500,1000,2000); 

                  foreach ($currency_array as $currency) {
            
            ?>


            <div class="col-md-12">
                <div class="col-sm-4">{{$currency}} * </div><div class="col-sm-4"><input class="form-control denom_input" name="{{$currency}}_input" data-value="{{$currency}}" id="{{$currency}}_input" value="0" /></div><div class="col-sm-4"> = <span id="{{$currency}}_input_result">0</span></div> 
            </div>

            <?php } ?>

            <div class="col-md-12">
            <div class="col-md-4"></div><div class="col-md-4">Total</div><div class="col-md-4"> <span class="denom_total"></span></div> 
            </div>
            </div>
        </div>            
    </div>    
</div>
<br>
@endif
<div class="row">
    <div class="col-md-6"><i><strong>Deductions</strong></i></div>
</div>
<br>
@if($approvalVal['current_status']==57055)
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Fuel</label>            
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <input class="form-control" name="fuel" id="fuel" value="0"/>    
        </div>
    </div>
    <div class="col-md-3">
            <input type="file" name="fuel_pic" id="fuel_pic" value=""/>
    </div>
</div>
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Extra Vehicle</label>            
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <input class="form-control" name="other_vehicle" id="other_vehicle" value="0" />    
        </div>
    </div>
    <div class="col-md-3">
            <input type="file" name="vehicle_pic" id="vehicle_pic" value=""/>
    </div>
</div>
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Short</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <input class="form-control" name="short" id="short" value="0"/>        
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-6"><i><strong>Arears</strong></i></div>
</div>
<br>
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Due</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <input class="form-control" readonly name="due_amount" id="due_amount" value="0" />        
        </div>
    </div>
</div>
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Deposited</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <input class="form-control" name="due_deposited" id="due_deposited" value="0" />        
        </div>
    </div>
</div>
@endif
@if($approvalVal['current_status']==57051)
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Fuel</label>            
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <span class="fuel">{{$approvalVal['rem_details']->fuel}}</span>    
        </div>
    </div>
    <div class="col-md-3">
    </div>
</div>
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Extra Vehicle</label>            
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <span class="vehicle">{{$approvalVal['rem_details']->vehicle}}</span>    
        </div>
    </div>
    <div class="col-md-3">
    </div>
</div>
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Short</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <span class="short">{{$approvalVal['rem_details']->due_amount}}</span>    
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-6"><i><strong>Arears</strong></i></div>
</div>
<br>
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Outstanding</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{$approvalVal['rem_details']->due_amount}}        
        </div>
    </div>
</div>
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Due</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{$approvalVal['rem_details']->due_amount}}        
        </div>
    </div>
</div>
<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Deposited</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{$approvalVal['rem_details']->arrears_deposited}}        
        </div>
    </div>
</div>

<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Remittance Mode</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <select class="form-control" name="remittance_mode" id="remittance_mode">
                <option value="154001">By Cash</option>
                <option value="154002">By Bank</option>
            </select>        
        </div>
    </div>
</div>

<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Amount Deposited</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <input class="form-control" name="submitted_amount" data-amount="{{$approvalVal['total_amount']}}" id="submitted_amount" value=""/>        
        </div>
    </div>
</div>

<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Small Coins</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <input class="form-control" name="coins_on_hand" id="coins_on_hand" value="0"/>    
        </div>
    </div>
</div>

<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Notes On Hand</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <input class="form-control" name="notes_on_hand" id="notes_on_hand" value="0"/>    
        </div>
    </div>
</div>

<div class="row margbott">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Used for Expenses</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <input class="form-control" name="used_expenses" id="used_expenses" value="0"/>    
        </div>
    </div>
</div>
@endif

@if($approvalVal['current_status']==57052)
<div class="row margbott">
    <div class="col-md-12">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Amount To Be Submitted</label>
                {{$approvalVal['rem_details']->collected_amt}}    
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Actual Deposited</label>
                {{$approvalVal['rem_details']->amount_deposited}}    
            </div>
        </div>
    </div>    
</div>

<div class="row margbott">
    <div class="col-md-12">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Coins On Hand</label>
                {{$approvalVal['rem_details']->coins_onhand}}    
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Notes On Hand</label>
                {{$approvalVal['rem_details']->notes_onhand}}    
            </div>
        </div>
    </div>
</div>        
<div class="row margbott">
    <div class="col-md-12">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Used For Fuel</label>
                {{$approvalVal['rem_details']->fuel}}    
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Used For Other Vehicle</label>
                {{$approvalVal['rem_details']->vehicle}}    
            </div>
        </div>    
    </div>    
</div>
<div class="row margbott">
    <div class="col-md-12">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Used For Expenses</label>
                {{$approvalVal['rem_details']->used_expenses}}    
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Due Amount</label>
                {{$approvalVal['rem_details']->due_amount}}    
            </div>
        </div>    
    </div>    
</div>
@endif

@if($approvalVal['current_status']==57055 || $approvalVal['current_status']==57051)

<div class="row">
    <div class="col-md-2"><i><strong>Net Difference</strong></i></div>
    <div class="col-md-6"><span class="net_diffrence">0</span></div>
</div>
@endif
<br>

<div class="row margbott">
    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label"><strong>Status</strong></label>
        </div>    
    </div>    
    <div class="col-md-6">
        <div class="form-group">
            <select name="approval_status" id="approval_status" class="form-control">
                @foreach($approvalOptions as $key=>$options)
                <option value="{{$key}}">{{$options}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="row margbott">
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label"><i><strong>Comment</strong></i></label>
            <textarea class="form-control" name="approval_comment" id="approval_comment"></textarea>    
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <button name="approval_submit" id="approval_submit" class="btn green-meadow">Submit</button>       
    </div>
</div>
@endif
