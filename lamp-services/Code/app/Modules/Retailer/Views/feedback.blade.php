<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="portlet-body">
   <div class="row">
            <div class="portlet light tasks-widget">
                <div class="portlet-title">
                    <div class="actions">
                        @if(isset($AddfeedbackPermission) and $AddfeedbackPermission)
                           <a class="btn green-meadow" data-toggle="modal" data-target="#feedback1" id="addnewfeedback" href="#feedback">Add Feedback</a><span data-placement="top"></span>
                        @endif
                    </div>
                </div>
                <div class="portlet-body">
                    <div role="alert" id="alertStatus_feedback"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div style="height: 500px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                                <div class="table-responsive">
                                    <table id="feedback"></table>
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>
                <!-- Add Modal -->
                <div class="modal " id="addnewfeedbackmodal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="addModalLabel">Add Feedback</h4>
                                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="addnewfeedbackform" enctype="multipart/form-data" method="POST">
                                    <input name="_token" type="hidden" value="{{csrf_token()}}">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <input type="hidden" class="form-control" id="legal_entity_id" name="legal_entity_id">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="feedback_group">Feedback Group<span class="required" aria-required="true">*</span></label>
                                                <select class="form-control select2me" id="add_feedback_group" name="add_feedback_group" style="margin-top: 6px" placeholder="Feedback Group">
                                                    <option value = "">--Please Select--</option>
                                                     @foreach($feedbackGroup as $group)
                                                    <option value = "{{$group['value']}}">{{$group['master_lookup_name']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="feedback_type">Feedback Type<span class="required" aria-required="true">*</span></label>
                                                <select class="form-control select2me" id="add_feedback_type" name="add_feedback_type" style="margin-top: 6px" placeholder="Feedback Type">
                                                    <option value = "">--Please Select--</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="comments">Comments<span class="required" aria-required="true">*</span></label>
                                                <select class="form-control select2me" id="add_comments" name="add_comments" style="margin-top: 6px" placeholder="Comments">
                                                        <option value = "">--Please Select--</option>
                                                        @foreach($feedbackComments as $comments)
                                                        <option value = "{{$comments['value']}}">{{$comments['master_lookup_name']}}</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <div class="form-group">
                                                    <label for="comments">Retailer Comments</label>
                                                    <textarea class="form-control" id="retailer_comments"  name="retailer_comments" style="height:50px !important;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6" id="hide_img_fld">
                                            <div class="form-group">
                                                <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:10px !important;">
                                                    <label class="control-label">Media</label>
                                                    <input type="file" id="feedbackimage" name="feedbackimage">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="audio_upload">
                                            <div class="form-group">
                                                <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:10px !important;">
                                                   <label class="control-label">Audio</label>
                                                   <input type="file" id="feedbackaudio" name="feedbackaudio">
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                            </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">Close</button>
                                        <button type="submit" id="addfeedbackdata" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>              

<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .feedback{
        float: right;
        margin-left: 20px;
        margin-right: 20px;
        margin-bottom:5px;

    }
    #feedback > thead > tr > th { padding: 0px 5px 0px 5px !important; }
    #feedback > tbody > tr > td { height: 25px !important; padding: 0px 5px 0px 5px; }
</style>