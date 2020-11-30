<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
</style>
</head>
<body>
<?php 
    $array[]['image'] = "https://ebutormedia.s3.ap-south-1.amazonaws.com/profile/1.jpg";
    $array[]['image'] = "https://ebutormedia.s3.ap-south-1.amazonaws.com/profile/2.jpg";
    $array[]['image'] = "https://ebutormedia.s3.ap-south-1.amazonaws.com/profile/3.jpg";
    
    $mt_rand = mt_rand(0,2);

    $array[0]['year_css'] = " font-size: -webkit-xxx-large;
    color: #B57801;
    margin-top: 22%;
    margin-left: -3%;
    font-family: Georgia;
    font-weight: 800;
    font-size: 500%;";
    $array[0]['profile_css'] = "    height: 110px;
    width: 110px;
    display: block;
    margin-top: 24%;
    border-radius: 50%;";
    $array[0]['name_css'] = "     color: aliceblue;
    font-family: Georgia;
    text-align: center;
    font-size: x-large;
    margin-top: 19%;";
    $array[0]['text_css'] = "    font-family: Georgia;
    font-size: 17px;
    line-height: 20px;
    padding: 20px;
    text-align: justify;";


    $array[1]['year_css'] = " 
    font-style: inherit;
    margin-top: 25%;
    color: #B57801;
    ont-family: Georgia;
    font-weight: 800;
    font-size: 500%;";

    $array[1]['profile_css'] = "     height: 110px;
    width: 110px;
    display: block;
    margin-top: 25%;
    border-radius: 50%;";
    $array[1]['name_css'] = "   color: aliceblue;
    font-family: Georgia;
    text-align: center;
    font-size: x-large;
    margin-top: 19%;";
    $array[1]['text_css'] = "     font-family: Georgia;
    font-size: 17px;
    line-height: 20px;
    padding: 22px;
    margin-top: -3%;
    text-align: justify;";


    $array[2]['year_css'] = " margin-top: 23%;
    font-size: -webkit-xxx-large;
    font-style: inherit;
    color: #B57801;
    margin-left: -3%;
    font-family: Georgia;
    font-weight: 800;
    font-size: 500%;";
    $array[2]['profile_css'] = " height: 110px;
    width: 110px;
    display: block;
    margin-top: 21%;
    margin-right: -2%;
    border-radius: 50%;";
    $array[2]['name_css'] = "  margin-top: 22%;
    font-family: Georgia;
    text-align: center;
    color: darkorange; ";
    $array[2]['text_css'] = "    font-family: Georgia;
    font-size: 17px;
    padding: 22px;
    line-height: 20px;
    text-align: justify;";

    

    $data = $array[$mt_rand];
    


?>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:660px;margin-top:5px;">
  <tr>
    <img style="margin: 0 auto;
    margin-left: auto;
    margin-right: auto;
    display: block;" src="https://ebutormedia.s3.ap-south-1.amazonaws.com/profile/logo.png">
    <td align="center" valign="top" style="padding:10px 20px 20px 10px; border-radius: 20px; height: 450px; background-size: cover;">

    <img src="{{$data['image']}}" style="position: absolute; z-index: -1; margin-left: -25%">
    <p valign="margin-bottom" style="{{$data['year_css']}}">{{$year}}</p>
    <img src="{{$profile_pic}}" style="{{ $data['profile_css'] }}"></img>
    <h3 valign="margin-bottom" style="{{$data['name_css']}}">{{$name}}</h3>
    <h2 style="{{$data['text_css']}}">{{$message_text}}</h2>

    </td>
    </tr>
</table>
<p>This message is confidential. It may also be privileged or otherwise protected by work product immunity or other legal rules. If you have received it by mistake, please let us know by e-mail reply and delete it from your system; you may not copy this message or disclose its contents to anyone. Please send us by fax any message containing deadlines as incoming e-mails are not screened for response deadlines. The integrity and security of this message cannot be guaranteed on the Internet <a target="_blank" href ="https://www.ebutor.com">https://www.ebutor.com</a>.</p>
</body>
</html>