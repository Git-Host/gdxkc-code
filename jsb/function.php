<?php
/**
 * @author gdxkc
 * @copyright 2012
 */
function ubb2html($str) {
    //Html过滤&,<,>,",', ,\t
	$str = preg_replace("/\&/","&amp;",$str);
	$str = preg_replace("/</","&lt;",$str);
	$str = preg_replace("/>/","&gt;",$str); 
	   
    $str = preg_replace("/\"/", "&quot;",$str);    
    $str = preg_replace("/\'/", "&#39;",$str);   
    $str = preg_replace("/\ /", "&nbsp;",$str);  
    $str = preg_replace("/\t/", "&nbsp;&nbsp;&nbsp;&nbsp;",$str);              
    //换行
	$str=preg_replace("/\r?\n/",'<br />',$str);
    
    //图片
    $str = preg_replace("/(\[img\])(.*)(\[\/img\])/iU", "<img src=\\2>", $str);
	//网址url
	$str = preg_replace("/(\[url\])(.*)(\[\/url\])/iU", "<a href=\\2 target=\"new\">\\2</a>", $str);
	
    //颜色
    $match = array("/(\[b\])(.*)(\[\/b\])/iU",
                   "/(\[r\])(.*)(\[\/r\])/iU",
                   "/(\[g\])(.*)(\[\/g\])/iU",
                   "/(\[o\])(.*)(\[\/o\])/iU",
                   "/(\[t\])(.*)(\[\/t\])/iU");
    $replace = array("<font color=blue>\\2</font>",
                     "<font color=red>\\2</font>",
                     "<font color=green>\\2</font>",
                     "<font color=#FF8000>\\2</font>",
                     "<font color=#7A9833>\\2</font>");
    $str = preg_replace($match, $replace, $str);
    return $str;
}

function getTag($str) {
    preg_match("/(\[t\])(.*)(\[\/t\])/iU",$str,$array);
    $tag = $array[0];
    $tag = preg_replace("/(\[t\])(.*)(\[\/t\])/iU", "\\2", $tag);
    return explode(',',$tag);
}

function pageView($page,$eachpage,$totalitem) {
    $totalpage = ceil($totalitem/$eachpage);
    if($page-2>0 && $page-2<$totalpage+1) {
        $array[] = $page-2;
    }
    if($page-1>0 && $page-1<$totalpage+1) {
        $array[] = $page-1;
    }
    if($page>0 && $page<$totalpage+1) {
        $array[] = $page;
    }
    if($page+1>0 && $page+1<$totalpage+1) {
        $array[] = $page+1;
    }
    if($page+2>0 && $page+2<$totalpage+1) {
        $array[] = $page+2;
    }
    return $array;
}

function pageItem($page,$eachpage,$array) {
    $totalitem = count($array);
    $beginpage = ($page-1)*$eachpage;
    for($num = $beginpage;$num<$beginpage+$eachpage;$num++) {
        if($num<$totalitem) {
            $result[] = $array[$num];
        }
    }
    return $result;
}

?>