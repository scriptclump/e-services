    @extends('layouts.default')
    @extends('layouts.header')
    @extends('layouts.sideview')
    @section('content')

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet light tasks-widget">
                <div class="portlet-title">                
                    <div class="caption"> Bin Dashboard</div>
                    <div class="actions">   
                    <!-- <button type="button" class="btn green-meadow" data-toggle="modal" data-target="#binDiaConf">Bin Dimension Conf</button> -->
                    @if($binexportPermissions == 1)
                    <button type="button" class="btn green-meadow" data-toggle="modal" href="#upload_bin_config">{{trans('products.lables.bin_upload')}}</button>
                    @endif
                    </div>
                </div>    
                <div class="portlet-body">
                    <div id="warehouse_dashboard_grid"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- multiple rack level configuration process -->
   

 
    <div id="upload_bin_config" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
   <div class = "product_bin_mapping_loader"></div> 
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">{{trans('products.lables.upload_bin_config')}}</h4>
            </div>
            <div class="modal-body">
                <br>
                <form id="download_wh_form" action="/warehouseconfig/downloadbinexcel">
                    <div class="row">
                        <div class="col-md-12 " align="center">
                            <select class="form-control select2me" id="wh_list_id2" name="wh_list_id2">
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12 " align="center">
                            <select class="form-control select2me" id="bin_type_id" name="bin_type_id">
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12 " align="pull-left">
                            <input type="checkbox" name="with_data"/> {{trans('products.lables.with_data')}}
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12 " align="center"> 
                            <button type="submit" id="download_template_button" role="button" class="btn green-meadow">
                                {{trans('products.lables.download_bin_config_btn')}}</button>
                            <p class="topmarg"></p>
                        </div>
                    </div>
                </form>
                <br>
                <div class="row">
                    <div class="col-md-12 text-center" align="center">
                        <form id='import_template_form3' action="{{ URL::to('/warehouseconfig/importbinexcel') }}" class="text-center" method="post" enctype="multipart/form-data">
                            <div class="fileUpload btn green-meadow"> <span id="up_text">{{trans('products.lables.upload_bin_config_btn')}}</span>
                                <input type="file" class="form-control upload" name="import_file" id="upload_bin_file"/>
                            </div>
                            
                            <br/><br/>
                            <p class="topmarg"></p>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<a class="btn green-meadow" data-toggle="modal" style="display:none" href="#import_messages">Show errors</a>
   <div id="import_messages" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <table class='product_success_msg'>
                </table>
            </div>
        </div>
    </div>
	</div>
	
    {{HTML::style('css/switch-custom.css')}}
    <link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
    {{HTML::style('css/dragdrop/jquery-ui.css')}}
    <!--Sumoselect CSS Files-->

    <link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
    @stop

    @section('style')
    <style type="text/css">
  .rightAlign { text-align:right;}
    .level_class1{
      font-size:14px !important;  font-weight:600 !important;  background:#dbdbdb !important;
    }
    .level_class2{
      font-size:12px !important; padding-left:10% !important;  font-weight: bold !important; 
    }
    .level_class3{
      font-size:12px !important; padding-left:12% !important; background:#fff !important; 
    }
    .level_class4{
     font-size:12px !important; padding-left:15% !important; background:#fff !important; 
    }
    .level_class5{
     font-size:12px !important; padding-left:17% !important; background:#fff !important; 
    }
    .ui-front{
        border-bottom: 1px solid #000 !important; 
    }
    .select2-results .select2-highlighted {
        background: rgba(0,0,0,0.3);
        color:#000;
        text-shadow:0 0px 0 #000;
        font-weight:bold;
    }
    .ui-autocomplete{
            z-index: 10100 !important; top:5px;  height:100px; width: 270px!important; overflow-y: scroll; overflow-x:hidden;
        }
      .ui-autocomplete li{ border-bottom:1px solid #efefef;width: 270px!important; padding-top:10px!important; padding-bottom:5px!important; border-width:0px 1px 0px 1px !important; border-color:#000 !important;border-style: solid !important;
        }
       .ui-icon-check{color:#32c5d2 !important;}
        .ui-igcheckbox-small-off{color:#e7505a !important;}
        .fa-thumbs-o-up{color:#3598dc !important;}
      .fa-rupee{color:#3598dc !important;}
        .fa-pencil{color:#3598dc !important;}
        .fa-trash-o{color:#3598dc !important;}
      .ui-iggrid-featurechooserbutton{display:none !important}
      .ui-icon.ui-corner-all.ui-icon-pin-w{display:none !important}
      .fa-fast-forward{color:#3598dc !important;}
      .ui-icon-pin-s{display:none !important}

    #warehouse_config_grid_Actions {text-align:center !important;}
        

    .ui-iggrid .ui-iggrid-headertable, .ui-iggrid .ui-iggrid-content, .ui-iggrid .ui-widget-content, .ui-iggrid-scrolldiv table{border-spacing:0px !important;}
    </style>
    <link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
    @stop
    @section('userscript')
    @include('includes.validators')
    @include('includes.ignite')
     <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
     <script src="{{ URL::asset('assets/global/plugins/get-brands-dropdown.js') }}" type="text/javascript"></script>

    {{HTML::script('assets/admin/pages/scripts/warehouseconfig/wms_dashboard.js')}}
    <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script> 
    <!-- grouped product list -->
    {{HTML::script('assets/admin/pages/scripts/warehouseconfig/warehouse_form_wizard.js')}}
    @extends('layouts.footer')
    <script>
        jQuery(document).ready(function() {
          FormWizard.init();
        });
    $('#import_template_form3').submit(function(e){
        e.preventDefault();
        var formData = new FormData($(this)[0]);
		var csrf_token = $('#csrf-token').val();
        //$( ".product_bin_mapping_loader" ).append('<img src="/img/ajax-loader.gif" style="width:25px"/>' );
        var url = $(this).attr('action');
        //$('.product_success_msg').html('');
        $.ajax({
        headers: {'X-CSRF-TOKEN': csrf_token},
                url: url,
                type: 'POST',
                data: formData,
                async: false,
                success: function (data) {
                    $('.product_success_msg').html('');
                $('.close').trigger('click');
                $('#pimloader12').hide();
                $('a[href="#import_messages"]').trigger('click');
                data = jQuery.parseJSON(data);
                if (data.status_messages.length == 0)
                {
                $('.product_success_msg').append('<tr><td>' + data.message + '</td></tr>');
                } else {


                $.each(data.status_messages, function(key, val){

                $('.product_success_msg').append('<tr><td>' + val + '</td></tr>');
                });
                }
                //$('.product_success_msg').append('<tr><td>' + 'Warehouse configuration saved sucessfully' + '</td></tr>');
                //alert();
                },
                cache: false,
                contentType: false,
                processData: false
        });
    }); 
	
    $('#upload_bin_file').change(function(){
     //$( ".product_bin_mapping_loader" ).append('<img src="/img/ajax-loader.gif" style="width:25px"/>' );
    $('#import_template_form3').submit();
    });	
	
    </script>

    @stop