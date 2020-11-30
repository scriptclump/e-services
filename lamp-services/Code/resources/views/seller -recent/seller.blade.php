@extends('layouts.default')

@extends('layouts.header')

@extends('layouts.sideview')

@section('content')




<div class="page-head">
<div class="page-title">
<h1>Add Seller<small></small></h1>
</div>
</div>
<ul class="page-breadcrumb breadcrumb">
<li><a href="javascript:void(0);">Home</a><i class="fa fa-circle"></i></li>
<li><a href="javascript:void(0);">Accounts</a><i class="fa fa-circle"></i></li>
<li class="active">Wharehouses</li>
</ul>


<div class="row">
<div class="col-md-12">
<div class="portlet box" id="form_wizard_1">
      
<div class="portlet-body form">

<form action="#" class="" id="submit_form" method="get">
    
<div class="form-wizard">
<div class="form-body" style="min-height:612px;">
<ul class="nav nav-pills nav-justified steps">
<li>
<a href="#tab1" data-toggle="tab" class="step">
<span class="number">
<i class="fa fa-building-o"></i> </span>
<span class="desc">
Seller Mapping</span>
</a>
</li>
<li>
<a href="#tab2" data-toggle="tab"  class="step">
<span class="number">
<i class="fa fa-gear"></i> </span>
<span class="desc">
Configure Seller</span>
</a>
</li>
<li>
<a href="#tab3" data-toggle="tab" class="step active">
<span class="number">
<i class="fa fa-check"></i> </span>
<span class="desc">
Complete </span>
</a>
</li>
</ul>
<div id="bar" class="progress progress-striped" role="progressbar">
<div class="progress-bar progress-bar-success">
</div>
</div>
<div class="tab-content">
<div class="alert alert-danger display-none">
<button class="close" data-dismiss="alert"></button>
You have some form errors. Please check below.
</div>
<div class="alert alert-success display-none">
<button class="close" data-dismiss="alert"></button>
Your form validation is successful!
</div>
<div class="tab-pane active" id="tab1">


<h3 class="form-section">Select Fulfilment Center</h3>

<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Select Fulfilment Center</label>




<select class="form-control" name="wharehouseId">

@foreach($warehouse_list as $warehouse_lists)

<option value="{{$warehouse_lists->lp_wh_id}}">{{$warehouse_lists->lp_legal_name}},{{$warehouse_lists->lp_wh_name}},{{$warehouse_lists->state}}</option>

@endforeach

</select>



</div>
</div>
</div>

<div class="row">
<div class="col-md-10"><h3 class="">Select Marketplace</h3></div>
<div class="col-md-2 pull-right">
<div class="form-group">
<div class="input-icon right margin-top-10">
<i class="fa fa-search"></i>
<input type="text" class="form-control" placeholder="Search here">
</div>
</div>
</div>
</div>




<div class="row">

 @foreach($channel_info as $channel_infos)   
 
 <div class="col-sm-6 col-md-2 ">
     
<span class="thumbnail" id="thumbselector">
<img  class="mp_logos btn" src="{{$channel_infos->channel_logo}}" itemid="{{$channel_infos->channel_id}}" />
</span>
 
 </div>


@endforeach

</div>												

<div class="row" style="margin-top:40px;">
<div class="col-md-12 text-center">
   
 <!--<a href="javascript:;" class="btn blue button-next" disabled id="test">Continue <i class="m-icon-swapright m-icon-white"></i></a>-->
    
    <a><input type="button" id="myBtn" value="Continue" disabled class="btn blue button-next"><i class="m-icon-swapright m-icon-white"></i></a>
</div>
</div>
												

</div>
        
    
<div class="tab-pane" id="tab2">
<h3 class="form-section">Add Locations</h3>
<div class="row">

<div class="col-md-4">

<div class="note note-info">

<div class="text-center image-center"></div>
`
<p class="channelDescription">

</p>

</div>
</div>
<div class="col-md-8">
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Channel Referance Name</label>
<input type="text" class="form-control" placeholder="Channel Referance Name" name="channelreferancename">
</div>
</div>
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Description</label>
<input type="text" class="form-control" placeholder="Description" name="description">
</div>
</div>
</div>
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Market Place User Name</label>
<input type="text" class="form-control" placeholder="Market Place User Name" name="marketplaceusername">
</div>
</div>
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Market Place Password</label>
<input type="text" class="form-control" placeholder="Market Place Password" name="password">
</div>
</div>
</div>
    
<div class="row">

<div class="input_fields_wrap"></div>

</div>
    <div class="input_hidden_wrap"></div>   
</div>
</div>

<div class="row" style="margin-top:40px;">
<div class="col-md-12 text-center">
    <a href="javascript:void(0);" class="btn blue goBack" ><i class="m-icon-swapleft m-icon-white"></i> Back </a>
<!--<input type="submit" value="Done" class="btn blue button-next submit" /> -->

<input type="submit" value="Done" class="btn blue button-next submit" />
<i class="m-icon-swapright m-icon-white"></i>

</div>
</div>

</div>
<div class="tab-pane" id="tab3">

<div class="row">
<div class="col-md-12">

<h3 class="block" style="text-align:center;">You have succesfully registerd <span class="font-green sellername"></span> as a seller for <span class="font-green channelname"></span>.Now you can see all the orders and process them.</h3>

</div>
</div>



</div>
</div>
</div>

</div>
</form>
</div>
      
</div>
</div>
</div>
<script src="assets/global/plugins/jquery.min.js"></script> 


<script type="text/javascript">

$(function(){
    $('#thumbselector img').click(function() {
      $('#thumbselector .active').removeClass('active');
      $(this).toggleClass('active');
    });
});


$('.mp_logos').click(function(){    
    
 
    var channelId = $(this).attr('itemid');
   
       if(channelId!='')
       {
           document.getElementById("myBtn").disabled = false;
          
       }
    $.get( "sellerconfig/"+channelId, function( data ) {
        var response = $.parseJSON(data);
        var wrapper = $(".input_fields_wrap");
        
        var channelKeys='';
       
        $.each(response, function (index, value) {
            var fieldName = value.field_name.split("_").join(" ");
            //var fieldName = value.field_name;
             channelKeys += '<div class="col-md-6"><label class="control-label">'+fieldName+'</label><input type="text" class="form-control" name="'+value.field_code+'" placeholder="'+ fieldName +'" style="width: 100%;"></div>';
//        console.log(temp);
                            
        });
        $(wrapper).html(channelKeys);
        
      var getChannelId = '<div class="col-md-6"><input type="hidden" name="channelId" value="'+channelId+'"></div>'
        $(".input_hidden_wrap").html(getChannelId);
        
    });
    
    $.ajax({
        url:'channelImage',
        data:'channelId='+channelId,
        type:'get',
        success:function(data){
            var response = $.parseJSON(data);
            
            var channelName = '';
          $.each(response,function(index,value){
              
       var chanenlImage ='<img src="'+value.channel_logo+'" /><h4>'+value.channnel_name+'</h4>';
         channelName = value.channnel_name;
           
           var channel_description = value.channel_description;
            $(".image-center").html(chanenlImage);
           $(".channelDescription").html(channel_description);  
           
          });
         
          
          $(".channelname").html(channelName +' ' + 'Marketplace');
        }
    })
    
});

$('.submit').click(function(){
var datastring = $("#submit_form").serialize();
//alert(datastring);
alert(datastring);
$.ajax({
   url:'sellerConfigInsertings',
   data:datastring,
   type:'get',
   success:function(data){
      var channel_referance_name ='';
       for(var i=0; i<data.length;i++)
       {
           channel_referance_name = data[i].channel_referance_name;
       }
       $(".sellername").html(channel_referance_name);
   }
   
});

});

$(".goBack").click(function(){
    $('a[href="#tab1"]').tab('show')
})


</script>
<style>
span .active {
    border:1px solid #337AB7;
}
</style>
@stop
@extends('layouts.footer')