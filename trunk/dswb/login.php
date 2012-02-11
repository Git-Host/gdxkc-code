<?php
session_start();
include_once( 'functions.php' );
$aurl=get_login_url();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>登录页面</title>
</head>
<body>
<div align="center" id="login">
  <p>这是登录按钮。可以按照需要自己更改风格外观</P>
  <p><a href="<?php echo $aurl?>"><img src="http://weibo7th.sinaapp.com/say8/images/sina_button.png" border="0"></a></p>
</div>
</body>
</html>
