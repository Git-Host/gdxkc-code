<?php
# This is the server side for Flash Uploader. it supports limited simultaneous uploads.
# It contains 3 stage: begin chunk finish. content-type is ignored.

include 'base.php'; check_auth();

$MAX_TEMP_NUM = 5;

check_field('action', 'dir', 'name');
$kv = kv_init();
$temp = unserialize(kv_get($kv, ':temp'));

$dir = $_REQUEST['dir'];
$name = unescape($_REQUEST['name']);

switch ($_REQUEST['action']) {
case "start":
	check_field('size', 'time');
	$size = (int)$_REQUEST['size'];
	$time = (int)$_REQUEST['time'];
	$node = unserialize(kv_get($kv, $dir)) OR exit_json(1, 'No such directory.');
	
	foreach($node['R'] as $f)
		if (!strcasecmp($f['N'], $name)) exit_json(2, "Already exists.");
	foreach($node['D'] as $f)
		if (!strcasecmp($f['N'], $name)) exit_json(2, "Already exists.");
	
	for (reset($temp); $it = each($temp); ) {
		$f = $it[1];
		if ($f['D'] == $dir && $f['N'] == $name) {
			if ($f['S'] == $size && $f['T'] == $time) {
				echo json_encode(array('code' => 0, 'index' => $f['X'] + 1));
				exit();
			} else {
				unset($temp[$it[0]]);
			}
		}
	}
	
	$temp[] =  array('D' => $dir, 'N' => $name, 'S' => $size, 'T' => $time, 'I' => uniqid(), 'X' => -1);
	echo json_encode(array('code' => 0, 'index' => 0));
	
	/* clear the first one */
	if (count($temp) > 10) {
		$first = array_shift($temp);
		for ($i = 0; $i <= $first['X']; $i++)
			$kv->delete($first['I'] . ':' . $i);
	}
	
	kv_set($kv, ':temp', serialize($temp));
	exit();
	
	break;
case "chunk":
	check_field('index');
	$index = (int)$_REQUEST['index'];
	
	$raw = file_get_contents('php://input');
	for (reset($temp); $it = each($temp); ) {
		$f = $it[1];
		if ($f['D'] == $dir && $f['N'] == $name) {
			if ($index != $f['X'] + 1) {
				exit_json(3, 'Unwanted index.');
			} else if (strlen($raw) != ($f['S'] - $index * 3145728 >= 3145728 ? 3145728 : $f['S'] % 3145728)) {
				exit_json(4, 'Broken chunk.');
			} else {
				if (!kv_set($kv, $f['I'] . ':' . $index, $raw)) {
#debuging					
					$s = new SaeStorage();
					$s->write( 's3' , 'error' , $raw );
					$s->getUrl( 's3' , 'error' );
					exit_json(8, $kv->errmsg() . ' - ' . $s->getUrl( 's3' , 'error' ));				
				
				}
				$temp[$it[0]]['X'] = $index;
				echo json_encode(array('code' => 0, 'size' => strlen($raw)));
			}
		}
	}
	
	kv_set($kv, ':temp', serialize($temp));
	exit();

	break;	
case "finish":
	for (reset($temp); $it = each($temp); ) {
		$f = $it[1];
		if ($f['D'] == $dir && $f['N'] == $name) {
			$num = (int)($f['S'] / 3145728) + ($f['S'] % 3145728 == 0 ? 0 : 1);
			if ( $f['X'] != $num - 1)
				exit_json(5, 'Not complete. ' . $f['X'] . '/' . ($num - 1));
			
			$node = unserialize(kv_get($kv, $dir));
			$node['R'][] = array('N' => $f['N'], 'T' => time(), 'A' => $node['A'], 'S' => $f['S'], 'I' => $f['I']);
			kv_set($kv, $dir, serialize($node));
			
			unset($temp[$it[0]]);
			kv_set($kv, ':temp', serialize($temp));
			
			exit_json(0, 'OK');
		}
	}
	
	exit_json(6, 'No such item');
	break;
default:
	exit_json(7, 'Unkown action.');
	break;
}
?>