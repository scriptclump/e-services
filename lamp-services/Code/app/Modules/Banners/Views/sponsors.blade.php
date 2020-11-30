@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<style>
    .alert-info {
        background-color: #00c0ef !important;
        border-color: #00c0ef !important;
        color: #fff !important;
    }
    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
        padding: 8px !important;
    }
    .text-disabled:hover{
        color: #0000ff;
    }
    .text-disabled{
        color: #bdbdbd !important;
    }
    .label-enabled{
        background-color: #89C4F4 !important;
    }
    .label-disabled{
        background-color: #bdbdbd !important;
    }
    a[id$="Tab"] {
        text-decoration: none;
	}
    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url(/img/ajax-loader.gif) center no-repeat #fff;
    }
</style>
<span class="loader" id="loader" style="display:none;"><img src=""/></span>
<div class="box">    
    <div class="box-header">
    </div>
</div>
<div class="portlet-body">
    @if (\Session::has('status'))           
    <div class="alert alert-info">{!! \Session::get('status') !!}
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="float: right;">&times;</button></div>
    @endif                    
</div>
<div id="success_message_ajax"></div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> {{trans('banners.heading.index_page_sponsor')}} </div>   

                <div class="actions">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
                    @if($exlsprprms)
                    <a href="#tag_1" data-toggle="modal" id = "" class="btn btn-success">Export Sponsors Reports</a>
                    @endif
                   @if($addprms)
                    <a href="/sponsors/sponsor" class="btn green-meadow">Add Sponsor</a>
                    @endif
                   
                </div>             
                
            </div>
            <div class="portlet-body">
                
                <div class="row" style="padding: 7px 0px 0px 0px;">
                    <div class="col-md-12">                        
                        <div class="table-responsive">
                            <table id="sponsergridlist"></table>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-scroll fade in" id="tag_1" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="modalclose" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Export Sponsors Reports</h4>
            </div>
            <div class="modal-body">
                <form id="bannersExportForm" action="/sponsors/createsponsorsExport" class="text-center" method="POST" onsubmit="return validateform();">
                <input type="hidden" name="_token" id = "csrf-token" value="{{ Session::token() }}">

                    <div class="row">
                        <!-- <div class="col-md-12" align="center"> -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
                                        <input type="text" id="fromdate" name="fromdate" class="form-control" placeholder="From Date" autocomplete="Off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="todate" name="todate" class="form-control" placeholder="To Date" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                  <select  name="select_flags" id="select_flags" class="form-control multi-select-search-box  avoid-clicks"  placeholder="Please Select " required="required" autocomplete="Off">
                                    <!-- <option value="0" >All DC'S</option> -->
                                        
                                        <option value="0" >ALL</option>
                                        <option value="1" >Retailer</option>
                                        <option value="2" >DC</option>
                                        <option value="3" >Hub</option>
                                        <option value="4" >Beat</option>
                                        
                                 </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-4">
                              <div class="form-group">
                                                <label class="control-label">DC <span class="required">*</span></label>
                                                <select class="form-control select2me" id="warehousesponsor" name="warehouse[]" multiple="multiple" autocomplete="Off">
                                                    <option value="0">All</option>
                                                    @foreach($dcs as $dc)
                                                    @if($dc->lp_wh_name!='')
                                                    <option value="{{ $dc->le_wh_id}}"> {{ $dc->lp_wh_name }}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>   
                             </div> 
                              <div class="col-md-4">
                              <div class="form-group">
                                                <label class="control-label">Hub <span class="required">*</span></label>
                                                <select class="form-control select2me" id="hubsponsor" name="hubs[]" multiple="multiple" autocomplete="Off">
                                                    <!-- <option value="0">All</option> -->
                                                   
                                                </select>
                                            </div>   
                             </div> 
                             <div class="col-md-4">
                              <div class="form-group">
                                                <label class="control-label">Beat <span class="required">*</span></label>
                                                <select class="form-control select2me" id="beatsponsor" name="beats[]" multiple="multiple" autocomplete="Off">
                                                   <!--  <option value="0">All</option> -->
                                                   
                                                </select>
                                            </div>   
                             </div>
                             </div> 
                            <div class="clearfix"></div>
                            <div class="row">
                            <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.banner_type')}} <span class="required">*</span></label>
                                                <select class="form-control select2me" id="banner_type" name="banner_type" autocomplete="Off">
                                                    <option value="">Select</option>
                                                    @foreach($bnrtype as $banneritem)
                                                    @if($banneritem->value!=16701 && $banneritem->value!=16704)
                                                     <option value="{{ $banneritem->value}}" @if(isset($editdata[0]['navigator_objects']) && $editdata[0]['navigator_objects']==$banneritem->value) selected @endif>{{ $banneritem->master_lookup_name}}</option>
                                                     @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                             <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">{{trans('banners.form.item_list')}} <span class="required">*</span></label>
                                                <select class="form-control select2me" id="banner_list" name="banner_list[]" multiple="multiple" required="required" autocomplete="Off">
                                                    <option value=''>Select</option>
                                                      
                                                </select>
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
<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />

@stop

@section('style')
<style type="text/css">
.fa-pencil {
    color: #3598dc !important;
}
.fa-trash-o {
    color: #3598dc !important;
}
.glyphicon-refresh {
    color: #3598dc !important;
}
.ui-iggrid-results {
    bottom: -3px !important;
}

.ui-iggrid .ui-iggrid-tablebody td:nth-child(4) {

           text-align: center !important;
 }.ui-iggrid .ui-iggrid-tablebody td:nth-child(5) {

           text-align: center !important;
 }
 .ui-iggrid .ui-iggrid-tablebody td:nth-child(6) {

           text-align: center !important;
 }
 .ui-iggrid .ui-iggrid-tablebody td:nth-child(7) {

           text-align: center !important;
 }
 .ui-iggrid .ui-iggrid-tablebody td:nth-child(8) {

           text-align: center !important;
 }
</style>
@stop

@section('script') 
@include('includes.ignite')
@include('includes.validators') 
@include('includes.jqx')
@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">

   $(document).ready(function ()
    {
        $(function () {
            $(document).attr("title", "{{trans('dashboard.dashboard_title.company_name')}} - {{trans('banners.heading.index_page_sponsor')}}");

           
            
                    $('#sponsergridlist').igGrid({
                      
                        dataSource: '/sponsors/sponsorsList',
                        initialDataBindDepth: 0,
                        autoGenerateColumns: false,
                        mergeUnboundColumns: false,
                        generateCompactJSONResponse: false,
                        responseDataKey: "results", 
                        enableUTCDates: true, 
                        width: "100%",
                         height: "100%",
                         columns: [
                            {headerText: "{{trans('banners.grid.sponsor_name')}}", key: 'sponsor_name', dataType: 'string', width: '15%'},
                            {headerText: "{{trans('banners.grid.dc_name')}}", key: 'lname', dataType: 'string', width: '15%'},
                            {headerText: "{{trans('banners.grid.hub_name')}}", key: 'hname', dataType: 'string', width: '15%'},
                            {headerText: "{{trans('banners.grid.grid_frequency')}}", key: "frequency", width: "10%", dataType: "number"},
                            {headerText: "{{trans('banners.grid.clickcost')}}", key: "click_cost", width: "10%", dataType: "number"},
                            {headerText: "{{trans('banners.grid.impressioncost')}}", key: "impression_cost", width: "10%", dataType: "number"},
                            {headerText: "{{trans('banners.grid.grid_from_date')}}", key: 'from_date', dataType: 'string', width: '15%'},
                            {headerText: "{{trans('banners.grid.grid_to_date')}}", key: 'to_date', dataType: 'string', width: '15%'},
                            {headerText: "Status", key: 'CustomActio', dataType: 'string', width: '15%'},
                            {headerText: "{{trans('banners.grid.grid_action')}}", key: 'CustomAction', dataType: 'string', width: '15%'},
                            
                        ],
                        features: [
                        {
                            name:'Filtering',
                            type: "remote",
                            mode: "simple",
                            allowFiltering: true,
                            filterDialogContainment: "window",
                            columnSettings: [
                             {columnKey: 'sponsors_name', allowFiltering: true },
                             {columnKey: 'lname', allowFiltering: true },
                             {columnKey: 'hub_id', allowFiltering: true },
                             {columnKey: 'frequency', allowFiltering: true },
                             {columnKey: 'from_date', allowFiltering: true },
                             {columnKey: 'to_date', allowFiltering: true },
                             {columnKey: 'CustomAction', allowFiltering: false },
                              {columnKey: 'CustomActio', allowFiltering: false },
                                                         
                    ]
                    },
                    { 
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 20,
                    name: 'Paging',
                    loadTrigger: 'auto',
                    type: 'local'
                     }
                            ]
                    });

        });
    });

   function deleteData(did){

      token  = $("#csrf-token").val();
        var sponsordelete = confirm("Are you sure you want to delete ?"), self = $(this);
            if ( sponsordelete == true )
            {
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                data: 'deleteData='+did,
                type: "POST",
                url: '/sponsors/deletesponsor',
                success: function( data ) {
                 
                $("#sponsergridlist").igGrid({dataSource: '/sponsors/sponsorsList'}).igGrid("dataBind");

                $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Sponsor Data deleted successfully</div></div>');
                $(".alert-success").fadeOut(20000)
                        
                    }
            });  
        }

   }
      

      var dateFormat = "dd/mm/yy";
    from = $( "#fromdate" ).datepicker({
          //defaultDate: "+1w",
            dateFormat : dateFormat,
            changeMonth: true,          
          })
    /*.on( "change", function() {
            to.datepicker( "option", "minDate", getDate( this ) );
          })*/,
      to = $( "#todate" ).datepicker({
            //defaultDate: "+1w",
              dateFormat : dateFormat,
              maxDate:0,
            changeMonth: true,        
          })
      /*.on( "change", function() {
            from.datepicker( "option", "maxDate", getDate( this ) );
          })*/;  
    $(document).on('click', '.block_users', function (event) {
        var checked = $(this).is(":checked");
        var sponsorId = $(this).val();
        blockBanner(sponsorId, checked,event);
    });

    function blockBanner(sponsorId, isChecked,event) {

         token  = $("#csrf-token").val();
        if(!isChecked)
        {
            var decission = confirm("Are you sure you want to In-Active the Sponsor.");
            isChecked = 0;
        }else{
            var decission = confirm("Are you sure you want to Active the Sponsor.");
            isChecked = 1;
        }
        event.preventDefault();
        if (decission == true) {
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                method: "POST",
                url: '/sponsors/blocksponsor',
                data: "sponsorId=" + sponsorId+"&status="+isChecked,
                success: function (response) {
                    /*var data = $.parseJSON(response);
                    alert(data.status);*/
                    if (response==1) {
                       $("#success_message_ajax").html('<div class="flash-message"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert"></button>Banner Status Updated Successfully</div></div>');
                         $(".alert-success").fadeOut(20000)
                        $("#sponsergridlist").igGrid({dataSource: '/sponsors/sponsorsList'}).igGrid("dataBind");
                    }else{
                        alert('Unable to In-Active Sponsor, please contact admin.');
                    }
                },
                statusCode: {
                    500: function() {
                      alert("Sorry! you cannot Active/Inactive for this Sponsor.");
                    }
                }
            });                 
        }
    }

$("#banner_type").change(function() {
        
    var token = $('#csrf-token').val();
    var bannertype = $('#banner_type').val();
    var listitem='';
    var select2all='alloptions';
    if(bannertype!=''){
        $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/banners/bannerType",
                type:"POST",
                data: 'bannertype='+bannertype+'&listitem='+listitem+'&select2all='+select2all,
                success:function(data){
                $('#banner_list').select2('data', null);
                $('#beatsponsor').empty();
                $("#banner_list").html(data);     
                }
        });
    }
    
    });


 $('#warehousesponsor').on('change', function() {
        var warehouse=$('#warehousesponsor').val();
        var hdnhubid="";
        var token = $('#csrf-token').val();
        if(warehouse!=0 && warehouse!=null){
         $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/banners/gethubs",
                type:"POST",
                data: 'warehouseid='+warehouse+'&hdnhubid='+hdnhubid,
                success:function(data){
                $('#hubsponsor').select2('data', null);
                $('#beatsponsor').select2('data', null);
                $("#hubsponsor").html(data);    
                }
        });
    }else{
         $('#hubsponsor').empty();
         $('#hubsponsor').select2('data', null);
        $('#hubsponsor').select2().append('<option value="0">All</option>');
        $('#beatsponsor').select2('data', null);
        $('#beatsponsor').empty();
        $('#beatsponsor').select2().append('<option value="0">All</option>');
    }
});

     $('#hubsponsor').on('change', function() {
        var hubid=$('#hubsponsor').val();
        var hdnbeatid="";
        var token = $('#csrf-token').val();
        var warehouseid=$('#warehousesponsor').val();
        if(hubid!=0 && hubid!=null){
         $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/banners/getbeats",
                type:"POST",
                data: 'hubid='+hubid+'&warehouseid='+warehouseid+'&hdnbeatid='+hdnbeatid,
                success:function(data){
                $('#beatsponsor').select2('data', null);
                $('#beatsponsor').empty();
                $("#beatsponsor").html(data);    
                }
        });
    }else{
        $('#beatsponsor').empty();
        $('#beatsponsor').select2('data', null)
        $('#beatsponsor').select2().append('<option value="0">All</option>');
    }
});

    $('#uploadfile').on('click', function() {
    
    var flag=$("#select_flags").val();
    var dc=$("#warehousesponsor").val();

    var hub=$("#hubsponsor").val();

    var beat=$("#beatsponsor").val();

    var banner_list=$("#banner_list").val();

    var startDate = document.getElementById("fromdate").value;
    var endDate = document.getElementById("todate").value;

    if ((Date.parse(endDate) <= Date.parse(startDate))) {
      alert("To date should be greater than From date");
     // document.getElementById("ed_endtimedate").value = "";
     return false;
    }

    if(banner_list==null){
        alert("Please Select Item List");
        return false;
    }

    if(startDate==""){
        alert("Please Select From Date");
        return false;
    }

    if(endDate==""){
        alert("Please Select To Date");
        return false;
    }

    if(flag==1){
        if(dc=="" || dc==null){
            alert("Please select DC");
             return false;
        }else if(hub=="" || hub==null){
             alert("Please select Hub");
             return false;
        }else if(beat=="" || beat==null){
            alert("Please select Beat");
             return false;
        }else{
            return true;
        } 
    }else if(flag==2){
       if(dc=="" || dc==null){
            alert("Please select DC");
             return false;
        }else{
            return true;
        } 
    }else if(flag==3){
        if(hub=="" || hub==null){
             alert("Please select Hub");
             return false;
        }else{
            return true;
        }   
    }else if(flag==4){
        if(beat=="" || beat==null){
            alert("Please select Beat");
             return false;
        }else{
            return true;
        } 
    }
});

    $("#modalclose").on('click', function() {

        document.getElementById("bannersExportForm").reset();
        $("#warehousesponsor").select2("val", "");
        $("#hubsponsor").select2("val", "");
        $("#beatsponsor").select2("val", "");
        $("#banner_list").select2("val", "");
        $("#banner_type").select2("val", "");
        
    });

</script>
@stop