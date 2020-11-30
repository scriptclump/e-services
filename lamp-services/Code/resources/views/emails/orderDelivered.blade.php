<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <div>
            Hello,
            <br />
            <br />
            <p>This is to inform you that an order with Order ID {{$ordercode}} has been delivered. Please note.</p>
            <?php echo (isset($orderDetail) ? $orderDetail : ''); ?>
           
            <br />
               
                <br/>
                <br/>
                <br/>
                <div>Thanks,</div>
                <div>Ebutor Admin</div>
            </p>             
        </div>
    </body>
</html>
