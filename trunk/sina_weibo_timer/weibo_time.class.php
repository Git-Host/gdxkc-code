<?

/*
@@ 新浪微博 功能类
@@作 者：xxfs91
@@ 联系邮箱：xxfs91@gmail.com
@@个人blog:http://geekdream.com
@@修改时间：2011-08-24
@@ 实 例：
		
		include('weibo.class.php');
		$new=new sina_weibo();			//实例化
		$new->callback($callback);		//登陆验证
		$new->get_user_id()			//获取用户id并生成lastkey和保存id到SESSION['id'] 保存数据库用
		$new->check_login();		//检查是否已经登陆	


		$new->new_weibo($user_id,$text,$pic=NULL,$time=NULL);		//新增一条微博(时间为空直接发送,否则存入数据库);
		$new->new_person($user_id);		//新增用户
		$new->check_person($user_id)		//检查是否存在用户
		$new->post_weibo()		//发送数据库内时间对应的微博 ---linux命令调用 定时~

*/

	//引入sina weibo api sdk
	include_once('lib/weibooauth.php');
	//配置文件config.php
	include_once('lib/config.php');


	class sina_weibo
	{	
		

		//跳转登陆页面 $callback返回的地址;
		function callback($callback)
		{
			$scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") ? 'http' : 'https';
			$port = $_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443 ? ':'.$_SERVER['SERVER_PORT'] : '';
			$oauth_callback = $scheme . '://' . $_SERVER['HTTP_HOST'] . $port . substr($_SERVER['PHP_SELF'],0,-9).$callback;

			$o = new WeiboOAuth( WB_AKEY , WB_SKEY  );

			$keys = $o->getRequestToken();

			$aurl = $o->getAuthorizeURL( $keys['oauth_token'] ,false , $oauth_callback );

			$_SESSION['keys'] = $keys;

			
			return $aurl;	//返回含有oauth_callbackk地址的验证地址,验证成功返回到$callback页面
		}

		//检测是否登陆
		function check_login()
		{
			if( isset($_SESSION['keys']) )
			{

				return true;

			}else{
				return false;
			}				
		}

		function get_user_id(){
					$o = new WeiboOAuth( WB_AKEY , WB_SKEY , $_SESSION['keys']['oauth_token'] , $_SESSION['keys']['oauth_token_secret']  );

					$last_key = $o->getAccessToken(  $_REQUEST['oauth_verifier'] ) ;

					$_SESSION['last_key'] = $last_key;
					
					$o= new WeiboClient( WB_AKEY , 
                      WB_SKEY , 
                      $last_key['oauth_token'] , 
                      $last_key['oauth_token_secret']  );
					
					 $msg= $o->verify_credentials();
					 $_SESSION['id']=$msg['id'];
					 return $msg['id'];
		}
		//新增微博函数
		function new_weibo($text,$pic=NULL,$time=NULL)
		{
			print_r($_SESSION);
			
			//检测是否有图片
			if( $pic==NULL )
			{
				//检测是否有输入时间
				if($time==NULL)
				{
					//直接发送						
				$new = new WeiboClient( WB_AKEY , 
                      WB_SKEY , 
                      $_SESSION['last_key']['oauth_token'] , 
                      $_SESSION['last_key']['oauth_token_secret']  );
					$msg = $new->update("$text");
					print_r($_SESSION);
					return $msg;
				}else{

					//存入数据库
					$insert=$this->insert_weibo_mysql($_SESSION['id'],"$text","$time",'0','0');
					if($insert==true)
						return "插入新微博成功(不含图片)";
					else
						return "插入失败";
				}				
			}else
			{
					if($time==NULL)
				{
				$c = new WeiboClient( WB_AKEY , 
                      WB_SKEY , 
                      $_SESSION['last_key']['oauth_token'] , 
                      $_SESSION['last_key']['oauth_token_secret']  );

					$msg = $new->upload("$text","$pic");
				}else{
					$insert=$this->insert_weibo_mysql($_SESSION['id'],"$text","$time","$pic",'0');
					if($insert==true)
						return "插入新微博成功";
					else
						return "插入失败";
				}			
			}
		
			print_r($msg);
		}

		//待发送的微博插入数据库--函数
		function insert_weibo_mysql($user_id,$text,$time,$pic_url,$state)
		{
			$insert=mysql_query("insert into weibo_wait (user_id,text,time,pic_url,state) values ('$user_id','$text','$time','$pic_url','$state')");
			if(!$insert)
				return false;
			else
				return true;
		}

		//添加用户信息,用于存储用户密钥
		function new_person($user_id)
		{
			$insert_person=mysql_query("insert into user_oauth (user_id,oauth_token,oauth_token_secret) values ('$user_id','".$_SESSION['last_key']['oauth_token']."','".$_SESSION['last_key']['oauth_token_secret']."')");
			if($insert_person)
				return true;
			else
				return false;
		}
		
		//检测是否存在用户,
		function check_person($user_id)
		{
			$select_person=mysql_query("select * from user_oauth where user_id='".$user_id."' ");
			$num=mysql_num_rows( $select_person );
			if($num!=0)
				return true;
			else
				return false;
		}

		//用于linux系统检测自动发送的函数
		function post_weibo()
		{

			$post_weibo="select user_oauth.oauth_token,user_oauth.oauth_token_secret,weibo_wait.text,weibo_wait.pic_url 
			,weibo_wait.time from weibo_wait INNER JOIN user_oauth ON weibo_wait.user_id = user_oauth.user_id 
			where weibo_wait.time=".date('YmdHi');
			$query=mysql_query($post_weibo);
			//
				while($a=mysql_fetch_array($query))
					{

						//检测是否有图片
						if( $a[3]=='0' )
						{
								//直接发送						
								$new = new WeiboClient( WB_AKEY , 
									  WB_SKEY , 
									  $a[0] ,
									  $a[1]  );
								$msg = $new->update("$a[2]");				
						}else
						{
								$new = new WeiboClient( WB_AKEY , 
									  WB_SKEY , 
									  $a[0] ,
									  $a[1]  );

								$msg = $new->upload("$a[2]","$a[3]");			
						}

							$update=mysql_query("update weibo_wait set state='1' where time='$a[4]'");
							if($msg['error_code']==0&&$update)
								return true;
					}
			
			
		}

	}
?>