<?
$kv = new SaeKV();
$ekv = new SaeKV();
$kv->init();
$ekv->init();
$d=date('Y-m-d H:i:s');
$mail = new SaeMail(); 
$smtp_user='abell3114@gmail.com';
$smtp_pass='858410258';
$ret = $kv->pkrget('web_', 3);
foreach($ret as $arr => $url){
$url='http://'.$url;
$tags = @get_meta_tags( $url );  
if($tags['saecheckweb'] == 'www.lijingquan.net'){  
	$ret=$ekv->get('fail_'.$url);
	if ($ret == '1'){
	echo $ret;
	}else{
    $mto=$kv->get('mail_'.$url);
    $mail->quickSend($mto,'网站恢复正常!','你的网站'.$url.'已经进入正常状态,并处于监控模式!更多功能,正在开发!欢迎访问我的博客:www.lijingquan.net',$smtp_user,$smtp_pass);
    $mail->clean();
    $ekv->set('fail_'.$url, '1');
    }
}else{  
	$ret=$ekv->get('fail_'.$url);
	if ($ret == '0'){
	echo $ret;
	}else{
    $mto=$kv->get('mail_'.$url);
    $mail->quickSend($mto,'网站发现异常!','你的网站'.$url.'已经进入正常状态,并处于监控模式!更多功能,正在开发!欢迎访问我的博客:www.lijingquan.net',$smtp_user,$smtp_pass);
    $mail->clean();  
    $ekv->set('fail_'.$url, '0');
    }
} 
  echo '<br />';
}
?>