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
<div class="portlet-title">
<div class="caption">
RECALL INVENTORY
</div>
<div class="tools">


<span class="badge bg-blue"><a  class="fullscreen" data-toggle="tooltip" title="Hi, This is help Tooltip!" style="color:#fff;"><i class="fa fa-question"></i></a></span>



</div>
</div>
<div class="portlet-body">

<div class="row">
<div class="col-md-6">
<div class="caption" style="margin-top:6px;">
 
<span class="caption-subject bold font-blue uppercase"> Sort By :</span>
<span class="caption-helper sorting">
<a href="#" class="active">All</a> &nbsp;&nbsp;
<a href="#" class="inactive">Delivered</a> &nbsp;&nbsp;
<a href="#" class="inactive">In Progress</a> &nbsp;&nbsp;
<a href="#" class="inactive">In hold</a> &nbsp;&nbsp;
<a href="#" class="inactive">Pending</a> &nbsp;&nbsp;
</span>
</div>
</div>
<div class="col-md-6 pull-right text-right">
<button type="button" class="btn green-meadow">Add Recall Request</button>
<button type="button" class="btn green-meadow">Upload Recall Request</button>
</div>
</div>

<div class="table-scrollable">
<div class="scroller" style="height: 500px; padding:10px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">

<table class="table table-striped table-advance table-hover dataTable no-footer" id="sample_editable_1">
<thead>
<tr>

<th>S no</th>
<th>Recall ID</th>
<th>WMS Service ID</th>
<th>Created Date</th>
<th>Current Status</th>
<th>Actions</th>
</tr>
</thead>
<tbody>

<tr>
<td>1</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>Delivered</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td>
</tr>
<tr>
<td>2</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>In progress</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td>
</tr>
<tr>
<td>3</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>Delivered</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>4</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>In progress</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>5</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>Delivered</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>6</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>In progress</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>7</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>In progress</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>8</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>Delivered</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>9</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>Delivered</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>10</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>In progress</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>11</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>In progress</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>12</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>Delivered</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>13</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>Delivered</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>14</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>In progress</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>15</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>In progress</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>16</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>Delivered</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>17</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>Delivered</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>18</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>In progress</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>19</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>In progress</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>20</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>Delivered</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>21</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>Delivered</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>22</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>In progress</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>23</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>In progress</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>
<tr>
<td>24</td>
<td>89765465876</td>
<td>8689099</td>
<td>02-02-2016</td>
<td>Delivered</td>
<td class="actionss">
<code>
<a class="edit"  href="javascript:;"> <i class="fa fa-hand-o-right"></i></a>&nbsp;&nbsp;
<a class="delete" href="javascript:;"> <i class="fa fa-times"></i> </a>
</code>
</td></tr>

</tbody>
</table>
</div>
							</div>

</div>
</div>
<!-- END PORTLET-->
</div>
				
			</div>
</div>
@stop

@section('style')
<style type="text/css">
.dataTables_filter{display:none;}
.dataTables_length{display:none;}
.dataTables_paginate .paging_bootstrap_number{display:none;}
#sample_editable_1_paginate{display:none;}
#sample_2_paginate{display:none;}
#sample_3_paginate{display:none;}
.dataTables_info{display:none;}

code {
    color: #5b98ce !important;
}
.actionss{padding-left: 22px !important;}
.sorting a{ list-style-type:none !important;text-decoration:none !important;}
.sorting a:hover{ list-style-type:none !important; text-decoration:underline !important;color:#ddd !important;}
.sorting a:active{text-decoration:none !important;}
.active{text-decoration:none !important; border-bottom:2px solid #32c5d2 !important; color:#32c5d2 !important; font-weight:bold!important;}
.inactive{text-decoration:none !important; color:#ddd !important;}
</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />

@stop

@section('userscript')
<script src="{{ URL::asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/table-datatables-editable.min.js') }}" type="text/javascript"></script>

@stop


@extends('layouts.footer')