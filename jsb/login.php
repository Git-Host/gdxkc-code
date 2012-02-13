<?php
/**
 * @author gdxkc
 * @copyright 2012
 */
session_start();
/*
if($_SESSION['logtag'] == 'in') {
    header("location:error.php?info=12");
}
*/
error_reporting(0);
if($_GET['action'] == 'logout') {
    session_destroy();
    header("location:error.php?info=12");
}
include_once('config.php');
if($_GET['action'] == 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if($username == $login_username && md5($password) == $login_password) {
        $_SESSION['logtag'] = 'in';
        header("location:error.php?info=11");
    }else {
        header("location:login.php");
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="images/favicon.ico" />
    <link href="images/isay.css" rel="stylesheet" type="text/css" media="all" />
    <script type="text/javascript" src="images/isay.js"></script>
	<title>登录</title>
</head>
<body>
<div class="login">
<form id="login" action="login.php?action=login" method="post">
<div class="login_user"><input type="text" name="username" /></div>
<div class="login_pw"><input type="password" name="password" onkeydown="loginSubmit2()" onkeypress="loginSubmit2(event)" /></div>
</form>
<div class="login_bt">
    <a class="login_submit" href="javascript:loginSubmit()"></a>
    <a class="login_back" href="index.php"></a>
</div>
</div>
</body>
</html>
