<?php
if ($_POST['submit'] == '上传'){
define('SYSTEM_ROOT', dirname(__FILE__));
//----- 配置开始 -----//
$appKey = 'T3vbP6cFjFRlL52Lx1PD'; // 应用Key
$appSecret = 'vqbc=y=G0W7#7CmcLFJXsDRnLwK#SEdkHHVVG9Ie'; // 应用Secret
$userKey = '5de5fb5ad9f9dd8cf11bc1ffd50419fb'; // 用户授权Key
$userSecret = 'a492f4577f373ac95bd7f49ce29fffad'; // 用户授权Secret
//----- 配置结束 -----//
require 'class/class_weibo_api_sohu.php';
$sohu = weibo_api_sohu::instance();
$sohu->init($appKey, $appSecret, $userKey, $userSecret);
//----- 发布一条图片微博开始 -----//
$result  = $sohu->addPic(array('content' => '', 'pic' => $_FILES['element_1']['tmp_name']));
//----- 发布一条图片微博结束 -----//
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>图床</title>
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>

</head>
<body id="main_body" >
	
	<img id="top" src="top.png" alt="">
	<div id="form_container">
	
		<h1><a>图床</a></h1>
		<form id="form_350237" class="appnitro" enctype="multipart/form-data" method="POST" action="#">
					<div class="form_description">
			<h2>图床</h2>
			<p>无流量限制!速度飞快!你值得拥有,只能上传图片,其他数据请勿尝试,违反国家规定也不要上传!</p>
		<?
if ($_POST['submit'] == '上传'){
                if ($$result['error']!=NULL) {
	echo $$result['error'];
} else {
  echo '小图:'.$result['small_pic'].'<br />';
    echo '中图:'.$result['middle_pic'].'<br />';
    echo '大图:'.$result['original_pic'];
	
                }}
                ?>
                
                
                </div>						
			<ul >
			
					<li id="li_1" >
		<label class="description" for="element_1">图片数据 </label>
		<div>
			<input id="element_1" name="element_1" class="element file" type="file"/> 
		</div> <p class="guidelines" id="guide_1"><small>此处只能放置图片数据,其他数据会导致失败!</small></p> 
		</li>
			
					<li class="buttons">
			  				
			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="上传" />
		</li>
			</ul>
		</form>	
		<div id="footer">
                  My Blog:<a href="http://LiJingQuan.net">LiJingQuan.net</a>
		</div>
	</div>
	<img id="bottom" src="bottom.png" alt="">
	</body>
                       
</html>