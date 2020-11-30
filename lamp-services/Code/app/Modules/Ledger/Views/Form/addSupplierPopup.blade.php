<!-- Modal -->
<div class="modal fade" id="add_spplier_popup" role="dialog">
<div class="modal-dialog">
   <!-- Modal content-->
   <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">Supplier Brand Mapping</h4>
      </div>
      <div class="modal-body">
         <!-- <p>Some text in the modal.</p> -->
         <form  action="" id="suppliersMapping">
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group">
                     <label class="control-label">Supplier</label>
                     <select name = "supplier_name" id="supplier_name" class="form-control select2me">
                        <option value="0">Select Here</option>
                        @foreach($names as $name)
                        <option value = "{{$name->legal_entity_id}}">{{$name->business_legal_name}}</option>
                        @endforeach                                                    
                     </select>
                     <div id="supplier_required" style="display: none;color: red">Please Select Supplier</div>
                  </div>
               </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="control-label">Manufacturer</label>
                        <select name = "manufacturer_name[]" id="manufacturer_name" class="form-control multi-select-search-box" multiple="multiple" onchange="manufacturerData()">
                           <!-- <option value="">Please Select</option> -->
                           <option value="0">ALL</option>
                           @foreach($manufacturer['manufacturer'] as $key=>$value)
                           <option value = "{{$key}}">{{$value}}</option>
                           @endforeach                                                    
                        </select>
                        <div id="manufacturer_required" style="display: none; color: red">Please Select Manufacturer</div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="control-label">Brand</label>
                        <select name = "brand_name[]" id="brand_name" class="form-control multi-select-search-box avoid-clicks" multiple="multiple">
                           <!-- <option value="">Please Select</option> -->
                                                                               
                        </select>
                        <div id="brand_required" style="display: none; color: red">Please Select Brand</div>
                     </div>
                  </div>
               </div>
            <div class="row">
               <div class="col-md-12 text-center">
                  <div class="form-group">
                     <button type="button" id="addbrandsupplier"  class="btn green-meadow">Submit</button>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<script type="text/javascript">
function manufacturerData(){
   var supplier_map_id=$('#supplier_brand_map_id').val();
   var token = $("#csrf-token").val();
   if(supplier_map_id==''){
      var sid=$('#supplier_name').val();
      var id=$('#manufacturer_name').val();   
   }else{
      var sid=$('#supplier_name_edit').val();
      var id=$('#manufacturer_name_edit').val();
   }
   if(id!=null && sid!=0){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': token
        },
        type: "GET",
        url: '/brandsForManufacture',
        data:{
         id:id,
         sid:sid,
        },
        success: function(data) {
         // data = JSON.parse(data);
         if(supplier_map_id==''){
            $('#brand_name').empty();
            $('#brand_name')[0].sumo.reload();
             $("#brand_name").append(data);
             $('#brand_name')[0].sumo.reload();
         }else{
            $('#brand_name_edit').empty();
            $('#brand_name_edit')[0].sumo.reload();
             $("#brand_name_edit").append(data);
             $('#brand_name_edit')[0].sumo.reload();
         }
         //  $("#brand_name").append('<option value="2" >Sunfeast</option><option value="3" >Sunfeast Dark Fantasy</option>'); 
        }
    });
 }
}
</script>