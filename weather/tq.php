<?php
/*
��������SAE��,Cron���ü����Զ����ʼ�����.
*/
$id = "*";  //City ID @ weather.com.cn
$url = "http://m.weather.com.cn/data/".$id.".html";
$f = new SaeFetchurl();
$f->setMethod('get');
$data = $f->fetch($url);
$json = json_decode($data, TRUE);
$mbody[1]='������'.$json['weatherinfo']['date_y'].''.$json['weatherinfo']['week'];
$mbody[2]='1���:'.$json['weatherinfo']['temp1'].' '.$json['weatherinfo']['weather1'].' '.$json['weatherinfo']['wind1'].$json['weatherinfo']['fl1'];
$mbody[3]='2���:'.$json['weatherinfo']['temp2'].' '.$json['weatherinfo']['weather2'].' '.$json['weatherinfo']['wind2'].$json['weatherinfo']['fl2'];
$mbody[4]='3���:'.$json['weatherinfo']['temp3'].' '.$json['weatherinfo']['weather3'].' '.$json['weatherinfo']['wind3'].$json['weatherinfo']['fl3'];
$mbody[5]='4���:'.$json['weatherinfo']['temp4'].' '.$json['weatherinfo']['weather4'].' '.$json['weatherinfo']['wind4'].$json['weatherinfo']['fl4'];
$mbody[6]='5���:'.$json['weatherinfo']['temp5'].' '.$json['weatherinfo']['weather5'].' '.$json['weatherinfo']['wind5'].$json['weatherinfo']['fl5'];
$mbody[7]='6���:'.$json['weatherinfo']['temp6'].' '.$json['weatherinfo']['weather6'].' '.$json['weatherinfo']['wind6'].$json['weatherinfo']['fl6'];
$mbody[8]='������:'.$json['weatherinfo']['index_uv'];
$mbody[9]='ϴ��ָ��:'.$json['weatherinfo']['index_xc'];
$mbody[10]='����ָ��:'.$json['weatherinfo']['index_tr'];
$mbody[11]='���ʶ�ָ��:'.$json['weatherinfo']['index_co'];
$mbody[12]='����ָ��:'.$json['weatherinfo']['index_cl'];
$mbody[13]='��ɹָ��:'.$json['weatherinfo']['index_ls'];
$mbody[14]='��˹����������ָ��:'.$json['weatherinfo']['index_ag'];
$mbody[15]='����ָ��:'.$json['weatherinfo']['index'];
$mbody[16]='���½���:'.$json['weatherinfo']['index_d'];
$mbody[17]='΢��:'.'����������������'; //��Ȩ,����ʱ�����޸�!
$mopt['from']='*';  //��139�ѿ�ͨSMTP����
$mopt['to']='*@139.com';  //139����
$mopt['smtp_host']='smtp.gmail.com';  //SMTP
$mopt['smtp_port']=587;  //�˿� Gmail:587 ����:25
$mopt['smtp_username']='*'; //�����ʺ�
$mopt['smtp_password']='*'; //����
$mopt['subject']=$json['weatherinfo']['city'].'����Ԥ��';
$mopt['content']=$mbody[1].'<br />'.$mbody[2].'<br />'.$mbody[3].'<br />'.$mbody[4].'<br />'.$mbody[5].'<br />'.$mbody[6].'<br />'.$mbody[7].'<br />'.$mbody[8].'<br />'.$mbody[9].'<br />'.$mbody[10].'<br />'.$mbody[11].'<br />'.$mbody[12].'<br />'.$mbody[13].'<br />'.$mbody[14].'<br />'.$mbody[15].'<br />'.$mbody[16].'<br />'.$mbody[17];
$mopt['content_type']='HTML';
$mopt['tls']=true;
$mail = new SaeMail();
$mail->setOpt($mopt);
$ret = $mail->send($mopt);
echo $ret;
?>