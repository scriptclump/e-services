@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')
<div class="row">
    <div class="col-md-12">
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="/">Home</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
            <li>PO Details</li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            
            <div class="portlet-body">
                <div class="row">
                  @if(isset($approvalOptions) && count($approvalOptions)>0)
                    <div class="col-md-6 text-left">                        
                        @include('VendorPayment::approvalForm')
                    </div>
                  @endif
                    <div class="col-md-6 text-left">
                      <div class="table-responsive">
                        <table class="table-bordered custom-table" width="100%" cellpadding="2" cellspacing="2">
                          <tr>
                            <th colspan="2" align="center"><strong>Purchase Order Detail</strong></th>
                          </tr>
                          <tr>
                            <td align="right" width="25%">PO code</td>
                            <td>{{ $poDetail->po_code  }}</td>
                          </tr>
                          <tr>
                            <td align="right" width="25%">PO value</td>
                            <td>{{ $poDetail->symbol  }} {{ $poDetail->poValue  }}</td>
                          </tr>
                          <tr>
                            <td align="right" width="25%">Requested Amount</td>
                            <td>{{ $poDetail->symbol  }} {{ $poDetail->requested_amount  }}</td>
                          </tr>                          
                          <tr>
                            <td align="right" width="25%">PO validity</td>
                            <td>{{ $poDetail->po_validity  }}</td>
                          </tr>
                          <tr>
                            <td align="right" width="25%">Payment due date</td>
                            <td>{{ $poDetail->payment_due_date  }}</td>
                          </tr>
                          <tr>
                            <td align="right" width="25%">PO date</td>
                            <td>{{ $poDetail->po_date  }}</td>
                          </tr>                          
                          <tr>
                            <td align="right" width="25%">User name</td>
                            <td>{{ $poDetail->user_name  }}</td>
                          </tr>
                          <tr>
                            <td align="right" width="25%">GRN value</td>
                            <td>{{ $poDetail->grn_value  }}</td>
                          </tr>
                          <tr>
                            <td align="right" width="25%">PO GRN diff</td>
                            <td>{{ $poDetail->po_grn_diff  }}</td>
                          </tr>                          
                          <tr>
                            <td align="right" width="25%">GRN Created</td>
                            <td>{{ $poDetail->grn_created  }}</td>
                          </tr>
                          <tr>
                            <td align="right" width="25%">Business legal name</td>
                            <td>{{ $poDetail->business_legal_name  }}</td>
                          </tr>
                          <tr>
                            <td align="right" width="25%">Le code</td>
                            <td>{{ $poDetail->le_code  }}</td>
                          </tr>
                          <tr>
                            <td align="right" width="25%">Lp wh name</td>
                            <td>{{ $poDetail->lp_wh_name  }}</td>
                          </tr>
                          <tr>
                            <td align="right" width="25%">City</td>
                            <td>{{ $poDetail->city  }}</td>
                          </tr>
                          <tr>
                            <td align="right" width="25%">Pincode</td>
                            <td>{{ $poDetail->pincode  }}</td>
                          </tr>
                          <tr>
                            <td align="right" width="25%">Address1</td>
                            <td>{{ $poDetail->address1  }}</td>
                          </tr>
                        </table>
                      </div>
                    </div>
                </div>                      
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
    .loderholder img{ position: absolute; top:50%;left:50%;    }
    .error{color: red;}
    .custom-table>tbody>tr>th{
      margin: 10px;
      padding: 5px;
      font-weight: bold;
      text-align: center;
    }
    .custom-table>tbody>tr>td{
      margin: 10px;
      padding: 5px;
    }
</style>
@stop
@section('userscript')
<script src="{{ URL::asset('assets/admin/pages/scripts/VendorPayment/approval.js') }}" type="text/javascript"></script>
@stop