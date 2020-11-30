    <!-- Add / Edit Model for Business Flow-->
    <div class="modal modal-scroll fade" id="business_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">ADD / EDIT BUSINESS FLOW</h4>
            </div>
            {{ Form::open(array('url' => '/businessunit/saveeditbusiness', 'method' => 'POST', 'id' =>'business_form_id'))}}

        <div class="modal-body">
        <div class="row">
        <div class="col-md-12">
        <div class="portlet box">
        <div class="portlet-body">
        <div class="row">
                    <div class="col-md-6"><div class="form-group">
                        <label class="control-label">Business Name</label>
                        <input type="hidden" name="add_edit_flag" id="add_edit_flag" value="0">
                        <input type="hidden" name="add_business_id" id="add_business_id" value="">
                        <input type="hidden" name="businessunit_id" id="businessunit_id" value="0">
                        <input type = "hidden" name ="parent_bu_id" id = "parent_bu_id">
                        <input type="text" id="business_name" name="business_name" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-6"><div class="form-group">
                        <label class="control-label">Description</label>
                            <input type="text" id="description" name="description" class="form-control">
                        </div>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-md-6"><div class="form-group">
                        <label class="control-label">Parent</label>
                            <select id = "parent_id"  name =  "parent_id" class="form-control select2me">
                                <option value = "0">--Please Select--</option>
                                <!-- @foreach($parentData as $businessdata)
                                <option value = "{{$businessdata->bu_id}}">{{$businessdata->bu_name}}</option>
                                @endforeach -->
                            </select>
                        </div>
                    </div>

                     <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Status</label>
                            <select id = "status"  name = "status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">In Active</option>
                        </select>    
                        </div>
                    </div>
                    </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Cost Center</label>
                            <input type = "text" class="form-control" name = "cost_center" id = "cost_center"/>   
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Legal Entity</label>
                            <select class="form-control select2me" name="legal_entity_bu" id="legal_entity_bu">
                                <option value="">{{trans('users.users_form_fields.role_select')}}</option>
                                @foreach($businessUnitsData as $buData)
                                    <option value="{{$buData->legal_entity_id}}">{{$buData->display_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Tally Company Name</label>
                            <input type = "text" class="form-control" name = "tally_company_name" id = "tally_company_name"/>  
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Sales Ledger Name</label>
                            <input type = "text" class="form-control" name = "sales_ledger_name" id = "sales_ledger_name"/>  
                        </div>
                    </div>
                </div>            




<div class="row">
        <div class="col-md-12 text-center">
            <button type="submit" class="btn green-meadow" id="price-save-button">Save</button>
        </div>
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