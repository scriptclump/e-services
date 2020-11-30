<table class="table table-striped table-hover table-borderless" id="statustable">
    <thead bgcolor="#f2f2f2">
        <tr>
            <th> {{trans('cp_headings.cp_grid_type')}}</th>
            <th> {{trans('cp_headings.cp_status')}} </th>
            <th> {{trans('cp_headings.eb_status')}} </th>

            <th>  {{trans('cp_headings.status')}} </th>


            <th> {{trans('cp_headings.action')}}  </th>
            <th></th><th></th>

        </tr>
    </thead>
    <tbody>  
        <tr>
            <td>
                <input type="hidden" id="update_status_id" name="update_status_id"/>
                <select class="form-control form-filter input-sm chr_ctrl" name="status_type" id='status_type'>
                    <option value="">Select...</option>
                    <option value="Order">Order</option>
                    <option value="Product">Product</option>
                </select>
            </td>

            <td><input type="text" value="" class="form-control input-small chr_ctrl" name="channel_status" id='channel_status'></td>
            <td>

                <select class="form-control form-filter input-sm chr_ctrl" name="ebutor_status" id='ebutor_status'>
                    <option value= "" >Select...</option>
                    @if($data['ebutor_order_stat'])
                    @foreach($data['ebutor_order_stat'] as $key=>$value)

                    <option value= "{{$value->master_lookup_name}}" name="ebutor_val">{{$value->master_lookup_name}}</option>
                    @endforeach
                    @endif
                </select>

            </td>
            <td><select class="form-control form-filter input-sm chr_ctrl" name="active_status" id='active_status'>
                    <option value="">Select...</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select></td>
            <td></td>
            <td></td>

            <td><button id="status_save" class="btn green" name="status_save" disabled="disabled"> Add</button></td>
        </tr>
        @if($data['mapping_data_status'])
        @foreach($data['mapping_data_status'] as $k=>$val)
        <tr align="center">
    <input type='hidden' value='{{$val['mapping_id']}}' id='mapping_id' name='mapping_id'>
    <td align="left">{{$val['status_type']}}</td>
    <td>{{$val['mp_status_name']}}</td>
    <td>{{$val['eb_status_name']}} </td>    
    <td>
        @if($val['active_status']==1)<i class="glyphicon glyphicon-ok font-green"></i>
        @elseif($val['active_status']==0)<i class="glyphicon glyphicon-remove"></i>@endif
    </td>
    <td><a class="edit_stat" href="javascript:;" data-map-id='{{$val['mapping_id']}}'><i class="fa fa-pencil"></i></a> 
        <a class="delete_stat" href="javascript:;" data-mapp-id='{{$val['mapping_id']}}'><i class="fa fa-trash-o"></i> </a></td>
    <td>  </td>
    <td>  </td>
</tr>
@endforeach
@endif






</tbody>
</table>