<?php

$d = $_SERVER['QUERY_STRING'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $d; ?>在线自动外链站</title>
<meta name="keywords" content="站长工具,查询工具,免费增加外链,<?php echo $d; ?>" />
<meta name="description" content="站长工具,超级SEO外链工具<?php echo $d; ?>，免费刷外链，快速增加外链！" />
<link href="images/style.css" rel="stylesheet" type="text/css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
function cgurl(obj,url){
	obj.href=url;
}

function ck_dn(str)
{
        if(!/^([\w-]+\.)+((com)|(net)|(org)|(gov\.cn)|(info)|(cc)|(me)|(asia)|(com\.cn)|(net\.cn)|(org\.cn)|(name)|(biz)|(tv)|(cn)|(la))$/.test(str)){
            return false;
        }else{
		    return true;	
		}

}
</script>

</head>

<body>
<div id="header">
   <div id="logo"><a href="#"><img src="images/logo.gif" /></a></div>
   <div id="adbanner"></div>
</div><!--header-->
<div id="menu">
  <div class="con">
  <a href="http://sae.sina.com.cn" target="_blank">SAE首页</a>
  <a href="./" class="this">超级外链</a>
  <a href="http://weibo.com/gdxkc" target="_blank">搭建者微博</a>

  </div>
</div><!--menu-->
<div id="main">
  <div class="cbox">
     <div class="head"><h1>超级SEO外链</h1></div>
     <div class="con">
        <div align="center">
         请输入你要刷外链的网址：http://<input type="text" ID="dn" value="<?php echo $d; ?>" class="input" name="dn" style="width:350px" /> <input name="" class="btn" id="linkbtn" type="submit" value="开始执行"  />
         </div>
     </div><!--con-->
     <div style="clear:both"></div>
  </div><!--cbox-->
  
  <div class="cbox" id="linkshow" style=" <?php if(strlen($d)==0){echo 'display:none';} ?>">
     <div class="head"><h1>正在访问的链接</h1></div>
     <div class="con" id="linklist">
           <?php
			  if(strlen($d)>3){
				  echo "<iframe src='data.php?p=1&dn=".$d."' height='270' width='900' marginwidth='0' marginheight='0' hspace='0' vspace='0' frameborder='0' scrolling='no'></iframe>";
			  }
?>
     </div><!--con-->
     <div style="clear:both"></div>
  </div><!--cbox-->
<script type="text/javascript">

$("#linkbtn").click(function(){
		$domain = $("#dn").val();
		if($domain==""){
		   alert("请输入域名");	
		}else if(ck_dn($domain)){
			$("#linkshow").show();
			$("#linklist").html("<iframe src='data.php?p=1&dn="+$domain+"' height='270' width='900' marginwidth='0' marginheight='0' hspace='0' vspace='0' frameborder='0' scrolling='no'></iframe>");

		}else{
            		$("#linkshow").show();
			$("#linklist").html("<iframe src='data.php?p=1&dn="+$domain+"' height='270' width='900' marginwidth='0' marginheight='0' hspace='0' vspace='0' frameborder='0' scrolling='no'></iframe>");

                        alert("此地址效果可能不理想。");
		}
							 
});

</script>  
  <div class="cbox">
     <div class="head"><h1>工具介绍</h1></div>
     <div class="con">
       　　超级外链快速增加网站外链的原理: 超级外链由本站精心收集了数个ip查询 Alexa排名查询，pr查询等站长常用查询网站，由于这些网站大多有查询记录显示功能，而且查询记录可以被百度，谷歌，搜狗等搜索引擎快速收录，这样就形成了外链。经过长时间观察发现这种外链有很大一部分还是比较稳定，所以可以用来进行seo利用，因为这是正常的查询产生的外链，所以这种外链对SEO效果还是很明显的！
把复杂的友情链接交换过程交给电脑，交给批量而自动化的外链工具，节省我们的时间、健康、人力、金钱和脑细胞。现在开始，体验和享受功能强大、轻松便捷而免费的网站推广过程吧。根据最新的科学艺术预测：现如今人类的一切重复性劳动，在未来都可以被机器和工具替代，人可以腾出手来，从事自己喜爱的创造性的事情。就让我们先行一步吧，把网站的宣传推广工作交由机器来完成。 



  </div><!--con-->
     <div style="clear:both"></div>
  </div><!--cbox-->

  
</div><!--main-->
<div id="footer">
Copyleft &copy; <a href="http://weibo.com/gdxkc" target="_blank">淡淡清香弥漫世界</a> SAE平台支持 </div><!--footer-->
</body>
</html>
