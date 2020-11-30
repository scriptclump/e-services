@section('style')
<style>
.modal-body {
	display: -webkit-inline-box;
}
.row {
	margin-left: 15px !important;
	margin-right: 15px !important;
}
.ui-autocomplete {
	z-index: 99999999 !important;
	width : 250px;
}

.ui-widget-content{z-index: 99999999 !important;}
#ui-id-1 ,#ui-id-2{
	z-index: 99999999 !important;
}

.clear{margin-left:0px !important; margin-right:0px !important;}
</style>
@stop

  <div class="table-container">
    <div class="actions pull-right">
      <div data-toggle="buttons" class="btn-group btn-group-devided" > <a href="#myModal3" role="button" id="upload_categories" class="btn green-meadow" data-toggle="modal">{{trans('cp_headings.cp_map_category')}}</a> </div>
    </div>
    <div class="row clear" >
      <div class="col-md-12 clear">
        <table id="MapCategoryGrid">
        </table>
      </div>
    </div>
    <div id="myModal3" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title">CATEGORY MAPPING</h4>
          </div>
          <div class="">
            <form class="eventInsForm" id="channel_category_map" method="post" action="">
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group">
                    <label>Channel Categories * <span class="lock"><i class="fa fa-lock" aria-hidden="true"></i></span></label>
                    <input type="hidden" name="hidden-channel_id" id="hidden-channel_id" value="">
                    <input id= "getchannel_categories2" name= "category_name" type="text" class="form-control select2me" style="width:80%">
                    <input id= "channel_category_id" name= "channel_category_name" type="hidden" form="form-control ">
                    <input type="hidden" name="channel_id" value="">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>&nbsp;</label>
                    <input type="hidden" id="csrf_token" name="_token" value="{{ Session::token() }}">
                    <button class="btn green-meadow" id="goalkeeper" disabled=""  onclick="map(); return false;">Map attributes</button>
                    <button class="btn green-meadow" id="revert" onclick="revert_map(); return false;" style="display:none">Revert</button>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                    <label>Ebutor Categories *<span class="lock"><i class="fa fa-lock" aria-hidden="true"></i></span> </label>
                    <input type="hidden" name="hidden-ebutor_id" id="hidden-ebutor_id" value="">
                    <!--  <a href="#" data-toggle="tooltip" title="Hooray!" class='btn btn-circle btn-icon-only btn-default pull-right'>?</a>-->
                    <input id="getebutor_categories2" name="ebutor_category_name" type="text" class="form-control ">
                    <!-- <br>
                                        <!-- <select name="ebutor_category_name" style="width: 250px; height: 35px;">
                                            @foreach ($data['ebutor_cat'] as $key => $val)
      <option value="{{$key}}">{{$val->cat_name}}</option>
      @endforeach
      
    </select> --> 
                    
                  </div>
                </div>
              </div>
              <div id="results"></div>
              <div class="row">
                <div class="col-md-12 text-center" style="margin:20px 0px;"> <span id="goalpoint"></span>
                  <div id="accordion6" class="panel-group accordion" style="display:none">
                    <div style="height: 0px;" aria-expanded="false" class="panel-collapse collapse" id="collapse_0">
                      <div class="panel-body" id="desc-issue"> </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

