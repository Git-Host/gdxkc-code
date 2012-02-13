<?php
/**
 * @author gdxkc
 * @copyright 2012
 */
//数据库连接信息
$server = SAE_MYSQL_HOST_M;
$user = SAE_MYSQL_USER;
$password = SAE_MYSQL_PASS;
$db = SAE_MYSQL_DB;

//后台登录信息,默认为admin,admin,密码为md5加密
$login_username = 'admin';
$login_password = md5("admin");

//如果为了安全你可以直接储存MD5,虽然MD5也不是安全解决方案!
//$login_password = '21232f297a57a5a743894a0e4a801fc3';

//每页显示条数
$eachPage = 5;
//提示
$tip = '今天我忘了做些神马呢~';
//公告
$notification = '提示 | 这是一个Demo,程序虽然简单,但是功能足够就OK了~';
//页脚版权信息
$copyRight = 'Extract and Modified By 淡淡清香弥漫世界 | Base On 云边';
//导航菜单扩展
$menuEx = '<a href="http://sae.sina.com.cn">SAE</a>';
?>