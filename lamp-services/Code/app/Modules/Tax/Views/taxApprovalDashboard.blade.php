@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<span id="success_message"></span>
<span id="error_message"></span>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">
                    Tax Mapping Approval Dashboard
                </div>
                <div class="actions">
                    <input id="take" type="hidden" name="_token" value="{{csrf_token()}}">
                </div>
            </div>
            <div class="portlet-body">
                <div class="row rowmargin">
                    <div class="col-md-6"><strong>Product Title</strong> : {{ $details['productDetails']['product_title'] }}</div>
                    <div class="col-md-6"><strong>Category Name</strong> : {{ $details['productDetails']['cat_name'] }}</div>
                </div>
                <div class="row rowmargin">
                    <div class="col-md-6"><strong>Brand Name</strong> : {{ $details['productDetails']['brand_name'] }}</div>
                    <div class="col-md-6"><strong>SKU</strong> : {{ $details['productDetails']['sku'] }}</div>
                </div>
                <div class="row rowmargin">
                    <div class="col-md-6"><strong>State</strong> : {{ $details['productDetails']['state_name'] }}</div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-border table-hover table-advance" border="1">
                            <thead>
                                <tr>
                                    <th>Tax Type</th>
                                    <th>Tax Class Code</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details['taxDetails'] as $eachDetail)
                                    <tr>
                                        <td>{{ $eachDetail['tax_class_type'] }}</td>
                                        <td>{{ $eachDetail['tax_class_code'] }}</td>
                                        <td>{{ $eachDetail['status'] }}</td>
                                        <td>{!! html_entity_decode($eachDetail['action']) !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('userscript')
<style type="text/css">
    .rowmargin{ margin: 10px;}
</style>
@extends('layouts.footer')

<script>
</script>

@stop
@extends('layouts.footer')