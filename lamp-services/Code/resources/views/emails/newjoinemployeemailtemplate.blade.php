<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
</style>
</head>
<body>
<?php 
    $array[]['image'] = "https://ebutormedia.s3.ap-south-1.amazonaws.com/profile/4.jpg";
    $array[]['image'] = "https://ebutormedia.s3.ap-south-1.amazonaws.com/profile/8.jpg";

    $mt_rand = mt_rand(0,1);
    $array[0]['profile_css'] = " height: 110px;
    width: 110px;
    display: block;
    margin-top: 1%;
    border-radius: 50%;";
    $array[0]['name_css'] = "margin-top: 3%;
    font-family: Georgia;
    text-align: center;
    color: darkcyan;
    font-size: 131%; ";
    $array[0]['text_css'] = " font-family: Georgia;
    font-size: 15px;
    line-height: 25px;
    margin-top: -2%;
    margin-left: 5%;
    margin-right: 5%;
    text-align: justify;";
    $array[0]['designation_css'] = " line-height: 20px;
    padding: 24px;
    text-align: justify;
    font-family: Georgia;
    color: firebrick;
    margin-top: -5%;
    margin-left: 1%;";

    $array[1]['profile_css'] = " height: 110px;
    width: 110px;
    display: block;
    margin-top: 10%;
    border-radius: 50%;";
    $array[1]['name_css'] = "margin-top: 3%;
    font-family: Georgia;
    text-align: center;
    color: darkcyan;
    font-size: 131%; ";
    $array[1]['text_css'] = " font-family: Georgia;
    font-size: 15px;
    line-height: 25px;
    margin-top: -1%;
    margin-left: 8%;
    margin-right: 8%;
    text-align: justify;";
    $array[1]['designation_css'] = " line-height: 20px;
    padding: 24px;
    text-align: justify;
    font-family: Georgia;
    color: firebrick;
    margin-top: -4%;
    margin-left: 4%;";

    $data = $array[$mt_rand];
    
?>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:660px;margin-top:5px;">
  <tr>
    <img style="margin: 0 auto;
    margin-left: auto;
    margin-right: auto;
    
    display: block;" src="https://ebutormedia.s3.ap-south-1.amazonaws.com/profile/logo.png">
    <td align="center" valign="top" style="padding:10px 20px 20px 10px;background-image: url({{$data['image']}}); border-radius: 20px; height: 450px;     background-size: cover;">
    
    <img src="{{$profile_pic}}" style="{{ $data['profile_css'] }}"></img>
    <h3 valign="margin-bottom" style="{{$data['name_css']}}">{{$name}}</h3>
    <h2 style="{{$data['text_css']}}">{{$message_text}}</h2>
    <h3 valign="margin-bottom" style="{{$data['designation_css']}}">{{$designation_text}}</h3>
    </td>
    </tr>
</table>
<p>This message is confidential. It may also be privileged or otherwise protected by work product immunity or other legal rules. If you have received it by mistake, please let us know by e-mail reply and delete it from your system; you may not copy this message or disclose its contents to anyone. Please send us by fax any message containing deadlines as incoming e-mails are not screened for response deadlines. The integrity and security of this message cannot be guaranteed on the Internet <a target="_blank" href ="https://www.ebutor.com">https://www.ebutor.com</a>.</p>
</body>
</html>