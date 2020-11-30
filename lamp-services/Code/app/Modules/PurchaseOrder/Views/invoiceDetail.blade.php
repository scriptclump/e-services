@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')
<?php $curSymbol = isset($productArr[0]->symbol) ? trim($productArr[0]->symbol) : 'Rs.'; ?>
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li><a href="/po/index">Purchases</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>Purchase Orders</li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption"> PURCHASE INVOICE # {{$productArr[0]->invoice_code}}</div>
                <div class="pull-right margin-top-10">
                    @if(isset($featureAccess['printFeature']) && $featureAccess['printFeature'])
                    <a target="_blank" class="btn green-meadow printbtn" href="/po/poInvoicePrint/{{$invoiceId}}"><i class="fa fa-print"></i></a>&nbsp;&nbsp;
                    @endif
                    @if(isset($featureAccess['downloadFeature']) && $featureAccess['downloadFeature'])
                    <a class="btn green-meadow downloadbtn" href="/po/poInvoicePdf/{{$invoiceId}}"><i class="fa fa-download"></i></a>&nbsp;&nbsp;
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                @include('PurchaseOrder::navigationTab')
            </div>
        </div>
    </div>
</div>
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
@stop
@section('style')
<style type="text/css">
    .loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
    .loderholder img{ position: absolute; top:50%;left:50%; }
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        border-top: 0px !important;
    }
    .favfont i{font-size:18px !important; color:#5b9bd1 !important;}
    .tabbable-line > .nav-tabs > li > a > i {
        color: #5b9bd1 !important;
    }
    .well1 {
        height:365px;
        border: 1px solid #eee !important;
        background:none !important;
        border-radius: 0px !important;
    }
    .well {
        border-radius: 0px !important;
    }

    .imgborder{border:1px solid #ddd !important;}
    .tabs-left.nav-tabs > li.active > a, .tabs-left.nav-tabs > li.active > a:hover > li.active > a:focus {
        border-radius: 0px !important;  
    }
    .nav>li>a:visited{
        color:red !important;
    }
    tabs.nav>li>a {
        padding-left: 10px !important;
    }
    .note.note-success {
        background-color: #c0edf1 !important;
        border-color: #58d0da !important;
        color: #000 !important;
    }
    hr {
        margin-top:0px !important;
        margin-bottom:10px !important;
    }
    .portlet > .portlet-title {
        border-bottom: 0px !important;
    }
    .newproduct{color: blue;font-weight: bold;}
    
    .table > tbody > tr > td{
        line-height: 1.4 !important;
        font-size: 12px !important;
    }
    .closedownload{ border:1px solid #ddd; width:25px; height:25px; padding:5px; margin:5px 0px;}   
    .downloadclose{
        position: relative;
        left: 34px;
        top: 9px; font-size:12px !important;color:#F3565D;
        text-decoration: none;
        cursor: pointer;
    }
    .rightAlignment { text-align: right;}
</style>
<link href="{{ URL::asset('assets/admin/pages/css/timeline.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/custom-infragistics.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('userscript')
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/poscript.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/approvalscript.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/purchaseorder/payments_script.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/image-box/js/jquery.imagebox.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $('.potabs').click(function(){            
            var id=$(this).attr('data-id');
            var type=$(this).attr('data-type');            
            var print_url='/po/printpo/'+id;
            var pdf_url='/po/download/'+id;
            if(type=='invoice'){
                print_url='/po/poInvoicePrint/'+id;
                pdf_url='/po/poInvoicePdf/'+id;
            }
            $('.printbtn').attr('href',print_url);
            $('.downloadbtn').attr('href',pdf_url);
        });
    })
</script>
@stop