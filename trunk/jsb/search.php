<?php
/**
 * @author gdxkc
 * @copyright 2012
 */
session_start();
error_reporting(0);
include_once('class.isay.php');
include_once('function.php');
include_once('config.php');
include_once('head.php');
?>
<div class="content">
<div class="search">
<div class="notification">提示 | 可以使用多个关键字搜索，关键字之间用空格分开。</div>
<div class="search_input">
    <input type="text" id="key" onkeydown="searchSubmit()" onkeypress="searchSubmit(event)"/>
	<a class="search_submit" href="javascript:searchSubmit2()"></a>
</div></div>
<?php

$key = urldecode($_GET['key']);
$keyarray = explode(' ',$key);

$isay = new isay;
$isay->config($server,$user,$password,$db);
if($isay->connect()) {
    $array = $isay->search($keyarray);
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
    echo '<div class="content_page"><a href="search.php?key='.urlencode($key).'">首页</a> ';
    if(!empty($result)) {
        foreach($result as $item) {
            if($item == $page) {
                echo $item.' ';
            }else{
                echo '<a href="search.php?key='.urlencode($key).'&page='.$item.'">['.$item.']</a> ';
            }
        }
    }
    echo '<a href="search.php?key='.urlencode($key).'&page='.$totalPage.'">末页</a>'
        .' | 共'.$totalPage.'页/'.count($array).'条记录</div>';
}
?>
</div>
<?php
include_once('foot.php');
?>