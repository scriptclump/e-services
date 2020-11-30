<html>
    <?php if (!isset($toexcel)) { ?>
        <head>
            <style>
                table {
                    font-family: arial, sans-serif;
                    border-collapse: collapse;
                    width: 100%;
                }
                <?php
                    if(isset($email_border) && $email_border==1) 
                    echo "
                    td, th {
                        border: 1px solid #dddddd;
                        text-align: left;
                        padding: 8px;
                    }";
                ?>
                tr:nth-child(even) {
                    background-color: #dddddd;
                }
                caption
                {
                    font-style: initial;
                    font-weight: bold;
                    font-size: 15px;
                    text-align: left;
                    padding: 10px;
                }
            </style>
        </head>
    <?php } ?>
    <body>
        <?php if (!isset($toexcel)) { ?>
            {{$name}},
            <br/>
        <?php } ?>
        @foreach($TableCaptions as $captionValues)
        @if($count<$size && !empty($tableData[$count]))
        <table>
            <?php if (!isset($toexcel)) { ?>
                <caption><strong>{{$captionValues}}</strong></caption>
            <?php } else { ?>
                <tr>
                    <th><strong>{{$captionValues}}</strong></th>
                </tr>            
            <?php } ?>
            <tr>
                @foreach($colunmNames[$count] as $ColumnValue )
                <th>{{$ColumnValue}}</th>
                @endforeach   
            </tr>
            @foreach($tableData[$count] as $tableValue)
            <tr>
                @foreach($colunmNames[$count] as $ColumnValue )
                <td>{{$tableValue[$ColumnValue]}}</td>
                @endforeach 
            </tr>
            @endforeach					  					
        </table>
        <br/><br/><br/>
        @endif
        <br/><br/>
        <?php $count++; ?>
        @endforeach
        <p>                
        <div>Thanks,</div>
        <div>Ebutor Tech Support</div>
    </p>   
</body>
</html>