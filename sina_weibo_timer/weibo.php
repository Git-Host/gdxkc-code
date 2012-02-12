<?
session_start();
		include('weibo_time.class.php');
		$new=new sina_weibo();	
		if(!$new->check_login()){//用于检查是否已经登陆	
			echo "你还没有登陆";
			exit;
		}		
echo $_SESSION['id'];

			if(isset($_POST['text'])){
				$time=trim($_POST['time']);
				$pic=trim($_POST['pic']);
			$e=$new->new_weibo($_POST['text'],$pic,$time);
			print_r($e);
			}else{
				echo "你还没有写微博内容";
			}
			echo $e;


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>定时微博</title>
</head>
<body>


<form  method="POST">
<table>
<tr>
	<td width="50px">
		新增的微博内容
	</td>
	<td>
		<textarea name="text" cols="40" rows="8"></textarea>
	</td>
</tr>
<tr>
	<td>
		图片链接:
	</td>
	<td>
		<input type="text" size="50"  name="pic">
	</td>
</tr>
<tr>
	<td>
		定时发送(不填则立即发送)
	</td>
	<td>
		<input type="text" name="time" size="15">(<i>example: 201107301457</i>)
	</td>
</tr>
<tr>
</tr>
</table>
<input type="submit" value="发送">

</form>
</body>
</html>

