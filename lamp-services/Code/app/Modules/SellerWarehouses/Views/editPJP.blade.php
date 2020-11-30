<form id="editPJPForm">
    <div class="row">
        <div class="col-md-6" id="spokes_select2">
            <div class="form-group">
                <label class="control-label">Spokes<span class="required" aria-required="true">*</span></label>
                <select class="form-control select2me spoke_data" name="spoke_id" id="spoke" onChange="editSpokeBeat()">
                    <option value="0">Please Select...</option>
                    @if(isset($spokes) && !empty($spokes))
                        @foreach($spokes as $spoke)
                            @if(isset($data->spoke_id) && $spoke->spoke_id == $data->spoke_id)
                                <option value="{{ $spoke->spoke_id }}" selected="selected">{{ $spoke->spoke_name }}</option>
                            @else
                                <option value="{{ $spoke->spoke_id }}">{{ $spoke->spoke_name }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Pincode<span class="required" aria-required="true">*</span></label>
                <input type="text" class="form-control" placeholder="Pincode" id="pincode_val" name="pincode" value="{{$data->default_pincode}}" />
            </div>
        </div>
        <div class="col-md-6" id="spokes_input2">
            <div class="form-group">
                <label class="control-label">Spoke<span class="required" aria-required="true">*</span></label>
                <input type="text" class="form-control" placeholder="Spoke" id="spoke_name_val" name="spoke_name" value="" />
            </div>
        </div>
<!--        <div class="col-md-2" id="spoke_button2">
            <label class="control-label">&nbsp;</label>
            <a class="btn green-meadow" href="javascript:void(0);" id="add_spoke2">
                <span style="color:#fff !important;"><i class="fa fa-plus" style="color:#fff !important;" aria-hidden="true"></i></span>
            </a>
        </div>-->
        <div class="col-md-4" id="spokes_input2">
            <label class="control-label">&nbsp;</label>
            <a class="btn green-meadow" href="javascript:void(0);" id="add_spoke_name2">
                <span style="color:#fff !important;"><i class="fa fa-check" style="color:#fff !important;" aria-hidden="true"></i></span>
            </a>
            <a class="btn green-meadow" href="javascript:void(0);" id="close_spoke_name2">
                <span style="color:#fff !important;"><i class="fa fa-close" style="color:#fff !important;" aria-hidden="true"></i></span>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Beat <span class="required" aria-required="true">*</span></label>
                <input type="text" name="pjp_name" id="pjp_name1" value="@if(isset($data->pjp_name)){{$data->pjp_name}}@endif" placeholder="Beat" class="form-control" onChange="editSpokeBeat()">
                <div id="beat_exist" class="beat_exist" name="beat_exist" style="display:none;color:#e02222;font-size:12px;padding-left: 15px;">Beat already exists</div>
                <input type="hidden" name="pjp_pincode_area_id" id="pjp_pincode_area_id" value="@if(isset($data->pjp_pincode_area_id)){{$data->pjp_pincode_area_id}}@endif">
                <input type="hidden" name="le_wh_id" id="le_wh_id" value="@if(isset($data->le_wh_id)){{$data->le_wh_id}}@endif">
                <input type="hidden" name="legal_entity_id" id="legal_entity_id" value="@if(isset($data->legal_entity_id)){{$data->legal_entity_id}}@endif">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Relationship Manager <span class="required" aria-required="true">*</span></label>
                <select class="form-control select2me" name="rm_id" id="edit_rm_id">
                    <option value="0">Please Select...</option>
                    @foreach($rm_ids as $rmId)
                        @if(isset($data->rm_id) && $rmId->user_id == $data->rm_id)
                            <option value="{{ $rmId->user_id }}" selected="selected">{{ $rmId->username }}</option>
                        @else
                            <option value="{{ $rmId->user_id }}">{{ $rmId->username }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">PDP <span class="required" aria-required="true">*</span></label>
                <select class="form-control" name="pdp" id="pdp">
                    <option value=" ">Please Select...</option>
                    @if(isset($data->pdp) && $data->pdp == 'Mon')
                    <option value="Mon" selected="selected">Mon</option>
                    @else
                    <option value="Mon">Mon</option>
                    @endif
                    @if(isset($data->pdp) && $data->pdp == 'Tue')
                    <option value="Tue" selected="selected">Tue</option>
                    @else
                    <option value="Tue">Tue</option>
                    @endif
                    @if(isset($data->pdp) && $data->pdp == 'Wed')
                    <option value="Wed" selected="selected">Wed</option>
                    @else
                    <option value="Wed">Wed</option>
                    @endif
                    @if(isset($data->pdp) && $data->pdp == 'Thu')
                    <option value="Thu" selected="selected">Thu</option>
                    @else
                    <option value="Thu">Thu</option>
                    @endif
                    @if(isset($data->pdp) && $data->pdp == 'Fri')
                    <option value="Fri" selected="selected">Fri</option>
                    @else
                    <option value="Fri">Fri</option>
                    @endif
                    @if(isset($data->pdp) && $data->pdp == 'Sat')
                    <option value="Sat" selected="selected">Sat</option>
                    @else
                    <option value="Sat">Sat</option>
                    @endif
                    @if(isset($data->pdp) && $data->pdp == 'Sun')
                    <option value="Sun" selected="selected">Sun</option>
                    @else
                    <option value="Sun">Sun</option>
                    @endif
                </select>
            </div>
        </div>
        <!-- <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">PDP Slot<span class="required" aria-required="true">*</span></label>
                <select class="form-control" name="pdp_slot" id="pdp_slot">
                    <option value="0">Please select...</option>
                    @foreach ($slot as $slotdata)
                    @if(isset($data->pdp_slot) && $data->pdp_slot == $slotdata->value)
                        <option value="{{ $slotdata->value }}" selected="selected">{{ $slotdata->master_lookup_name }}</option>
                    @endif
                    <option value="{{ $slotdata->value }}">{{ $slotdata->master_lookup_name }}</option>
                    @endforeach
                </select>
            </div>                            
        </div> -->
    </div>
    <div class="row">
        <div class="col-md-12">                           
            <div class="form-group">
                <label>Select Day</label>
                    <label class="mt-checkbox mt-checkbox-outline"> Monday
                        @if(isset($data->days) && in_array('Mon',$data->days))
                        <input type="checkbox" checked="true" value="Mon" name="week[]" />
                        @else
                        <input type="checkbox" value="Mon" name="week[]" />
                        @endif
                        <span></span>
                    </label>
                    <label class="mt-checkbox mt-checkbox-outline"> Tuesday
                        @if(isset($data->days) && in_array('Tue',$data->days))
                        <input type="checkbox" checked="true" value="Tue" name="week[]" />
                        @else
                        <input type="checkbox"  value="Tue" name="week[]" />
                        @endif
                        <span></span>
                    </label>
                    <label class="mt-checkbox mt-checkbox-outline"> Wednesday
                        @if(isset($data->days) && in_array('Wed',$data->days))
                        <input type="checkbox" checked="true" value="Wed" name="week[]" />
                        @else
                        <input type="checkbox"  value="Wed" name="week[]" />
                        @endif
                        <span></span>
                    </label>
                    <label class="mt-checkbox mt-checkbox-outline"> Thursday
                        @if(isset($data->days) && in_array('Thu',$data->days))
                        <input type="checkbox" checked="true" value="Thu" name="week[]" />
                        @else
                        <input type="checkbox" value="Thu" name="week[]" />
                        @endif

                        <span></span>
                    </label>
                    <label class="mt-checkbox mt-checkbox-outline"> Friday
                        @if(isset($data->days) && in_array('Fri',$data->days))
                        <input type="checkbox" checked="true" value="Fri" name="week[]" />
                        @else
                        <input type="checkbox" value="Fri" name="week[]" />
                        @endif

                        <span></span>
                    </label>
                    <label class="mt-checkbox mt-checkbox-outline"> Saturday
                        @if(isset($data->days) && in_array('Sat',$data->days))
                        <input type="checkbox" checked="true" value="Sat" name="week[]" />
                        @else
                        <input type="checkbox" value="Sat" name="week[]" />
                        @endif

                        <span></span>
                    </label>
                    <label class="mt-checkbox mt-checkbox-outline"> Sunday
                        @if(isset($data->days) && in_array('Sun',$data->days))
                        <input type="checkbox" checked="true" value="Sun" name="week[]" />
                        @else
                        <input type="checkbox" value="Sun" name="week[]" />
                        @endif

                        <span></span>
                    </label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            <a role="button" id="update_pjp_grid" class="btn green-meadow">Update</a>
        </div>
    </div>
</form>


<script type="text/javascript">
    $(document).ready(function () {

        $('.beat_exist').css('display','none');
        $("#update_pjp_grid").css('display','block');
        $('#update_pjp_grid').click(function () {
            var formValid = $('#editPJPForm').formValidation('validate');
            formValid = formValid.data('formValidation').$invalidFields.length;
            if ( formValid != 0 ) {
                return false;
            }
            else {
                var data = $('#editPJPForm').serialize();
                $.ajax({
                    url: '/warehouse/updatePJP',
                    data: data,
                    type: 'POST',
                    success: function (result)
                    {
                        var response = JSON.parse(result);
                        alert(response.message);
                        $("#pjp_table").igGrid("dataBind");
                        $('#basicvalCodeModal4').modal('hide');
                        $('#basicvalCodeModal4').on('hide.bs.modal', function () {
                            $('#editPJPForm').bootstrapValidator('resetForm', true);
                            $('#editPJPForm')[0].reset();
                        });
                    }
                });
            }
        });
        $('#editPJPForm').formValidation({
            framework: 'bootstrap',
            icon: {
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                'week[]': {
                    validators: {
                        choice: {
                            min: 1,
                            max: 7,
                            message: ' '
                        }
                    }
                },
                rm_id: {
                    validators: {
                        callback: {
                            message: 'Please select relationship manager',
                            callback: function (value, validator) {
                                return value > 0;
                            }
                        }
                    }
                }, 
                spoke_id: {
                    validators: {
                        callback: {
                            message: 'Please select Spoke',
                            callback: function (value, validator) {
                                return value > 0;
                            }
                        }
                    }
                },
                pjp_name: {
                    validators: {
                        notEmpty: {
                            message: ' '
                        }
                    }
                }
            }
        });
        $('[id="spokes_input2"]').hide();
        $('[id="add_spoke2"]').click(function () {
            $('[id="spokes_input2"]').show();
            $('[id="spokes_select2"]').hide();
            $('[id="spoke_button2"]').hide();
        });
        $('[id="close_spoke_name2"]').click(function () {
            $('[id="spokes_input2"]').hide();
            $('[id="spokes_select2"]').show();
            $('[id="spoke_button2"]').show();
        });
        $('[id="add_spoke_name2"]').click(function () {
            var spoke_name = $.trim($('[id="spoke_name_val"]').val());
            var le_wh_id = $('#le_wh_id').val();
            if(spoke_name == '' && le_wh_id > 0)
            {
                $('#spokes_input2').children().addClass('has-error');
            }else{
                $('#spokes_input2').children().removeClass('has-error');
                $.ajax({
                    url: '/warehouse/addspoke',
                    data: { 'hub_id': le_wh_id, 'spoke_name' : spoke_name },
                    type: 'POST',
                    success: function (result)
                    {
                        if(result > 0)
                        {
                            $('[id="spoke_name_val"]').val('');
                            $('[id="pincode_val"]').val('');
                            $('[id="spokes_input2"]').hide();
                            $('#spokes_select2').show();
                            $('#spoke_button2').show();                            
                            var newOption = $('<option>');
                            newOption.attr('value', result).text(spoke_name);
                            $('[id="spoke"]').append(newOption);
                            $('#spoke > [value="' + result + '"]').attr("selected", "true");
                            $('[id="spoke"]').select2('data', {id: result, text: spoke_name});
                        }else{
                            $('#spokes_input2').children().addClass('has-error');
                        }
                    }
                });
            }
        });
    });


    function editSpokeBeat(){
        
        var beat=$(".spoke_data").val();
        var pjp_name=$('#pjp_name1').val();
        var le_wh_id=$('#le_wh_id').val();

        $.ajax({
                url:'/warehouse/checkUniquePJP',
                data:{'le_wh_id': le_wh_id, 'pjp_name' : pjp_name,'spoke':beat},
                type:'POST',
                success:function(dataResult){

                    var result=JSON.parse(dataResult);
                    
                    if(result.valid == true){
                        
                        $('.beat_exist').css('display','none'); 
                        $("#update_pjp_grid").css('display','block');

                    }else {
                        
                        $('.beat_exist').css('display','block');
                        $("#update_pjp_grid").css('display','none');

                    }

                }

        });
    }
    function displayadd(){
        $('.beat_exist').css('display','none');
        $("#update_pjp_grid").css('display','block');
    }

</script>