<div class="tab-pane" id="tab_66">
<div class="row"><div class="col-lg-12 histhead" >  
<div class="col-md-1"> <b>User</b></div>
<div class="col-md-2">  </div>  
<div class="col-md-2"> <b> Date</b></div>
<div class="col-md-3"> <b>Status</b></div>
<div class="col-md-3"><b>Comments</b></div></div>   </div>  

<div class="timeline" >
@if(isset($history))
@foreach($history as $historyVal )
<?php
$url=  public_path();
if(file_exists($url.$historyVal['profile_picture']) && $historyVal['profile_picture']!='')
{
$img = $historyVal['profile_picture'];      
}
else
{
 $bp = url('uploads/LegalEntities/profile_pics');
 $base_path = $bp."/";   
 $img = $base_path."avatar5.png";         
}
?>
<div class="timeline-item timline_style">
<div class="timeline-badge">
<img class="timeline-badge-userpic" src="{{$img}}">
</div>
    
<div class="timeline-body">

<div class="row">
<div class="col-md-2"> <p>{{ucwords($historyVal['firstname']).' '.ucwords($historyVal['lastname'])}}
<span>{{$historyVal['name']}}</span></p>  </div> 
<div class="col-md-2 "><?php echo date('d/m/Y h:i A',strtotime($historyVal['created_at'])); ?></div> 
<div class="col-md-3 push_right">{{$historyVal['master_lookup_name']}}</div>                
<div class="col-md-3 push_right" style="width: 350px;word-wrap: break-word;">{!! $historyVal['awf_comment'] !!}</div></div>                
</div>
</div>
@endforeach
@endif
</div>    
</div>