@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php View::share('title', ' Ebutor - Supplier Brand Mapping'); ?>
    <div id="success_message_ajax"></div>
<div class="row">
<div class="col-md-12 col-sm-12">
<div class="portlet light tasks-widget">
   <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
   @if(isset($suppAccess) && $suppAccess==1)
   <div class="portlet-title">
      <div class="caption">Supplier Brand Mapping</div>
      <div class="actions">      
         <a class="btn green-meadow" data-toggle="modal" data-target="#add_spplier_popup" id="add_spplier">Brand Mapping</a> <span data-placement="top"></span> 
      </div>
   </div>
   @endif
   <div class="portlet-body">
      <div class="row">
         <div class="col-md-12">
            <table id="suppliersGrid_id"></table>
         </div>
      </div>
   </div>
</div>
</div>
</div>




<!-- Modal -->
<div class="modal fade" id="editPopUpDetails" role="dialog">
<div class="modal-dialog">
   <!-- Modal content-->
   <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Supplier Brand Mapping</h4>
      </div>
      <div class="modal-body">
         <!-- <p>Some text in the modal.</p> -->
         <form  action="" id="editPopUpDetails_form">
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group">
                     <label class="control-label">Supplier</label>
                     <input type="hidden" name="supplier_brand_map_id" id="supplier_brand_map_id" value="">
                     <select name = "supplier_name_edit" id="supplier_name_edit" class="form-control select2me">
                        <!-- <option value="0">Please Select</option> -->
                        @foreach($names as $name)
                        <option value = "{{$name->legal_entity_id}}">{{$name->business_legal_name}}</option>
                        @endforeach                                                    
                     </select>
                     <div id="supplier_required_edit" style="display: none;color: red">Please Select Supplier</div>
                  </div>
               </div><!-- 
               <div class="row"> -->
                    <div class="col-md-4">
                     <div class="form-group">
                        <label class="control-label">Manufacturer</label>
                        <select name = "manufacturer_name_edit[]" id="manufacturer_name_edit" class="form-control multi-select-search-box" multiple="multiple" onchange="manufacturerData()">
                           <!-- <option value="">Please Select</option> -->
                            <option value="0">ALL Manufacturer</option>
                           @foreach($manufacturer['manufacturer'] as $key=>$value)
                           <option value = "{{$key}}">{{$value}}</option>
                           @endforeach                                                    
                        </select>
                        <div id="manufacturer_required_edit" style="display: none; color: red">Please Select Manufacturer</div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="control-label">Brand</label>
                        <select name = "brand_name_edit[]" id="brand_name_edit" class="form-control multi-select-search-box avoid-clicks" multiple="multiple">
                           <!-- <option value="">Please Select</option> -->
                         
                                                                        
                        </select>
                        <div id="brand_required_edit" style="display: none; color: red">Please Select Brand</div>
                     </div>
                  </div>

               </div>
            </div>
            <div class="row">
               <div class="col-md-12 text-center">
                  <div class="form-group">
                     <button type="button" id="addbrandsupplier_edit"  class="btn green-meadow">Submit</button>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>
</div>




@include('Ledger::Form.addSupplierPopup')
@stop
@section('style')
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('css/switch-custom.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/javascript"/>

@stop
@section('script')
@include('includes.validators')
@stop
@section('userscript')
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/price/bootstrap_framework.min.js') }}" type="text/javascript"></script>
<script>
   window.asd = $('.multi-select-box').SumoSelect({csvDispCount: 4, captionFormatAllSelected: "Selected All !!"});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
        window.Search = $('.multi-select-search-box').SumoSelect({csvDispCount: 4, search: true, searchText: 'Search..'});
    $(function () {
    $("#suppliersGrid_id").igGrid({
        dataSource: '/suppliersGridData',
        responseDataKey: "results",
        columns: [
            { headerText: "Supplier", key: "business_legal_name", dataType: "string", width: "30%"},
            { headerText: "Supplier Code", key: "supplier_code", dataType: "string", width: "20%"},
            { headerText: "Brand", key: "brand_name", dataType: "string", width: "25%"},
            { headerText: "Manufacturer", key: "manuf_name", dataType: "string", width: "25%"},
            { headerText: "City", key: "city", dataType: "string", width: "25%"},
            { headerText: "Action", key: "CustomAction", dataType: "string", width: "15%" }
        ],
        features: [
            {
                name: "Sorting",
                type: "remote",
                columnSettings: [
                {columnKey: 'name_Display', allowSorting: true },
                {columnKey: 'business_legal_name', allowSorting: true },   
                {columnKey: 'phone_no', allowSorting: true },                
                {columnKey: 'Email', allowSorting: true },
            ]
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                {columnKey: 'FullName', allowFiltering: true },
                {columnKey: 'business_legal_name', allowFiltering: true },   
                {columnKey: 'phone_no', allowFiltering: true },                
                {columnKey: 'Email', allowFiltering: true },
                {columnKey: 'Warehouse', allowFiltering: true },
                {columnKey: 'pincode', allowFiltering: true },
                {columnKey: 'StateName', allowFiltering: true },
                {columnKey: 'City', allowFiltering: true },
                {columnKey: 'gstin', allowFiltering: true },
                {columnKey: 'CustomAction', allowFiltering: false },
                ]
            },
            { 
                recordCountKey: 'TotalRecordsCount', 
                pageIndexUrlKey: 'page', 
                pageSizeUrlKey: 'pageSize', 
                pageSize: 10,
                name: 'Paging', 
                loadTrigger: 'auto', 
                type: 'remote' 
            },
                            
        ],
        primaryKey: 'supplier_brand_map_id',
        width: '100%',
        height: '400px',
        defaultColumnWidth: '100px'
    }); 

    
});
</script>
<script>
  $("#addbrandsupplier").click(function() {
    // var formdata=$('#suppliersMapping').serialize();
    var supplierName = $('#supplier_name').val();
    var brandName = $('#brand_name').val();
    var manufacturerName = $('#manufacturer_name').val();
    if (supplierName == "" || supplierName == 0) {
        $('#supplier_required').css('display', 'block');
        return false;
    }
    if (brandName == "" || brandName == null) {
        $("#brand_required").css('display', 'block');
        return false;
    }
    if (manufacturerName == "" || manufacturerName == null) {
        $("#manufacturer_required").css('display', 'block');
        return false;
    }
    $.ajax({
        type: "POST",
        url: '/dataInsert',
        headers: {
            'X-CSRF-Token': $('input[name="_token"]').val()
        },
        data: $('#suppliersMapping').serialize(), // serializes the form's elements.
        success: function(data) {
            // show response from the php script.
            $("#supplier_name").select2("val", 0);
            data = JSON.parse(data); 
            if(data.status == 200) {
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">' + data.message + '</div></div>');
                $(".alert-success").fadeOut(20000);
                //or  $('#IDModal').modal('hide');
            } else {
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">' + data.message + '</div></div>');
                $(".alert-danger").fadeOut(20000);
                //$('#add_spplier_popup').modal('toggle'); //or  $('#IDModal').modal('hide');
            }
            $('#add_spplier_popup').modal('toggle');

            $("#suppliersGrid_id").igGrid({
                dataSource: '/suppliersGridData'
            }).igGrid("dataBind");
            $('#manufacturer_name')[0].sumo.unSelectAll();
            $('#brand_name')[0].sumo.unSelectAll();
        }
    });
});
$("#add_spplier").click(function() {

    $("#supplier_name").select2("val", 0);
    $('#brand_name')[0].sumo.unSelectAll();
    $('#manufacturer_name')[0].sumo.unSelectAll();
});
$('#supplier_name').change(function() {
    $('#supplier_required').css('display', 'none');
});
$('#brand_name').change(function() {
    $('#brand_required').css('display', 'none');
});
$('#manufacturer_name').change(function() {
    $('#manufacturer_required').css('display', 'none');
});

function editGridData(id) {
    $("#editPopUpDetails").modal("show");
    $('#editPopUpDetails').modal({
        backdrop: 'static',
        keyboard: false
    });
    var token = $("#csrf-token").val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': token
        },
        type: "GET",
        url: '/editGridDetails/' + id,

        success: function(data) {
            data = JSON.parse(data);
            $("#supplier_name_edit").select2('val', data[0].supplier_id);
            //$("#brand_name_edit").select2('val', data[0].brand_id);
            // var brandids=data[0].brand_id.split(',');
            // $.each(brandids,function(i,val){
            //   console.log(val);
            //    $('#brand_name_edit')[0].sumo.selectItem(val);
            // });
            $("#supplier_brand_map_id").val(data[0].supplier_brand_map_id);
             var manufacturerids=data[0].manufacturer_id.split(',');
            $.each(manufacturerids,function(i,val){
              console.log(val);
               $('#manufacturer_name_edit')[0].sumo.selectItem(val);
            });


        }
    });

}
$("#addbrandsupplier_edit").click(function() {
    var supplier = $('#supplier_name_edit').val();
    var brand = $('#brand_name_edit').val();
    var supplier_mapping_id = $('#supplier_brand_map_id').val();
    var manufacturerName = $('#manufacturer_name_edit').val();
    if (supplier == "" || supplier == 0) {
        $('#supplier_required_edit').css('display', 'block');
        return false;
    }
    if (brand == "" || brand == null) {
        $("#brand_required_edit").css('display', 'block');
        return false
    }
    if (manufacturerName == "" || manufacturerName == null) {
        $("#manufacturer_required_edit").css('display', 'block');
        return false;
    }
    $.ajax({
        type: "POST",
        url: '/updateSuppliersMapping',
        headers: {
            'X-CSRF-Token': $('input[name="_token"]').val()
        },
        data: $('#editPopUpDetails_form').serialize(), // serializes the form's elements.
        success: function(data) {
            // show response from the php script.
                $("#supplier_name_edit").select2("val", 0);
                $('#brand_name_edit')[0].sumo.unSelectAll();
                $('#manufacturer_name_edit')[0].sumo.unSelectAll();
            data = JSON.parse(data);
            if (data.status == 200) {
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">' + data.message + '</div></div>');
                $(".alert-success").fadeOut(20000);
                //or  $('#IDModal').modal('hide');
            } else {
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">' + data.message + '</div></div>');
                $(".alert-danger").fadeOut(20000);
                //$('#add_spplier_popup').modal('toggle'); //or  $('#IDModal').modal('hide');
            }
            $('#editPopUpDetails').modal('toggle');

            $("#suppliersGrid_id").igGrid({
                dataSource: '/suppliersGridData'
            }).igGrid("dataBind");

        }
    });
});

function deleteData(id) {
    var token = $("#csrf-token").val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': token
        },
        type: "GET",
        url: '/deleteSupplierID/' + id,
        success: function(data) {
          data = JSON.parse(data);
            if (data.status == 200) {
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success">' + data.message + '</div></div>');
                $(".alert-success").fadeOut(10000);
            } else {
                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-danger">' + data.message + '</div></div>');
                $(".alert-danger").fadeOut(10000);
            }
                $("#suppliersGrid_id").igGrid({
                dataSource: '/suppliersGridData'
                }).igGrid("dataBind");
        }
    });
}
$('#supplier_name').change(function(){
  var supplierid=$(this).val();
  var token = $("#csrf-token").val();          
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': token
        },
        type: "GET",
        url: '/checkbrandmanufbysupplierid/' + supplierid,
        success: function(data) {
        data = JSON.parse(data);
        $('#brand_name')[0].sumo.unSelectAll();
        $('#manufacturer_name')[0].sumo.unSelectAll();
        // if(data.length>0 && data[0].hasOwnProperty('brand_id')){
        //     var brandids=data[0].brand_id.split(',');
        //     $.each(brandids,function(i,val){
        //       console.log(val);
        //        $('#brand_name')[0].sumo.selectItem(val);
        //     });
        //   }
          if(data.length>0 && data[0].hasOwnProperty('manufacturer_id')){
             var manufacturerids=data[0].manufacturer_id.split(',');
            $.each(manufacturerids,function(i,val){
              console.log(val);
               $('#manufacturer_name')[0].sumo.selectItem(val);
            });
          }
          //manufacturerData(data[0].manufacturer_id);
        }
      });
   // $('#manufacturer_name').change(function(){
   //  var manuf=$(this).val();
   //  manuf = JSON.parse("["+manuf+"]");
   //  //if(manuf.indexOf(0)!==-1){
   //    if($.inArray('0', manuf)){
   //    $('#manufacturer_name')[0].sumo.unSelectAll();
   //    $('#manufacturer_name')[0].sumo.selectItem('0');
   //  }
   // });    
});
</script>   
@stop   