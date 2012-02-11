<?php
session_start();
include_once( 'functions.php' );
include_once( 'config.php' );
//回调参数
if(isset($_GET['ran_back']))
{
	$_SESSION['uid']=$_GET['uid'];
	$_SESSION['encrypt']=$_GET['encrypt'];
	$_SESSION['ran_back']=$_GET['ran_back'];
	if($_SESSION['ran']==$_SESSION['ran_back'])
	{
		echo "<script language=\"Javascript\" type=\"text/javascript\">"; echo "window.location.href=\"".BACK_PAGE."\""; 
		echo "</script>";
		exit();
	}else{
		echo "<script language=\"Javascript\" type=\"text/javascript\">"; echo "window.location.href=\"./\""; 
		echo "</script>";
	}
}
$ary=array(
"account"=>ACCOUNT,//公共微博的后缀。由say8分配的域名后缀，并非新浪微博域名。
"uid"=>$_SESSION['uid'],
"settime"=>"",
"year"=>"",
"month"=>"",
"day"=>"",
"hour"=>"",
"minute"=>"",
"text"=>"",
"upFile"=>"",
"encrypt"=>$_SESSION['encrypt']
);
//表单处理
if(isset($_POST['text']))
{
	//先给数组的各个值赋值
	$ary['settime']=$_POST['settime'];
	$ary['year']=$_POST['year'];
	$ary['month']=$_POST['month'];
	$ary['day']=$_POST['day'];
	$ary['hour']=$_POST['hour'];
	$ary['minute']=$_POST['minute'];
	$ary['text']=$_POST['text'];
	if($_FILES['upFile']['tmp_name'])
	{
		$ary['upFile']="@".$_FILES['upFile']['tmp_name'];
	}
	if($_SESSION['ran_back']==$_SESSION['ran'])
	{
		//调用函数执行
		$result=uploadByCURL($ary);
		$c=(json_decode($result,true));
		//print_r($c);
	}else{
		$c =array(
		"success"=>0,
		"tips"=>"验证错误。如此问题多次出现，请关闭所有浏览窗口再打开"
		);
	}
}
?>

