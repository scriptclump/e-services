<html>
    {{--
        --Filename : inboundProductList.blade.php
        --Author : Vijaya Bhaskar Chenna
        --CreateData : 20-May-2016
        --Desc : View page of inbound product listing
        --}}
    <head>
        {{HTML::style('assets/css/jquery.datetimepicker.css')}}
    </head>
    <body>
        <h1>All products</h1>
        <form action="{{ url('inbound/create') }}" method="POST">
            <div>
                <label>Pickup Location</label>
                <select name="pickup_location">
                    <option value="{{ $pickup_location }}">{{ $pickup_location }}</option>
                </select>
            </div>
            <div>
                <label>Delivery Location</label>
                <select name="delivery_location">
                @foreach ($delivery_location as $delivery_location_each)
                <option value="{{ $delivery_location_each['wh_location_id'] }}">{{ $delivery_location_each['address1'] }} , {{ $delivery_location_each['address2'] }}, {{ $delivery_location_each['city'] }}</option>
                @endforeach
                </select>
            </div>
            <div>
                <label>Slots</label>
                <input type="text" name="time_slots" value="" id="datetimepicker"/>
            </div>
            <div>
                <label>STN Number</label>
                <input type="text" name="stn_number" value="STN1234"/>
            </div>
            <div>
                <label>Upload STN document</label>
                <input type="file" name="stn_document" value=""/>
            </div>
            <br /><br />
            <table>
                <thead>
                    <tr>
                        <td>Product Details</td>
                        <td>Avl Qty</td>
                        <td>Qty</td>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    @foreach ($product_list as $product_each)                 
                    <tr>
                        <td>
                            <img src="{{ $product_each['image'] }}" />
                            {{ $product_each['name'] }}<br />
                            {{ $product_each['sku'] }}<br />
                            {{ $product_each['mrp'] }}
                            <input type="hidden" name="request_type" value="inward" />
                            <input type="hidden" name="request_status" value="open" />
                            <input type="hidden" name="product_details[{{$i}}][]" value="{{ $product_each['product_id'] }}"/>
                            <input type="hidden" name="product_details[{{$i}}][]" value="{{ $product_each['sku'] }}" />
                        </td>
                        <td>{{ $product_each['available_inventory'] }}</td>
                        <td><input type="text" name="product_details[{{$i}}][]" value="" /></td>
                    </tr>
                    <?php $i++; ?>
                    @endforeach
                    <tr>
                        <td>
                            <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                            <input type="submit" name="inward" value="Create Inward"/>
                        </td>
                    </tr>
                </tbody>
            </table>
            @if(isset($req_response))
            @if ($req_response)
            Request was successful
            @else
            Request was unsuccessful
            @endif
            @endif
        </form>
    </body>
    {{HTML::script('assets/js/jquery.js')}}
    {{HTML::script('assets/js/jquery.datetimepicker.full.min.js')}}
    <script>
        jQuery('#datetimepicker').datetimepicker();
    </script>
</html>