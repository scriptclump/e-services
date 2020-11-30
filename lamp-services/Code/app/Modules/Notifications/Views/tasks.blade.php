@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<?php
$tasks = json_decode($notifications_tasks);
$taskCollection = property_exists($tasks, 'data') ? $tasks->data : [];
if (!empty($taskCollection)) {
//    echo "<pre>";
//    print_R($taskCollection);
//    echo "</pre>";
//    die;
    ?>
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <!-- BEGIN PORTLET-->
            <div class="portlet light">
                <div class="portlet-title tabbable-line">
                    <div class="caption caption-md">
                        <i class="icon-globe theme-font-color hide"></i>
                        <span class="caption-subject theme-font-color bold uppercase">Tasks</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <!--BEGIN TABS-->
                    <div class="tab-content">
                        <div class="scroller" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                                <ul class="feeds">
                                    <?php foreach($taskCollection as $taskData) { ?>
                                    <li>
                                        <div class="col1">
                                            <div class="cont">
                                                <div class="cont-col1">
                                                    <div class="label label-sm label-success">
                                                        <i class="fa fa-bell-o"></i>
                                                    </div>
                                                </div>
                                                <div class="cont-col2">
                                                    <div class="desc">
                                                        <?php echo $taskData->message; ?>
<!--                                                        <span class="label label-sm label-info">
                                                            Take action <i class="fa fa-share"></i>
                                                        </span>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col2">
                                            <div class="date">
                                                <?php echo $taskData->time_delay; ?>
                                            </div>
                                        </div>
                                    </li>
                                    <?php } ?>                                    
                                </ul>
                            </div>
                    </div>
                    <!--END TABS-->
                </div>
            </div>
            <!-- END PORTLET-->
        </div>
    </div>
<?php } ?>
@stop
@extends('layouts.footer')