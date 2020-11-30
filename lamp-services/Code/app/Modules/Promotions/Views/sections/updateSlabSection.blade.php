<div class="condition_container" style="display:none;">
    <div class="row">
        <div class="col-md-12">
            <h5><strong>Offer Condition</strong></h5>
            <span id="no_slab_span" style ="color:red"></span>
            <hr />
        </div>
    </div> 

    <!-- Offer Condition Part Goes here -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
            <label for="Select offer tmpl">Condition</label>
                <select id = "condition"  name =  "condition" class="form-control" onChange="changetextbox(this);">
                    <option value = "">--Please Select--</option>
                    <option value = "=" @if($getpromotionData->prmt_condition == '=') {{'selected'}}@endif > = </option>
                    <option value = ">" @if($getpromotionData->prmt_condition == '>') {{'selected'}}@endif > > </option>
                    <option value = "Range" @if($getpromotionData->prmt_condition == 'Range') {{'selected'}}@endif > Range </option> 
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
                <input type="hidden" id="product_id_update" name="product_id_update"/>
                <div class="col-md-4">
                    <div class="form-group">
                    <label for="promotion_name">Pack</label>
                        <select id = "pack_number_update"  name =  "pack_number_update" class="form-control" onChange="stepValue();">
                            <option value = "">--Please Select--</option>
                            @foreach($packdata as $packDataInner){
<option  star_code='{{$packDataInner->star}}'  pack_level='{{$packDataInner->level}}' esu='{{$packDataInner->esu}}' prd_star_color='{{$packDataInner->StarColor}}' value = "{{$packDataInner->no_of_eaches}}">{{$packDataInner->DPValue}}</option>         
            
                            }@endforeach
                        </select>
                    </div> 
                     <span class="fa fa-star" id="product_star_color" aria-hidden="true" style="position: absolute;top: 31px;font-size: 25px;right: -11px;"></span>                
                </div>         
              <div class="Pack">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="promotion_name">ESU</label>
                            <input type="hidden" name="step_count_update" id="step_count_update"/>
                            <input type="Number" step="" min="0" class="form-control" name="pack_value_update" placeholder="0" id="pack_value_update" onChange="maxQty();"placeholder="Only Number Allowed"/>
                        </div>
                    </div>
                </div>
                <div class="second_condition value_from">
                    <div class="col-md-2">
                    <div class="error_messege" style="color:red"></div>
                        <div class="form-group">
                            <label for="promotion_name">Max Qty</label>
                            <input type="text" class="form-control" name="value_two" id="value_two" placeholder="Only Number Allowed" readonly />
                        </div>
                    </div>
                </div>

                <div class="offer_value">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="promotion_name">Off Val / Unit price</label>
                            <input type="text" class="form-control" name="offer_value" id="offer_value" placeholder="Only Number Allowed"/>
                        </div>
                    </div>
                </div>

                <div class="col-md-1 text-left plus-icon" style="margin-left:-13px; display:none;">
                    <div class="form-group addbut">
                        <span class="btn btn-icon-only green addslab" id="slabrate_validate" name ="slabrate_validate"><i class="fa fa-plus"></i></span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row slab_rates">
        <div class="col-md-6">&nbsp;</div>

        <div class="col-md-6">
            <div class="scroller" style="height: 150px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                <div class="cust-error-no-tr-lines" style="color:red"></div>

                <table class="table table-striped table-bordered table-hover table-advance" id="slab_table" name = "slab_table[]">

                <thead>
                    <tr>
                        <th>Pack</th>
                        <th>Star</th>
                        <th>ESU</th>
                        <th>Maximum Quantity</th>
                        <th>Offer Value</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @if(isset($getSlabData))
                 @foreach( $getSlabData as $slabDetails)

                    <tr class="gradeXSlab odd list-head">
                        <td data-val="cond_to"><input type="hidden" value="{{$slabDetails->pack_type}}" id="pack_number_update" name="pack_number_update[]" class="form-control" ><span>{{$slabDetails->packtype}}</span></td>
                        <td data-val="cond_to"><span class="fa fa-star"  style="color:{{$slabDetails->PrdStar}}"></span><input type="hidden" value="{{$slabDetails->product_star_slab}}" id="product_star_color_table" name="product_star_color_table[]" class="form-control" ></td>
                        <td data-val="cond_to"><span>{{$slabDetails->esu}}</span><input type="hidden" value="{{$slabDetails->esu}}" id="pack_value_update" name="pack_value_update[]" class="form-control"></td>
                        <td data-val="cond_to"><span>{{$slabDetails->end_range}}</span><input type="hidden" value="{{$slabDetails->end_range}}" id="value_two" name="value_two[]" class="form-control" ></td>
                        <span id = "error_mess"></span>
                        <td data-val="offer_value"><input type="text" value="{{$slabDetails->price}}" id="offer_value" name="offer_value[]" class="form-control" ></td>
                        <td><a href="" class="btn btn-icon-only default delcondition"><i class="fa fa-trash-o"></i></a></td>
                    </tr>
                 @endforeach
                 @endif                    
                </tbody>
                </table>
            </div>                        
        </div>

    </div>
</div>