<?php  //echo "<pre>";print_R($retailers);?>
<div class="tab-pane active" id="tab_11">
    <form id="retailersinfo" action="/retailers/update" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        <h3>"{{trans('retailers.tab.retailer_info')}}"</h3>
        <div class="row">
            <input type="hidden" value="{{$retailers->legal_entity_id}}" id="legal_entity_id" name="legalEntityId">
            <?php //print_R($retailers);exit;?>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">{{trans('retailers.tab.retailer_name')}} <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" id="retailer_name" value="@if(isset($retailers->business_legal_name)){{$retailers->business_legal_name}}@endif" name="retailer_name">
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">{{trans('retailers.form_fields.segment_type')}} <span class="required" aria-required="true">*</span></label>
                    
                    <?php //echo"<pre>"; print_R($segments); ?>
                    <select class="form-control" id="segment_type" name="business_type_id">
                        <option value="">{{trans('retailers.form_fields.select_segment_type')}}</option>    
                        
                        @foreach($segments as $segment)
                        @if($segment->value == $retailers->business_type_id)
                        <option value="{{$segment->value}}" selected="">
                            {{$segment->master_lookup_name}}</option>
                        @else
                        <option value="{{$segment->value}}">
                            {{$segment->master_lookup_name}}</option>                        
                        @endif
                        @endforeach
                    </select>
                </div>                 
            </div> 
            <div class="col-md-4">
                <div class="form-group">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">{{trans('retailers.form_fields.volume')}}<span class="required" aria-required="true">*</span></label>
                            <select class="form-control" id="volume" name="volume">
                                @if(isset($volumes))
                                    @foreach($volumes as $volume)
                                        @if($volume->value == $retailers->volume_class)
                                            <option value="{{ $volume->value }}" selected="selected">{{ $volume->master_lookup_name }}</option>
                                        @else
                                            <option value="{{ $volume->value }}">{{ $volume->master_lookup_name }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">{{trans('retailers.form_fields.shutters')}}<span class="required" aria-required="true">*</span></label>
                            <input class="form-control" type="number" name="shutters" value="@if(isset($retailers->No_of_shutters)){{$retailers->No_of_shutters}}@endif"  />
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 text-left">
                <div class="form-group">
                    <label class="control-label">{{trans('retailers.grid.mobile')}}<span class="required" aria-required="true">*</span></label>
                    <input  readonly="" type="text" class="form-control" id="retailer_name" value="@if(isset($retailers->mobile_no)){{$retailers->mobile_no}}@endif" name="retailer_name">
                </div>
            </div>
        </div>                
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">  
                            <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-left:16px !important;">
                                <div class="fileinput-preview thumbnail">  
                                    <img src="@if(isset($retailers->profile_picture)){{$retailers->profile_picture}}@endif" class="org_edit_file" height="250px" width="275px" />
                                </div>
                            </div>
                            <input id="org_file" type="file" name="file" value="{{trans('retailers.button.change')}}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h3>{{trans('retailers.form_fields.registered_address')}}</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">{{trans('retailers.grid.address1')}} <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" value="@if(isset($retailers->address1)){{$retailers->address1}}@endif"  id="org_address1" name="org_address1">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">{{trans('retailers.grid.address1')}}</label>
                    <input type="text" class="form-control" value="@if(isset($retailers->address2)){{$retailers->address2}}@endif"  id="org_address2" name="org_address2">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">{{trans('retailers.grid.pincode')}} <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control" value="@if(isset($retailers->pincode)){{$retailers->pincode}}@endif"  id="org_pincode" name="org_pincode">
                </div>
            </div>
        </div>        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">{{trans('retailers.grid.state')}} <span class="required" aria-required="true">*</span></label>
                    <select class="form-control" id="org_state" name="org_state">
                        <option value="">{{trans('retailers.form_validate.default_state')}}</option>    
                        
                        @foreach($states_data as $stateVal )
                        @if($stateVal->zone_id==$retailers->state_id)
                        <option value="{{$stateVal->zone_id}}" selected="">{{$stateVal->name}}</option>
                        @else
                        <option value="{{$stateVal->zone_id}}" >{{$stateVal->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">{{trans('retailers.grid.city')}} <span class="required" aria-required="true">*</span></label>
                    <input type="text" class="form-control"  value="@if(isset($retailers->city)){{$retailers->city}}@endif"  id="org_city" name="org_city">
                </div>
            </div>
            <?php $country = 'ee' ?>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">{{trans('retailers.grid.country')}} <span class="required" aria-required="true">*</span></label>
                    <select class="form-control" id="org_country" name="org_country">
                        <option value="">{{trans('retailers.form_validate.default_country')}}</option>
                        @if(isset($countries))
                        @foreach($countries as $country_value)
                        @if($country_value['country_id']==$retailers->country)
                        <option value="{{$country_value['country_id']}}" selected>{{$country_value['name']}}</option>
                        @else
                        <option value="{{$country_value['country_id']}}">{{$country_value['name']}}</option>
                        @endif
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <h3>{{trans('retailers.form_fields.customer_documents')}}</h3>        
        <div class="row">
            <div class="col-md-4">
                <?php //echo "<pre>";print_R($documents);die(0)?>
                @foreach($documents as $document) 
                <div class="form-group">       
                    @if(empty($document->doc_name))
                    <?php 
                    $docName = explode('/', $document->doc_url);                    
                    ?>
                    <a href="{{$document->doc_url}}" target="_blank"><?php echo end($docName);?></a>
                    @else
                    <a href="{{$document->doc_url}}" target="_blank">{{$document->doc_name}}</a>
                    @endif
                    <!--<input type="checkbox" id="approv{{$document->doc_id}}" name="approve" value="1" > Is Approve-->                  
                </div>
                @endforeach
            </div>                      
        </div>

        <hr>        
        <div class="row">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn green-meadow btnn supp_info" id="supp_info">{{trans('retailers.button.update')}}</button>
                <button type="button" id="cancelretailerinfo" class="btn green-meadow">{{trans('retailers.button.cancel')}}</button>
            </div>
        </div>
 </form>
</div>

<style>
    .alert-info {
        background-color: #00c0ef !important;
        border-color: #00c0ef !important;
        color: #fff !important;
    }
    </style>
    