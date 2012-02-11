<?php
#This is the main page that user view and manage his or her files.

include 'base.php'; check_auth();

$path = '/'; $dir = '/'; $name = '';
if (isset($_REQUEST['path'])) {
	$path = $_REQUEST['path'];
	if ($path[0] != '/') exit_print('Incorrect path.');
	$dir = substr($path, 0, strrpos($path, '/') + 1);
	$name = substr(strrchr($path, '/'), 1);
}

$kv = kv_init();
$node = unserialize(kv_get($kv, $dir)) OR exit_print('No such item.');

if ($name != '') {
	foreach($node['R'] as $f) {
		if (!strcasecmp($f['N'], $name)) {
			header('Content-Type: application/octet-stream');
			header('Content-Length: ' . $f['S']);
			header('Content-Disposition: attachment; filename=' . $f['N']);
			for ($i = 0; $i * 3145728 < $f['S']; $i++)
				echo kv_get($kv, $f['I'] . ':' . $i);//echo kv_get($kv, $path . ':' . $i);
			exit();
		}
	}
	/* without much usage */
	foreach($node['D'] as $f) {
		if (!strcasecmp($f['N'], $name))
			exit_redirect('home.php?path='. $path . '/');
	}
	exit_print('No such item. ' . $name);
}?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>KVDB-MemStorage - Use MemStorage From SaeKVDB To Save Files</title>
<link rel="stylesheet" type="text/css" href="css/view.css" />
<link type="image/x-icon" href="images/favicon.ico" rel="shortcut icon">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/swfobject.js"></script>
<script type="text/javascript" src="js/uploader.js"></script>
<script type="text/javascript" src="js/main.js"></script>
<script type="text/javascript">
var dir = "<?php echo $dir ?>";
var host = "<?php echo $_SERVER['HTTP_HOST'] ?>";
</script>
</head>
<body>
<div class="main">
	<div class="head clear">
		<div id="title">Welcome to KVDB-MemStorage!</div>
		<div id="new">
			<div id="newdir">
				<form action="manage.php?action=newdir" method="post">
					<input type="hidden" name="dir" value="<?php echo $dir ?>" />
					<input id="ipt_newdir" type="text" name="name" />
					<div class="ico_newdir"></div>
				</form>
			</div>
			<div id="upload">
				<form action="upload.php" method="post" enctype="multipart/form-data">
					<input type="hidden" name="dir" value="<?php echo $dir ?>" />
					<div id="filename"><span>Browse</span></div>
					<div id="uploader"><input type="file" name="file"/></div>
					<div class="ico_upload"></div>
				</form>
			</div>
		</div>
	</div>
	<div class="body">
		<ul id="list">
<?php
$counter = 0;

foreach ($node['D'] as $f) {
	echo '<li type="folder" id="' . $f['N'] . '" class="' . (++$counter % 2 == 0 ? 'even' : '') . '"> ';
	echo '<div class="ico_select"></div> ';
	echo $f['A'] ? '<div class="ico_folder2"></div> ' : '<div class="ico_folder"></div> ';
	echo '<span class="name"><a href="home.php?path=' . $path . $f['N'] . '/">' . $f['N'] . '</a></span> ';
	echo '<span class="size_time"> ';
	echo '<span class="size">FOLDER</span> ';
	echo '<span class="time">' . date('Y-m-d H:i', $f['T']) . '</span> ';
	echo '</span></li> ';
}

foreach ($node['R'] as $f) {
	echo '<li type="file" id="' . $f['N'] . '" class="' . (++$counter % 2 == 0 ? 'even' : '') . '"> ';
	echo '<div class="ico_select"></div> ';
	echo $f['A'] ? '<div class="ico_file2"></div> ' : '<div class="ico_file"></div> ';
	echo '<span class="name"><a href="home.php?path=' . $path . $f['N'] . '">' . $f['N'] . '</a></span> ';
	echo '<span class="size_time"> ';
	echo '<span class="size">' . ($f['S'] >= 1048576 ? number_format($f['S'] / 1048576, 1) .' MB' : ($f['S'] >= 1024 ? (int)($f['S'] / 1024) . ' KB' :  $f['S'] . '&nbsp;&nbsp;&nbsp;B')) . '</span> ';
	echo '<span class="time">' . date('Y-m-d H:i', $f['T']) . '</span> ';
	echo '</span></li> ';
}
?>
		</ul>
		<div id="toolbar" class="clear">
			<div class="tb1">
				<div id="tb_selectall"><span></span></div>
				<div id="tb_copy" class="disabled" style="display: none;"><span></span></div>
				<div id="tb_move" class="disabled" style="display: none;"><span></span></div>
				<div id="tb_delete" class="disabled"><span></span></div>
				<div id="tb_rename" class="disabled"><span></span></div>
				<div id="tb_share" class="disabled"><span></span></div>
			</div>
			<div class="tb2">
<?php
	if ($dir != '/') {
		$str = substr($dir, 0, strrpos(substr($dir, 0, strlen($dir) - 1), '/') + 1);
		echo '<a href="home.php?path=' . $str . '">Upper</a> | ';
	} else {
		echo '<a href="#" class="gray">Upper</a> | ';
	}
?>
				<a href="#" onClick="return show_account();">Account</a> |
				<a href="account.php?action=logout" class="last">Logout</a>
			</div>
		</div>
	</div>
	<div class="foot"> 
	</div>
</div>

<form action="" method="POST">
	<input type="hidden" name="dir" value="/" />
	<input type="hidden" name="name" value="" />
</form>

<div id="overlay"></div>
<div id="dialog">
	<div id="dialog_bar">
		<span id="dialog_title">KVDB-MemStorage uploader</span>
		<div id="dialog_close" onclick="close_dialog();">X</div>
	</div>
	<div id="dialog_content">
		
	</div>
</div>

</body>
</html>