<?php

// echo "<pre>";print_r($bulkdetails);die;
?>
@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<span id="success_message"></span>
<span id="error_message"></span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">
                    Inventory Writeoff Approval
                </div>
                <div class="actions">
                    <input id="take" type="hidden" name="_token" value="{{csrf_token()}}">
                </div>
            </div>
            <div class="portlet-body">
                
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-border table-hover table-advance" border="1">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>MRP</th>
                                    <th>ESU</th>
<!--                                     <th>SOH Old Value</th>
                                    <th>SOH Difference</th>
                                    <th>SOH New Value</th> -->
                                    
                                    <th>DND Current Value</th> 
                                    <th>DIT Current Value</th>         
                                    <th>DND Uploaded Value</th>
                                    <th>DIT Uploaded Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bulkdetails as $value)
                                   <tr> 
                                        <td>{{ $value['product_id'] }}</td>
                                        <td>{{ $value['product_title'] }}</td>
                                        <td>{{ $value['mrp'] }}</td>
                                        <td>{{ $value['esu'] }}</td>



                                        <td>{{ $value['old_dnd_qty'] }}</td>
                                        <td>{{ $value['old_dit_qty'] }}</td>
                                        <td>{{ $value['upload_dnd_qty'] }}</td> 

                                        <td>{{ $value['upload_dit_qty'] }}</td>
                                   </tr>
                                @endforeach
                            </tbody>
                            
                        </table>
                    </div>
                </div>
                @if(!empty($approvalStatus))
                <div class="row" id="approval_div">
                    {{ Form::open(array('id' => 'approval-workflow-form'))}}
                    <div class="col-md-3">
                        <div class="form-group">
                         <select name="next_status" id="next_status" class="form-control">
                                <option value="">Select</option>
                                @foreach ($approvalStatus as $eachOptionKey => $eachOptionValue)
                                    <option value="{{ $eachOptionValue['nextStatusId'] }},{{ $eachOptionValue['isFinalStep'] }}">{{ $eachOptionValue['condition'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <textarea name="approval_comment" rows="3" id="approval_comment" class="form-control"></textarea>
                        <input type="hidden" name="current_status_id" id="current_status_id" value="{{ $curr_status_id }}">
                        <input type="hidden" name="bulk_upload_id" id="bulk_upload_id" value="{{ $bulk_upload_id }}">
                    </div>
                    <div class="col-md-3">
                        <input type="submit" id="approval_submit" class="btn btn-primary" value="Submit"> 
                    </div>

                     {{ Form::close() }}
                    <div id="loader_div" class="loader-outer display_div">
                        <div class="loader" style=""></div>
                    </div>
                </div>
                <br />
                <div class="row display_div" id="message_div">
                    <div class="col-md-12 text-center" id="after_submit">
                        Your request was submitted, <a href="javascript:closeWindow();">Close tab!</a>
                    </div>

                    <div class="col-md-12 text-center" id="after_success">
                        This request was already approved, <a href="javascript:closeWindow();">Close tab!</a>
                    </div>
                    <div class="col-md-12 text-center" id="array_success" style="background: #f9989d75;">
                        It is going to rejected state, Because calculation wise we are getting negative values.<button type="button" id="button_model"  class="btn btn-link " data-toggle="modal" data-target="#classModal">Click here</button>
                    </div>
                </div>
                @endif
              
            </div>
        </div>
    </div>
</div>
<button type="button" id="button_model" style="display: none;" class="btn btn-info btn-lg" data-toggle="modal" data-target="#classModal">Open Modal</button>
<div id="classModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="classInfo" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          ×
        </button>
        <h4 class="modal-title" id="classModalLabel">
              <b>Uploaded Inventory Sheet got rejected, Please check the below negative values.</b>
            </h4>
      </div>
      <div class="modal-body">
        <table id="classTable" class="table table-bordered">
          <thead>
             <tr>
              <th>Product Id</th>
              <th>Product Title</th>
              <th>Current SOH</th>
              <th>Uploaded SOH</th>
              <th>DIT Qty</th>
              <th>DND Qty</th>
              <th>Quarantine Qty</th>
            </tr>
          </thead>
          <tbody id="inv_table_id">
            

          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div>
</div>

@stop

@section('userscript')
<style type="text/css">

    /* Important part */
    .modal-dialog{
        overflow-y: initial !important
    }
    .modal-body{
        height: 500px;
        overflow-y: auto;
    }
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
    .loader {
        margin:1em auto;
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
        top:22em;
        left:60em;
    }
    .spinnerQueue {
        position: absolute;
        z-index: 990;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background-color: rgba(255,255,255,.6);
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
</style>
<!--Bootstrap JavaScript & CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
@extends('layouts.footer')

<script>
   /* $("#approval_submit").click(function () {
        console.log("before ajax");
        var token = $("#take").val();
        //     mapId = $("#mapping_id").val(),
        //     next_status = $("#next_status").val(),
        //     current_status = $("#current_status").val(),
        //     approval_comment = $("#approval_comment").val(),
        //     formData = new FormData();
        // formData.append('mapping_id', mapId);
        // formData.append('next_status', next_status);
        // formData.append('current_status', current_status);
        // formData.append('approval_comment', approval_comment);
        $.ajax({
            type: "POST",
            url: "/inventory/approvalworkflowdetails?_token=" + token,
            data: $("#approval-workflow-form").serialize(),
            // processData: false,
            // contentType: false,
            // dataType: "json",
            beforeSend: function () {
                $('#loader_div').show();
            },
            complete: function () {
                $('#loader_div').hide();
                $('#approval_div').hide();
                $('#message_div').show();
            },
            success: function (data)
            {}
        });
    });
   */ 
    function closeWindow() { 
        console.log("close window");
        var url = window.location.href;
        window.open(url, '_self', '');
        window.close();
    }



    
$('#approval-workflow-form').validate({
    rules: {
        next_status: {
            required: true
        },
        approval_comment: {
            required: true
        }
    },
    highlight: function (element) {
        var id_attr = "#" + $(element).attr("id") + "1";
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
        $(id_attr).removeClass('glyphicon-ok').addClass('glyphicon-remove');
    },
    unhighlight: function (element) {
        var id_attr = "#" + $(element).attr("id") + "1";
        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
        $(id_attr).removeClass('glyphicon-remove').addClass('glyphicon-ok');
    },
    errorElement: 'span',
    errorClass: 'help-block',
    errorPlacement: function (error, element) {
        if (element.length) {
            error.insertAfter(element);
        } else {
            error.insertAfter(element);
        }
    },
    submitHandler: function (form) {
        console.log("testing done");
        var token = $("#take").val();
        
          $.ajax({
            type: "POST",
            url: "/writeoffapprovalworkflowdetails?_token=" + token,
            data: $("#approval-workflow-form").serialize(),
            // processData: false,
            // contentType: false,
            // dataType: "json",
            beforeSend: function () {
            $('body').spinnerQueue({showSpeed: 'fast', hideSpeed: 'fast'}).spinnerQueue('started', 'box2Load', true);
            },
            complete: function () {
                $('#approval_div').hide();
                $('#message_div').show();
                $('body').spinnerQueue({showSpeed: 'fast', hideSpeed: 'fast'}).spinnerQueue('finished', 'box2Load', true);
            },
            success: function (data)
            {
                console.log(data);
                if(data == 1)
                {
                    console.log("im in 1 status");
                     $("#array_success").hide();
                    $("#after_submit").hide();
                    $("#after_success").show();
                }
                else if(data == 0)
                {
                    console.log("im in 0 status");
                    $("#array_success").hide();
                    $("#after_submit").show();
                    $("#after_success").hide();
                }
            }
        });
    

    }
    
});
</script>

@stop
@extends('layouts.footer')