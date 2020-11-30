$(document).ready(function () {

    
    var date = new Date();

    $.ajaxSetup({
        headers: {'X-CSRF-Token': $('input[name="_token"]').val()},
        dataType: 'JSON'
    });

    

    
    $('#customDatePickerZone .input-daterange').datepicker({
            format: 'yyyy-mm-dd',
            endDate: "today",
            todayHighlight: true
        });


    $('#customDateWidthSubmit').click(function () {
        var fromDate = '';
        var dc_fc_type=$("#dc_fc_selection").val();
        var whid=$("#dc_fc_list").val();
        var toDate ='';
        var dashboard_filter_dates=$('#dashboard_filter_dates').val();
        var flag = '';
       

        if(dashboard_filter_dates=='custom')
        {
              fromDate=$('#fromDate').val();
            
              toDate=$('#toDate').val();  
        }

        if(dc_fc_type==undefined|| dc_fc_type=='')
        {
           alert("Please select Business Type");
        }
        else if(dc_fc_type=='1014')
        {
             flag=1; //FC type
        }else if(dc_fc_type=='1016')
        {
             flag=2; //DC type
        }
        if((whid==undefined|| whid=='') && dc_fc_type=='1014')
        {
            alert("Please select FC ");
        } 
        else if((whid==undefined|| whid=='') && dc_fc_type=='1016')
        {
            alert("Please select DC ");
        } 
        else if(dashboard_filter_dates==undefined || dashboard_filter_dates== '' )
        {
           alert("Please select Date Type");
        }
        else if((fromDate == undefined || fromDate == '') && dashboard_filter_dates=='custom'){
            alert("Please select valid From Date");
            $("#fromDate").val('');
        }
        else if((toDate == undefined || toDate == '')  && dashboard_filter_dates=='custom'){
            alert("Please select valid To Date");
            $("#toDate").val('');
        }else
        {
            
            
            location='/downloaddpr?fromDate='+fromDate+'&toDate='+toDate+'&whid='+whid+'&filter_date='+dashboard_filter_dates+'&flag='+flag;
        }
                   
        
      
    });
     
     /*Getting the DC or Fc dropDown*/
     $('#dc_fc_selection').change(function () {

       var dc_fc_selection=$(this).val();
       if(dc_fc_selection!=null && dc_fc_selection!='')
       {
          document.getElementById('customDisplayView').style.display = dc_fc_selection != '' ? 'block' : 'none';
          
       }       
       var token = $('#csrf-token').val();
        $('[class="loader"]').show();

      $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                url:"/dcfclist",
                type:"POST",
                data: 'dc_fc_type='+dc_fc_selection,
                dataType:'json',
                success:function(response){   
                 $("#dc_fc_list").empty();
                 var option = $("<option/>", {value: '', text: 'Please Select '});
                 $("#dc_fc_list").html(response.res);
                 $("#dc_fc_list").select2("val", '');
                 $('[class="loader"]').hide();

                },
                error: function(response){
                alert("Please select Business Type");
                $('[class="loader"]').hide();

                }

        }); 
    }); 

    $('[class="loader"]').hide();
});