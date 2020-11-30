<div class="tab-pane" id="tab_44">
    <div class="row">
        <div class="col-md-12 text-right">
            <a class="btn green-meadow" data-toggle="modal" href="#upload_pim">Upload Product Template</a>
            <a class="btn green-meadow" data-toggle="modal" href="#addp">Add Product</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12"> &nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="scroller" style="height: 400px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2" id="list2">
                <div class="table-responsive">
                    <table id="manufacturer_product_grid"></table>
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
                    <form id="download_temp_form" action="/manufacturer/downloadTOTExcel">
                        <div class="row">
                            <div class="col-md-12 " align="center">
                                <select name="template_type" id="download_template_type" class="form-control">
                                    <option value="/manufacturer/importTOTExcel" id="TOT">TOT Template</option>
                                    <option value="/manufacturer/importPIMExcel" id="PIM">PIM Template</option>
                                    <option value="/manufacturer/importTOTPIMExcel" id="TOT and PIM">TOT & PIM Template</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12 " align="center">
                                <select name="category_id" id="category_id" class="form-control">
                                    
                                </select>
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
                                <p class="topmarg">Lorem ipsum dolor sit amet, consectetur adipisicing elit <br>send do eiusmod tempor incidiunt ut laboret dolor manal</p>
                            </div>
                        </div>
                    </form>
                    <br>
                    <div class="row">
                        <div class="col-md-12 text-center" align="center">
                            <form id='import_template_form' action="{{ URL::to('/manufacturer/importTOTExcel') }}" class="text-center" method="post" enctype="multipart/form-data">
                                <div class="fileUpload btn green-meadow"> <span id="up_text">Upload Your TOT Template</span>
                                    <input type="file" class="form-control upload" name="import_file" id="upload_pim_file"/>
                                </div>
                                <span class="loader" id="pimloader" style="display:none;"><img src="/img/ajax-loader.gif" style="width:25px"/></span>
                            <br/><br/>
                                <p class="topmarg">Lorem ipsum dolor sit amet, consectetur adipisicing elit <br>send do eiusmod tempor incidiunt ut laboret dolor manal</p>

                            </form>
                                                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


