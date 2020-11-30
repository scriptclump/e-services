<html>
    <table>
        <tr>   
            <th>Date:</th>
            <td><?php echo date('d/m/Y H:i:s'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <th>Hub Name:</th>
            <td><?php echo isset($HubtoDc[0]['hub_name']) ? $HubtoDc[0]['hub_name'] : '';?></td>
        </tr>
        <tr>
            <th>Vehicle No:</th>
            <td><?php echo isset($HubtoDc[0]['rt_vehicle_no']) ? $HubtoDc[0]['rt_vehicle_no'] :''; ?></td>
            <td></td>
            <td></td>
            <td></td>
            <th>Driver Name:</th>
            <td><?php echo isset($HubtoDc[0]['rt_driver_name']) ? $HubtoDc[0]['rt_driver_name'] : ''; if(isset($HubtoDc[0]['rt_driver_mobile']) && !empty($HubtoDc[0]['rt_driver_mobile'])) { echo '(M:'.$HubtoDc[0]['rt_driver_mobile'].')'; }  ?></td>
        </tr>
        <tr>
            <th>Hub Address:</th>
            <td colspan="3" style="word-wrap: break-word; vertical-align: middle; height: 30px"><?php echo isset($HubtoDc[0]) ? $HubtoDc[0]['hub_name'].', '.$HubtoDc[0]['hub_address1'].',<br /> '.$HubtoDc[0]['hub_address2'].', '.$HubtoDc[0]['hub_city'].', '.$HubtoDc[0]['hub_pincode'] : ''; ?></td>
            <td></td>
            <th>Dc Address:</th>
            <td colspan="3" style="word-wrap: break-word; vertical-align: middle; height: 30px"><?php echo isset($HubtoDc[0]) ? $HubtoDc[0]['wh_name'].', '.$HubtoDc[0]['dc_address1'].',<br /> '.$HubtoDc[0]['dc_address2'].', '.$HubtoDc[0]['dc_city'].', '.$HubtoDc[0]['dc_pincode'] : ''; ?></td>
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
        <tr>   
            <th style="width: 14px;">Sr. No.</th>
            <th style="width: 14px;">Container Id</th>
            <th style="width: 18px;">Docket No</th>
            <th style="width: 18px;">Order No</th>
            <th style="width: 20px;">Order Status</th>
            <th style="width: 18px;">Invoice No</th>
            <th style="width: 12px;">DC Name</th>
            <th style="width: 12px;">Area/Beats</th>
            <th style="width: 16px;">Container Type</th>
        </tr>
        <?php
            $totCartons = 0;
            $totCrates = 0; 
            $totBags = 0;
            $srno = 1;
            $containerCount = $HubtoDc[count($HubtoDc)-1];
            for($i=0; $i < count($HubtoDc) - 1; $i++) {
        ?>
        <tr>
            <td><?php echo  $srno; ?></td>
            <td><?php echo  $HubtoDc[$i]['crates_id'] ?></td>
            <td><?php echo  $HubtoDc[$i]['rt_docket_no'] ?></td>
            <td><?php echo  $HubtoDc[$i]['order_code'] ?></td>
            <td><?php echo  $HubtoDc[$i]['order_status'] ?></td>
            <td><?php echo  $HubtoDc[$i]['invoice_code'] ?></td>
            <td><?php echo  $HubtoDc[$i]['wh_name'] ?></td>
            <td><?php echo  $HubtoDc[$i]['beat_area'] ?></td>
            <td><?php echo  $HubtoDc[$i]['container_value'] ?></td>
        </tr>
        <?php
                if($HubtoDc[$i]['container_type'] == 16004){ //CFC
                    $totCartons = $totCartons + 1;
                } else if($HubtoDc[$i]['container_type'] == 16007){ //Crate
                    $totCrates = $totCrates + 1;
                } else if($HubtoDc[$i]['container_type'] == 16006){ //Bag
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