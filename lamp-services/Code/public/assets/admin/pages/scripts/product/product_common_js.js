
$("#add_product_group_name").val('');
$("#save_new_product_group_id").click(function()
{
	var product_group_name=$("#add_product_group_name").val();
	alert(product_group_name);
	if(product_group_name!='')
	{
		$.ajax({
	        headers: {'X-CSRF-TOKEN': token},
	        url: '/products/saveProductGroupName/'+product_group_name,
	        type: 'POST',
	        success: function (rs)
	        {
	           if(rs=='false')
	           {
	           	  	alert("sorry this name is already exist.");
	           		$(".close").click();
	           }
	           else
	           {
	           	alert("successfully saved.");
	           	$(".close").click();
	           }
	        }
        });
	}else
	{
		alert("Please enter product group name.");
	}
});
