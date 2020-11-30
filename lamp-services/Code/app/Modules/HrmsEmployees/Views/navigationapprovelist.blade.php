<div class="tabbable-line">
    <ul class="nav nav-tabs nav-tabs-lg">
           
        <li class="active"><a href="#tab11" class="potabs" data-type="po" data-id="" data-toggle="tab" >Employee Pending List</a></li>
        
        <li class=""><a href="#tab33" class="potabs" data-type="po" data-id="" data-toggle="tab" onclick="ApprovedHistory(<?php echo Session::get("userId")?>);" aria-expanded="true">Approved History
                <span class="badge badge-success" id="totalPayments"></span></a>
        </li>
        
    </ul>
    <div class="tab-content">
        @include('HrmsEmployees::empleavelisttomanager')

        @include('HrmsEmployees::empleavehistorytomanager')       
    </div>
</div>