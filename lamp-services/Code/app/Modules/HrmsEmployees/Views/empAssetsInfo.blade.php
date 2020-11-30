<div class="tab-pane" id="tab_4-7">
    <div class="basicInfoOverlay"></div>
    <table class="table table-striped table-bordered table-advance table-hover" id="" style="margin-top: 25px;">
        <thead>
            <tr>
                <th>Asset Name</th>
                <th>Asset Code</th>
                <th>Serial Number</th>
                <th>Manufacturer</th>
                <th>Asset Allocated Date</th>

            </tr>
        </thead>                    
        <tbody>
        
        @foreach($employeeAssets as $assets)                     
        <tr>
            <td>{{$assets['product_title']}}</td>
            <td>{{$assets['company_asset_code']}}</td>
            <td>{{$assets['serial_number']}}</td>
            <td>{{$assets['manufacturername']}}</td>
            <td>{{$assets['asset_allocated_date']}}</td>
        </tr>
        @endforeach
        </tbody>   
    </table>  
    <div id="exp_table_msg"><?php if(!count($employeeAssets)){?> <p>No Records Found.</p> <?php } ?></div>
   
</div>

