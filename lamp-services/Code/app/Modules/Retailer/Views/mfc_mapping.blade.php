
<?php  //echo "<pre>";print_r($businessNames);die;  ?>

<div class="row">
<div class="mflender"> 
    <a class="btn green-meadow" data-toggle="modal" data-target="#mfcdetails" href="#mfcdetails">Add Lender</a> <span data-placement="top"></span> 
</div>
<div class="col-md-12">
        <div style="height: 500px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
            <div class="table-responsive">
                <table id="update_lender_grid"></table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mfcdetails" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                   </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">
                                   {{ Form::open(array('url' => '/retailers/mfcMapping', 'id' => 'mfc_mapping'))}}
                                        <div class="row">
                                           <div class="form-group row">
									            <div class="col-md-5">
									            <label class="control-label">Lending Partners</label>
									                <select class="form-control select2me" id="b_name" name="b_name"> 
                                                        <option value="">
                                                            
                                                        </option>
                                                       @foreach($businessNames as $name)
                                                            <option value = "{{$name->legal_entity_id}}">{{$name->business_legal_name}}</option>
                                                       @endforeach 
									                </select>
									            </div>
									               <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Credit Limit</label>
                                                    <input type="text" name="c_limit" id="c_limit" class="form-control">
                                               </div>
                                            </div>                                                                                                                               
                                        </div>
                                        <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">                                             
                                                <label>
                                                <input type="checkbox" id="is_mfc_active" value="1" name = "is_mfc_active">Active
                                                </label>              
                                            </div>
                                       </div>                                              
                                        </div> 
                                         <div class="col-md-11 text-center">
                                                <div class="form-group">
                                                    <button type="submit" class="btn green-meadow">Submit</button>
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
 @include('Retailer::update_mfc_details')

<style type="text/css">
	.mflender{
         float: right;
         margin-left: 20px;
        margin-right: 20px;
        margin-bottom:5px;

	}
    #update_lender_grid > thead > tr > th { padding: 0px 5px 0px 5px !important; }
    #update_lender_grid > tbody > tr > td { height: 25px !important; padding: 0px 5px 0px 5px; }
</style>



