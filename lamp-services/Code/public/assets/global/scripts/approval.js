function approval(url)
{
	var formData = new FormData();
    var token = $("#csrf-token").val();  
    formData.append('approval_for_id',$("#approval_for_id").val());
    formData.append('approval_type_id',$("#approval_type_id").val());           
    formData.append('_token',token);
           
        $.ajax({
                  headers: {'X-CSRF-TOKEN': token},
                   method: "POST",
                   url: '/approveproduct',
                   processData: false,
                   contentType: false,                                             
                   data: formData,
                   success: function (rs) 
                      {
                         if(rs !== '0')
                         {
                             $('#approval_select_id').html(rs);
                             $('#approval_save').css('display','inline');
                             $('#approva_comments').css('display','block');
                             $('#approva_comments').css('margin-left','250px');
                            $('#approval_row_id').css('display','block');
                             $('#approval_row_id2').css('display','block');                        
                         }

                     }
            
        });
          
    $('#product_close_id').on('click', function (e) {
         e.preventDefault();
    });
    
    }
    function approvalSave(url) {            
        var formData = new FormData();
        var token = $("#csrf-token").val();  
        formData.append('approval_comments', $("#approval_comments").val());              
        formData.append('_token',token);
        formData.append('approval_select_id',$("#approval_select_id").val());
        formData.append('approval_for_id',$("#approval_for_id").val());
        formData.append('approval_type_id',$("#approval_type_id").val());  
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            method: "POST",
            url: '/approvalsave',
            processData: false,
            contentType: false,                                             
            data: formData,
            success: function (rs) {
                
                $('#approval_save').css('display','none');
                $('#approva_comments').css('display','none');                                
                $('#approval_row_id').css('display','none');
                $('#approval_row_id2').css('display','none');        
                alert('Approval submitted Successfully.');
                window.location=url;
            }
        });

    
}	