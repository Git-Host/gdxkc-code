<?php
$kv = new SaeKVClient();
$kv->init();

if ($id = $_REQUEST['id']) {
	header('Content-Type: application/octet-stream');
} else {
	exit('ID not specified.');
}

for ($i = 0; ; $i++) {
	$ret = $kv->get($id . ':' . $i);
	
	if ($ret == false)
		exit();
	else
		echo $ret;
}
?>