@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')    
    <div class="portlet light"> 
        <div class="portlet-title">   
            <div class="caption">{{ $add_update_flag }} Promotion</div>
            <div class="tools">&nbsp;</div>
    </div>
        <form  action = @if(empty($update)) {{"/promotions/savepromotion"}} @else {{"/promotions/updateid"}} @endif  method="POST" id = "frm_promotion_add_tmpl" name = "frm_promotion_add_tmpl" >
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
        <input type = "hidden" name = "prmt_tmpl_Id" id = "prmt_tmpl_Id" @if(!empty($update)) { value = "{{$update->prmt_tmpl_Id}}" } @endif />

<div class="portlet-body" style="height:553px;">
<div class="row">
        <div class="col-md-6">
        <div class="form-group">
            @if(empty($update))
               Used For Slab : <input type="checkbox" name="used_for_slab" id="used_for_slab" value="1">
            @else
               Used For Slab : <input type="checkbox" name="used_for_slab" id="used_for_slab" @if($update->is_slab == '1') {{'checked'}}@endif>
            @endif
        </div>
        </div>
</div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="promotion_name">Promotion Name</label>
                    <input type="text" class="form-control" name="promotion_name" id="promotion_name" @if(!empty($update)) { value = "{{$update->prmt_tmpl_name}}" } @endif />
                    @if ($errors->has('promotion_name'))<p style="color:red;">{!!$errors->first('promotion_name')!!}</p>@endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="offertype">Offer Type</label>
                @if(empty($update))
                                    <select id = "offertype"  name =  "offertype" class="form-control">
                                        <option value = "">Select type</option>
                                        <option value="percentage">Percentage</option>
                                        <option value="amount">Amount</option>
                                        <option value="free">Free</option>
                                    </select>
                                @else
                                    <select id = "offertype"  name =  "offertype" class="form-control">
                                        <option value="percentage" @if($update->offer_type == 'percentage') {{'selected'}}@endif>Percentage</option>
                                        <option value="amount" @if($update->offer_type == 'amount') {{'selected'}}@endif>Amount</option>
                                        <option value="free" @if($update->offer_type == 'free') {{'selected'}}@endif>Free</option>
                                    </select>
                                @endif
                                 @if ($errors->has('offertype'))<p style="color:red;">{!!$errors->first('offertype')!!}</p>@endif
            </div>
        </div>
    </div>
    <div class="row offeron">
        <div class="col-md-6">
            <div class="form-group">
                <label for="Offeron">Offer On</label>
                @if(empty($update))
                     <select id = "offeron"  name =  "offeron" class="form-control">
                        <option value = "">--select offeron--</option>
                        <option value="Bill">Bill</option>
                        <option value="Product">Product</option>
                        <option value="Category">Category</option>
                    </select> 
                @else
                    <select id = "offeron"  name =  "offeron" class="form-control">
                        <option value="Bill" @if($update->offer_on == 'bill') {{'selected'}}@endif>Bill</option>
                        <option value="Product" @if($update->offer_on == 'Product') {{'selected'}}@endif>Product</option>
                        <option value="Category" @if($update->offer_on == 'Category') {{'selected'}}@endif>Category</option>
                    </select> 
                @endif
                 @if ($errors->has('offeron'))<p style="color:red;">{!!$errors->first('offeron')!!}</p>@endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                @if(empty($update))
                @else
                    <label for="Status">Status</label>
                        <select id = "status"  name =  "status" class="form-control">
                            <option value="Active" @if($update->status == 'Active') {{'selected'}}@endif>Active</option>
                            <option value="In Active" @if($update->status == 'In Active') {{'selected'}}@endif>In Active</option>
                        </select>
                @endif
                @if ($errors->has('status'))<p style="color:red;">{!!$errors->first('status')!!}</p>@endif

            </div>
        </div>
    </div>
        <div class="row">
            <div class="col-md-6 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
</div>
</form>
</div>
      
@stop
@section('userscript')
<style type="text/css">
.glyphicon-remove, .glyphicon-ok {
    margin-right: 30px;
    margin-top: 32px;
}
.glyphicon-remove{
    color: red;
}

.glyphicon-ok {
    color: green;
}

.help-block {
    color: red !important;
}

.help-block {
    width: 100% !important;
}
.gradeXSlab {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
</style>
<!-- Ignite UI Required Combined CSS Files -->
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/igniteui/infragistics.css') }}" rel="stylesheet" type="text/css" />
<!--Ignite UI Required Combined JavaScript Files-->
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.core.js') }}" type="text/javascript"></script> 
<script src="{{ URL::asset('assets/global/plugins/igniteui/infragistics.lob.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/promotion/formValidation.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/promotion/bootstrap_framework.min.js') }}" type="text/javascript"></script>


<script type="text/javascript">
    
$(document).ready(function() {
    $('#frm_promotion_add_tmpl').formValidation({
        message: 'This value is not valid',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            promotion_name: {
                message: 'Promotion Name is required',
                validators: {
                    notEmpty: {
                        message: 'Promotion Name is required'
                    },
                    stringLength: {
                        min: 6,
                        max: 30,
                        message: 'Promotion Name is required must be more than 6 and less than 30 characters long'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9% ]+$/,
                        message: 'The username can only consist of alphabetical, number, dot and underscore'
                    }
                }
            },
            offertype: {
                validators: {
                    notEmpty: {
                        message: 'Offertype is required'
                    }
                }
            },
            offeron: {
                validators: {
                    notEmpty: {
                        message: 'offeron is required'
                    }
                }
            },
        }
    });
});

    $(function () {
    $('#used_for_slab').change(function () {
        if( $('#used_for_slab').prop("checked") ){
           $('#offeron').children('option[value="brand"]').css('display','none');
           $('#offeron').children('option[value="bill"]').css('display','none');
           $('#offeron').children('option[value="category"]').css('display','none');
        }else{
            $('#offeron').children('option[value="brand"]').show();
           $('#offeron').children('option[value="bill"]').show();
           $('#offeron').children('option[value="category"]').show();
        }

    });
});
$(function () {
    
        if(  $('#used_for_slab').is(':checked') ) {
            $('#offeron').children('option[value="Brand"]').css('display','none');
           $('#offeron').children('option[value="Bill"]').css('display','none');
           $('#offeron').children('option[value="Category"]').css('display','none');    
        }
        else{
            $('#offeron').children('option[value="Brand"]').show();
            $('#offeron').children('option[value="Bill"]').show();
            $('#offeron').children('option[value="Category"]').show();
        }   

});
</script>
@stop
@extends('layouts.footer')