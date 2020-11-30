@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="portlet-body">
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
            <div class="portlet-title">
                <div class="caption">{{trans('Web Enquiries')}}</div>
            </div>
            <div class="portlet-body">
                <div role="alert" id="alertStatus"></div>
                <div class="row">
                    <div class="col-md-12">                        
                        <div class="table-responsive">
                            <table id="webenquiriesGrid"></table>
                        </div>                        
                    </div>
                </div>
                <!--edit modal-->
                <div class="modal fade" id="editWebEnquiryModal" tabindex="-1" role="dialog" aria-labelledby="editWebEnquiryLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="editWebEnquiryLabel">Edit</h4>
                                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="alert" role="alert" id="modalAlert"></div>
                                <form id="editWebEnquiryForm">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <input type="hidden" name="edit_enquiry_no" id="edit_enquiry_no">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control" id="edit_name" name="edit_name" readonly="readonly" placeholder="Name">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="type">Type</label>
                                                <input type="text" class="form-control" id="edit_type" name="edit_type" readonly="readonly" placeholder="Type">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">    
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="address">Address</label>
                                                <input type="text" class="form-control" id="edit_address" name="edit_address" readonly="readonly" placeholder="Address">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="phone">Phone Number</label>
                                                <input type="text" class="form-control" id="edit_phone" name="edit_phone" readonly="readonly" placeholder="Phone Number">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">    
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="text" class="form-control" id="edit_email" name="edit_email" readonly="readonly" placeholder="Email">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="purpose">Purpose</label>
                                                <input type="text" class="form-control" id="edit_purpose" name="edit_purpose" readonly="readonly" placeholder="Purpose">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select class="form-control select2me" id="edit_status"
                                                name="edit_status" style="margin-top: 6px"
                                                placeholder="status">
                                                <option value ="">--Please Select--</option>
                                                @foreach($statusInfo as $status)
                                                    <option value = "{{$status['description']}}">{{$status['description']}}</option>
                                                @endforeach</select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="comments">Comments</label>
                                                <input type="text" class="form-control" id="edit_comments" name="edit_comments" placeholder="comments">
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">Close</button>
                                    <button type="submit" id="saveWebEnquiry" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@stop
@section('style')
<link href="{{ URL::asset('assets/global/plugins/select2-promotions/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .alignRight{
        text-align: right !important;
        padding: 10px 10px 10px 10px;
    }
    .actionsStyle{
        padding-left: 20px;
    }
</style>
@stop
@section('script')
@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    function editWebEnquiry(id){
        $("#editWebEnquiryModal").modal("show");
        $('#editWebEnquiryModal').modal({backdrop:'static', keyboard:false});
        $("#editWebEnquiryForm").bootstrapValidator('resetForm', true);

        $.post('/webenquiries/edit/'+id,function(response){
            if(response.status1){
                $("#edit_enquiry_no").attr('value',id);
                $("#edit_name").attr('value',response.name);
                $("#edit_type").attr('value',response.type);
                $("#edit_address").attr('value',response.address);
                $("#edit_phone").attr('value',response.phone);
                $("#edit_email").attr('value',response.email);
                $("#edit_purpose").attr('value',response.purpose);
                $("#edit_status").select2('val',response.status);
                $("#edit_comments").val(response.comments);
            }
            else{
                $("#modalAlert").addClass("alert-danger").text("Invalid Data! Please Try Again!").show();
            }
        });
    }

    function deleteWebEnquiry(id) {
        var decision = confirm("Are you sure you want to delete.");
        if(decision){
                $.post('/webenquiries/delete/'+id,function(response){
                if(response.status){
                    $("#alertStatus").attr("class","alert alert-info").text("Web Enquiry Record Deleted!").show().delay(3000).fadeOut(350);
                    $('#webenquiriesGrid').igGrid("dataBind");
                }
                else{
                    $("#alertStatus").attr("class","alert alert-danger").text("Failed to Delete Web Enquiry. Please Try Again").show().delay(3000).fadeOut(350);
                }
            });
        }
    }

    $(document).ready(function ()
    {
        $(function(){
            webenquiriesGrid();
            $('#webenquiriesGrid_dd_created_on').find('.ui-iggrid-filtericonnextyear').parents('li').remove();
            $('#webenquiriesGrid_dd_created_on').find('.ui-iggrid-filtericonthisyear').parents('li').remove();
            $('#webenquiriesGrid_dd_created_on').find('.ui-iggrid-filtericonlastyear').parents('li').remove();
            $('#webenquiriesGrid_dd_created_on').find('.ui-iggrid-filtericonnextmonth').parents('li').remove();
            $('#webenquiriesGrid_dd_created_on').find('.ui-iggrid-filtericonthismonth').parents('li').remove();
            $('#webenquiriesGrid_dd_created_on').find('.ui-iggrid-filtericonlastmonth').parents('li').remove();
            $('#webenquiriesGrid_dd_created_on').find('.ui-iggrid-filtericonnoton').parents('li').remove();
            $('#webenquiriesGrid_dd_name').find('.ui-iggrid-filtericondoesnotequal').parents('li').remove();
            $('#webenquiriesGrid_dd_type').find('.ui-iggrid-filtericondoesnotequal').parents('li').remove();
            $('#webenquiriesGrid_dd_address').find('.ui-iggrid-filtericondoesnotequal').parents('li').remove();
            $('#webenquiriesGrid_dd_phone').find('.ui-iggrid-filtericondoesnotequal').parents('li').remove();
            $('#webenquiriesGrid_dd_email').find('.ui-iggrid-filtericondoesnotequal').parents('li').remove();
            $('#webenquiriesGrid_dd_purpose').find('.ui-iggrid-filtericondoesnotequal').parents('li').remove();
            $('#webenquiriesGrid_dd_comments').find('.ui-iggrid-filtericondoesnotequal').parents('li').remove();
            $('#webenquiriesGrid_dd_status').find('.ui-iggrid-filtericondoesnotequal').parents('li').remove();
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#modalAlert").hide();
        $("#alertStatus").hide();

        $("#modalClose").click(function(){
            $("#modalAlert").hide();
            $('#modalAlert').data('bs.modal',null); // this clears the BS modal data
            $("#edit_name").attr('value','');
            $("#edit_type").attr('value','');
            $("#edit_address").attr('value','');
            $("#edit_phone").attr('value','');
            $("#edit_email").attr('value','');
            $("#edit_purpose").attr('value','');
            $("#edit_status").select2('value','');
            $("#edit_comments").attr('value','');
        });

        function webenquiriesGrid()
        {   
            $('#webenquiriesGrid').igGrid({
                dataSource: '/webenquiries/list',
                responseDataKey: 'results',
                height:'100%',
                columns: [
                    {headerText:"Name", key: 'name', dataType: "string",width: '20%'},
                    {headerText:"Type", key: 'type', dataType: "string",width: '20%'},
                    {headerText:"Address", key: 'address', dataType: "string",width: '20%'},
                    {headerText:"Phone Number", key: 'phone', dataType: "string",width: '17%'},
                    {headerText:"Email", key: 'email', dataType: "string",width: '20%'},
                    {headerText:"Purpose", key: 'purpose', dataType: "string",width: '20%'},
                    {headerText:"Comments", key: "comments", dataType: "string",width: '20%'},
                    {headerText:"Status", key: "status", dataType: "string",width: '15%'},
                    {headerText:"Date", key: "created_on", dataType: "date",width: '15%'},
                    {headerText:"Actions", key: "actions", dataType: "string",width: '15%'},


                    ],
                features: [
                    {
                        name: "Filtering",
                        mode: "simple",
                        columnSettings: [
                            {columnKey: 'actions', allowFiltering: false},
                        ]
                    },
                    {
                        name: "Sorting",
                        type: "remote",
                        persist: false,
                        columnSettings: [
                            {columnKey: 'actions', allowSorting: false},
                        ],
                    },
                    {
                         name: 'Paging',
                         type: 'remote',
                         pageSize: 10,
                         recordCountKey: 'TotalRecordsCount',
                         pageIndexUrlKey: "page",
                         pageSizeUrlKey: "pageSize"
                    },
                    {
                        name: "Resizing",
                    }
                ]
            }); 
        }

        $('#editWebEnquiryForm')
        .bootstrapValidator({
            framework: 'bootstrap',
            fields: {
            }
        })
        .on('success.form.bv', function(event){
            event.preventDefault();
            var newWebEnquiryData = {
                name: $("#edit_name").val(),
                type: $("#edit_type").val(),
                phone: $("#edit_phone").val(),
                address: $("#edit_address").val(),
                email: $("#edit_email").val(),
                purpose: $("#edit_purpose").val(),
                comments: $("#edit_comments").val(),
                status: $("#edit_status").val(),
                enquiry_no: $("#edit_enquiry_no").val()
            };
            $.post('/webenquiries/update',newWebEnquiryData,function(response){
                $("#editWebEnquiryModal").modal("hide");
                if(response.status1){
                    $("#alertStatus").attr("class","alert alert-success").text("Updated!").show().delay(3000).fadeOut(350);
                    $('#webenquiriesGrid').igGrid("dataBind");
                }
                else
                    $("#alertStatus").attr("class","alert alert-danger").text("Failed To Update!").show().delay(3000).fadeOut(350);
            });            
        });
    });
        
</script>
@stop
@extends('layouts.footer')