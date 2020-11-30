<div class="tab-pane" id="tab_experience_info">
    @if($editAccess == '1' ||  $myProfile_id != "")
    <div class="row" align="right" style="margin-bottom: 10px; margin-right: 3px;">
        <button type="button" class="btn btn-success green-meadow" data-toggle="modal" data-target="#myModal" onclick="addExperience()" >Add</button>
    </div>
    @endif
  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close experi_close" data-dismiss="modal" >&times;</button>
          <h4 class="modal-title experience_title"></h4>
        </div>
        <div class="modal-body">
           <form action="#" class="submit_form " id="experience_form" method="get">
            <input type="hidden" name="work_experience_id" id="work_experience_id" value="">
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="control-label "><strong>Title:</strong></label>
                    <input type="text" class="form-control" name="designation" id="designation" value="" />
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="control-label "><strong>Company:</strong></label>
                    <input type="text" class="form-control" name="organization_name" id="organization_name" value="" />
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-12">
                        <label class="control-label "><strong>Location:</strong></label>
                        <input type="text" class="form-control" name="location" id="location" value=""  placeholder="Region" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label "><strong>From Date:</strong></label>
                        <input type="text" class="form-control" name="from_date" id="from_date" value="" autocomplete="off" placeholder="From Date" />
                    </div>
                    <div class="col-md-6" >
                        <label class="control-label "><strong>To Date:</strong></label>
                        <input type="text" class="form-control" name="to_date" id="to_date" value=""  placeholder="To Date" autocomplete="off" />
                    </div>
                </div>
            </div>
             <div class="row">
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label "><strong>Reference Name:</strong></label>
                        <input type="text" class="form-control" name="reference_name" id="reference_name" value=""  placeholder="Reference Name" />
                    </div>
                    <div class="col-md-6" >
                        <label class="control-label "><strong>Reference Mobile No:</strong></label>
                        <input type="text" class="form-control" name="reference_contact_number" id="reference_contact_number" value=""  placeholder="Reference Mobile Number" />
                    </div>
                </div>
            </div>
            <div class="row">
                <hr />
                <div class="col-md-12 text-center"> 
                    <input type="submit" class="btn green-meadow saveusers" value="Save" id="saveusers"/> 
                </div>
                <div class="basicInfoLoader"></div>
            </div>
        </form>
        </div>
      </div>
    </div>
  </div>

    <table class="table table-striped table-bordered table-advance table-hover" id="emp_experience_table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Company Name</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Location</th>
                <th>Reference Name</th>
                <th>Reference Mobile no</th>
                <th>Action</th>
            </tr>
        </thead>                    
        <tbody>
        @if(isset($experienceData) && count($experienceData) > 0)
        @foreach($experienceData as $experienceValue)                        
        <tr>
            <td>{{$experienceValue['designation']}}</td>
            <td>{{$experienceValue['organization_name']}}</td>
            <td>{{$experienceValue['from_date']}}</td>
            <td>{{$experienceValue['to_date']}}</td>
            <td>{{$experienceValue['location']}}</td>
            <td>{{$experienceValue['reference_name']}}</td>
            <td>{{$experienceValue['reference_contact_number']}}</td>
            <td>
                @if($editAccess == '1' ||  $myProfile_id != "")
                <span><a class="delete" id="{{$experienceValue['work_experience_id']}}" onclick='editExperience({{$experienceValue['work_experience_id']}})' href="javascript:void(0);"><i class="fa fa-pencil"></i></a></span>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <span><a class="delete delete_experience" id="{{$experienceValue['work_experience_id']}}"  href="javascript:void(0);"><i class="fa fa-trash-o"></i></a></span>
            @endif
         </td>
           
        </tr>
        @endforeach
        </tbody>
        @endif       
    </table>      
    <div id="exp_table_msg"><?php if(!count($experienceData)){?> <p>No Records Found.</p> <?php } ?></div>	
</div>

