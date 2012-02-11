<?php
session_start();
if(!$_SESSION['ran_back'])
{
	header("location:./login.php");
	exit();
}
require('post.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>demo示例</title>
</head>
<body>
<div >
<form action="" method="post" enctype="multipart/form-data">
这是一个简单的示例。你可以根据自己的需要去DIY自己的风格<br />
<textarea class="text" name="text" cols="50" rows="5" id="text" ></textarea>
<br />添加图片
<input type="file" name="upFile" id="upFile"/>
<br />
<input type="submit" value="提交" />
</form>
<?php 
//当$c['success']==1时，表示发送成功。当$c['success']==0时，表示发送失败。这里无论成功或失败，都输出提示信息。
echo $c['tips'];//返回提示
?>
</div>
</body>
</html>