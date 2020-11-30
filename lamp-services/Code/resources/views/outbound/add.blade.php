@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')


<ul class="page-breadcrumb breadcrumb">
<li><a href="javascript:;">Home</a><i class="fa fa-circle"></i></li>
<li><a href="javascript:;">Service request</a><i class="fa fa-circle"></i></li>
<li class="">Outbound</li>
</ul>

<div class="row minheight">
<div class="col-md-12 col-sm-12">
<div class="portlet light tasks-widget">
<div class="portlet-title" style="padding:10px;">
<div class="caption">
RECALL INVENTORY
</div>
<div class="tools">


<span class="badge bg-blue"><a  class="fullscreen" data-toggle="tooltip" title="Hi, This is help Tooltip!" style="color:#fff;"><i class="fa fa-question"></i></a></span>



</div>
</div>
<div class="portlet-body">

<div class="row">
<div class="col-md-12">
<div class="portlet box">
<div class="portlet-body">

<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Fulfilment Center</label>
<select class="form-control" name="Pickup Location">
<option value="">Street no.8 Banjarahills, hyd.</option>
</select>
</div>
</div>
<div class="col-md-6">
<div class="form-group">
<label class="control-label">Delivery Location</label>
<select class="form-control" name="Delivery Location">
<option value="">Street no.8 Jublihills, hyd.</option>
</select>
</div>
</div>

</div>

<div class="row">

<div class="col-md-4 col-sm-12" style="width:390px;">
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

<div class="tabbable tabs-left" style="width:360px; padding-top:50px;">
<ul class="nav nav-tabs">
<li class="active"><a href="#a" data-toggle="tab">Brand</a></li>
<li><a href="#b" data-toggle="tab">Category</a></li>
<li><a href="#c" data-toggle="tab">Quantity</a></li>
</ul>
<div class="tab-content">
<div class="tab-pane active" id="a">
<div class="scroller" style="height: 350px;  padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">

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

<div class="row" style="margin-top:10px; margin-left:-15px; margin-bottom:5px;">
<div class="col-md-12">
<div class="caption">

<span class="caption-subject bold font-blue uppercase"> Sort By :</span>
<span class="caption-helper sorting">
<a href="#" class="active">All</a> &nbsp;&nbsp;
<a href="#" class="inactive">Sellable</a> &nbsp;&nbsp;
<a href="#" class="inactive">Damaged</a> &nbsp;&nbsp;
<a href="#" class="inactive">Qc Rejected</a> &nbsp;&nbsp;
</span>
</div>
</div>
</div>

</div>
<div class="portlet-body">

<div class="scroller" style="height: 500px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list1">   

<table class="table table-striped table-hover" id="sample_2">
<thead>
<tr>
<th class="table-checkbox"><input type="checkbox" class="group-checkable" data-set="#sample_2 .checkboxes"/></th>
<th>&nbsp;</th>
<th>Product Details</th>
<th>Status</th>
<th>Avl Qty</th>
<th style="display:none;">My Brand</th>
</tr>
</thead>
<tbody>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td><span class="font-green-sharp">Qc Rejected</span></td>
<td>250</td>
<td style="display:none;">My Brand</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>Samsung <br> MRP : 5,000</td>
<td><span class="font-green-sharp">Damaged</span></td>
<td>25</td>
<td style="display:none;">My Brand</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td><span class="font-green-sharp">Qc Rejected</span></td>
<td>250</td>
<td style="display:none;">My Brand</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td><span class="font-green-sharp">Qc Rejected</span></td>
<td>250</td>
<td style="display:none;">My Brand</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td><span class="font-green-sharp">Qc Rejected</span></td>
<td>250</td>
<td style="display:none;">My Brand</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td><span class="font-green-sharp">Qc Rejected</span></td>
<td>250</td>
<td style="display:none;">My Brand</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td><span class="font-green-sharp">Qc Rejected</span></td>
<td>250</td>
<td style="display:none;">My Brand</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td><span class="font-green-sharp">Qc Rejected</span></td>
<td>250</td>
<td style="display:none;">My Brand</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td><span class="font-green-sharp">Qc Rejected</span></td>
<td>250</td>
<td style="display:none;">My Brand</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td><span class="font-green-sharp">Qc Rejected</span></td>
<td>250</td>
<td style="display:none;">My Brand</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td><span class="font-green-sharp">Qc Rejected</span></td>
<td>250</td>
<td style="display:none;">My Brand</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td><span class="font-green-sharp">Qc Rejected</span></td>
<td>250</td>
<td style="display:none;">My Brand</td>
</tr>
<tr class="odd gradeX">
<td><input type="checkbox" class="checkboxes" value="1"/></td>
<td><img src="../../assets/admin/layout4/img/image.png"></td>
<td>iPhone 6 <br> MRP : 28,000</td>
<td><span class="font-green-sharp">Qc Rejected</span></td>
<td>250</td>
<td style="display:none;">My Brand</td>
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

        
<div class="col-md-7 col-sm-12" style="margin-left:-50px;">
<h3 class="form-section">Selected Products</h3>
<div class="portlet light tasks-widget">

<div class="portlet-body">

<div class="scroller" style="height: 500px; width:650px !important;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list2">                
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover" id="sample_3">
<thead>
<tr>




<th>S No</th>
<th>Product Details</th>
<th>Brand</th>
<th>MRP</th>
<th>Avl Qty</th>
<th>Status</th>
<th>Set Qty</th>
<th>&nbsp;</th>
</tr>
</thead>
<tbody>


<tr class="odd gradeX list-head">
<td width="7%">&nbsp;</td>
<td width="20%"><input type="text" class="form-control input-sm" ></td>
<td width="15%"><input type="text" class="form-control input-sm" ></td>
<td width="12%"><input type="text" class="form-control input-sm" ></td>
<td width="12%"><input type="text" class="form-control input-sm" ></td>

<td width="15%">
<select class="form-control input-sm">
<option>< 10</option>
<option>< 20</option>
<option>< 30</option>
<option>< 40</option>
<option>< 50</option>
</select>
</td>
<td width="9%"><input type="text" class="form-control input-sm input-xsmall"></td>
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
</div>

</div>

<div class="row">
<div class="col-md-12 text-center">
<button type="button" class="btn green-meadow">Recall</button>
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

</div>
</div>

</div>
				
</div>
@stop

@section('style')



<style type="text/css">
/* custom inclusion of right, left and below tabs */
.table thead tr th {
    font-size: 11px !important;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 6px !important;
}

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
.input-xsmall {
    width: 40px !important;
}
.scroller {
    padding-right: 0px !important;
}
.remove {
    height: 29px !important;
    width: 29px !important;
}
</style>
<link href="{{ URL::asset('assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/select2/select2.css') }}" rel="stylesheet" type="text/css" />





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
			<td data-val="brand"></td>\
			<td  data-val="price"></td>\
			<td data-val="qty"></td>\
			<td data-val="status"></td>\
			<td><input type="text" class="form-control input-sm" value="1"></td>\
			<td><a href="" class="btn btn-icon-only default delList remove"><i class="icon-trash"></i></a></td>\
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
			data.qty = tr.find('td:eq(4)').text();
			data.status = tr.find('td:eq(3)').text();
			data.brand = tr.find('td:eq(5)').text();
			
			var new_tr = $(prod_tr);
			var sNo = $('#sample_3').find('tbody').find('tr:not(".list-head")').length + 1;
			new_tr.find('td[data-key]').text( sNo );
			
			new_tr.find('td[data-val]').each(function(){
				var index = $(this).data('val');
				//alert(index);
				$(this).text( data[index] || '' );
			});
			
			$('#sample_3').append(new_tr);
			tr.remove();
        });
	});
});
	
</script>




<script src="{{ URL::asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script src="{{ URL::asset('assets/global/plugins/datatables/media/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}" type="text/javascript"></script>

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