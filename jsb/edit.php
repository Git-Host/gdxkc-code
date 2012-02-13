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
if($_GET['action'] == 'edit') {
    $id = $_GET['id'];
    if($content = $_POST['content']) {
        $isay = new isay;
        $isay->config($server,$user,$password,$db);
        if($isay->connect()) {
            if($isay->changeItem($id,$content)) {
                header("location:error.php?info=8");
            }else {
                header("location:error.php?info=9");
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
    <form id="content" action="edit.php?action=edit&id=<?php echo $_GET['id']; ?>" method="post">
    <?php
        if($id = $_GET['id']) {
            $isay = new isay;
            $isay->config($server,$user,$password,$db);
            if($isay->connect()) {
                $item = $isay->getItemById($id);
                echo '<textarea id="edit" name="content">'.$item['content'].'</textarea>';
            }
        }   
    ?>
    </form>
</div>
<div class="edit_submit"><a href="javascript:editSubmit()">修改</a></div>
</div>
<?php
include_once('foot.php');
?>