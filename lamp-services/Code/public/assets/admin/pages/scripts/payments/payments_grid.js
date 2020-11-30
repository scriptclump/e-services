function paymentsApproval(del_exec,del_fdate,del_tdate,status) {

    $('.paymentsApprovalGrid').igHierarchicalGrid({
        dataSource: '/payments/getPaymentsByDelExec/'+del_exec+'/'+del_fdate+'/'+del_tdate+'/'+status,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'data',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [                     
            {headerText: '', key: 'remittance_id', dataType: 'number', width: '0px'},
            //{headerText: '', key: 'chk', dataType: 'string', width: '20px'},
            {headerText: 'Id', key: 'remittance_code', dataType: 'string', width: '130px', columnCssClass: "leftAlignment"},
            {headerText: 'Collection Date', key: 'collection_date', dataType: 'date',format:"dd-MM-yyyy",width: '110px', columnCssClass: "leftAlignment"},
            {headerText: 'Submitted Date', key: 'submit_on', dataType: 'date',format:"dd-MM-yyyy", width: '110px', columnCssClass: "leftAlignment"},
            {headerText: 'Submitted By', key: 'SubmittedByName', dataType: 'string', width: '100px', columnCssClass: "leftAlignment"},   
            {headerText: 'DC', key: 'DCName', dataType: 'string', width: '100px', columnCssClass: "leftAlignment"},   
            {headerText: 'Hub', key: 'hub_name', dataType: 'string', width: '100px', columnCssClass: "leftAlignment"},   
            {headerText: 'Collected Amount', key: 'collected_amt', dataType: 'number',format:'0.00',width: '120px', columnCssClass: "rightAlignment",template:'<div class="rightAlignment">${collected_amt}</div>'},            
            {headerText: 'Deposited Amount', key: 'amount_deposited', dataType: 'number',format:'0.00', width: '120px', columnCssClass: "rightAlignment"},            
            {headerText: 'Cash', key: 'by_cash', dataType: 'number',format:'0.00',width: '70px', columnCssClass: "rightAlignment"},            
            {headerText: 'Cheque', key: 'by_cheque', dataType: 'number',format:'0.00', width: '70px', columnCssClass: "rightAlignment"},            
            {headerText: 'Online', key: 'by_online', dataType: 'number',format:'0.00', width: '70px', columnCssClass: "rightAlignment"},            
            {headerText: 'UPI', key: 'by_upi', dataType: 'number',format:'0.00', width: '70px', columnCssClass: "rightAlignment"},            
            {headerText: 'POS', key: 'by_pos', dataType: 'number', width: '70px', columnCssClass: "rightAlignment"},            
            {headerText: 'eCash', key: 'by_ecash', dataType: 'number',format:'0.00', width: '70px', columnCssClass: "rightAlignment"},            

            {headerText: 'Fuel', key: 'fuel', dataType: 'number',format:'0.00', width: '70px', columnCssClass: "rightAlignment"},   
            {headerText: 'Vehicle', key: 'vehicle', dataType: 'number',format:'0.00', width: '70px', columnCssClass: "rightAlignment"},            
            {headerText: 'Short', key: 'due_amount', dataType: 'number',format:'0.00', width: '70px', columnCssClass: "rightAlignment"},            

            {headerText: 'Small Coins', key: 'coins_onhand', dataType: 'number',format:'0.00', width: '120px', template: '<div class="rightAlignment"> ${coins_onhand} </div>'},   
            {headerText: 'Notes on Hand', key: 'notes_onhand', dataType: 'number',format:'0.00', width: '100px', template: '<div class="rightAlignment"> ${notes_onhand} </div>'},            
            {headerText: 'Used for Expenses', key: 'used_expenses', dataType: 'number',format:'0.00', width: '120px', template: '<div class="rightAlignment"> ${used_expenses} </div>'},            
            {headerText: 'Due Deposited', key: 'arrears_deposited', dataType: 'number',format:'0.00', width: '120px', template: '<div class="rightAlignment"> ${arrears_deposited} </div>'},            
            {headerText: 'Arrears Deposited', key: 'coins_notes_deposited', dataType: 'number',format:'0.00', width: '120px', template: '<div class="rightAlignment"> ${coins_notes_deposited} </div>'},            


            {headerText: 'Ack By', key: 'acknowledged_by', dataType: 'string', width: '120px', columnCssClass: "leftAlignment"},            
            {headerText: 'Ack On', key: 'ack_on', dataType: 'date',format:"dd-MM-yyyy", width: '120px', columnCssClass: "leftAlignment"},            
            {headerText: 'Status', key: 'remittance_status', dataType: 'string', width: '120px', columnCssClass: "leftAlignment"},
//            {headerText: 'Actions', key: 'action', width: '10%', template: "<center style='border-width:1px; border-style:solid; border-color:#efefef; height:32px; width:32px; line-height:30px; display:block; background:#fff;'><a target='_blank' href='/salesorders/detail/${gds_order_id}#payments'>Details</a></center>"},
            {headerText: 'Actions', key: 'action', width: '70px', template:"<i class='fa fa-eye remittanceDetail' style='cursor:pointer'></i>&nbsp;<a href='#pmtHistory' data-toggle='modal' data-remittance='${remittance_id}' class='remittanceHistoryDetail'><i class='fa fa-history' style='cursor:pointer'></i></a>", columnCssClass: "leftAlignment"},
            {headerText: '', key: 'total_collected_amt',dataType: 'string', width: '0px'},
            {headerText: '', key: 'total_by_cash',dataType: 'total_by_cash', width: '0px'},
            {headerText: '', key: 'total_by_cheque',dataType: 'string', width: '0px'},
            {headerText: '', key: 'total_by_online',dataType: 'string', width: '0px'},
            {headerText: '', key: 'total_by_upi',dataType: 'string', width: '0px'},
            {headerText: '', key: 'total_by_ecash',dataType: 'string', width: '0px'},
            {headerText: '', key: 'total_by_pos',dataType: 'string', width: '0px'},


        ],

        columnLayouts: [
        {
            dataSource: 'payments/getRemittanceDetails',
            autoGenerateColumns: false,
            autoGenerateLayouts: false,
            mergeUnboundColumns: false,
            responseDataKey: 'data',
            generateCompactJSONResponse: false,
            enableUTCDates: true,
            columns: [
                {headerText: 'Retailer Name', key: 'customer_name', dataType: 'string', width: '15%'},
                {headerText: 'Order Number', key: 'order_code', dataType: 'string', width: '15%'},
                {headerText: 'Collected On', key: 'collected_on', dataType: 'string', width: '15%'},
                {headerText: 'Collected By', key: 'collected_by', dataType: 'string', width: '15%'},
                {headerText: 'Collected Amt', key: 'amount', dataType: 'string', width: '15%'},
                {headerText: 'Payment Mode', key: 'payment_mode', dataType: 'string', width: '15%'},
                {headerText: 'Proof', key: 'proof', dataType: 'string', width: '10%'},
                {headerText: 'Invoice Code', key: 'invoice_code', dataType: 'string', width: '15%'},            
                {headerText: 'Invoice Amount', key: 'invoice_amount', dataType: 'string', width: '15%'},            
                {headerText: 'Return Code', key: 'return_code', dataType: 'string', width: '15%'},   
                {headerText: 'Return Amount', key: 'return_total', dataType: 'string', width: '15%'},   
                {headerText: 'Cancel Code', key: 'cancel_code', dataType: 'string', width: '15%'},
                {headerText: 'Cancel Amount', key: 'cancel_total', dataType: 'string', width: '15%'},
                //{headerText: 'Acknowledged By', key: 'AckBy',dataType: 'string', width: '15%'},
                //{headerText: 'Actions', key: 'action', width: '5%'}
            ],
            key: 'Remittance',
            foreignKey: 'remittance_id',
            primaryKey: 'WarehouseId',
            width: '100%',
            features: [
                {
                    name: 'Paging',
                    type: "local",
                    pageSize: 10
                },
                {
                    name: "Filtering",
                    type: "local",
                    mode: "simple",
                    filterDialogContainment: "window",
                    columnSettings: [
                        {columnKey: 'Action', allowFiltering: false},
                    ]
                },
                {
                    name: 'Sorting',
                    type: 'local',
                    persist: false,
                    columnSettings: [
                        {columnKey: 'Action', allowSorting: false},
                    ]

                }]
        }],

        features: [           
            {
                name: 'Paging',
                type: 'remote',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "remote",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'action', allowFiltering: false},
                    {columnKey: 'by_cash',allowFiltering:true},
                    {columnKey: 'by_cheque',allowFiltering:true},
                    {columnKey: 'collected_amt',allowFiltering:true},
                    {columnKey:'amount_deposited',allowFiltering:true},
                    {columnKey: 'by_ecash',allowFiltering:true},
                    {columnKey: 'collection_date',allowFiltering:false},
                    {columnKey: 'SubmittedByName',allowFiltering:false},
                    {columnKey: 'DCName',allowFiltering:false},
                   // {columnKey: 'acknowledged_by',allowFiltering:false},
                    {columnKey: 'remittance_status',allowFiltering:false},




                ]
            },
            {
                    name: 'Sorting',
                    type: 'remote',
                    //persist: false,
                    columnSettings: [
                        {columnKey: 'action', allowSorting: false},
                        {columnKey: 'by_cash',allowSorting:true},
                        {columnKey: 'by_cheque',allowSorting:true},
                        {columnKey: 'collected_amt',allowSorting:true},
                        {columnKey:'amount_deposited',allowSorting:true},
                        {columnKey:'by_ecash',allowSorting:true},
                        {columnKey: 'collection_date',allowSorting:false},
                       // {columnKey: 'SubmittedByName',allowSorting:false},
                        //{columnKey: 'DCName',allowSorting:false},
                       // {columnKey: 'acknowledged_by',allowSorting:false},
                        {columnKey: 'remittance_status',allowSorting:false},

                    ]

            }
        ],
        primaryKey: 'remittance_id',
        //width: '100%',
        dataRendered: function(evt, ui) {
          $('.balanceAmt').html($('td[aria-describedby="_BalanceAmt"]').html());

          $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
            $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
            $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
            $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
            $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
            $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
            $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
            $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
            $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
            //$("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericonon").closest("li").remove();   
           // $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();
            //$("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericonafter").closest("li").remove();
            $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericonyesterday").closest("li").remove();
            $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericontoday").closest("li").remove();
            $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
            $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericonendswith").closest("li").remove();
            $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
            $("#paymentsApprovalGrid_container").find(".ui-iggrid-filtericonnoton").closest("li").remove();





          
          

          populateTotals();
          $('.showConsolidate').show();
        },
        initialDataBindDepth: 0,
        localSchemaTransform: false});
}

function collectionDetails(del_exec,del_fdate,del_tdate) {

    $('.paymentsApprovalGrid').igGrid({
        dataSource: '/payments/getCollectionsByDelExec/'+del_exec+'/'+del_fdate+'/'+del_tdate,
        autoGenerateColumns: false,
        autoGenerateLayouts: false,
        mergeUnboundColumns: false,
        responseDataKey: 'data',
        generateCompactJSONResponse: false,
        enableUTCDates: true,
        columns: [                     
            {headerText: '', key: 'collection_id', dataType: 'number', width: '0%'},
            {headerText: 'Collected On', key: 'created_on', dataType: 'string', width: '10%'},
            {headerText: 'Collection Code', key: 'collection_code', dataType: 'string', width: '14%'},
            {headerText: 'Collected Amt', key: 'collected_amount', dataType: 'number', width: '11%', template: '<div class="textRightAlign"> ${collected_amount} </div>'},            
            {headerText: 'Customer Name', key: 'customer_name', dataType: 'string', width: '13%'},
            {headerText: 'Invoice Code', key: 'invoice_code', dataType: 'string', width: '13%'},   
            {headerText: 'Invoice Amt', key: 'invoice_amount', dataType: 'number', width: '13%', template: '<div class="textRightAlign"> ${invoice_amount} </div>'},   
            {headerText: 'Order Code', key: 'order_code', dataType: 'string', width: '14%'},            
            {headerText: 'Return Code', key: 'return_code', dataType: 'string', width: '14%'},            
            {headerText: 'Return Amt', key: 'return_total', dataType: 'string', width: '10%', template: '<div class="textRightAlign"> ${return_total} </div>'},            
            {headerText: 'Cancel Code', key: 'cancel_code', dataType: 'number', width: '14%'},            
            {headerText: 'Cancel Amt', key: 'cancel_total', dataType: 'string', width: '10%', template: '<div class="textRightAlign"> ${cancel_total} </div>'},            
            {headerText: 'Payment Mode', key: 'payment_mode', dataType: 'string', width: '13%'},
            {headerText: 'Proof', key: 'proof', width: '7%'},
        ],

        features: [           
            {
                name: 'Paging',
                type: 'local',
                pageSize: 10,
                recordCountKey: 'TotalRecordsCount',
                pageIndexUrlKey: "page",
                pageSizeUrlKey: "pageSize"
            },
            {
                name: "Filtering",
                type: "local",
                mode: "simple",
                filterDialogContainment: "window",
                columnSettings: [
                    {columnKey: 'proof', allowFiltering: false},
                ]
            }
        ],
        primaryKey: 'collection_id',
        //width: '100%',
        dataRendered: function(evt, ui) {          
          //igGridHideOption();
          $(".ui-iggrid-fixcolumn-headerbuttoncontainer").remove();
          $('.balanceAmt').html($('td[aria-describedby="_BalanceAmt"]').html());
        },

        initialDataBindDepth: 0,
        localSchemaTransform: false});
}

function igGridHideOption() {
  $(".paymentsApprovalGrid").find(".ui-iggrid-filtericongreaterthanorequalto").closest("li").remove();
  $(".paymentsApprovalGrid").find(".ui-iggrid-filtericonlessthanorequalto").closest("li").remove();
  $(".paymentsApprovalGrid").find(".ui-iggrid-filtericondoesnotequal").closest("li").remove();    
  $(".paymentsApprovalGrid").find(".ui-iggrid-filtericonthismonth").closest("li").remove();
  $(".paymentsApprovalGrid").find(".ui-iggrid-filtericonlastmonth").closest("li").remove();   
  $(".paymentsApprovalGrid").find(".ui-iggrid-filtericonnextmonth").closest("li").remove();   
  $(".paymentsApprovalGrid").find(".ui-iggrid-filtericonthisyear").closest("li").remove();   
  $(".paymentsApprovalGrid").find(".ui-iggrid-filtericonlastyear").closest("li").remove();   
  $(".paymentsApprovalGrid").find(".ui-iggrid-filtericonnextyear").closest("li").remove();   
  $(".paymentsApprovalGrid").find(".ui-iggrid-filtericonon").closest("li").remove();   
  $(".paymentsApprovalGrid").find(".ui-iggrid-filtericonnoton").closest("li").remove();
  $(".paymentsApprovalGrid").find(".ui-iggrid-filtericonstartswith").closest("li").remove();
  $(".paymentsApprovalGrid").find(".ui-iggrid-filtericonendswith").closest("li").remove();
  $(".paymentsApprovalGrid").find(".ui-iggrid-filtericondoesnotcontain").closest("li").remove();
  
  $(".ui-iggrid-fixcolumn-headerbuttoncontainer").remove();
}

function populateTotals() {

        var total_collected_amt = 0;
        var total_by_cash = 0;
        var total_by_cheque = 0;
        var total_by_online = 0;
        var total_by_upi = 0;
        var total_by_ecash = 0;
        var total_by_pos=0;

        $(".ui-iggrid-tablebody tr").each(function(a,val) {
            total_collected_amt+=parseFloat($(this).find('td[aria-describedby="paymentsApprovalGrid_collected_amt"]').html()) || 0.00;
            total_by_cash+=parseFloat($(this).find('td[aria-describedby="paymentsApprovalGrid_total_by_cash"]').html()) || 0.00;
            total_by_cheque+=parseFloat($(this).find('td[aria-describedby="paymentsApprovalGrid_total_by_cheque"]').html()) || 0.00;
            total_by_online+=parseFloat($(this).find('td[aria-describedby="paymentsApprovalGrid_total_by_online"]').html()) || 0.00;
            total_by_upi+=parseFloat($(this).find('td[aria-describedby="paymentsApprovalGrid_total_by_upi"]').html()) || 0.00;
            total_by_ecash+=parseFloat($(this).find('td[aria-describedby="paymentsApprovalGrid_total_by_ecash"]').html()) || 0.00;
            total_by_pos+=parseFloat($(this).find('td[aria-describedby="paymentsApprovalGrid_total_by_pos"]').html()) || 0.00;

        });


        $('.total_collected_amt').html(total_collected_amt);
        $('.total_by_cash').html(total_by_cash);
        $('.total_by_cheque').html(total_by_cheque);
        $('.total_by_online').html(total_by_online);
        $('.total_by_upi').html(total_by_upi);
        $('.total_by_ecash').html(total_by_ecash);
        $('.total_by_pos').html(total_by_pos);

}