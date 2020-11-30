              <div class="tab-pane" id="tab_22">
                  <form id="supplierdocs" name="supplierdocs" method="POST" enctype="multipart/form-data">
                              <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                <div class="row PAN">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">PAN Card Number <span class="required PAN_I" aria-required="true">*</span> <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span> </label>
                      <input type="text" class="form-control" id="pan_number" name="pan_number" value="@if(isset($pan_det['ref_no'])){{$pan_det['ref_no']}}@endif">
                    </div>
                  </div>
                   <?php
                    $bp = url('uploads/Suppliers_Docs');
                    $base_path = $bp."/";
                    ?>
                

                  <div class="col-md-4">

                    <div class="form-group">
                    <label class="control-label">PAN Proof <span class="required PAN_F" aria-required="true">*</span></label>
                      <div class="row">
                        <div class="col-md-12">  

                          <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-left:16px !important;">
                          <span class="btn default btn-file btn green-meadow" style="width:110px !important;">

                          <span class="fileinput-new">Choose File </span>
                          <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>


                          </span>
                         
                          <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                          <?php
                          $extn = '';
                          if(isset($pan_det['img']))
                          {    
                          $ext1 = explode(".",$pan_det['img']);
                          if(isset($ext1[1]))
                          {
                          $extn = $ext1[1];
                          }
                          }
                          ?>
                          <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px;">@if($extn == 'png' || $extn == 'jpg' || $extn == 'jpeg')<a href="@if(isset($pan_det['img'])){{$base_path.$pan_det['img']}}@endif" target="blank"> <img src="@if(isset($pan_det['img'])){{$base_path.$pan_det['img']}}@endif"  class="pan_files_id" alt="" /></a>@elseif($extn == 'doc' || $extn == 'docx')<a target="_blank" class="pan_files_id" href="@if(isset($pan_det['img'])){{$base_path.$pan_det['img']}}@endif"><img src="{{$base_path."word.jpg"}}" style="width: 40px; height: 33px;"  /></a> @elseif($extn == 'pdf')<a target="_blank" class="pan_files_id" href="@if(isset($pan_det['img'])){{$base_path.$pan_det['img']}}@endif"><img src="{{$base_path."pdf.jpg"}}" style="width: 40px; height: 33px;" /></a>@endif</div>
                          
                          <br />
                          <input id="pan_files" type="file" class="upload" name="pan_files" style=" margin-top: -30px;position: absolute;opacity: 0;width:110px !important;"/>

                          <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                          <a class="files_edit" href="@if(isset($pan_det['img'])){{$base_path.$pan_det['img']}}@endif">@if(isset($pan_det['img'])){{$pan_det['img']}}@endif</a>

                        </div>



                        </div>
                      </div>
                    </div>




                  </div>
                </div>
                <div class="row CIN">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">CIN Number <span class="required CIN_I" aria-required="true">*</span><span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span></label>
                      <input type="text" class="form-control" id="cin_number" name="cin_number" value="@if(isset($cin_det['ref_no'])){{$cin_det['ref_no']}}@endif">
                    </div>
                  </div>   
                    <div class="col-md-4">
                    
                     <div class="form-group">
                      <label class="control-label">CIN Proof <span class="required CIN_F" aria-required="true">*</span></label>
                      <div class="row">
                        <div class="col-md-12">  

                          <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-left:16px !important;">
                          <span class="btn default btn-file btn green-meadow" style="width:110px !important;">

                          <span class="fileinput-new">Choose File </span>
                          <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>


                          </span>
                         
                          <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                          <?php
                          $extn2 = '';
                          if(isset($cin_det['img']))
                          {
                          $ext2 = explode(".",$cin_det['img']);
                          if(isset($ext2[1]))
                          {
                          $extn2 = $ext2[1];
                          }
                          }
                          ?>
                          <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px;">@if($extn2 == 'png' || $extn2 == 'jpg' || $extn2 == 'jpeg') <a href="@if(isset($cin_det['img'])){{$base_path.$cin_det['img']}}@endif" target="blank"> <img src="@if(isset($cin_det['img'])){{$base_path.$cin_det['img']}}@endif" alt="" class="cin_files_id" /></a>@elseif($extn2 == 'doc' || $extn2 == 'docx')<a target="_blank" class="cin_files_id" href="@if(isset($cin_det['img'])){{$base_path.$cin_det['img']}}@endif"><img src="{{$base_path."word.jpg"}}" style="width: 40px; height: 33px;"  /></a> @elseif($extn2 == 'pdf')<a target="_blank" class="cin_files_id" href="@if(isset($cin_det['img'])){{$base_path.$cin_det['img']}}@endif"><img src="{{$base_path."pdf.jpg"}}" style="width: 40px; height: 33px;"  /></a> @endif</div>
                          
                          <br />
                          <input id="cin_files" type="file" class="upload" name="cin_files" style=" margin-top: -30px;position: absolute;opacity: 0;width:110px !important;"/>

                          <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                          <a class="files_edit" href="@if(isset($cin_det['img'])){{$base_path.$cin_det['img']}}@endif">@if(isset($cin_det['img'])){{$cin_det['img']}}@endif</a>
                          </div>



                        </div>
                      </div>
                    </div>


                  </div>
                </div>
                <div class="row VAT">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">TIN/VAT Number <span class="required TINVAT_I" aria-required="true">*</span><span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span></label>
                      <input type="text" class="form-control" name="tinvat_number" id="tinvat_number" value="@if(isset($tinvat_det['ref_no'])){{$tinvat_det['ref_no']}}@endif">
                    </div>
                  </div>
                   <div class="col-md-4">
                    
                     <div class="form-group">
                      <label class="control-label">VAT Proof <span class="required TINVAT_F" aria-required="true">*</span></label>
                      <div class="row">
                        <div class="col-md-12">  

                          <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-left:16px !important;">
                          <span class="btn default btn-file btn green-meadow" style="width:110px !important;">

                          <span class="fileinput-new">Choose File </span>
                          <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>


                          </span>
                         
                          <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                          <?php
                          $extn3 = '';
                          if(isset($tinvat_det['img']))
                          {
                          $ext3 = explode(".",$tinvat_det['img']);
                          if(isset($ext3[1]))
                          {
                          $extn3 = $ext3[1];
                          } 
                          }
                          ?>
                          <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px;">@if($extn3 == 'png' || $extn3 == 'jpg' || $extn3 == 'jpeg') <a href="@if(isset($tinvat_det['img'])){{$base_path.$tinvat_det['img']}}@endif" target="blank"><img src="@if(isset($tinvat_det['img'])){{$base_path.$tinvat_det['img']}}@endif" alt="" class="tinvat_files_id" /></a>@elseif($extn3 == 'doc' || $extn3 == 'docx')<a target="_blank" class="tinvat_files_id" href="@if(isset($tinvat_det['img'])){{$base_path.$tinvat_det['img']}}@endif"><img src="{{$base_path."word.jpg"}}" style="width: 40px; height: 33px;"  /></a> @elseif($extn3 == 'pdf')<a target="_blank" class="tinvat_files_id" href="@if(isset($tinvat_det['img'])){{$base_path.$tinvat_det['img']}}@endif"><img src="{{$base_path."pdf.jpg"}}" style="width: 40px; height: 33px;"  /></a> @endif</div>

                          <br />
                          <input id="tinvat_files" type="file" class="upload" name="tinvat_files" style=" margin-top: -30px;position: absolute;opacity: 0;width:110px !important;"/>

                          <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                          <a class="files_edit" href="@if(isset($tinvat_det['img'])){{$base_path.$tinvat_det['img']}}@endif">@if(isset($tinvat_det['img'])){{$tinvat_det['img']}}@endif</a>
                          </div>



                        </div>
                      </div>
                    </div>


                  </div>



                </div>
                <div class="row CST">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="control-label">CST Number <span class="required CST_I" aria-required="true">*</span> <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span></label>
                      <input type="text" class="form-control" id="cst_number" name="cst_number" value="@if(isset($cst_det['ref_no'])){{$cst_det['ref_no']}}@endif">
                    </div>
                  </div>
                  <div class="col-md-4">
                    
                     <div class="form-group">
                      <label class="control-label">CST Proof <span class="required CST_F" aria-required="true">*</span></label>
                      <div class="row">
                        <div class="col-md-12">  

                          <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-left:16px !important;">
                          <span class="btn default btn-file btn green-meadow" style="width:110px !important;">

                          <span class="fileinput-new">Choose File </span>
                          <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>


                          </span>
                         
                          <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                          <?php
                          $extn4 = '';
                          if(isset($cst_det['img']))
                          {
                          $ext4 = explode(".",$cst_det['img']);
                          if(isset($ext4[1]))
                          {
                          $extn4 = $ext4[1];
                          }  
                          }
                          ?>
                          <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px;">@if($extn4 == 'png' || $extn4 == 'jpg' || $extn4 == 'jpeg') <a href="@if(isset($cst_det['img'])){{$base_path.$cst_det['img']}}@endif" target="blank"><img src="@if(isset($cst_det['img'])){{$base_path.$cst_det['img']}}@endif" alt="" class="cst_files_id" /></a>@elseif($extn4 == 'doc' || $extn4 == 'docx')<a target="_blank" class="cst_files_id" href="@if(isset($cst_det['img'])){{$base_path.$cst_det['img']}}@endif"><img src="{{$base_path."word.jpg"}}" style="width: 40px; height: 33px;"  /></a> @elseif($extn4 == 'pdf')<a target="_blank" class="cst_files_id" href="@if(isset($cst_det['img'])){{$base_path.$cst_det['img']}}@endif"><img src="{{$base_path."pdf.jpg"}}" style="width: 40px; height: 33px;"  /></a> @endif</div>

                          <br />
                          <input id="cst_files" type="file" class="upload" name="cst_files" style=" margin-top: -30px;position: absolute;opacity: 0;width:110px !important;"/>

                          <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                          <a class="files_edit" href="@if(isset($cst_det['img'])){{$base_path.$cst_det['img']}}@endif">@if(isset($cst_det['img'])){{$cst_det['img']}}@endif</a>
                          </div>



                        </div>
                      </div>
                    </div>


                  </div>




                </div>
                <div class="row CHEQUE">
                  <div class="col-md-4">
                    <div class="form-group"> &nbsp;</div>
                  </div>
                 <div class="col-md-4">
                    
                     <div class="form-group">
                      <label class="control-label">Cancel Cheque Proof<span class="required CHEQUE_F" aria-required="true">*</span></label>
                      <div class="row">
                        <div class="col-md-12">  

                          <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-left:16px !important;">
                          <span class="btn default btn-file btn green-meadow" style="width:110px !important;">

                          <span class="fileinput-new">Choose File </span>
                          <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>


                          </span>
                         
                          <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                          <?php
                          $extn5 = '';
                          if(isset($cheque_det['img']))
                          {
                          $ext5 = explode(".",$cheque_det['img']);
                          if(isset($ext5[1]))
                          {
                          $extn5 = $ext5[1];
                          }  
                          }
                          ?>
                          <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px;">@if($extn5 == 'png' || $extn5 == 'jpg' || $extn5 == 'jpeg') <a href="@if(isset($cheque_det['img'])){{$base_path.$cheque_det['img']}}@endif" target="blank"><img src="@if(isset($cheque_det['img'])){{$base_path.$cheque_det['img']}}@endif" alt="" class="cheque_files_id" /></a>@elseif($extn5 == 'doc' || $extn5 == 'docx')<a target="_blank" class="cheque_files_id" href="@if(isset($cheque_det['img'])){{$base_path.$cheque_det['img']}}@endif"><img src="{{$base_path."word.jpg"}}" style="width: 40px; height: 33px;"  /></a> @elseif($extn5 == 'pdf')<a target="_blank" class="cheque_files_id" href="@if(isset($cheque_det['img'])){{$base_path.$cheque_det['img']}}@endif"><img src="{{$base_path."pdf.jpg"}}" style="width: 40px; height: 33px;"  /></a> @endif</div>

                          <br />
                          <input id="cheque_files" type="file" class="upload" name="cheque_files" style=" margin-top: -30px;position: absolute;opacity: 0;width:110px !important;"/>

                          <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                          <a class="files_edit" href="@if(isset($cheque_det['img'])){{$base_path.$cheque_det['img']}}@endif">@if(isset($cheque_det['img'])){{$cheque_det['img']}}@endif</a>
                          </div>



                        </div>
                      </div>
                    </div>


                  </div>



                </div>
                <div class="row MOU">
                  <div class="col-md-4">
                    <div class="form-group"> &nbsp;</div>
                  </div>
                    <div class="col-md-4">
                    
                     <div class="form-group">
                      <label class="control-label">MOU Document <span class="required MOU_F" aria-required="true">*</span></label>
                      <div class="row">
                        <div class="col-md-12">  

                          <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-left:16px !important;">
                          <span class="btn default btn-file btn green-meadow" style="width:110px !important;">

                          <span class="fileinput-new">Choose File </span>
                          <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>


                          </span>
                         
                          <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span>
                          <?php
                          $extn6 = '';
                          if(isset($mou_det['img']))
                          {
                          $ext6 = explode(".",$mou_det['img']);
                          if(isset($ext6[1]))
                          {
                          $extn6 = $ext6[1];
                          }  
                          }
                          ?>
                          <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px;">@if($extn6 == 'png' || $extn6 == 'jpg' || $extn6 == 'jpeg') <a href="@if(isset($mou_det['img'])){{$base_path.$mou_det['img']}}@endif" target="blank"><img src="@if(isset($mou_det['img'])){{$base_path.$mou_det['img']}}@endif" alt="" class="mou_files_id" /></a>@elseif($extn6 == 'doc' || $extn6 == 'docx')<a class="mou_files_id" href="@if(isset($mou_det['img'])){{$base_path.$mou_det['img']}}@endif"><img src="{{$base_path."word.jpg"}}" style="width: 40px; height: 33px;"  /></a> @elseif($extn6 == 'pdf')<a target="_blank" class="mou_files_id" href="@if(isset($mou_det['img'])){{$base_path.$mou_det['img']}}@endif"><img src="{{$base_path."pdf.jpg"}}" style="width: 40px; height: 33px;"  /></a>  @endif</div>
                          <br />

                          <input id="mou_files" type="file" class="upload" name="mou_files" style=" margin-top: -30px;position: absolute;opacity: 0;width:110px !important;"/>

                          <span class="fileinput-filename" style="white-space:normal !important; word-wrap:break-word; width:333px;">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>
                          <a class="files_edit" href="@if(isset($mou_det['img'])){{$base_path.$mou_det['img']}}@endif">@if(isset($mou_det['img'])){{$mou_det['img']}}@endif</a>
                          </div>



                        </div>
                      </div>
                    </div>


                  </div>


                </div>
                <hr>
                <div class="row">
                  <div class="col-md-12 text-center">
                    <button type="submit" class="btn green-meadow">Save & Continue</button>
                    <button type="button" id="canceldocs" class="btn green-meadow">Cancel</button>
                  </div>
                </div>
                </form>
              </div>

