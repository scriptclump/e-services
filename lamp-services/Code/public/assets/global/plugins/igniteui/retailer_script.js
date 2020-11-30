
$('#retalir_list_grid').igGrid({
    dataSource: '/retailers/getRetailers',
    autoGenerateColumns: false,
    autoGenerateLayouts: false,
    mergeUnboundColumns: false,
    responseDataKey: 'Records',
    generateCompactJSONResponse: false,
    enableUTCDates: true, 
    renderCheckboxes: true,
    columns: [
        {headerText: 'Logo', key: 'logo', dataType: 'number', width: '5%'},
        {headerText: 'Retailer', key: 'business_legal_name', dataType: 'string', width: '15%'},
        {headerText: 'Mobile Number', key: 'mobile_no', dataType: 'string', width: '15%'},
        {headerText: 'Address', key: 'address', dataType: 'string', width: '15%'},
        {headerText: '#Shutters', key: 'No_of_shutters', dataType: 'int', width: '5%'},
        {headerText: 'Volume', key: 'master_lookup_name', dataType: 'string', width: '5%'},
        {headerText: 'City', key: 'city', dataType: 'string', width: '10%'},
        {headerText: 'State', key: 'state', dataType: 'string', width: '10%'},
        {headerText: 'Pincode', key: 'pincode', dataType: 'string', width: '5%'},
        {headerText: 'Is Approved', key: 'is_approved', dataType: 'string', width: '10%'},
        {headerText: 'Profile Completed', key: 'profile_completed', dataType: 'string', width: '10%'},
        {headerText: 'Actions', key: 'actions', dataType: 'string', width: '10%'},
        {headerText: 'LegalEntityId', key: 'legal_entity_id', dataType: 'number', width: '0%'},
        
        
        
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
                {columnKey: 'logo', allowFiltering: false},                
                {columnKey: 'actions', allowFiltering: false},                
            ]
        },
        {
            name: 'Sorting',
            type: 'local',
            persist: false,
            columnSettings: [
                {columnKey: 'logo', allowSorting: false},                
            ]

        }
    ],
    primaryKey: 'legal_entity_id',
    width: '100%',
    initialDataBindDepth: 0,
    localSchemaTransform: false
});
		