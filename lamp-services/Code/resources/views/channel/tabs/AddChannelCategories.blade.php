<div class="table-container">
    <div class="actions pull-right">

    <div data-toggle="buttons" class="btn-group btn-group-devided" style="margin-bottom:8px;"> <a href="#myModal1" role="button" id="upload_categories" class="btn green-meadow btn-sm" data-toggle="modal">Add</a> <a href="#myModal2" role="button" id="upload_categories" class="btn green-meadow btn-sm" data-toggle="modal">Upload Categories</a> </div>
    </div>
    <div id="myModal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">{{trans('cp_headings.cp_pop_add_cat')}}</h4>
                </div>
                <div class="modal-body">
                    <form id="update_channel_fee">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('cp_headings.cp_pop_cat')}}</label>
                                    <input type='text' id="channel_catgory" name="channel_catgory" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('cp_headings.cp_pop_parent')}}</label>
                                    <input type='text' id="parent_catgory" name="parent_catgory" class="form-control"/>
                                    <input type="hidden" id="hidden_parent_id" name="hidden_parent_id" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('cp_headings.cp_pop_catid')}}</label>
                                    <input type='text' id="category_ID" name="category_ID" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('cp_headings.cp_pop_catcharge')}}</label>
                                    <input name="channel_cat_fee" id="channel_cat_fee" type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('cp_headings.cp_pop_catchargetype')}}</label>
                                    <select id= "category_chargeType" name="category_chargeType" class="form-control">
                                        <option value="">Select...</option>

                                        @foreach($data['chargeType'] as $chargeTypes)

                                        <option  value="{{$chargeTypes->value}}">{{$chargeTypes->master_lookup_name}}</option>

                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding: 15px;">
                            <div class="col-md-12 text-center">
                                <button class="btn btn-success" id="channel_save" type="submit" aria-hidden="true">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--Edit Category Model Start -->
    <div id="edit_cat" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">{{trans('cp_headings.cp_cat')}}</h4>
                </div>
                <div class="modal-body">
                    <form id="edit_channel_cat">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('cp_headings.cp_cat')}}</label>
                                    <input type='hidden' id="edit_channel_catgory_id" name="edit_channel_catgory_id" class="form-control"/>
                                    <input type='text' id="edit_channel_catgory" name="edit_channel_catgory" readonly class="form-control disabled"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('cp_headings.cp_tab_charge')}}</label>
                                    <input name="edit_channel_cat_fee" id="edit_channel_cat_fee" type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Charge Type*</label>
                                    <select id="edit_category_chargeType" name="edit_category_chargeType" class="form-control">
                                        <option value="">Select...</option>

                                        @foreach($data['chargeType'] as $chargeTypes)

                                        <option  value="{{$chargeTypes->value}}">{{$chargeTypes->master_lookup_name}}</option>

                                        @endforeach

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding: 15px;">
                            <div class="col-md-12 text-center">
                                <button class="btn btn-success" id="edit_cat_save" type="submit" aria-hidden="true">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Edit Category Mode End-->

    <div id="myModal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">{{trans('cp_headings.cp_bulk_upload')}}</h4>
                </div>
                <div class="modal-body1">
                    <div class="row">
                        <div class="col-md-12 text-center" >
                            <select class="form-control input-medium" id="download_template_type">
                                <option value="/download/Category_Uploads/Category_Upload_Template_V1.0.0.xlsx" id="Categories">{{trans('cp_headings.categories')}}</option>
                                <option value="/download/Category_Uploads/Features_Upload_Template_V1.0.0.xlsx" id="Features">{{trans('cp_headings.feature')}}</option>
                                <option value="/download/Category_Uploads/Variants_Upload_Template_V1.0.0.xlsx" id="Variants">{{trans('cp_headings.variants')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center" > 
                            <a href="/download/Category_Uploads/Category_Upload_Template_V1.0.0.xlsx" role="button" id="download_categories" class="btn green-meadow" data-toggle="modal">Download Categories Template</a>
                            <p>{{trans('cp_headings.cp_download_template')}}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3 text-center" >
                            <form id='import_category' action="{{ URL::to('categoryImportExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                                <div class="fileUpload btn green-meadow"> <span id="up_text">{{trans('cp_headings.cp_upload_catfile')}}</span>
                                    <input type="file" id="import_file" class="upload" name="import_file"/>
                                    <input type="hidden" value='Categories' id="template_type" name="template_type"/>
                                    <input id="take" type="hidden" name="_token" value="{{csrf_token()}}"/>
                                </div>
                                <div class="loader" id="loadercats" style="display:none"><img src="/img/ajax-loader.gif" style="width:25px" class="pull-right" /></div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center" >
                            <p>{{trans('cp_headings.uploadfile')}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--igniteui table start-->
<div class="row" >
    <div class="col-md-12">
        <table id="hierarchicalGrid">
        </table>
    </div>
</div>
<!--igniteui table end--> 

<!--<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-2 col-md-8 text-center">
            <button class="btn green-meadow" type="button" href="javascript:;"  id="btn_save_edit">Back</button>
            <button class="btn green-meadow" type="button" href="javascript:;"  id="btn_save_edit">Save & Continue</button>
            <button class="btn green-meadow" type="button" href="javascript:;" id="Cancel_btn">Cancel</button>
        </div>
    </div>
</div>--> 