@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<ul class="page-breadcrumb breadcrumb">
<li><a href="javascript:;">Home</a><i class="fa fa-angle-right"></i></li>
<li><a href="javascript:;">Service Request</a><i class="fa fa-angle-right"></i></li>
<li class="active">Inbound</li>
</ul>

<div class="row">
<div class="col-md-12">
<div class="portlet box">
<div class="portlet-body">
<h3 class="form-section">INBOUND REQUEST</h3>

<div class="row">
<div class="col-md-4">
<div class="form-group">
<label class="control-label">Pickup Location</label>
<select class="form-control" name="Pickup Location">
<option value="">Street no.8 Banjarahills, hyd.</option>
</select>
</div>
</div>
<div class="col-md-4">
<div class="form-group">
<label class="control-label">Delivery Location</label>
<select class="form-control" name="Delivery Location">
<option value="">Street no.8 Jublihills, hyd.</option>
</select>
</div>
</div>
<div class="col-md-4">
<div class="form-group">
<label class="control-label">Select Available Slots</label>
<select class="form-control" name="Select Available Slots">
<option value="">9:00 AM - 9:00 PM | 26-06-2016</option>
</select>
</div>
</div>
</div>

<div class="row">
<div class="col-md-4">
<div class="form-group">
<label class="control-label">STN Number</label>
<input type="text" class="form-control" placeholder="46456HJK987">
</div>
</div>
<div class="col-md-4">

<div class="form-group">
<label class="control-label">Upload STN Document</label>
<div class="fileinput fileinput-new" data-provides="fileinput">
<div class="input-group input-medium" style="width:247px !important;">
<div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
<i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
</span>
</div>
<span class="input-group-addon btn btn green-meadow btn-file">
<span class="fileinput-new">
Select file </span>
<span class="fileinput-exists">
Change </span>
<input type="file" name="files">
</span>
</div>
</div>
</div>


</div>

</div>




<div class="row">

        <div class="col-md-4 col-sm-12">
           <h3 class="form-section">Select Products</h3>
            <div class="portlet light tasks-widget">
                <div class="portlet-title">
                
<div class="row">
<div class="col-md-6">
<div class="inputs">
<div class="portlet-input input-small input-inline">
<div class="input-icon right">
<i class="icon-magnifier"></i>
<input type="text" class="form-control form-control-solid" placeholder="search...">
</div>
</div>
</div>
</div>
<div class="col-md-6">
<div class="btn-group pull-right">

<a href="#aa" data-toggle="tab" class=" btn green pull-right dropdown-toggle filters">Filter <i class="fa fa-angle-down"></i></a>
<div class="tabbable">
<div class="tab-content">
<div class="tab-pane filtertab" id="aa">

<div class="tabbable tabs-left" style="width:330px; padding-top:50px;">
<ul class="nav nav-tabs">
<li class="active"><a href="#a" data-toggle="tab">Brand</a></li>
<li><a href="#b" data-toggle="tab">Category</a></li>
<li><a href="#c" data-toggle="tab">Quantity</a></li>
</ul>
<div class="tab-content">
<div class="tab-pane active" id="a">
<div class="scroller" style="height: 350px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">

<div class="row">
<div class="col-md-12">
<div class="form-group form-md-line-input has-success">
<div class="input-icon right">
<input type="text" class="form-control" placeholder="Search">
<i class="icon-magnifier"></i>
</div>
</div>
</div>
</div>
<div class="row">
<div class="col-md-12">

<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" class="uncheck_brands" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" class="uncheck_brands"  value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" class="uncheck_brands" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" class="uncheck_brands"  value="option1"> Flipkart </label>
</div>
<br>

<button type="button" class="btn btn-success" id="clear_all_brands">Clear All</button>
<button type="button" class="btn btn-success" id="brand_apply">Apply</button>
</div>
</div>

</div>
</div>
<div class="tab-pane" id="b">
<div class="scroller" style="height: 350px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">

<div class="row">
<div class="col-md-12">
<div class="form-group form-md-line-input has-success">
<div class="input-icon right">
<input type="text" class="form-control" placeholder="Search">
<i class="icon-magnifier"></i>
</div>
</div>
</div>
</div>
<div class="row">
<div class="col-md-12">

<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> Flipkart </label>
</div>
<br>

<button type="button" class="btn btn-success">Clear All</button>
<button type="button" class="btn btn-success" id="brand_apply1">Apply</button>
</div>
</div>

</div>
</div>
<div class="tab-pane" id="c">
<div class="scroller" style="height: 350px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">

<div class="row">
<div class="col-md-12">
<div class="form-group form-md-line-input has-success">
<div class="input-icon right">
<input type="text" class="form-control" placeholder="Search">
<i class="icon-magnifier"></i>
</div>
</div>
</div>
</div>
<div class="row">
<div class="col-md-12">

<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 10 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 20 </label>
</div>
<div class="checkbox-list">
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 30 </label>
<label class="checkbox-inline">
<input type="checkbox" id="inlineCheckbox1" value="option1"> < 40 </label>
</div>


<br>

<button type="button" class="btn btn-success">Clear All</button>
<button type="button" class="btn btn-success" id="brand_apply2">Apply</button>
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
</div>
</div>   
                    
                </div>
                <div class="portlet-body">
				
<div class="scroller" style="height: 442px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list1">   
             
<table class="table table-striped table-hover" id="sample_2">
<thead>
<tr>
<th class="table-checkbox"><input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes"/></th>
<th>&nbsp;</th>
<th>Product Details</th>
<th>Avl Qty</th>
</tr>
</thead>
<tbody>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td>25,890</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 7 <br> MRP : 28,000</td>
<td>25,890</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td>25,890</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 7 <br> MRP : 28,000</td>
<td>25,890</td>
</tr>

<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td>25,890</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 7 <br> MRP : 28,000</td>
<td>25,890</td>
</tr>

<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td>25,890</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 7 <br> MRP : 28,000</td>
<td>25,890</td>
</tr>

<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td>25,890</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 7 <br> MRP : 28,000</td>
<td>25,890</td>
</tr>

<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td>25,890</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 7 <br> MRP : 28,000</td>
<td>25,890</td>
</tr>

<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td>25,890</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 7 <br> MRP : 28,000</td>
<td>25,890</td>
</tr>

</tbody>
</table>
</div>                    
                </div>
            </div>
        </div>

<div class="col-md-1 col-sm-12" style="position:relative; top:300px;">
<a href="#" class="btn btn-icon-only green moveLeft"><i class="fa fa-angle-double-right"></i></a>
</div>

        
        <div class="col-md-7 col-sm-12">
            <h3 class="form-section">Selected Products</h3>
            <div class="portlet light tasks-widget">
                
                <div class="portlet-body">
                
<div class="scroller" style="height: 500px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list2">                
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover" id="sample_3">
<thead>
<tr>
<th>S No</th>
<th>Product Details</th>
<th>MRP</th>
<th>Avl Qty</th>
<th>Qty</th>
<th>&nbsp;</th>
</tr>
</thead>
<tbody>


<tr class="odd gradeX list-head">
<td width="10%">&nbsp;</td>
<td width="45%"><input type="text" class="form-control input-sm" ></td>
<td width="10%"><input type="text" class="form-control input-sm" ></td>
<td width="15%">
<select class="form-control input-sm">
<option>< 10</option>
<option>< 20</option>
<option>< 30</option>
<option>< 40</option>
<option>< 50</option>
</select>
</td>
<td width="10%"><input type="text" class="form-control input-sm input-xsmall"></td>
<td width="10%"><button type="button" class="btn btn-default">Reset</button></td>
</tr>
<!--<tr class="odd gradeX list-head">
<td colspan="7" class="text-center">Total : 10</td>
</tr>
-->
</tbody>
</table>



</div>                    
                    
                </div>
            </div>
            <!-- END PORTLET-->
        </div>
    </div>

<div class="row">
<div class="col-md-12 text-center">
<button type="button" class="btn green-meadow">Create Inbound</button>
</div>
</div>
			

<div class="row">
<div class="col-md-12 text-center">
&nbsp;
</div>
</div>


		</div>

</div>
</div>
</div>

@stop


@section('style')
<style type="text/css">
/* custom inclusion of right, left and below tabs */

.dataTables_filter{display:none;}
.dataTables_length{display:none;}
.dataTables_paginate .paging_bootstrap_number{display:none;}
#sample_2_paginate{display:none;}
#sample_3_paginate{display:none;}
.dataTables_info{display:none;}

.tabs-below > .nav-tabs,
.tabs-right > .nav-tabs,
.tabs-left > .nav-tabs {
  border-bottom: 0;
}

.tab-content > .tab-pane,
.pill-content > .pill-pane {
  display: none;
}
.no-search .select2-search {
    display:none
}

.tab-content > .active,
.pill-content > .active {
  display: block;
}

.tabs-below > .nav-tabs {
  border-top: 1px solid #ddd;
}

.tabs-below > .nav-tabs > li {
  margin-top: -1px;
  margin-bottom: 0;
}

.tabs-below > .nav-tabs > li > a {
}

.tabs-below > .nav-tabs > li > a:hover,
.tabs-below > .nav-tabs > li > a:focus {
  border-top-color: #ddd;
  border-bottom-color: transparent;
}

.tabs-below > .nav-tabs > .active > a,
.tabs-below > .nav-tabs > .active > a:hover,
.tabs-below > .nav-tabs > .active > a:focus {
  border-color: transparent #ddd #ddd #ddd;
}

.tabs-left > .nav-tabs > li,
.tabs-right > .nav-tabs > li {
  float: none;
}

.tabs-left > .nav-tabs > li > a,
.tabs-right > .nav-tabs > li > a {
  min-width: 74px;
  margin-right: 0;
  margin-bottom: 3px;
}

.tabs-left > .nav-tabs {
  float: left;
  margin-right: 19px;
  border-right: 1px solid #ddd;
}

.tabs-left > .nav-tabs > li > a {
  margin-right: -1px;
}

.tabs-left > .nav-tabs > li > a:hover,
.tabs-left > .nav-tabs > li > a:focus {
  border-color: #eeeeee #dddddd #eeeeee #eeeeee;
}

.tabs-left > .nav-tabs .active > a,
.tabs-left > .nav-tabs .active > a:hover,
.tabs-left > .nav-tabs .active > a:focus {
  border-color: #ddd transparent #ddd #ddd;
  *border-right-color: #ffffff;
}

.tabs-right > .nav-tabs {
  float: right;
  margin-left: 19px;
  border-left: 1px solid #ddd;
}

.tabs-right > .nav-tabs > li > a {
  margin-left: -1px;
}

.tabs-right > .nav-tabs > li > a:hover,
.tabs-right > .nav-tabs > li > a:focus {
  border-color: #eeeeee #eeeeee #eeeeee #dddddd;
}

.tabs-right > .nav-tabs .active > a,
.tabs-right > .nav-tabs .active > a:hover,
.tabs-right > .nav-tabs .active > a:focus {
  border-color: #ddd #ddd #ddd transparent;
  *border-left-color: #ffffff;
}
.v-center {
  min-height:200px;
  display: flex;
  justify-content:center;
  flex-flow: column wrap;
}

.portlet.light {
    padding: 0px !important;
    background-color: #fff;
}

</style>
<link href="{{ URL::asset('assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('userscript')
<script type="text/javascript">
count=0;

$(".filters").click(function(){ 

if(count%2==0)
{
$(".tabbable").show();
}
else
{
$(".tabbable").hide();
} 


count++;
});
$("#brand_apply").click(function(){
	$(".tabbable").hide();
	});
	$("#brand_apply1").click(function(){
	$(".tabbable").hide();
	});
	$("#brand_apply2").click(function(){
	$(".tabbable").hide();
	});
	$("#clear_all_brands").click(function(){
		
		  $('.uncheck_brands').removeAttr('checked');
		});
		

$(document).ready(function(e) {
	var prod_tr = '<tr class="odd gradeX">\
			<td data-key></td>\
			<td  data-val="product_details"></td>\
			<td  data-val="price"></td>\
			<td data-val="qty"></td>\
			<td><input type="text" class="form-control input-sm" value="1"></td>\
			<td><a href="" class="btn btn-icon-only default delList"><i class="icon-trash"></i></a></td>\
			</tr>';
			
	$('#sample_3').on('click', '.delList', function(e){
		e.preventDefault();
		$(this).closest('tr').remove();
		$('#sample_3').find('[data-key]').each(function(i){
			$(this).text( ++i );
		});
	});
	

	
    $('.moveLeft').click(function(e){
		e.preventDefault();
		var el = $('#sample_2').find('input:checkbox:checked:not(".group-checkable")');
		el.each(function(index, element) {
            var tr = $(this).closest('tr');
			var data = {};
			var product_details = tr.find('td:eq(2)').text();
			data.price = product_details.split(':')[1].replace('MRP', '').trim();
			data.product_details = product_details.split(':')[0].replace('MRP', '').trim();
			data.qty = tr.find('td:eq(3)').text();
			var new_tr = $(prod_tr);
			var sNo = $('#sample_3').find('tbody').find('tr:not(".list-head")').length + 1;
			new_tr.find('td[data-key]').text( sNo );
			new_tr.find('td[data-val]').each(function(){
				var index = $(this).data('val');
				$(this).text( data[index] || '' );
			});
			$('#sample_3').append(new_tr);
			tr.remove();
        });
	});
});
	
</script>


<!--<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
--><script src="{{ URL::asset('assets/global/plugins/datatables/media/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-file-upload/js/jquery.fileupload-audio.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-file-upload/js/jquery.fileupload-video.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-file-upload/js/jquery.fileupload-ui.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/table-managed.js') }}" type="text/javascript"></script>

<script>
jQuery(document).ready(function() {    
   Metronic.init(); // init metronic core componets
   Layout.init(); // init layout
   Demo.init(); // init demo features
   QuickSidebar.init(); // init quick sidebar
   TableManaged.init();
    //Index.init(); // init index page
	//FormFileUpload.init();
 Tasks.initDashboardWidget(); // init tash dashboard widget  
 

});
</script>


@stop
@extends('layouts.footer')