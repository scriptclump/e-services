
function removeError(fldId) {
	if($('#'+fldId).val() !='') {
		$('#'+fldId).removeClass('error-style').addClass('success-style');
	}
}

function getCheckedBox() {
	var checked = false;
	$("input[name='orderItems[]']").each( function () {
		   if($(this).prop('checked') == true){
			  checked = true;
			  return;
		   }
	});
	return checked;
}

function getCarrierServices() {
	var courierId = $('#courier').val();
	 $.ajax({
			headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
			url: "/salesorders/shipping",
			type: "POST",
			data: {courierId:courierId},
			dataType: 'json',
			success: function(response) {
				$('#service_selectbox').html(response.data);
			},
			error:function(response){
				$('#cancelAjaxResponse').html('Unable to saved comment');
			}
	});    
}
function validateTracking() {
	var carrier = $('#courier').val();
	var service_name = $('#service_name').val();
	var carrier_name = $('#courier option:selected').text();			
	var service_text = $('#service_name option:selected').text();			
	var track_number = $('#track_number').val();			
	var vehicle_number = $('#vehicle_number').val();
	var representative_name = $('#representative_name').val();
	var contact_num = $('#contact_num').val();
	var isValid = 0;
	
	$('#courier').addClass('error-style');
	$('#service_name').addClass('error-style');
	$('#track_number').addClass('error-style');
	
	if(contact_num != '' && validatePhone(contact_num) == false) {
		isValid = 0;
	}

	if(carrier_name == 'Self Shipment') {
		$('#track_number').removeClass('error-style');
		$('#vehicle_number').addClass('error-style');
		$('#representative_name').addClass('error-style');
		$('#contact_num').addClass('error-style');
	}
				
	if(carrier != '' && service_name != '' && track_number != '') {				
		isValid = 1;
	}
	else if(carrier_name == 'Self Shipment' && vehicle_number != '' && representative_name != '' && contact_num != '' && validatePhone(contact_num) == true) {				
		isValid = 1;
	}
	
	if(isValid) {
		$('#courier').removeClass('error-style').addClass('success-style');
		$('#service_name').removeClass('error-style').addClass('success-style');
		$('#track_number').removeClass('error-style').addClass('success-style');
		$('#vehicle_number').removeClass('error-style').addClass('success-style');
		$('#representative_name').removeClass('error-style').addClass('success-style');
		$('#contact_num').removeClass('error-style').addClass('success-style');

		$('#track_data').append('<tr><td><input type="hidden" name="carriers[]" value="'+carrier+'">'+carrier_name+'</td><td><input type="hidden" name="services[]" value="'+service_name+'">'+service_text+'</td><td><input type="hidden" name="track_numbers[]" value="'+track_number+'">'+track_number+'</td><td><input type="hidden" name="vehicle_numbers[]" value="'+vehicle_number+'">'+vehicle_number+'</td><td><input type="hidden" name="representatives[]" value="'+representative_name+'">'+representative_name+'</td><td><input type="hidden" name="contacts[]" value="'+contact_num+'">'+contact_num+'</td><td><a href="javascript:void(0)" id="removeTrack">-</a></td></tr>');
		$('#courier').val('').removeClass('success-style');
		$('#service_name').val('').removeClass('success-style');		
		$('#track_number').val('').removeClass('success-style');			
		$('#vehicle_number').val('').removeClass('success-style');
		$('#representative_name').val('').removeClass('success-style');
		$('#contact_num').val('').removeClass('success-style');
		$('#tracking-error').hide();
	}
}

function validatePhone(phone) {
	var pattern = /^\d{10}$/;
	if (pattern.test(phone)) {
		return true;
	}
	return false;
}

function filterOrder(status) {
	var formData = $('#frm_sales_orders').serialize();
	var filterURL = "/salesorders/ajax/index/"+status+"?"+formData;
	
	$("#orderList").igGrid({
						dataSource: filterURL,
						autoGenerateColumns: false
					});
	getOrderStats();					
}

function getOrderStats() {
	
	var formData = $('#frm_sales_orders').serialize();
	$.ajax({
		headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
		url: "/salesorders/getorderstats",
		type: "GET",
		data: formData,
		dataType: 'json',
		success: function(response) {
			$('#allorders').html(response.all);
			$('#delivered').html(response.delivered);
			$('#process').html(response.process);
			$('#completed').html(response.completed);
		},
		error:function(fail){
			alert('fails');
		}
	});  
}

function getStats(orderId) {
	$.ajax({
		headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
		url: "/salesorders/getStats",
		type: "GET",
		data: {orderId:orderId},
		dataType: 'json',
		success: function(response) {
			$('#totalInvoices').html(response.message.totalInvoices);
			$('#totalShipments').html(response.message.totalShipments);
			$('#totalComments').html(response.message.totalComments);
			$('#totCancelled').html(response.message.totCancelled);
			$('#totReturns').html(response.message.totReturns);
			$('#totRefunds').html(response.message.totRefunds);
			$('#totalPayments').html(response.message.totPayments);
			$('#totNctHistory').html(response.message.totNctHistory);
			$('#totVerification').html(response.message.totVerification);
		},
		error:function(response){
		}
	});  
}

function getOrderDetail(orderId) {
	//$('#tab_1').html('<div align="center"><img src="/img/ajax-loader1.gif"></div>');
	$.ajax({
		headers:{'X-CSRF-Token': $('input[name="_token"]').val()},
		url: "/salesorders/getOrderDetail",
		type: "GET",
		data: {orderId:orderId},
		dataType: 'json',
		success: function(response) {
			$('#tab_1').html(response.message);
		},
		error:function(response){
		}
	});  
}

function validateForm() {
	
	$("#order_status_form").validate({
			rules: {
				//orderStatus: "required",
				//order_comment: "required"		
			},
			submitHandler: function(form) {				
				 var form = $('#order_status_form');
				 $('.loderholder').show();
				   $.ajax({
							url: form[0].action,
							type: form[0].method,
							data: form.serialize(),
							dataType: 'json',
							success: function(data) {
								if(data.status == 200) {
									$('.loderholder').show();
									$('#ajaxResponse').removeClass('text-danger').addClass('text-success').html(data.message);
									window.setTimeout(function(){location.reload()},2000);
									//window.location.href = '/salesorders/addshipment}}';
								}
								else {
									$('.loderholder').hide();
									$('#ajaxResponse').removeClass('text-success').addClass('text-danger').html(data.message);
								}
							},
							error:function(response){
								$('.loderholder').hide();
							$('#ajaxResponse').html('Unable to saved comment');
						  }
					});
			}
		});
}

function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }

 function getChkVal() {
    var selected = [];

    $("input[name='chk[]']").each( function () {
       if($(this).val() != 'on') {
        if($(this).prop('checked') == true){
            selected.push($(this).val());
        }
       }
   });

    return selected;
 }

 function zeroPad(num, count) {
    var numZeropad = num + '';
    while (numZeropad.length < count) {
        numZeropad = "0" + numZeropad;
    }
    return numZeropad;
}
function getNextDay(select_date){
    select_date.setDate(select_date.getDate() + 1);
    var setdate = new Date(select_date);
    var nextdayDate = zeroPad((setdate.getMonth()+1),2)+'/'+zeroPad(setdate.getDate(),2)+'/'+setdate.getFullYear();
    return nextdayDate;
}

function getStatusVal() {
    var selected = [];
   
    $("input[name='chk[]']").each( function () {
       if($(this).val() != 'on') {
        if($(this).prop('checked') == true){
            var chkId = $(this).val();
            var statusVal = $('#'+chkId).val();
            selected.push(statusVal);
        }
       }
   });

    return selected;
 }

function getHubsVal() {
    var selected = [];
   
    $("input[name='chk[]']").each( function () {
       if($(this).val() != 'on') {
        if($(this).prop('checked') == true){
            var hubId = $(this).parent().find('input[name="hubIds[]"]').val();
            
            if(jQuery.inArray(hubId, selected) == -1) {
	            selected.push(hubId);
            }
        }
       }
   });

    return selected;
 }

$(document).on('click','#edit_order',function(){
  var selected = $('#order_id_toedit').val();
  let token = $('input[name="_token"]').val();
    $.ajax({
      headers: {'X-CSRF-TOKEN': token},
      method: "POST",
      url: '/salesorders/checkcancellations?order_id='+selected,
      dataType: 'json',
      success:function(res){
        console.log(res);
        if(res.count > 0){
          alert('You are not allowed to edit as there are cancel items in this order!');
        }else{
        	window.open(window.location.origin+'/salesorders/editorder?order_ids='+selected,
  '_blank');
          //window.location.href = '/salesorders/editorder?order_ids='+selected;
        }
      }
    });
 
});
$(document).on('click','#openInvoice',function(){
	var selected = [$('#order_id_toedit').val()];
	var status =[$('#order_status_val').val()];
    var res = confirm("Are you sure do you want to 'Generate Invoice' for this order?");
	 if(selected.length>0) {
	 if(res==true){
        $('.loderholder').show();
        $.ajax({
          headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
          url: "/salesorders/generateInvoiceFromOpen",
          type: "POST",
          data: {ids: selected, statusCodes:status},
          dataType: 'json',
          success: function (responses) {
              if(responses.status==200){
                $('#ajaxResponse').removeClass('alert-danger').addClass('alert-success').html('Invoice generated successfully.').show();
                $('.loderholder').hide();
                location.reload(true);
              }else if(responses.message.status_type != undefined && responses.message.status_type =="inventory_error"){
                $("#order_code_inv").html(responses.message.order_code);
                $("#inv_table_body").html(responses.message.inv_html);                
                $("#invoiceError").modal("show");
                $('.loderholder').hide();
              }else{
                $('#ajaxResponse').removeClass('alert-success').addClass('alert-danger').html(responses.message).show();
                $('.loderholder').hide();
                //location.reload(true);
              }
          },
          error: function (response) { }
        });
        
      }
  }

});
