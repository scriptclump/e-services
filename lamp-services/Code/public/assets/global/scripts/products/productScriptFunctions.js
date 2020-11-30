function editPackageConfiguration(product_id)
{
    token  = $("#csrf-token").val();


        $.ajax({
          headers: {'X-CSRF-TOKEN': token},
          url: '/editPackageConfiguration/'+product_id,
          processData: false,
          contentType: false,                                             
          success: function (rs) 
          {
            
            $("#packConfig").click();
            var obj = jQuery.parseJSON(rs);
            //console.log(obj.value);
            $("#packSkuCode").val(obj.pack_sku_code);
            $("#packEaches").val(obj.no_of_eaches);
            $("#packInner").val(obj.inner_pack_count);
            $("#pack_lenght").val(obj.length);
            $("#pack_breadth").val(obj.breadth);
            $("#pack_height").val(obj.height);

            $("#weight").val(obj.weight);
            $("#valumetricWeight").val(obj.vol_weight);
            $("#stackHeight").val(obj.stack_height);
            $("#packingMeterial").val(obj.pack_material);
            $("#palletization").val(obj.palletization);
            $("#palleteCapacity").val(obj.pallet_capacity);
            $("#pack_status").val("edit");
             $("#pack_id").val(product_id);
            var packLevel = $('#packageLevel option');
            $.map(packLevel ,function(option) {
               $('#packageLevel option[value='+obj.level+']').attr('selected','selected');
            });
             var weightUOM = $('#packWeightUOM option');

            $.map(weightUOM ,function(option) {
               $('#packWeightUOM option[value='+obj.weight_uom+']').attr('selected','selected');
            });


          }
      });

}
function deleteRelatedProduct(pid)
{
  //console.log("deleted id is ="+wh_id);
  if (confirm('Are you sure you want to delete?'))
  {
    token  = $("#csrf-token").val();
     $.ajax({
          headers: {'X-CSRF-TOKEN': token},
          url: '/deleterelatedproduct/'+pid,
          processData: false,
          contentType: false,                                             
          success: function (rs) 
          {
            $("#relatedProductsGrid").igHierarchicalGrid({"dataSource":'/relatedproducts/'+$('#product_id').val()});
           //alert(rs);
          }
      });
}}

function delete_product_supplier(pid)
{
  //console.log("deleted id is ="+wh_id);
  if (confirm('Are you sure you want to delete?'))
  {
    token  = $("#csrf-token").val();
     $.ajax({
          headers: {'X-CSRF-TOKEN': token},
          url: '/deletesupplierproduct/'+pid,
          processData: false,
          contentType: false,                                             
          success: function (rs) 
          {
            $("#relatedProductsGrid").igHierarchicalGrid({"dataSource":'/relatedproducts/'+$('#product_id').val()});
           //alert(rs);
          }
      });
}}

function delete_product_pack(pid)
{
  if (confirm('Are you sure you want to delete?'))
  {
    token  = $("#csrf-token").val();
     $.ajax({
          headers: {'X-CSRF-TOKEN': token},
          url: '/deleteproductpack/'+pid,
          processData: false,
          contentType: false,                                             
          success: function (rs) 
          {
            $("#packingConfigGrid").igHierarchicalGrid({"dataSource":'/packingproducts/'+$('#product_id').val()});
           //alert(rs);
          }
      });
  }
}