@extends('layouts.default')
@extends('layouts.header')
@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light tasks-widget">
<div class="portlet-body">
    <table width="100%" class="table table-border table-hover">
        <tr>
            <?php
            for ($i = 0; $i < sizeof($hsn_headers); $i++) {
                ?>
                <th><?php echo $hsn_headers[$i]; ?></th>
                <?php
            }
            ?>

        </tr>
        <?php
        if (!empty($hsn_code_details)) {
            foreach ($hsn_code_details as $key => $value) {
                echo "<tr>
                        <td width='10%'>{$value['ITC_HSCodes']}</td>
                        <td width='80%'>{$value['HSC_Desc']}</td>
                        <td width='10%'>{$value['tax_percent']}</td>
                      </tr>";
            }
        }
        ?>
    </table>
</div>
</div>
</div>
</div>

@stop

@section('style')
<style type="text/css">
table{ border: 1px solid; border-collapse: collapse;;}
table tr td{ border: 1px solid #e7ecf1;}
table tr th { border: 1px solid #e7ecf1; }
.page-sidebar-closed .page-content-wrapper .page-content {
    margin-left: 0px !important;
}
</style>
@stop