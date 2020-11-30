@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet light tasks-widget">
                <div class="portlet-title">                
                    <div class="caption">Average Stock Value Report</div>
                </div>     
                    <div class="row">
                    <div class="col-md-3" style="">
                            <div class="form-group">
                                <div class="caption">Business Units</div>
                                <select class="form-control select2me" id="business_unit_id">
                                <option value=''>Please Select</option>
                                <?php
                                if(isset($bu)){
                                  foreach ($bu as $key => $value) {
                                    echo $value;
                                  }
                                }
                                ?>
                                </select>
                            </div>
                    </div> 
                    <div class="col-md-2" style="padding-top:18px">
                            <input type="button" value="Export" class="btn green-meadow" id="getAvgStockReport" >
                            <input id="token_value" type="hidden" name="_token" value="{{csrf_token()}}">
                    </div>
                    </div>
                                 
            </div>
        </div>
    </div>
     {{HTML::style('css/switch-custom.css')}}
     @stop
      @section('style')
      <style type="text/css">
         
      </style>
      
      <link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
      <link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
      @stop

      @section('userscript')
      <style type="text/css">
      .modal-dialog {
          width: 220px !important;
      }
      .textRightAlign {
              text-align:right !important;
          }
            .bu1{
                margin-left: 10px;
                font-size: 18px;
                color:#000000;
            }
            .bu2{
                margin-left: 20px;
                font-size: 16px;
                color:#1d1d1d;
            }.bu3{
                margin-left: 30px;
                font-size: 15px;
                color:#3a3a3a;
            }.bu4{
                margin-left: 40px;
                font-size: 14px;
                color:#535353;
            }.bu5{
                margin-left: 50px;
                font-size: 13px;
                color: #6d6c6c;
            }.bu6{
                margin-left: 60px;
                font-size: 11px;
                color:#868383;
            }
  
      </style>
      @include('includes.validators')
      @include('includes.ignite')

      <script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
      <script src="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
      <script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.min.js') }}" type="text/javascript"></script>
      <script src="{{ URL::asset('assets/global/plugins/select2-promotions/js/select2.full.min.js') }}" type="text/javascript"></script>
      <script src="{{ URL::asset('assets/admin/pages/scripts/components-select2.min.js') }}" type="text/javascript"></script>
      <link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
      <link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
      <script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
      <link href="{{ URL::asset('assets/global/css/custom-selectDropDown.css') }}" rel="stylesheet" type="text/css" />
      <script type="text/javascript">
              $(document).ready(function(){             
               $("#getAvgStockReport").click(function(){
              	var bu_id = $("#business_unit_id").val();
               if(bu_id=='' || bu_id == undefined)
              	{
                  alert('Please select the DC or FC');
                }
                else
                {
                  location='getavgStock?bu_id='+bu_id;
                }
              });
            });
        </script>
        @stop
        @extends('layouts.footer')