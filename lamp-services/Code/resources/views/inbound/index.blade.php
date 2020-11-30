@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<style type="text/css">
.actionss{padding-left: 22px !important;}
.sorting a{ list-style-type:none !important;text-decoration:none !important;}
.sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
.sorting a:active{text-decoration:none !important;}
.active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
.inactive{text-decoration:none !important; color:#ddd !important;}
</style>

<ul class="page-breadcrumb breadcrumb">
<li><a href="javascript:;">Home</a><i class="fa fa-circle"></i></li>
<li><a href="javascript:;">Service request</a><i class="fa fa-circle"></i></li>
<li class="">Inbound</li>
</ul>



<div class="row">
<div class="col-md-12 col-sm-12">
<div class="portlet light tasks-widget">
<div class="portlet-title">
<div class="caption">
MANAGE INBOUND
</div>
<div class="tools">


<span class="badge bg-blue"><a  class="fullscreen" data-toggle="tooltip" title="Hi, This is help Tooltip!" style="color:#fff;"><i class="fa fa-question"></i></a></span>



</div>
</div>
<div class="portlet-body">

<div class="row">
<div class="col-md-6">
<div class="caption">
 
<span class="caption-subject bold font-blue uppercase"> Sort By :</span>
<span class="caption-helper sorting">
<a href="#" class="active">Pending</a> &nbsp;&nbsp;
<a href="#" class="inactive">Approved</a> &nbsp;&nbsp;
<a href="#" class="inactive">Date</a> 
</span>
</div>
</div>
<div class="col-md-6 pull-right text-right">
<button type="button" class="btn green-meadow">Create Inbound</button>
<button type="button" class="btn green-meadow">Upload Consigment</button>
</div>
</div>

<div class="table-scrollable">
<div class="scroller" style="height: 350px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">

<table class="table table-striped table-advance table-hover">
<thead>
<tr>

<th>Consigment ID</th>
<th>Created Date</th>
<th>Quantity</th>
<th>Drop Schedule</th>
<th>Consigment Status</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Not Schedule</td>
<td>Created</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Not Schedule</td>
<td>Created</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Dropped</td>
<td>QC-Process</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Not Schedule</td>
<td>QC-Process</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Dropped</td>
<td>QC-Process</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Dropped</td>
<td>Created</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Dropped</td>
<td>Created</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Not Schedule</td>
<td>Created</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Not Schedule</td>
<td>QC-Process</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Not Schedule</td>
<td>Created</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Not Schedule</td>
<td>Created</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Not Schedule</td>
<td>Created</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Not Schedule</td>
<td>Created</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Not Schedule</td>
<td>Created</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Not Schedule</td>
<td>Created</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Not Schedule</td>
<td>Created</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Not Schedule</td>
<td>Created</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
<tr>
<td>Cons-7878675646</td>
<td>Feb 02, 2016</td>
<td>40</td>
<td>Not Schedule</td>
<td>Created</td>
<td class="actionss"><code><i class="fa fa-times"></i></code></td>
</tr>
</tbody>
</table>
</div>
							</div>

</div>
</div>
<!-- END PORTLET-->
</div>
				
			</div>



@stop
@extends('layouts.footer')