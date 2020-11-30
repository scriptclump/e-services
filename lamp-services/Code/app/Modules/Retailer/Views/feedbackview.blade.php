@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="portlet-body">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet light tasks-widget">
                <div class="portlet-title">
                    <div class="caption">Retailer Feedback</div>                
                </div>
                <div class="portlet-body">
                    <div role="alert" id="alertStatus"></div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="legal_entity_id">Business Legal Name </label>
                                <input type="text" class="form-control" value="{{$result['legal_entity_id']}}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="feedback_group">Feedback Group<span class="required" aria-required="true">*</span></label>
                                <input type="text" class="form-control" value="{{$result['feedback_group_type']}}">
                            </div>
                        </div>
                    </div>    
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="feedback_type">Feedback Type<span class="required" aria-required="true">*</span></label>
                                <input type="text" class="form-control" value="{{$result['feedback_type']}}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="comments">Comments<span class="required" aria-required="true">*</span></label>
                               <input type="text" class="form-control" value="{{$result['comments']}}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="created_at">Created At</label>
                               <input type="text" class="form-control" value="{{$result['created_at']}}">
                            </div>
                        </div>   
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="created_by">Created By</label>
                               <input type="text" class="form-control" value="{{$result['created_by']}}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" id="hide_img_fld">
                            <div class="form-group">
                                <label class="control-label">Media</label>
                                @if(isset($result['picture']) && ($result['picture']!=''))
                                <img class="timeline-badge-userpic" id="updatedurl" src="{{$result['picture']}}" height="300px" width="500px">
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6" id="audio_upload">
                            <div class="form-group">
                                <label class="control-label">Audio</label>
                                <audio controls>
                                  <source src="{{$result['audio']}}" type="audio/ogg">
                                </audio>
                            </div>
                        </div>
                    </div>
                        <button type="button" onclick="window.location.replace('/retailers/index');"  class="btn green-meadow" id="modalClose" data-dismiss="modal" <span style="font-size:15px;margin-left: 550px;margin-top:200px;">Cancel</span></button>
                </div>
            </div>
        </div>
    </div>
</div>
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('script')
@include('includes.ignite')
@stop
@extends('layouts.footer')

    