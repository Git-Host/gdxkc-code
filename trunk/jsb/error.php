<?php
/**
 * @author gdxkc
 * @copyright 2012
 */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="refresh" content="2; url=index.php" />
	<link rel="shortcut icon" href="images/favicon.ico" />
    <link href="images/isay.css" rel="stylesheet" type="text/css" media="all" />
	<title>提示</title>
</head>
<body>
<?php
error_reporting(0);
if($_GET['info'] == 1) {
    echo '<div class="error">连接数据库失败 | 2秒后返回首页</div>';
}
if($_GET['info'] == 2) {
    echo '<div class="error">发布内容不能为空 | 2秒后返回首页</div>';
}
if($_GET['info'] == 3) {
    echo '<div class="error">发布内容成功 | 2秒后返回首页</div>';
}
if($_GET['info'] == 4) {
    echo '<div class="error">发布内容失败 | 2秒后返回首页</div>';
}
if($_GET['info'] == 5) {
    echo '<div class="error">删除内容成功 | 2秒后返回首页</div>';
}
if($_GET['info'] == 6) {
    echo '<div class="error">删除内容失败 | 2秒后返回首页</div>';
}
if($_GET['info'] == 7) {
    echo '<div class="error">删除内容不存在 | 2秒后返回首页</div>';
}
if($_GET['info'] == 8) {
    echo '<div class="error">修改内容成功 | 2秒后返回首页</div>';
}
if($_GET['info'] == 9) {
    echo '<div class="error">修改内容失败 | 2秒后返回首页</div>';
}
if($_GET['info'] == 10) {
    echo '<div class="error">请输入关键字 | 2秒后返回首页</div>';
}
if($_GET['info'] == 11) {
    echo '<div class="error">您登录成功 | 2秒后返回首页</div>';
}
if($_GET['info'] == 12) {
    echo '<div class="error">您退出成功 | 2秒后返回首页</div>';
}
if(!$_GET['info']) {
    echo '<div class="error">这是提示页面 | 2秒后返回首页</div>';
}

?>
</body>
</html>
