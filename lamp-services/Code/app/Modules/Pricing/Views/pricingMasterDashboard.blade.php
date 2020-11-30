
@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<span id="success_message"></span>
<span id="error_message"></span>
<div id="loadingmessage" class=""></div>
@if(Session::has('flash_message'))
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert">×</a>
            {!!Session::get('flash_message')!!}
        </div>
@endif
<input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
<div class="row">
    <div class="caption" style="margin-left:41px; margin-top: 6px; "><bold>{{ trans('priceMaster_Label.filters.heading_1') }}</bold></div>
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget" style="height: auto;">
            <!-- portlet title start -->
            <div class="portlet-title">
                
                <div class="col-md-2">
                    <div class="form-group">
                        <select id = "zn_id"  name = "zn_id" class="form-control" style="margin-top:6px;" >
                            <option value=''>Select Zone</option>
                            @foreach($zones as $allzns)
                            @if($allzns["bu_name"]!='' || $allzns["bu_name"]!=null)
                           <option value="{{ $allzns['bu_id'] }}" > {{$allzns["bu_name"]}} </option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div> 
                <div class="col-md-2">
                    <div class="form-group">
                        <select id = "st_id"  name = "st_id" class="form-control" style="margin-top: 6px;" >
                            <option value=''>Select State</option>
                        </select>
                    </div>
                </div> 
                <div class="col-md-2">
                    <div class="form-group">
                        <select id = "dc_id"  name = "dc_id" class="form-control" style="margin-top: 6px;" >
                            <option value=''>Select DC</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <select id = "cust_tp"  name = "cust_tp" class="form-control" style="margin-top: 6px;">
                            <option value = "3014">{{ trans('priceMaster_Label.filters.product_type') }}</option>
                            @foreach($Product_type as $allproduct_types)
                            <option value = "{{$allproduct_types->value}}" >{{$allproduct_types->master_lookup_name}} </option>
                            @endforeach
                        </select>
                    </div>
                </div>     
                <div class="actions">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                    <button type="button"   id="exportButton" class="btn btn-success" >Excel Export</button>
                    <button type="button"   id="import_btn" class="btn btn-success" >Excel Import</button>
                </div>
                <div class="modal modal-scroll fade" id="upload-document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Import Pricing</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                          <a href="/download/PriceMasterImportSample.xls">Download the Sample File</a>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                         <input type="file" name="price_data" id="price_data">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label"> </label>
                                            <button type="button" class="col-md-12 btn green-meadow" id="price-upload-button">Upload</button>
                                        </div>
                                    </div>
                              </div>
                            </div>
                        </div>
                      </div>
                  </div>
            <!-- portlet title end -->
            <div class="portlet-body">
                 <div class="row">
                        <div class="col-md-12">                        
                            <div class="table-responsive">
                                <table id="pricingMethodListGrid"></table>
                            </div>                        
                        </div>
                    </div>
            </div>
         </div>
        </div>
    </div>
</div>

@stop

@section('userscript')
<style type="text/css">
.modal-dialog {
    width: 220px !important;
}
.textRightAlign {
        text-align:right !important;
    }
  
</style>

<!--Sumoselect CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<link href="{{ URL::asset('assets/global/plugins/sumo/sumoselect.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Bootstrap dataepicker CSS Files-->
<link src="{{ URL::asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/css"></link>
<!--Nouislider picker CSS Files-->
<link href="{{ URL::asset('assets/global/plugins/nouislider-new/nouislider.css') }}" rel="stylesheet" type="text/css" />

<script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
<!--Nouislider picker JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/nouislider-new/nouislider.js') }}" type="text/javascript"></script>
<!--Sumoselect JavaScriptFiles-->
<script src="{{ URL::asset('assets/global/plugins/sumo/jquery.sumoselect.js') }}" type="text/javascript"></script>
<!--Ignite UI Required Combined JavaScript Files--> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<!-- excel Export -->
<script src="{{ URL::asset('assets/global/plugins/igniteui/filesaver.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/Blob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.loader.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="http://cdn-na.infragistics.com/igniteui/2018.2/latest/js/infragistics.excel-bundled.js"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>


<script type="text/javascript">
        
    $(document).ready(function () {
      var bu_id,st_id,dc_id,zn_id,fc_id='';
      let bu_ids=[]; 
      var cust_tp=$('#cust_tp').val();
      function getBuid()
      {
        if (typeof zn_id !== 'undefined' && zn_id!== '' && zn_id!== null ) {
          bu_ids.push(zn_id);
        }
        if (typeof st_id !== 'undefined' && st_id!== '' && st_id!== null ) {
          bu_ids.push(st_id);
        }
        if (typeof dc_id !== 'undefined' && dc_id!== '' && dc_id!== null ) {
          console.log('hello');
          bu_ids.push(dc_id);
        }
        if (typeof fc_id !== 'undefined' && fc_id!== '' && fc_id!== null ) {
          bu_ids.push(fc_id);
        }
        if(typeof bu_ids[bu_ids.length-1]!=='undefined' && bu_ids[bu_ids.length-1]!=='' && bu_ids[bu_ids.length-1]!== null)
         {
             bu_id=bu_ids[bu_ids.length-1];
         }

      }          
      $('#import_btn').click(function(){
        $('#price_data').val('');
        $('#upload-document').modal('toggle');
      })
      $("#price-upload-button").click(function () {        
        token  = $("#csrf-token").val();
        var formData = new FormData();
        var importFiles = $('#price_data')[0].files[0];
        if( typeof importFiles !== "undefined" ){
          formData.append('price_data', importFiles);
          $.ajax({
              type: "POST",
              headers: {'X-CSRF-TOKEN': token},
              url: "/pricing/upload_esp_price",
              data: formData,
              processData: false,
              contentType: false,
              success: function (data){
                  // console.log(data);
                  $("#success_message").html('<div class="flash-message"><div class="alert alert-success">'+data+'</div></div>' );
                  $('#upload-document').modal('toggle');
              }
          });          
        } else{
          alert('Please select a valid excel file (.xls)');
        }        
      });


    $('#zn_id').change(function () {
        pricingMethodListGrid();
       var zn_id=$(this).val();
       var token = $('#csrf-token').val();
        $('[class="loader"]').show();

      $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/pricing/bulist",
                type:"POST",
                data: 'bu_id='+zn_id,
                dataType:'json',
                success:function(response){
                 $("#st_id").empty();
                 $("#dc_id").empty();
                 $("#fc_id").empty(); 
                 var st_option='<option value="">Select State</option>';
                 var dc_option='<option value="">Select DC</option>';
                 var fc_option='<option value="">Select FC</option>';
                 $("#st_id").html(st_option+response.result);
                 $('#dc_id').html(dc_option);
                 $('#fc_id').html(fc_option);
                 $("#st_id").select2("val", ''); 
                 $('[class="loader"]').hide();

                },
                error: function(response){
                alert("Please select the Business Unit");
                $('[class="loader"]').hide();

                }

        }); 
    }); 

    $('#st_id').change(function () {
        st_id=$(this).val();
        pricingMethodListGrid(st_id);
       var token = $('#csrf-token').val();
        $('[class="loader"]').show();

      $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/pricing/apob_dc_list",
                type:"POST",
                data: 'bu_id='+st_id,
                dataType:'json',
                success:function(response){

                 $("#dc_id").empty();
                 $("#fc_id").empty();
                 var dc_option='<option value="">Select DC</option>';   
                 var fc_option='<option value="">Select FC</option>';
                   $("#dc_id").html(dc_option+response.result);
                   $('#fc_id').html(fc_option);
                   $("#dc_id").select2("val", ''); 
                   $('[class="loader"]').hide();

                },
                error: function(response){
                alert("Please select the Business Unit");
                $('[class="loader"]').hide();

                }

        }); 
    }); 

    $('#dc_id').change(function () {
        dc_id=$(this).val();
        pricingMethodListGrid(dc_id);
       var token = $('#csrf-token').val();
        $('[class="loader"]').show();

      $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/pricing/bulist",
                type:"POST",
                data: 'bu_id='+dc_id,
                dataType:'json',
                success:function(response){
                  $("#fc_id").empty();
                  var fc_option='<option value="">Select FC</option>';
                  $("#fc_id").html(fc_option+response.result);
                  $("#fc_id").select2("val", ''); 
                  $('[class="loader"]').hide();

                },
                error: function(response){
                alert("Please select the Business Unit");
                $('[class="loader"]').hide();

                }

        }); 
    }); 

    $('#fc_id').change(function () {
        fc_id=$(this).val();
        pricingMethodListGrid(fc_id);
       var token = $('#csrf-token').val();
        
    }); 
    $('#cust_tp').change(function () {
            getBuid();
            pricingMethodListGrid(bu_id);
            var token = $('#csrf-token').val();
        
    }); 
    
         function pricingMethodListGrid(bu_id)
        {
            var zn_id=$("#zn_id").val();
            var st_id=$("#st_id").val();
            var dc_id=$("#dc_id").val();
            var fc_id=$("#fc_id").val();
            var cust_tp=$('#cust_tp').val();
            let bu_ids=[];
            if (typeof zn_id !== 'undefined' && zn_id!== '' && zn_id!== null ) {
              bu_ids.push(zn_id);
            }
            if (typeof st_id !== 'undefined' && st_id!== '' && st_id!== null ) {
              bu_ids.push(st_id);
            }
            if (typeof dc_id !== 'undefined' && dc_id!== '' && dc_id!== null ) {
              bu_ids.push(dc_id);
            }
            if (typeof fc_id !== 'undefined' && fc_id!== '' && fc_id!== null ) {
              bu_ids.push(fc_id);
            }
            // $('#pricingMethodListGrid').igGrid({
            //     dataSource: '/pricing/productlist?bu_id='+bu_ids[bu_ids.length-1]+'&cust_tp='+cust_tp,
            //     responseDataKey: 'Records',
            //     initialDataBindDepth: 0,
            //     autoGenerateColumns: false,
            //     mergeUnboundColumns: false,
            //     enableUTCDates: true, 
            //     width:'200%',
            //     height:'100%',
            //     columns: [
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_25') }}", key: "Warehouse", dataType: "string", width: '30%'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_1') }}", key: "SKU", dataType: "string", width: '30%'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_2') }}", key: "Product_ID", dataType: "number", width: '30%'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_3') }}", key: "Manufacturer", dataType: "string", width: '70%'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_4') }}", key: "Product_Title", dataType: "string", width: '70%'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_5') }}", key: "Group_ID", dataType: "number", width: '30%'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_6') }}", key: "LAST", dataType: "string", width: '50%'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_7') }}", key: "KVI", dataType: "string", width: '20%'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_8') }}", key: "SOH", dataType: "number", width: '20%'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_9') }}", key: "Active", dataType: "string", width: '20%'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_10') }}", key: "CFC", dataType: "number", width: '30%'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_11') }}", key: "ESU", dataType: "number", width: '30%'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_12') }}", key: "MRP", dataType: "number", width: '30%'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_13') }}", key: "PTR", dataType: "number", width: '30%',format:'{0:n2}'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_14') }}", key: "PTR_PER", dataType: "text", width: '30%',formatter: getPerValue},        
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_16') }}", key: "GST_PER", dataType: "text", width: '30%'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_17') }}", key: "Base_Rate", dataType: "number", width: '30%',format:'{0:n2}'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_18') }}", key: "Base_Rate_Sch_Amt", dataType: "number", width: '40%',format:'{0:n2}'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_19') }}", key: "Net_Rate", dataType: "number", width: '30%', format:'{0:n2}'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_22') }}", key: "Ebutor_Margin_PER", dataType: "text", width: '40%',formatter: getPerValue},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_15') }}", key: "Net_PER_after_PTR", dataType: "text", width: '40%',formatter: getPerValue},                  
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_24') }}", key: "Extra_PER", dataType: "text", width: '40%',formatter: getPerValue},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_20') }}", key: "ELP", dataType: "number", width: '30%',format:'{0:n2}'},
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_21') }}", key: "ESP", dataType: "number", width: '30%',format:'{0:n2}'},                   
            //         {headerText: "{{ trans('priceMaster_Label.gridheadings.gridLevel_1_column_23') }}", key: "ELP_PER", dataType: "text", width: '30%',formatter: getPerValue}
                   
                    
            //     ],
            //     features: [
            //         {
            //             name: "Filtering",
            //             mode: "simple",
            //             type: 'local',
            //             columnSettings: [
            //                 {columnKey: 'actions', allowFiltering: false},
            //                 {columnKey: 'Warehouse', allowFiltering: true },
            //                 {columnKey: 'SKU', allowFiltering: true },
            //                 {columnKey: 'Product_ID', allowFiltering: true },
            //                 {columnKey: 'Manufacturer', allowFiltering: true },
            //                 {columnKey: 'Product_Title', allowFiltering: true },
            //                 {columnKey: 'Group_ID', allowFiltering: true },
            //                 {columnKey: 'LAST', allowFiltering: true },
            //                 {columnKey: 'KVI', allowFiltering: true },
            //                 {columnKey: 'SOH', allowFiltering: true },
            //                 {columnKey: 'Active', allowFiltering: true },
            //                 {columnKey: 'CFC', allowFiltering: true },
            //                 {columnKey: 'ESU', allowFiltering: true },
            //                 {columnKey: 'MRP', allowFiltering: true },
            //                 {columnKey: 'PTR', allowFiltering: true },
            //                 {columnKey: 'PTR_PER', allowFiltering: true },                           
            //                 {columnKey: 'GST_PER', allowFiltering: true },
            //                 {columnKey: 'Base_Rate', allowFiltering: true },
            //                 {columnKey: 'Base_Rate_Sch_Amt', allowFiltering: true },
            //                 {columnKey: 'Net_Rate', allowFiltering: true },
            //                 {columnKey: 'Ebutor_Margin_PER', allowFiltering: true },
            //                 {columnKey: 'Net_PER_after_PTR', allowFiltering: true },                    
            //                 {columnKey: 'Extra_PER', allowFiltering: true },
            //                 {columnKey: 'ELP', allowFiltering: true },
            //                 {columnKey: 'ESP', allowFiltering: true },                            
            //                 {columnKey: 'ELP_PER', allowFiltering: true },
                           
            //             ],
            //         },
            //         {
            //             name: "Sorting",
            //             type: "local",
            //             persist: false,
            //             columnSettings: [
            //                 {columnKey: 'actions', allowFiltering: false},
            //                 {columnKey: 'Warehouse', allowFiltering: true },
            //                 {columnKey: 'SKU', allowFiltering: true },
            //                 {columnKey: 'Product_ID', allowFiltering: true },
            //                 {columnKey: 'Manufacturer', allowFiltering: true },
            //                 {columnKey: 'Product_Title', allowFiltering: true },
            //                 {columnKey: 'Group_ID', allowFiltering: true },
            //                 {columnKey: 'LAST', allowFiltering: true },
            //                 {columnKey: 'KVI', allowFiltering: true },
            //                 {columnKey: 'SOH', allowFiltering: true },
            //                 {columnKey: 'Active', allowFiltering: true },
            //                 {columnKey: 'CFC', allowFiltering: true },
            //                 {columnKey: 'ESU', allowFiltering: true },
            //                 {columnKey: 'MRP', allowFiltering: true },
            //                 {columnKey: 'PTR', allowFiltering: true },
            //                 {columnKey: 'PTR_PER', allowFiltering: true },                           
            //                 {columnKey: 'GST_PER', allowFiltering: true },
            //                 {columnKey: 'Base_Rate', allowFiltering: true },
            //                 {columnKey: 'Base_Rate_Sch_Amt', allowFiltering: true },
            //                 {columnKey: 'Net_Rate', allowFiltering: true },
            //                 {columnKey: 'Ebutor_Margin_PER', allowFiltering: true },
            //                 {columnKey: 'Net_PER_after_PTR', allowFiltering: true },
            //                 {columnKey: 'Extra_PER', allowFiltering: true },
            //                 {columnKey: 'ELP', allowFiltering: true },
            //                 {columnKey: 'ESP', allowFiltering: true },                         
            //                 {columnKey: 'ELP_PER', allowFiltering: true },
                           

            //             ],
            //         },
                   
            //        { 
            //         recordCountKey: 'TotalRecordsCount', 
            //         chunkIndexUrlKey: 'page', 
            //         chunkSizeUrlKey: 'pageSize', 
            //         chunkSize: 50,
            //         name: 'Paging',
            //         loadTrigger: 'auto',
            //         type: 'local'
            //          },
            //         {
            //             name: "Resizing",
            //         }
            //     ],
            // });
             function  getPerValue(val)
             {
                if(val!==null && val!=='undefined' && val!=='')
                {
                var data=val.replace("%","");
                var a=data.substr(0,4);
                var b='%';
                var c=a.concat(b);
                 return c;

                }
                else 
                  return '';
                
             }

              $.ig.loader({
                scriptPath: "{{ URL::asset('assets/global/plugins/igniteui') }}",
                cssPath: "{{ URL::asset('assets/global/plugins/igniteui') }}",
                resources:'igGrid,' + 'igGridExcelExporter'
               });
          }
          $("#exportButton").on("click", function () {
              getBuid();
              var stateId = $("#st_id").prop('selectedIndex');
              var zoneId = $("#zn_id").prop('selectedIndex');
              var dcId = $("#dc_id").prop('selectedIndex');
              var cust_tp = $("#cust_tp option:selected").val();
              if( stateId =="0" || zoneId == "0" || dcId == "0" ){
                alert("Please select Zone, State & DC");
              } else{
                if(bu_id== undefined || bu_id =='' || bu_id == null)
                {
                    
                    alert("Please select the business unit");
                }
                else if(cust_tp =='' || cust_tp == undefined || cust_tp == null)
                {
                    alert("Please select the customer type");   
                }
                else
                {
                    location='/pricing/getpricingdata?bu_id='+bu_id+'&cust_tp='+cust_tp;                          
                }
              }
          });

});
 
</script>


@extends('layouts.footer')
@stop
