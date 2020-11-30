<html>
    {{--
        --Filename : createInward.blade.php
        --Author : Vijaya Bhaskar Chenna
        --CreateData : 20-May-2016
        --Desc : View page of inbound request
        --}}
    <head>
        {{HTML::style('assets/css/jquery.datetimepicker.css')}}
    </head>
    <body>
        <form action="{{ url('inbound/create') }}" method="POST">
            <div>
                <label for="prod_name">Product Name</label>
                <input type="hidden" name="clnt_id" id="clnt_id" value="123456">
                <input type="hidden" name="seller_id" id="seller_id" value="7891011">
                <input type="hidden" name="req_typ" id="req_typ" value="inward">
                <input type="hidden" name="req_status" id="req_status" value="open">
                <input type="text" name="prod_name" id="prod_name">
            </div>
            <div>
                <label for="pro_quant">Product Quantity</label>
                <input type="text" name="pquant" id="pquant">
            </div>
            <div>
                <label for="sel_sku">Seller SKU</label>
                <input type="text" name="seller_sku" id="sel_sku">
            </div>
            <div>
                <label for="sell_price">Selling Price</label>
                <input type="text" name="sell_price" id="sell_price">
            </div>
            <div>
                <label for="mrp_price">MRP</label>
                <input type="text" name="mrp_price" id="mrp_price">
            </div>
            <div>
                <label for="datetimepicker">Consignment Slot</label>
                <input type="text" name="cons_slot" id="datetimepicker">
            </div>
            <!-- <div>
                <label for="browse">Browse</label>
                <input type="file" name="file_name" id="browse">
            </div> -->
            <div>
                <button type="submit">Create Inward</button>
            </div>
        </form>
        @if(isset($req_response))
        @if ($req_response)
        Request was successful
        @else
        Request was unsuccessful
        @endif
        @endif
    </body>
    {{HTML::script('assets/js/jquery.js')}}
    {{HTML::script('assets/js/jquery.datetimepicker.full.min.js')}}
    <script>
        jQuery('#datetimepicker').datetimepicker();
    </script>
</html>