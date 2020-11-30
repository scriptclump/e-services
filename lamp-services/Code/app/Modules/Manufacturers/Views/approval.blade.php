<?php
$approvalForId='';
$approvaltype='';
if(isset($productData))
{
    $approvaltype = 'Product PIM';
    $approvalForId = $productData->product_id;            
}
else if(Session::get('supplier_id'))
{
     $approvaltype = 'Supplier';
     $approvalForId = Session::get('supplier_id');   
}
else if(Session::get('po_id'))
{
     $approvaltype = 'Purchase Order';
     $approvalForId = Session::get('po_id');   
}
else if(Session::get('legal_entity_id'))
{
     $approvaltype = 'Retailer';
     $approvalForId = Session::get('legal_entity_id');   
}
else if(Session::get('inward_id'))
{
     $approvaltype = 'GRN';
     $approvalForId = Session::get('inward_id');   
}
else if(Session::get('pr_id'))
{
     $approvaltype = 'Purchase Return';
     $approvalForId = Session::get('pr_id');   
}
?>

      <form method="post" id="approval_form">
      <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
            <input type="hidden" class="form-control" name="approval_for_id" id="approval_for_id" value="{{$approvalForId}}">
      <input type="hidden" class="form-control" name="approval_type_id" id="approval_type_id" value="{{$approvaltype}}">
 	  <div class="modal-body">
	 <div class="row" id='approval_row_id' style="display:none">
             <hr>
                    <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Status <span class="required" aria-required="true">*</span></label>
                    <div id="div_approval_select">
                        <select class="form-control" name="approval_select" id="approval_select_id">
                            
                        </select>
                        
                    </div>                     
                </div>
            </div>
                         <div class="col-md-6">
            <div class="form-group">
              <label class="control-label">Comments <span class="required" aria-required="true">*</span></label>
              <textarea rows="1" name="approval_comments" id="approval_comments" class="form-control"></textarea>
            </div>
          </div>
         </div>
	<!-- <div class="row" id='approval_row_id2' style="display:none">
            <div class="col-md-12">
            <div class="form-group">
              <label class="control-label">Comments</label>
              <textarea rows="1" name="approval_comments" id="approval_comments" class="form-control"></textarea>
            </div>
          </div>
         </div>-->
    
        <div class="form-actions">
        <div class="row">
          <div class="col-md-12 text-center">
              <input type='submit' class="btn green btn-sm" value="Save" id='approval_save' style="display:none">
<!--<button class="btn red btn-sm" type="button">Disapprove</button>-->

          </div>
        </div>
        </div>
      </div>
      </form>