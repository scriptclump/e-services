@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
<div class="col-md-12 col-sm-12">
<div class="portlet light tasks-widget">
        <div class="portlet-title">                
                    <div class="caption">Sales Report</div>
                </div>
                <form id="form_gettbvreport" method="post" action="gettbvreport">
              <div class="row">
                     <div class="col-md-2" style="">
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
                     <div class="col-md-2" style="">
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
                      <div class="col-md-2" style="">
                            <div class="form-group">
                                <div class="caption"> Sale Type</div>
                                <div class="form-group">
                                    <div class="input-icon right">
                                    <select class="form-control" name="sale_id" id="sale_id" required>
                                        <option value=''>Please Select</option>
                                        <option value="1">Primary Sale</option>
                                        <option value="2">Secondary Sale</option>
                                    </select>
                                    </div>
                                </div>
                            </div>
                    </div> 
                    <div class="col-md-2" style="">
                            <div class="form-group">
                                <div class="caption">Relates To</div>
                                <div class="form-group">
                                    <div class="input-icon right">
                                    <select class="form-control" name="tbv_id" id="tbv_id" required>
                                        <option value=''>Please Select</option>
                                        <option value="1">Manufacturer</option>
                                        <option value="2">Brand</option>
                                        <option value="3">Category</option>
                                        <option value="4">Product Group</option>
                                        <option value="5">State</option>
                                    </select>
                                    </div>
                                </div>
                            </div>
                    </div> 
                    <div class="col-md-2" style="">
                            <div class="form-group">
                                <div class="caption">Bill Type</div>
                                <div class="form-group">
                                    <div class="input-icon right">
                                    <select class="form-control" name="bill_id" id="bill_id" required>
                                        <option value=''>Please Select</option>
                                        <option value="1">Ordered Bill Value</option>
                                        <option value="2">Delivered Bill Value</option>
                                    </select>
                                    </div>
                                </div>
                            </div>
                    </div> 
                    <div class="col-md-2" style="padding-top:18px">
                            <input type="button" value="Export" class="btn green-meadow" id="gettbvreports" >
                            <input id="csrf-token" type="hidden" name="_token" value="{{csrf_token()}}">
                    </div>
        </div>
</form>

</div>
</div>
</div>

  @extends('layouts.footer')
  {{HTML::style('css/switch-custom.css')}}
     @stop
      @section('style')
      <link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
      <link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
      @stop

      @section('userscript')
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
  

              $('#gettbvreports').on('click',function(){
                var startDate = document.getElementById("fsdate").value;
                var endDate = document.getElementById("todate").value;
                var tbv = document.getElementById("tbv_id").value;
                var sale = document.getElementById("sale_id").value;
                var bill_type = document.getElementById('bill_id').value;
                if ((Date.parse(endDate) < Date.parse(startDate))) {
                  alert("To date should be greater than From date");
                  return false;
                }else if(startDate==""){
                      alert("Please Select From Date").focus();
                      return false;
                }else if(endDate ==""){
                      alert("Please Select To Date").focus();
                      return false;
                }else if(sale==""){
                      alert("Please Select Sale Type");
                      return false;
                    }else if(tbv==""){
                      alert("Please Select Relates To");
                      return false;
                    }else if(bill_type==""){
                      alert("Please Select Bill Type");
                      return false;
                    }
                    else{
                  $('#form_gettbvreport').submit();
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