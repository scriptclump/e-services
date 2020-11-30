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
                <div class="caption">{{trans('Brand Feedback')}}</div>
                <div class="actions">
                @if($brandfeedbackexport == 1)
                <div class="actions" style="margin-right:10px; "> 
                    <a type="button" id="" href="#brandfeedbackexport" data-toggle="modal" class="btn green-meadow">Export</a><span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"></span>
                </div>
                @endif


            </div>
            </div>
            
            <div class="portlet-body">
                <div role="alert" id="alertStatus"></div>
                <div class="row">
                    <div class="col-md-12">                        
                        <div class="table-responsive">
                            <table id="brandFeedbackGrid"></table>
                        </div>                        
                    </div>
                </div>
                <!--edit modal-->
                <div class="modal fade" id="editBrandFeedbackModal" tabindex="-1" role="dialog" aria-labelledby="editBrandFeedbackLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="editBrandFeedbackLabel">Edit</h4>
                                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="alert" role="alert" id="modalAlert"></div>
                                <form id="editWebEnquiryForm">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <input type="hidden" name="edit_brandfeedback_id" id="edit_brandfeedback_id">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="edit_sales_rep">Sales Rep</label>
                                                <input type="text" class="form-control" id="edit_sales_rep" name="edit_sales_rep" readonly="readonly" placeholder="Sales Rep">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="status">Assignee</label>
                                                <select class="form-control select2me" id="edit_assignee"
                                                name="edit_assignee" style="margin-top: 6px"
                                                placeholder="Search Assignee">
                                                <option value ="0">Select Assignee</option>
                                                @foreach($assignUsersByFeatureCode as $assignInfoArr)
                                                    <option value = "{{$assignInfoArr['user_id']}}">{{$assignInfoArr['name']}}</option>
                                                @endforeach</select>
                                            </div>
                                        </div>
                                       
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="edit_shop_name">Shop Name</label>
                                                <input type="text" class="form-control" id="edit_shop_name" name="edit_shop_name" readonly="readonly" placeholder="Shop Name">
                                            </div>
                                        </div>    
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="edit_state">State</label>
                                                <input type="text" class="form-control" id="edit_state" name="edit_state" readonly="readonly" placeholder="State">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="edit_city">City</label>
                                                <input type="text" class="form-control" id="edit_city" name="edit_city" readonly="readonly" placeholder="City">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                    <label for="edit_beat">Beat</label>
                                                    <input type="text" class="form-control" id="edit_beat" name="edit_beat" readonly="readonly" placeholder="Beat">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="edit_dc_fc">FC</label>
                                                <input type="text" class="form-control" id="edit_dc_fc" name="edit_dc_fc" readonly="readonly" placeholder="FC">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="edit_buying_price">Buying Price</label>
                                                <input type="text" class="form-control" id="edit_buying_price" name="edit_buying_price" readonly="readonly" placeholder="Buying Price">
                                            </div>
                                        </div>    
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="edit_selling_price">Selling Price</label>
                                                <input type="text" class="form-control" id="edit_selling_price" name="edit_selling_price" readonly="readonly" placeholder="Selling Price">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="Weekly Sales Value">Weekly Sales Value</label>
                                                <input type="text" class="form-control" id="edit_weekly_sales_value" name="edit_weekly_sales_value" readonly="readonly" placeholder="Weekly Sales Value">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select class="form-control select2me" id="edit_status"
                                                name="edit_status" style="margin-top: 6px"
                                                placeholder="status">
                                                <option value ="0">Open</option>
                                                @foreach($statusInfo as $status)
                                                    @if ($status['value'] != "189004")
                                                    <option value = "{{$status['value']}}">{{$status['master_lookup_name']}}</option>
                                                    @endif
                                                @endforeach</select>
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="form-group">
                                                <label for="image">Image</label>
                                                <span id = "uploaded_images"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="edit_comments">Comments</label>
                                                <textarea class="form-control" id="edit_comments" value="" name="edit_comments" rows="1" placeholder="comments" width="50px"></textarea>
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
@if($brandfeedbackexport == 1)
<div class="modal modal-scroll fade in" id="brandfeedbackexport" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close feedbackExportClose" id="modalClose" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="basicvalCode">Brand Feedback Export</h4>
            </div>
            <div class="modal-body">
                <form id="brandfeedbackexportForm" action="brandfeedback/export" class="text-center" >
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group err">
                                <label>From Date</label>
                                <div class="input-icon right" style="width: 100%">
                                    <i class="fa fa-calendar" style="line-height: 5px"></i>
                                    <input type="text" class="form-control" name="from_date" id="from_date" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group err">
                                <label>To Date</label>

                                <div class="input-icon right" style="width: 100%">
                                    <i class="fa fa-calendar" style="line-height: 5px"></i>
                                    <input type="text" class="form-control" name="to_date" id="to_date" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" id="download" class="btn green-meadow">Download</button>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@endif

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
    .rightAlign { text-align:right;}
    #brandFeedbackGrid_actions {text-align:center !important;}
    .ui-iggrid-featurechooserbutton{display:none !important}
    .ui-icon.ui-corner-all.ui-icon-pin-w{display:none !important}
    .ui-iggrid .ui-iggrid-headertable, .ui-iggrid .ui-iggrid-content, .ui-iggrid .ui-widget-content, .ui-iggrid-scrolldiv table{border-spacing:0px !important;}
</style>
@stop
@section('script')
@include('includes.ignite')
{{HTML::script('assets/global/plugins/igniteui/infragistics.loader.js')}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script>
<script src="{{ URL::asset('assets/global/plugins/select2/select2.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">

    function editBrandFeedback(id){
        // alert(id);
        $("#editBrandFeedbackModal").modal("show");
        $('#editBrandFeedbackModal').modal({backdrop:'static', keyboard:false});
        $("#editWebEnquiryForm").bootstrapValidator('resetForm', true);

        $.post('/brandfeedback/edit/'+id,function(response){
            if(response.status1){
                $("#edit_brandfeedback_id").attr('value',id);
                $("#edit_sales_rep").attr('value',response.sales_rep);
                $("#edit_shop_name").attr('value',response.shop_name);
                $("#edit_state").attr('value',response.state);
                $("#edit_city").attr('value',response.city);
                $("#edit_beat").attr('value',response.beat);
                $("#edit_dc_fc").attr('value',response.dc_name);
                $("#edit_buying_price").attr('value',response.buying_price)
                $("#edit_selling_price").attr('value',response.selling_price);
                $("#edit_weekly_sales_value").attr('value',response.weekly_sales_value);
                $("#edit_status").select2('val',response.status);
                $("#edit_comments").val(response.comments);
                $("#edit_assignee").select2('val',response.assignee);
                if(response.image!="")
                {
                    // $("#image").attr("src",response.image);
                    //loop the images and push the html to uploaded_imaged span
                    let images_uploaded_arr = response.image.split(',');
                    if(images_uploaded_arr.length)
                    {
                        let images_html = "";
                        $.each( images_uploaded_arr, function( index, value ){
                            images_html += '<img style="max-height: 200px; max-width: 200px; height:auto; width:auto;vertical-align: middle;padding:5px;" src="'+value+'" id="image" name="image" alt="Brand feedback uploaded image">';
                        });
                        if(images_html!="")
                        {
                            $("#uploaded_images").html(images_html);
                        }
                    }
                }
                else
                {
                    let nosourceimg= "https://s3.ap-south-1.amazonaws.com/ebutormedia/products/168+products/harisharan/no-image_zpseqbcsx2n.jpg";
                    $("#image").attr("src",nosourceimg);
                }
                
            }
            else{
                $("#modalAlert").addClass("alert-danger").text("Invalid Data! Please Try Again!").show();
            }
        });
    }

    function deleteWebEnquiry(id) {
        var decision = confirm("Are you sure you want to delete.");
        if(decision){
                $.post('/brandfeedback/delete/'+id,function(response){
                if(response.status){
                    $("#alertStatus").attr("class","alert alert-info").text("Brand Feedback Record Deleted!").show().delay(3000).fadeOut(350);
                    $('#brandFeedbackGrid').igGrid("dataBind");
                }
                else{
                    $("#alertStatus").attr("class","alert alert-danger").text("Failed to Delete Brand Feedback. Please Try Again").show().delay(3000).fadeOut(350);
                }
            });
        }
    }

    $(document).ready(function ()
    {
        $(function(){
            brandFeedbackGrid();
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
            $("#edit_sales_rep").attr('value','');
            $("#edit_shop_name").attr('value','');
            $("#edit_state").attr('value','');
            $("#edit_city").attr('value','');
            $("edit_buying_price").attr('value','');
            $("#edit_selling_price").attr('value','');
            $("#edit_weekly_sales_value").attr('value','');
            $("#edit_status").select2('value','');
            $("#edit_comments").attr('value','');
            $("#uploaded_images").empty();
            $("#edit_assignee").select2('value','');
        });

        function brandFeedbackGrid()
        {   
            $('#brandFeedbackGrid').igGrid({

                dataSource: '/brandfeedback/list',
                autoGenerateColumns: false,
                autoGenerateLayouts: false,
                mergeUnboundColumns: false,
                responseDataKey: 'results',
                generateCompactJSONResponse: false,
                rowHeight:12,
                enableUTCDates: true,
                expandColWidth: 0,
                renderCheckboxes: true,
                width: '100%',
                height: '520px', 
                initialDataBindDepth: 0,
                localSchemaTransform: false,
                columns: [
                    {headerText:"Sales Rep", key: 'sales_rep', dataType: "string",width: '150px'},
                    {headerText:"Shop Name", key: 'shop_name', dataType: "string",width: '150px'},
                    {headerText:"Beat", key: 'beat', dataType: "string",width: '150px'},
                    {headerText:"FC", key: 'dc_name', dataType: "string",width: '150px'},
                    {headerText:"City", key: 'city', dataType: "string",width: '100px'},
                    {headerText:"State", key: 'state', dataType: "string",width: '100px'},
                    {headerText:"Buying Price", key: 'buying_price', dataType: "number",width: '100px',template: '<div class="rightAlign"> ${buying_price} </div>'},
                    {headerText:"Selling Price", key: 'selling_price', dataType: "number",width: '100px',template: '<div class="rightAlign"> ${selling_price} </div>'},
                    {headerText:"Weekly Sales Value", key: 'weekly_sales_value', dataType: "number",width: '150px',template: '<div class="rightAlign"> ${weekly_sales_value} </div>'},                    
                    {headerText:"Image", key: "image", dataType: "string",columnCssClass: "imgalign",width: '60px',template: "<center style='border-width:1px; border-style:solid; border-color:#efefef; height:32px; width:32px; line-height:30px; display:block; background:#fff;margin-left:15px;'><img style='max-height: 32px; max-width: 32px; height:auto; width:auto;vertical-align: middle;' src ='${image}'/></center>"},
                    {headerText:"Status", key: "status", dataType: "string",width: '100px'},
                    {headerText:"Comments", key: "comments", dataType: "string",width: '150px'},
                    {headerText:"Assignee", key: "assignee", dataType: "string",width: '150px'},
                    {headerText:"Created By", key: "created_by", dataType: "string",width: '100px'},
                    {headerText:"Created At", key: "created_at", dataType: "date",width: '150px'},
                    {headerText:"Updated By", key: "updated_by", dataType: "string",width: '100px'},
                    {headerText:"Updated At", key: "updated_at", dataType: "date",width: '150px'},
                    {headerText:"Actions", key: "actions", dataType: "string",width: '100px'},


                    ],
                features: [
                    {
                        name: "ColumnFixing",
                        fixingDirection: "right",
                        columnSettings: [
                            {
                                columnKey: "actions",
                                isFixed: true,
                                allowFixing: false
                            }
                        ]
                    },
                    {
                        name: "Filtering",
                        mode: "simple",
                        columnSettings: [
                            {columnKey: 'actions', allowFiltering: false},
                            {columnKey: 'image', allowFiltering:false}
                        ]
                    },
                    {
                        name: "Sorting",
                        type: "remote",
                        persist: false,
                        columnSettings: [
                            {columnKey: 'actions', allowSorting: false},
                            {columnKey: 'image', allowSorting:false}
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
            var editBrandFeedbackData = {
                sales_rep: $("#edit_sales_rep").val(),
                shop_name: $("#edit_shop_name").val(),
                city: $("#edit_city").val(),
                state: $("#edit_state").val(),
                buying_price: $("#edit_buying_price").val(),
                selling_price: $("#edit_selling_price").val(),
                purpose: $("#edit_weekly_sales_value").val(),
                comments: $("#edit_comments").val(),
                status: $("#edit_status").val(),
                brand_feedback_id: $("#edit_brandfeedback_id").val(),
                assignee : $("#edit_assignee").val()
            };
            $.post('/brandfeedback/update',editBrandFeedbackData,function(response){
                $("#editBrandFeedbackModal").modal("hide");
                if(response.status1){
                    $("#alertStatus").attr("class","alert alert-success").text("Updated!").show().delay(3000).fadeOut(350);
                    $('#brandFeedbackGrid').igGrid("dataBind");
                }
                else
                    $("#alertStatus").attr("class","alert alert-danger").text("Failed To Update!").show().delay(3000).fadeOut(350);
            });            
        });
    });
        
</script>
<script>
    $(document).ready(function(){

        $('#brandfeedbackexport').on('hide.bs.modal', function () {
            $("#brandfeedbackexportForm").bootstrapValidator('resetForm', true);
            $("#from_date").val("");
            $("#to_date").val("");
            $('.modal-backdrop').remove();
        });

        $( "#from_date" ).datepicker({
            maxDate: new Date(),
            onSelect: function(date) {
                $('#brandfeedbackexportForm').bootstrapValidator('revalidateField', 'from_date');
                $("#to_date").datepicker('option', 'minDate', date);
            }
        });

        $("#to_date").datepicker({  maxDate: new Date() });

        $('#brandfeedbackexportForm').bootstrapValidator({
            framework: 'bootstrap',
            fields: {
                from_date: {
                    validators: {
                        notEmpty: {
                            message: 'This field is required'
                        },
                        date: {
                            format: 'MM/DD/YYYY',
                            message: 'Invalid format'
                        }
                    }
                },
                to_date: {
                    validators: {
                        notEmpty: {
                            message: 'This field is required'
                        },
                        date: {
                            format: 'MM/DD/YYYY',
                            minDate: 'from_date',
                            message: 'Invalid format'
                        }
                    }
                },
            }
        })
        .on('success.form.bv', function(event) {
                event.preventDefault();
                var form = $('#brandfeedbackexportForm');
                window.location = form.attr('action') + '?' + form.serialize();
                $('.feedbackExportClose').click();
        });

        $("#to_date").change(function(){
            $('#brandfeedbackexportForm').bootstrapValidator('revalidateField', 'from_date');
            $('#brandfeedbackexportForm').bootstrapValidator('revalidateField', 'to_date');

        });
    });

</script>
@stop
@extends('layouts.footer')