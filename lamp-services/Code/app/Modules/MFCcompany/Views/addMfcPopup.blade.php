 <div class="modal fade" id="addMfc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="mfCcompanyallgrid">Lending Partner Details</h4>
                    </div>


                    <div class="modal-body" id="addMfc">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">
                                    {{ Form::open(array('url' => 'mfccompany/addingUsers', 'id' => 'mfccompanydetails'))}}
                                
                                         <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">First Name</label>
                                                    <input type="text" name="first_name" id="first_name" class="form-control">

                                                </div>
                                                
                                            </div>
                                             <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Last Name</label>
                                                    <input type="text" name="last_name" id="last_name" class="form-control">
                                                </div>
                                            </div>                                           
                                        </div>


                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Business Legal Name</label>
                                                    <input type="text" name="business_legal_name" id="business_legal_name" class="form-control">

                                                </div>
                                                
                                            </div>
                                             <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Email ID</label>
                                                    <input type="text" name="email" id="email" class="form-control">
                                                </div>
                                            </div>                                           
                                        </div>
                                 
                                        <div class="row">
                                           <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Mobile Number</label>
                                                    <input type="text" name="phone_number" id="phone_number" class="form-control">
                                                </div>
                                            </div> 
                                              <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Company ID</label>
                                                    <input type="text" name="company_id" id="company_id" class="form-control">
                                                </div>
                                            </div>                                          
                                        </div>

                                         <div class="row">
                                           <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">GSTIN Number</label>
                                                    <input type="text" name="gstin_number" id="gstin_number" class="form-control">
                                                </div>
                                            </div> 

                                              <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">State</label>
                                                   <select name = "state_id" id="state_id" class="form-control select2me">
                                                          <option value = "0">
                                                              <p>Select State Name</p>
                                                          </option>
                                                            @foreach($state as $value)

                                                            <option value = "{{$value->zone_id}}">{{$value->name}}</option>
                                                       @endforeach                                                    
                                                    </select>
                                                </div>
                                            </div>                                          
                                        </div>
                                       
                                          <div class="row">
                                           <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Pincode</label>
                                                    <input type="text" name="pincode" id="pincode" class="form-control">
                                                </div>
                                            </div> 
                                              <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Address</label>
                                                    <input type="text" name="address" id="address" class="form-control">
                                                </div>
                                            </div>                                          
                                        </div>

                                          <div class="row">
                                           <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">City</label>
                                                    <input type="text" name="city" id="city" class="form-control">
                                                </div>
                                            </div> 
                                                                                     
                                        </div>

                                         <div class="col-md-11 text-center">
                                                <div class="form-group">
                                                    <button type="submit"  class="btn green-meadow">Submit</button>
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





