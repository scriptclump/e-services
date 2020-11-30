@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">WARNING MESSAGE</div>
                <div class="actions">&nbsp;</div>
            </div>
            
            <div class="portlet-body">		   
                <div class="row">
                    <div class="col-md-12">
						<div class="alert alert-danger" role="alert">
						  <strong>Sorry!</strong> You don't have access. Please contact to administrator.
						</div>
                    </div>
                </div>
			</div>
    </div>
</div>
</div>
</div>
@stop
