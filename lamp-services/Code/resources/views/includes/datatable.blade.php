@section('userscript')
<script src="{{ URL::asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
<!--<script src="{{ URL::asset('assets/admin/pages/scripts/table-editable.js') }}" type="text/javascript"></script>-->
@stop
@section('style')

<link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@stop