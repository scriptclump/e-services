@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="portlet light tasks-widget">
                <div class="portlet-title">                
                    <div class="caption">Payment Ledger Report</div>
                </div>
                <form id="getledgerReportForm" method="post" action="getledgerpaymentreport">     
                    <div class="row">
                    <div class="col-md-3" style="">
                            <div class="form-group">
                                <div class="caption">From Date</div>
                                <div class="form-group">
                                    <div class="input-icon right">
                                        <i class="fa fa-calendar"></i>
                                        <input type="text" id="fsdate" name="fromdate" class="form-control" placeholder="From Date" autocomplete="Off" required>
                                    </div>
                                </div>
                                
                            </div>
                    </div>
                    <div class="col-md-3" style="">
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
                    <div class="col-md-3" style="">
                            <div class="form-group">
                                <div class="caption">Please select DC name</div>
                                <div class="form-group">
                                    <div class="input-icon right">
									<select class="form-control select2me" name="business_unit_id" id="business_unit_id" required>
									<option value=''>Please Select</option>
                    @foreach($bu as $alldcs)
                    @if($alldcs->lp_name!='' || $alldcs->lp_name!=null)
                    <option value="{{ $alldcs->le_wh_id}}" >{{$alldcs->lp_wh_name}} - ({{$alldcs->name}})</option>
                    @endif
                    @endforeach
								
                                	</select>
                                    </div>
                                </div>
                            </div>
                    </div> 
                    <div class="col-md-2" style="padding-top:18px">
                            <input type="button" value="Export" class="btn green-meadow" id="getledgerreport" >
                            <input id="csrf-token" type="hidden" name="_token" value="{{csrf_token()}}">
                    </div>
                    </div>
                    </form>
                                 
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
                    
                          from = $( "#fsdate" ).datepicker({
                            dateFormat: 'dd-mm-yy',
                            maxDate:0,
                            onSelect: function () {
                              var select_date = $(this).datepicker('getDate');
                              var nextdayDate = getNextDay(select_date);
                              $('#todate').datepicker('option', 'minDate', nextdayDate);
                          }
                            //changeMonth: true,          
                          }),
                          to = $( "#todate" ).datepicker({
                                  dateFormat: 'dd-mm-yy',
                                   maxDate: '+0D',
                                //changeMonth: true,        
                          });
  

              $('#getledgerreport').on('click',function(){
               
                var startDate = document.getElementById("fsdate").value;
                var endDate = document.getElementById("todate").value;
                var dc = document.getElementById("business_unit_id").value;
                if ((Date.parse(endDate) < Date.parse(startDate))) {
                  alert("To date should be greater than From date");
                 return false;
                }else if(startDate==""){
                      alert("Please Select From date").focus();
                      return false;
                  }else if(endDate ==""){
                      alert("Please Select To date").focus();
                      return false;
                  }else if(dc==""){
                      alert("Please Select DC Name");
                      return false;
                  }else{
                  $('#getledgerReportForm').submit();
                }

                
              });
            });
        function getNextDay(select_date) {
        select_date.setDate(select_date.getDate());
        var setdate = new Date(select_date);
        var nextdayDate = zeroPad(setdate.getDate(),2) + '-' + zeroPad((setdate.getMonth() + 1), 2) + '-' + setdate.getFullYear();
        return nextdayDate;
    }

    function zeroPad(num, count) {
        var numZeropad = num + '';
        while (numZeropad.length < count) {
            numZeropad = "0" + numZeropad;
        }
        return numZeropad;
    }
  
        </script>
        @stop
        @extends('layouts.footer')