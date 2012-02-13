<?php
/**
 * @author gdxkc
 * @copyright 2012
 */
include_once('config.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="images/favicon.ico" />
    <link href="images/isay.css" rel="stylesheet" type="text/css" media="all" />
    <script type="text/javascript" src="images/isay.js"></script>
	<title>Online Notepad</title>
</head>

<body>
<div class="contain">
<div class="head"><div class="menu">
    <a href="index.php">首页</a>
	<?php 
		@session_start();
		if($_SESSION['logtag'] == 'in') {
			echo '<a href="add.php">添加</a>';
			echo '<a href="login.php?action=logout">退出</a>';
		}else {
			echo '<a href="login.php">登录</a>';
		}
	
	?>
	<a href="message.php">留言</a>
	<?php echo $menuEx; ?>
</div></div>
<div class="tip"><?php echo $tip; ?></div>