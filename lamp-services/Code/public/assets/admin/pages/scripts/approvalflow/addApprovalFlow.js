var statusHTML='';
var status_to_HTML='';
var conditionHTML ='';
var roleHTML = '';

function loadApprovalStatus(){

  $prntID = $('#appr_status_for').val();

  $.ajax({
      method: "GET",
      url: "/approvalworkflow/approvalstatus/"+$prntID+"/57,58",
      success:function(data)
      {
        statusHTML = '<select name="app_status[]" class="form-control input-sm">';
        status_to_HTML = '<select name="status_to[]" class="form-control input-sm">';
        conditionHTML = '<select name="status_condition[]" class="form-control input-sm">';
        var generateOption = '';
        for (var i = 0; i < data.length; i++) {
          if( data[i].mas_cat_id=='57'){
            generateOption = generateOption + '<option value="'+data[i].value+'">'+data[i].master_lookup_name+'</option>';
          }else{
            conditionHTML = conditionHTML + '<option value="'+data[i].value+'">'+data[i].master_lookup_name+'</option>'; 
          }
        } 
        statusHTML =  statusHTML + generateOption + '</select>';
        status_to_HTML =  status_to_HTML + generateOption + '</select>';
        conditionHTML = conditionHTML + '</select>';

        $('td[data-status]').html(statusHTML);
        $('td[data-condition]').html(conditionHTML);
        $('td[data-status-to-go]').html(status_to_HTML);
      }
    });
}

$(document).ready(function(e) {

  $('#frm_save_workflow').formValidation({
        message: 'This value is not valid',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            appr_status_name: {
                message: 'Approval Status Name is required',
                validators: {
                    notEmpty: {
                        message: 'Approval Status Name is required'
                    },
                    stringLength: {
                        min: 6,
                        max: 100,
                        message: ' Approval Status Name is required must be more than 6 and less than 30 characters long'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9% ]+$/,
                        message: 'Approval Status Name can only consist of alphabetical, number, dot and underscore'
                    }
                }
            },
            appr_status_for: {
                validators: {
                    notEmpty: {
                        message: 'Approval Status  For is required'
                    }
                }
            },
        }
    }).on('success.field.fv', function(e, data) {
        // Setting the value to the isFinal Checkbox so that we can get proper set value as per the loop
        $findCheckBox = 0;
        $('#sample_3').find('tr').each (function() {
            if( $(this).find('input[type=checkbox]:checked').val() ){
                $(this).find('input[type=checkbox]:checked').val($findCheckBox);
            }
            $findCheckBox++;
        });
    });


    $.ajax({
      method: "GET",
      url: "/approvalworkflow/approvalrole",
      success:function(data)
       {
        roleHTML = '<select name="role_ids[]" class="form-control input-sm">';

        var generateOption = '';
        for (var i = 0; i < data.length; i++) {
          
          generateOption = generateOption + '<option value="'+data[i].role_id+'">'+data[i].name+'</option>';   
                                              } 
          roleHTML =  roleHTML + generateOption + '</select>';

          $('td[data-role]').html(roleHTML);
        }
    });

    var prod_tr = '<tr class="gradeX odd">\
               <td><input type="text" data_qty="productQty" class="form-control input-sm" value="1" id="product_qty"></td>\
                <td data-status="approval_status" class="data-status"></td>\
                <td data-role="roles"></td>\
                <td data-condition="approval_condition"></td>\
                <td data-status-to-go="status_to_go" class="status_to_go"></td>\
                <td width="5%" data-is-final="is_final"><input type="checkbox" name="final[]"> </td>\
                <td width="5%" data-is-hub="hub_data"><input type="checkbox" name="hubdata[]"> </td>\
                <td>\
                <div style="text-center:center;" class="actionsty">\
                <a href="" class="delList"><i class="fa fa-remove"></i></a>\
                &nbsp;\
                <a href="" class="moveLeft"><i class="fa fa-plus"></i></a>\
                </div>\
                </tr>';

    $('#sample_3').on('click', '.moveLeft', function(e){
        e.preventDefault();
        var tr = $(this).closest('tr');
        var new_tr = $(prod_tr);
        var sNo = $('#sample_3').find('tbody').find('tr:not(".list-head")').length + 2;   
        new_tr.find('input[data_qty]').text(sNo);
        new_tr.find('td[data-status]').html(statusHTML);
        new_tr.find('td[data-role]').html(roleHTML);
        new_tr.find('td[data-condition]').html(conditionHTML);
        new_tr.find('td[data-status-to-go]').html(status_to_HTML);
        new_tr.find('input[name=final]').val(sNo);
        new_tr.data('html', tr.html());
        $('#sample_3').append(new_tr);
    });

    $('#sample_3').on('click', '.delList', function(e){
        e.preventDefault();

        $(this).closest('tr').remove();

        $('#sample_3').find('[data-key]').each(function(i){
        $(this).text( ++i );      
        });
    });

 })