<div class="tab-pane" id="tab_44">
    <div class="row">
        <div class="col-md-12 text-right" id="addProduct">            
            <?php if(1){?>
            <a class="btn green-meadow" data-toggle="modal" href="#upload_pim">Upload Product Template </a>
            <?php } ?>
            <!--<a class="btn green-meadow" data-toggle="modal" href="#upload_qty">Upload DC MAPPING</a>-->
            <?php if(1) { ?>
            <a class="btn green-meadow addProduct" data-toggle="modal" href="#addp" data-type="add" id="addTot">Add Product</a>
            <?php }?>
            
        </div>
    </div>
    <div class="row">
        <div class="col-md-12"> &nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="scroller" style="height: 400px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#000" id="list2">
                <div class="table-responsive">
                    <table id="supplier_tot_grid"></table>
                </div>
            </div>
        </div>
    </div>    
    <div id="upload_pim" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">UPLOAD PRODUCTS</h4>
                </div>
                <div class="modal-body">
                    <br>
                    <form id="download_temp_form" action="/suppliers/downloadTOTExcel">
                        <div class="row">
                            <div class="col-md-12 ">
								<select name="manufacturer_id" id="manufacturer_name2" class="form-control select2me manuProductSelect">
								</select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12 ">
                            <select class="form-control select2me" name="category" id="category">
                        </select>
                            </div>
                        </div>
						<br>
						<div class="row">
                            <div class="col-md-12 ">
                                <select name="warehouse_id" id="warehouse_tot" class="form-control">
                                <option value="">Select Warehouse</option>
                                                                @if(isset($legalentity_warehouses))                    
								@foreach($legalentity_warehouses as $Val )
									<option value="{{$Val['le_wh_id']}}">{{$Val['lp_wh_name']}}</option>
								@endforeach
                                                                @endif
								</select>
                               <!--  <select name="category_id" id="category_id" class="form-control select2me">
								<option value="">Please Select...</option>
								@include('Supplier::categoryList')
                                </select> -->
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12 " align="pull-left">
                                <input type="checkbox" name="with_data"/> with data
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12 " align="center"> 
                                <button type="submit" id="download_template_button" role="button" class="btn green-meadow">Download TOT Template</button>
                                <br/>
                                <p class="topmarg">Select template type and category.<br /> Check With data to update product information</p>
                            </div>
                        </div>
                    </form>
                    <br>
                    <div class="row">
                        <div class="col-md-12 text-center" align="center">
                            <form id='import_template_form' action="{{ URL::to('/suppliers/importTOTExcel') }}" class="text-center" method="post" enctype="multipart/form-data">
                                <div class="fileUpload btn green-meadow"> <span id="up_text">Upload Your TOT Template</span>
                                    <input type="file" class="form-control upload" name="import_file" id="upload_pim_file"/>
                                </div>
                                <span class="loader" id="dcloader" style="display:none;"><img src="/img/ajax-loader.gif" style="width:25px"/></span>
                            <br/>
                                <p class="topmarg">Upload the filled product template</p>

                            </form>
                                                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<a class="btn green-meadow" data-toggle="modal" style="display:none" href="#import_messages">Show errors</a>


    <div id="import_messages" class="modal modal-scroll fade" tabindex="-2" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
					<table class='product_success_msg'>
					</table>
                </div>
            </div>
        </div>
    </div>
