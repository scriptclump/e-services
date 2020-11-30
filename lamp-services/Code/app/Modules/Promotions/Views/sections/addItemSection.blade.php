<div class="item_container">
    <div class="row">
        <div class="col-md-12">
            
            <h5><strong>Assign Item to the Promotion</strong> <button type="button" onclick="getItemGrid();" class="btn green-meadow">Get <span id="item_text">Item</span></button> <span id="no_product_span" style ="color:red"></span> </h5>
            <hr />
        </div>
    </div>

    <!-- Promotion Assign Grid Part -->
    <div class="row item-inner-container">
        <div class="col-md-5">

                <table class="table table-striped table-hover" id="product_grid"></table>
        </div>
        <div class="col-md-1" style="position:relative; top:200px;">
            <a href="#" class="btn btn-icon-only green moveLeft"><i class="fa fa-angle-double-right"></i></a>
        </div>
        <div class="col-md-6">
            <div class="scroller" style="height: 500px; width:650px !important;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list2"> 
                <div class="cust-error-no-product"></div>
 
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-bordered table-hover table-advance" id="add_product_table" name = "add_product_table">
                <thead>
                    
                </thead>
                <tbody>
                    <tr class="odd gradeX list-head">
                    </tr>
                </tbody>
                </table>
            </div>                    
        </div>
    </div>
</div>