<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
</style>
</head>
<body>
<?php 
    $array[]['image'] = "https://ebutormedia.s3.ap-south-1.amazonaws.com/profile/realistic-birthday-balloon-background_52683-4021.jpg";
    $array[]['image'] = "https://ebutormedia.s3.ap-south-1.amazonaws.com/profile/realistic-birthday-party-balloon-background_52683-13090.jpg";
    $array[]['image'] = "https://ebutormedia.s3.ap-south-1.amazonaws.com/profile/Birthday+Wishes+Wallpaper+Hd_17.jpg";
    $array[]['image'] = "https://ebutormedia.s3.ap-south-1.amazonaws.com/profile/Happy-Birthday-Present-Backgrounds.jpg";
    $mt_rand = mt_rand(0,3);

    $array[0]['profile_css'] = "    height: 100px;
    width: 100px;
    display: block;
    margin-top: 13%;
    margin-left: 53%;
    border-radius: 50%;";

    $array[0]['birthday_css'] = "    margin-left: 60%;
    color: orangered; ";

    $array[0]['name_css'] = "    text-align: center;
    display: block;
    margin-top: 3%;
    left: 0%;
    font-size: 18px;
    font-weight: bold;
    margin-right: 21px;
    color: darkturquoise;
    font-family: Source Sans Pro, Helvetica, Arial, sans-serif;
    margin-left: 70%;";
    $array[0]['text_css'] = "    font-size: 13px;
    color: white;
    font-family: Source Sans Pro, Helvetica, Arial, sans-serif;
    margin-bottom: 0px;
    margin-top: 0%;
    margin-left: 63%;";

    $array[1]['profile_css'] = "   height: 100px;
    width: 100px;
    display: block;
    margin-top: 13%;
    margin-right: 60%;
    border-radius: 50%;";
    $array[1]['birthday_css'] = " margin-right: 55%;
    color: deeppink;";
    $array[1]['name_css'] = "  text-align: center;
    display: block;
    margin-top: 5%;
    left: 0%;
    font-size: 18px;
    font-weight: bold;
    color: darkmagenta;
    font-family: Source Sans Pro, Helvetica, Arial, sans-serif;
    margin-right: 55%;";
    $array[1]['text_css'] = "    font-size: 15px;
    color: white;
    font-family: Source Sans Pro, Helvetica, Arial, sans-serif;
    margin-bottom: 0px;
    margin-top: 0%;
    margin-right: 52%;";


    $array[2]['profile_css'] = "   height: 100px;
    width: 100px;
    display: block;
    margin-top: 2%;
    margin-right: 40%;
    border-radius: 50%;";
    $array[2]['birthday_css'] = "color: rebeccapurple;
    margin-right: 40%;
    margin-top: 10%; ";
    $array[2]['name_css'] = "    text-align: center;
    display: block;
    margin-top: 4%;
    left: 0%;
    font-size: 18px;
    font-weight: bold;
    color: black;
    font-family: Source Sans Pro, Helvetica, Arial, sans-serif;
    margin-right: 40%;";
    $array[2]['text_css'] = "    font-size: 17px;
    color: white;
    font-family: Source Sans Pro, Helvetica, Arial, sans-serif;
    margin-bottom: 0px;
    margin-top: 0%;
    margin-right: 40%;";

    $array[3]['profile_css'] = "   height: 100px;
    width: 100px;
    display: block;
    margin-top: 18%;
    border-radius: 50%;";
    $array[3]['birthday_css'] = " color: mediumvioletred;";
    $array[3]['name_css'] = "    text-align: center;
    display: block;
    left: 0%;
    font-size: 18px;
    font-weight: bold;
    color: forestgreen;
    margin-top: -2%;
    font-family: Source Sans Pro, Helvetica, Arial, sans-serif;";
    $array[3]['text_css'] = "    font-size: 15px;
    color: #8b178b;
    font-family: Source Sans Pro, Helvetica, Arial, sans-serif;
    margin-bottom: 0px;
    margin-top: -2%;";

    // $array[0]['hpy_text'] = $array[1]['hpy_text']  = $array[2]['hpy_text']  ="";
    // $array[0]['hpy_text_css'] = $array[1]['hpy_text_css']  = $array[2]['hpy_text_css']  ="";
    
    // $array[3]['hpy_text'] ="HAPPY BIRTHDAY";
    // $array[3]['hpy_text_css'] ="    text-align: center;
    // display: block;
    // font-size: 22px;
    // font-weight: bold;
    // color: #f30f6f;
    // font-family: Source Sans Pro, Helvetica, Arial, sans-serif;";


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

    <h3 valign="margin-bottom" style="{{$data['birthday_css']}}">{{$birthday_text}}</h3>
    <h3 valign="margin-bottom" style="{{$data['name_css']}}">{{$name}}</h3>
    
    <h2 style="{{$data['text_css']}}">{{$message_text}}</h2>

    </td>
    </tr>
</table>
<p>This message is confidential. It may also be privileged or otherwise protected by work product immunity or other legal rules. If you have received it by mistake, please let us know by e-mail reply and delete it from your system; you may not copy this message or disclose its contents to anyone. Please send us by fax any message containing deadlines as incoming e-mails are not screened for response deadlines. The integrity and security of this message cannot be guaranteed on the Internet <a target="_blank" href ="https://www.ebutor.com">https://www.ebutor.com</a>.</p>
</body>
</html>