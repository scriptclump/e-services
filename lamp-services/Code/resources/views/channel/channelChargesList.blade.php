
<table class="table table-striped table-hover table-borderless" id="channel_charges_table">
    <thead bgcolor="#f2f2f2">
        <tr>
            <th>{{trans('cp_headings.cp_servicetype')}} </th>
            <th>{{trans('cp_headings.cp_chargetype')}} </th>
            <th> {{trans('cp_headings.cp_charges')}} </th>
            <th>  {{trans('cp_headings.currency')}}  </th>
            <th>  {{trans('cp_headings.cp_isrecur')}}</th>
            <th>{{trans('cp_headings.cp_rec_interval')}}</th>
            <th> {{trans('cp_headings.start_date')}} </th>
            <th>{{trans('cp_headings.end_date')}} </th>
            <th> {{trans('cp_headings.status')}} </th>
            <th> {{trans('cp_headings.action')}} </th>
        </tr>
    </thead>            
    <tbody id=''>
        <tr>
            <td><input type="hidden" name="charge_idd" id="charge_idd" value="0">
                <select id="service_type_id"  name="service_type_id" class="form-control  chr_ctrl">
                    <option value="">Select...</option>
                    @foreach($data['serviceTypes'] as $serviceType)
                    <option value="{{$serviceType->service_type_id}}">{{$serviceType->service_name}}</option>
                    @endforeach          
                </select>
            </td>
            <td>
                <select id="charge_type" name="charge_type" class="form-control chr_ctrl">
                    @foreach($data['chargeType'] as $chargeTypes)
                    <option  value="{{$chargeTypes->value}}">{{$chargeTypes->master_lookup_name}}</option>
                    @endforeach
                </select>
            </td>
            <td><input id="charges" name="charges" type="text" class="form-control chr_ctrl"></td>
            <td>
                <select id = "currency_id" name= "currency_id" class="form-control chr_ctrl">
                    <option value="">Select Currency</option>
                    @foreach($data['CurrencyTypes'] as $CurrencyType)
                    <option  value="{{$CurrencyType->currency_id}}">{{$CurrencyType->code}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select id="is_recurring" name="is_recurring" class="form-control">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </td>
            <td>
                <select id="recurring_interval" name="recurring_interval" class="form-control chr_ctrl">
                    <option value="">Select...</option>
                    @foreach($data['paymentsTypes'] as $paymentsType)
                    <option value="{{$paymentsType->value}}">{{$paymentsType->master_lookup_name}}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <div class="input-group date" data-provide="datepicker">
                    <input type="text" id="start_date" name="start_date" readonly class="form-control datepicker chr_ctrl" data-date-format="dd-mm-yyyy">
                    <div class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </div>
                </div>
            </td>
            <td>  
                <div class="input-group date" data-provide="datepicker">
                    <input type="text" id="end_date" name="end_date" readonly class="form-control datepicker chr_ctrl" data-date-format="dd-mm-yyyy">
                    <div class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </div>
                </div>
            </td>

            <td>
                <select id="is_active" name="is_active" class="form-control chr_ctrl">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </td>
            <td><button type="button" id="add_charge_button" class="btn green" disabled="disabled"> Add</button>


            </td>                    
        </tr>
        @foreach($data['charges'] as $charges)
        <tr align="center">
            <td align="left"> {{$charges->service_type_id}}</td>
            <td> {{$charges->charge_type}}</td>
            <td> {{$charges->charges}} </td>
            <td> {{$charges->currency_id}}</td>
            <td> {{$charges->is_recurring}}</td>
            <td> {{$charges->recurring_interval}} </td>
            <td>{{date('d-m-Y',strtotime($charges->charges_from_date))}}</td>
            <td> {{date('d-m-Y',strtotime($charges->charges_to_date))}} </td>
            <td>
                <?php
                if ($charges->is_active == 1) {
                    echo ' <i class="glyphicon glyphicon-ok font-green"></i>';
                } else {
                    echo ' <i class="ui-icon ui-icon-close"></i>';
                }
                ?>
            </td>
            <td><a class="edit edit_charges" data-channel-idd="{{$charges->mp_charges_id}}" href="javascript:;"><i class="fa fa-pencil"></i></a> <a class="delete delete_charges"  data-channel-idd="{{$charges->mp_charges_id}}" href="javascript:;"><i class="fa fa-trash-o"></i> </a></td>
        </tr>
        @endforeach
    </tbody>
</table>
<script>
    $('#channel_charges_table').dataTable({
        "columnDefs": [
            {"orderable": false, "targets": 0}
        ],
        "pageLength": 10
    });
</script>