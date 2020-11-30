<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <div>
            Hi {{ $name }},
            <br />
            
            <p>New Product has been created in portal.ebutor.com, Product Details:</p>

                <table>
                <tr>
                <td>SKU  : </td>    
                <td>{{ $sku }}</td>
                </tr>
                <tr>
                <td>Title  : </td>    
                <td>{{ $title }}</td>
                </tr>
                <tr>
                <td>MRP  : </td>    
                <td>{{$mrp}}</td>
                </tr>
            </table>
            <br />
                <div>Thanks,</div>
                <div>Ebutor</div>
            </p>             
        </div>
    </body>
</html>