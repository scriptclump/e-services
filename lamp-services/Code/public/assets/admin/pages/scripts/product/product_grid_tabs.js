$(document).ready(function(){ 
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) 
    {
        var targetedTab = $(e.target).attr("href") // activated tab
        var product_id=$('#product_id').val();
        var html_code="";
        switch(targetedTab)
        {
            case '#tab_15_2':
                    childproductlist(product_id);   
                    break;
            case '#freebie':                                     
                    freeBieConfigGrid();
                    break;
            case '#tab_15_3':
                    packingConfigGrid();
                    break;
             case '#grouped_products':
                    groupedProductsGrid();
                    break;
            case '#tab_15_4':
                    productTax(product_id);
                    break;
            case '#tab_15_5':
                    productSuppliersGrid();
                    break;
            case '#tab_15_8':
                    inventoryProductGrid1(product_id);
                    break;
            case '#promotion_tab': 
                    slabPrices(product_id);
                    break;
            case '#warehouse_config': 
                    warehouseBinConfigGrid();
                    break;
            case '#product_history': 
                   productHistoryGrid();
                    break;
            case '#cpenable': 
                   cpEnableDcorFc();
                    break;        
            case '#product_elp_history': 
                   productELPHistory();
                    break;
            case '#customer_type_esu': 
                   customerTypeEsu();
                    break; 

        }
        
    });
});