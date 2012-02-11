<?php
/*
程序用在SAE上,Cron配置即可自动发邮件天气.
*/
$id = "*";  //City ID @ weather.com.cn
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
$mbody[8]='紫外线:'.$json['weatherinfo']['index_uv'];
$mbody[9]='洗车指数:'.$json['weatherinfo']['index_xc'];
$mbody[10]='旅游指数:'.$json['weatherinfo']['index_tr'];
$mbody[11]='舒适度指数:'.$json['weatherinfo']['index_co'];
$mbody[12]='晨练指数:'.$json['weatherinfo']['index_cl'];
$mbody[13]='晾晒指数:'.$json['weatherinfo']['index_ls'];
$mbody[14]='鼻斯敏过敏气象指数:'.$json['weatherinfo']['index_ag'];
$mbody[15]='穿衣指数:'.$json['weatherinfo']['index'];
$mbody[16]='穿衣建议:'.$json['weatherinfo']['index_d'];
$mbody[17]='微博:'.'淡淡清香弥漫世界'; //版权,公用时请勿修改!
$mopt['from']='*';  //非139已开通SMTP邮箱
$mopt['to']='*@139.com';  //139邮箱
$mopt['smtp_host']='smtp.gmail.com';  //SMTP
$mopt['smtp_port']=587;  //端口 Gmail:587 其他:25
$mopt['smtp_username']='*'; //邮箱帐号
$mopt['smtp_password']='*'; //密码
$mopt['subject']=$json['weatherinfo']['city'].'天气预报';
$mopt['content']=$mbody[1].'<br />'.$mbody[2].'<br />'.$mbody[3].'<br />'.$mbody[4].'<br />'.$mbody[5].'<br />'.$mbody[6].'<br />'.$mbody[7].'<br />'.$mbody[8].'<br />'.$mbody[9].'<br />'.$mbody[10].'<br />'.$mbody[11].'<br />'.$mbody[12].'<br />'.$mbody[13].'<br />'.$mbody[14].'<br />'.$mbody[15].'<br />'.$mbody[16].'<br />'.$mbody[17];
$mopt['content_type']='HTML';
$mopt['tls']=true;
$mail = new SaeMail();
$mail->setOpt($mopt);
$ret = $mail->send($mopt);
echo $ret;
?>