<?php
/**
 * @author gdxkc
 * @copyright 2012
 */
error_reporting(0); 
session_start();
if($_SESSION['logtag'] != 'in') {
    header("location:login.php");
}
include_once('class.isay.php');
include_once('config.php');
if($_GET['action'] == 'add') {
    if($content = $_POST['content']) {
        $isay = new isay;
        $isay->config($server,$user,$password,$db);
        if($isay->connect()) {
            if($isay->addItem($content)) {
                header("location:error.php?info=3");
            }else {
                header("location:error.php?info=4");
            }
        }else {
            header("location:error.php?info=1");
        }
    }else {
        header("location:error.php?info=2");
    }
}
?>

<?php
include_once('head.php');
?>
<div class="edit">
<div class="content_tool">
    <a href="javascript:void(0)" class="url" onclick="tagUrl()"></a>
    <a href="javascript:void(0)" class="img" onclick="tagImg()"></a>
    <a href="javascript:void(0)" class="t" onclick="tagTag()"></a>
    <a href="javascript:void(0)" class="o" onclick="tagOrange()"></a>
    <a href="javascript:void(0)" class="b" onclick="tagBlue()"></a>
    <a href="javascript:void(0)" class="r" onclick="tagRed()"></a>
    <a href="javascript:void(0)" class="g" onclick="tagGreen()"></a>   
</div>
<div class="edit_textarea">
    <form id="content" action="add.php?action=add" method="post">
        <textarea id="edit" name="content">[o]Title[/o] | [t]Sort[/t]</textarea>
    </form>
</div>
<div class="edit_submit"><a href="javascript:addSubmit()" >保存</a></div>
</div>
<?php
include_once('foot.php');
?>