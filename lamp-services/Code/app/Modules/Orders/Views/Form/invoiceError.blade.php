<div class="modal modal-scroll fade in" id="invoiceError" tabindex="-1" role="dialog" aria-labelledby="basicvalCode" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                <h4 class="modal-title" id="basicvalCode">Inventory Error : <span style="color: blue;font-weight: 600" id="order_code_inv"></span></h4>
            </div>
            <div class="modal-body">
              <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                  <thead>
                    <tr>
                      <th> Product Name </th>
                      <th> Invoice Qty </th>
                      <th> Status </th>
                    </tr>
                  </thead>
                  <tbody id="inv_table_body">

                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <div class="row">
                  <div class="col-md-12 text-center">
                      <button class="btn" id="Okay" data-dismiss="modal">Okay</button>
                  </div>
              </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>