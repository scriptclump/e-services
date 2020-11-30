<html>
    <table>
        <tr>   
            <th>Trip Date</th>
            <th>HUB</th>
            <th>Area/Beats</th>
            <th>Docket No</th>
            <th>Order No</th>
            <th>Invoice No</th>
            <th>Retailer Name</th>
            <th>Total Cartons</th>
            <th>Total Crates</th>
            <th>Total Bags</th>
            <th>Total</th>
        </tr>
        <?php $temp = '';
        $totCartons = 0;
        $totcrates =0; 
        $totBags =0;
        $tot = 0;
        ?>
        <?php 
        for($i=0;$i<count($Reportinfo);$i++)	
        {?>
        <tr>
            <td>{{ $Reportinfo[$i]['st_del_date'] }}</td>
            <td>{{ $Reportinfo[$i]['lp_wh_name'] }}</td>            
            <td>{{ $Reportinfo[$i]['beat_area'] }}</td> 
            <td>{{ $Reportinfo[$i]['st_docket_no'] }}</td>
            <td>{{ $Reportinfo[$i]['order_code'] }}</td>
            <td>{{ $Reportinfo[$i]['invoice_code'] }}</td>
            <td>{{ $Reportinfo[$i]['shop_name'] }}</td>            
            <td>{{ $Reportinfo[$i]['cfc_cnt'] }}</td>            
            <td>{{ $Reportinfo[$i]['crates_cnt'] }}</td>            
            <td>{{ $Reportinfo[$i]['bags_cnt'] }}</td>            
            <td>{{ ($Reportinfo[$i]['cfc_cnt']+$Reportinfo[$i]['crates_cnt']+$Reportinfo[$i]['bags_cnt']) }}</td>  
        </tr>
        <?php
        $totCartons += $Reportinfo[$i]['cfc_cnt'];
        $totcrates += $Reportinfo[$i]['crates_cnt'];
        $totBags +=  $Reportinfo[$i]['bags_cnt'];
        $tot += ($Reportinfo[$i]['cfc_cnt']+$Reportinfo[$i]['crates_cnt']+$Reportinfo[$i]['bags_cnt']);           
        $temp = isset($Reportinfo[$i+1]['lp_wh_name'])?$Reportinfo[$i+1]['lp_wh_name']:'last';
        if($temp == 'last' || $Reportinfo[$i]['lp_wh_name'] != $temp){ ?>
            <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>            
            <td>Total</td>            
            <td>{{ $totCartons }}</td>            
            <td>{{ $totcrates }}</td>            
            <td>{{ $totBags }}</td>            
            <td>{{ $tot }}</td>  
            </tr>
        <?php 
        $totCartons = 0;
        $totcrates =0; 
        $totBags =0;
        $tot = 0;
        }        
    } ?>
    </table>
    
</html>