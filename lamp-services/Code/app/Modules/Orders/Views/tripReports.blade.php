<html>
    <table>
        <tr>   
            <th>Date:</th>
            <td><?php echo date('d/m/Y H:i:s'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <th>Hub Name:</th>
            <td>{{ isset($Reportinfo[0]) ? $Reportinfo[0]['lp_wh_name'] : ''}}</td>
        </tr>
        <tr>
            <th>Vehicle No:</th>
            <td>{{ isset($Reportinfo[0]) ? $Reportinfo[0]['st_vehicle_no'] : '' }}</td>
            <td></td>
            <td></td>
            <td></td>
            <th>Driver Name:</th>
            <td>{{ isset($Reportinfo[0]) ? $Reportinfo[0]['st_driver_name'] : '' }} <?php (isset($Reportinfo[0]) && !empty($Reportinfo[0]['st_driver_mobile'])) ? '(M: '. $Reportinfo[0]['st_driver_mobile'] .') ': ''; ?></td>
        </tr>
        <tr>
            <th>DC Address:</th>
            <td colspan="3" style="word-wrap: break-word; vertical-align: middle; height: 30px"><?php echo isset($Reportinfo[0]) ? $Reportinfo[0]['wh_name'].', '.$Reportinfo[0]['wh_address1'].', '.$Reportinfo[0]['wh_address2'].', '.$Reportinfo[0]['wh_city'].', '.$Reportinfo[0]['wh_pincode'] : ''; ?></td>
            <td></td>
            <th>Hub Address:</th>
            <td colspan="3" style="word-wrap: break-word; vertical-align: middle; height: 30px"><?php echo isset($Reportinfo[0]) ? $Reportinfo[0]['lp_wh_name'].', '.$Reportinfo[0]['hub_address1'].', '.$Reportinfo[0]['hub_address2'].', '.$Reportinfo[0]['hub_city'].', '.$Reportinfo[0]['hub_pincode'] : ''; ?></td>
        </tr>
         <tr>   
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <thead>
        <tr>   
            <th style="width: 14px;">Sr. No.</th>
            <th style="width: 14px;">Container Id</th>
            <th style="width: 18px;">Docket No</th>
            <th style="width: 18px;">Order No</th>
            <th style="width: 18px;">Invoice No</th>
            <th style="width: 14px;">Area/Beats</th>
            <th style="width: 16px;">Container Type</th>
        </tr>
        </thead>
        <?php
            $totCartons = 0;
            $totCrates = 0; 
            $totBags = 0;
            $srno = 1;
            $containerCount = $Reportinfo[count($Reportinfo)-1];
            for($i = 0; $i < count($Reportinfo) - 1; $i++){
        ?>
        <tr>
            <td>{{ $srno}}</td> 
            <td>{{ $Reportinfo[$i]['crates_id'] }}</td>
            <td>{{ $Reportinfo[$i]['st_docket_no'] }}</td>
            <td>{{ $Reportinfo[$i]['order_code'] }}</td>
            <td>{{ $Reportinfo[$i]['invoice_code'] }}</td>
            <td>{{ $Reportinfo[$i]['beat_area'] }}</td>
            <td>{{ $Reportinfo[$i]['container_value'] }}</td>
        </tr>
        <?php
                if($Reportinfo[$i]['container_type'] == 16004){ //CFC
                    $totCartons = $totCartons + 1;
                } else if($Reportinfo[$i]['container_type'] == 16007){ //Crate
                    $totCrates = $totCrates + 1;
                } else if($Reportinfo[$i]['container_type'] == 16006){ //Bag
                    $totBags = $totBags + 1;
                }
                $srno++;
            }
        ?>
        <tr>   
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>   
            <th>CFC Count</th>
            <th>Crate Count</th>
            <th>Bag Count</th>
            <th>Total Count</th>
        </tr>
        <tr>
            <td><?php echo $containerCount["cfc_cnt"]; ?></td>
            <td><?php echo $totCrates; ?></td>
            <td><?php echo $containerCount["bags_cnt"]; ?></td>
            <td><?php echo $containerCount["cfc_cnt"] + $containerCount["bags_cnt"] + $totCrates; ?></td>
        </tr>
    </table>
</html>