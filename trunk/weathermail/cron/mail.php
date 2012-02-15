<?php
$con = mysql_connect(SAE_MYSQL_HOST_M.":".SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db(SAE_MYSQL_DB, $con);

$result = mysql_query("SELECT * FROM Email");
while($row = mysql_fetch_array($result))
  {
    if ($row['Time'] == date(H)){
  
$id = $row['City'];
$url = "http://m.weather.com.cn/data/".$id.".html";
$f = new SaeFetchurl();
$f->setMethod('get');
$data = $f->fetch($url);
$json = json_decode($data, TRUE);
$mbody[1]='今天是'.$json['weatherinfo']['date_y'].''.$json['weatherinfo']['week'];
$mbody[2]='1天后:'.$json['weatherinfo']['temp1'].' '.$json['weatherinfo']['weather1'].' '.$json['weatherinfo']['wind1'].$json['weatherinfo']['fl1'];
$mbody[3]='2天后:'.$json['weatherinfo']['temp2'].' '.$json['weatherinfo']['weather2'].' '.$json['weatherinfo']['wind2'].$json['weatherinfo']['fl2'];
$mbody[4]='3天后:'.$json['weatherinfo']['temp3'].' '.$json['weatherinfo']['weather3'].' '.$json['weatherinfo']['wind3'].$json['weatherinfo']['fl3'];
$mbody[5]='4天后:'.$json['weatherinfo']['temp4'].' '.$json['weatherinfo']['weather4'].' '.$json['weatherinfo']['wind4'].$json['weatherinfo']['fl4'];
$mbody[6]='5天后:'.$json['weatherinfo']['temp5'].' '.$json['weatherinfo']['weather5'].' '.$json['weatherinfo']['wind5'].$json['weatherinfo']['fl5'];
$mbody[7]='6天后:'.$json['weatherinfo']['temp6'].' '.$json['weatherinfo']['weather6'].' '.$json['weatherinfo']['wind6'].$json['weatherinfo']['fl6'];
$mbody[17]='微博:'.'淡淡清香弥漫世界';

$mopt['from']='用户名密码@gmail.com';
$mopt['smtp_host']='smtp.gmail.com';
$mopt['smtp_port']=587;
$mopt['smtp_username']='用户名密码@gmail.com';
$mopt['smtp_password']='用户名密码';
$mopt['subject']=$json['weatherinfo']['city'].'天气预报';
$mopt['content']=$mbody[1].'<br />'.$mbody[2].'<br />'.$mbody[3].'<br />'.$mbody[4].'<br />'.$mbody[5].'<br />'.$mbody[6].'<br />'.$mbody[7].'<br />'.$mbody[17];
$mopt['content_type']='HTML';
$mopt['tls']=true;
$mopt['to']=$row['Email'];
$fh[0]=0;
$fh[1]=0;
$mail = new SaeMail();
$mail->setOpt($mopt);
$mail->send();;
  $fh[0]=$fh[0]+1;
  $fh[1]=$fh[1]+1;
  }else{
  $fh[1]=$fh[1]+1;
    }}
mysql_close($con);
echo '数量:'.$fh[0].'/'.$fh[1];

?>
