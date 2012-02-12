<?
session_start();
			include('weibo_time.class.php');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>定时微博</title>
</head>
<body>
<?
		
		$new=new sina_weibo();
		echo '<a href="'.$new->callback('callback.php').'">oauth认证</a>'; 
?>
</body>
</html>