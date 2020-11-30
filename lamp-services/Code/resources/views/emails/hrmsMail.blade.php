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
            
            <br /><br />
            {!! $emailContent !!}<br/>
           <br/>
            <b>Thanks,</b><br/>
            Team Ebutor.


        </div>
    </body>
</html>