function editWhBinConfig(wh_bin_id)
{
      $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/getWhBinConfigDataByBinId/'+wh_bin_id,
            type: 'POST',
            success: function (rs)
            {
                  $("#edit_wh_id").val(rs['prod_bin_conf_id']);
                  $("#wh_id").val(rs['wh_id']);
                  $("#bin_type").val(rs['bin_type_dim_id']);
                  $("#pro_min_capacity").val(rs['min_qty']);
                  $("#wh_pack_type").val(rs['pack_conf_id']);
                  $("#pro_max_capacity").val(rs['max_qty']);
                 $("#warehouse_config_id").click();
            }
      });
}
$("#warehouse_config_id").on('click', function(e)
{

    if (e.originalEvent !== undefined)
    {
        $('#wh_bin_configuration')[0].reset();
    }

});

$("#save_product_group_name").click(function()
{
	var productGrName=$("#product_group_name").val();
	var grp_id=null;
    var pid=$("#product_id").val();
	if(productGrName!='')
	{
		$.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/saveProductGroup',
            type: 'POST',
            data: JSON.stringify({
                "product_group_name":productGrName,
                "product_group_id":grp_id,
                "pid":pid
              }),
            dataType: "json",
            contentType: "application/json",
            success: function (rs)
            {
            	if(rs === false)
            	{
            		alert("Name already exists.");
            		$(".close").click();
            		$("#product_group_name").val('');

            	}else
            	{
                    $("#product_group").select2().select2('val',rs);
                    $("#product_group_id").val(rs);
            		alert("Successfully saved.");
                        groupedProductList();
            		$(".close").click();
            		$("#product_group_name").val('');
                    
            	}
            }
        });
	}else
	{
		alert("Please enter new product group name");
	}
});
$("#update_product_group_name").click(function(){
	var pro_grp_id=$("#edit_product_group_id").val();
	var pro_name=$("#edit_product_group_name").val();
    var pid=null;
	if(pro_name!='')
	{
		$.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/saveProductGroup',
            type: 'POST',
            data: JSON.stringify({
                "product_group_name":pro_name,
                "product_group_id":pro_grp_id,
                "pid":null
              }),
            dataType: "json",
            contentType: "application/json",
            success: function (rs)
            {
            	if(rs === false)
            	{
            		alert("Name already exists.");
            		$(".close").click();
            		$("#edit_product_group_name").val('');
            	}else
            	{
            		alert("Successfully saved.");
                    groupedProductList();
            		$(".close").click();
                    $("#product_group").select2().select2('val',rs);
                     //location.reload();
               /*     $('.select2-chosen').attr('id',"select2-chosen-"+pro_grp_id);
                     $("#select2-chosen-"+pro_grp_id).remove();*/
            		//$("#edit_product_group_name").val('');
            	}
            }
        });
	}else
	{
		alert("Please enter product group name");
	}
});

