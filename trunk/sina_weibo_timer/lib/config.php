<?php
//sina_app_key
define( "WB_AKEY" , '1275027625' );
define( "WB_SKEY" , 'c367177f870081b5219c1a68323daabb' );

//数据库连接
$host="localhost";
$user="root";
$pwd="";
$db_name="sina_weibo";	//数据库名


if(@$con=mysql_connect($host,$user,$pwd))
{
	@mysql_select_db($db_name,$con) or die("<b>链接数据库表失败</b>");
	mysql_query("set names UTF8");
}else{
	echo "<b>连接数据库失败</b>";
	exit;
}


?>
