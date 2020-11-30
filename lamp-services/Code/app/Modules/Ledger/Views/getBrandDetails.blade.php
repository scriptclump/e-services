@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')
<?php View::share('title', 'Reports'); ?>
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Reports</li>
        </ul>
    </div>
</div>
<div class="portlet light portlet-fit">
    <div class="portlet-title">
        <div class="caption uppercase">Company Reports</div>
        <div class="tools uppercase">&nbsp;</div>       
         <div class="actions">
        @if(isset($hasAccess) && $hasAccess==1)
        <button href="#brandDetails" data-toggle="modal" class="btn green-meadow" id="brandDetailsExcel">Brand Sales Report</button>
        @endif
        @if(isset($inventory) && $inventory==1)
        <button href="#inventoryReport" data-toggle="modal" class="btn green-meadow" id="exportExcel">Inventory Report</button> 
        @endif 
        </div> 
         
</div>
</div>

<div class="modal modal-scroll fade in" id="brandDetails" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Brand Sales Report</h4>
            </div>
            <div class="modal-body">
                <form id="brandDetailsForm" action="/getBrandDetails/download" class="text-center" method="GET">
                <input type="hidden" name="_token" id = "csrf-token" value="{{ Session::token() }}">

                    <div class="row">
                        <div class="col-md-12" align="center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="brand_fdate" name="fdate" class="form-control" placeholder="From Date" autocomplete="off">
                                        <span id="fdate_span_id" style="font-size: 13px; color: #bb1010; display: none">Please Select From Date</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="brand_tdate" name="tdate" class="form-control" placeholder="To Date" autocomplete="off">
                                        <span id="tdate_span_id" style="font-size: 13px; color: #bb1010; display: none">Please Select To Date</span>
                                    </div>
                                </div>
                            </div>
                               <div class="col-md-6">
                                <div class="form-group">
                                  <select  name="loc_dc_id[]" id="loc_dc_id" class="form-control multi-select-search-box  avoid-clicks" multiple="multiple" placeholder="Please Select DC">
                                    <option value="0" selected="selected">All DC</option>
                                        @foreach ($filter_options['dc_data'] as $dc_data)
                                        <option value="{{ $dc_data->le_wh_id }}" >{{ $dc_data->name }}</option>
                                        @endforeach
                                 </select>
                                     <!-- <span id="dc_span_id" style="font-size: 13px; color: #bb1010; display: none">Please Select DC</span> -->

                                </div>
                                <div class="form-group">
                                  <select  name="brand_id[]" id="brand_id" class="form-control multi-select-search-box avoid-clicks" multiple="multiple" placeholder="Please Select Brands"
                                  onchange="brandSupplierData()">
                                     @if(count($brandData))
                                         <option value="0" selected="selected">All Brands</option>
                                     @endif   
                                        @foreach ($brandData as $key => $data)
                                        <option value="{{ $key }}" >{{ $data }}</option>
                                        @endforeach
                                 </select>
                                <!-- <span id="brand_id_span" style="font-size: 13px; color: #bb1010; display: none">Please Select Brands</span>  -->
                                </div>

                            </div>
                               <div class="col-md-6">
                                <div class="form-group">
                                  <select  name="manufacture_id[]" id="manufacture_id" class="form-control multi-select-search-box  avoid-clicks" multiple="multiple" placeholder="Please Select Manufacturers" onchange="manufacturerBrandData()">
                                    @if(count($mnData))
                                    <option value="0" selected="selected">All Manufacturers</option>
                                    @endif 
                                        @foreach ($mnData as $key => $value)
                                        <option value="{{ $key }}" >{{ $value }}</option>
                                        @endforeach
                                 </select>
                                 <!-- <span id="manufature_id_span" style="font-size: 13px; color: #bb1010; display: none">Please Select Manufacturers</span> -->
                                </div>
                                <div class="form-group">
                                   <select name = "supplier_id" id="supplier_id" class="form-control multi-select-search-box  avoid-clicks" multiple="multiple" placeholder="Please Select Supplier">
                                      <option value="0" selected="selected">All Suppliers</option>
                                    <!--   @foreach($names as $name)
                                      <option value="{{$name->legal_entity_id}}">{{$name->business_legal_name}}</option>
                                      @endforeach  -->                                                   
                                   </select>
                                  <!-- <span id="supplier_required_valid_span" style="font-size: 13px; color: #bb1010; display: none">Please Select Supplier Name</span> -->
                                </div> 
                            </div>
                       </div>
                    <hr/>
                     <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="uploadfile" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                        </div>
                      </div>
                    </div>
                   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


<div class="modal modal-scroll fade in" id="inventoryReport" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Inventory Report</h4>
            </div>
            <div class="modal-body">
                <form id="inventory_form_Report" action="/getInventoryData/download" class="text-center" method="POST">
                 <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="row">
                        <div class="col-md-12" align="center">
                           <div class="col-md-6" align="">
                                <div class="form-group">
                                  <select  name="loc_dc_id[]" id="dc_id" class="form-control multi-select-search-box avoid-clicks" multiple="multiple" placeholder="Please Select DC">
                                    <option value="0" selected="Selected">All DC</option>
                                        @foreach ($filter_options['dc_data'] as $dc_data)
                                        <option value="{{ $dc_data->le_wh_id }}" >{{ $dc_data->name }}</option>
                                        @endforeach
                                 </select>
                                  <span id="span_get_data" style="font-size: 13px; color: #bb1010; display: none">Please Select DC</span>
                                </div>
                                <div class="form-group">
                                  <select  name="inv_brand_name[]" id="brand_name_id" class="form-control multi-select-search-box avoid-clicks" multiple="multiple" placeholder="Please Select Brands"
                                  onchange="getbrandBySelectedManu()">
                                     @if(count($brandData))
                                         <option value="0" selected="selected">All Brands</option>
                                     @endif   
                                        @foreach ($brandData as $key => $data)
                                        <option value="{{ $key }}" >{{ $data }}</option>
                                        @endforeach
                                 </select>
                                <!-- <span id="brand_id_span" style="font-size: 13px; color: #bb1010; display: none">Please Select Brands</span>  -->
                                </div>
                            </div>
                               <div class="col-md-6" align="">
                                <div class="form-group">
                                  <select  name="inv_manufacture_name[]" id="manufacture_name_id" class="form-control multi-select-search-box  avoid-clicks" multiple="multiple" placeholder="Please Select Manufacturers" onchange="manufacturerDataToGetBrandData()">
                                    @if(count($mnData))
                                    <option value="0" selected="selected">All Manufacturers</option>
                                    @endif 
                                        @foreach ($mnData as $key => $value)
                                        <option value="{{ $key }}" >{{ $value }}</option>
                                        @endforeach
                                 </select>
                                 <!-- <span id="manufature_id_span" style="font-size: 13px; color: #bb1010; display: none">Please Select Manufacturers</span> -->
                                </div>
                            </div>
                            <div class="col-md-6" align="">
                                <div class="form-group">
                                   <select name = "supplier_name[]" id="supplier_name_id" class="form-control multi-select-search-box  avoid-clicks" multiple="multiple" placeholder="Please Select Supplier">
                                      <option value="0" selected="selected">All Suppliers</option>
                                    <!--   @foreach($names as $name)
                                      <option value="{{$name->legal_entity_id}}">{{$name->business_legal_name}}</option>
                                      @endforeach  -->                                                   
                                   </select>
                                  <!-- <span id="supplier_required_valid_span" style="font-size: 13px; color: #bb1010; display: none">Please Select Supplier Name</span> -->
                                </div>
                            </div>
                        <div class="row">
                        <div class="col-md-12" align="left">
                            <!-- <span style="color:red">*</span> Note: default current month data will download -->
                        </div>
                    </div>
                    <hr/>
                     <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="uploadfile_id" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                        </div>
                      </div>
                    </div>
                   
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@stop

@section('userscript')

<script type="text/javascript" src="{{URL::asset('assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/payments/payments_grid.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/payments/approvalscript.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
   $(document).ready(function () {

        window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});

    //      $("#brandDetailsExcel").click(function() {
    //      $(':input').val("");

    //      $('#brand_id')[0].sumo.unSelectAll();
    //      $('#manufacture_id')[0].sumo.unSelectAll();
    //      $('#loc_dc_id')[0].sumo.unSelectAll();

    // });
    //     $("#exportExcel").click(function() {
    //     $(':input').val("");

    //      $('#dc_id')[0].sumo.unSelectAll();
    //      $('#manufacturers_id')[0].sumo.unSelectAll();
    //      $('#brand_data_id')[0].sumo.unSelectAll();

    // });


     var dateFormat = "dd/mm/yy";
    from = $( "#brand_fdate" ).datepicker({
          //defaultDate: "+1w",
            dateFormat : dateFormat,
            changeMonth: true,          
          }).on( "change", function() {
            to.datepicker( "option", "minDate", getDate( this ) );
          }),
      to = $( "#brand_tdate" ).datepicker({
            //defaultDate: "+1w",
              dateFormat : dateFormat,
            changeMonth: true,        
          }).on( "change", function() {
            from.datepicker( "option", "maxDate", getDate( this ) );
          });
          
     function getDate( element ) {
        var date;
        try {
          date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
          date = null;
        } 
      return date;
    }  
    });
     $("#brand_fdate").keydown(function(e) {
            e.preventDefault();  
        });
        $("#brand_tdate").keydown(function(e) {
            e.preventDefault();  
        });


$("#uploadfile").click(function(e){
 var dc_id = $("#loc_dc_id").val();
 var manufacture_id = $("#manufacture_id").val();
 var brand= $("#brand_id").val();
 var status = 0;
 var supplier = $('#supplier_id').val();
 var fdate = $('#brand_fdate').val();
 var tdate = $('#brand_tdate').val();
    if(dc_id == null){
           e.preventDefault();
        $("#dc_span_id").show();

    }
    else{
        $("#dc_span_id").hide();
    }
    if(manufacture_id == null){
          e.preventDefault();
        $("#manufature_id_span").show();
    }
    else{
        status =1;
        $("#manufature_id_span").hide();
    }

    if(brand == null){
           e.preventDefault();
      $("#brand_id_span").show();
        }else{
        $("#brand_id_span").hide();
    }
    if(supplier == null){
           e.preventDefault();
      $("#supplier_required_valid_span").show();
        }else{
        $("#supplier_required_valid_span").hide();
    }
      if(fdate == ''){
           e.preventDefault();
      $("#fdate_span_id").show();
        }else{
        $("#fdate_span_id").hide();
    }
    if(tdate == ''){
           e.preventDefault();
      $("#tdate_span_id").show();
        }else{
        $("#tdate_span_id").hide();
    }
 });

// $("#uploadfile_id").click(function(e){
    
//  var branddata = $("#dc_id").val();
//  var manufacture_iddata = $("#manufacturers_id").val();
//  var dc_iddata= $("#brand_data_id").val();

//     var status = 0;
//     if(branddata == null){ 
//     e.preventDefault();        
//         $("#span_get_data").show();
//     }
//     else{
//         $("#span_get_data").hide();
//       }
//      if(manufacture_iddata == null){
//         e.preventDefault();
//         $("#span_man_id_data").show();
//     }
//     else{
//         $("#span_man_id_data").hide();
//     }
//    if(dc_iddata == null){
//         e.preventDefault();
//         $("#brand_span_id_data").show();
//     }
//     else{
//         $("#brand_span_id_data").hide();
//     }
// });

function manufacturerBrandData(){
   var token = $("#csrf-token").val();
   // if(supplier_map_id==''){
   //    var sid=$('#supplier_id').val();
   //    var id=$('#manufacture_id').val();  
   //    alert(id); 
   // }else{
     // var sid=$('#supplier_id').val();
      var id=$('#manufacture_id').val();
   //}
   if(id!=null){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': token
        },
        type: "GET",
        url: '/getBrandsForManufacture',
        data:{
         id:id,
         //dataType:JSON,
        },
        dataType:'text',
        success: function(data) {
         // data = JSON.parse(data);
            $('#brand_id').empty();
            $('#brand_id')[0].sumo.reload();
             $("#brand_id").append(data);
             $('#brand_id')[0].sumo.reload();
        
         //  $("#brand_name").append('<option value="2" >Sunfeast</option><option value="3" >Sunfeast Dark Fantasy</option>'); 
        }
    });
 }
}
function brandSupplierData(){
    var token = $("#csrf-token").val();
    var brandID=$('#brand_id').val();
    var mnID=$('#manufacture_id').val();
   if(brandID!=null){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': token
        },
        type: "GET",
        url: '/brandsForSupplier',
        data:{
         id:brandID,
         manufacture_id:mnID,
         dataType:JSON,
        },
        success: function(data) {
            // data = JSON.parse(data);
            // alert(data);
              $('#supplier_id').empty();
              $('#supplier_id')[0].sumo.reload();
              $("#supplier_id").append(data);
              $('#supplier_id')[0].sumo.reload();
         //  $("#brand_name").append('<option value="2" >Sunfeast</option><option value="3" >Sunfeast Dark Fantasy</option>'); 
        }
    });
 }
}
function manufacturerDataToGetBrandData(){
  var token = $("#csrf-token").val();
  var id=$('#manufacture_name_id').val();
  if(id!=null){
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': token
      },
      type: "GET",
      url: '/getBrandsForManufacture',
      data:{
        id:id,
      },
      dataType:JSON,
      dataType:'text',
          success: function(data) {
          $('#brand_name_id').empty();
          $('#brand_name_id')[0].sumo.reload();
          $("#brand_name_id").append(data);
          $('#brand_name_id')[0].sumo.reload();  
        }
    });
  }
}

function getbrandBySelectedManu(){
    var token = $("#csrf-token").val();
    var brandID=$('#brand_name_id').val();
    var mnID=$('#manufacture_name_id').val();
    if(brandID!=null){
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': token
          },
          type: "GET",
          url: '/brandsForSupplier',
          data:{
            id:brandID,
            manufacture_id:mnID,
            dataType:JSON,
          },
          dataType:'text',
          success: function(data) {
            $('#supplier_name_id').empty();
            $('#supplier_name_id')[0].sumo.reload();
            $("#supplier_name_id").append(data);
            $('#supplier_name_id')[0].sumo.reload();
          }
        });
    }
}
</script>

@stop
@section('style')

<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" />

<style type="text/css">
.reportsmarg{ margin-left:-82px !important;  }
.portlet.light > .portlet-title > .actions .dropdown-menu li > a {
  color: #555;
  background:#fff;
  text-align:left;
}
.SumoSelect > .optWrapper > .options {
    text-align: left;
}
 .dropdown>.dropdown-menu:before, .dropdown-toggle>.dropdown-menu:before, .btn-group>.dropdown-menu:before {
    right: 9px;
    left: auto;
}
.dropdown>.dropdown-menu:after, .dropdown-toggle>.dropdown-menu:after, .btn-group>.dropdown-menu:after {
    right: 10px;
    left: auto;
}
.ui-iggrid .ui-iggrid-headertable, .ui-iggrid .ui-iggrid-content, .ui-iggrid .ui-widget-content, .ui-iggrid-scrolldiv table{border-spacing:0px !important;}
.ui-datepicker .ui-datepicker-prev .ui-icon, .ui-datepicker .ui-datepicker-next .ui-icon{
  color:#000 !important;
}

.avoid-clicks {
  pointer-events: none;
}
</style>
@stop

