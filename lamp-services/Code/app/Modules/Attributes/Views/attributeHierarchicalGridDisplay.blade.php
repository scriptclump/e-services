@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message"></span>
<span id="error_message"></span>
<div id="loadingmessage" class=""></div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget" style="height: auto;">
            <div class="portlet-title">
                <div class="caption">{{trans('attributes.lables.product_template')}}</div>
                <input type="hidden" name="filtered_data_export" id="filtered_data_export">
                 <div class="actions"> 
                  <a href="javascriot:void(0)" data-toggle="modal" class="btn green-meadow" data-target="#basicvalCodeModal3">{{trans('attributes.lables.attribute_group')}} </a>           
                  <a href="javascriot:void(0)" data-toggle="modal"value class="btn green-meadow" id="add_att_set_tab" data-target="#addAttributeSet"> {{trans('attributes.lables.add_attribute_set')}}</a> 
          
                  <a href="javascriot:void(0)" data-toggle="modal" class="btn green-meadow" data-target="#addAttributes"> {{trans('attributes.lables.import_attribute')}}</a>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table id="attributeGrid"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- attribute group  pop up start-->
<div class="modal modal-scroll fade in" id="basicvalCodeModal3" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="basicvalCode">{{trans('attributes.lables.attribute_group')}}</h4>
      </div>
      <div class="modal-body">
      <form action="" class="" id="save_attribute_form" method="POST">
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <label class="control-label">{{trans('attributes.lables.attribute_name')}} <span class="required">*</span></label>
              <input type="text"  id="attribute_group_name" name="attribute_group_name" placeholder="name" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
              <label class="control-label">{{trans('attributes.lables.category_name')}} <span class="required">*</span></label>
              <span class="custom-dropdown custom-dropdown--white">
    
              <select class="form-control select2me" name="attribute_group_category_id" id="category4">
                        </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 text-center"> 
             <button type="submit" class="btn green-meadow" >Save</button>
          </div>
        </div>
        {{ Form::close() }} </div>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>

<!-- /.modal --> 
<!--ending model  -->
<!-- edit attribute model inside attribute set -->
<div class="modal fade" id="basicvalCodeModal1" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
  <div class="modal-dialog wide">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="basicvalCode">Edit Attribute</h4>
      </div>
      <div class="modal-body"> {{ Form::open(array('url' => '/product/updateattribute', 'data-url' => '/product/updateattribute/','id'=>'editAttribute')) }} 
        {{ Form::hidden('_method', 'PUT') }}
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">Attribute Set <span class="required">*</span></label>
              <input type="text" name="attribute_set_name" readonly  class="form-control attribute_set_name" value="">
              <input type="hidden" name="attribute_set_value"   class="form-control attribute_set_value" value="">
              <input type="hidden" name="manufacturer_id" id="update_manufacturer_id" value="" />
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">Attribute Type <span class="required">*</span></label>
              <select name="attribute_type" class="form-control">
                <option  value="1">Static</option>
                <option  value="2">Dynamic</option>
                <option  value="3">Binding</option>
                <option  value="4">TP</option>
                <option  value="5">QC</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">Attribute Name <span class="required">*</span></label>
              <input type="text"  id="name" name="name" value="" class="form-control" aria-describedby="basic-addon1">
              <input type="hidden" name="attribute_id" id="attributeid" value="" />
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">Attribute Code <span class="required">*</span></label>
              <input type="text"  id="attribute_code" name="attribute_code" value="" class="form-control" aria-describedby="basic-addon1">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">Input Type <span class="required">*</span></label>
              <select name="input_type" required class="form-control">
                <option  value="checkbox">Check Box</option>
                <option  value="radio">Radio</option>
                <option  value="text">Text</option>
                <option  value="textarea">Text Area</option>
                <option  value="date">Date</option>
                <option  value="datetime">Date Time</option>
                <option  value="select">Select Drop Down</option>
              </select>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">Default Value </label>
              <input type="text" id="default_value" name="default_value" value=""  class="form-control" aria-describedby="basic-addon1">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">Is Required </label>
              <select name="is_required" class="form-control">
                <option  value="1">Yes</option>
                <option  value="0">No</option>
              </select>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">Validation</label>
              <input type="text" id="validation" name="validation" value=""  class="form-control" aria-describedby="basic-addon1">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">Regexp </label>
              <input  type="text" id="regexp" name="regexp" value=""  class="form-control" aria-describedby="basic-addon1">
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">Lookup ID </label>
              <input  type="text" id="lookup_id" name="lookup_id" value=""  class="form-control" aria-describedby="basic-addon1">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 text-center">
            <div class="form-group"> {{ Form::submit('Save ', array('class' => 'btn btn-primary')) }} </div>
          </div>
        </div>
        {{ Form::close() }} </div>
      <!-- /.modal-content --> 
    </div>
    <!-- /.modal-dialog --> 
  </div>
  <!-- /.modal --> 
</div>
<!-- end edit attribute model-->
<!-- add attribute model  -->
<div class="modal fade" id="basicvalCodeModal" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
  <div class="modal-dialog wide">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="basicvalCode">Add Attribute</h4>
      </div>
      <div class="modal-body"> {{ Form::open(array('url' => 'product/saveattribute','id'=>'addAttribute')) }}
        {{ Form::hidden('_method', 'POST') }}
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">{{trans('attributes.add.attribute_set')}}<span class="required">*</span></label>
              <input type="text" name="attribute_set_name" readonly  class="form-control attribute_set_name" value="">
              <input type="hidden" name="attribute_set_value"   class="form-control attribute_set_value" value="">
              
              <!-- <select name="attribute_set_id" id="attribute_set_id_add_attribute" disabled class="form-control">
@foreach($attributeSetData as  $attributeSet)
<option value="{{ $attributeSet->attribute_set_id}}">{{ $attributeSet->attribute_set_name}}</option>
@endforeach
</select> --> 
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">{{trans('attributes.add.attribute_type')}} <span class="required"></span></label>
              <select name="attribute_type" id="attribute_type" class="form-control">
                <option  value="1">{{trans('attributes.add.static')}}</option>
                <option  value="2">{{trans('attributes.add.dynamic')}}</option>
                <option  value="3">{{trans('attributes.add.binding')}}</option>
                <option  value="4">{{trans('attributes.add.tp')}}</option>
                <option  value="5">{{trans('attributes.add.qc')}}</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">{{trans('attributes.add.attribute_name')}}<span class="required">*</span></label>
              <input type="text"  id="cname" name="name" placeholder="name" class="form-control">
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">{{trans('attributes.add.attribute_code')}}<span class="required">*</span></label>
              <input type="text"  id="attribute_code" name="attribute_code" placeholder="text" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">{{trans('attributes.add.input_type')}} <span class="required">*</span></label>
              <select name="input_type" id="input_type"  class="form-control">
                <option  value="0">{{trans('attributes.add.pls_select')}} ..</option>
                <option  value="checkbox">{{trans('attributes.add.check_box')}}</option>
                <option  value="radio">{{trans('attributes.add.radio')}}</option>
                <option  value="text">{{trans('attributes.add.text')}}</option>
                <option  value="textarea">{{trans('attributes.add.text_area')}}</option>
                <option  value="date">{{trans('attributes.add.date')}}</option>
                <option  value="datetime">{{trans('attributes.add.date_time')}}</option>
                <option  value="select">{{trans('attributes.add.drop_down')}}</option>
              </select>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">{{trans('attributes.add.default_value')}}</label>
              <input type="text" id="default_value" name="default_value" placeholder="default_value" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">{{trans('attributes.add.is_required')}}</label>
              <select name="is_required" class="form-control">
                <option  value="1">{{trans('attributes.add.yes')}}</option>
                <option  value="0">{{trans('attributes.add.no')}}</option>
              </select>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">{{trans('attributes.add.validation')}} </label>
              <input type="text" id="validation" name="validation" placeholder="validation" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">{{trans('attributes.add.regexp')}} </label>
              <input type="text" id="regexp" name="regexp" placeholder="regexp" class="form-control">
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label class="control-label">{{trans('attributes.add.lookup_id')}}</label>
              <input type="text" id="lookup_id" name="lookup_id" placeholder="lookup_id" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12 text-center">
            <div class="form-group"> {{ Form::submit('Submit', array('class' => 'btn btn-primary')) }} </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  {{ Form::close() }} </div>
  <!-- end attribute model -->

  <!-- edit attribute set modle -->
  <!-- edit attribute model start -->
<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
<div class="modal modal-scroll fade" id="editAttributeSet" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close"  id="edit_att_set_close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="basicvalCode">Edit Attribute Set</h4>
      </div>
      <div class="modal-body">
        <div class="tabbable-line">
          <ul class="nav nav-tabs ">
            <li class="active"><a href="#tab_16_1" data-toggle="tab" aria-expanded="false">{{trans('attributes.lables.attribute_set')}} </a></li>
            <li class="" ><a href="#tab_16_2" data-toggle="tab" id="edit_next_tab" aria-expanded="true"> {{trans('attributes.lables.attribute_config')}} </a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_16_1"> 
             <form action="" class="" id="editAttributeset_form" method="POST">
                 <input type="hidden" name="_token" id="csrf-token1" value="{{ Session::token() }}" />
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">{{trans('attributes.lables.attribute_set_name')}}  <span class="required">*</span></label>
                    <input type="text"  id="edit_attribute_name" name="attribute_set_name" placeholder="name" class="form-control">
                    <input type="hidden" name="attribute_set_id" id="attribute_set_id" value="" />
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">{{trans('attributes.lables.category_name')}} <span class="required">*</span></label>
                   
                     <select class="form-control select2me" name="edit_category_id" id="category3">
                        </select>
                  </div>
                </div>
              </div>
              <div id="fieldChooser" tabIndex="1">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>{{trans('attributes.lables.select')}}</label>
                      <a href="#" data-toggle="modal" data-target="#wizardCodeModal" data-placement="right" title="Add New Attribute!"></a>
                       <input type="text" class="form-control form-control-solid search" placeholder="search..." id="search_edit"
                             style="width:100%;margin-bottom: -15px !important;position: ">
                        <br/> 
                      <div id="selectbox">
                        <input type="hidden" name="formattributes" id="formattributes1" value="0" />
                        <div id="Selectattribute1" name="attributes"></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="exampleInputEmail" >{{trans('attributes.lables.selected')}}</label>
                      <div id="attribute_id1" name="attribute_id[]"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12 text-center"> &nbsp; </div>
              </div>
              <div class="row">
                <div class="col-sm-12 text-center">
                   <button type="submit" class="btn green-meadow " >Save and Continue</button>
                 </div>
              </div>
              {{ Form::close() }} </div>
            <div class="tab-pane" id="tab_16_2">
              <div class="scroller" style=" height:400px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#000">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Attribute Name</th>
                        <th>Group Name</th>
                        <th>1st Variant</th>
                        <th>2nd Variant</th>
                        <th>3rd Variant</th>
                        <th>Is Searchable</th>
                        <th>Is Filterable</th>
                      </tr>
                    </thead>
                    <tbody id="editAttributSetId">
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="row" style="margin-top:20px;">
                <div class="col-md-12 text-center">
                  <button type="button" class="btn btn-info" id="save_att_close_btun" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>

  <!-- end edit attribute set model -->
<!-- create attribute set model -->
<div class="modal fade modal-scroll" id="addAttributeSet" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id="add_att_set_close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="basicvalCode">Add Attribute Set</h4>
      </div>
      <div class="modal-body">
        <div class="tabbable-line">
          <ul class="nav nav-tabs ">
            <li class="active"><a href="#tab_15_1" data-toggle="tab" aria-expanded="false">Attribute Set </a></li>
            <li class=""><a href="#tab_15_2" id="next_tab" data-toggle="tab" aria-expanded="true"> Attributes Configuration </a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_15_1"> 
              <form action="" class="" id="save_attribute_set_form" method="POST">
                 <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Attribute Set Name <span class="required">*</span></label>
                    <input type="text"  id="name" name="add_attribute_set_name" placeholder="name" class="form-control">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="control-label">Category Name <span class="required">*</span></label>
                    <span class="custom-dropdown custom-dropdown--white">
                    <select name="attribute_set_category_id" id="category2" class="form-control select2me" >
                    </select>
                    </span>
                  </div>
                </div>
              </div>
              <div id="fieldChooser" tabIndex="1">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label>Select Attributes</label>
                      <a href="#" data-toggle="modal" data-target="#wizardCodeModal" data-placement="right" title="Add New Attribute!"></a>
                     
                     <input type="text" class="form-control form-control-solid search" placeholder="search..." id="search_add"
                             style="width:100%;margin-bottom: -15px !important;position: ">
                        <br/> 

                      <div id="selectbox" >
                        <div id="Selectattribute"></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="exampleInputEmail" >Selected Attributes</label>
                      <div id="attribute_id" name="attribute_id[]"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">&nbsp;</div>
              </div>
              <div class="row">
                <div class="col-sm-12 text-center"> 
                  <button type="submit" class="btn green-meadow " >Save and Continue</button>
                 
                 </div>
              </div>
              </form> </div>
            <div class="tab-pane" id="tab_15_2">
              <div class="scroller" style=" height:440px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Attribute Name</th>
                        <th>Group Name</th>
                        <th>1st Variant</th>
                        <th>2nd Variant</th>
                        <th>3rd Variant</th>
                        <th>Is Searchable</th>
                        <th>Is Filterable</th>
                      </tr>
                    </thead>
                    <tbody id="att_set_model_table">
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="row" style="margin-top:20px;">
                <div class="col-md-12 text-center">
                  <button type="button" class="btn btn-info" id="edit_att_close_btn" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>

<!-- /. end attribute set modal -->
<!-- Add Attributes from excel Modal -->
<div class="modal modal-scroll fade bs-modal-sm" id="addAttributes" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content" style="height:320px;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="basicvalCode">{{trans('attributes.lables.attribute_excel')}}</h4>
      </div>
      <div class="modal-body"> {{ Form::open(array('url' => '/product/addAttributesFromExcel', 'id' => 'add_attributes_form_excel', 'files'=>'true' )) }}
        {{ Form::hidden('_method','POST') }}
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">{{trans('attributes.lables.select_attribute_set')}} <span class="required">*</span></label>
              <span class="custom-dropdown custom-dropdown--white">
    
              <select class="form-control select2me" name="attribute_sets" id="attributeset_id" parsley-trigger="change" parsley-required="true" parsley-error-container="#selectbox">
                <option value="">--{{trans('attributes.lables.select_attribute_set')}} --</option>
                
@foreach($attribute_sets as $key => $value)

                <option value="{{ $value->attribute_set_id }}">{{ $value->attribute_set_name }}</option>
                
@endforeach

              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="fileUpload btn green-meadow up-down"> <span id="up_text">{{trans('attributes.lables.upload_attribute')}} </span>
              <input type="file" class="form-control upload" name="import_file" id="attributes_fileupload"/>
            </div>
            <span class="loader" id="attloader" style="display:none;"><img src="/img/ajax-loader.gif" style="width:25px"/></span>{{ Form::close() }} </div>
        </div>
        <div class="row">
          <div class="col-md-12"> <a id="download_csv_link" href="/download/Import_Attributes_List.xlsx" class="btn green-meadow up-down"><i class="icon-download-alt"> </i> {{trans('attributes.lables.download')}} </a> </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>
<!-- /.modal --> 


@stop


@section('userscript')
<style type="text/css">
.btn-info {
    color: #fff;
    background-color: #5c86a9;
    border-color: #72b8f2;
}
    .slider-container{margin-top:15px !important;}
    .bootstrap-switch-handle-on {
            color:#fff !important;
        background: #26C281 !important;
    }
    .bootstrap-switch-handle-off {
            color:#fff !important;
        background: #D91E18 !important;
    }
    
    .parent_child_0{
        padding-left: 30px !important;
    }
    
    .parent_child_1{
        padding-left: 45px !important;
    }
    
    .parent_child_2{
        padding-left: 60px !important;
    }
    
    .parent_child_3{
        padding-left: 75px !important;
    }
    
    .parent_child_4{
        padding-left: 90px !important;
    }

    .actionss{padding-left: 22px !important;}
    .sorting a{ list-style-type:none !important;text-decoration:none !important;}
    .sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
    .sorting a:active{text-decoration:none !important;}
    .active{text-decoration:none !important;  font-weight:bold!important;}
    .inactive{text-decoration:none !important; color:#ddd !important;}

    #loadingmessage{ z-index: 9999999999 !important; position: relative; top: 50% !important; left: 50% !important;}

    /*/ Absolute Center Spinner /*/
    .loading {
        position: fixed;
        z-index: 999;
        height: 2em;
        width: 2em;
        overflow: show;
        margin: auto;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    /*/ Transparent Overlay /*/
    .loading:before {
        content: '';
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.3);
    }

    /*/ :not(:required) hides these rules from IE9 and below /*/
    .loading:not(:required) {
        /*/ hide "loading..." text /*/
        font: 0/0 a;
        color: transparent;
        text-shadow: none;
        background-color: transparent;
        border: 0;
    }

    .loading:not(:required):after {
        content: '';
        display: block;
        font-size: 10px;
        width: 1em;
        height: 1em;
        margin-top: -0.5em;
        -webkit-animation: spinner 1500ms infinite linear;
        -moz-animation: spinner 1500ms infinite linear;
        -ms-animation: spinner 1500ms infinite linear;
        -o-animation: spinner 1500ms infinite linear;
        animation: spinner 1500ms infinite linear;
        border-radius: 0.5em;
        -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
        box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
    }

    /*/ Animation /*/

    @-webkit-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @-moz-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @-o-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    label {
    padding-bottom: 0px !important;
}

.select2-disabled{font-weight:bold !important;}

.fc-field {
    cursor:pointer !important;
}

.up-down{width:264px !important; margin:3px !important;}
.portlet.light > .portlet-title > .tools {
padding:0px !important;
}
.portlet > .portlet-title > .tools > a {
    height: auto !important;
}
.has-feedback label~.form-control-feedback {
    top: 40px !important;
    right:10px !important;
}

i.fa.fa-plus {
    font-size: 14px !important; color:#3598dc !important;
}
i.fa.fa-pencil {
    font-size: 14px !important; color:#3598dc !important;
}
i.fa.fa-trash-o {
    font-size: 14px !important; color:#3598dc !important;
}
#attribute_id{height: 338px;}

.slimScrollBar{width:11px !important; border-radius:0px !important;}
</style>
<!--Sumoselect CSS Files-->
<!-- <link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" /> -->

<!--Range picker CSS Files-->
<!-- <link href="{{ URL::asset('assets/global/plugins/range/jquery.range.css') }}" rel="stylesheet" type="text/css" /> -->
<!--Bootstrap dataepicker CSS Files-->
<!-- <script src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/javascript"></script> -->
<!--Ignite UI Required Combined CSS Files-->
{{HTML::style('jqwidgets/styles/jqx.base.css')}}
{{HTML::style('css/dragdrop/jquery-ui.css')}}
{{HTML::style('css/dragdrop/style.css')}}
{{HTML::style('css/switch-custom.css')}}
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />

@include('includes.validators')

<script src="{{ URL::asset('assets/admin/pages/scripts/attributes/form-wizard-attribute-configuration.js') }}" type="text/javascript"></script>


<script src="/js/helper.js"></script> 
<script src="/js/bootstrapValidator.js"></script> 

{{HTML::script('js/plugins/dragdrop/jquery-ui.js')}}
{{HTML::script('jqwidgets/demos.js')}} 
{{HTML::script('js/plugins/dragdrop/fieldChooser.js')}} 

<!--Ignite UI Required Combined JavaScript Files--> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<!--Range picker JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/range/jquery.range.js') }}" type="text/javascript"></script>
<!--Bootstrap dataepicker JavaScript Files-->


<!-- select2me option drop down like category -->
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/getcategorylist.js') }}" type="text/javascript"></script>

@extends('layouts.footer')
<script type="text/javascript">
//IgGrid

$(function () {
    FormWizard.init();
    attributes_grid();
});
  function attributes_grid(){
    $('#attributeGrid').igHierarchicalGrid({
      dataSource: '/product/getAttributeSets',
      dataSourceType: "json",
      responseDataKey: 'results',
      autoGenerateColumns: false,
      autoGenerateLayouts: false,
      // mergeUnboundColumns: false,      
      generateCompactJSONResponse: false,
      // enableUTCDates: true,
      columns: [
        {headerText: "Attribute Set ID", key: "attribute_set_id", dataType: "number", width: "15%", hidden: 'true'},
        {headerText: "Attribute Set Name", key: "attribute_set_name", dataType: "string", width: "40%"},
        {headerText: "Category Name", key: "cat_name", dataType: "string", width: "20%"},
        {headerText: "Created By", key: "business_legal_name", dataType: "string", width: "30%"},
        {headerText: "Action", key: "actions", dataType: "string", width: "10%"}
      ],
      columnLayouts: [{
          dataSource: '/product/getAttributesDetails',
          dataSourceType: "json",
          responseDataKey: 'resultData',
          autoGenerateColumns: false,
          autoGenerateLayouts: false,
          // mergeUnboundColumns: false,
          generateCompactJSONResponse: false,
          // enableUTCDates: true,
          columns: [
              {headerText: "Attribute ID", key: "id", dataType: "number", width: "20%", hidden: 'true'},
              {headerText: "Group Name", key: "name", dataType: "string", width: "20%"},
              {headerText: "Attribute Name", key: "attribute_code", dataType: "string", width: "20%"},
              {headerText: "1st Variant", key: "is_varient", dataType: "string", width: "10%"},
              {headerText: "2st Variant", key: "is_secondary_varient", dataType: "string", width: "10%"},
              {headerText: "3st Variant", key: "is_third_varient", dataType: "string", width: "10%"},
              {headerText: "Is Filterable", key: "is_filterable", dataType: "string", width: "10%"},
              {headerText: "Is Searchable", key: "is_searchable", dataType: "string", width: "10%"},
              {headerText: "Actions", key: "actions", dataType: "string", width: "10%"}
          ],
          features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                  {columnKey: 'actions', allowSorting: false}
                ],
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                  {columnKey: 'is_varient', allowFiltering: false},
                  {columnKey: 'is_secondary_varient', allowFiltering: false},
                  {columnKey: 'is_third_varient', allowFiltering: false},
                  {columnKey: 'is_filterable', allowFiltering: false},
                  {columnKey: 'is_searchable', allowFiltering: false},
                  {columnKey: 'actions', allowFiltering: false}
                ],
            },
        {
            name: 'Paging',
            type: 'remote',
            pageSize: 10,
            recordCountKey: 'TotalRecordsCount',
            pageIndexUrlKey: "page",
            pageSizeUrlKey: "pageSize"
        }
      ],
          primaryKey: 'id',
          width: '100%',
          height: '500px',
          localSchemaTransform: false,
          rendered: function (evt, ui) {
            $("#attributeGrid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
            $("#attributeGrid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
            $("#attributeGrid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
            $("#attributeGrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
            $("#attributeGrid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
            $("#attributeGrid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();            
        }
      }],
      features: [
        {
            name: "Sorting",
            type: "remote",
            columnSettings: [
              {columnKey: 'actions', allowSorting: false}
            ],
        },
        {
            name: "Filtering",
            type: "remote",
            mode: "simple",
            filterDialogContainment: "window",
            columnSettings: [
              {columnKey: 'actions', allowFiltering: false}
            ],
        },
        {
            recordCountKey: 'TotalRecordsCount',
            chunkIndexUrlKey: 'page',
            chunkSizeUrlKey: 'pageSize',
            chunkSize: 10,
            name: 'AppendRowsOnDemand',
            loadTrigger: 'auto',
            type: 'remote'
        }
      ],
      primaryKey: 'attribute_set_id',
      width: '100%',
      height: '610px',
      initialDataBindDepth: 0,
      localSchemaTransform: false,
      rendered: function (evt, ui) {
        $("#attributeGrid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
        $("#attributeGrid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#attributeGrid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
        $("#attributeGrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
        $("#attributeGrid_container").find(".ui-iggrid-filtericonequals").closest("li").remove();
        $("#attributeGrid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();            
    }
  });
  }


//End --Hierarchial Grid
</script>
<script type="text/javascript">
    $("#save_att_close_btun").click(function(){
        attributes_grid();
    });
    $("#edit_att_set_close").click(function()
      {
        attributes_grid();
      });
    $("#add_att_set_close").click(function()
      {
          attributes_grid();
      });
    $("#edit_att_close_btn").click(function()
      {
         attributes_grid();
      });
    $(document).ready(function () {
      /*$(".close").click(function()
        {
          attributes_grid();
        });*/
        var $sourceFields = $("#Selectattribute1");
        var $destinationFields = $("#attribute_id1");
        var $chooser = $("#fieldChooser").fieldChooser(Selectattribute1, attribute_id1);
    });
    $(document).ready(function () {
        var $sourceFields = $("#Selectattribute");
        var $destinationFields = $("#attribute_id");
        var $chooser = $("#fieldChooser").fieldChooser(Selectattribute, attribute_id);
    });
    $(document).ready(function ()
    {
        $('#main_manufacturer_id').trigger('change');
        makePopupAjax($('#basicvalCodeModal'));
        makePopupEditAjax($('#basicvalCodeModal1'), 'attribute_id');
        makePopupAttributeAjax($('#basicvalCodeModal2'), 'attribute_id');
        makePopupAjax($('#basicvalCodeModal3'));
        //makePopupEditAjax($('#basicvalCodeModal4'), 'attribute_group_id');
       // makePopupEditAjax($('#editAttributeSet'), 'attribute_set_id');
        
        
        var manufacturerId = $('#brand_name').val();
                  
    });

   $(document).ready(function () {
     //ajaxCall();   
        //var manf_name = document.getElementById("update_manufacturer_name").value;
        var manf_name = $('#brand_name').text();

        var manufacturer_id = $('#brand_name').val();
        console.log(manufacturer_id);
        console.log(manufacturer_id,manf_name);
        
        $('input[type=checkbox]').on('change', function(){
            var x = $(this).prop('checked');
            
            if (x) {
                console.log(x);
                $('input#update_manufacturer_name').val("");
                $('input#update_manufacturer_id').val(0);
            }
            else {
                ///console.log(manf_name);
                console.log(manufacturer_id);
                $('[id="update_manufacturer_name"]').val($('#brand_name option:selected').text());
                $('input#update_manufacturer_id').val(manufacturer_id);
            }
        });


    $('#addAttribute [name="name"]').keyup(function () {
        //console.log('Hi');
        $('#addAttribute [name="attribute_code"]').val($('#addAttribute [name="name"]').val().replace(/\s+/g, '_').toLowerCase());
        $('[name="attribute_code"]').change();
    });
    $('#editAttribute [name="name"]').keyup(function () {
        //console.log('Hi');
       // var manufacturerId = $('#brand_name').val();
        
        $('#editAttribute [name="attribute_code"]').val($('#editAttribute [name="name"]').val().replace(/\s+/g, '_').toLowerCase());
        $('#editAttribute [name="attribute_code"]').change();
    });

  
//validator
    
        token  = $("#csrf-token").val(); 
        $('#editAttribute').bootstrapValidator({
//        live: 'disabled',
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                attribute_set_id: {
                    validators: {
                        notEmpty: {
                            message: 'Please select Attribute Set.'
                        }
                    }
                },
                name: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('attributes.validate.attribute_name')}}"
                        },
                        remote: {
                            message: "{{trans('attributes.validate.attribute_name')}}",
                             headers: {'X-CSRF-TOKEN': token},
                            url: '/product/checkAttributeAvailability',
                            data: function (validator, $field, value) {
                                return {
                                    'attribute_id': validator.getFieldElements('attribute_id').val(),
                                    /*'attribute_code': validator.getFieldElements('attribute_code').val(),*/
                                };
                            },
                            delay: 7000     // Send Ajax request every 2 seconds
                        },
                    }
                },
                attribute_code: {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: 'Attribute Code is required'
                        },
                        regexp: {
                            regexp: '^[a-zA-Z0-9_]+$',
                            message: 'Please enter only alpha-numeric and underscore'
                        },
                        remote: {
                            message: 'Attribute Exists with this code.Please enter a new code',
                            url: '/product/checkAttrAvailability',
                            type: 'GET',
                            data: function (validator, $field, value) {
                                return {
                                    'attribute_code': validator.getFieldElements('attribute_code').val(),
                                    'attribute_id': validator.getFieldElements('attribute_id').val(),
                                };
                            },
                            delay: 7000     // Send Ajax request every 2 seconds
                        },
                    }
                },
                input_type: {
                    validators: {
                        notEmpty: {
                            message: 'Input Type is required'
                        }
                    }
                }
            }
        }).on('success.form.bv', function (event) {
            var manufacturerId = $('#brand_name').val();
            
            event.preventDefault();
            ajaxCallPopup($('#editAttribute'));
              attributes_grid();
            return false;
        }).validate({
            submitHandler: function (form) {
                return false;
            }
        });
        $('#basicvalCodeModal1').on('hide.bs.modal', function () {
            console.log('resetForm');
            $('#editAttribute').data('bootstrapValidator').resetForm();
            $('#editAttribute')[0].reset();
        });
        token  = $("#csrf-token").val(); 
        $('#addAttribute').bootstrapValidator({
//        live: 'disabled',
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                attribute_set_id: {
                    validators: {
                        notEmpty: {
                            message: "{{trans('attributes.validate.select_attribute')}}"
                        }
                    }
                },
                attribute_code: {
                    trigger: 'change keyup',
                    validators: {
                        notEmpty: {
                            message: "{{trans('attributes.validate.attribute_code')}}"
                        },
                        regexp: {
                            regexp: '^[a-zA-Z0-9_]+$',
                            message: "{{trans('attributes.validate.alpha_numeric')}}"
                        },
                        remote: {
                            message: "{{trans('attributes.validate.alpha_numeric')}}",
                             headers: {'X-CSRF-TOKEN': token},
                            url: '/product/checkAttrAvailability',
                            type: 'GET',
                            data: function (validator, $field, value) {
                                return {
                                    'attribute_code': validator.getFieldElements('attribute_code').val(),
                                };
                            },
                            delay: 2000     // Send Ajax request every 2 seconds
                        }

                    }
                },
                name: {
                    validators: {
                        notEmpty: {
                            message: 'Attribute Name is Required'
                        },
                        remote: {
                            message: 'Name already exists.Please enter a new name',
                             headers: {'X-CSRF-TOKEN': token},
                            url: '/product/checkAttributeAvailability',
                            type: 'GET',
                            data: function (validator, $field, value) {
                                return {
                                   
                                };
                            },
                            delay: 2000     // Send Ajax request every 2 seconds
                        },
                    }/*,onSuccess: function(e, data) {
                     $('#addAttribute').data('bootstrapValidator').validateField('attribute_code');
                     }, */
                },
                input_type: {
                    validators: {
                        callback: {
                            message: "{{trans('attributes.validate.input_type')}}",
                            callback: function (value, validator, $field) {
                                var options = $('[id="input_type"]').val();
                                return (options != 0);
                            }
                        },
                        notEmpty: {
                            message: "{{trans('attributes.validate.input_required')}}"
                        }
                    }
                }
            }
        }).on('success.form.bv', function (event) {
            event.preventDefault();
           // alert($('#addAttribute'));/*
            // $('#addAttribute').submit();
            ajaxCallPopup($('#addAttribute'));
            //ajaxCall();
            attributes_grid(); 
            return false;
        }).validate({
            submitHandler: function (form) {
                return false;
            }
        });
        $('#basicvalCodeModal').on('hide.bs.modal', function () {
            console.log('resetForm');
            $('#addAttribute').data('bootstrapValidator').resetForm();
            $('#addAttribute')[0].reset();
        });
        
        $('#basicvalCodeModal3').on('hide.bs.modal', function () {
           
            $('#save_attribute_form')[0].reset();
             $("#category4").select2().select2('val',0);
          });
        $('#basicvalCodeModal3').on('show.bs.modal', function (e) {
          $('#save_attribute_form')[0].reset(); 
           $("#category4").select2().select2('val',0);
        });
        
        $('#editAttributeset').bootstrapValidator({
//        live: 'disabled',
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                attribute_set_name: {
                    validators: {
                        remote: {
                            message: 'Name already exists.Please enter a new name',
                            headers: {'X-CSRF-TOKEN': token},
                            url: '/product/checkSetAvailability',
                            type: 'GET',
                            data: function (validator, $field, value) {
                                return {
                                    'attribute_set_id': validator.getFieldElements('attribute_set_id').val()
                                };
                            },
                            delay: 2000     // Send Ajax request every 2 seconds
                        },
                        notEmpty: {
                            message: 'Attribute Set Name is Required'
                        }
                    }
                },
                category_id: {
                    validators: {
                        callback: {
                            message: 'Please choose Category Name',
                            callback: function (value, validator, $field) {
                                // Get the selected options
                                var options = $('[id="category_id1"]').val();
                                return (options != 0);
                            }
                        },
                        notEmpty: {
                            message: 'Please select Category.'
                        }
                    }
                }
            }
        }).on('success.form.bv', function (event) {
            event.preventDefault();
            console.log("i am in bootstrapValidator.");
            //ajaxCall(); 
            return false;
        });
        $('#add_attributes_form_excel').bootstrapValidator({
    //        live: 'disabled',
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                'attribute_sets': {
                    validators: {
                        notEmpty: {
                            message: 'Select Attribute Sets.'
                        }
                    }
                },
                attribute_files: {
                    validators: {
                        callback: {
                            message: 'The selected file is not valid',
                            callback: function (value, validator, $field) {
                                console.log($field);
                                var exts = ['csv','xls','xlsx'];
                                // split file name at dot
                                var get_ext = value.split('.');
                                // reverse name to check extension
                                get_ext = get_ext.reverse();
                                // check file type is valid as given in ‘exts’ array
                                if ($.inArray(get_ext[0].toLowerCase(), exts) > -1) {
                                    return true;
                                } else {
                                    return false;
                                }
                            }
                        },
                    }
                }
            }
        }).on('success.form.bv', function (event) {
            event.preventDefault();
            $form = $(this);
            var url = $form.attr('action');
            var formData = new FormData($(this)[0]);
            $('#attloader').show();
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                async: false,
                success: function (data) {
                    //$('#update_import_product_message').text(data);
                    //alert(data);
                    $('.close').trigger('click');
                    $('#attloader').hide();
                    //ajaxCall(); 
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
        $('#attributes_fileupload').change(function(){
            $('#add_attributes_form_excel').submit();
        });
    });
    
    $(".modal").on('hide.bs.modal', function () {
        var form_id = $(this).find('form').attr('id');
        $('#' + form_id).bootstrapValidator('resetForm', true);
        $('#' + form_id)[0].reset();
    });
    
   /* $('#editAttributeSet').on('hide.bs.modal', function () {
        console.log('resetForm');
        //$('#editAttributeset').data('bootstrapValidator').resetForm();
        //$('#editAttributeset')[0].reset();
        $('[id="update_manufacturer_name"]').val($('#brand_name option:selected').text());
        $('[id="update_manufacturer_id"]').val($('#brand_name option:selected').val());
    });
    */
   /* $('#editAttributeSet').on('show.bs.modal', function () {
      
        $('[id="update_manufacturer_name"]').val($('#brand_name option:selected').text());
        $('[id="update_manufacturer_id"]').val($('#brand_name option:selected').val());
    });
*/
    
    $('#map_attributes').submit(function (event) {
        event.preventDefault();
        var url = $(this).attr('action');
        $.post(url, {attribute_group_id: $('#attribute_group_id').val(), aname: $('#aname').val()}, function (data) {
            $.each(data, function (i, v) {
                if ( true == v )
                {
                    $('#basicvalCodeModal2').addClass('modal fade');
                    location.reload();
                }
            });
        });
    });

    function makePopupAttributeAjax($el, primaryKey)
    {
        $el.on('shown.bs.modal', function (e) {
            var url = $(e.relatedTarget).data('href'),
                    $this = $(this),
                    $form = $this.find('form'),
                    key = primaryKey || 'attribute_group_id';

            $.get(url, function (data) {
                $.each(data, function (i, v) {
                    $form.find('[name="' + i + '"]').val(v);
                });
            });
        });
    }
    function deleteAttrSet(attribute_set_id)
    {
        var manufacturerId = $('#brand_name').val();
         token  = $("#csrf-token").val(); 
        var dec = confirm("Are you sure you want to Delete ?");
        console.log(dec);
        if ( dec == true ) {
        
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': token},
                        url: '/product/deleteattributeset',
                        data: {attribute_set_id: attribute_set_id, 'password': 1},
                        type: 'POST',
                        success: function (result)
                        {
                          alert(result);
                           attributes_grid();
                              //ajaxCall(); 
                            
                        },
                        error: function (err) {
                            console.log('Error: ' + err);
                        },
                        complete: function (data) {
                            console.log(data);
                        }
                    });
           // });
        }
    }
    function delAttributeFromAttSet(attribute_id, attribute_set_id)
    {
        var dec = confirm("Are you sure you want to Delete ?");
        var manufacturerId = $('#brand_name').val();
          token  = $("#csrf-token").val(); 
        if ( dec == true ) {
           
            $.ajax({
                 headers: {'X-CSRF-TOKEN': token},
                url: '/product/delAttributeFromGroup',
                data: {attribute_id: attribute_id, attribute_set_id: attribute_set_id},
                type: 'POST',
                success: function (result)
                {
                    if ( result == 1 ) {
                        alert('Succesfully Deleted !!');
                       attributes_grid();
                    } else {
                        alert(result);
                    }
                },
                error: function (err) {
                    console.log('Error: ' + err);
                },
                complete: function (data) {
                    console.log(data);
                }
            });
        }
    }
    function addAttributGroup(att_id,att_set_id)
    {
          var selectedVal = $("#attribute_group_id"+att_id).val();
            token  = $("#csrf-token").val();
            groupId='';
            if(selectedVal!='no')
            {
                groupId=selectedVal;
            } 
            $.ajax({
                 headers: {'X-CSRF-TOKEN': token},
                url: '/product/addAttributeGroup',
                data: {attribute_id: att_id, attribute_set_id: att_set_id, groupId: selectedVal},
                type: 'POST',
                success: function (result)
                {
                  alert("Succesfully updated Attribute Group.");
                   console.log(result);
                },
                error: function (err) {
                    console.log('Error: ' + err);
                },
                complete: function (data) {
                    console.log(data);
                }
            });
    }
    function switchAttributeSearchable(attribute_id,attribute_set_id,flag)
    {
      if($("#is_searchble"+attribute_id).prop(':checked'))
      {
            var decission = confirm("Do you want to make it searchable?");
            if(decission==true)
               updateSearch(attribute_id,attribute_set_id,1);
                       
       }else
       {
            console.log(flag);
            var decission = confirm("Are you sure you want to make it unsearchable?");
            if(decission==true)
            {
              updateSearch(attribute_id,attribute_set_id,0); 
            }

       }
    }
 function updateSearch(attribute_id,attribute_set_id,flag)
 {    
    token  = $("#csrf-token").val(); 
    $.ajax({
         headers: {'X-CSRF-TOKEN': token},
        url: '/product/searchAttributes',
        data: {attribute_id: attribute_id, attribute_set_id: attribute_set_id, flag: flag},
        type: 'POST',
        success: function (result)
        {
          
            if ( result == 1) {
                //alert('Succesfully Updated !!');
                //location.reload();
                ////ajaxCall(); 
            } else {
                alert(result);
            }
        },
        error: function (err) {
            console.log('Error: ' + err);
        },
        complete: function (data) {
            console.log(data);
        }
    });
 }
    function getAttributeGroupName(attributeSetId) {
         $('.attribute_set_value').val(attributeSetId);
          token  = $("#csrf-token").val();
        $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        url: '/getAttributeName/'+attributeSetId,
        type: 'POST',
            success: function (dataRs)
            {   
            //alert(dataRs);        
               $('.attribute_set_name').val(dataRs);

            },
            error: function (err) {
                console.log('Error: ' + err);
            },
            complete: function (data) {
                console.log(data);
            }
        });
      
    }

    function getAssignAttribute(attributeSetId)
    {
        
        $('#assign_attribute_set_id').val(attributeSetId);
        $('#attribute_set_id_add_attribute').val(attributeSetId);
        $('#assign_attribute_set_name').val($('#attribute_set_id_add_attribute option:selected').text());
    }

    function loadAssignData()
    {
        var manufacturer_id = $('#brand_name').val();
        var url = '/product/getelementdata';
        // Send the data using post
        var posting = $.post(url, {data_type: 'locations_groups', data_value: manufacturer_id});
        // Put the results in a div
        posting.done(function (data) {
            var result = JSON.parse(data);
            var temp;
            var fieldId;
            $.each(result, function (field, data) {
                if ( field == 'locations' )
                {
                    fieldId = 'locations';
                } else {
                    fieldId = 'product_groups';
                }
                if ( data != '' )
                {
                    $.each(data, function (key, value) {
                        $('#' + fieldId).append('<option value="' + value['id'] + '">' + value['name'] + '</option>');
                    });
                }
            });
        });
    }

    $('#brand_name').change(function () {
        $('[id="update_manufacturer_name"]').val($('#brand_name option:selected').text());
        $('[id="update_manufacturer_id"]').val($(this).val());
        ajaxCall($(this).val());
        updateGroups();
        loadAssignData();
    });

    function updateGroups()
    {
        $('[name="attribute_group_id"]').empty();
        var manufacturer_id = $('#brand_name').val();
        var url = '/product/getelementdata';
        // Send the data using post
        var posting = $.post(url, {data_type: 'attributeGroups', data_value: manufacturer_id});
        // Put the results in a div
        posting.done(function (data) {
            var result = JSON.parse(data);
            $('[name="attribute_group_id"]').append('<option value="" selected="true">Please select... </option>');
            $.each(result, function (key, value) {
                $('[name="attribute_group_id"]').append('<option value="' + value['attribute_group_id'] + '">' + value['name'] + '</option>');
            });
        });
    }
    
    $("#add_att_set_tab").click(function (e) 
    {
      if (e.originalEvent !== undefined)
      {
        var attribute_set_id=0;
        var selattid    =   '';
        $("#att_set_model_table").empty();
        $('#save_attribute_set_form')[0].reset();
        $("#category2").select2().select2('val',0);
        getAttributes(attribute_set_id,selattid);
      }      
    });
    $('#update_assign_attribute_set_button').click(function () {
        var manufacturerId = $('#brand_name').val();
        var url = $('#assignGroupsLocations').attr('action');
        var postData = $('#assignGroupsLocations').serializeArray();
        var posting = $.post(url, postData );
        
        posting.done(function (data) {
            /*var result = JSON.parse(data);
            console.log(data);*/
            console.log(data['message']);
            if ( data['status'] == true )
            {
                $('.close').trigger('click');
                alert(data['message']);
                //location.reload();
                ajaxCall(manufacturerId);
            } else {
                alert(data['message']);
            }            
        });
        //getAttributes();
    });
    function getAttributeGroup($att_id)
    {
        token  = $("#csrf-token").val();
        $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        url: '/product/getAttributeGroup',
        type: 'POST',
            success: function (dataRs)
            {  

            }
        });
    }
    function getAttributes(attribute_set_id,selattid){
        var url = '/product/addAttributedata/'+ attribute_set_id;
    $('#Removeattribute'+selattid).val('0');
    var posting = $.get(url);
    $('#Selectattribute'+selattid).html('');
    $('#attribute_id'+selattid).html('');
    posting.done(function (data) {
            var result = JSON.parse(data);
            $.each(result, function (key, value) {
        
        $('#Selectattribute'+selattid).append('<div class="fc-field" value="' + value.attribute_id + '">' + value.name + '</div>');
            });
           /* $.each(result.selectedAttr, function (key, value) {
        var key = key.substr(1, key.length);
        $('#attribute_id'+selattid).append('<div class="fc-field" value="' + key.attribute_id + '">' + value.name + '</div>');
            });*/
            $('#Removeattribute'+selattid).val('0');
            $('#attribute_id'+selattid+' div').each(function (i, v) {
        $('#formattributes'+selattid).val($('#formattributes'+selattid).val() + ',' + $(v).attr('value'));
            });
    });
    }
   
    function getAttirbuteGroups(category_id)
    {
     // alert(category_id);
      var attribut_group_var_temp = <?php echo json_encode($ag); ?>;
                  var combo = '<select name="attribute_group_id" id="attribute_group_id'+value.attribute_id+'" onchange="addAttributGroup('+value.attribute_id+','+value.attribute_set_id+');" class="form-control">';

                  combo+= '<option value="no">Please select</option>';
                  $.each(attribut_group_var_temp, function (i, el) {
                    alert(category_id);
                   if(category_id==el.category_id)
                   {
                     alert(category_id);
                      if(att_group_id == el.attribute_group_id)
                      {
                        combo+= "<option value=" + el.attribute_group_id + " selected>" + el.name + "</option>";
                      }
                      else
                      {
                        combo+= "<option value=" + el.attribute_group_id + ">" + el.name + "</option>";
                      }
                    }

                  });  
                   combo+= "</select>";
                   return combo;
    }
    //edit attribute set varient tab
    function editAttributeVarientTab(dataRs,attRs)
    {
        $("#editAttributSetId").empty();
        var html_code="";
            var combo="";
             var attributeName=[];
             var attributeGroupName=[];
             var attributeGroup=[];

                   $.each(dataRs, function (index, value) {
                  //alert(value.category_id);
                  var att_id=value.attribute_id;
                  var att_set_id=value.attribute_set_id;
                  var att_group_id=value.attribute_group_id;
                  var attribut_group_var_temp = <?php echo json_encode($ag); ?>;
                   var combo = '<select name="attribute_group_id" id="attribute_group_id'+value.attribute_id+'" onchange="addAttributGroup('+value.attribute_id+','+value.attribute_set_id+');" class="form-control">';
                    combo+= '<option value="1" >General</option>';
                    $.each(attRs, function (index, value)
                    {    
                       if(att_group_id == value.attribute_group_id)
                       {
                           combo+= '<option value="'+value.attribute_group_id+'" selected>'+value.name+'</option>';
                       }else
                       {
                         combo+= '<option value="'+value.attribute_group_id+'">'+value.name+'</option>';
                       }
                                   
                    });
                    combo+= "</select>";



                   var is_filterble_status= (value.is_filterable == 1) ? "checked='true'":"check='false'";
                   var  is_varient_status= (value.is_varient == 1)? "checked='true'":"check='false'";

                   var  is_secondary_varient_status= (value.is_secondary_varient == 1)? "checked='true'":"check='false'";
                   var  is_third_varient_status= (value.is_third_varient == 1)? "checked='true'":"check='false'";
                   
                    var is_searchble_status= (value.is_searchable == 1)? "checked='true'":"check='false'";

                    //appending data in table
                 html_code+= '<tr><td>'+value.name+'</td><td>'+combo+'</td><td><label class="switch"><input class="switch-input vr_status'+value.attribute_id+'" onclick="vr_enabled('+value.attribute_id+','+value.attribute_set_id+');" '+is_varient_status+' type="checkbox" id="vr_enabled_id'+value.attribute_id+'"/><span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label></td><td><label class="switch"><input class="switch-input vr_secondary_status'+value.attribute_id+'" onclick="vr_secondary_enabled('+value.attribute_id+','+value.attribute_set_id+');" '+is_secondary_varient_status+' type="checkbox" id="vr_secondary_enabled_id'+value.attribute_id+'"/><span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label></td><td><label class="switch"><input class="switch-input vr_third_status'+value.attribute_id+'" onclick="vr_third_enabled('+value.attribute_id+','+value.attribute_set_id+');" '+is_third_varient_status+' type="checkbox" id="vr_third_enabled_id'+value.attribute_id+'"/><span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label></td><td><label class="switch"><input class="switch-input is_searchble'+value.attribute_id+'" id="is_searchble'+value.attribute_id+'" '+is_searchble_status+' onclick ="switchAttributeSearchable('+value.attribute_id+','+value.attribute_set_id+',1);" type="checkbox" /><span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label></td><td><label class="switch"><input class="switch-input filter_status'+value.attribute_id+'" id="is_filterable_id'+value.attribute_id+'" '+is_filterble_status+' onclick ="checkIsFilterble('+value.attribute_id+','+value.attribute_set_id+');" type="checkbox"/><span class="switch-label" data-on="Yes" data-off="No"></span><span class="switch-handle"></span></label></td></tr>';

                  });
                  $("#editAttributSetId").append(html_code);
                
               
                  
    }
//Edit
function getAttributeSetName(att_name,cat_id,att_set_id)
{

   $("#category3").select2().select2('val',cat_id);
   $("#edit_attribute_name").val(att_name);
   $("#attribute_set_id").val(att_set_id);
         token  = $("#csrf-token1").val();
        $.ajax({
        headers: {'X-CSRF-TOKEN': token},
        url: '/product/getattributelistdata',
        data: {attribute_set: att_set_id},
        type: 'POST',
          success: function (dataRs)
          {           
            var getAttributOptions='<option value="1">General</option>';
            $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/product/getAllAttributeGroup/'+cat_id,
            processData: true,
                success: function (attRs)
                {   
                    
                  editAttributeVarientTab(dataRs,attRs);               
                }
            });
            
                
            },
            error: function (err) {
                console.log('Error: ' + err);
            },
            complete: function (data) {
                console.log(data);
            }
        });
}
    $('#editAttributeSet').on('show.bs.modal', function (e) {
        
    
        // console.log(manufacturer_id);

        var attribute_set_id = $(e.relatedTarget).data('attributeid');

        var url = '/product/getAttributedata/' + attribute_set_id;
        //console.log('Removing already selected attributes: '+$('#Removeattribute1').val());
        $('#Removeattribute1').val('0');
        var posting = $.get(url);
        $('#Selectattribute1').html('');
        $('#attribute_id1').html('');
        posting.done(function (data) {

        
            var result = JSON.parse(data);
            console.log(result);            
            $.each(result.unselected, function (key, value) {
                var key = key.substr(1, key.length);
                //$('#Selectattribute1').append('<option value="' + key + '">' + value + '</option>'); 
                $('#Selectattribute1').append('<div class="fc-field" value="' + key + '">' + value + '</div>');
            });
            $.each(result.selectedAttr, function (key, value) {
                var key = key.substr(1, key.length);
                //console.log(key);
                /*$('#attribute_id1').append('<option value="' + key + '">' + value + '</option>');*/
                $('#attribute_id1').append('<div class="fc-field" value="' + key + '">' + value + '</div>');
                //console.log($('#attribute_id').html());
            });
            $('#Removeattribute1').val('0');
            $('#attribute_id1 div').each(function (i, v) {
                $('#formattributes1').val($('#formattributes1').val() + ',' + $(v).attr('value'));
            });
        });
    });
    
    
//Edit
 
    $('[name="input_type"').change(function () {
        var inputTypeValue = $(this).val();
        if ( inputTypeValue == 'select' || inputTypeValue == 'multiselect' )
        {
            $('#option-button').trigger('click');
        }
    });
    $('#add_new_option').on('click', function () {
        var $template = $('#option_data');
        $clone = $template.clone();
        $('#option_data').before($clone.removeAttr('id').removeAttr('style'));
    });
    $('#assignAttributeSet').on('show.bs.modal', function (e) {
        
        var manufacturerName = $('#brand_name option:selected').text();
        
        $('[id="update_manufacturer_name"]').val($('#brand_name option:selected').text());
        
        $('[id="update_manufacturer_id"]').val($('#brand_name option:selected').val());
        
        var attribute_set_id = $('#assign_attribute_set_id').val();
        //console.log(attribute_set_id);
        var url = '/product/getAssignGroupDetails/'+ attribute_set_id;
        var posting = $.get(url); 
        posting.done(function (data) {
            //console.log(data);
            $('#assigntable').empty();

            $.each(data, function (key, value) {
                var jsonArg = new Object();
                jsonArg.product_group = value['product_group_id'];
                jsonArg.location_val = value['location_id'];
                var hiddenJsonData = new Array();
                hiddenJsonData.push(jsonArg);                
                $("#assign_data").append('<tr><td scope="row" id="product_groups_text">' + value['productgroup'] + '</td><td id="location_text">' + value['location_name']
                            + '</td><td><a href="javascript:void(0);" class="check-toggler" id="remCF"><span class="badge bg-red"><i class="fa fa-trash-o"></i></span></a><input type="hidden" name="assign_locations[]" value=' + "'" + JSON.stringify(jsonArg) + "'" + ' /></td></tr>');
            });
        });               
    });    
    function postData()
    {
        console.log('we are in view');
        return;
    }
    
    function checkIsFilterble(attrId,attrSetId)
    {
        token  = $("#csrf-token").val();
        if($(".filter_status"+attrId).prop('checked'))
        {
            status=1;
            var vr_enabled = confirm("Are you sure you want to set this as Filterble. ?"), self = $(this);
            if ( vr_enabled == true ) 
            {
                
                   
                $.ajax({
                     headers: {'X-CSRF-TOKEN': token},
                    type: "POST",
                    url: '/product/filterenabled/'+attrId+'/'+attrSetId+'/'+status,
                    success: function( msg ) {
                        if(msg == 1){
                            $('#text_'+attrId).removeAttr("disabled", "disabled");
                        }else{
                            alert("Only one Filterble can be set for an attribute set");
                            $('.filter_status'+attrId).prop('checked', false);
                        } 
                    }
                });
            }
            else
            {
                $('.filter_status'+attrId).prop('checked', false);
            }   
        }
        else
        {
            status=0;
            var vr_enabled = confirm("Are you sure you want to Disable Filterble for this.?"), self = $(this);
            if ( vr_enabled == true ) 
            {
                $.ajax({
                     headers: {'X-CSRF-TOKEN': token},
                    type: "POST",
                    url: '/product/filterenabled/'+attrId+'/'+attrSetId+'/'+status,
                    success: function( msg ) {
                       // console.log(msg+'____________'+status);
                        $('#text_'+attrId).attr("disabled", "disabled"); 
                    }
                });
            }
            else
            {
                $('.filter_status'+attrId).prop('checked', true);
            }  
        }  
    }
    
    function vr_third_enabled(attrId,attrSetId)
    {
        token  = $("#csrf-token").val();
        if($("#vr_third_enabled_id"+attrId).prop('checked') == true)
        {
            status=1;
            var vr_enabled = confirm("Are you sure you want to set this as Third Variant. ?"), self = $(this);
            if ( vr_enabled == true ) 
            {             
                   
                $.ajax({
                     headers: {'X-CSRF-TOKEN': token},
                    type: "POST",
                    url: '/product/edthirdenabled/'+attrId+'/'+attrSetId+'/'+status,
                    success: function( msg ) {
                        if(msg == 1){
                            $('#text_'+attrId).removeAttr("disabled", "disabled");
                        }else if(msg == 2){
                            alert("Attribute set is already exists to primary variant.");
                            $('#vr_third_enabled_id'+attrId).prop('checked', false);
                        }else if(msg == 3){
                            alert("Attribute set is already exists to Secondary variant.");
                            $('#vr_third_enabled_id'+attrId).prop('checked', false);
                        } else
                        {
                             alert("Only one Third variant can be set for an attribute set");
                            $('#vr_third_enabled_id'+attrId).prop('checked', false);
                        }
                    }
                });
            }
            else
            {
                $('#vr_third_enabled_id'+attrId).prop('checked', false);
            }   
        }
        else
        {
            status=0;
            var vr_enabled = confirm("Are you sure you want to Disable Third variant for this.?"), self = $(this);
            if ( vr_enabled == true ) 
            {
                $.ajax({
                     headers: {'X-CSRF-TOKEN': token},
                    type: "POST",
                    url: '/product/edthirdenabled/'+attrId+'/'+attrSetId+'/'+status,
                    success: function( msg ) {
                       // console.log(msg+'____________'+status);
                        $('#text_'+attrId).attr("disabled", "disabled"); 
                    }
                });
            }
            else
            {
                $('#vr_third_enabled_id'+attrId).prop('checked', true);
            }  
        }  
    }
    function vr_secondary_enabled(attrId,attrSetId)
    {
        token  = $("#csrf-token").val();
        if($("#vr_secondary_enabled_id"+attrId).prop('checked') == true)
        {
            status=1;
            var vr_enabled = confirm("Are you sure you want to set this as Secondary Variant. ?"), self = $(this);
            if ( vr_enabled == true ) 
            {                
                   
                $.ajax({
                     headers: {'X-CSRF-TOKEN': token},
                    type: "POST",
                    url: '/product/edsecodaryenabled/'+attrId+'/'+attrSetId+'/'+status,
                    success: function( msg ) {
                        if(msg == 1){
                            $('#text_'+attrId).removeAttr("disabled", "disabled");
                        }else if(msg == 2){
                            alert("Attribute set is already exists to primary variant.");
                            $('#vr_secondary_enabled_id'+attrId).prop('checked', false);
                        } else if(msg == 3){
                            alert("Attribute set is already exists to Third variant.");
                            $('#vr_secondary_enabled_id'+attrId).prop('checked', false);
                        } else
                        {
                             alert("Only one Secondary variant can be set for an attribute set");
                            $('#vr_secondary_enabled_id'+attrId).prop('checked', false);
                        }
                    }
                });
            }
            else
            {
                $('#vr_secondary_enabled_id'+attrId).prop('checked', false);
            }   
        }
        else
        {
            status=0;
            var vr_enabled = confirm("Are you sure you want to Disable seconadary variant for this.?"), self = $(this);
            if ( vr_enabled == true ) 
            {
                $.ajax({
                     headers: {'X-CSRF-TOKEN': token},
                    type: "POST",
                    url: '/product/edsecodaryenabled/'+attrId+'/'+attrSetId+'/'+status,
                    success: function( msg ) {
                       // console.log(msg+'____________'+status);
                        $('#text_'+attrId).attr("disabled", "disabled"); 
                    }
                });
            }
            else
            {
                $('#vr_secondary_enabled_id'+attrId).prop('checked', true);
            }  
        }  
    }
  function vr_enabled(attrId,attrSetId)
    {
        token  = $("#csrf-token").val();
        if($("#vr_enabled_id"+attrId).prop('checked') == true)
        {
            status=1;
            var vr_enabled = confirm("Are you sure you want to set this as variant. ?"), self = $(this);

            if(vr_enabled == true ) 
            {
                
                   
                $.ajax({
                     headers: {'X-CSRF-TOKEN': token},
                    type: "POST",
                    url: '/product/edenabled/'+attrId+'/'+attrSetId+'/'+status,
                    success: function( msg ) {
                        if(msg == 1){
                            $('#text_'+attrId).removeAttr("disabled", "disabled");
                        }else if(msg == 2){
                            alert("Attribute set is already exists to secondary variant.");
                            $('#vr_enabled_id'+attrId).prop('checked', false);
                        }else if(msg == 3){
                            alert("Attribute set is already exists to Third variant.");
                            $('#vr_enabled_id'+attrId).prop('checked', false);
                        }else{
                            alert("Only one primary variant can be set for an attribute set");
                            $('#vr_enabled_id'+attrId).prop('checked', false);
                        } 
                    }
                });
            }
            else
            {
                $('#vr_enabled_id'+attrId).prop('checked', false);
            }   
        }
        else 
        {
            status=0;
            var vr_enabled = confirm("Are you sure you want to Disable variant for this.?"), self = $(this);
            if ( vr_enabled == true ) 
            {
                $.ajax({
                     headers: {'X-CSRF-TOKEN': token},
                    type: "POST",
                    url: '/product/edenabled/'+attrId+'/'+attrSetId+'/'+status,
                    success: function( msg ) {
                       // console.log(msg+'____________'+status);
                        $('#text_'+attrId).attr("disabled", "disabled"); 
                    }
                });
            }
            else
            {
                $('#vr_enabled_id'+attrId).prop('checked', true);
            }  
        }  
    }
  function setopt()
    {
                      token  = $("#csrf-token").val();
                     var data = $('form#setoptions_form').serialize();
                    $.ajax({
                    headers: {'X-CSRF-TOKEN': token},
                    url:'/product/setoptions/',
                    data: data,
                    processData: false,
                    contentType: false, 
                    success:function(response){
                        $('#option-close').trigger('click');
                    }
                  });
    }


 $(function () {        
       $('#search_add').keyup(function(){       
        var value = $("#search_add").val();
        $("#Selectattribute1 > div").each(function() {            
        if ($(this).text().search(new RegExp(value, "i")) > - 1) {
        $(this).show();        
        } else {                  
        $(this).hide();            
        }
         });       
        $("#Selectattribute > div").each(function() {            
        if ($(this).text().search(new RegExp(value, "i")) > - 1) {
        $(this).show();        
        } else {                  
        $(this).hide();            
        }
         });
      });
      $('#search_edit').keyup(function(){       
        var value = $("#search_edit").val();
        $("#Selectattribute1 > div").each(function() {            
        if ($(this).text().search(new RegExp(value, "i")) > - 1) {
        $(this).show();        
        } else {                  
        $(this).hide();            
        }
         });       
        $("#Selectattribute > div").each(function() {            
        if ($(this).text().search(new RegExp(value, "i")) > - 1) {
        $(this).show();        
        } else {                  
        $(this).hide();            
        }
         });
      });                  
    });

</script> 

@stop