@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', 'Ebutor - Lending Partners'); ?>
<div class="alert alert-info hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
          <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
            <div class="portlet-title">
                <div class="caption">Lending Partners Dashboard</div> 
                 <div class="actions"> 
           <a class="btn green-meadow" data-toggle="modal" data-target="#addMfc" href="#addMfc">Add New Lender</a> <span data-placement="top"></span> 
         </div>              
            </div>

            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="mfCcompanyallgrid"></table>
                    </div>
                </div>
            </div>           
        </div>
 <div class="modal fade" id="mfCcompany_view_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="mfCcompanyallgrid">Microfinance  Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">
                                   {{ Form::open(array('url' => 'mfccompany/mfCcompany', 'id' => 'update_mfCcompany_data'))}}
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">First Name</label>
                                                    <input type="text" name="f_name" id="f_name" class="form-control">
                                                    <input type="hidden" name="le_hidden_id" id="le_hidden_id" class="form-control">
                                                    <input type="hidden" name="user_id" id="user_id" class="form-control">
                                                </div>
                                                
                                            </div>
                                             <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Last Name</label>
                                                    <input type="text" name="l_name" id="l_name" class="form-control">
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
                                                    <label class="control-label">City</label>
                                                    <input type="text" name="city_name" id="city_name" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                           <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Mobile Number</label>
                                                    <input type="text" name="mobile_number" id="mobile_number" class="form-control">
                                                </div>
                                            </div> 
                                              <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Email</label>
                                                    <input type="text" name="email" id="email_name" class="form-control">
                                                </div>
                                            </div>                                          
                                        </div>
                                      <div class="row">
                                                                                                       
                                             <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">State</label>
                                                   <select name = "satename" id="state_id" class="form-control select2me">
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
                                                    <label class="control-label">GSTIN</label>
                                                    <input type="text" name="gstin_name" id="gstin_name" class="form-control">
                                                </div>
                                            </div> 
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Business Legal Name</label>
                                                    <input type="text" name="bu_le_name" id="bu_le_name" class="form-control">
                                                </div>
                                            </div>                                             
                                        </div>
                                <div class="form-group">
                                    <input type="checkbox" name="le_check_active" name="le_check_active"  class="form-control" value="1">
                                    Active
                               </div>
                           
                                         <div class="col-md-11 text-center">
                                                <div class="form-group">
                                                    <button type="submit" class="btn green-meadow">Update</button>
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
    @include('MFCcompany::addMfcPopup')

</div>
@stop
@section('style')
<style type="text/css">

.row voucher_table{width:103 !important;}
timeline-badge {
    height: 80px;
    padding-right: 30px;
    position: relative;
    width: 80px;
    z-index: 1111 !important;
}
.amount-right{ text-align: right; padding-right: 4px;}
.ui-autocomplete{z-index: 99999 !important; height: 250px !important; border:1px solid #efefef !important; overflow-y:scroll !important;overflow-x:hidden !important; width:300px !important; white-space: pre-wrap !important;}

.ui-iggrid-footer{ height: 30px !important; padding-top: 30px !important; padding-left: 10px !important;}
.timeline-body {
    font-weight: 600;
    margin-bottom: -9px !important;
    margin-left: 75px !important;
    margin-top: -45px !important;
}.amount {
    height : 40px;
}
.timline_style .timeline-badge-userpic {
        border-radius: 30px !important;}

        .timeline::before {
    background: #f5f6fa none repeat scroll 0 0;
    bottom: 0;
    content: "";
    display: block;
    margin-left: 54px;
    position: absolute;
    top: 0;
    width: 4px;
    top:62px !important;
}

#modal_padding{
    padding-top: 5px !important;
    font-size: 12px !important;

}
.ui-iggrid-summaries-footer-text-container{
   
    font-weight: bold;
    padding-left: 30px;
}

</style>
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/javascript"/>

@stop
@section('script')
@include('includes.validators')
@stop
@section('userscript')
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>
<script>
$(function () {
    $("#mfCcompanyallgrid").igGrid({
        dataSource: 'mfccompany/mfCcompany',
        responseDataKey: "results",
        columns: [
            // { headerText:"Logo", key:"logo", dataType: "string", width: "15%",template: "<img style=\"height:50px;\" src=\"${logo}\"/>"},
            { headerText: "Name", key: "fullname", dataType: "string", width: "15%"},
            { headerText: "Business Name", key: "business_legal_name", dataType: "string", width: "15%"},
            { headerText: "Mobile",key:"mobile_no",dataType: "string", width: "15%"},
            { headerText: "Email", key: "email_id", dataType: "string", width: "15%"},
            { headerText: "Pincode", key: "pincode", dataType: "string", width: "10%"},
            { headerText: "State", key: "StateName", dataType: "string", width: "15%"},
            { headerText: "City", key: "city", dataType: "string", width: "15%"},
            { headerText: "Address", key: "address1", dataType: "string", width: "15%"},
            { headerText: "GSTIN", key: "gstin", dataType: "string", width: "15%"},
            { headerText: "Action", key: "CustomAction", dataType: "string", width: "10%" }
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                {columnKey: 'fullname', allowSorting: true },
                {columnKey: 'business_legal_name', allowSorting: true },   
                {columnKey: 'mobile_no', allowSorting: true },                
                {columnKey: 'email_id', allowSorting: true },
                {columnKey: 'pincode', allowSorting: true },
                {columnKey: 'state_id', allowSorting: true },
                {columnKey: 'city', allowSorting: true },
                {columnKey: 'address1', allowSorting: true },
                {columnKey: 'gstin', allowSorting: true },
                {columnKey: 'CustomAction', allowSorting: false },
            ]
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                {columnKey: 'fullname', allowFiltering: true },
                {columnKey: 'business_legal_name', allowFiltering: true },   
                {columnKey: 'mobile_no', allowFiltering: true },                
                {columnKey: 'email_id', allowSorting: true },
                {columnKey: 'pinc', allowFiltering: true },
                {columnKey: 'pincode', allowFiltering: true },
                {columnKey: 'state_id', allowFiltering: true },
                {columnKey: 'city', allowFiltering: true },
                {columnKey: 'gstin', allowFiltering: true },
                {columnKey: 'address1', allowFiltering: true },
                {columnKey: 'CustomAction', allowFiltering: false },
                ]
            },
            { 
                recordCountKey: 'TotalRecordsCount', 
                pageIndexUrlKey: 'page', 
                pageSizeUrlKey: 'pageSize', 
                pageSize: 10,
                name: 'Paging', 
                loadTrigger: 'auto', 
                type: 'remote' 
            },
                            
        ],
        primaryKey: 'legal_entity_id',
        width: '100%',
        height: '400px',
        defaultColumnWidth: '100px'
    }); 

    
});

$('#update_mfCcompany_data').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        f_name: {
                validators: {
                    notEmpty: {
                        message: ' Enter First Name '
                    },
                    regexp: {
                        regexp: '^[a-zA-Z]*$',
                                message: "Name  must be string only."
                        },
                }
            },
       pincode: {
                validators: {
                notEmpty: {
                message: "Pincode is required."
                },
                        stringLength: {
                        min: 6,
                                max: 6,
                                message: "Pincode  should be 6 digit."
                        },
                        regexp: {
                        regexp: '^[0-9]*$',
                                message: "Pincode  must be digits only."
                        },
                    
                    }
                },
           gstin_name: {
            validators: {
                regexp: {
                  regexp:'^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$',  
                    message:'Enter Valid GSTIN.'
                }
            }
        },
        l_name: {
            validators: {
                notEmpty: {
                    message: 'Enter First Name'
                },
                 regexp: {
                    regexp:'^[a-zA-Z]*$',
                            message: "Name  must be string only."
                    },
            }
        },
          email_name: {
                validators: {
                    notEmpty: {
                        message: "Email is required."
                    },
                    regexp: {
                        regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                        message: "Invalid email formate."
                    }
                }
            },
        city_name: {
            validators: {
                notEmpty: {
                    message: 'Enter City Name'
                },
             regexp: {
                regexp:'^[a-zA-Z]*$',
                        message: "Name  must be string only."
                },
            }
        },
       mobile_number: {
                    validators: {
                    notEmpty: {
                    message: "Mobile is required."
                    },
                            stringLength: {
                            min: 10,
                                    max: 10,
                                    message: "'Mobile number should be 10 digit."
                            },
                            regexp: {
                            regexp: '^[0-9]*$',
                                    message: "Mobile number must be digits only."
                            },
                    
                    }
                },
        state_id: {
            validators: {
                notEmpty: {
                    message: ' Enter State Name'
                }
            }
        },

        bu_le_name: {
            validators: {
                notEmpty: {
                    message: ' Enter Business Name'
                }
            }
        },     
    }
}).on('success.form.fv', function(e){
    e.preventDefault();

    var frmData = $('#update_mfCcompany_data').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/mfCcompany/updateintotable',
        data: frmData,
        success: function (respData)
        {
             $('#flass_message').text("Successfully Updated");
             $('#update_mfCcompany_data').formValidation('resetForm', true);            
             $('div.alert').show();
             $('div.alert').removeClass('hide');
             $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
            
        }
    });
});

function updateDetailsData(legalid){   
    var token  = $("#csrf-token").val();
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/mfCcompany/editData/' + legalid,
            success: function (data)
            {
              alert(data.state_id);
                if(data.is_active == 1){
                    $("#le_check_active").attr("checked","checked");
                    $("#le_check_active").parent().addClass("checked");
                }                
                $("#le_hidden_id").val(data.legal_entity_id);
                $("#user_id").val(data.user_id);
                $("#f_name").val(data.firstname);
                $("#l_name").val(data.lastname);
                $("#city_name").val(data.city);
                $("#mobile_no").val(data.phone_no);
                $("#email_id").val(data.email);
                $("#gstin_name").val(data.gstin);
                $("#pincode").val(data.pincode);
                $("#state_id").select2('val',data.state_id);
                $("#bu_le_name").val(data.business_legal_name);              
            }
    });
    $('#mfCcompany_view_data').modal('toggle');
}


   $('#mfccompanydetails').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
         first_name: {
                validators: {
                    notEmpty: {
                        message: 'Enter First Name'
                    },
                    regexp: {
                        regexp: '^[a-zA-Z_ ]*$',
                                message: "Name  must be string only."
                        },
                }
            },
          state_id: {
            validators: {
                notEmpty: {
                        message: ' Enter State Name '
                    }
                }
             },           
            last_name: {
                validators: {
                    notEmpty: {
                        message: 'Enter Last Name'
                    },
                    regexp: {
                        regexp: '^[a-zA-Z_ ]*$',
                                message: "Name  must be string only."
                        },
                }
            },
            city: {
                validators: {
                    notEmpty: {
                        message: 'Enter City Name'
                    },
                    regexp: {
                        regexp: '^[a-zA-Z_ ]*$',
                                message: "Name  must be string only."
                        },
                }
            },

            pincode: {
                validators: {
                notEmpty: {
                message: "Pincode is required."
                },
                        stringLength: {
                        min: 6,
                                max: 6,
                                message: "Pincode  should be 6 digit."
                        },
                        regexp: {
                        regexp: '^[0-9]*$',
                                message: "Pincode  must be digits only."
                        },
                    
                    }
                }, 
             address: {
                validators: {
                    notEmpty: {
                        message: 'Enter Address'
                    },
                }
            },
            state_id: {
                validators: {
                    notEmpty: {
                        message: 'Select State'
                    },
                }
            },
          business_legal_name: {
                validators: {
                    notEmpty: {
                        message: 'Enter Business Legal Name'
                    },
                    regexp: {
                        regexp: '^[a-zA-Z_ ]*$',
                                message: "Name  must be string only."
                        },
                }
            },
          email: {
                validators: {
                    notEmpty: {
                        message: "Email is required."
                    },
                    regexp: {
                        regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                        message: "Invalid email formate."
                    }
                }
            },
       phone_number:{
                    validators: {
                    notEmpty: {
                    message: "Mobile is required."
                    },
                    stringLength: {
                    min: 10,
                            max: 10,
                            message: "'Mobile number should be 10 digit."
                    }, 

                    remote: {
                    headers: {'X-CSRF-TOKEN': $("#csrf_token").val()},
                    url: '/mfccompany/validator',
                    type: 'POST',
                    data: function (validator, $field, value) {
                        return  {
                            phone_number: value
                        };
                    },
                    delay: 1000, // Send Ajax request every 1 seconds
                    message: "{{trans('users.users_form_validate.user_mobile_exist')}}"
                },                   
              }
         },
           gstin_number: {
            validators: {
                regexp: {
                  regexp:'^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$',  
                    message:'Enter Valid GSTIN.'
                }
            }
        },
    }
}).on('success.form.fv', function(e){
    e.preventDefault();

    var frmData = $('#mfccompanydetails').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/mfccompany/addingUsers',
        data: frmData,
        success: function (respData)
        {       
            if(respData['status']==1){
                $('#flass_message').text(respData['message']);
                $("#state_id").prop('selectedIndex',0);
                $('#mfccompanydetails').formValidation('resetForm', true); 
                $('#addMfc').modal('toggle');
                $('div.alert').show();
                $('div.alert').removeClass('hide');
                $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
                reloadGridData();
            }
                        
        }
    });
});

   function reloadGridData(){

    $("#mfCcompanyallgrid").igGrid("dataBind");
}
</script>   
@stop   