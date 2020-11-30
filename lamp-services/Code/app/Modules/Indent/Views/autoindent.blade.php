@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')

<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/indents/index">Indents</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Auto Indent</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">Auto Indent</div>
                <div class="actions">
                <a href="/indents/createIndent" class="btn btn-success">Create Indent</a>
                </div>
            </div>
            
            <div class="portlet-body">
                    <div class="row">
						<div class="col-md-12 col-sm-12">
							@if(isset($error))
							<div class="alert alert-warning">
							  <strong>Warning!</strong>&nbsp;{{$error}}
							</div>
							@endif
						</div>
                        
                        <div class="col-md-12 col-sm-12">
							@if(isset($sucessIndent))
							<div class="alert alert-success">
								@foreach($sucessIndent as $indentCode)
								Indent #<strong>{{$indentCode}}</strong> created successfully!<br>
								@endforeach
							</div>
							@endif
						</div>
                    </div>
            </div>
        </div>
    </div>

</div>
@stop
