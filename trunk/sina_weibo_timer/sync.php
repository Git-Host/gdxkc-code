<?
SESSION_start();
include('weibo_time.class.php');
$new=new sina_weibo();
$new->post_weibo();
?>
