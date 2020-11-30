@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">
                    Employee Dashboard
                </div>
                <div class="actions">
                    @if($export_by_status == '1')
                    <a href="/employee/exportselectionstatus" class="btn green-meadow" id="employee_selection_status">
                        <i class="fa fa-plus-circle"></i>
                        <span style="font-size:11px;"> Export </span>
                    </a>
                    @endif
                    @if($export_employees == '1')
                    <a href="#" data-id="#" data-toggle="modal" data-target="#export_employee" class="btn green-meadow">Export Employees</a>
                    @endif
                    

                    @if($add_employee == '1')
                    <a href="/employee/addemployee" class="btn green-meadow">
                        <i class="fa fa-plus-circle"></i>
                        <span style="font-size:11px;"> Add Employee </span>
                    </a>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    @if($initiated == '1' || $offer_created == '1' || $offer_approved == '1' || $offer_rejected == '1' || $dropped == '1')
                    <div class="col-md-1"><b>Offer</b></div>
                    @endif
                    <div class="col-md-11">
                        @if($initiated == '1')
                        <a class="inactive" id="57148_btn">Initiated <span id="57148_count"></span></a>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($offer_created == '1')
                        <a class="inactive" id="57149_btn">Created <span id="57149_count"></span></a>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($offer_approved == '1')
                        <a class="inactive" id="57150_btn">Approved <span id="57150_count"></span></a>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($offer_rejected == '1')
                        <a class="inactive" id="57161_btn">Rejected <span id="57161_count"></span></a>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($dropped == '1')
                        <a class="inactive" id="57167_btn">Dropped <span id="57167_count"></span></a>
                        @endif
                    </div>
                </div>
                <div class="row">
                    @if($on_boarding == '1' || $on_boarding_approved == '1' || $on_boarding_rejected == '1' || $it_asset_assign == '1')
                    <div class="col-md-1"><b>On-Board</b></div>
                    @endif
                    <div class="col-md-11">
                        @if($on_boarding == '1')
                        <a class="inactive" id="57151_btn">Created <span id="57151_count"></span></a>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($on_boarding_approved == '1')
                        <a class="inactive" id="57152_btn">Approved <span id="57152_count"></span></a>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($on_boarding_rejected == '1')
                        <a class="inactive" id="57168_btn">Rejected <span id="57168_count"></span></a>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($it_asset_assign == '1')
                        <a class="inactive" id="57153_btn">IT Assets Assigned <span id="57153_count"></span></a>&nbsp;&nbsp;&nbsp;
                        @endif
                        <a class="inactive" id="57155_btn">Active <span id="57155_count"></span></a>
                    </div>
                </div>
                <div class="row">
                    @if($off_boarding == '1' || $off_boarding_approved == '1' || $it_cleared == '1' || $finance_cleared == '1' || $in_active == '1')
                    <div class="col-md-1"><b>Exit</b></div>
                    @endif
                    <div class="col-md-11">
                        @if($off_boarding == '1')
                        <a class="inactive" id="57156_btn">Initiated <span id="57156_count"></span></a>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($off_boarding_approved == '1')
                        <a class="inactive" id="57157_btn">Approved <span id="57157_count"></span></a>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($it_cleared == '1')
                        <a class="inactive" id="57158_btn">IT Cleared <span id="57158_count"></span></a>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($finance_cleared == '1')
                        <a class="inactive" id="57159_btn">Finance Cleared <span id="57159_count"></span></a>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($in_active == '1')
                        <a class="inactive" id="57160_btn">In-Active <span id="57160_count"></span></a>
                        @endif
                    </div>
                </div>
                <div class="table-scrollable">
                    <table id="grid"></table>
                </div>



    <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
 
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      <img class="modal-content " id="profile_pic" src="" height="80%" >
      </div>
      </div>

  </div>
</div>


            </div>
        </div>
    </div>
</div>





<div class="modal fade" id="export_employee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">Export Employees</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box">
                                    <div class="portlet-body">
                                        {{ Form::open(array('url' => '/employee/exportemployeesdata', 'id' => 'export_employee'))}}

                                    <div class="row"> 
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><strong>Emp Status</strong><span class="required">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <select id="exp_employee"  name="exp_employee" class="form-control" >
                                                    <option value = "">--Please select--</option>
                                                    <option value = "1">Active</option>
                                                    <option value = "0">In Active</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                        <div class="row">
                                            <div class="col-md-6 text-center">
                                                <div class="form-group">
                                                    <button type="submit" class="btn green-meadow" id="download-excel_employees">Download</button>
                                                </div>
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

@stop
@section('userscript')
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css"/>
@include('includes.validators')

@extends('layouts.footer')
<style>

#profile_pic {
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

#profile_pic:hover {opacity: 0.7;}

/* The Modal (background) */


/* Modal Content (image) */
.modal-content {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
}




.modal-content {
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    border: none !important;
     }


/* Add Animation */
.modal-content, #caption {    
    -webkit-animation-name: zoom;
    -webkit-animation-duration: 0.6s;
    animation-name: zoom;
    animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
    from {-webkit-transform:scale(0)} 
    to {-webkit-transform:scale(1)}
}

@keyframes zoom {
    from {transform:scale(0)} 
    to {transform:scale(1)}
}

/* The Close Button */
.close {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
}

.close:hover,
.close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 80px){
    .modal-content {
        width: 80%;
    }
}
</style>
<script>
$(document).ready(function () {
    display("57155");
    var statusvalue=57155;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    activeTab("57155_btn");

    $("#57148_btn").click(function () {
        activeTab("57148_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57148");
        var statusvalue=57148;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });

    $("#57149_btn").click(function () {
        activeTab("57149_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57149");
        var statusvalue=57149;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });

    $("#57150_btn").click(function () {
        activeTab("57150_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57150");
        var statusvalue=57150;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });

    $("#57151_btn").click(function () {
        activeTab("57151_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57151");
        var statusvalue=57151;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });

    $("#57152_btn").click(function () {
        activeTab("57152_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57152");
        var statusvalue=57152;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });

    $("#57153_btn").click(function () {
        activeTab("57153_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57153");
        var statusvalue=57153;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });

    $("#57155_btn").click(function () {
        activeTab("57155_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57155");
        var statusvalue=57155;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });

    $("#57156_btn").click(function () {
        activeTab("57156_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57156");
        var statusvalue=57156;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });

    $("#57157_btn").click(function () {
        activeTab("57157_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57157");
        var statusvalue=57157;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });

    $("#57158_btn").click(function () {
        activeTab("57158_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57158");
        var statusvalue=57158;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });

    $("#57159_btn").click(function () {
        activeTab("57159_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57159");
        var statusvalue=57159;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });

    $("#57160_btn").click(function () {
        activeTab("57160_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57160");
        var statusvalue=1;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });

    $("#57161_btn").click(function () {
        activeTab("57161_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57161");
        var statusvalue=57161;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });

    $("#57167_btn").click(function () {
        activeTab("57167_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57167");
        var statusvalue=57167;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });

    $("#57168_btn").click(function () {
        activeTab("57168_btn");

        if ($.trim($("#grid").html()) != '') {
            $("#grid").igGrid("destroy");
        }

        display("57168");
        var statusvalue=57168;
        $("#employee_selection_status").attr("href", "/employee/exportselectionstatus/" + statusvalue);
    });
});

function activeTab(tabId) {
    if (tabId === "57148_btn") {
        $("#57148_btn").css("color", "#5b9bd1");
        $("#57149_btn").css("color", "#999");
        $("#57150_btn").css("color", "#999");
        $("#57151_btn").css("color", "#999");
        $("#57152_btn").css("color", "#999");
        $("#57153_btn").css("color", "#999");
        $("#57155_btn").css("color", "#999");
        $("#57156_btn").css("color", "#999");
        $("#57157_btn").css("color", "#999");
        $("#57158_btn").css("color", "#999");
        $("#57159_btn").css("color", "#999");
        $("#57160_btn").css("color", "#999");
        $("#57161_btn").css("color", "#999");
        $("#57167_btn").css("color", "#999");
        $("#57168_btn").css("color", "#999");
    } else if (tabId === "57149_btn") {
        $("#57148_btn").css("color", "#999");
        $("#57149_btn").css("color", "#5b9bd1");
        $("#57150_btn").css("color", "#999");
        $("#57151_btn").css("color", "#999");
        $("#57152_btn").css("color", "#999");
        $("#57153_btn").css("color", "#999");
        $("#57155_btn").css("color", "#999");
        $("#57156_btn").css("color", "#999");
        $("#57157_btn").css("color", "#999");
        $("#57158_btn").css("color", "#999");
        $("#57159_btn").css("color", "#999");
        $("#57160_btn").css("color", "#999");
        $("#57161_btn").css("color", "#999");
        $("#57167_btn").css("color", "#999");
        $("#57168_btn").css("color", "#999");
    } else if (tabId === "57150_btn") {
        $("#57148_btn").css("color", "#999");
        $("#57149_btn").css("color", "#999");
        $("#57150_btn").css("color", "#5b9bd1");
        $("#57151_btn").css("color", "#999");
        $("#57152_btn").css("color", "#999");
        $("#57153_btn").css("color", "#999");
        $("#57155_btn").css("color", "#999");
        $("#57156_btn").css("color", "#999");
        $("#57157_btn").css("color", "#999");
        $("#57158_btn").css("color", "#999");
        $("#57159_btn").css("color", "#999");
        $("#57160_btn").css("color", "#999");
        $("#57161_btn").css("color", "#999");
        $("#57167_btn").css("color", "#999");
        $("#57168_btn").css("color", "#999");
    } else if (tabId === "57151_btn") {
        $("#57148_btn").css("color", "#999");
        $("#57149_btn").css("color", "#999");
        $("#57150_btn").css("color", "#999");
        $("#57151_btn").css("color", "#5b9bd1");
        $("#57152_btn").css("color", "#999");
        $("#57153_btn").css("color", "#999");
        $("#57155_btn").css("color", "#999");
        $("#57156_btn").css("color", "#999");
        $("#57157_btn").css("color", "#999");
        $("#57158_btn").css("color", "#999");
        $("#57159_btn").css("color", "#999");
        $("#57160_btn").css("color", "#999");
        $("#57161_btn").css("color", "#999");
        $("#57167_btn").css("color", "#999");
        $("#57168_btn").css("color", "#999");
    } else if (tabId === "57152_btn") {
        $("#57148_btn").css("color", "#999");
        $("#57149_btn").css("color", "#999");
        $("#57150_btn").css("color", "#999");
        $("#57151_btn").css("color", "#999");
        $("#57152_btn").css("color", "#5b9bd1");
        $("#57153_btn").css("color", "#999");
        $("#57155_btn").css("color", "#999");
        $("#57156_btn").css("color", "#999");
        $("#57157_btn").css("color", "#999");
        $("#57158_btn").css("color", "#999");
        $("#57159_btn").css("color", "#999");
        $("#57160_btn").css("color", "#999");
        $("#57161_btn").css("color", "#999");
        $("#57167_btn").css("color", "#999");
        $("#57168_btn").css("color", "#999");
    } else if (tabId === "57153_btn") {
        $("#57148_btn").css("color", "#999");
        $("#57149_btn").css("color", "#999");
        $("#57150_btn").css("color", "#999");
        $("#57151_btn").css("color", "#999");
        $("#57152_btn").css("color", "#999");
        $("#57153_btn").css("color", "#5b9bd1");
        $("#57155_btn").css("color", "#999");
        $("#57156_btn").css("color", "#999");
        $("#57157_btn").css("color", "#999");
        $("#57158_btn").css("color", "#999");
        $("#57159_btn").css("color", "#999");
        $("#57160_btn").css("color", "#999");
        $("#57161_btn").css("color", "#999");
        $("#57167_btn").css("color", "#999");
        $("#57168_btn").css("color", "#999");
    } else if (tabId === "57155_btn") {
        $("#57148_btn").css("color", "#999");
        $("#57149_btn").css("color", "#999");
        $("#57150_btn").css("color", "#999");
        $("#57151_btn").css("color", "#999");
        $("#57152_btn").css("color", "#999");
        $("#57153_btn").css("color", "#999");
        $("#57155_btn").css("color", "#5b9bd1");
        $("#57156_btn").css("color", "#999");
        $("#57157_btn").css("color", "#999");
        $("#57158_btn").css("color", "#999");
        $("#57159_btn").css("color", "#999");
        $("#57160_btn").css("color", "#999");
        $("#57161_btn").css("color", "#999");
        $("#57167_btn").css("color", "#999");
        $("#57168_btn").css("color", "#999");
    } else if (tabId === "57156_btn") {
        $("#57148_btn").css("color", "#999");
        $("#57149_btn").css("color", "#999");
        $("#57150_btn").css("color", "#999");
        $("#57151_btn").css("color", "#999");
        $("#57152_btn").css("color", "#999");
        $("#57153_btn").css("color", "#999");
        $("#57155_btn").css("color", "#999");
        $("#57156_btn").css("color", "#5b9bd1");
        $("#57157_btn").css("color", "#999");
        $("#57158_btn").css("color", "#999");
        $("#57159_btn").css("color", "#999");
        $("#57160_btn").css("color", "#999");
        $("#57161_btn").css("color", "#999");
        $("#57167_btn").css("color", "#999");
        $("#57168_btn").css("color", "#999");
    } else if (tabId === "57157_btn") {
        $("#57148_btn").css("color", "#999");
        $("#57149_btn").css("color", "#999");
        $("#57150_btn").css("color", "#999");
        $("#57151_btn").css("color", "#999");
        $("#57152_btn").css("color", "#999");
        $("#57153_btn").css("color", "#999");
        $("#57155_btn").css("color", "#999");
        $("#57156_btn").css("color", "#999");
        $("#57157_btn").css("color", "#5b9bd1");
        $("#57158_btn").css("color", "#999");
        $("#57159_btn").css("color", "#999");
        $("#57160_btn").css("color", "#999");
        $("#57161_btn").css("color", "#999");
        $("#57167_btn").css("color", "#999");
        $("#57168_btn").css("color", "#999");
    } else if (tabId === "57158_btn") {
        $("#57148_btn").css("color", "#999");
        $("#57149_btn").css("color", "#999");
        $("#57150_btn").css("color", "#999");
        $("#57151_btn").css("color", "#999");
        $("#57152_btn").css("color", "#999");
        $("#57153_btn").css("color", "#999");
        $("#57155_btn").css("color", "#999");
        $("#57156_btn").css("color", "#999");
        $("#57157_btn").css("color", "#999");
        $("#57158_btn").css("color", "#5b9bd1");
        $("#57159_btn").css("color", "#999");
        $("#57160_btn").css("color", "#999");
        $("#57161_btn").css("color", "#999");
        $("#57167_btn").css("color", "#999");
        $("#57168_btn").css("color", "#999");
    } else if (tabId === "57159_btn") {
        $("#57148_btn").css("color", "#999");
        $("#57149_btn").css("color", "#999");
        $("#57150_btn").css("color", "#999");
        $("#57151_btn").css("color", "#999");
        $("#57152_btn").css("color", "#999");
        $("#57153_btn").css("color", "#999");
        $("#57155_btn").css("color", "#999");
        $("#57156_btn").css("color", "#999");
        $("#57157_btn").css("color", "#999");
        $("#57158_btn").css("color", "#999");
        $("#57159_btn").css("color", "#5b9bd1");
        $("#57160_btn").css("color", "#999");
        $("#57161_btn").css("color", "#999");
        $("#57167_btn").css("color", "#999");
        $("#57168_btn").css("color", "#999");
    } else if (tabId === "57160_btn") {
        $("#57148_btn").css("color", "#999");
        $("#57149_btn").css("color", "#999");
        $("#57150_btn").css("color", "#999");
        $("#57151_btn").css("color", "#999");
        $("#57152_btn").css("color", "#999");
        $("#57153_btn").css("color", "#999");
        $("#57155_btn").css("color", "#999");
        $("#57156_btn").css("color", "#999");
        $("#57157_btn").css("color", "#999");
        $("#57158_btn").css("color", "#999");
        $("#57159_btn").css("color", "#999");
        $("#57160_btn").css("color", "#5b9bd1");
        $("#57161_btn").css("color", "#999");
        $("#57167_btn").css("color", "#999");
        $("#57168_btn").css("color", "#999");
    } else if (tabId === "57161_btn") {
        $("#57148_btn").css("color", "#999");
        $("#57149_btn").css("color", "#999");
        $("#57150_btn").css("color", "#999");
        $("#57151_btn").css("color", "#999");
        $("#57152_btn").css("color", "#999");
        $("#57153_btn").css("color", "#999");
        $("#57155_btn").css("color", "#999");
        $("#57156_btn").css("color", "#999");
        $("#57157_btn").css("color", "#999");
        $("#57158_btn").css("color", "#999");
        $("#57159_btn").css("color", "#999");
        $("#57160_btn").css("color", "#999");
        $("#57161_btn").css("color", "#5b9bd1");
        $("#57167_btn").css("color", "#999");
        $("#57168_btn").css("color", "#999");
    } else if (tabId === "57167_btn") {
        $("#57148_btn").css("color", "#999");
        $("#57149_btn").css("color", "#999");
        $("#57150_btn").css("color", "#999");
        $("#57151_btn").css("color", "#999");
        $("#57152_btn").css("color", "#999");
        $("#57153_btn").css("color", "#999");
        $("#57155_btn").css("color", "#999");
        $("#57156_btn").css("color", "#999");
        $("#57157_btn").css("color", "#999");
        $("#57158_btn").css("color", "#999");
        $("#57159_btn").css("color", "#999");
        $("#57160_btn").css("color", "#999");
        $("#57161_btn").css("color", "#999");
        $("#57167_btn").css("color", "#5b9bd1");
        $("#57168_btn").css("color", "#999");
    } else if (tabId === "57168_btn") {
        $("#57148_btn").css("color", "#999");
        $("#57149_btn").css("color", "#999");
        $("#57150_btn").css("color", "#999");
        $("#57151_btn").css("color", "#999");
        $("#57152_btn").css("color", "#999");
        $("#57153_btn").css("color", "#999");
        $("#57155_btn").css("color", "#999");
        $("#57156_btn").css("color", "#999");
        $("#57157_btn").css("color", "#999");
        $("#57158_btn").css("color", "#999");
        $("#57159_btn").css("color", "#999");
        $("#57160_btn").css("color", "#999");
        $("#57161_btn").css("color", "#999");
        $("#57167_btn").css("color", "#999");
        $("#57168_btn").css("color", "#5b9bd1");
    }
}

function display(tabId) {
    statusCount();
    $("#grid").igGrid({
        primaryKey: "emp_id",
        dataSource: '/employee/employeegrid?status=' + tabId,
        columns: [
            {headerText: " ", key: "profile_picture", dataType: "string", width: "10%"},
            {headerText: "Emp Code", key: "emp_code", dataType: "string", width: "10%"},
            {headerText: "Emp Name", key: "emp_name", dataType: "string", width: "15%"},
            {headerText: "Business Unit", key: "bu_name", dataType: "string", width: "18%"},
            {headerText: "Designation", key: "designation", dataType: "string", width: "16%"},
            {headerText: "Reporting Manager", key: "reporting_manager_id", dataType: "string", width: "20%"},
            {headerText: "Email ID", key: "office_email", dataType: "string", width: "25%"},
            {headerText: "DOJ", key: "doj", dataType: "date", width: "14%",format:"dd-MMM-yyyy"},
            {headerText: "Role", key: "role_name", dataType: "string", width: "13%"},
            {headerText: "Role Code", key: "role_code", dataType: "string", width: "10%"},
            {headerText: "DOL", key: "exit_date", dataType: "date", width: "14%",format:"dd-MMM-yyyy"},
            {headerText: "Active", key: "is_active", dataType: "bool", width: "8%", formatter: isActiveFormatter},
            {headerText: "Actions", key: "actions", dataType: "string", width: "8%"}
        ],
        responseDataKey: "result",
        features: [
            {
                name: "Sorting",
                sortingDialogContainment: "window",
                columnSettings: [
                    {columnKey: "profile_picture", allowSorting: false},
                    {columnKey: "actions", allowSorting: false}
                ]
            },
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 50,
                recordCountKey: 'recordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                allowFiltering: true,
                type: "remote",
                mode: "simple",
                columnSettings: [
                    {columnKey: "profile_picture", allowFiltering: false},
//                    {columnKey: "reporting_manager_id", allowFiltering: false},
                    {columnKey: "actions", allowFiltering: false}
                ]
            }
        ],
        width: '100%',
        height: '800px',
        rendered: function (evt, ui) {
            $("#grid_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
            $("#grid_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
            $("#grid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
            $("#grid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
            $("#grid_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
            $("#grid_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
            $("#grid_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
            $("#grid_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
            $("#grid_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
            $("#grid_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
            $("#grid_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();
            $("#grid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
            $("#grid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
            $("#grid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
            $("#grid_container").find(".ui-iggrid-filtericonafter").closest("li").remove();
            $("#grid_container").find(".ui-iggrid-filtericonbefore").closest("li").remove();
            $("#grid_container").find(".ui-iggrid-filtericontoday").closest("li").remove();
            $("#grid_container").find(".ui-iggrid-filtericonyesterday").closest("li").remove();
            $("#grid_container").find(".ui-iggrid-filtericonon").closest("li").remove();
        }
    });
}

function isActiveFormatter(val) {
    if (val === true)
        return "Yes";
    return "No";
}

function statusCount() {
    $.ajax({
        url: "/employee/statuscount",
        type: "GET",
        success: function (response) {
            $("#57148_count").html("(" + response.initiated + ")");
            $("#57149_count").html("(" + response.offer_created + ")");
            $("#57150_count").html("(" + response.offer_approved + ")");
            $("#57151_count").html("(" + response.on_boarded + ")");
            $("#57152_count").html("(" + response.on_boarding_approved + ")");
            $("#57153_count").html("(" + response.it_assets_assigned + ")");
            $("#57155_count").html("(" + response.active + ")");
            $("#57156_count").html("(" + response.exit_initiated + ")");
            $("#57157_count").html("(" + response.exit_approved + ")");
            $("#57158_count").html("(" + response.it_cleared + ")");
            $("#57159_count").html("(" + response.finance_cleared + ")");
            $("#57160_count").html("(" + response.in_active + ")");
            $("#57161_count").html("(" + response.offer_rejected + ")");
            $("#57167_count").html("(" + response.dropped + ")");
            $("#57168_count").html("(" + response.on_boarding_rejected + ")");
        }
    });
}

function popupimage(src1){

    $("#profile_pic").attr("src", src1);
    $('#myModal').modal('toggle');

}



$(".modal").on('hidden.bs.modal', function () {
        $('#export_employee').val('');
});


    $('#export_employee').on('show.bs.modal', function (e) {
        $('#export_employee').val('');
    });

    $("#download-excel_employees").click(function () {
        var exp_employess = $("#exp_employee").val();
        if (exp_employess == ""){
            alert("Please Select Any Status");
            return false;
        }
    });
</script>
@stop
@extends('layouts.footer')