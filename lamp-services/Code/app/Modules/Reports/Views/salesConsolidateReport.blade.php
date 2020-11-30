@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet light tasks-widget">
                <div class="portlet-title">                
                    <div class="caption">Sales/Invoice Consolidate Summary Report</div>
                    <div class="actions">
                    <a href="#salessummaryOrders" data-toggle="modal" id = "" class="btn btn-success">Export Sales Consoldate Summary</a>
                    <a href="#invoicesummaryOrders" data-toggle="modal" id = "" class="btn btn-success">Export Invoice Consolidated Summary</a>
                   
                </div> 
                </div>
                
                                 
            </div>
        </div>
    </div>
    <div class="modal modal-scroll fade in" id="salessummaryOrders" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width:600px;height:150px;margin-left: -108px;margin-top: 100px">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode"> Sales Consolidate Summary Report</h4>
            </div>
            <div class="modal-body">
                <form id="getSalesConsolidateReportForm" method="post" action="getSalesConsolidateReport">     
                    <div class="row">
                    <div class="col-md-4" style="">
                            <div class="form-group">
                                <div class="caption">From Date</div>
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="fsdate" name="fromdate" class="form-control" placeholder="From Date" autocomplete="Off">
                                    </div>
                                </div>
                                
                            </div>
                    </div>
                    <div class="col-md-4" style="">
                            <div class="form-group">
                                <div class="caption">To Date</div>
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="todate" name="todate" class="form-control" placeholder="To Date" autocomplete="Off">
                                    </div>
                                </div>
                                
                            </div>
                    </div> 
                    <div class="col-md-4" style="padding-top:18px">
                            <input type="button" value="Export" class="btn green-meadow" id="getsalesreport" >
                            <input id="csrf-token" type="hidden" name="_token" value="{{csrf_token()}}">
                    </div>
                    </div>
                    </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal modal-scroll fade in" id="invoicesummaryOrders" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width:600px;height:150px;margin-left: -108px;margin-top: 100px">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode"> Invoice Consolidate Summary Report</h4>
            </div>
            <div class="modal-body">
                <form id="getInvoiceConsolidateReportForm" method="post" action="getinvoicereport">     
                    <div class="row">
                    <div class="col-md-4" style="">
                            <div class="form-group">
                                <div class="caption">From Date</div>
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="fsinvoicedate" name="fsinvoicedate" class="form-control" placeholder="From Date" autocomplete="Off">
                                    </div>
                                </div>
                                
                            </div>
                    </div>
                    <div class="col-md-4" style="">
                            <div class="form-group">
                                <div class="caption">To Date</div>
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="toinvoicedate" name="toinvoicedate" class="form-control" placeholder="To Date" autocomplete="Off">
                                    </div>
                                </div>
                                
                            </div>
                    </div> 
                    <div class="col-md-4" style="padding-top:18px">
                            <input type="button" value="Export" class="btn green-meadow" id="getinvoicesalesreport" >
                            <input id="csrf-token" type="hidden" name="_token" value="{{csrf_token()}}">
                    </div>
                    </div>
                    </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
     {{HTML::style('css/switch-custom.css')}}
     @stop
      @section('style')
      <style type="text/css">
         
      </style>
      
      <link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
      <link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
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
      <script type="text/javascript">
              $(document).ready(function(){             
               var dateFormat = "dd-mm-yy";
                    
                          from = $( "#fsdate,#fsinvoicedate" ).datepicker({
                            dateFormat : dateFormat,
                            //changeMonth: true,          
                          }),
                          to = $( "#todate,#toinvoicedate" ).datepicker({
                                  dateFormat : dateFormat,
                                  maxDate:0,
                                //changeMonth: true,        
                          });

              $('#getsalesreport').on('click',function(){
                var fromdate=$('#fsdate').val();
                var todate=$('#todate').val();
                if(fromdate==''){
                  alert('Select From Date');
                  return false;
                }
                if(todate==''){
                  alert('Select From Date');
                  return false;
                }
                if ((Date.parse(todate) < Date.parse(fromdate))) {
                  alert("To date should be greater than From date");
                 // document.getElementById("ed_endtimedate").value = "";
                 return false;
                }else{
                  $('#getSalesConsolidateReportForm').submit();
                }
              });

              $('#getinvoicesalesreport').on('click',function(){
                var fromdate=$('#fsinvoicedate').val();
                var todate=$('#toinvoicedate').val();
                if(fromdate==''){
                  alert('Select From Date');
                  return false;
                }
                if(todate==''){
                  alert('Select From Date');
                  return false;
                }
                if ((Date.parse(todate) < Date.parse(fromdate))) {
                  alert("To date should be greater than From date");
                 // document.getElementById("ed_endtimedate").value = "";
                 return false;
                }else{
                  $('#getInvoiceConsolidateReportForm').submit();
                }
              });
            });
        </script>
        @stop
        @extends('layouts.footer')