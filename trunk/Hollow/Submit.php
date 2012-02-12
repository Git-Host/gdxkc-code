<head>
<meta http-equiv="Content-Language" content="zh-cn" charset="utf-8">
<META name="viewport" content="width=device-width; initial-scale=1.0; minimum-scale=1.0; maximum-scale=2.0">
<title>广州六中树洞信息提交页</title>
<link rel="stylesheet" type="text/css" href="style.css">
<?php

include_once('saet.ex.class.php');
/**
 *
 *
 * @version $Id$
 * @copyright 2011
 */
$akey='';//應用的AppKey
$asec='';//應用的AppSecret
$token='';//用戶的AccessToken
$tsec='';//用戶的AccessSecret
//上面的全部要字符串
$so=new SaeTClient($akey,$asec,$token,$tsec);

if ($_POST[mid]!=NULL && strlen($_POST[mid])>0) {
	$curl=curl_init();
	curl_setopt($curl, CURLOPT_URL, 'http://api.t.sina.com.cn/queryid.json?mid='.$_POST[mid].'&isBase62=1&type=1');
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	$rs=curl_exec($curl);
	curl_close($curl);

	$id=json_decode($rs)->id;
	$return=$so->repost($id,$_POST[Text1],0);
} else {
	$return=$so->update(str_replace('\r','',str_replace('\n',' ',$_POST[Text1])),NULL,NULL,NULL,NULL);
}

if ($return['error']!=NULL) {
	echo '发布失败。<br />'.$return['error'];
} else {
	echo '发布成功。';
}
?>
</head>
<html>
<body>
<br>
<a href="http://weibo.com/6zsd">跳入六中树洞</a>(若使用手机请直接用新浪客户端)<br>
<a href="javascript:void(0);" onclick="history.back()">后退</a>
</body>
</html>