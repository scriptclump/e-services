
var myProduct = [];

    $(function () {
        var url = window.location.href;
        var urlArr = url.split("/");
        var status  = urlArr[5];
        if(status == null)
        {
            status == "";
        }

        $("#promotionlist").igGrid({
            dataSource: '/promotions/showpromotionDetails',
            autoGenerateColumns: false,
            mergeUnboundColumns: false,
            responseDataKey: "results",
            generateCompactJSONResponse: false, 
            enableUTCDates: true, 
            width: "100%",
            height: "100%",
            columns: [
                { headerText: "Promotion Name", key: "prmt_det_name", dataType: "string", width: "14%" },
                { headerText: "Product Information", key: "ProductInformation", dataType: "string", width: "14%" },
                { headerText: "OfferType", key: "prmt_offer_type", dataType: "string", width: "9%" },
                { headerText: "Offer Value", key: "prmt_offer_value", dataType: "string", width: "7%" },
                { headerText: "State", key: "state_names", dataType: "string", width: "7%" },
                { headerText: "Offer On", key: "offer_on", dataType: "string", width: "8%" },
                { headerText: "Promotion Created", key: "created_at", dataType: "date",format:"dd-MM-yyyy", width: "10%", template: "<div style='text-align:center'>${created_at_grid}</div>" },
                { headerText: "Promotion Starts", key: "start_date", dataType: "date", format:"dd-MM-yyyy", width: "10%" ,template: "<div style='text-align:center'>${start_date_grid}</div>" },
                { headerText: "Promotion Ends ", key: "end_date", dataType: "date", format:"dd-MM-yyyy", width: "10%" ,template: "<div style='text-align:center'>${end_date_grid}</div>" },
                { headerText: "Promotion Status ", key: "PrmtStatus", dataType: "string",  width: "10%" ,template: "<div style='text-align:center'>${PrmtStatus}</div>" },
                { headerText: "Actions", key: "CustomAction", dataType: "string", width: "10%"},
                 ],
             features: [
                 {
                    name: "Sorting",
                    type: "remote",
                    columnSettings: [
                    {columnKey: 'CustomAction', allowSorting: false },
                    {columnKey: 'prmt_tmpl_name', allowSorting: true },
                    {columnKey: 'offer_type', allowSorting: true },
                    {columnKey: 'offer_on', allowSorting: true },
                    {columnKey: 'PrmtStatus', allowSorting: true },
                    ]
                },
                {
                    name: "Filtering",
                    type: "remote",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'prmt_tmpl_name', allowFiltering: true },
                        {columnKey: 'offer_type', allowFiltering: true },
                        {columnKey: 'offer_on', allowFiltering: true },
                        {columnKey: 'status', allowFiltering: false },
                        {columnKey: 'CustomAction', allowFiltering: false },
                        {columnKey: 'PrmtStatus', allowFiltering: false },
                    ]
                },
                { 
                    recordCountKey: 'TotalRecordsCount', 
                    chunkIndexUrlKey: 'page', 
                    chunkSizeUrlKey: 'pageSize', 
                    chunkSize: 20,
                    name: 'AppendRowsOnDemand', 
                    loadTrigger: 'auto', 
                    type: 'remote' 
                }
                
            ],
            primaryKey: 'prmt_tmpl_Id',
            width: '100%',
            height: '500px',
            initialDataBindDepth: 0,
            localSchemaTransform: false,
            
        });
    });     

    var lastActiveClass = "all";

    function filterdata(status){
        var sortURL = "/promotions/promotiondata?filterStatusType="+status;
        
        ds = new $.ig.DataSource({
            type: "json",
            responseDataKey: "results",
            dataSource: sortURL,
            callback: function (success, error) {
                if (success) {
                    $("#promotionlist").igGrid({
                            dataSource: ds,
                            autoGenerateColumns: false
                    });
                } else {
                    alert(error);
                }
            },
        });
        ds.dataBind();

        //change the active class
        $('#'+status).addClass('active');
        $('#'+lastActiveClass).removeClass('active');
        lastActiveClass = status;
    }
    function deleteDetailsData(deleteData){
        token  = $("#csrf-token").val();
        var promotion_delete = confirm("Are you sure you want to delete this promotion Data ?"), self = $(this);
            if ( promotion_delete == true )
            {
            $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                data: 'deleteData='+deleteData,
                type: "POST",
                url: '/promotions/deletepromotiondetails',
                success: function( data ) {
                        reloadGrid();
                    }
            });  
        }    
    }

    $("#slab-promotion-upload-button").click(function () {
        token  = $("#csrf-token").val();
        var stn_Doc = $("#upload_slabfile")[0].files[0];
        var formData = new FormData();
        formData.append('slab_data', stn_Doc);
        formData.append('test', "sample");
        $.ajax({
            type: "POST",
            headers: {'X-CSRF-TOKEN': token},
            url: "/promotions/uploadslabpromotion",
            data: formData,
            processData: false,
            contentType: false,
            success: function (data){
                
                filterdata();
                console.log(data);
                $("#success_message").html('<div class="flash-message"><div class="alert alert-success">'+data+'</div></div>');
                $(".alert-success").fadeOut(20000)

                reloadGrid();
            }
            
        });

        $('#upload-slab').modal('toggle');
    });

    $('#upload-slab').on('show.bs.modal', function (e) {        
        //emptying the field values           
        $("#mdl_manufac").val("");
        $("#mdl_brand").val("");
        $("#mdl_state").val("");
        $("#mdl_custgroup").val("");    
        $("#upload_slabfile").val("");


    });

    function reloadGrid(){
            var gridURL = "/promotions/showpromotionDetails";
            ds = new $.ig.DataSource({
                type: "json",
                responseDataKey: "results",
                dataSource: gridURL,
                callback: function (success, error) {
                    if (success) {
                        $("#promotionlist").igGrid({
                                dataSource: ds,
                                autoGenerateColumns: false
                        });
                    } else {
                        alert(error);
                    }
                },
            });
            ds.dataBind();
    }

    function reloadProductGrid(){
        var gridURL = "/promotions/productgrid";
            ds = new $.ig.DataSource({
                type: "json",
                responseDataKey: "results",
                dataSource: gridURL,
                callback: function (success, error) {
                    if (success) {
                        $("#productgrid").igGrid({
                                dataSource: ds,
                                autoGenerateColumns: false
                        });
                    } else {
                        alert(error);
                    }
                },
            });
            ds.dataBind();
    }

    function updateDetailsData(updateId){
        //window.location = "/promotions/editnewpromotion/"+updateId;
        window.open("/promotions/editnewpromotion/"+updateId, '_blank');
    }

    $("#promotion-upload-button").click(function () {
        token  = $("#csrf-token").val();
        var stn_Doc = $("#upload_promotionfile")[0].files[0];
        var formData = new FormData();
        formData.append('promotion_data_slab', stn_Doc);
        formData.append('test', "sample");
        $.ajax({
            type: "POST",
            headers: {'X-CSRF-TOKEN': token},
            url: "/promotions/uploadpromotionslab",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (data){
                $('#upload-document').modal('toggle');
                $("#success_message").html("uploaded successfully");
            }
        });
    });



    function loadBrandInModal(){
        var manufac = $("#mdl_manufac").val();
        token  = $("#csrf-token").val(); 
        $('#mdl_brand').val('');
        // prepare the ajax call to get the brand information
        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            type: "GET",
            url: '/promotions/getbrandsasmanufac/'+manufac,
            success: function( data ) {
                    if(data){
                        var brand = $('#mdl_brand');
                        brand.find('option').remove().end();
                        brand.append(
                                $('<option></option>').val('all').html("All")
                            );
                        for(var i=0; i<data.length; i++){
                            brand.append(
                                $('<option></option>').val(data[i].brand_id).html(data[i].brand_name)
                            );
                        }
                    }
                    $('#mdl_brand').val('');
                }
        });
    }
