<?php
 $hsn_code =  isset($hsn_data['ITC_HSCodes'])?$hsn_data['ITC_HSCodes']:"";
 $hsn_desc = isset($hsn_data['HSC_Desc'])?$hsn_data['HSC_Desc']:"";
 $hsn_percentage = isset($hsn_data['tax_percent'])?$hsn_data['tax_percent']:"";

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
                    Approve Tax Mapping
                </div>
                <div class="actions">
                    <input id="take" type="hidden" name="_token" value="{{csrf_token()}}">
                </div>
            </div>
            <div class="portlet-body">
                @if(!empty($rejected))
                <div class="row rowmargin">
                    <div class="alert alert-warning"><strong>This ticket has been rejected!</strong></div>
                </div>
                @else

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-border table-hover table-advance" border="1">
                            <thead>
                                <tr>
                                    <th>Product Id</th>
                                    <th>Tax Type</th>
                                    <th>Tax Class Code</th>
                                    <th>Tax %</th>
                                    <th>State</th>
                                    <th>Effective Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php  
                            for($i=0;$i<count($taxDetails);$i++){
                                    echo '<tr>';
                                        echo '<td>' .$taxDetails[$i]['product_id'].'</td>';
                                        echo '<td>' .$taxDetails[$i]['tax_class_type'].'</td>';
                                        echo '<td>' .$taxDetails[$i]['tax_class_code'].'</td>';
                                        echo '<td>' .$taxDetails[$i]['tax_percentage'].'</td>';
                                        echo '<td>' .$taxDetails[$i]['state_name'].'</td>';
                                        echo '<td>' .$taxDetails[$i]['date_start'].'</td>';
                                    echo '</tr>';
                                }
                              ?>

                            </tbody>
                        </table>
                    </div>
                </div>

                @if(!empty($approvalOptions))
                <div class="row" id="approval_div">
                    {{ Form::open(array('id' => 'approval-workflow-form'))}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="hidden" id="mapping_id" name="mapping_id" value="{{ $taxDetails[0]['parent_id'] }}"/>
                            
                            <select name="next_status" id="next_status" class="form-control">
                                <option value="">Select</option>
                                @foreach ($approvalOptions as $eachOptionKey => $eachOptionValue)
                                   
                                    <option value="{{ $eachOptionKey }}">{{ $eachOptionValue }}</option>
                                    
                                @endforeach
                            </select>
                            <input type="hidden" name="current_status" id="current_status" value="{{ $currentStatus[0] }}" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <textarea name="approval_comment" rows="3" id="approval_comment" class="form-control" required></textarea>
                    </div>
                    <div class="col-md-3">
                        <input type="submit" id="approval_submit" class="btn btn-primary" value="Submit"> 
                    </div>
                    {{ Form::close() }}
                    <div id="loader_div" class="loader-outer display_div">
                        <div class="loader" style=""></div>
                    </div>
                </div>
                @endif
                <br />
                <div class="row display_div" id="message_div">
                    <div class="col-md-12 text-center">
                        Your request was submitted, <a href="javascript:closeWindow();">Close tab!</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@stop

@section('userscript')
<style type="text/css">
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
    // $("#approval_submit").click(function () {
    //     var token = $("#take").val(),
    //         mapId = $("#mapping_id").val(),
    //         next_status = $("#next_status").val(),
    //         current_status = $("#current_status").val(),
    //         approval_comment = $("#approval_comment").val(),
    //         formData = new FormData();
    //     formData.append('mapping_id', mapId);
    //     formData.append('next_status', next_status);
    //     formData.append('current_status', current_status);
    //     formData.append('approval_comment', approval_comment);
    //     $.ajax({
    //         type: "POST",
    //         url: "/tax/taxapprovalupdate?_token=" + token,
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         dataType: "json",
    //         beforeSend: function () {
    //             $('#loader_div').show();
    //         },
    //         complete: function () {
    //             $('#loader_div').hide();
    //             $('#approval_div').hide();
    //             $('#message_div').show();
    //         },
    //         success: function (data)
    //         {}
    //     });
    // });
    
    function closeWindow() { 
        window.open('','_parent','');
        window.close();
    }



    //This validation for Tax Approval workflow 
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

        var token = $("#take").val();

        var inputdata = $("#approval-workflow-form").serialize();
        console.log("form data"+inputdata);
        // return false;
        $.ajax({
            // headers:{'X-CSRF-Token': token},
            type:"POST",
            data:inputdata,
            url:"/tax/taxapprovalupdate?_token=" + token,
            beforeSend: function () {
                $('#loader_div').show();
            },
            complete: function () {
                $('#loader_div').hide();
                $('#approval_div').hide();
                $('#message_div').show();
            },
            success:function(data)
            {
                console.log("test"+data);
                if(data == "same-effective-date-exists")
                {
                    alert("Product have other tax with same effective date !!");
                    location.reload(true);
                }
            }

        });
    

    }
});

</script>

@stop
@extends('layouts.footer')

