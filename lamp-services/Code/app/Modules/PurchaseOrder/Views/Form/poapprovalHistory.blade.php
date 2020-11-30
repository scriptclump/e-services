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
            //print_r($historyVal);exit;
            $url = public_path();
            /*if (file_exists($url . $historyVal['profile_picture']) && $historyVal['profile_picture'] != '') {
                $img = $historyVal['profile_picture'];
            } else {*/
                $bp = url('uploads/LegalEntities/profile_pics');
                $base_path = $bp . "/";
                $img = $base_path . "avatar5.png";
            //}
            ?>
            <div class="timeline-item timline_style">
                <div class="timeline-badge">
                    <img class="timeline-badge-userpic" src="{{$img}}">
                </div>

                <div class="timeline-body">

                    <div class="row">
                        <?php 
                            $firstname=isset($historyVal['firstname'])?$historyVal['firstname']:'';
                            $lastname=isset($historyVal['lastname'])?$historyVal['lastname']:'';
                            $name=isset($historyVal['name'])?$historyVal['name']:'';
                            $created_at=isset($historyVal['created_at'])?$historyVal['created_at']:'';
                            $awf_comment=isset($historyVal['awf_comment'])?$historyVal['awf_comment']:'';
                        ?>
                        <div class="col-md-2 col-sm-4"> <p>{{ucwords($firstname).' '.ucwords($lastname)}}
                        <span>{{$name}}</span></p> </div>  
                        <div class="col-md-2 col-sm-2"><?php echo date('d/m/Y h:i A', strtotime($created_at)); ?></div> 
                        <div class="col-md-3 col-sm-2 push_right">@if(isset($historyVal['master_lookup_name'])) {{$historyVal['master_lookup_name']}} @endif </div>
                        <div class="col-md-3 col-sm-2 push_right">{!! $awf_comment !!}</div></div>                
                </div>
            </div>
            @endforeach
            @endif
        </div>    
    </div>    
</div>    
