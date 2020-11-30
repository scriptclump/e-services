var check_cfc=0;
var check_is_creatable=0;

$(document).on('click', '#packConfig', function(e) {
 			$('#effective_date').datepicker("option", "minDate", new Date());	
			$('#effective_date').val('');
});

$("#packageLevel").on('change',function(){
    var level=$("#packageLevel").val();
    if(level=='16004')
    {
         if($("#is_cratable").prop('checked') == true)
        {
            $('#is_cratable').prop('checked', false);
        }else
        {
           $('#is_cratable').prop('checked', true);
           check_is_creatable=1;
        }
        
        if($("#palletization").prop('checked') == true)
        {
        }else
        {
           $("#palletization").prop('checked', true);
           check_cfc=1;
        }
    }else
    {
        if(check_cfc==1 )
        {
           $("#palletization").prop('checked', false);
        }
        if(check_is_creatable==1)
        {
            $('#is_cratable').prop('checked', false);
        }else
        {
             $('#is_cratable').prop('checked', true);
        }
    }
   
});
 token = $("#csrf-token").val();
$('button[href="#addpacking"]').on('click', function(e)
{

    $('form[id="package_configuration"]')[0].reset();
    $("#edit_pack_id").val('');
    $("#packageLevel").val('');
    $('#package_configuration .has-error').each(function()
    {
        $(this).removeClass('has-error');
        $(this).removeClass('err1');
    });
    if (e.originalEvent !== undefined)
    {
        $('#package_configuration')[0].reset();
        $('#pack_status').val('');
        $("#edit_pack_id").val('');
        $("#packageLevel").val('');
        $('#pack_id').val('');
    }
    else
    {

    }
});
$('button[href="#addpacking"]').on('click', function(e)
{
    $('form[id="package_configuration"]')[0].reset();
    $("#edit_pack_id").val('');
    $("#packageLevel").val('');
    $('#package_configuration .has-error').each(function()
    {
        $(this).removeClass('has-error');
        $(this).removeClass('err1');
    });
    if (e.originalEvent !== undefined)
    {
        $('#package_configuration')[0].reset();
        $('#pack_status').val('');
        $("#edit_pack_id").val('');
        $("#packageLevel").val('');
        $('#pack_id').val('');
    }
    else 
    {

    }
});
 function editPackageConfiguration(product_id)
{
    $.ajax({
    headers: {'X-CSRF-TOKEN': token},
            url: '/editPackageConfiguration/' + product_id,
            processData: false,
            contentType: false,
            success: function (rs)
            {

            $("#packConfig").click();
            var obj = jQuery.parseJSON(rs);
            $("#edit_pack_id").val(obj.pack_id);
            //console.log(obj.value);
            $("#editPackEaches").val(obj.no_of_eaches);
            $("#existedEditPackEaches").val(obj.no_of_eaches);
            $("#packageLevel").val(obj.level);
            $("#packSkuCode").val(obj.pack_sku_code);
            $("#packEaches").val(obj.no_of_eaches);
            $("#packInner").val(obj.inner_pack_count);
            $("#pack_lenght").val(obj.length);
            $("#pack_breadth").val(obj.breadth);
            $("#packEsu").val(obj.esu);
            $("#product_pack_star").val(obj.star);
            $("#pack_height").val(obj.height);
            if (obj.palletization == 1)
            {
            $('#palletization').prop('checked', true);
            }
            if (obj.is_sellable == 1)
            {
            $('#is_sellable').prop('checked', true);
            }
            if (obj.is_cratable == 1)
            {
            $('#is_cratable').prop('checked', true);
            }      
            $("#weight").val(obj.weight);
            $("#valumetricWeight").val(obj.vol_weight);
            $("#stackHeight").val(obj.stack_height);
            $("#packingMeterial").val(obj.pack_material);
            $("#palletization").val(obj.palletization);
            $("#palleteCapacity").val(obj.pallet_capacity);
            $("#pack_status").val("edit");
            $("#pack_id").val(product_id);
            $("#packWeightUOM").val(obj.weight_uom);
			$('#effective_date').datepicker('option', 'dateFormat', 'yy-mm-dd');
            
			if(obj.effective_date != null)
			{
			$('#effective_date').datepicker("option", "minDate", new Date(obj.effective_date));
			$('#effective_date').datepicker("setDate", new Date(obj.effective_date));
			}
			else
			{
			$('#effective_date').datepicker("option", "minDate", new Date());	
			$('#effective_date').val('');	
			}
            var packLevel = $('#packageLevel option');
            $.map(packLevel, function(option) {
            $('#packageLevel option[value=' + obj.level + ']').attr('selected', 'selected');
            });
           
            }
    });
}
 function delete_product_pack(pid)
{
    if (confirm('Are you sure you want to delete?'))
    {
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            url: '/deleteproductpack/' + pid + '/' + $('#product_id').val(),
            processData: false,
            contentType: false,
            success: function (rs)
            {
            $("#packingConfigGrid").igHierarchicalGrid({"dataSource":'/packingproducts/' + $('#product_id').val()});
            //alert(rs);
            }
        });
    }
}

