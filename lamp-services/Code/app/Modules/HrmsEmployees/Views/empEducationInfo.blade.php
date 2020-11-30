<div class="tab-pane" id="tab_edu_info">
    <div class="basicInfoOverlay"></div>
    @if($myProfile_id != "" || $editAccess == '1')
    <div class="row" align="right">
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#education_modal" id="add_btn_education" style="margin-right: 21px;">Add</button>
    </div>
    @endif
    <div class="modal fade modal-scroll" id="education_modal" role="dialog">
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close education_close" data-dismiss="modal" >&times;</button>
          <h4 class="modal-title " id="education_tab_title">Add Education Information</h4>
        </div>
        <div class="modal-body">
           <form action="#" class="submit_form " id="empEducation_form" method="get">
            <input type="hidden" name="emp_education_id" id="emp_education_id" value="">
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="control-label "><strong>School (or) University</strong></label>
                    <input type="text" class="form-control" name="institute" id="institute" value="" placeholder="School (or) University (ex: OU University)" />
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="control-label "><strong>Degree</strong></label>
                    <input type="text" class="form-control" name="degree" id="degree" value="" placeholder="Degree (ex: MBA)" />
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-12">
                        <label class="control-label "><strong>Field of Study</strong></label>
                        <input type="text" class="form-control" name="specilization" id="specilization" value=""  placeholder="Field of study (ex: Marketing)" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-12">
                        <label class="control-label "><strong>Grade/ Percentage</strong></label>
                        <input type="text" class="form-control" name="grade" id="grade" value=""  placeholder="Grade" />
                    </div>
                   
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-12">
                        <label class="control-label "><strong>From Date</strong></label>
                        <input type="text" class="form-control" name="from_year" id="from_year" value=""  placeholder="From Date" autocomplete="off" />
                    </div>
                    
                </div>
            </div>
           <div class="row">
                <div class="form-group">
                    <div class="col-md-12" >
                        <label class="control-label "><strong>To Date</strong></label>
                        <input type="text" class="form-control" name="to_year" autocomplete="off" id="to_year" value=""  placeholder="To Date" />
                    </div>
                </div>
            </div>
            <div class="row">
                <hr />
                <div class="col-md-12 text-center"> 
                    <input type="submit" class="btn green-meadow" value="Save" /> 
                </div>
            </div>
        </form>
        </div>
      </div>
    </div>
</div>


    <table class="table table-striped table-bordered table-advance table-hover" id="emp_education_table" style="margin-top: 25px;">
        <thead>
            <tr>
                <th>School (or) University</th>
                <th>Degree</th>
                <th>Field of Study</th>
                <th>Grade/ Percentage</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Action</th>
            </tr>
        </thead>                    
        <tbody>
        @if(isset($eduArray) && count($eduArray) > 0)
        @foreach($eduArray as $doc)                        
        <tr>
            <td>{{$doc['institute']}}</td>
            <td>{{$doc['degree']}}</td>
            <td>{{$doc['specilization']}}</td>
            <td>{{$doc['grade']}}</td>
            <td>{{$doc['from_year']}}</td>
            <td>{{$doc['to_year']}}</td>
            <td>
                 @if($editAccess == '1' ||  $myProfile_id != "")
                <span><a onclick="editEducation({{$doc['emp_education_id']}})"  href="javascript:void(0);"><i class="fa fa-pencil"></i></a></span>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <span><a class="delete delete_educations" id="{{$doc['emp_education_id']}}" href="javascript:void(0);"><i class="fa fa-trash-o"></i></a></span>
                 @endif
            </td>
        </tr>
        @endforeach
        </tbody>
        @endif       
    </table> 
    <div id="edu_table_msg"><?php if(!count($eduArray)){?> <p>No Records Found.</p> <?php } ?></div>     
   
</div>

