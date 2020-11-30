<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <div>
            Hi,
            <br />
            <br />

            <!-- 
            Email flags
            0 -> Add
            1 -> Edit
            2 -> Upload
            3 -> Delete
             -->

            <!-- Top Part -->
            <b>{{$topMsg}}</b>
            <br />
            <!-- Price Details Part -->
            @if($editFlag==0)
                <br /><br/>
                <b>Added By : </b> {{$changedby}}
            @endif
            @if($editFlag==1)
                <br /> <br />
                <b>Changed By : </b> {{$changedby}}
            @endif
            @if($editFlag==3)
                <br /> <br />
                <b>Deleted By : </b> {{$changedby}}
            @endif

            @if($editFlag==2)
                <br /> <br />
                <b>Uploaded By : </b> {{$changedby}}
            @endif

            <br /><br />

            <table border="1" cellspacing="0" cellpadding="5">
                <tr bgcolor="#efefef">
                    <th>ProductID</th>
                    <th>SKU</th>
                    <th>Warehouse</th>
                    <th>Product Title</th>
                    <th>Old Price</th>
                    <th bgcolor="#FFFF00">New Price</th>
                    <th>Old PTR</th>
                    <th bgcolor="#FFFF00">New PTR</th>
                    <td>Comment</td>
                </tr>
                {!! $mailHTML !!}
            </th>
            </table>
            
            <br /><br />
           
            Thanks

        </div>
    </body>
</html>