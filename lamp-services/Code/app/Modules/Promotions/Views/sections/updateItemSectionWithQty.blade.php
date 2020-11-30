<div id="gridSection">
    <div class="row">
        <div class="col-md-12">
            <strong>Assign Item to the Promotion</strong> <!-- <button type="button" onclick="getItemGrid();" class="btn green-meadow">Get <span id="item_text">Item</span></button> -->
            <hr/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <table class="table table-striped table-hover" id="product_grid"></table>
        </div>

        <div class="col-md-1" style="position:relative; top:200px;">
            <a href="#" class="btn btn-icon-only green moveLeft"><i class="fa fa-angle-double-right"></i></a>
        </div>

        <div class="col-md-6">
            <div class="scroller" style="height: 500px; width:650px !important;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list2">  
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-bordered table-hover table-advance" id="add_product_table" name = "add_product_table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>QTY</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($getProductAndCategory))
                        @foreach($getProductAndCategory as $list )
                        <tr class="gradeX odd">
                              <td data-val="list_details" class="prom-font-size">{!! $list->ItemName !!}</td>
                              <td><input type = "text" id="product_qty" name = "product_qty[]" value = "{{$list->product_qty}}"></td>
                              <td style = "display:none;"><input type="hidden" data_item_id="item_id" class="form-control input-sm" value="{{$list->ItemID}}" id="item_id" name = "item_id[]"></td>
                              <td><a href="" class="btn btn-icon-only default delList"><i class="fa fa-trash-o"></i></a></td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>                    
        </div>
    </div>
</div>