@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">                
                <div class="caption"> Customer Feedback </div>
                <div class="actions"> 
                @if($xlsaccess)    
                <a class="btn green-meadow" href="custfeedbackxls">Download Feedback</a>
                @endif
                </div>
            </div>                               
            <div class="portlet-body">
                <table id="customer_feedback_grid"></table>
            </div>
        </div>
    </div>
</div>

<iframe class="lightbox"  style="border:none;" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
    
</iframe>
{{HTML::style('css/switch-custom.css')}}
@stop

@section('style')
<style type="text/css">
    .margtop{margin-top:15px;}
    .ui-iggrid-filterrow ui-widget { }
    .actions {
    display: inline-block;
    float: right !important;}
    .audiofile{width: 200px !important;}

</style>
<link href="{{ URL::asset('assets/global/css/components.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet"  href="{{ URL::asset('assets/global/css/featherlight.min.css') }}" />
@stop
@section('userscript')
@include('includes.validators')
@include('includes.ignite')
{{HTML::script('assets/admin/pages/scripts/feedback/customer_feedback.js')}}
{{HTML::script('assets/admin/pages/scripts/feedback/featherlight.min.js')}}

<script>
        $(document).on('click', '.deletefeedback', function (event) {
        event.preventDefault();

            if (confirm("Are you sure you want to delete this Customer Feedback?") == true)
            {
                var csrf_token = $('#csrf-token').val();

                var fid = $(this).attr('href');

                $.ajax({
                    headers: {'X-CSRF-TOKEN': csrf_token},
                    url: '/customerfeedback/deletefeedback',
                    type: 'POST',
                    data: {'fid': fid},
                    async: false,
                    success: function (data) {
                        alert('Customer Feedback deleted successfully')
                        $('#customer_feedback_grid').igGrid({dataSource: '/customerfeedback/getcustomerfeedback'});
						$('#customer_feedback_grid').igGrid("dataBind");
                    },
                    cache: false
                });

            }

    })
	function GridModalMessage(grid) {
		var modalBackground = $("<div class='ui-widget-overlay ui-iggrid-blockarea' style='position: absolute; display: none; width: 100%; height: 100%;'><div style='position: relative;top:50%; font-size:30px; font-weight: bold; text-align: center;'></div></div>").appendTo(grid.container());
		function _show(message) {
			modalBackground.show().find("div").text(message);
		}
		function _hide() {
			$('.ui-widget-overlay').css('display','none');
		}
		return {
			show: _show,
			hide: _hide
		}
	}
</script>
@stop
@extends('layouts.footer')