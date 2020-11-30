@extends('layouts.default')
@extends('layouts.header')
@extends('layouts.sideview')

@section('content')
        
         <form  action="{{url('/sellerConfigInserting')}}/{{$channelId}}"  role="form" method="post"  name="cahnnelconfig" >
           {{ csrf_field() }}
           <table>
           <tr><td>Channel Referance Name:</td><td><input type="text" id="xx" name="channelreferancename" value=""></td></tr>
           <tr><td>Description:</td><td><input type="text" name="description" value=""></td></tr>
           <tr><td> MarketPlace User Name: </td><td><input type="text" name="marketplaceusername" value=""></td></tr>
           <tr><td>Market Place Password:</td><td><input type="text" name="marketplacepassword" value=""></td></tr>
           
<!--           <tr><td>Api Key:</td><td><input type="text" name="apikey" value=""></td></tr>
           <tr><td>Api:</td><td><input type="text" name="api" value=""></td></tr>-->
         
           
            @foreach($seller_Config_Info as $channel_Config_infos)
            
            <tr>
                <td>{{$channel_Config_infos->field_name}}</td>
                <td><input type="text" name="{{$channel_Config_infos->field_code}}" value="" /></td>
            </tr>
            
            @endforeach 
            <input type="hidden" name="channel_id" value="{{$channelId}}" />
            <tr><td> <input type="submit" value="Done" /></td></tr>
           </table>
         
           
        </form>
          
        

    
 



@stop
@extends('layouts.footer')