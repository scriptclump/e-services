@if(isset($approvalOptions) && count($approvalOptions)>0)
<input type="hidden" name="approval_unique_id" id="approval_unique_id" value="{{$approvalVal['approval_unique_id']}}"/>
<input type="hidden" name="approval_module" id="approval_module" value="{{$approvalVal['approval_module']}}"/>
<input type="hidden" name="current_status" id="current_status" value="{{$approvalVal['current_status']}}"/>
<input type="hidden" name="table_name" id="table_name" value="{{$approvalVal['table_name']}}"/>
<input type="hidden" name="unique_column" id="unique_column" value="{{$approvalVal['unique_column']}}"/>
<input type="hidden" name="approvalurl" id="approvalurl" value="{{isset($approvalVal['approvalurl'])?$approvalVal['approvalurl']:''}}"/>
<div class="row margbott">
    <div class="col-md-12">
        <div style="display:none;" id="appr_error-msg" class="alert alert-danger"></div>
        <div class="form-group">
            <label class="control-label"><strong>Status</strong></label>
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
            <label class="control-label"><strong>Comment</strong></label>
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
