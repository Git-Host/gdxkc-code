<?

if ($_POST['submit'] == '进入'){
  switch($_POST['element_1']){
    case 1:
    header("location:xlpic.php");
    break;
    case 2:
    header("location:wypic.php");
    break;
    case 3:
    header("location:shpic.php");
    break;
    case 4:
    header("location:txpic.php");
    break;
  }

}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>主页(新浪微博@淡淡清香弥漫世界)</title>
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>

</head>
<body id="main_body" >
	
	<img id="top" src="top.png" alt="">
	<div id="form_container">
	
		<h1><a>主页(新浪微博@淡淡清香弥漫世界)</a></h1>
		<form id="form_351086" class="appnitro"  method="post" action="">
					<div class="form_description">
			<h2>主页(新浪微博@淡淡清香弥漫世界)</h2>
			<p>请选择接下来的动作!目前已经开放四大微博平台作为图床了哦~</p>
		</div>						
			<ul >
			
					<li id="li_1" >
		<label class="description" for="element_1">接下来... </label>
		<span>
			<input id="element_1_1" name="element_1" class="element radio" type="radio" value="1" />
<label class="choice" for="element_1_1">图床(新浪版)</label>
<input id="element_1_2" name="element_1" class="element radio" type="radio" value="2" />
<label class="choice" for="element_1_2">图床(网易版)</label>
<input id="element_1_3" name="element_1" class="element radio" type="radio" value="3" />
<label class="choice" for="element_1_3">图床(搜狐版)</label>
<input id="element_1_3" name="element_1" class="element radio" type="radio" value="4" />
<label class="choice" for="element_1_3">图床(腾讯版)</label>

		</span> 
		</li>
			
					<li class="buttons">
			    <input type="hidden" name="form_id" value="351086" />
			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="进入" />
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