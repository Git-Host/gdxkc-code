<?php
if ($_POST['submit'] == '上传'){
include_once('saet.ex.class.php');
$akey='98045090';//AppKey
$asec='07fb0ec46141e0bd0563fec0f6e368ed';//AppSecret
$token='acabbae36448457402e32290cfb67220';//AccessToken
$tsec='44b90eae427cbcfaa72ac02d97263e62';//AccessSecret
$so=new SaeTClient($akey,$asec,$token,$tsec);
$x='#分享图片# 随机序号:'.rand(1,99999).'[ http://7130.sinaapp.com/ ]';
$return=$so->upload($x,$_FILES['element_1']['tmp_name']); 
$original_pic=$return['original_pic']; //图片的地址
$thumbnail_pic=$return['thumbnail_pic']; //缩略图地址
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
                if ($return['error']!=NULL) {
	echo $return['error'];
} else {
  echo '原图:'.$original_pic=$return['original_pic'].'<br />';
    echo '缩略图:'.$thumbnail_pic=$return['thumbnail_pic'];
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