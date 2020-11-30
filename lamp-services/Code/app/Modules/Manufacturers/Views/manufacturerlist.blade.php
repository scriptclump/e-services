@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')
@section('content')

<div class="alert alert-success hide">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <span id="flass_message"></span>
</div>
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="portlet light tasks-widget">
      <div class="portlet-title">
        <div class="caption">Manage Brands</div>
        @if($add_brand == 1)
          <div class="actions"><a class="btn green-meadow" href="brands/add">Add Brand</a> <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span> </div>
        @endif
      </div>
      <div class="portlet-body">
			<div class="row">
			  <div class="col-md-12">
				<div class="scroller" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
             <table id="manufacturer_list_grid"></table>
				</div>
			  </div>
			</div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="csrf-token" name="_Token" value="{{ csrf_token() }}">

<div id="addbrand" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="add_brand_form" method="post" enctype="multipart/form-data">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">ADD BRAND</h4>
      </div>
      <div class="modal-body">
      
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Brand Name <span class="required" aria-required="true">*</span></label>
             <input name="brand_name" id="brand_name" type="text" class="form-control">
            </div>
          </div>    
             <input name="brand_id" id="edit_brand_id" type="hidden" class="form-control"  />
          <div class="col-md-6">
          <div class="form-group">
          <label class="control-label">Logo <span class="required" aria-required="true">*</span></label>
          <div class="row">
          <div class="col-md-12">  

          <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">

          <div>
          <span class="btn default btn-file btn green-meadow" style="width:110px !important;">

          <span class="fileinput-new">Choose File </span>
          <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>
          <input id="brandLogo" type="file" name="brand_logo" class="upload" />
          </span>

          <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px;"></div>

          <span class="fileinput-filename" style=" float:left; width:233px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>

          </div>
          </div>



          </div>
          </div>
          </div></div>    
          </div>      
                
          <div class="row">
          <div class="col-md-6"><div class="form-group">
                          <label class="control-label">Description <span class="required" aria-required="true">*</span></label>
                          <textarea rows="1" name="brand_desc" id="brand_desc" class="form-control"></textarea>
                        </div></div>    
          <div class="col-md-6"><div class="form-group">
          <label class="control-label">Trade Mark Registration Proof</label>
          <div class="row">
          <div class="col-md-12">  

          <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">

          <div>
          <span class="btn default btn-file btn green-meadow" style="width:110px !important;">

          <span class="fileinput-new">Choose File </span>
          <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>
          <input id="tradeMarkProof" type="file" name="brand_trademark_proof" class="upload" />
          </span>

          <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px;"></div>

          <span class="fileinput-filename" style=" float:left; width:233px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>

          </div>
          </div>



          </div>
          </div>
          </div></div>    
          </div>      
                
          <div class="row">
          <div class="col-md-6"><div class="form-group">
                          <label class="control-label">Trade Mark Registration Number
                          <span data-original-title="Tooltip in top" data-placement="top" class="badge bg-blue tooltips"><i class="fa fa-question"></i></span></label>
                          <input name="brand_trademark_number" id="brand_trademark_number" type="text" class="form-control">
                        </div></div>    
          <div class="col-md-6"><div class="form-group">
          <label class="control-label">Brand Authorization Proof</label>
          <div class="row">
          <div class="col-md-12">  

          <div class="fileinput fileinput-new" data-provides="fileinput" style="margin-top:0px !important;">

          <div>
          <span class="btn default btn-file btn green-meadow" style="width:110px !important;">

          <span class="fileinput-new">Choose File </span>
          <span class="fileinput-exists" style="margin-top:-9px !important;">Change </span>
          <input id="authorizationProof" type="file" name="brand_authorization" class="upload" />
          </span>

          <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px; height: 33px; margin-left:9px;"></div>

          <span class="fileinput-filename" style=" float:left; width:233px; visibility:">&nbsp; <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"></a></span>

          </div>
          </div>



</div>
</div>
</div></div>    
</div>      
      
</div>
      <div class="modal-footer ">
        <center>
          <button class="btn green-meadow" type="submit" value="Save">Save</button>
        </center>
      </div>
    </div>
  </form>
  </div>
</div>




@stop
@section('style')
<style type="text/css">
.fa-thumbs-o-up {
    color: #3598dc !important;
}
.fa-pencil {
    color: #3598dc !important;
}
.fa-trash-o {
    color: #3598dc !important;
}
.actionsaling{text-align:center !important;}
</style>
<link href="{{ URL::asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@stop
@section('script')
<script type="text/javascript"> 
      jQuery(document).ready(function () {
         $("#manufacturer_list_grid_Trademarked").attr("title", "Trademarked Authorized");         
         $("#manufacturer_list_grid_is_authorised").attr("title", "Is Authorized");         
         $("#manufacturer_list_grid_Products").attr("title", "Products");         
         $("#manufacturer_list_grid_With_Images").attr("title", "Products with Images");         
         $("#manufacturer_list_grid_Without_Images").attr("title", "Products without Images");         
         $("#manufacturer_list_grid_With_Inventory").attr("title", "Products with Inventory");         
         $("#manufacturer_list_grid_Without_Inventory").attr("title", "Products without Inventory");
         $("#manufacturer_list_grid_Approved").attr("title", "Approved");         
         $("#manufacturer_list_grid_Pending").attr("title", "Pending for Approval"); 

                FormWizard.init();
            });

</script>
@stop

@section('userscript')
@include('includes.ignite')
<script src="{{ URL::asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/table-datatables-editable.min.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/brands/brands_grid_script.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/admin/pages/scripts/form-wizard-manufactures.js') }}" type="text/javascript"></script>
<script>
$(".ui-icon-triangle-1-se").click(function(){
        $(".ui-iggrid-filterrow").toggle();
    });

	  $(".fileinput-preview").removeClass('fileinput-preview fileinput-exists thumbnail').addClass('fileinput-preview thumbnail');

$(document).on('click', '.deleteBrand', function(event) {
	
	event.preventDefault();
	
	brand_id = $(this).attr('href');
	
    var productsCount = $.trim($(this).parents('tr').find('[aria-describedby$="manufacturer_list_grid_Products"]').find('span').text());    
	if(productsCount==0)
	{

		if (confirm('Are you sure you want to delete?'))
		{
			token  = $("#csrf-token").val();
			 $.ajax({
				  headers: {'X-CSRF-TOKEN': token},
				  url: '/brands/deletebrand/'+brand_id,
				  processData: false,
				  contentType: false,                                             
				  success: function (rs) 
				  {
                                     
                                      if(rs)
                                      {
                                        $('#manufacturer_list_grid').igHierarchicalGrid({dataSource: '/brands/getManfBrandsGrid'});    
                                      }
                                      else
                                      {
                                          alert('Please unmap/delete products assosiated with this brand.');
                                      }
				      		
				  } 
			  });
		}
	}
	else {

		alert('Please delete / unmap products associated with this Brand')
	
	}
});


$(document).on('click', '.deleteProduct', function(event) {
	
	event.preventDefault();
	
	product_id = $(this).attr('href');
	
	
	if (confirm('Are you sure, you want delete product. It will delete TOT, Pack configuration, Slab rates?'))
	{
		token  = $("#csrf-token").val();
		 $.ajax({
			  headers: {'X-CSRF-TOKEN': token},
			  url: '/brands/deleteProduct/'+product_id,
			  processData: false,
			  contentType: false,                                             
			  success: function (rs) {                              
                              if (rs == 0) {                                   			                                  
                                    $('#flass_message').text('Product cannot be deleted as it is associated with Orders/PO/Indent/Inward transactions' );                            
                                    $('div.alert').show();
                                    $('div.alert').removeClass('hide').addClass('alert-danger').removeClass('alert-success');
                                    $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                                    $('html, body').animate({scrollTop: '0px'}, 800);
                              } else {
                                    $('#flass_message').text('Product is deleted successfully');                            
                                    $('div.alert').show();
                                    $('div.alert').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
                                    $('div.alert').not('.alert-important').delay(5000).fadeOut("450");
                                    $('html, body').animate({scrollTop: '0px'}, 800);
                                    $('#manufacturer_list_grid').igHierarchicalGrid({dataSource: '/brands/getManfBrandsGrid'});
                              }
                            
			  } 
		  });
	}
});

	
	$('a[href="#addp"]').on('click',function(e) {
		

		supplier_product_formwizard.resetForm();

			$('#supplier_add_products .has-error').each(function(){
					
					$(this).removeClass('has-error');
			});

		if (e.originalEvent !== undefined)
		{
			$('#supplier_add_products')[0].reset();
			$('#edit_form_product_id').val('');
		}
		else {
			
		}
	});
	
	
</script>
@stop
@extends('layouts.footer')
