<?
function is_email($email){
return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-]+(\.\w+)+$/", $email);
}

$len=strlen($_POST[element_4]);
if (is_email($_POST[element_1]) != 1)
{
echo "邮箱不正确!";
  }else{
  if ($len != 9){echo "城市编号不正确!";
                }else{
  $con = mysql_connect(SAE_MYSQL_HOST_M.":".SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db(SAE_MYSQL_DB, $con);

mysql_query("INSERT INTO Email (Time, Email, City) 
VALUES ('$_POST[element_7]', '$_POST[element_1]', '$_POST[element_4]')");

mysql_close($con);

echo "已经收录!";

    $mail = new SaeMail();
    $mail->quickSend( 
        $_POST[element_1] ,
        "天气预报程序" ,
     "你的设置已经成功!@淡淡清香弥漫世界" ,
        "用户名密码@gmail.com" ,
        "用户名密码" 
    );

  } };
  
  if ($_POST[element_8] == 1){
    $mail = new SaeMail();
    $mail->quickSend( 
        "cn_lite@163.com" ,
        "天气预报程序帮助者信件" ,
     "我的邮箱是:".$_POST[element_1] ,
        "用户名密码@gmail.com" ,
        "用户名密码" 
    );
    echo '<br />你的帮助申请已经发送到开发者邮箱，如需了解更多请新浪微博<a href=http://weibo.com/gdxkc>@淡淡清香弥漫世界</a>!';
  }

?>
