<html>
    <table>
        @foreach($tripdata as $data)	
        <tr>
            @foreach($data as $d)
            <td>{{$d}}</td>
            @endforeach    
        </tr>
        @endforeach
    </table>
    
</html>