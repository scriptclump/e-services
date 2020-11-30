@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<ul class="page-breadcrumb breadcrumb">
  <li><a href="javascript:;">Home</a><i class="fa fa-circle"></i></li>
  <li><a href="javascript:;">Account</a><i class="fa fa-circle"></i></li>
  <li><a href="javascript:;">Supplier</a><i class="fa fa-circle"></i></li>
  <li class="active">Add Suppliers</li>
</ul>
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="portlet light tasks-widget">
      <div class="portlet-title">
        <div class="caption"> Create Supplier</div>
        <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question-circle-o"></i></a></span> </div>
      </div>
      <div class="portlet-body">
        <div class="portlet-body">
          <div class="tabbable-line">
            <ul class="nav nav-tabs ">
              <li class="active"><a href="#tab_11" data-toggle="tab">Supplier Information</a></li>
              <li><a href="#tab_22" data-toggle="tab">Documents</a></li>
              <li><a href="#tab_33" data-toggle="tab">Brands</a></li>
              <li><a href="#tab_44" data-toggle="tab">ToT</a></li>
              <li><a href="#tab_55" data-toggle="tab">Warehouses</a></li>
            </ul>
            <div class="tab-content headings">
              <div class="tab-pane active" id="tab_11">
                <h3>Supplier Information</h3>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Organization Name <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Organization Type <span class="required" aria-required="true">*</span></label>
                      <select class="form-control">
                        <option>Option 1</option>
                        <option>Option 2</option>
                        <option>Option 3</option>
                        <option>Option 4</option>
                        <option>Option 5</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Reference ERP Code <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Date Of Establishment <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Website <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Logo</label>
                      <div class="col-md-12">
                  <div class="fileUpload btn green-meadow"> <span>Choose File</span>
                    <input id="uploadBtn" type="file" class="upload" />
                  </div>
                  <input name="doc_files" id="pan_file" placeholder="No File Chosen" disabled="disabled" />
                  
                     
                      	<span style="float:right;position: relative;z-index: 9; margin-top:5px;"  data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                        
                        
                </div>
                    </div>
                  </div>
                  
                </div>
                <h3>Contact Information</h3>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">First Name <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Last Name <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Email <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Mobile <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="row">
                      <div class="col-md-7">
                        <div class="form-group">
                          <label class="control-label">Landline <span class="required" aria-required="true">*</span></label>
                          <input type="text" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-5">
                        <div class="form-group">
                          <label class="control-label">EXT Number <span class="required" aria-required="true">*</span></label>
                          <input type="text" class="form-control">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <h3>Registered Office Address</h3>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Address 1 <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Address 2</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Country <span class="required" aria-required="true">*</span></label>
                      <select class="form-control">
                        <option>Option 1</option>
                        <option>Option 2</option>
                        <option>Option 3</option>
                        <option>Option 4</option>
                        <option>Option 5</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">State <span class="required" aria-required="true">*</span></label>
                      <select class="form-control">
                        <option>Option 1</option>
                        <option>Option 2</option>
                        <option>Option 3</option>
                        <option>Option 4</option>
                        <option>Option 5</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">City <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <h3>Billing Address <span style="font-weight:normal !important; margin-left:20px;">
                      <input type="checkbox" id="inlineCheckbox1" value="option1">
                      Same As Registered Office Address
                      </label>
                      </span> </h3>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Address 1 <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Address 2</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Country <span class="required" aria-required="true">*</span></label>
                      <select class="form-control">
                        <option>Option 1</option>
                        <option>Option 2</option>
                        <option>Option 3</option>
                        <option>Option 4</option>
                        <option>Option 5</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">State <span class="required" aria-required="true">*</span></label>
                      <select class="form-control">
                        <option>Option 1</option>
                        <option>Option 2</option>
                        <option>Option 3</option>
                        <option>Option 4</option>
                        <option>Option 5</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">City <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                </div>
                <h3>Bank Details</h3>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Account Name <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Bank Name <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Account No <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Account Type <span class="required" aria-required="true">*</span></label>
                      <select class="form-control">
                        <option>Option 1</option>
                        <option>Option 2</option>
                        <option>Option 3</option>
                        <option>Option 4</option>
                        <option>Option 5</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">IFSC Code <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Branch Name</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">MICR Code</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Currecy Code <span class="required" aria-required="true">*</span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-md-12 text-center">
                    <button type="button" class="btn green-meadow">Save & Continue</button>
                    <button type="button" class="btn green-meadow">Cancel</button>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="tab_22">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">PAN Card Number <span class="required" aria-required="true">*</span> <span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span> </label>
                      <input type="text" class="form-control">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">PAN Proof <span class="required" aria-required="true">*</span></label>
                       <div class="col-md-12">
                          <div class="fileUpload btn green-meadow"> <span>Choose File</span>
                            <input id="uploadBtn" type="file" class="upload" />
                          </div>
                          <input name="doc_files" id="pan_file" placeholder="No File Chosen" disabled="disabled" />
                        </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">CIN Number <span class="required" aria-required="true">*</span><span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">CIN Proof <span class="required" aria-required="true">*</span></label>
                      <div class="col-md-12">
                          <div class="fileUpload btn green-meadow"> <span>Choose File</span>
                            <input id="uploadBtn" type="file" class="upload" />
                          </div>
                          <input name="doc_files" id="pan_file" placeholder="No File Chosen" disabled="disabled" />
                        </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">TIN/VAT Number <span class="required" aria-required="true">*</span><span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                 
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">VAT Proof <span class="required" aria-required="true">*</span></label>
                     <div class="col-md-12">
                          <div class="fileUpload btn green-meadow"> <span>Choose File</span>
                            <input id="uploadBtn" type="file" class="upload" />
                          </div>
                          <input name="doc_files" id="pan_file" placeholder="No File Chosen" disabled="disabled" />
                        </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">CST Number <span class="required" aria-required="true">*</span> <span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">CST Proof <span class="required" aria-required="true">*</span></label>
                      <div class="col-md-12">
                          <div class="fileUpload btn green-meadow"> <span>Choose File</span>
                            <input id="uploadBtn" type="file" class="upload" />
                          </div>
                          <input name="doc_files" id="pan_file" placeholder="No File Chosen" disabled="disabled" />
                        </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group"> &nbsp;</div>
                  </div>
                 
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">Cancel Cheque Proof <span class="required" aria-required="true">*</span></label>
                     <div class="col-md-12">
                          <div class="fileUpload btn green-meadow"> <span>Choose File</span>
                            <input id="uploadBtn" type="file" class="upload" />
                          </div>
                          <input name="doc_files" id="pan_file" placeholder="No File Chosen" disabled="disabled" />
                        </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group"> &nbsp;</div>
                  </div>
                 
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">MOU Document <span class="required" aria-required="true">*</span></label>
                      <div class="col-md-12">
                          <div class="fileUpload btn green-meadow"> <span>Choose File</span>
                            <input id="uploadBtn" type="file" class="upload" />
                          </div>
                          <input name="doc_files" id="pan_file" placeholder="No File Chosen" disabled="disabled" />
                        </div>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="row">
                  <div class="col-md-12 text-center">
                    <button type="button" class="btn green-meadow">Save & Continue</button>
                    <button type="button" class="btn green-meadow">Cancel</button>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="tab_33">
                <div class="row">
                  <div class="col-md-12 text-right"> <a class="btn green-meadow" data-toggle="modal" href="#addbrand">Add Brand</a> </div>
                </div>
                <div class="row">
                  <div class="col-md-12"> &nbsp;</div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="scroller" style="height: 400px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list2">
<table class="table table-striped table-advance table-hover dataTable no-footer" id="sample_editable_2">



                        <thead>
                          <tr>
                            <th>Logo</th>
                            <th>Brand Name</th>
                            <th>Discription</th>
                            <th>#Products</th>
                            <th>Authorized</th>
                            <th>Trademark</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td><img src="../../assets/admin/layout4/img/brandlogo.png"></td>
                            <td> Micromax </td>
                            <td> Does your old smartphone slow you down and freez multiple times while switching from... </td>
                            <td> 23 </td>
                            <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                            <td class="center"><code><i class="fa fa-times"></i></code></td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="tab_44">
                <div class="row">
                  <div class="col-md-12 text-right"> <a class="btn green-meadow" data-toggle="modal" href="#addp">Add Product</a> 
                  <a class="btn green-meadow" data-toggle="modal" href="#uploadp">Upload Product</a> </div>
                </div>
                <div class="row">
                  <div class="col-md-12"> &nbsp;</div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="scroller" style="height: 400px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list2">
                      <div class="table-responsive">
                        <table class="table table-striped table-advance table-hover dataTable no-footer" id="sample_editable_3" style="text-align:center">
                          <thead>
                            <tr>
                              <th width="5%">Brand</th>
                              <th width="16%">Catagory</th>
                              <th width="26%">Product Name</th>
                              <th width="3%">MRP</th>
                              <th width="3%">MSP</th>
                              <th width="3%">Bestprice</th>
                              <th width="3%">VAT%</th>
                              <th width="3%">CST%</th>
                              <th width="3%">EBP(Margin)</th>
                              <th width="7%">RBP(B2B)</th>
                              <th width="3%">CBP(B2C)</th>
                              <th width="14%">Inventory Mode</th>
                              <th width="3%">Status</th>
                              <th width="15%">Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td align="left">Samsung</td>
                              <td align="left">Mobile & Accessesories </td>
                              <td align="left">Samsung Z1(White, 4GB)</td>
                              <td>3,990</td>
                              <td>3,000</td>
                              <td>2,500</td>
                              <td>15%</td>
                              <td>2%</td>
                              <td>2,800 <span>(40%)</span></td>
                              <td>3,000 <span>(20%)</span></td>
                              <td>3,400 <span>(5%)</span></td>
                              <td>GIT</td>
                              <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                              <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-gift"></i> </a>&nbsp; <a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                            </tr>
                            <tr>
                              <td>Samsung</td>
                              <td>Mobile&Accessesories </td>
                              <td>Samsung Z1(White, 4GB)</td>
                              <td>3,990</td>
                              <td>3,000</td>
                              <td>2,500</td>
                              <td>15%</td>
                              <td>2%</td>
                              <td>2,800 <span>(40%)</span></td>
                              <td>3,000 <span>(20%)</span></td>
                              <td>3,400 <span>(5%)</span></td>
                              <td>SEO</td>
                              <td class="center"><code><i class="fa fa-times"></i></code></td>
                              <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-gift"></i> </a>&nbsp; <a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                            </tr>
                            <tr>
                              <td>Samsung</td>
                              <td>Mobile&Accessesories </td>
                              <td>Samsung Z1(White, 4GB)</td>
                              <td>3,990</td>
                              <td>3,000</td>
                              <td>2,500</td>
                              <td>15%</td>
                              <td>2%</td>
                              <td>2,800 <span>(40%)</span></td>
                              <td>3,000 <span>(20%)</span></td>
                              <td>3,400 <span>(5%)</span></td>
                              <td>GIT</td>
                              <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                              <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-gift"></i> </a>&nbsp; <a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                            </tr>
                            <tr>
                              <td>Samsung</td>
                              <td>Mobile&Accessesories </td>
                              <td>Samsung Z1(White, 4GB)</td>
                              <td>3,990</td>
                              <td>3,000</td>
                              <td>2,500</td>
                              <td>15%</td>
                              <td>2%</td>
                              <td>2,800 <span>(40%)</span></td>
                              <td>3,000 <span>(20%)</span></td>
                              <td>3,400 <span>(5%)</span></td>
                              <td>SEO</td>
                              <td class="center"><code><i class="fa fa-times"></i></code></td>
                              <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-gift"></i> </a>&nbsp; <a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                            </tr>
                            <tr>
                              <td>Samsung</td>
                              <td>Mobile&Accessesories </td>
                              <td>Samsung Z1(White, 4GB)</td>
                              <td>3,990</td>
                              <td>3,000</td>
                              <td>2,500</td>
                              <td>15%</td>
                              <td>2%</td>
                              <td>2,800 <span>(40%)</span></td>
                              <td>3,000 <span>(20%)</span></td>
                              <td>3,400 <span>(5%)</span></td>
                              <td>GIT</td>
                              <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                              <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-gift"></i> </a>&nbsp; <a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                            </tr>
                            <tr>
                              <td>Samsung</td>
                              <td>Mobile&Accessesories </td>
                              <td>Samsung Z1(White, 4GB)</td>
                              <td>3,990</td>
                              <td>3,000</td>
                              <td>2,500</td>
                              <td>15%</td>
                              <td>2%</td>
                              <td>2,800 <span>(40%)</span></td>
                              <td>3,000 <span>(20%)</span></td>
                              <td>3,400 <span>(5%)</span></td>
                              <td>SEO</td>
                              <td class="center"><code><i class="fa fa-times"></i></code></td>
                              <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-gift"></i> </a>&nbsp; <a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                            </tr>
                            <tr>
                              <td>Samsung</td>
                              <td>Mobile&Accessesories </td>
                              <td>Samsung Z1(White, 4GB)</td>
                              <td>3,990</td>
                              <td>3,000</td>
                              <td>2,500</td>
                              <td>15%</td>
                              <td>2%</td>
                              <td>2,800 <span>(40%)</span></td>
                              <td>3,000 <span>(20%)</span></td>
                              <td>3,400 <span>(5%)</span></td>
                              <td>GIT</td>
                              <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                              <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-gift"></i> </a>&nbsp; <a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                            </tr>
                            <tr>
                              <td>Samsung</td>
                              <td>Mobile&Accessesories </td>
                              <td>Samsung Z1(White, 4GB)</td>
                              <td>3,990</td>
                              <td>3,000</td>
                              <td>2,500</td>
                              <td>15%</td>
                              <td>2%</td>
                              <td>2,800 <span>(40%)</span></td>
                              <td>3,000 <span>(20%)</span></td>
                              <td>3,400 <span>(5%)</span></td>
                              <td>SEO</td>
                              <td class="center"><code><i class="fa fa-times"></i></code></td>
                              <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-gift"></i> </a>&nbsp; <a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                            </tr>
                            <tr>
                              <td>Samsung</td>
                              <td>Mobile&Accessesories </td>
                              <td>Samsung Z1(White, 4GB)</td>
                              <td>3,990</td>
                              <td>3,000</td>
                              <td>2,500</td>
                              <td>15%</td>
                              <td>2%</td>
                              <td>2,800 <span>(40%)</span></td>
                              <td>3,000 <span>(20%)</span></td>
                              <td>3,400 <span>(5%)</span></td>
                              <td>GIT</td>
                              <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                              <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-gift"></i> </a>&nbsp; <a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                            </tr>
                            <tr>
                              <td>Samsung</td>
                              <td>Mobile&Accessesories </td>
                              <td>Samsung Z1(White, 4GB)</td>
                              <td>3,990</td>
                              <td>3,000</td>
                              <td>2,500</td>
                              <td>15%</td>
                              <td>2%</td>
                              <td>2,800 <span>(40%)</span></td>
                              <td>3,000 <span>(20%)</span></td>
                              <td>3,400 <span>(5%)</span></td>
                              <td>SEO</td>
                              <td class="center"><code><i class="fa fa-times"></i></code></td>
                              <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-gift"></i> </a>&nbsp; <a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                            </tr>
                            <tr>
                              <td>Samsung</td>
                              <td>Mobile&Accessesories </td>
                              <td>Samsung Z1(White, 4GB)</td>
                              <td>3,990</td>
                              <td>3,000</td>
                              <td>2,500</td>
                              <td>15%</td>
                              <td>2%</td>
                              <td>2,800 <span>(40%)</span></td>
                              <td>3,000 <span>(20%)</span></td>
                              <td>3,400 <span>(5%)</span></td>
                              <td>GIT</td>
                              <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                              <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-gift"></i> </a>&nbsp; <a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                            </tr>
                            <tr>
                              <td>Samsung</td>
                              <td>Mobile&Accessesories </td>
                              <td>Samsung Z1(White, 4GB)</td>
                              <td>3,990</td>
                              <td>3,000</td>
                              <td>2,500</td>
                              <td>15%</td>
                              <td>2%</td>
                              <td>2,800 <span>(40%)</span></td>
                              <td>3,000 <span>(20%)</span></td>
                              <td>3,400 <span>(5%)</span></td>
                              <td>SEO</td>
                              <td class="center"><code><i class="fa fa-times"></i></code></td>
                              <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-gift"></i> </a>&nbsp; <a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                            </tr>
                            <tr>
                              <td>Samsung</td>
                              <td>Mobile&Accessesories </td>
                              <td>Samsung Z1(White, 4GB)</td>
                              <td>3,990</td>
                              <td>3,000</td>
                              <td>2,500</td>
                              <td>15%</td>
                              <td>2%</td>
                              <td>2,800 <span>(40%)</span></td>
                              <td>3,000 <span>(20%)</span></td>
                              <td>3,400 <span>(5%)</span></td>
                              <td>GIT</td>
                              <td class="center"><code><span class="green-meadow"><i class="fa fa-check"></i></span></code></td>
                              <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-gift"></i> </a>&nbsp; <a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                            </tr>
                            <tr>
                              <td>Samsung</td>
                              <td>Mobile&Accessesories </td>
                              <td>Samsung Z1(White, 4GB)</td>
                              <td>3,990</td>
                              <td>3,000</td>
                              <td>2,500</td>
                              <td>15%</td>
                              <td>2%</td>
                              <td>2,800 <span>(40%)</span></td>
                              <td>3,000 <span>(20%)</span></td>
                              <td>3,400 <span>(5%)</span></td>
                              <td>SEO</td>
                              <td class="center"><code><i class="fa fa-times"></i></code></td>
                              <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-gift"></i> </a>&nbsp; <a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="tab_55">
                <div class="row">
                  <div class="col-md-12 text-right"> <a class="btn green-meadow" data-toggle="modal" href="#uploadw">Upload Warehouse</a> 
                  <a class="btn green-meadow" data-toggle="modal" href="#addlp">Add New Warehouses</a> </div>
                </div>
                <div class="row">
                  <div class="col-md-12"> &nbsp;</div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="scroller" style="height: 400px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list2">
                      <table class="table table-striped table-advance table-hover dataTable no-footer" id="sample_editable_1">
                        <thead>
                          <tr>
                            <th>Warehouse Name</th>
                            <th>Area</th>
                            <th>City</th>
                            <th>Email ID</th>
                            <th>Phone</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                          <tr>
                            <td>Ekart Hyderabad</td>
                            <td>Uppal</td>
                            <td>Hyderabad</td>
                            <td>praneeth.javvaji@gmail.com</td>
                            <td>9080706050</td>
                            <td class="center"><a class="edit" href="javascript:;"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp; <a class="delete" href="javascript:;"> <i class="fa fa-trash-o"></i> </a></td>
                          </tr>
                        </tbody>
                      </table>
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
</div>
<div id="addbrand" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">ADD BRAND</h4>
      </div>
      <div class="modal-body">
        <form id="update_channel_fee">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Brand Name <span class="required" aria-required="true">*</span></label>
                <input name="channel_cat_fee1" id="channel_cat_fee1" type="text" class="form-control">
              </div>
              <div class="form-group">
                <label class="control-label">Logo <span class="required" aria-required="true">*</span></label>
                <div class="col-md-12">
                  <div class="fileUpload btn green-meadow"> <span>Choose File</span>
                    <input id="uploadBtn" type="file" class="upload" />
                  </div>
                  <input name="doc_files" id="pan_file" placeholder="No File Chosen" disabled="disabled" />
                </div>
                <br>
                <label class="control-label">Trade Mark Registration Proof <span class="required" aria-required="true">*</span></label>
                <div class="col-md-12">
                  <div class="fileUpload btn green-meadow"> <span>Choose File</span>
                    <input id="uploadBtn" type="file" class="upload" />
                  </div>
                  <input name="doc_files" id="pan_file" placeholder="No File Chosen" disabled="disabled" />
                </div>
                <label class="control-label">Brand Authorization Proof <span class="required" aria-required="true">*</span></label>
                <div class="col-md-12">
                  <div class="fileUpload btn green-meadow"> <span>Choose File</span>
                    <input id="uploadBtn" type="file" class="upload" />
                  </div>
                  <input name="doc_files" id="pan_file" placeholder="No File Chosen" disabled="disabled" />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Description <span class="required" aria-required="true">*</span></label>
                <textarea rows="3" class="form-control"></textarea>
              </div>
              <div class="form-group">
                <label class="control-label">Trade Mark Registration Number <span class="required" aria-required="true">*</span>
                <span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></label>
                <input name="channel_cat_fee1" id="channel_cat_fee1" type="text" class="form-control">
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer ">
        <center>
          <button class="btn green-meadow" data-dismiss="modal" aria-hidden="true">Save</button>
        </center>
      </div>
    </div>
  </div>
</div>



<div class="modal fade modal-scroll in" id="addbrand" tabindex="-1" role="addlp" aria-hidden="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">ADD WAREHOUSE</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Warehouse Name <span class="required" aria-required="true">*</span></label>
              <select class="form-control">
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
                <option>Option 4</option>
                <option>Option 5</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Contact Name <span class="required" aria-required="true">*</span></label>
              <select class="form-control">
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
                <option>Option 4</option>
                <option>Option 5</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Email <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Phone <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">Address 1 <span class="required" aria-required="true">*</span></label>
                  <input type="text" class="form-control">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">Address 2 <span class="required" aria-required="true">*</span></label>
                  <input type="text" class="form-control">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                  <input type="text" class="form-control">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">City <span class="required" aria-required="true">*</span></label>
                  <select class="form-control">
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
                <option>Option 4</option>
                <option>Option 5</option>
              </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">State <span class="required" aria-required="true">*</span></label>
                  <select class="form-control">
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
                <option>Option 4</option>
                <option>Option 5</option>
              </select>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3806.194237107969!2d78.37890601487723!3d17.450414988040805!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb93ded9f6f0d7%3A0xa3d91e5d00d50b63!2sCyber+Towers!5e0!3m2!1sen!2sin!4v1465378467671" width="265" height="290" frameborder="0" style="border:0" allowfullscreen></iframe>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">ERP Code <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Latitude</label>
                  <input type="text" class="form-control">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Logitude</label>
                  <input type="text" class="form-control">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center">
            <button type="button" class="btn green-meadow">Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="editlp" tabindex="-1" role="editlp" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">EDIT WAREHOUSE</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Warehouse Name</label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Address 1</label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Address 2</label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Area</label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">City</label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Pincode</label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-5">
                <div class="form-group">
                  <label class="control-label">Latitude</label>
                  <input type="text" class="form-control">
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-group">
                  <label class="control-label">Logitude</label>
                  <input type="text" class="form-control">
                </div>
              </div>
              <div class="col-md-2" style="margin-top:28px;"> <a href="" class="btn btn-sm blue"><i class="fa fa-map-marker"></i></a> </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Landmark</label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Email</label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Phone</label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center">
            <button type="button" class="btn green-meadow">Add New</button>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>
<div id="uploadp" class="modal fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">UPLOAD PRODUCTS</h4>
      </div>
      <div class="modal-body">
        <br>
        <div class="row">
          <div class="col-md-12 " align="center"> <a href="/download/Category_Uploads/Category_Upload_Template_V1.0.0.xlsx" role="button" id="upload_categories" class="btn green-meadow" data-toggle="modal">Download Products Template</a>
            <p class="topmarg">Lorem ipsum dolor sit amet, consectetur adipisicing elit <br>send do eiusmod tempor incidiunt ut laboret dolor manal</p>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-md-12 " align="center">
            <form id='import_category' action="{{ URL::to('categoryImportExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
              <div class="fileUpload btn green-meadow"> <span>Upload Your Products List</span>
                <input type="file" class="upload" />
              </div>
            </form>
           <p class="topmarg">Lorem ipsum dolor sit amet, consectetur adipisicing elit <br>send do eiusmod tempor incidiunt ut laboret dolor manal</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="uploadw" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">UPLOAD WAREHOUSE</h4>
      </div>
      <div class="modal-body">
        <br>
        <div class="row">
          <div class="col-md-12 " align="center"> <a href="/download/Category_Uploads/Category_Upload_Template_V1.0.0.xlsx" role="button" id="upload_categories" class="btn green-meadow" data-toggle="modal">Download Warehouse Template</a>
            <p class="topmarg">Lorem ipsum dolor sit amet, consectetur adipisicing elit <br>send do eiusmod tempor incidiunt ut laboret dolor manal</p>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-md-12 " align="center">
            <form id='import_category' action="{{ URL::to('categoryImportExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
              <div class="fileUpload btn green-meadow"> <span>Upload Your Wrehouse List</span>
                <input type="file" class="upload" />
              </div>
            </form>
           <p class="topmarg">Lorem ipsum dolor sit amet, consectetur adipisicing elit <br>send do eiusmod tempor incidiunt ut laboret dolor manal</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade modal-scroll in" id="addlp" tabindex="-1" role="addlp" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">ADD WAREHOUSE</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Warehouse Name <span class="required" aria-required="true">*</span></label>
              <select class="form-control">
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
                <option>Option 4</option>
                <option>Option 5</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Contact Name <span class="required" aria-required="true">*</span></label>
              <select class="form-control">
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
                <option>Option 4</option>
                <option>Option 5</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Email <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Phone <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">Address 1 <span class="required" aria-required="true">*</span></label>
                  <input type="text" class="form-control">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">Address 2 <span class="required" aria-required="true">*</span></label>
                  <input type="text" class="form-control">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">Pincode <span class="required" aria-required="true">*</span></label>
                  <input type="text" class="form-control">
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label">City <span class="required" aria-required="true">*</span></label>
                  <input type="text" class="form-control">
                </div>
              </div>
              
            </div>
            <div class="row">
               <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">Country <span class="required" aria-required="true">*</span></label>
                          <select class="form-control">
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
                <option>Option 4</option>
                <option>Option 5</option>
              </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="control-label">State <span class="required" aria-required="true">*</span></label>
                          <select class="form-control">
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
                <option>Option 4</option>
                <option>Option 5</option>
              </select>
                        </div>
                      </div>
                    </div>
                  </div>
            </div>
          </div>
          <div class="col-md-6">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3806.194237107969!2d78.37890601487723!3d17.450414988040805!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb93ded9f6f0d7%3A0xa3d91e5d00d50b63!2sCyber+Towers!5e0!3m2!1sen!2sin!4v1465378467671" width="265" height="290" frameborder="0" style="border:0" allowfullscreen></iframe>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">ERP Code <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Latitude</label>
                  <input type="text" class="form-control">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Logitude</label>
                  <input type="text" class="form-control">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="form-actions">
        <div class="row">
          <div class="col-md-12 text-center">
            <button type="button" class="btn green-meadow">Save</button>
          </div>
        </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>
<div class="modal fade modal-scroll in" id="addp" tabindex="-1" role="addlp" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">ADD PRODUCTS</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
               <label class="control-label">Brand <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Category<span class="required" aria-required="true">*</span></label>
              <select class="form-control">
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
                <option>Option 4</option>
                <option>Option 5</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Product Name <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
            <div class="form-group">
              <label class="control-label">Product Title <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Product Description <span class="required" aria-required="true">*</span></label>
              <textarea class="form-control" rows="3"></textarea>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">SKU ID <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">EAN <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">MRP <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Base Price <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">MSP <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">CST% <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">VAT% <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">RBP (B2B) <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">EBP (Margin) <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">CBP (B2C) <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Credit Days <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Returns Location Type <span class="required" aria-required="true">*</span></label>
               <select class="form-control">
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
                <option>Option 4</option>
                <option>Option 5</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Delivery Terms <span class="required" aria-required="true">*</span></label>
              <input type="text" class="form-control">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label col-md-12">Return Accepted <span class="required" aria-required="true">*</span></label>
              <div class="col-md-6">
              <label class="checkbox">
                <div class="checker"><span><input type="checkbox" value="1" name="remember"></span></div> Yes</label>
                <label class="checkbox">
                <div class="checker"><span><input type="checkbox" value="1" name="remember"></span></div> No</label>
                </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Margin Type <span class="required" aria-required="true">*</span></label>
               <select class="form-control">
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
                <option>Option 4</option>
                <option>Option 5</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Inventory Mode <span class="required" aria-required="true">*</span></label>
               <select class="form-control">
                <option>Option 1</option>
                <option>Option 2</option>
                <option>Option 3</option>
                <option>Option 4</option>
                <option>Option 5</option>
              </select>
            </div>
          </div>
        </div>

        
        <div class="form-actions">
        <div class="row">
          <div class="col-md-12 text-center">
            <button type="button" class="btn green-meadow">Save</button>
          </div>
        </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>
@stop
@section('style')
<style type="text/css">

.form-actions {
    padding: 10px 10px 0px 10px !important;
}


.table.dataTable{font-size:11.5px !important;}
.table.dataTable span{font-size:9px;}

.fileUpload{float:none !important;}
.form-group .col-md-12{margin-bottom: 15px; padding-left: 0 !important;}
/*#upload_file, #pan_file{ right:0px !important;}*/
.table-advance thead tr th {
    font-size: 12px !important;
}
table.dataTable thead th, table.dataTable thead td {
    padding-left:5px !important;
}
table.dataTable tbody th, table.dataTable tbody td {
    padding:3px !important}

.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 5px !important;}


.actionss{font-size:24px !important;}
.green-meadow {
    color: #1BBC9B;
}
.code {
    font-size: 24px !important;
}


.headings h3{border-bottom:1px solid #ddd; padding-bottom:5px; font-weight:bold; font-size:16px;}

label {
    margin-bottom: 0px !important;
	padding-bottom: 0px !important;
}
.modal-header {
    padding: 5px 15px !important;
}

.modal .modal-header .close {
    margin-top: 8px !important;
}

.form-group {
    margin-bottom: 5px !important;
}
.checkbox {
    display: initial !important;
}
</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('userscript') 
<script>
document.getElementById("uploadBtn").onchange = function () {
    document.getElementById("uploadFile").value = this.value;
};
document.getElementById("uploadBtn1").onchange = function () {
    document.getElementById("uploadFile1").value = this.value;
};
</script> 
<script src="{{ URL::asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/admin/pages/scripts/table-datatables-editable.min.js') }}" type="text/javascript"></script> 
<script>
            jQuery(document).ready(function () {
                Metronic.init(); // init metronic core componets
                Layout.init(); // init layout
                Demo.init(); // init demo features
                FormWizard.init();
                QuickSidebar.init(); // init quick sidebar
                Index.init(); // init index page
                Tasks.initDashboardWidget(); // init tash dashboard widget 
                ComponentsFormTools.init();
				FormFileUpload.init();
            });
        </script> 
@stop
@extends('layouts.footer')