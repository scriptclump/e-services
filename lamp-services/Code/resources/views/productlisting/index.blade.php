@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
<div class="col-md-12 col-sm-12">
<div class="portlet light tasks-widget" style="height:auto;">
<div class="portlet-title">
<div class="caption"> CREATE PRODUCT </div>
<div class="tools"> <span class="badge bg-blue"><a class="fullscreen" data-toggle="tooltip" title="" style="color:#fff;" data-original-title="Hi, This is help Tooltip!"><i class="fa fa-question"></i></a></span> </div>
</div>
<div class="portlet-body">
<div id="form-wiz">
<div class="tabbable-line">
<ul class="nav nav-tabs ">
<li class="active"><a href="#tab11" data-toggle="tab">Product</a></li>
<li><a href="#tab22" data-toggle="tab">Attributes </a></li>
<li><a href="#tab33" data-toggle="tab">Images </a></li>
<li><a href="#tab44" data-toggle="tab">SEO </a></li>
</ul>



<div class="tab-content">
<div class="tab-pane active" id="tab11">
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Category <span class="required">*</span></label>
<select class="form-control">
<option value="">Electronic and Hardware</option>
<option>option</option>
<option>option</option>
</select>
</div>
</div>
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Sub Category <span class="required">*</span></label>
<select class="form-control">
<option value="">Home Appliances</option>
<option>option</option>
<option>option</option>
</select>
</div>
</div>
</div>
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Product Class <span class="required">*</span></label>
<select class="form-control">
<option value=""></option>
<option>option</option>
<option>option</option>
</select>
</div>
</div>
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Product Title <span class="required">*</span></label>
<input type="text" class="form-control" />
</div>
</div>
</div>
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Product Name <span class="required">*</span></label>
<input type="password" class="form-control" />
</div>
</div>
<div class="col-md-6">
<div class="form-group">
<label class="control-label">EAN/UPC <span class="required">*</span></label>
<input type="password" class="form-control" />
</div>
</div>
</div>
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label">SKU Code <span class="required">*</span></label>
<input type="password" class="form-control" />
</div>
</div>
<div class="col-md-6">
<div class="form-group">
<label class="control-label">MRP <span class="required">*</span></label>
<input type="password" class="form-control" />
</div>
</div>
</div>
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Product Description <span class="required">*</span></label>
<textarea rows="4" cols="50" class="form-control">
</textarea>
</div>
</div>
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Product Type <span class="required">*</span></label>
<select class="form-control">
<option value="">Packing Material</option>
<option>option</option>
<option>option</option>
</select>
</div>
</div>
</div>
<div class="row" style="margin-top:50px;">
<hr />
<div class="col-md-12 text-center"> <a class="btn green-meadow" href="">Save & Continue</a>  <a class="btn green-meadow" href="">Cancel</a> </div>
</div>
</div>

<div class="tab-pane" id="tab22">
<div class="row">
<div class="col-md-12">
    <div class="contenttab-line"> Internet Connectivity</div>
    <div class="row prodatt1">
    <div class="form-body">
    <div class="form-group">
        <div class="row">
    <label class="col-md-3 control-label">Network Type</label>
    <div class="col-md-4 connectselect">
    <select class="form-control">
    <option value=""></option>
    <option>option</option>
    <option>option</option>
    </select>
    </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
    <label class="col-md-3 control-label">Preinstalled Browser</label>
    <div class="col-md-4 connectselect">
    <select class="form-control">
    <option value=""></option>
    <option>option</option>
    <option>option</option>
    </select>
    </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
    <label class="col-md-3 control-label">Navigation Technology</label>
    <div class="col-md-4 connectselect">
    <select class="form-control">
    <option value=""></option>
    <option>option</option>
    <option>option</option>
    </select>
    </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
    <label class="col-md-3 control-label">Bluetooth</label>
    <div class="col-md-4 connectselect">
    <select class="form-control">
    <option value=""></option>
    <option>option</option>
    <option>option</option>
    </select>
    </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
    <label class="col-md-3 control-label">WIFI</label>
    <div class="col-md-4 connectselect">
    <select class="form-control">
    <option value=""></option>
    <option>option</option>
    <option>option</option>
    </select>
    </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
    <label class="col-md-3 control-label">Internet Features</label>
    <div class="col-md-4 connectselect">
    <select class="form-control">
    <option value=""></option>
    <option>option</option>
    <option>option</option>
    </select>
    </div>
        </div>
    </div>
    </div>
    </div>
    <div class="contenttab-line"> Multimedia</div>
    <div class="row prodatt1">
    <div class="form-body">
    <div class="form-group">
        <div class="row">
    <label class="col-md-3 control-label">Video Recording</label>
    <div class="col-md-4 connectselect">
    <select class="form-control">
    <option value=""></option>
    <option>option</option>
    <option>option</option>
    </select>
    </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
    <label class="col-md-3 control-label">Secondary Camera Features</label>
    <div class="col-md-4 connectselect">
    <select class="form-control">
    <option value=""></option>
    <option>option</option>
    <option>option</option>
    </select>
    </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
    <label class="col-md-3 control-label">Flash</label>
    <div class="col-md-4 connectselect">
    <select class="form-control">
    <option value=""></option>
    <option>option</option>
    <option>option</option>
    </select>
    </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
    <label class="col-md-3 control-label">HD Recording</label>
    <div class="col-md-4 connectselect">
    <select class="form-control">
    <option value=""></option>
    <option>option</option>
    <option>option</option>
    </select>
    </div>
        </div>
    </div>
    </div>
    </div>
</div>
</div>
<div class="row" style="margin-top:10px;">
<hr />
<div class="col-md-12 text-center"> <a class="btn green-meadow" href="">Back</a> <a class="btn green-meadow" href="">Save & Continue</a>  <a class="btn green-meadow" href="">Cancel</a> </div>
</div>
</div>

<div class="tab-pane" id="tab33">
<div class="row">
<div class="col-md-12">

<form class="form-horizontal" role="form">
<div class="form-body">
<div class="form-group">
<label class="col-md-3 control-label">Paste Image URL</label>
<div class="col-md-6">
<input id="images" class="form-control upimg" type="text" name="img">
</div>
<div class="col-md-3">
<a href="" class="btn btn-primary">Add Image</a>
</div>
</div>
</div>
</form>

</div>                
</div>                
<div class="row">
<div class="col-md-12">
<form action="../../assets/global/plugins/dropzone/upload.php" class="dropzone " id="my-dropzone"></form>
<i class="fa fa-cloud-upload" aria-hidden="true" ></i>
</div>
</div>
<div class="row">
<div class="col-md-12">
<div class="contenttab-line"> INSTRUCTIONS</div>
<div class="imgtitle">

<p>Listings that are missing a main image will not appear in search or browse until you fix the listing.</p>
<p>Choose images that are clear, information-rich, and attractive.  </p>
<p>Images must meet the following requirements:</p>
<p>Products must fill at least 85% of the image.</p>
<p>Images must show only the product that is for sale, with few or no props and with no logos, watermarks, or inset images.</p>
<p> Images may only contain text that is a part of the product.</p>
<p> Main images must have a pure white background, must be a photo (not a drawing), and must not contain excluded accessories.</p>
<p> Images must be at least 1000 pixels on the longest side and at least 500 pixels on the shortest side to be zoom-able.</p>
<p> Images must not exceed 10000 pixels on the longest side.</p>
</div>
</div>
</div>
<div class="row" style="margin-top:10px;">
<hr />
<div class="col-md-12 text-center"> <a class="btn green-meadow" href="">Back</a> <a class="btn green-meadow" href="">Save & Continue</a>  <a class="btn green-meadow" href="">Cancel</a> </div>
</div>
</div>


<div class="tab-pane" id="tab44">
<div class="row" >
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Meta Title </label>
<input type="text" class="form-control" />
</div>
</div>
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Meta Keywords </label>
<input type="text" class="form-control" />
</div>
</div>
</div>
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Meta Description </label>
<textarea rows="4" cols="50" class="form-control">
</textarea>
</div>
</div>
</div>
<div class="row" style="margin-top:0px;">
<hr />
<div class="col-md-12 text-center"> <a class="btn green-meadow" href="">Back</a>  <a class="btn green-meadow" href="">Cancel</a> </div>
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
     #dragandrophandler
    {
        border: 2px dashed #92AAB0;
        width: 350px;
        height: 50px;
        color: #92AAB0;
        text-align: center;
        vertical-align: middle;
        padding: 10px 0px 10px 10px;
        font-size:200%;
        display: table-cell;
    }
    .progressBar {
        width: 100px;
        height: 22px;
        border: 1px solid #ddd;
        border-radius: 5px;	
        overflow: hidden;
        display:inline-block;
        margin:0px 10px 5px 5px;
        vertical-align:top;
    }

    .progressBar div {
        height: 100%;
        color: #fff;
        text-align: right;
        line-height: 22px; /* same as #progressBar height if we want text middle aligned */
        width: 0;
        background-color: #0ba1b5; border-radius: 3px; 
    }
    .statusbar
    {
        border-top:1px solid #A9CCD1;
        min-height:25px;
        width:450px;
        padding:10px 10px 0px 10px;
        vertical-align:top;
    }
    .statusbar:nth-child(odd){
        background:#EBEFF0;
    }
    .filename
    {
        display:inline-block;
        vertical-align:top;
        width:150px;
    }
    .filesize
    {
        display:inline-block;
        vertical-align:top;
        color:#30693D;
        width:80px;
        margin-left:10px;
        margin-right:5px;
    }
    .abort{
        background-color:#A8352F;
        -moz-border-radius:4px;
        -webkit-border-radius:4px;
        border-radius:4px;display:inline-block;
        color:#fff;
        font-family:arial;font-size:13px;font-weight:normal;
        padding:4px 15px;
        cursor:pointer;
        vertical-align:top
    }
   
.preview-image { display: none; height: auto; width: 200px; }
    
/*.portlet > .portlet-title { margin-bottom:0px !important;}*/
.imgborder{border:1px solid #ddd !important;}
.tabs-left.nav-tabs > li.active > a, .tabs-left.nav-tabs > li.active > a:hover > li.active > a:focus {
border-radius: 0px !important;  
}
.nav>li>a:visited{
    color:red !important;
}
tabs.nav>li>a {
    padding-left: 10px !important;
}
.note.note-success {
    background-color: #c0edf1 !important;
    border-color: #58d0da !important;
    color: #000 !important;
}
hr {
margin-top:0px !important;
margin-bottom:10px !important;
}
.portlet > .portlet-title {
    border-bottom: 0px !important;
}
    
/* SAM */
    .contenttab-line {
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
    font-weight: bold;
}
    .connectselect {
    padding-bottom: 10px;
}
    .prodatt {
    padding-left: px;
    /* padding-top: 15px; */
}
    .prodatt1 {
    padding-left: 15px;
    padding-top: 15px; 
}
.dz-default.dz-message {
    border: 2px dashed #3598dc;
    color: #3598dc;
    height: 150px;
    padding-top: 90px;
    /* padding-left: 35px; */
    text-align: center;
}
.dz-details img {
    width: 100%;
    padding-top: 15px;
}
    .imgtitle {
    padding-top: 10px;
    font-weight: 600;
}

.imgtitle p {
    font-weight: 400;
    padding-top: 5px;
    font-size: 12px;
    line-height: 4.5px;
}
    
.portlet.light {
     height: auto !important; 
     min-height: auto !important; 
   }
    
.nav-tabs > li > a, .nav-pills > li > a {
    font-weight: bold;
}
    
label {
    font-weight: normal !important;
}    
  
i.fa.fa-cloud-upload {
    color: #428bca;
    font-size: 70px;
    /* margin-top: -110px; */
    /* padding-left: 559px; */
    position: relative;
    float: left;
    left: 47%;
    top: -110px;
}
/* SAM */
</style>
@stop



@extends('layouts.footer')
