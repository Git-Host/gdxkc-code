<?php
//JSON to Array
function object_array($array)
{
   if(is_object($array))
   {
    $array = (array)$array;
   }
   if(is_array($array))
   {
    foreach($array as $key=>$value)
    {
     $array[$key] = object_array($value);
    }
   }
   return $array;
}
//URL-HTTP判断 (1-正确 0-错误)
function is_httpurl($str){
  return preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/", $str);
 }
//BaiDu
function baidu($xxurl){

$ch=curl_init();

curl_setopt($ch,CURLOPT_URL,"http://dwz.cn/create.php");

curl_setopt($ch,CURLOPT_POST,true);

curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

$data=array('url'=>$xxurl);

curl_setopt($ch,CURLOPT_POSTFIELDS,$data);

$strRes=curl_exec($ch);

curl_close($ch);

$arrResponse=json_decode($strRes,true);

if($arrResponse['status']==0)

return $arrResponse['tinyurl'];

}
//Bai.LU
function bailu($xxurl){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://bai.lu/api?url=" . urlencode($xxurl));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
$url = json_decode(curl_exec($ch), true);
echo $url['url'];
}
//Bit.LY
function bitly($xxurl){

function get_bitly_short_url($url,$login,$appkey,$format='txt') {  
$connectURL = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;  
return curl_get_result($connectURL);  
}  
 
function curl_get_result($url) {  
$ch = curl_init();  
$timeout = 5;  
curl_setopt($ch,CURLOPT_URL,$url);  
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
$data = curl_exec($ch);  
curl_close($ch);  
return $data;  
}  
  
return $short_url = get_bitly_short_url($xxurl,'cl17726','R_8b11d4a89732abac4fb46ef9b40f54ec');  

}
//is.GD
function isgd($xxurl){
  $f = new SaeFetchurl();
  $content = $f->fetch('http://is.gd/create.php?format=simple&url='.$xxurl);
return $content;
}
//v.GD
function vgd($xxurl){
  $f = new SaeFetchurl();
  $content = $f->fetch('http://v.gd/create.php?format=simple&url='.$xxurl);
return $content;
}
//LNK.by
function lnkby($xxurl){
$f = new SaeFetchurl();
$content = $f->fetch('http://lnk.by/Shorten?url='.$xxurl.'&format=json');
$data=object_array(json_decode($content));
return ($data['ShortUrl']);
} 
//Sinaweibo
function sinaweibo($xxurl){
function shortenSinaUrl($long_url){
 $apiKey='4098049467';
 $apiUrl='http://api.t.sina.com.cn/short_url/shorten.json?source='.$apiKey.'&url_long='.$long_url;
 $curlObj = curl_init();
 curl_setopt($curlObj, CURLOPT_URL, $apiUrl);
 curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
 curl_setopt($curlObj, CURLOPT_HEADER, 0);
 curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
 $response = curl_exec($curlObj);
 curl_close($curlObj);
 $json = json_decode($response);
 return $json[0]->url_short;
}

function expandSinaUrl($short_url){
 $apiKey='4098049467';
 $apiUrl='http://api.t.sina.com.cn/short_url/expand.json?source='.$apiKey.'&url_short='.$short_url;
 $curlObj = curl_init();
 curl_setopt($curlObj, CURLOPT_URL, $apiUrl);
 curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
 curl_setopt($curlObj, CURLOPT_HEADER, 0);
 curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
 $response = curl_exec($curlObj);
 curl_close($curlObj);
 $json = json_decode($response);
 return $json[0]->url_long;
}


return shortenSinaUrl($xxurl);
}

function xurl($xxurl){
$f = new SaeFetchurl();
$content = $f->fetch('http://xurl.cn/api.php?url='.$xxurl);
$data=object_array(json_decode($content));
return ($data['info']);
}
//ZZB
function zzb($xxurl){
$url = "http://zzb.bz/panel/api/create/";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, TRUE);

	$param_key = "NJR07"; // your api key
	$param_url = $xxurl;  // your huge url
	$param_password = "";
	$param_description = "";
	$array = array('key'=>$param_key, 'url'=>$param_url, 'password'=>$param_password, 'description'=>$param_description);
	curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($array));

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	$response = curl_exec($ch);
	curl_close($ch);
   

$data=object_array(json_decode($response));
return ($data['Result']);
}
//Google
function GoogleUrl($xxurl){
 $apiKey = 'AIzaSyCKC1gSDQujgr94UgI_tVJahCGoOoIeuJ0'; 
 $postData = array('longUrl' => $xxurl, 'key' => $apiKey);
 $jsonData = json_encode($postData);
 $curlObj = curl_init();
 curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url');
 curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
 curl_setopt($curlObj, CURLOPT_HEADER, 0);
 curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
 curl_setopt($curlObj, CURLOPT_POST, 1);
 curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);
 $response = curl_exec($curlObj);
 curl_close($curlObj);
 $json = json_decode($response);
 return $json->id;
}

if (isset($_GET['longurl']))
  {
  $xxurl = $_GET['longurl'] ; 
    if (is_httpurl($xxurl) == 0){
$darr[Code]="-1";
$darr[Baidu]="NULL";
$darr[Bitly]="NULL";
$darr[Isgd]="NULL";
$darr[Lnkby]="NULL";
$darr[Sinaweibo]="NULL";
$darr[Zzb]="NULL";
$darr[Vgd]="NULL";
$darr[Google]="NULL";
$darr[CL]="www.weibo.com/gdxkc";
	}
    else{
$darr[Code]="0";
$darr[Baidu]=baidu($xxurl);
$darr[Bitly]=bitly($xxurl);
$darr[Isgd]=isgd($xxurl);
$darr[Lnkby]=lnkby($xxurl);
$darr[Sinaweibo]=sinaweibo($xxurl);
$darr[Zzb]=zzb($xxurl);
$darr[Vgd]=vgd($xxurl);
$darr[Google]=GoogleUrl($xxurl);
$darr[CL]="www.weibo.com/gdxkc";
}
  }
else
  {
$darr[Code]="-2";
$darr[Baidu]="NULL";
$darr[Bitly]="NULL";
$darr[Isgd]="NULL";
$darr[Lnkby]="NULL";
$darr[Sinaweibo]="NULL";
$darr[Zzb]="NULL";
$darr[Vgd]="NULL";
$darr[Google]="NULL";
$darr[CL]="www.weibo.com/gdxkc";
  }
echo json_encode($darr);
if($_GET['a'] == 1){
echo '<pre>';
print_r($darr);
echo '</pre>';
}
?>