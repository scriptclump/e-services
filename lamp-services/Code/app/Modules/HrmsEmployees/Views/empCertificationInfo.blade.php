
<div class="tab-pane" id="tab_certification_info">
    <div class="basicInfoOverlay"></div>
    <div class="row" align="right">
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#certification_modal" id="add_btn_certification" style="margin-right: 21px;">Add</button>
    </div>
    <table class="table table-striped table-bordered table-advance table-hover" id="emp_certification_table" style="margin-top: 25px;">
        <thead>
            <tr>
                <th>Certification Name</th>
                <th>Certification Authority</th>
                <th>Grade</th>
                <th>Certified Date</th>
                <th>Valid Upto</th>
                <th>Action</th>
            </tr>
        </thead>                    
        <tbody>
        @if(isset($certificationArray) && count($certificationArray) > 0)
        @foreach($certificationArray as $cer)                        
        <tr>
            <td>{{$cer['certification_name']}}</td>
            <td>{{$cer['institution_name']}}</td>
            <td>{{$cer['grade']}}</td>
            <td>{{$cer['certified_on']}}</td>
            <td>{{$cer['valid_upto']}}</td>
            <td>
                <span><a  id="{{$cer['employee_certification_id']}}" onclick="editCertifications({{$cer['employee_certification_id']}})" href="javascript:void(0);"><i class="fa fa-pencil"></i></a></span>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <span><a class="delete delete_cert" id="{{$cer['employee_certification_id']}}" click="delete_certifications({{$cer['employee_certification_id']}})" href="javascript:void(0);"><i class="fa fa-trash-o"></i></a></span></td>
        </tr>
        @endforeach
        </tbody>
        @endif       
    </table>      
    <div id="cer_table_msg"><?php if(!count($certificationArray)){?> <p>No Records Found.</p> <?php } ?></div>
    
    </table>      
</div>


<div class="modal fade modal-scroll" id="certification_modal" role="dialog">
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close certification_close" data-dismiss="modal" >&times;</button>
          <h4 class="modal-title " id="certification_tab_title">Add Certification Information</h4>
        </div>
        <div class="modal-body">
           <form action="#" class="submit_form " id="certification_form" method="get">
            <input type="hidden" name="employee_certification_id" id="employee_certification_id" value="">
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="control-label "><strong>Certification Name:</strong></label>
                    <input type="text" class="form-control" name="certification_name" id="certification_name" value="" placeholder="Certification Name" />
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="control-label "><strong>Certification Authority:</strong></label>
                    <input type="text" class="form-control" name="institution_name" id="institution_name" value="" placeholder="Certification Authority" />
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-12">
                        <label class="control-label "><strong>Grade:</strong></label>
                        <input type="text" class="form-control" name="grade" id="cer_grade" value=""  placeholder="Grade" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-12">
                        <label class="control-label "><strong>Certified Date:</strong></label>
                        <input type="text" class="form-control" name="certified_on" id="certified_on" value="" autocomplete="off" placeholder="Certified Date" />
                    </div>
                    
                </div>
            </div>
           <div class="row">
                <div class="form-group">
                    <div class="col-md-12" >
                        <label class="control-label "><strong>Valid Upto:</strong></label>
                        <input type="text" class="form-control" name="valid_upto" id="valid_upto" value="" autocomplete="off" placeholder="Valid Upto" />
                    </div>
                </div>
            </div>
            <div class="row">
                <hr />
                <div class="col-md-12 text-center"> 
                    <input type="submit" class="btn green-meadow" value="Save" /> 
                </div>
                <div class="basicInfoLoader"></div>
            </div>
        </form>
        </div>
      </div>
    </div>
</div>

