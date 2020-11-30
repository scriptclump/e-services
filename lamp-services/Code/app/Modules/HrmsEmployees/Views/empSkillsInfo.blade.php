<div class="tab-pane" id="tab_skill_info"> 
    <div class="overlay"></div>
    {{ Form::open(array('url' => '', 'method' => 'POST', 'id' =>'add_hrm_skill', 'name' => 'add_hrm_skill' ))}}
        <div class="row">
            <div class="col-md-12">
            @if($myProfile_id != "" || $editColAccess == '1')
                 <div class="row">
                    <div class="col-md-1">
                    <div class="form-group">
                    <label class="control-label"><strong>Skill</strong></label>
                    </div>
                    </div>
                    <div class="col-md-4">
                    <div class="form-group">
                        <input type="text" class="form-control" name="skill" id="skill" placeholder="Search skill"/>
                        <input type="hidden" id="employee_hidden_id" name="employee_hidden_id" value = "{{ $userData['emp_id'] }}" class="form-control"/>
                        <input type="hidden" name="emp_skill_id_master" id="emp_skill_id_master"/>                  
                    </div>
                    </div>
                    <div class="col-md-4">
                    <button type="submit" class="btn btn-primary" id="hrms-save-button">Add</button>
                    </div>                    
                </div>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box">
                            <div class="portlet-body">
                                <div class="tab-pane" id ="append_rows_details">
                                    <div class="row skills_div">
                                        <div class="col-md-12"><b>Skills</b> 
                                            @if($myProfile_id != "" || $editColAccess == '1')
                                            <a><i class="fa fa-pencil" id="showskill" style ="margin-left:10px;"></i></a>
                                            @endif
                                        </div>
                                    </div>  
                                    @if($myProfile_id != "" || $editColAccess == '1')
                                    <div id="historyContainer">
                                    </div>
                                    @endif
                                </div>                                        
                            </div>
                        </div>
                    </div>
                </div>
                <div class="loader"></div>
    </div>
            </div>
        {{ Form::close() }}
        
</div>