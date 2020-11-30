@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
               <div class="caption uppercase">Order #{{$orderdata->order_code}}&nbsp;&nbsp;{{date('d-m-Y h:i:s',strtotime($orderdata->order_date))}}</div>
                <div class="tools uppercase">&nbsp;</div>
            </div>
            @include('Orders::navigationTab')
            </div>
        </div>
    </div>
</div>
@stop

@section('userscript')
@include('Orders::gridJsFile')

<script type="text/javascript">
$(document).ready(function() {	


    $('#order_cancel_form').on('click',function(){




        $("#order_cancel_form").validate({
            rules: {
                    cancel_status:"required"        
                },
            submitHandler: function(form) {
                if(getCheckedBox() == false) {
                    $('#cancelAjaxResponse').html('Please select at least one product.');
                }
                else {      
                                if(confirm('Are you sure to cancel this order?')){
                                    var form = $('#order_cancel_form');
                                    $('.loderholder').show();
                                    $.ajax({
                                        url: form[0].action,
                                        type: "POST",
                                        data: form.serialize(),
                                        dataType: 'json',
                                        success: function(data) {
                                                if(data.status == 200) {
                                                     $('.loderholder').show();
                                                        $('#cancelAjaxResponse').removeClass('text-danger').addClass('text-success').html(data.message);
                                                        //window.setTimeout(function(){location.reload()},2000);
                                                        window.setTimeout(function(){window.location.href="/salesorders/detail/{{$orderdata->gds_order_id}}"},2000);
                                                }
                                                else {
                                                     $('.loderholder').hide();
                                                        $('#cancelAjaxResponse').removeClass('text-success').addClass('text-danger').html(data.message);
                                                }
                                        },
                                        error:function(response){
                                             $('.loderholder').hide();
                                                $('#cancelAjaxResponse').html('Unable to saved comment');
                                        }
                                    });     
                                }
                }
            }   
        });

        
        var cancels = $('select[name^="cancelReason"]');
        cancels.each(function() {

            if($(this).parents('tr').find('input[name="orderItems[]"]').prop('checked')) {
                console.log($(this))
              $(this).rules('add',"required");  
          } else {
              $(this).rules('remove',"required");
              $(this).removeClass('error');  
          }


        });



    });

    $(".cancelqty").change(function(){
        var id = $(this).attr('id');
        if( this.value > 0) {
            $('#chk'+id).attr('checked', true);            
        }
        else {
            $('#chk'+id).removeAttr('checked');   
        }
    });
});
</script>
<style type="text/css"> .loderholder{background: rgba(0, 0, 0, 0.2);  height:100%; position:absolute; top:0; bottom:0; width:100%; z-index:999; text-align:center; display:none;    }
.loderholder img{ position: absolute; top:50%;left:50%;    }
.error{color: red;}
</style>
<div class="row loderholder">
    <img src="/img/ajax-loader1.gif">
</div>
@stop

