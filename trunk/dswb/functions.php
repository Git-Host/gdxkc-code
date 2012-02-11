<?php 
include_once( 'config.php' );
//发送数据
function uploadByCURL($post_data){
	$curl = curl_init();
	$url =SERVER_URL."/post.php";
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_POST, 1 );
	curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl,CURLOPT_USERAGENT,"Mozilla/4.0");
	$result = curl_exec($curl);
	$error = curl_error($curl);
	return $error ? $error : $result;
}
//获取登录url
function get_login_url()
{
	$ran=rand();
	$_SESSION['ran']=$ran;
	$_SESSION['account']=ACCOUNT;
	$aurl=SERVER_URL."?account=".$_SESSION['account']."&ran=".$_SESSION['ran'];
	return $aurl;
}
?>