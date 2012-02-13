<?php
/**
 * @author gdxkc
 * @copyright 2012
 */
session_start();
include_once('class.isay.php');
include_once('function.php');
include_once('config.php');
include_once('head.php');
?>
<div class="search">
<div class="notification"><?php echo $notification; ?></div>
<div class="search_input">
    <input type="text" id="key" onkeydown="searchSubmit()" onkeypress="searchSubmit(event)"/>
	<a class="search_submit" href="javascript:searchSubmit2()"></a>
</div></div>
<div class="content">
<?php
$isay = new isay;
$isay->config($server,$user,$password,$db);
if($isay->connect()) {
    $array = $isay->getAll();
	$_GET['page']='';
    if($_GET['page']) {
        $page = $_GET['page'];
    }
    else{
        $page =1;
    }
    
    $totalPage = ceil(count($array)/$eachPage);
    
    $result = pageItem($page,$eachPage,$array);
    if(!empty($result)) {
        foreach($result as $item) {
            echo '<div class="content_item">'.ubb2html($item['content']).'</div>';
            echo '<div class="content_info">';
            echo $item['postime'];
			if($_SESSION['logtag'] == 'in') {
				echo ' | <a href="edit.php?id='.$item['id'].'">编辑</a> | '
					.'<a href="delete.php?id='.$item['id'].'">删除</a>';
			}
			echo '</div>';
        }
    }
    
    $result = pageView($page,$eachPage,count($array));
    echo '<div class="content_page"><a href="index.php">首页</a> ';
    if(!empty($result)) {
        foreach($result as $item) {
            if($item == $page) {
                echo $item.' ';
            }else{
                echo '<a href="index.php?page='.$item.'">['.$item.']</a> ';
            }
        }
    }
    echo '<a href="index.php?page='.$totalPage.'">末页</a>'
        .' | 共'.$totalPage.'页/'.count($array).'条记录</div>';
}
?>
</div>
<?php
include_once('foot.php');
?>