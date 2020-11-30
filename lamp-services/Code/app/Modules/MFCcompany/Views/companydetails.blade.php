@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="alert alert-info hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">     
        <form id="update_form_info" action="#" method="POST">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"></div>
                <div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Click here for Fullscreen"><i class="fa fa-question-circle-o"></i></a></span> </div>
            </div>
            <div class="portlet-body">
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                @include('MFCcompany::createCompany')
                <input type="hidden" id="csrf_token" name="_Token" value="{{ csrf_token() }}">
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn green-meadow btnn supp_info" id="customer_info">Update</button>
                </div>
            </div>
        </div>
        </form>
        <div class="modal fade" id="micro_view_data" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        <!-- </button> -->
                        <h4 class="modal-title" id="mfCcompanyallgrid">Edit Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">
                                   {{ Form::open(array('url' => '/mfccompany', 'id' => 'update_users_data'))}}
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">First Name</label>
                                                    <input type="text" name="f_name" id="f_name" class="form-control">
                                                    <input type="hidden" name="le_hidden1_id" id="le_hidden1_id" class="form-control">
                                                </div>
                                            </div>

                                             <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Last Name</label>
                                                    <input type="text" name="l_name" id="l_name" class="form-control">
                                                    <input type="hidden" name="users_id" id="users_id" class="form-control">
                                                </div>
                                            </div>
                                                 
                                       
                                           <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Mobile Number</label>
                                                    <input type="text" name="mobile_no" id="mobile_no" class="form-control">
                                                </div>
                                            </div>
                                        
                                             <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Email ID</label>
                                                    <input type="text" name="email_id" id="email_id" class="form-control">
                                                </div>
                                            </div>                                           
                                                                                     
                                        </div>
                                        <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">                                             
                                                <label>
                                                <input type="checkbox" id="le_check_active" value="1" name = "le_check_active">Active
                                                </label>              
                                            </div>
                                       </div>                                              
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
</div>
<div class="row" style="margin-top:10px;">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light">
            <div class="portlet-body">
                <div class="tabbable-line">                        
                    <ul class="nav nav-tabs" >
                        <li class="active"><a href="#tab_11" data-toggle="tab">Users</a></li>
                        <li><a href="#tab_22" data-toggle="tab">Documents</a></li>                       
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_11">  
                            @include('MFCcompany::users')
                        </div>
                        <div class="tab-pane" id="tab_22">
                            @include('MFCcompany::documents')
                        </div> 
                    </div>
                </div>
             
            </div>            
        </div>
    </div>
</div>
@stop
@section('style')
<style type="text/css">
    .rightAlignText{text-align: right;}
    .parent_cat{font-size:14px !important;}
    .sub_cat{ font-size:13px !important; padding-left:3px !important; background:#fff !important;}
    .prod_class{padding-left:10px !important; background:#fff !important; }


    .select2-disabled{font-weight:bold !important;}

   .rowlinht{ line-height:30px !important;}
    .thumbnail {
        padding: 0px !important;
        margin-bottom: 0px !important;
    }
    .fileinput-filename{word-wrap: break-word !important;}

    .fileinput-new .thumbnail{ width:100px !important; height:33px !important;}

    h4.block{padding:0px !important; margin:0px !important; padding-bottom:10px !important;}

    .pac-container .pac-logo{    z-index: 9999999 !important;}
    .pac-container{    z-index: 9999999 !important;}
    .pac-logo{    z-index: 9999999 !important;}
    #dvMap{height:304px !important; width:269px !important;}

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

    .radio input[type=radio]{ margin-left:0px !important;}
	.thumbnail  {
    
	height: 350px;
	width: 350px;
}
.fileinput .thumbnail > img{padding: 10px;}
.profile-sub{
	margin-top: -56px !important; width:350px; height:45px;  position: absolute;opacity: 0;z-index: 9;
}
.profile-sub-title{
	background-color: #000;
    bottom: 20px;
    color: #fff;
    left: 42px;
    line-height: 45px;
    opacity: 0.60;
    position: absolute;
    text-align: center;
    width: 330px;
    
}
.page-content{background:none !important;}
.rowbotmarg{margin-bottom:10px !important;}
.rightAlign{text-align: right;}
</style>
@stop

@section('userscript')
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />

<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>

    <script>
    $(document).ready(function () {
          $("#update_users_data_grid").igGrid({
            dataSource: '/mfccompany/getUsersList',
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false, 
            enableUTCDates: true, 
            width: "100%",
            height: "100%",
            columns: [
            {headerText: "Name", key: 'fullname', dataType: 'string', width: '30%'},
            {headerText: "Mobile No", key: 'mobile_no', dataType: 'string', width: '30%'},
            {headerText: "Email ID", key: 'email_id', dataType: 'string', width: '30%'},           
            {headerText: "Action", key:'CustomAction', dataType: 'string', width: '10%'}  
                 ],
             features: [
                 {
                    name: "Sorting",
                    type: "remote",
                    columnSettings: [
                    {columnKey: 'fullname', allowSorting: true },
                    {columnKey: 'CustomAction', allowSorting: false },
                    {columnKey: 'mobile', allowSorting: true },
                    {columnKey: 'email', allowSorting: true },
                   
                    ]
                },
                {
                    name: "Filtering",
                    type: "remote",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'CustomAction', allowFiltering: false },
                        {columnKey: 'fullname', allowFiltering: true },
                        {columnKey: 'mobile', allowFiltering: true },
                        {columnKey: 'email', allowFiltering: true },
                        {columnKey: 'role', allowFiltering: true },
                     
                    ]
                },
                { 
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 20,
                    name: 'Paging', 
                    loadTrigger: 'auto', 
                    type: 'remote' 
                }
                
            ],
            primaryKey: 'prmt_tmpl_Id',
            width: '100%',
            height: '500',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            
        });

    });
function update_users_data(user_id){ 
    var token  = $("#csrf-token").val();
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/mfccompany/getuserdata/' + user_id,
            success: function (data)
            {
                // console.log(data);
                var data = data[0];
                if(data.is_active == 1){
                $("#le_check_active").attr("checked","checked");
                $("#le_check_active").parent().addClass("checked");
                }                
                $("#le_hidden1_id").val(data.legal_entity_id);
                $("#user_id").val(data.user_id);
                $("#f_name").val(data.firstname);
                $("#l_name").val(data.lastname);
                $("#mobile_no").val(data.mobile_no);
                $("#email_id").val(data.email_id);
                           
            }
    });
    $('#micro_view_data').modal('toggle');
}

$('#update_users_data').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        f_name: {
                validators: {
                    notEmpty: {
                        message: 'Enter Name'
                    },
                    regexp: {
                        regexp: '^[a-zA-Z_ ]*$',
                                message: "Name  must be string only."
                        },
                }
            },
            l_name: {
                validators: {
                    notEmpty: {
                        message: 'Enter Name'
                    },
                    regexp: {
                        regexp: '^[a-zA-Z_ ]*$',
                                message: "Name  must be string only."
                        },
                }
            },
          email_id: {
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


       mobile_no:{
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
    }
}).on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#update_users_data').serialize();
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/mfccompany/updateUsersData',
        data: frmData,
        success: function (respData)
        {
            $('#micro_view_data').modal('toggle');
            $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">Updated Succesfully </div></div>' );
            $(".alert-success").fadeOut(20000);
            reloadGridData();           
        }
    });
});

    function reloadGridData(){

    $("#update_users_data_grid").igGrid("dataBind");
}


$("#micro_view_data").on('show.bs.modal', function () {
$('#update_legalentity_data').formValidation('resetForm', true);

    });



$("#customer_info").click(function(){
$('#LegalEntity_update_view').formValidation('resetForm', true);    
})


//update form info validations----------------------
$('#update_form_info').formValidation({
    message: 'This value is not valid',
    icon: {
        validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
        business_name: {
                validators: {
                    notEmpty: {
                        message: ' Enter  Name '
                    },
                    regexp: {
                        regexp: '^[a-zA-Z_ ]*$',
                                message: "Name  must be string only."
                        }
                }
            },
      org_address1: {
                validators: {
                    notEmpty: {
                        message: ' Enter Your Address '
                    },
                    
                }
            },
       org_pincode: {
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
        org_city: {
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

        // gstin: {
        //     validators: {
        //         regexp: {
        //           regexp:'^([0][1-9]|[1-2][0-9]|[3][0-5])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$',  
        //             message:'Enter Valid GSTIN.'
        //         }
        //     }
        // }, 
    }
}).on('success.form.fv', function(e){
    e.preventDefault();
    var frmData = $('#update_form_info').serialize();
  
    var token  = $("#csrf-token").val();
    $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: "POST",
        url: '/mfccompany/updateCustomerInfo',
        data: frmData,
        success: function (respData)
        {
             $('#flass_message').text("Updated Successfully");
             $('#update_form_info').formValidation('resetForm', false);    
             $('div.alert').show();
             $('div.alert').removeClass('hide');
             $('div.alert').not('.alert-important').delay(3000).fadeOut(350);

            
        }
    });
});
    
</script>
@stop
@extends('layouts.footer')