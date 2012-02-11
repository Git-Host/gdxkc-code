<?php
error_reporting(E_ALL & ~E_NOTICE);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DataURL</title>
<style>
body {
 margin:0; padding:0; font-size:12px; color:#343434; background:url(../images/bg.gif) repeat-x;
}
a{ color:#343434; text-decoration:none}
a:hover{color:#F00;text-decoration:underline}
img{border:0;}
h3{font-size:14px;}
ul,li{list-style:none;margin:0; padding:0;line-height:180%;}
.linklist li{height:20px; margin-bottom:3px; overflow:hidden; vertical-align:bottom; font-size:12px;}
.linklist li .num{display:inline-block; float:left; color:#333; margin-right:10px; letter-spacing:1px;}
.linklist li a{display:inline-block; float:left; color:#03C;}
.linklist li a:hover{color:#C30;}
.linklist li .status{display:inline-block; float:right;}
</style>
</head>
<body>
<?php if($p=='1'){
  // echo 	'<span id="loading" style="text-align:center"><img src="loading.gif" /></span>';
}
?>

<?php
$urls = file('url.txt');
$count = count($urls);
$maxpage = ceil($count/10);
if(isset($_GET['dn'])){
	$dn = $_GET['dn'];
}
if(isset($_GET['p'])){
	$p = $_GET['p'];
	if($p<1){
	  $p=1;
}
if($p>$maxpage){
	echo '<div style="color:red; text-align:center">工作完毕！</div>';
	exit;
}
?>
<div style="color:red; text-align:center">（<?php echo ($p*10).'/'.$count;?>）请不要关闭页面，<span id="endtime">10</span>秒后跳到下一页!</div>
<ul class="linklist">
<?php

	$ii = ($p*10)-10;
	$jj = $p*10;
	for($i=$ii;$i<$jj;$i++){
		$link = str_replace('***',$dn,$urls[$i]);
		$link = str_replace('\n','',$link);
		$link = str_replace('\r\n','',$link);
		$link = str_replace(' ','',$link);
		
		echo "<li><span class=\"num\">[".($i+1).".]</span><a target='_blank' href=\"".$link."\">".$link."</a><span class=\"status\"><iframe src='url.htm?".$link."' height='20' width='20' marginwidth='0' marginheight='0' hspace='0' vspace='0' frameborder='0' scrolling='no'></iframe></span></li>";
	}
}

?>
</ul>

<script language="javascript" type="text/javascript">
<!--
//document.getElementById("loading").style.display="none";
var lc = "10";
var p = "<?php echo $p+1;?>";
var k = "<?php echo $dn;?>";
var second=10;
var timer;
function change()
{
	second--;
	if(second>-1)
	{
		document.getElementById("endtime").innerHTML=second;
		timer = setTimeout('change()',1000);
	}
	else
	{
		clearTimeout(timer);
	}
}
timer = setTimeout('change()',1000); 
setTimeout('ourl()',10000);

function ourl()
{
	location.href='data.php?p='+p+'&dn='+k;
}
-->
</script>

</body>
</html>