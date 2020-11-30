@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<div class="page-head">
    <div class="page-title">
        <h1>Channel Category Import<small></small></h1>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="portlet box" id="form_wizard_1">
            <div class="portlet-body form">
                <!--<a href="{{ URL::to('downloadExcel/xls') }}"><button class="btn btn-success">Download Excel xls</button></a>
                    <a href="{{ URL::to('downloadExcel/xlsx') }}"><button class="btn btn-success">Download Excel xlsx</button></a>
                    <a href="{{ URL::to('downloadExcel/csv') }}"><button class="btn btn-success">Download CSV</button></a>
                -->
                <a href="channel/create"><button class="btn btn-primary">Create Channel</button></a>
                <div class="clear"></div>
                <form id='import_category' action="{{ URL::to('categoryImportExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    <input type="file" name="import_file" />
                    <input id="take" type="hidden" name="_token" value="{{csrf_token()}}">
                    <input id="channel_id" type="hidden" name="channel_id" value="2">
                    <button type='submit' id='submitbtn' class="btn btn-primary">Import File</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="assets/global/plugins/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#import_category').submit(function(event) {
            event.preventDefault();
            $form = $(this);
            url = $form.attr('action');
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                async: false,
                success: function (data) {
                    //$('#update_import_product_message').text(data);
                    alert(data);
                    $('.close').trigger('click');
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    });
</script>
@stop