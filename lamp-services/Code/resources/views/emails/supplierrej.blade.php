<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <div>
            Hi {{ $srmname }},
            <br />
            <p>The supplier "{{ $suppliername }}" has been rejected.<br/> Comments: </p>            
			<p>{{$comments}}</p>
            <p>                
                <div>Thanks,</div>
                <div>Ebutor</div>
            </p>             
        </div>
    </body>
</html>