<?php
/*
 * Created by: RAJU A
 * Date: 29-02-16
 */
//print_r($channel_ship_charges);
?>
<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th align="center">SHIPMENT TYPE</th>
            <th align="center">WEIGHT SLAB</th>
            <th class="">CURRENCY<br></th>
            <th class="">CHARGE TYPE<br></th>
            <th class="cell-color">LOCAL<br></th>
            <th class="cell-color">REGIONAL<br></th>
            <th class="cell-color">METRO TO METRO<br></th>
            <th class="cell-color">NORTH EAST<br></th>
            <th class="cell-color">JAMMU & KASHMIR<br></th>
            <th class="cell-color">REST OF INDIA<br></th>
            <th class="cell-color">ACTION<br></th>
        </tr>
    </thead>
    <tbody>
         @foreach($channel_ship_charges as $ship_charges)
         <?php 
         $uom = ($ship_charges->uom==0) ? 'gm' : 'kg'; 
         ?>
        <tr>
            <td>{{ ($ship_charges->shipment_type==0)?'Heavy':'Non-heavy'}}</td>
            <td align="center">{{ ($ship_charges->additional_weight_flag==0)?'Up to '.$ship_charges->end_weight.$uom:'Every Additional '.$uom }}</td>
            <td align="center">{{ $ship_charges->currency_code }}</td>
            <td align="center">{{ $ship_charges->charge_type }}</td>
            <td class="cell-color">{{($ship_charges->intracity!='')?$ship_charges->intracity:'NA'}}</td>
            <td class="cell-color">{{($ship_charges->regional!='')?$ship_charges->regional:'NA'}}</td>
            <td class="cell-color">{{($ship_charges->metro_to_metro!='')?$ship_charges->metro_to_metro:'NA'}}</td>
            <td class="cell-color">{{($ship_charges->north_east!='')?$ship_charges->north_east:'NA'}}</td>
            <td class="cell-color">{{($ship_charges->j_k!='')?$ship_charges->j_k:'NA'}}</td>
            <td class="cell-color">{{($ship_charges->rest_of_india!='')?$ship_charges->rest_of_india:'NA'}}</td>
            <td class="cell-color jqx-grid-cell1">
                <a title="{{ $ship_charges->shipping_charges_id }}" class="edit_shippingch" data-shipping_charge_id="{{ $ship_charges->shipping_charges_id }}"  style="margin-bottom:10px;" role="button" href="#"><i class="fa fa-pencil"></i></a>
                <a title="{{ $ship_charges->shipping_charges_id }}" class="delete_shippingch" data-shipping_charge_id="{{ $ship_charges->shipping_charges_id }}"  style="margin-bottom:10px;" role="button" href="#"><i class="fa fa-trash-o"></i></a>
            </td>
        </tr>
         @endforeach 
    </tbody>
</table>
<p>�Volumetric Weight (kg)=L*B*H (Lenght x breadth x Height)/6000 where LBH are in cm�</p>