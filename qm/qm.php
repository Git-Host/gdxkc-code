<?php
function getBrowse()
{
global $_SERVER;
$Agent = $_SERVER['HTTP_USER_AGENT'];
$browseinfo='';
if(ereg('Mozilla', $Agent) && !ereg('MSIE', $Agent)){
$browseinfo = 'Netscape Navigator';
}
if(ereg('Opera', $Agent)) {
$browseinfo = 'Opera';
}
if(ereg('Mozilla', $Agent) && ereg('MSIE', $Agent)){

$browseinfo = 'Internet Explorer';
}
if(ereg('Chrome', $Agent)){
$browseinfo="Chrome";
}
if(ereg('Firefox', $Agent)){
$browseinfo="Firefox";
}

return $browseinfo;
}
 
function getIP ()
{
global $_SERVER;
if (getenv('HTTP_CLIENT_IP')) {
$ip = getenv('HTTP_CLIENT_IP');
} else if (getenv('HTTP_X_FORWARDED_FOR')) {
$ip = getenv('HTTP_X_FORWARDED_FOR');
} else if (getenv('REMOTE_ADDR')) {
$ip = getenv('REMOTE_ADDR');
} else {
$ip = $_SERVER['REMOTE_ADDR'];
}
return $ip;
}
 
function getOS ()
{
global $_SERVER;
$agent = $_SERVER['HTTP_USER_AGENT'];
$os = false;
if (eregi('win', $agent) && strpos($agent, '95')){
$os = 'Windows 95';
}
else if (eregi('win 9x', $agent) && strpos($agent, '4.90')){
$os = 'Windows ME';
}
else if (eregi('win', $agent) && ereg('98', $agent)){
$os = 'Windows 98';
}
else if (eregi('win', $agent) && eregi('nt 5.1', $agent)){
$os = 'Windows XP';
}
else if (eregi('win', $agent) && eregi('nt 5.2', $agent)){
$os = 'Windows 2003';
}
else if (eregi('win', $agent) && eregi('nt 6.0', $agent)){
$os = 'Windows Vista';
}
else if (eregi('win', $agent) && eregi('nt 6.1', $agent)){
$os = 'Windows 7';
}
else if (eregi('win', $agent) && eregi('nt 6.2', $agent)){
$os = 'Windows Slate';
}
else if (eregi('win', $agent) && eregi('nt 5', $agent)){
$os = 'Windows 2000';
}
else if (eregi('win', $agent) && eregi('nt', $agent)){
$os = 'Windows NT';
}
else if (eregi('win', $agent) && ereg('32', $agent)){
$os = 'Windows 32';
}
else if (eregi('linux', $agent)){
$os = 'Linux';
}
else if (eregi('unix', $agent)){
$os = 'Unix';
}
else if (eregi('sun', $agent) && eregi('os', $agent)){
$os = 'SunOS';
}
else if (eregi('ibm', $agent) && eregi('os', $agent)){
$os = 'IBM OS/2';
}
else if (eregi('Mac', $agent) && eregi('PC', $agent)){
$os = 'Macintosh';
}
else if (eregi('PowerPC', $agent)){
$os = 'PowerPC';
}
else if (eregi('AIX', $agent)){
$os = 'AIX';
}
else if (eregi('HPUX', $agent)){
$os = 'HPUX';
}
else if (eregi('NetBSD', $agent)){
$os = 'NetBSD';
}
else if (eregi('BSD', $agent)){
$os = 'BSD';
}
else if (ereg('OSF1', $agent)){
$os = 'OSF1';
}
else if (ereg('IRIX', $agent)){
$os = 'IRIX';
}
else if (eregi('FreeBSD', $agent)){
$os = 'FreeBSD';
}
else if (eregi('teleport', $agent)){
$os = 'teleport';
}
else if (eregi('flashget', $agent)){
$os = 'flashget';
}
else if (eregi('webzip', $agent)){
$os = 'webzip';
}
else if (eregi('offline', $agent)){
$os = 'offline';
}
else {
$os = 'Unknown';
}
return $os;
}


$SaeLocationObj = new SaeLocation();
$ip_to_geo_arr = array('ip'=>getIP());
$ip_to_geo = $SaeLocationObj->getIpToGeo($ip_to_geo_arr);



$ob=getBrowse();
$os= getOS ();
$ip=getIP ();
$black = imagecolorallocate($im, 0,0,0);


$str="IP:".$ip;
$str2=$os;
$str3=$ob;
$str4=$ip_to_geo['geos']['0']['longitude'].'|'.$ip_to_geo['geos']['0']['latitude'];
$str5=$ip_to_geo['geos']['0']['more'];
$str6=date("Y-m-d H:i:s");


$sj=rand(1,10);
switch ($sj)
{
	case 1:
	$say="程序无版权，有兴趣者可以联系我获得源码。";
	break;
	case 2:
	$say="哎呀，怎么一个不小心被你看到我的个性签名了。";
	break;
	case 3:
	$say="今天天气还不错，来逛逛论坛吧。";
	break;
	case 4:
	$say="贵客到，贵客到，竟然有人看我的签名。";
	break;
	case 5:
	$say="独一无二，网上无此签名啦！";
	break;
	case 6:
	$say="有时间再想想该怎么优化优化程序。";
	break;
	case 7:
	$say="写程序是一件让人很抓狂的事情。";
	break;
	case 8:
	$say="我不小心内存溢出，先去整理整理。";
	break;
	case 9:
	$say="客官真是得闲啊！";
	break;
  default:
	$say="额，一时之间不知道可以写什么。";
}
if ($sj<>10){
$str7='0'.$sj;
}


@header("Content-Type:image/png"); 

$im = @imagecreatefrompng ("back.png");//读取图片名

$color = imagecolorallocate($im, 183, 150, 37); //文字颜色

imagestring($im,4,5,120,$str,$font); 
imagettftext($im,12,0,7,155,$black,SAE_Font_Sun,"操作系统:");
imagestring($im,4,80,140,$str2,$font); 
imagettftext($im,12,0,7,175,$black,SAE_Font_Sun,"浏览器:");
imagestring($im,4,60,160,$str3,$font); 
imagettftext($im,12,0,7,195,$black,SAE_Font_Sun,"经纬度:");
imagestring($im,4,60,180,$str4,$font);  
imagettftext($im,12,0,7,215,$black,SAE_Font_Sun,"服务提供商:".$str5);
imagettftext($im,12,0,7,235,$black,SAE_Font_Sun,"目前时间:");
imagestring($im,4,80,220,$str6,$font);  
imagestring($im,4,80,240,"--------------------------------",$font); 
imagettftext($im,12,0,7,275,$black,SAE_Font_Sun,$say);
imagestring($im,4,195,85,$str7,$font);

imagerectangle($im,0,0,$width-1,$height-1,$font); 
imagepng($im); 
imagedestroy($im); 
?>