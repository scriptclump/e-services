<div class="row hidden-xs">
<div class="col-md-12" > 
<div class="col-md-3 col-sm-6 histhead"><b>User</b></div>
<div class="col-md-2 col-sm-2 histhead"><b> Date</b></div>
<div class="col-md-3 col-sm-2 histhead"><b>Status</b></div>
<div class="col-md-4 col-sm-2 histhead"><b>Comments</b></div>
</div>
</div>
<div class="row">
<div class="col-md-12">
<div class="timeline" >
@if(isset($history))
@foreach($history as $historyVal )
<?php
$url = public_path();
if (file_exists($url . $historyVal['profile_picture']) && $historyVal['profile_picture'] != '') {
$img = $historyVal['profile_picture'];
} else {
$bp = url('uploads/LegalEntities/profile_pics');
$base_path = $bp . "/";
$img = $base_path . "avatar5.png";
}
?>
<div class="timeline-item timline_style">
<div class="timeline-badge">
<img class="timeline-badge-userpic" src="{{$img}}" width="120">
</div>

<div class="timeline-body">

<div class="row">
<div class="col-md-2 col-sm-4"> <p>{{ucwords($historyVal['firstname']).' '.ucwords($historyVal['lastname'])}}
<span>{{$historyVal['name']}}</span></p> </div> 
<div class="col-md-2 col-sm-2"><?php echo date('d/m/Y h:i A', strtotime($historyVal['created_at'])); ?></div> 
<div class="col-md-3 col-sm-2 push_right">{{$historyVal['master_lookup_name']}}</div> 
<div class="col-md-3 col-sm-2 push_right">{!! $historyVal['awf_comment'] !!}</div></div> 
</div>
</div>
@endforeach
@endif
</div> 
</div> 
</div> 