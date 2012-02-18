<?php
if ($_POST['submit'] == '上传'){
	@session_start();
	include_once( 'txwboauth.php' );
	$pic=file_get_contents($_FILES['element_1']['tmp_name']);
	$c = new WeiboClient( '801096156' , '750c8eaab78db1e46e2d26dab726fa4c' , 'c782b7f7f41a41849f91bb05891b68a9' , '9d3ebc512b1f5665c04f148261e896a5'  );
	$ms=$c->t_add_pic('图片分享',$pic);
    $kk=$c->t_show($ms['data']['id']);
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
    echo '图片地址:'.$kk['data']['image'][0];
           }
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