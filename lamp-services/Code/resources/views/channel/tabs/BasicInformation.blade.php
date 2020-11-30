{{ Form::open(array('url' => '#','class'=>'form-horizontal' ,'name'=>'add_channel_form', 'id'=>"add_channel_form",'files' => true)) }}
<div class="form-body">
    <?php $mpinfo = isset($page_prop['cdata'][0]) ? $page_prop['cdata'][0] : array(); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label  ">{{trans('cp_headings.cp_name')}}</label>
                    <?php
                    $mpname = isset($mpinfo['mp_name']) ? $mpinfo['mp_name'] : '';
                    $mpname_disabled = isset($mpinfo['mp_name']) ? 'disabled=""' : '';
                    ?>
                    <input type="text"  value='<?php echo $mpname; ?>' id="channnel_name" name="channel_name" placeholder="Channel Name" class="form-control" <?php echo $mpname_disabled; ?>>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label ">{{trans('cp_headings.cp_type')}}</label>
                    <div class="mt-checkbox-inline">
                        <?php
                        $b2bstatus = '';
                        $b2cstatus = '';
                        $mptype = isset($mpinfo['mp_type']) ? $mpinfo['mp_type'] : '';
                        if ($mptype != '') {
                            $mptype = explode(',', $mptype);
                            if (count($mptype) > 1) {
                                $b2bstatus = 'checked';
                                $b2cstatus = 'checked';
                            } else {
                                if ($mptype[0] == 'B2B') {
                                    $b2bstatus = 'checked';
                                    $b2cstatus = '';
                                }
                                if ($mptype[0] == 'B2C') {
                                    $b2bstatus = '';
                                    $b2cstatus = 'checked';
                                }
                            }
                        }
                        ?>
                        <div class="col-md-3">
                            <label><span class="">
                                    <input type="checkbox" name="channel_type[]" id="channel_type" value="B2B" <?php echo $b2bstatus; ?>>
                                </span>&nbsp;
                                B2B</label>
                        </div>
                        <div class = "col-md-3">
                            <label><span class = "">
                                    <input type = "checkbox" name = "channel_type[]" id = "channel_type" value = "B2C"  <?php echo $b2cstatus; ?>>
                                </span>&nbsp;
                                B2C </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class = "row">
        <div class = "col-md-6">
            <div class = "col-md-12">
                <?php
                $mpdesc = isset($mpinfo['mp_description']) ? $mpinfo['mp_description'] : '';
                ?>
                <div class = "form-group">
                    <label class = "control-label ">{{trans('cp_headings.cp_desc')}}</label>
                    <textarea id = "channel_description" name = "channel_description" class = "form-control"><?php echo $mpdesc; ?></textarea>
                </div>
            </div>
        </div>
        <div class = "col-md-6">
            <div class = "col-md-12">
                <div class = "form-group">
                    <div class = "">
                        <label class = "control-label ">{{trans('cp_headings.cp_logo')}}</label>
                    </div>
                    <?php
                    $mpimg = isset($mpinfo['mp_logo']) ? $mpinfo['mp_logo'] : '';
                    $mp_enable_logo = isset($mpinfo['mp_enable_logo']) ? $mpinfo['mp_enable_logo'] : '';
                    $mp_disable_logo = isset($mpinfo['mp_enable_logo']) ? $mpinfo['mp_disable_logo'] : '';
                    if ($mpimg != '') {
                        ?>
                        <div class="input-group image-preview show_new_logo" name="edit_channel_logo" style="display: none;">
                            <input type="text" class="form-control image-preview-filename"  disabled="disabled"> <!-- don't give a name === doesn't send on POST/GET -->
                            <span class="input-group-btn">
                                <!-- image-preview-clear button -->
                                <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                                    <span class="glyphicon glyphicon-remove"></span> Clear
                                </button>
                                <!-- image-preview-input -->
                                <div class="btn btn-default image-preview-input">
                                    <span class="glyphicon glyphicon-folder-open"></span>
                                    <span class="image-preview-input-title">Browse</span>
                                    <input type="file" name="edit_channel_logo" accept="image/png, image/jpeg, image/gif" /> <!-- rename it -->
                                </div>
                            </span>
                        </div><!-- /input-group image-preview [TO HERE]--> 
                        <!--<div class ="file-upload btn btn-success show_new_logo" style="display:none;"><span>Upload Logo</span>
                            <input type="file" name="edit_channel_logo" style = " cursor: pointer; font-size: 20px; margin: 0; opacity: 0; padding: 0; position: absolute;  top: 30px; display:block; ">

                        </div>-->
                        <p class= "help-block show_new_logo" style="display:none"> {{trans('cp_headings.cp_upload_help')}}</p>

                        <div class="col-md-6" id="showlogo"> <img src='{{$mpimg}}' style="width:85px;"> 
                            <!--<img src='{{$mp_enable_logo}}' style="width:30px;"> 
                            <img src='{{$mp_disable_logo}}' style="width:30px;">--> 
                        </div>
                        <span id="up_new_logo" style="cursor: pointer" data-type="close" class="fa fa-times"></span>
                        <?php
                    } else {
                        ?>
                        <div class="input-group image-preview " >
                            <input type="text" class="form-control image-preview-filename"  disabled="disabled"> <!-- don't give a name === doesn't send on POST/GET -->
                            <span class="input-group-btn">
                                <!-- image-preview-clear button -->
                                <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                                    <span class="glyphicon glyphicon-remove"></span> Clear
                                </button>
                                <!-- image-preview-input -->
                                <div class="btn btn-default image-preview-input">
                                    <span class="glyphicon glyphicon-folder-open"></span>
                                    <span class="image-preview-input-title">Browse</span>
                                    <input type="file" name="channel_logo" accept="image/png, image/jpeg, image/gif" /> <!-- rename it -->
                                </div>
                            </span>
                        </div><!-- /input-group image-preview [TO HERE]--> 
                       <!-- <div class = "file-upload btn btn-success"><span>{{trans('cp_headings.cp_upload_logo')}}</span>
                            <input type = "file" name="channel_logo" style = " cursor: pointer; font-size: 20px; margin: 0; opacity: 0; padding: 0; position: absolute;  top: 30px; display:block; ">
                        </div>-->
                        <p class= "help-block show_new_logo"> {{trans('cp_headings.cp_upload_help')}}</p>

                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <!--/row-->
    <div class = "row">
        <div class = "col-md-6">
            <div class = "col-md-12">
                <div class = "form-group">
                    <?php $country_code = isset($mpinfo['country_code']) ? $mpinfo['country_code'] : ''; ?>
                    <label class = "control-label">{{trans('cp_headings.country')}}</label>
                    <select id = "location" name = "location" class = "form-control select2">
                        <option value = "">Select...</option>
                        <?php
                        $country_code = isset($mpinfo['country_code']) ? $mpinfo['country_code'] : '';
                        $country_status = '';
                        ?>


                        @foreach($data['location'] as $locations)

                        @if($locations->iso_code_3==$country_code)
                        {{$country_status='selected'}}
                        @else
                        {{$country_status=''}}

                        @endif

                        <option value = "{{$locations->iso_code_3}}" {{$country_status}} >{{$locations->name}}</option>



                        @endforeach



                    </select>
                </div>
            </div>
        </div>
        <div class = "col-md-6">
            <div class = "col-md-12">
                <div class = "form-group">
                    <?php
                    $mpurl = isset($mpinfo['mp_url']) ? $mpinfo['mp_url'] : '';
                  
                    ?>
                    <label class = "control-label">{{trans('cp_headings.cp_url')}}</label>
                    <input type = "text" value='<?php echo $mpurl; ?>' id= "channel_url" name = "channel_url" class = "form-control">
                </div>
            </div>
        </div>
    </div>
    <!--/row-->
    <div class = "row">
        <div class = "col-md-6">
            <div class = "col-md-12">
                <div class = "form-group">
                    <?php $mppriceurl = isset($mpinfo['price_url']) ? $mpinfo['price_url'] : ''; ?>
                    <label class = "control-label">{{trans('cp_headings.cp_priceurl')}}</label>
                    <input type = "text" value='<?php echo $mppriceurl; ?>' id = "price_url" name = "price_url" class = "form-control">
                </div>
            </div>
        </div>
        <div class = "col-md-6">
            <div class = "col-md-12">
                <div class = "form-group">
                    <?php $mptncurl = isset($mpinfo['tnc_url']) ? $mpinfo['tnc_url'] : ''; ?>
                    <label class = "control-label">{{trans('cp_headings.cp_tcurl')}}</label>
                    <input type = "text" value="<?php echo $mptncurl; ?>" id ="tnc_url" name = "tnc_url" class = "form-control">
                </div>
            </div>
        </div>
    </div>
    <!--/row-->
    <div class = "row">
        <div class = "col-md-6">
            <div class = "col-md-12">
                <?php $mpshippingurl = isset($mpinfo['shipping_url']) ? $mpinfo['shipping_url'] : ''; ?>
                <div class = "form-group">
                    <label class = "control-label  ">{{trans('cp_headings.cp_shippingurl')}}</label>
                    <input type = "text" id = "shipping_url" value="<?php echo $mptncurl; ?>"  name = "shipping_url" class = "form-control">
                </div>
            </div>
        </div>
        <div class = "col-md-6">
            <div class = "col-md-12">
                <div class = "form-group">
                    <?php
                    $issupportstatus = '';
                    $mpinfo['is_support'] = isset($mpinfo['is_support']) ? $mpinfo['is_support'] : 0;
                    if ($mpinfo['is_support'] == 1) {
                        $issupportstatus = 'checked';
                    } else {
                        $issupportstatus = '';
                    }
                    ?>
                    <label class = "control-label  ">{{trans('cp_headings.cp_issupport')}}</label>
                    <input type = "checkbox" id = "is_support" name = "is_support" <?php echo $issupportstatus; ?> class = "form-control"/>
                </div>
            </div>
        </div>
    </div>
    <!--/row--> 
</div>
{{Form::close()}} 