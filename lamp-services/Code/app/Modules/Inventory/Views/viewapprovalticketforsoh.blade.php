@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
    
<span id="success_message"></span>
<span id="error_message"></span>
<div class="row">
    <div class="overlay"></div>
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">
                    SOH Transfer Approval
                </div>
                <div class="actions">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                </div>
            </div>
            <div class="portlet-body">
            <div id="old-data" class="row vertical-space table-data-arrange">
                <div class="row vertical-space">
                    <div class="col-md-12">
                    </div>
                </div>
                    <div class="row">
                    <div class="col-md-12">
                        <table class="table table-border table-hover table-advance" border="1" cellpadding="10px">
                            <thead>
                                <tr>
                                    <th>Old SKU</th>
                                    <th>New SKU</th>
                                    <th>Old Product Name</th>
                                    <th>New Product Name</th>              
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stockDetails as $value)
                                   <tr> 
                                    
                                       <td>{{ $value->old_sku }}</td>
                                       <td>{{ $value->new_sku }}</td>
                                       <td>{{ $value->old_product_name }}</td>
                                       <td>{{ $value->new_product_name }}</td>
                                   </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
                </div>
            </div>
                    </div>
                <div id="response-data" style="width: 100%;display:none; " class="table-data-arrange"></div>
                </div>
                @if(!empty($appDropdown))
    <div class="container">
        <div class="tab-content">
            <div id="home">
                <form method="POST" id ="approve_soh_details">
                    <div class="row" style ="margin-left:10px;">
                        <div class="row">
                            <!-- <div class="col-md-2">
                                <span><strong>Approval For</strong></span>
                            </div> -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control" id="nextStatusId" name ="nextStatusId">
                                        <option value=""> Please Select </option>
                                        @foreach($appDropdown as $appValue)
                                        <option value="{{$appValue['nextStatusId'] . "," . $appValue['isFinalStep']}}" data-status="{{$appValue['nextStatus']}}">{{$appValue['condition']}}</option>
                                        @endforeach
                                    </select>
                                    <input type = "hidden" id="stock_id" name = "stock_id" value = "{{$stockID}}"/>
                                    <input type ="hidden" id="currentStatus" name ="currentStatus" value = "{{$currentStatusId}}"/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <span><strong>Comment</strong></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <textarea rows="2" class="form-control" id="comments" name="comments"></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <button class="btn green-meadow saveusers" id="approval_process_data_soh">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="loader"></div>
    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@stop

@section('userscript')
<style type="text/css">

th{ white-space: nowrap;}

    table{border: 1px solid #ddd;}
    .display_div { display: none; }
    .rowmargin{ margin: 10px;}
    .btnwidth{width:250px;}
    .fa-pencil{ color:#3598DC !important;}
    .actionss{padding-left: 22px !important;}
    .sorting a{ list-style-type:none !important;text-decoration:none !important;}
    .sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
    .sorting a:active{text-decoration:none !important;}
    .active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
    .inactive{text-decoration:none !important; color:#ddd !important;}

    .loader-outer{    
        background-color: #fff;
        opacity: 0.9;
        width: 97%;
        height: 500px;
        position: absolute;
        top: 2.5em;
        left: 1.2em;
    }
    .table-data-arrange{
        width: 98.5%;
    overflow: scroll;
    margin-left: 11px;
    margin-right: 4px;
    }
    .loader {
        margin:1em auto;
        display: none;
        font-size: 10px;
        width: 1em;
        height: 1em;
        border-radius: 50%;
        position: absolute;
        text-indent: -9999em;
        -webkit-animation: load5 1.1s infinite ease;
        animation: load5 1.1s infinite ease;
        -webkit-transform: translateZ(0);
        -ms-transform: translateZ(0);
        transform: translateZ(0);
        z-index:999;
        top:16em;
        left:48em;
    }
    .btn-space
    {
        margin-right: 5px;
    }
    .vertical-space{
        margin-bottom:5px;
    }
    .overlay {
        background: #e9e9e9;
        display: none;
        position: absolute;
        top: 0;
        right: 15px;
        bottom: 0;
        left: 0;
        opacity: 0.5;
        z-index:999;
        height: 700px;
    }
    @-webkit-keyframes load5 {
        0%,
        100% {
            box-shadow: 0em -2.6em 0em 0em #8fa4ed, 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.5), -1.8em -1.8em 0 0em rgba(143,164,237, 0.7);
        }
        12.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.7), 1.8em -1.8em 0 0em #8fa4ed, 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.5);
        }
        25% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.5), 1.8em -1.8em 0 0em rgba(143,164,237, 0.7), 2.5em 0em 0 0em #8fa4ed, 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        37.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.5), 2.5em 0em 0 0em rgba(143,164,237, 0.7), 1.75em 1.75em 0 0em #8fa4ed, 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        50% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.5), 1.75em 1.75em 0 0em rgba(143,164,237, 0.7), 0em 2.5em 0 0em #8fa4ed, -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        62.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.5), 0em 2.5em 0 0em rgba(143,164,237, 0.7), -1.8em 1.8em 0 0em #8fa4ed, -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        75% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.5), -1.8em 1.8em 0 0em rgba(143,164,237, 0.7), -2.6em 0em 0 0em #8fa4ed, -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        87.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.5), -2.6em 0em 0 0em rgba(143,164,237, 0.7), -1.8em -1.8em 0 0em #8fa4ed;
        }
    }
    @keyframes load5 {
        0%,
        100% {
            box-shadow: 0em -2.6em 0em 0em #8fa4ed, 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.5), -1.8em -1.8em 0 0em rgba(143,164,237, 0.7);
        }
        12.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.7), 1.8em -1.8em 0 0em #8fa4ed, 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.5);
        }
        25% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.5), 1.8em -1.8em 0 0em rgba(143,164,237, 0.7), 2.5em 0em 0 0em #8fa4ed, 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        37.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.5), 2.5em 0em 0 0em rgba(143,164,237, 0.7), 1.75em 1.75em 0 0em #8fa4ed, 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        50% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.5), 1.75em 1.75em 0 0em rgba(143,164,237, 0.7), 0em 2.5em 0 0em #8fa4ed, -1.8em 1.8em 0 0em rgba(143,164,237, 0.2), -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        62.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.5), 0em 2.5em 0 0em rgba(143,164,237, 0.7), -1.8em 1.8em 0 0em #8fa4ed, -2.6em 0em 0 0em rgba(143,164,237, 0.2), -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        75% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.5), -1.8em 1.8em 0 0em rgba(143,164,237, 0.7), -2.6em 0em 0 0em #8fa4ed, -1.8em -1.8em 0 0em rgba(143,164,237, 0.2);
        }
        87.5% {
            box-shadow: 0em -2.6em 0em 0em rgba(143,164,237, 0.2), 1.8em -1.8em 0 0em rgba(143,164,237, 0.2), 2.5em 0em 0 0em rgba(143,164,237, 0.2), 1.75em 1.75em 0 0em rgba(143,164,237, 0.2), 0em 2.5em 0 0em rgba(143,164,237, 0.2), -1.8em 1.8em 0 0em rgba(143,164,237, 0.5), -2.6em 0em 0 0em rgba(143,164,237, 0.7), -1.8em -1.8em 0 0em #8fa4ed;
        }
    }
    }
</style>
<!--Bootstrap JavaScript & CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
@extends('layouts.footer')

<script>    

$("#approval_process_data_soh").click(function(e){
      e.preventDefault();
    var nextStatusId = $('#nextStatusId').val();
    if ( nextStatusId == '')
        {
            alert("Please select status");
            return false;
        }
    var frmData = $('#approve_soh_details').serialize();
    var token = $("#csrf-token").val();
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: '/inventory/approvalRequestForSOH',
            type: "post",
            data: frmData,
             beforeSend: function () {
                    $('[class="loader"]').show();
                    $(".overlay").show();
                    },
                    complete: function () {
                    $('[class="loader"]').hide();
                    $(".overlay").hide();
                    },
            success: function (respData)
            {
            $("#comments").val('');
            alert(respData);
             window.location.reload();
            $("#success_message").html('<div class="flash-message"><div class="alert alert-success">'+respData+' <button type="button" class="close" data-dismiss="alert"></button></div></div>');
            $(".alert-success").fadeOut(20000)

            }
    });
  });


</script>

@stop
@extends('layouts.footer')