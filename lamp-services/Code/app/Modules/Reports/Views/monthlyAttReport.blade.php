<table>
  <thead>
    <tr>
    @foreach($headers as $key => $header)
      <th>{{$key}}</th>
    @endforeach
  
      <?php
        while(strtotime($start_date) <= strtotime($end_date)){
          echo "<th>";
          echo $start_date;
          $start_date = date("Y-m-d",strtotime("+1 day",strtotime($start_date)));
          echo "</th>";
        }
        echo "<th>Present Days</th>";
      ?>
        </tr>
  </thead>
  <tbody>
    @foreach($procedure2 as $key => $data)
      <tr>
        @foreach($data as $key1 => $dat)
          <td>{{$dat}}</td>
        @endforeach
        <?php
          $present_days = 0;
        ?>
         @foreach($procedure as $key => $data1)
              @if($procedure[$key]->UserID == $data->UserID)
                <td>{{$procedure[$key]->TBV }}</td>
                <?php 
                  if($procedure[$key]->TBV != "LOP")
                    $present_days = $present_days + 1;
                ?>
              @endif
        @endforeach

        <td>{{$present_days}}</td>

      </tr>
    @endforeach



   
  </tbody>
</table>
 
