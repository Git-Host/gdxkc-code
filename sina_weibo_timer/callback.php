<?
session_start();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>定时微博</title>
</head>
<body>

<?
		include('weibo_time.class.php');
		$new=new sina_weibo();	
		$user_id=$new->get_user_id();			

		if($new->check_login()){	//用于检查是否已经登陆
			echo "认证成功<br/>";
			echo "<a href='weibo.php'>跳转进去</a><br/>";
			
			if(!$new->check_person($user_id)){		//新增用户
				if($new->new_person($user_id)){
					echo "添加用户成功.";
					print_r($_SESSION['keys']);
				}else{
					echo "添加用户失败";		
					print_r($_SESSION['keys']);
				}
			}else{
				echo "失败因数据库存在此用户";
									
					print_r($_SESSION['keys']);
			}
		}else{
			echo "认证出现错误";
			print_r($_SESSION['keys']);
	
		}
			

?>
</body>
</html>


		
