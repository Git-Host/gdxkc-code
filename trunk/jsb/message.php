<?php
error_reporting(0);
session_start();
include_once('class.message.php');
include_once('function.php');
include_once('config.php');
if($_GET['action'] == 'add') {
    $guest = $_POST['guest'];
    $content = $_POST['message'];
    if($guest && $content) {
        $message = new message;
        $message->config($server,$user,$password,$db);
        if($message->connect()) {
            $message->addItem($guest,$content);
        }
    }
    header("location:message.php");
}

if($_GET['action'] == 'delete') {
	$id = $_GET['id'];
    if($id) {
        $message = new message;
        $message->config($server,$user,$password,$db);
        if($message->connect()) {
            $message->deleteItem($id);
        }
    }
    header("location:message.php");
}
include_once('head.php');
?>
<div class="book">
<form id="message" action="message.php?action=add" method="post">
<input class="book_guest" type="text" name="guest"/>
<input class="book_message" type="text" name="message"/>
<a class="book_submit" href="javascript:messageSubmit()"></a>
</form>
</div>
<div class="content"></div>
<div class="content">
<?php
$message = new message;
$message->config($server,$user,$password,$db);
if($message->connect()) {
    $array = $message->getAll();
    if(!empty($array)) {
        foreach($array as $item) {
            echo '<div class="content_item">'
                .'<font color="green"><strong>'.$item['guest'].'</strong></font><br />'
                .ubb2html($item['message']).'</div>';
            echo '<div class="content_info">';
            echo $item['postime'];
            if($_SESSION['logtag'] == 'in') {
                echo ' | <a href="message.php?action=delete&id='.$item['id'].'">删除</a>';
            }
            echo '</div>';
        }
    }
}
?>
<div class="content_page">欢迎给我们留言 | 共<?php echo count($array); ?>条记录</div>
</div>
<?php
include_once('foot.php');
?>