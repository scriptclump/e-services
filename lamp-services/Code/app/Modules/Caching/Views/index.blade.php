@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<style>
.hidden {
    display: none;
}
.modal-lg {
  width: 85%;
  margin: auto;
}
.loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url(/img/load.gif) center no-repeat #fff;
}
</style>
<span class="loader" id="attloader" style="display:none;"><img src=""/></span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title"><!--hello-->
                <div class="caption"> {{trans('caching.caching.caching_caption')}} </div>
                <div class="actions">
                	
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="cache_table">
                                <thead>
                                    <tr>
                                        <th> {{trans('caching.caching.caching_cat')}} </th>    
                                        <th colspan="4">{{trans('caching.caching.caching_flush')}}</th>    
                                    </tr>
                                </thead>
<tbody>
    <tr>
        <form>
        <td>
            {{trans('caching.caching_side_headings.product_slab')}}
        </td>
        <td>
            {{trans('caching.caching_form_fields.product')}}: <br/>
            <select class="form-control select2me" id="products_id" name="products_id" required="required">
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                @if(isset($productsInfo))
                    @foreach($productsInfo as $key => $value)
                        <option id="{{$key}}">{{$value}}</option>
                    @endforeach
                @endif
            </select>
        </td>
        <td>
            {{trans('caching.caching_form_fields.customer_type')}}: <br/>
            <select class="form-control select2me" id="customer_type_id" name="customer_type_id">
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                <option id="3014"> All type </option>
                @if(isset($customerTypeInfo))
                    @foreach($customerTypeInfo as $key => $value)
                        <option id="{{$key}}">{{$value}}</option>
                    @endforeach
                @endif
            </select>
        </td>
        <td>
            {{trans('caching.caching_form_fields.dc')}}: <br/>
            <select class="form-control select2me" id="dc_id" name="dc_id">
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                <option id="0">{{trans('caching.caching_form_fields.no_dc')}}</option>
                @if(isset($dcInfo))
                    @foreach($dcInfo as $key => $value)
                        <option id="{{$key}}">{{$value}}</option>
                    @endforeach
                @endif
            </select>
        </td>
        <td>
            <br/>
            <input type="button" class="btn green-meadow" value="{{trans('caching.caching_form_fields.flush_btn')}}" title="{{trans('caching.caching_form_fields.flush_btn_title')}}" id="products_slab_submit">
            <input type="button" class="btn green-meadow" value="{{trans('caching.caching_form_fields.view_btn')}}" title="{{trans('caching.caching_form_fields.view_btn_title')}}" id="products_slab_view" data-backdrop="static">
        </td>
        </form>
    </tr>
    <tr>
        <td>
            {{trans('caching.caching_side_headings.brand')}}
        </td>
        <!-- <td colspan="2">
            {{trans('caching.caching_form_fields.brand')}}:<br/>
            @if(isset($brandsInfo))
            <select class="form-control select2me" id="brands_id">
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                    @foreach($brandsInfo as $key => $value)
                        <option id="{{$key}}">{{$value}}</option>
                    @endforeach
            </select>
            @endif
        </td>
         <td>
            {{trans('caching.caching_form_fields.beat')}}: <br/>
            @if(isset($beatInfo))
            <select class="form-control select2me" id="brands_beat_id">
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                <option id="0">{{trans('caching.caching_form_fields.no_beat')}}</option>
                    @foreach($beatInfo as $key => $value)
                        <option id="{{$key}}">{{$value}}</option>
                    @endforeach
            </select>
            @endif
        </td> -->
        <td>
            {{trans('caching.caching_form_fields.customer_type')}}: <br/>
            <select class="form-control select2me" id="brands_customer_type_id" name="brands_customer_type_id">
                <option></option>
                <option id="all">All</option>
                 <option id="3014"> All type </option>
                @if(isset($customerTypeInfo))
                    @foreach($customerTypeInfo as $key => $value)
                        <option id="{{$key}}">{{$value}}</option>
                    @endforeach
                @endif
            </select>
        </td>
        <td>
            {{trans('caching.caching_form_fields.dc')}}: <br/>
            <select class="form-control select2me" id="brands_dc_id" name="brands_dc_id">
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                <option id="0">{{trans('caching.caching_form_fields.no_dc')}}</option>
                @if(isset($dcInfo))
                    @foreach($dcInfo as $key => $value)
                        <option id="{{$key}}">{{$value}}</option>
                    @endforeach
                @endif
            </select>
        </td>
        <td></td>
        <td>
            <br/>
            <input type="button" class="btn green-meadow" value="{{trans('caching.caching_form_fields.flush_btn')}}" title="{{trans('caching.caching_form_fields.flush_btn_title')}}" id="brands_flush_submit">
            <!-- <input type="button" class="btn green-meadow" value="{{trans('caching.caching_form_fields.view_btn')}}" title="{{trans('caching.caching_form_fields.view_btn_title')}}" id="view_item_cache_brands" data-backdrop="static"> -->
        </td>
    </tr>
    <tr>
        <td>
            {{trans('caching.caching_side_headings.category')}}
        </td>
        <!-- <td colspan="2">
            {{trans('caching.caching_form_fields.category')}}:<br/>
            @if(isset($categoryInfo))
            <select class="form-control select2me" id="category_id">
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                @foreach($categoryInfo as $key => $value)
                    <option id="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            @endif
        </td>
        <td>
            {{trans('caching.caching_form_fields.beat')}}:<br/>
            @if(isset($beatInfo))
            <select class="form-control select2me" id="category_beat_id">
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                <option id="0">{{trans('caching.caching_form_fields.no_beat')}}</option>
                    @foreach($beatInfo as $key => $value)
                        <option id="{{$key}}">{{$value}}</option>
                    @endforeach
            </select>
            @endif
        </td> -->
        <td>
            Select Segment:<br/>
            @if(isset($segmentinfo))
            <select class="form-control select2me" id="category_segment_id">
                <option id=""></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                @foreach($segmentinfo as $key => $value)
                    <option id="{{$key}}">{{$value}}</option>
                @endforeach
            </select>
            @endif
        </td>
        <td>
            {{trans('caching.caching_form_fields.customer_type')}}: <br/>
            <select class="form-control select2me" id="category_customer_type_id" >
                <option></option>
                <option id="all">All</option>
                <option id="3014"> All type </option>
                @if(isset($customerTypeInfo))
                    @foreach($customerTypeInfo as $key => $value)
                        <option id="{{$key}}">{{$value}}</option>
                    @endforeach
                @endif
            </select>
        </td>
        <td>
            {{trans('caching.caching_form_fields.dc')}}: <br/>
            <select class="form-control select2me" id="category_dc_id" >
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                <option id="0">{{trans('caching.caching_form_fields.no_dc')}}</option>
                @if(isset($dcInfo))
                    @foreach($dcInfo as $key => $value)
                        <option id="{{$key}}">{{$value}}</option>
                    @endforeach
                @endif
            </select>
        </td>
        <td>
            <br/>
            <input type="button" class="btn green-meadow" value="{{trans('caching.caching_form_fields.flush_btn')}}" title="{{trans('caching.caching_form_fields.flush_btn_title')}}" id="category_flush_submit">
            <!-- <input type="button" class="btn green-meadow" value="{{trans('caching.caching_form_fields.view_btn')}}" title="{{trans('caching.caching_form_fields.view_btn_title')}}" id="view_item_cache_category" data-backdrop="static"> -->
        </td>
    </tr>
    <tr>
        <td>
            {{trans('caching.caching_side_headings.manufacturer')}}
        </td>
        <!-- <td colspan="2">
            {{trans('caching.caching_form_fields.manufacturer')}}:<br/>
            @if(isset($manufacturerInfo))
            <select class="form-control select2me" id="manufacturer_id">
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                @foreach($manufacturerInfo as $key => $value)
                    <option id="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            @endif
        </td>
         <td>
            {{trans('caching.caching_form_fields.beat')}}: <br/>
            @if(isset($beatInfo))
            <select class="form-control select2me" id="manufacturer_beat_id">
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                <option id="0">{{trans('caching.caching_form_fields.no_beat')}}</option>
                    @foreach($beatInfo as $key => $value)
                        <option id="{{$key}}">{{$value}}</option>
                    @endforeach
            </select>
            @endif
        </td> -->
        <td>
            {{trans('caching.caching_form_fields.customer_type')}}: <br/>
            <select class="form-control select2me" id="manf_customer_type_id" name="manf_customer_type_id">
                <option></option>
                <option id="all">All</option>
                 <option id="3014"> All type </option>
                @if(isset($customerTypeInfo))
                    @foreach($customerTypeInfo as $key => $value)
                        <option id="{{$key}}">{{$value}}</option>
                    @endforeach
                @endif
            </select>
        </td>
        <td>
            {{trans('caching.caching_form_fields.dc')}}: <br/>
            <select class="form-control select2me" id="manf_dc_id">
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                <option id="0">{{trans('caching.caching_form_fields.no_dc')}}</option>
                @if(isset($dcInfo))
                    @foreach($dcInfo as $key => $value)
                        <option id="{{$key}}">{{$value}}</option>
                    @endforeach
                @endif
            </select>
        </td>
        <td></td>
        <td>
            <br/>
            <input type="button" class="btn green-meadow" value="{{trans('caching.caching_form_fields.flush_btn')}}" title="{{trans('caching.caching_form_fields.flush_btn_title')}}" id="manufacturer_flush_submit">
            <!-- <input type="button" class="btn green-meadow" value="{{trans('caching.caching_form_fields.view_btn')}}" title="{{trans('caching.caching_form_fields.view_btn_title')}}" id="view_item_cache_manufacturer" data-backdrop="static"> -->
        </td>
    </tr>
    <!-- <tr>
        <td>
            {{trans('caching.caching_side_headings.retailer')}}
        </td>
        <td colspan="2">
            {{trans('caching.caching_form_fields.retailer')}}:<br/>
             The below element makes an Ajax Call in Select2 -->
            <!-- <input type="hidden" id="retailer_id" style="width:100%" class="form-control" />
        </td>
        <td>
            {{trans('caching.caching_form_fields.beat')}}: <br/>
            @if(isset($beatInfo))
            <select class="form-control select2me" id="retailer_beat_id">
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                <option id="0">{{trans('caching.caching_form_fields.no_beat')}}</option>
                @foreach($beatInfo as $key => $value)
                    <option id="{{$key}}">{{$value}}</option>
                @endforeach
            </select>
            @endif
        </td>
        <td>
            <br/>
            <input type="button" class="btn green-meadow" value="{{trans('caching.caching_form_fields.flush_btn')}}" title="{{trans('caching.caching_form_fields.flush_btn_title')}}" id="retailer_flush_submit">
            <input type="button" class="btn green-meadow" value="{{trans('caching.caching_form_fields.view_btn')}}" title="{{trans('caching.caching_form_fields.view_btn_title')}}" id="view_item_cache_retailer" data-backdrop="static">
        </td>
    </tr> -->
    <tr>
        <td>
            {{trans('caching.caching_side_headings.dashboard')}}
        </td>
        <td colspan="2">
            {{trans('caching.caching_form_fields.dashboard')}}:<br/>
            <select class="form-control select2me" id="dashboard_id">
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
            </select>
        </td>
        <td>
            {{trans('caching.caching_form_fields.day')}}:<br/>
            <select class="form-control select2me" id="dashboard_day_id">
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
            </select>
        </td>
        <td>
            <br/>
            <input type="button" class="btn green-meadow" value="{{trans('caching.caching_form_fields.flush_btn')}}" title="{{trans('caching.caching_form_fields.flush_btn_title')}}" id="dashboard_flush_button">
        </td>
    </tr>
    <tr>
        <td>
            {{trans('caching.caching_side_headings.dynamic')}}
        </td>
        <td colspan="3">
            {{trans('caching.caching_form_fields.dynamic')}}:<br/>
            @if(isset($dynamicKeysInfo) and !empty($dynamicKeysInfo))
            <select class="form-control select2me" id="dynamic_id">
                <option></option>
                <option id="all">{{trans('caching.caching_form_fields.all')}}</option>
                @foreach($dynamicKeysInfo as $key)
                    <option id="{{$key->pattern}}">{{$key->key_title}} ({{$key->pattern}})</option>
                @endforeach
            </select>
            @endif
        </td>
        <td>
            <br/>
            <input type="button" class="btn green-meadow" value="{{trans('caching.caching_form_fields.flush_btn')}}" title="{{trans('caching.caching_form_fields.flush_btn_title')}}" id="dynamic_flush_button">
        </td>
    </tr>
</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="itemCacheDisplayModal" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" id="closeModal" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{{trans('caching.caching.caching_information')}}</h4>
        </div>
        <div class="modal-body" id="item_modal_body">

        </div>
        <div class="modal-footer">
          <button type="button" id="closeModal" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>
@stop
@section('script')
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {

        // Hiding Default
        $('#dashboard_ff_name_view').hide();
        
        $("#retailer_id").select2({
            placeholder: "Select",
            allowClear: true,
            minimumInputLength: 3,
            ajax: 
            {
                url: "/cache/getAjaxData/retailer",
                delay: 250,
                data: function (term) {
                    return {
                        q: term // search term
                    };
                },
                results: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.retailer_name,
                                id: item.retailer_id,
                                data: item
                            };
                        }),
                    };
                },
                error: function(xhr, textStatus, errorThrown){
                   alert('request failed');
                }
            }
        });


        function getProductsSlabUrl(option,route){
            
            var product_id = $('#products_id option:selected').attr('id');
            var dc_id = $('#dc_id option:selected').attr('id');
            var customer_type_id = $('#customer_type_id option:selected').attr('id');
            var content_url = null;
            var invalidData = false;

            if(product_id == undefined && dc_id == undefined && customer_type_id == undefined)
            {
                alert("{{trans('caching.messages.select_product_&_customer_&_dc')}}"+option);
                invalidData = true;
            }
            else if(product_id == undefined || product_id == '')
            {
                alert("{{trans('caching.messages.select_product')}}"+option);
                invalidData = true;
            }
            else if(dc_id == undefined || dc_id == '')
            {
                alert("{{trans('caching.messages.select_dc')}}"+option);
                invalidData = true;
            }
            else if(customer_type_id == undefined || customer_type_id == '')
            {
                alert("{{trans('caching.messages.select_customer_type')}}"+option);
                invalidData = true;
            }

            if(invalidData) return null; 

            content_url = "/cache/"+route+"/"+product_id+"/"+customer_type_id+"/"+dc_id;
            console.log(content_url);

            return content_url;
        }

        function getCacheItemsUrl(option,route,item_name,beat_name){

            var item_id = $('#'+item_name+'_id option:selected').attr('id');
            var beat_id = $('#'+beat_name+'_id option:selected').attr('id');
            var content_url = null;

            if(item_name == "retailer"){
                var item_id = $('#'+item_name+'_id').val();
                if(item_id == null) item_id = undefined;
            }

            if(item_id == undefined && beat_id == undefined) alert("{{trans('caching.messages.select_item_&_beat')}}"+option);
            else if(item_id == undefined) alert("{{trans('caching.messages.select_item')}}"+option);
            else if(beat_id == undefined) alert("{{trans('caching.messages.select_beat')}}"+option);
            else if(item_id == "all" && beat_id == "all")
                content_url = "/cache/"+route+"/all/all";
            else if(item_id == "all" && beat_id != "all")
                content_url = "/cache/"+route+"/all/"+beat_id;
            else if(item_id != "all" && beat_id == "all")
                content_url = "/cache/"+route+"/"+item_id+"/all";
            else
                content_url = "/cache/"+route+"/"+item_id+"/"+beat_id;

            return content_url;
        }

        $(document).on('click', '#closeModal', function(event) {
            var content =
              "#dashboard_id, #dashboard_day_id, "+
              "#manufacturer_id, #manufacturer_beat_id, "+
              "#category_id, #category_beat_id, "+
              "#retailer_id, #retailer_beat_id, "+
              "#brands_id, #brands_beat_id, "+
              "#products_id, #customer_type_id, #dc_id";
            $(content).select2("val", "");
        });

        $('[id$=_flush_submit]').click(function(){
            var item_type = $(this).attr('id');
            var dc_id,customer_type,segment_id='no';
            if(item_type == "category_flush_submit"){
                item_type = "category";
                //dc_id= $('#category_dc_id').val();
               //  dc_id = $("#category_dc_id").val();
                dc_id = $('#category_dc_id option:selected').attr('id');
                customer_type= $('#category_customer_type_id option:selected').attr('id');
                segment_id=$('#category_segment_id option:selected').attr('id');
                if(!segment_id){
                    segment_id='no';
                }
                console.log(dc_id);
                console.log(customer_type);
            }
            else if(item_type == "brands_flush_submit"){
                item_type = "brands";
                dc_id = $('#brands_dc_id option:selected').attr('id');
                customer_type= $('#brands_customer_type_id option:selected').attr('id');
            }
            else if(item_type == "manufacturer_flush_submit"){
                item_type = "manufacturer";
                dc_id = $('#manf_dc_id option:selected').attr('id');
                customer_type= $('#manf_customer_type_id option:selected').attr('id');
            }
            if(item_type == "category"){
                if(!customer_type && segment_id == 'no')
                    alert('Please select Customer Type / Segment Type');
                else if(!dc_id)
                    alert('Please select DC');
            }else if(!dc_id && !customer_type){
                alert('Please select DC and Customer Type');
            } else if(!customer_type ){
                alert('Please select Customer Type');
            }else if(!dc_id){
                alert('Please select DC');
            }
            if(dc_id && (customer_type || segment_id!='no')){
                $.ajax({
                    type:'get',
                    url:'/cache/type/'+dc_id+'/'+customer_type+'/'+item_type+'/'+segment_id,
                    success:function(res){
                        console.log(res);
                        alert(res.message);
                        window.location.reload();
                    }
                });
            }
            
           

          /*  var flush_url = getCacheItemsUrl("{{trans('caching.caching_form_fields.flush_btn')}}", "flush_item",item_type,item_type+"_beat");
            console.log('item type',flush_url);
            if(flush_url != null){
                $('#attloader').show();
                var item_name = $('#'+item_type+'_id option:selected').val();
                $.ajax({
                    url: flush_url+'/'+item_type,
                    type: 'GET',
                    success: function (response) {
                        $('#attloader').hide();
                        if (response.status) {
                            // Add the response to the body of the modal
                            $('#item_modal_body').html(response.table);
                            // Open the Modal, set the backdroup to static
                            $('#itemCacheDisplayModal').modal({backdrop: "static"});
                            
                        } else {
                            alert("{{trans('caching.messages.no_response')}}");
                        }
                    }
                });
            }*/

        });

        $('[id^=view_item_cache]').click(function(){
            var item_type = $(this).attr('id');
            if(item_type == "view_item_cache_category")
                item_type = "category";
            else if(item_type == "view_item_cache_brands")
                item_type = "brands";
            else if(item_type == "view_item_cache_manufacturer")
                item_type = "manufacturer";

            var view_url = getCacheItemsUrl("{{trans('caching.caching_form_fields.view_btn')}}","view_item",item_type,item_type+"_beat");
            if(view_url != null)
            {
                $('#attloader').show();
                var item_name = $('#'+item_type+'_id option:selected').val();
                $.ajax({
                    url: view_url+'/'+item_type,
                    type: 'GET',
                    success: function (response) {
                        $('#attloader').hide();
                        if (response.status) {
                            // Add the response to the body of the modal
                            $('#item_modal_body').html(response.table);
                            // Open the Modal, set the backdroup to static
                            $('#itemCacheDisplayModal').modal({backdrop: "static"});
                            
                        } else {
                            alert("{{trans('caching.messages.no_response')}}");
                        }
                    }
                });
            }
        });

        $('#products_slab_view').click(function(){

            var view_url = getProductsSlabUrl("{{trans('caching.caching_form_fields.view_btn')}}","view_product_slab");
            if(view_url != null)
            {
                var product_name = $('#products_id option:selected').text();
                var customer_type_name = $('#customer_type_id option:selected').text();
                var dc_name = $('#dc_id option:selected').text();

                $('#attloader').show();
                $.ajax({
                    url: view_url+'?product_name='+product_name+'&customer_type_name='+customer_type_name+'&dc_name='+dc_name,
                    type: 'GET',
                    success: function (response) {
                        $('#attloader').hide();
                        if (response.status) {
                            $('#item_modal_body').html(response.table);
                            $('#itemCacheDisplayModal').modal({backdrop: "static"});
                            
                        } else {
                            alert("{{trans('caching.messages.no_response')}}");
                        }
                    }
                });
            }
        });
        
        $('#products_slab_submit').click(function(){
            var content_url = getProductsSlabUrl("Flush","flush_product_slab");
            if(content_url != null)
            {
                $('#attloader').show();
                $.ajax({
                    url: content_url,
                    type: 'GET',
                    success: function (response) {
                        $('#attloader').hide();
                        if (response.message)
                            alert(response.message);
                        else
                            alert("{{trans('caching.messages.no_response')}}");
                        $("#products_id, #customer_type_id, #dc_id").select2("val","");
                    }
                });
            }
        });

        $(document).on("click","#dashboard_flush_button",function(){
            var dashboard_id = $('#dashboard_id option:selected').attr('id');
            var day_id = $('#dashboard_day_id option:selected').attr('id');
            var ff_user_id = $('#dashboard_ff_name_id option:selected').attr('id');
            
            var url = undefined;
            if(dashboard_id == undefined)
                alert("{{trans('caching.messages.dashboard_type')}}");
            else
                url = "/cache/dashboard_flush/"+dashboard_id+"/"+day_id+"/"+ff_user_id;
            if(url != undefined)
            {
                $('#attloader').show();
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        $('#attloader').hide();
                        if (response.message) {
                            alert(response.message);
                        } else {
                            alert("{{trans('caching.messages.no_response')}}");
                        }
                    }
                });
            }
            $("#dashboard_id, #dashboard_day_id").select2("val", "");
            // else
            //     alert("Please Fill all the Fields");
        });

        $(document).on("change","#dashboard_id",function(){
            var item_name = $('#dashboard_id option:selected').attr('id');
            console.log(item_name);
            if((item_name === "ff_list") || (item_name === "all"))
                $('#dashboard_ff_name_view').show();
            else
                $('#dashboard_ff_name_view').hide();
        });

        // $(document).on("click","tr td",function(){
        $('tr td').click(function() {
            $(this).closest('tr').find('.hidden').toggle();
            // $(e).find("#showMoreDataDiv").toggle();
        });
        $(document).on("change","input[name='search_filter']",function(){
            var content = $("input[name='search_filter']:checked").val();
            if(content == "products")
            {
                $("#myProductInput").show();
                $("#myItemInput").hide();
            }
            else if(content == "items")
            {
                $("#myItemInput").show();
                $("#myProductInput").hide();
            }
        });

        // Dynamic Keys Flush related jQuery
        $(document).on("click","#dynamic_flush_button",function(){
            var dynamic_pattern = $('#dynamic_id option:selected').attr('id');
            
            var url = undefined;
            if(dynamic_pattern == undefined)
                alert("Please Select Atleast One Key Name");
            else
                url = "/cache/dynamic_flush/"+dynamic_pattern;
            if(url != undefined)
            {
                $('#attloader').show();
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        $('#attloader').hide();
                        if (response.message) {
                            alert(response.message);
                        } else {
                            alert("There is no response from the server, please try again.");
                        }
                    }
                });
            }
            $("#dynamic_id").select2("val", "");
        });

    });

    function showAdditionalContent(element){
        $(element).find("td:last").toggleClass("hidden");
    }


    function searchProducts() {
      var input, filter, table, tr, td, i;
      input = document.getElementById("myProductInput");
      document.getElementById("myItemInput").value = "";
      filter = input.value.toUpperCase();
      table = document.getElementById("myTable");
      tr = table.getElementsByTagName("tr");
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[3];
        if (td) {
          if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }       
      }
    }

    
    function searchItems() {
      var input, filter, table, tr, td, i;
      input = document.getElementById("myItemInput");
      document.getElementById("myProductInput").value = "";
      filter = input.value.toUpperCase();
      table = document.getElementById("myTable");
      tr = table.getElementsByTagName("tr");
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[2];
        if (td) {
          if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }       
      }
    }
</script>
@stop
@extends('layouts.footer')