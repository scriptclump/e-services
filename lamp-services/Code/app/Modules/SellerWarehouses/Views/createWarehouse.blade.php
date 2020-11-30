@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
   <div class="col-md-12">
      <div class="portlet box" id="form_wizard_1">
         <div class="portlet-body form">
            
               <div class="form-wizard">
                  <div class="form-body" style="min-height:546px;">
                     <ul class="nav nav-pills nav-justified steps">
                        <li> <a href="tab1" data-toggle="tab" class="step"> <span class="number">1 </span> <span class="desc"> Select Logistic Partner</span> </a> </li>
                        <li> <a href="tab2" data-toggle="tab"  class="step"> <span class="number">2</span> <span class="desc"> Select Warehouse</span> </a> </li>
                        <li> <a href="tab3" data-toggle="tab" class="step active"> <span class="number">3</span> <span class="desc"> Upload Documents </span> </a> </li>
                     </ul>
                     <div id="bar" class="progress progress1 progress-striped" role="progressbar">
                        <div class="progress-bar progress-bar-success"> </div>
                     </div>
                     <div class="tab-content">
                        <div class="alert alert-danger display-none hideerror">
                           <button class="close" data-dismiss="alert"></button>
                           You have some form errors. Please check below. 
                        </div>
                        <div class="alert alert-success display-none">
                           <button class="close" data-dismiss="alert"></button>
                           Your form validation is successful! 
                        </div>
                        <div class="tab-pane active" id="tab1">
                           <div  id="channel_names">
                          <div class="scroller" style=" height:330px; " data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                              <!--<div class="text-right">
                                 <div class="inputs">
                                    <div class="portlet-input input-inline">
                                       <div class="input-icon right"> <i class="icon-magnifier"></i>
                                          <input type="text" class="form-control form-control-solid" placeholder="search...">
                                       </div>
                                    </div>
                                 </div>
                              </div>-->
                           
                           
                           <ul class="list" style=" list-style-type: none;">
                              @foreach($lps as $key => $lp)
                              <div class="col-md-2" style="padding-left:0px !important;">
                               <li>
                               <a href="javascript:void(0);" class="thumbnail"><img style="height:53px;" class="mp_logos" src="{{$lp->lp_logo}}" itemid="{{$lp->lp_id}}" id="lp_logo{{$lp->lp_id}}" /></a>
                               <p class="channel_name" id="lp_name{{$lp->lp_id}}" style="text-align:center;">{{$lp->lp_name}}</p>
                              </li>
                              </div>
                              <input type="hidden" id="lp_desc{{$lp->lp_id}}" name="lp_desc" value="{!! $lp->description !!}">
                              @endforeach                      
                          </ul>
                          </div>
                         </div> 
                           <div class="form-actions" >
                              <div class="row">
                                 <div class="col-md-12 text-center"> <a id="continue1" href="javascript:;" class="btn green-meadow">Continue</a> </div>
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane" id="tab2">
                           <div class="row">
                              <div class="col-md-3 text-center">
                                 <div class="note note-info note-shadow">
                                    <input type="image" id="logo">
                                    <h4 id="logo_name" class="block"></h4>
                                    <p id="desc">  </p>
                                 </div>
                              </div>
                              <div class="col-md-9">
                                 <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list2">
                                    <div class="table-responsive" >
                                      <table class="table table-striped table-bordered table-hover">
                                          <thead>
                                             <tr>
                                                <th>&nbsp;</th>
                                                <th>S No</th>
                                                <th>Warehouse Name</th>
                                                <th>Warehouse ID</th>
                                                <th>Address</th>
                                                <th>City</th>
                                                <th>Status</th>
                                             </tr>
                                          </thead>
                                          <tbody id="warehouse_table">
                                          </tbody>
                                        </table>
                                         <input type="hidden" id="count">
                                         <input type="hidden" id="selected_count">
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="form-actions" style="margin-top:80px;">
                              <div class="row">
                                 <div class="col-md-12 text-center"> <a id="back1" class="btn green-meadow">Back</a> <a href="javascript:;" id="check_continue" class="btn green-meadow">Continue</a> </div>
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane" id="tab3">
                        <form id="doc_form" method="POST"  files="true" enctype ="multipart/form-data" action="/warehouse/saveWarehouse">
                           <div class="row">
                              <div class="col-md-3 text-center">
                                 <div class="note note-info note-shadow">
                                    <input type="image" id="logo2">
                                    <h4 id="logo_name2" class="block"></h4>
                                    <p id="desc2">  </p>
                                 </div>
                              </div>
                               <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                              <div class="col-md-9">
                                 <div class="scroller" style="height: 500px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list2">
                                    <div class="table-responsive">
                                       <table class="table table-striped table-bordered table-hover">
                                          <thead>
                                             <tr>
                                                <th width="10%">S No</th>
                                                <th width="30%">Warehouse Details</th>
                                                <th width="50%">Upload Documents</th>
                                                <th width="10%">Status</th>
                                             </tr>
                                          </thead>
                                          <tbody id="document_table">
                                          </tbody>
                                       </table>
                                       <input type="hidden" name="legal_id" value="{!! $legal_id !!}">
                                    </div>
                                 </div>
                              </div>
                           </div>
                          </form>
                           <div class="form-actions" style="margin-top:0px;">
                              <div class="row">
                                 <div class="col-md-12 text-center"> <a id="back2" class="btn green-meadow">Back</a> <a href="javascript:;" id="saveForm" class="btn green-meadow">Done</a> </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
         </div>
      </div>
   </div>
</div>
@stop
@section('style')
<style type="text/css">
#logo {width: 100%;}
#logo2 {width: 100%;}
   .actionss{font-size:24px !important;}
   .code {
   font-size: 24px !important;
   }
   .note {
   border-left: 0px !important;
   }
   .note.note-info.note-shadow {
   box-shadow:none !important;
   }
   /*a.thumbnail.active, a.thumbnail:focus, a.thumbnail:hover {
   border-color: #A9CEEF !important;
   background-color: rgba(52, 122, 183, 0.3);
   padding: 10px 10px 0px 10px;
   }*/
   .form-actions {
   background:none !important;
   border-top: 1px solid #e5e5e5;
   }
   .btn {
   border-radius: 0px !important;
   }
   .form-control {
   border-radius: 0px !important;
   }
   .nofile{background:none !important; border:none !important;}
   .fileUpload {
   position: relative;
   overflow: hidden;
   margin: 0px;
   float: left;
   }
   .help-block{
   width: 300px !important;
   }
   .green-meadow {
   color: #1BBC9B;
   }
   [class^="fa-"]:not(.fa-stack), [class^="glyphicon-"], [class^="icon-"], [class*=" fa-"]:not(.fa-stack), [class*=" glyphicon-"], [class*=" icon-"] {
   font-size: 15px !important;
   }
   .fileUpload input.upload {
   position: absolute;
   top: 0;
   right: 0;
   margin: 0;
   padding: 0;
   font-size: 13px;
   cursor: pointer;
   opacity: 0;
   filter: alpha(opacity=0);
   float: left;
   }
   .uploadFile, .uploadFile1 {
     background-color: #fff !important;
    border: 0px;
    float: left;
    position: absolute;
    margin-left: 20px;
    line-height: 30px;
    right:28px;
   }
   .progress1 {
   height: 3px !important;
   margin-bottom: 0px !important;
   overflow: hidden  !important;
   background-color: #f5f5f5 !important;
   border-radius: 4px !important;
   -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.1) !important;
   box-shadow: inset 0 1px 2px rgba(0,0,0,.1) !important;
   position: relative !important;
   top: -80px !important;
   width: 65% !important;
   margin: 0 auto !important;
   }
   .form-wizard .steps > li > a.step > .number {
   z-index: 9 !important;
   position: relative !important;
   border:3px solid #dfdfdf !important;
   }
   .fa-times {
  color: red;
  }
  .fa-check {
  color: green;
  }
   .form-wizard .steps > li > a.step > .desc{display:block !important;}
   

</style>
@stop
@section('userscript') 
{{HTML::script('js/plugins/validator/formValidation.min.js')}}
{{HTML::script('js/plugins/validator/validator.bootstrap.min.js')}}
{{HTML::script('js/plugins/validator/jquery.bootstrap.wizard.min.js')}}
<script src="{{ URL::asset('/assets/global/plugins/list.min.js') }}"></script> 
<script type="text/javascript">
$(function () {
       var sellerId = $('#legal_id').val();
   
       if (sellerId > 0)
       {
           $('a.thumbnail.active img').trigger('click');
           $('#continue1').trigger('click');
       }
       $('.progress-bar-success').width('33.33%');
       console.log('we are are areraeraeraer');
       var options = {
           valueNames: ['channel_name']
       };
       var userList = new List('channel_names', options);
   });
   var cahnnel_Id = '';
   $('#continue1').click(function () {
       if ($('a.thumbnail.active').length == 1)
       {
           $('#tab2').show();
           $('#tab1').hide();
           $('.progress-bar-success').width('66.66%');
           $('.alert.alert-channel-selection').hide();
           $('li.active').removeClass('active');
           $('[href="#tab1"]').parent('li').addClass('active');
           $('[href="#tab2"]').parent('li').addClass('active');
           $('#tab1').removeClass('active');
           $('#tab2').addClass('active');
       } else {
           $('.alert.alert-channel-selection').show();
       }
   });
   
   $('.mp_logos').click(function () { 
       $('a.thumbnail').removeClass('active');
       $(this).parent('a').addClass('active');
       var lp_id = $(this).attr('itemid');
       var img = $('#lp_logo'+lp_id).attr('src');
       var lp_name = $('#lp_name'+lp_id).text();
       var desc = $('#lp_desc'+lp_id).val();
       console.log(desc);
       console.log(lp_name);
       console.log(img);
       $.ajax({
           url: '/warehouse/getLpWarehouses/'+lp_id,
           type: 'get',
           success: function (data) {
               //console.log(data);
               //var response = $.parseJSON(data);
               var i = 1;
               var response = data;
               var tableData='';
               var channelName = '';
               $.each(response, function (index, value) {
                console.log('value.existing_id:'); console.log(value.existing_id);
                if(value.existing_id != null){
                  var status = 'Mapped';
                   var chanenlImage = '<img src="' + img + '" /><h4 style="text-transform:capitalize;">' + lp_name + '</h4>';
                     tableData += '<tr> <td></td><td> '+i+'</td><td id="wh_name'+i+'">'+value.lp_wh_name +'</td><td id="wh_id'+i+'">'+value.lp_wh_id +'</td><td id="address'+i+'">'+value.address1 +', ' + value.address2 + ', '+value.city + ', '+value.state+', '+value.pincode+'</td><td id="city'+i+'">'+value.city +'</td><td id="status'+i+'">'+status+'</td></tr>'
                   $("#logo").attr('src',img);
                   $("#logo_name").html(lp_name);
                   $("#desc").html(desc);
                   $("#logo2").attr('src',img);
                   $("#logo_name2").html(lp_name);
                   $("#desc2").html(desc);
                   
                 }
                 else{
                  var status = 'Not Mapped';
                  var chanenlImage = '<img src="' + img + '" /><h4 style="text-transform:capitalize;">' + lp_name + '</h4>';
                     tableData += '<tr> <td><input name="checkbox'+i+'" type="checkbox" class="checkboxes"/></td><td> '+i+'</td><td id="wh_name'+i+'">'+value.lp_wh_name +'</td><td id="wh_id'+i+'">'+value.lp_wh_id +'</td><td id="address'+i+'">'+value.address1 +', ' + value.address2 + ', '+value.city + ', '+value.state+', '+value.pincode+'</td><td id="city'+i+'">'+value.city +'</td><td id="status'+i+'">'+status+'</td></tr>'
                   $("#logo").attr('src',img);
                   $("#logo_name").html(lp_name);
                   $("#desc").html(desc);
                   $("#logo2").attr('src',img);
                   $("#logo_name2").html(lp_name);
                   $("#desc2").html(desc);
               }
               i++;
               });
               console.log('i :' +i);
               $('#count').attr('value',i);
               $(".channelname").html(channelName + ' ' + 'Marketplace');
               $("#warehouse_table").html(tableData);
               }

       });
   });
  
    $('[class="upload"]').keyup(function(){
       validateFields();
    });
    var selected_count = $('#selected_count').val();
    var i = 1;
    function validateFields(sno){
      console.log('in validate');
        var tin_number = document.getElementById("tin_number["+sno+"]").value;
        var tinProof = document.getElementById("tinProof["+sno+"]").value;
        var apobProof = document.getElementById("apobProof["+sno+"]").value;
        console.log(tin_number); console.log(tinProof); console.log(apobProof);
        if (tin_number != '' && tinProof != '' && apobProof != '') {
          document.getElementById('span['+sno+']').innerHTML = "<i class='fa fa-check'></i>";
          $('#saveForm').removeAttr('disabled');
        }
        else{
          document.getElementById('span['+sno+']').innerHTML = "<i class='fa fa-times'></i>";
          $('#saveForm').attr('disabled',true); 
        }
    }

   $('#check_continue').click(function (){
      var check_result = $('input:checkbox').is(':checked');
      if (check_result) {
      $('#tab2').hide();
      $('.progress-bar').width('100%');
      $('#tab3').show();
      var i = $('#count').val();
      var myArray = new Array(i);
      var tableText = '';
      var sno = 1;
      //var selected_Count = 0;
      for ( var j = 1; j < i; j++) { 
        var check=$('input:checkbox[name=checkbox'+j+']').is(':checked');
        console.log(check);      
        if(check==true)
        {    myArray[j]=$('input:checkbox[name=checkbox'+j+']').val();
             var address = $('#address'+j+'').text();
             var city = $('#city'+j+'').text();
             var wh_name = $('#wh_name'+j+'').text();
             var wh_id = $('#wh_id'+j+'').text();
             console.log('warehouse:'+wh_name);
             console.log('sno:'+sno);
             tableText += '<tr><td><input type="hidden" name="wh_id[]" value="'+wh_id+'" /> '+sno+'</td><td><strong>'+wh_name+'</strong><br />'+address+'<br /><strong>'+city+'</strong></td><td><div class="row"><div class="col-md-12"><div class="form-group"><label class="control-label">TIN Number <span class="required" aria-required="true">* </span></label><input type="text" name="tin_number[]" onfocusout="validate('+sno+');" id="tin_number['+sno+']" data-required="1" class="form-control"></div></div></div><div class="row"><div class="col-md-12"><div class="fileUpload btn btn-primary" style="padding:7px 38px;"> <span>Upload TIN Proof</span><input name="tinProof[]" id="tinProof['+sno+']" onfocusout="validate('+sno+');" type="file" class="upload" /></div><input class="uploadFile" id="uploadFile['+sno+']" placeholder="No File Choosen" disabled="disabled" /><span style="float:right; margin-top:-20px;"><span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></span></div></div><div class="row" style="margin-top:15px;"><div class="col-md-12"><div class="fileUpload btn btn-primary"> <span>Upload APOB Document</span><input onfocusout="validate('+sno+');" id="apobProof['+sno+']" name="apobProof[]" type="file" class="upload" /></div><input class="uploadFile1" id="uploadFile1['+sno+']" placeholder="No File Choosen" disabled="disabled" /><span style="float:right; margin-top:-20px;"><span data-original-title="Tooltip in top" data-placement="top" class="tooltips"><i class="fa fa-question-circle-o"></i></span></span></div></div></td><td class="actionss"><span id="span['+sno+']"><i class="fa fa-times"></i></span></td></tr>'
        sno++;
        //tinNumber(sno);
        }
        
    }
    $('#selected_count').attr('value',sno);
    $("#document_table").html(tableText); 
    }
    else{
        alert('Please select atleast one Warehouse to Proceed');
       $('#tab2').show();
       $('#tab3').hide();
    }   
   });
   function validate(sno)
   {
    validateFields(sno);
    getFileName(sno);
  }

   $('#saveForm').click(function (){
    var selected_count = $('#selected_count').val();
    console.log('selected_count:'+selected_count);
    var i = 1;
    var error = 0;
    for(i; i<selected_count; i++){
      var tin_number = document.getElementById("tin_number["+i+"]").value;
      var tinProof = document.getElementById("tinProof["+i+"]").value;
      var apobProof = document.getElementById("apobProof["+i+"]").value;
      var myspan = document.getElementById('span['+i+']');
      if (tin_number != '' && tinProof != '' && apobProof != '') {
        myspan.innerHTML = "<i class='fa fa-check'></i>";
        error = 0;
      }
      else{
        myspan.innerHTML = "<i class='fa fa-times'></i>";
        error = error + 1;
      }
    }
if(error == 0){
    $('#saveForm').removeAttr('disabled');
    var form = document.forms.namedItem("doc_form"); 
    var formdata = new FormData(form);
    //console.log(form);
    
    $.ajax({
        url: '/warehouse/saveWarehouse',
        data: formdata,
        type: $(form).attr('method'),
        processData :false,
        contentType:false,
        success: function (result)
        {
          var response = result;
          if(response.status == true){
            alert(response.message);
            window.location = "/warehouse";
          }

          else{
            alert(response.message);
            $('#saveForm').attr('disabled',true);
          }
        }
    });
  }
  else{
    alert('Please fill all the details');
    $('#saveForm').attr('disabled',true);
  }
  });
</script>

<script type="text/javascript">
   jQuery(document).ready(function () {
       Metronic.init(); // init metronic core componets
       Layout.init(); // init layout
       Demo.init(); // init demo features
       FormWizard.init();
       QuickSidebar.init(); // init quick sidebar
       // Index.init(); // init index page
       Tasks.initDashboardWidget(); // init tash dashboard widget 
   
       console.log('we are herere');
       var pathname = window.location.pathname.split("/");
       var filename = pathname[pathname.length - 1];
       if (filename > 0)
       {
           $('[href="#tab2"]').parent().addClass('active');
           $('.progress-bar').width('66%');
           $('#tab2').show();
           $('#tab3').hide();
       }
   });
   
   $(document).ready(function(){
     var active = $('#active').val();
     console.log(active);
     if(active == 1){
     $('#tab1').hide();
     $('#tab2').show();
     $('.progress-bar').width('66.66%');
     }
     else{
     $('#tab1').show();
     $('#tab2').hide();
     }
   });
   
   
</script> 
<script type="text/javascript">
   $.ajaxSetup({
       headers:
       {
           'X-CSRF-Token': $('input[name="_token"]').val()
       }
   });
   
</script> 
<script>
function getFileName(sno){
document.getElementById("tinProof["+sno+"]").onchange = function () {
document.getElementById("uploadFile["+sno+"]").value = getFile($(this).attr("id"));
}
document.getElementById("apobProof["+sno+"]").onchange = function () {
document.getElementById("uploadFile1["+sno+"]").value = getFile($(this).attr("id"));

}
}
function getFile(id){
     var str = '';
     var files = document.getElementById(id).files;
     for (var i = 0; i < files.length; i++){
         str += files[i].name;
     }
     return str;
}
</script> 
<script type="text/javascript">
$('#back1').click(function (){
   $('#tab2').removeClass('active');
   $('#tab2').hide();
   $('#tab1').show();
   $('#tab1').addClass('active');
   $('[href="#tab2"]').parent('li').removeClass('active');
   $('.progress-bar').width('33.33%');
});
$('#back2').click(function (){
    $('#tab3').hide();
    $('#tab2').show();
    $('.progress-bar').width('66.66%');
});
  
$('.tab1').click(function() { return false; });
$('.tab2').click(function() { return false; });
$('.tab3').click(function() { return false; });
</script>

@stop
@extends('layouts.footer')