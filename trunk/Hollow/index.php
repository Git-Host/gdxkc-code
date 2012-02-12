<html>
<head>
<meta http-equiv="Content-Language" content="zh-cn" charset="utf-8">
<META name="viewport" content="width=device-width; initial-scale=1.0; minimum-scale=1.0; maximum-scale=2.0">
<title>广州六中树洞发布平台</title>
<link type="text/css" rel="stylesheet" href="style.css">
</head>
<body>
<script type="text/javascript">
function append(val) {
	document.getElementsByName('Text1')[0].value+=val;
}
</script>
<a href="http://blog.sina.com.cn/s/blog_94358a9f0100zcj8.html" name="top">六中树洞详细资料</a><br>
<a href="http://weibo.com/6zsd">直接跳入树洞</a><br>
Windows PC建议直接使用<a href="http://kuai.xunlei.com/d/HBCWESNEHCUX">客户端</a>发送微博<br>
<div class="s"></div>
				<form method="post" action="Submit.php" class="heightlight">
								若需转发微博,请输入<a href="http://blog.sina.com.cn/s/blog_94358a9f0100zere.html">微博MID</a>:<br>
								<input name="mid" type="text" style="width: 100%"><br>
								待发内容:<br>
								<textarea name="Text1" style="width: 100%; height: 120px"></textarea><br>
								<input name="Submit1" type="submit" value="发布">
								<p id="emotable">
Nani?你说你的手机不能用JavaScript?那对不起了……请直接把你记住的表情转义符（比如“[阴险]”）填到上面吧。</p>
								</form>
<a href="#top">返回顶部</a>
<script type="text/javascript">
var emarray={
"呵呵":"/ac/smilea_org.gif",
"嘻嘻":"/0b/tootha_org.gif",
"哈哈":"/6a/laugh.gif",
"可爱":"/14/tza_org.gif",
"可怜":"/af/kl_org.gif",
"挖鼻屎":"/a0/kbsa_org.gif",
"吃惊":"/f4/cj_org.gif",
"害羞":"/6e/shamea_org.gif",
"晕":"/d9/dizzya_org.gif",
"闭嘴":"/29/bz_org.gif",
"鄙视":"/71/bs2_org.gif",
"爱你":"/6d/lovea_org.gif",
"泪":"/9d/sada_org.gif",
"偷笑":"/19/heia_org.gif",
"亲亲":"/8f/qq_org.gif",
"生病":"/b6/sb_org.gif",
"太开心":"/58/mb_org.gif",
"懒得理你":"/17/ldln_org.gif",
"右哼哼":"/98/yhh_org.gif",
"鼓掌":"/36/gza_org.gif",
"嘘":"/a6/x_org.gif",
"衰":"/af/cry.gif",
"委屈":"/73/wq_org.gif",
"吐":"/9e/t_org.gif",
"打哈气":"/f3/k_org.gif",
"抓狂":"/62/crazya_org.gif",
"怒":"/7c/angrya_org.gif",
"疑问":"/5c/yw_org.gif",
"馋嘴":"/a5/cza_org.gif",
"拜拜":"/70/88_org.gif",
"思考":"/e9/sk_org.gif",
"汗":"/24/sweata_org.gif",
"阴险":"/6d/yx_org.gif",
"睡觉":"/6b/sleepa_org.gif",
"钱":"/90/money_org.gif",
"失望":"/0c/sw_org.gif",
"酷":"/40/cool_org.gif",
"花心":"/8c/hsa_org.gif",
"哼":"/49/hatea_org.gif"
};
var tdata='下方为表情:<table>';
curL=0;
for (akey in emarray) {
	if (curL==0) {
		tdata+='<tr>';
	}
	tdata+='<td style="width: 22px; height: 22px"><a href="#" onclick="append(\'['+akey+']\');"><img alt="'+akey+'" src="http://img.t.sinajs.cn/t35/style/images/common/face/ext/normal'+emarray[akey]+'" border="0"></a></td>';
	curL++;
	if (curL==6) {
		tdata+='</tr>';
		curL=0;
	}
}
if (tdata.substring(tdata.length-5,5)!='</tr>') {
	tdata+='</tr>';
}
tdata+='</table>';

document.getElementById('emotable').innerHTML=tdata;
alert("六中樹洞祝你春節快樂~");
</script>
</body>
</html>