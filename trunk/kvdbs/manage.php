<?php
# This file is the file manager, support many functions.

include 'base.php'; check_auth();

$kv = kv_init();

check_field('dir'); $dir = $_REQUEST['dir'];

$node = unserialize(kv_get($kv, $dir)) OR exit_print('No such directory.');

switch($_REQUEST['action']) {
case 'newdir':
	check_field('name'); $name = $_REQUEST['name'];
	
	foreach($node['R'] as $f)
		if (!strcasecmp($f['N'], $name)) exit_print("Already exists. <a href=\"home.php?path=" . $dir. "\">Back home</a>?");
	foreach($node['D'] as $f)
		if (!strcasecmp($f['N'], $name)) exit_print("Already exists. <a href=\"home.php?path=" . $dir. "\">Back home</a>?");
	
	$new = array('T' => time(), 'A' => $node['A'], 'R' => array(), 'D' => array());
	kv_set($kv, $dir . $name. '/', serialize($new));
	
	$node['D'][] = array('N' => $name, 'T' => $new['T'], 'A' => $new['A'], 'I' => uniqid());
	usort($node['D'], item_cmp);
	$node['D'] = array_values($node['D']);
	kv_set($kv, $dir, serialize($node));
	
	exit_redirect('home.php?path=' . $dir);
	break;
case 'delete':
	//check_field('name');
	$name = $_REQUEST['name'];
	
	$done = false;
	for (reset($node['R']); $it = each($node['R']); ) {
		foreach ($name as $n) {
			if (!strcasecmp($it[1]['N'], $n)) {
				for ($i = 0; $i * 3145728 < $it[1]['S']; $i++)
					$kv->delete($it[1]['I'] . ':' . $i);
				unset($node['R'][$it[0]]);
				
				$done = true; // kv_set($kv, $dir, serialize($node));
				// exit_redirect('home.php?path=' . $dir);
			}
		}
	}

	for (reset($node['D']); $it = each($node['D']); ) {
		foreach ($name as $n) {
			if (!strcasecmp($it[1]['N'], $n)) {
				$list = array($dir . $it[1]['N'] . '/');
				while($a = array_shift($list)) {
					$b = unserialize(kv_get($kv, $a));
					foreach($b['R'] as $c) {
						for ($i = 0; $i * 3145728 < $c['S']; $i++)
							$kv->delete($c['I'] . ':' . $i);
					}
					foreach($b['D'] as $d) {
						$list[] = $a . $d['N'] . '/';
					}
					kv_delete($kv, $a);
				}
				unset($node['D'][$it[0]]);
				
				$done = true; // kv_set($kv, $dir, serialize($node));
				// exit_redirect('home.php?path=' . $dir);
			}
		}
	}

	if ($done) {
		kv_set($kv, $dir, serialize($node));
		exit();
	} else {
		exit_json(1, 'No such file or directory.');
	}
	break;
case 'rename':
	check_field('name', 'value');
	$name = $_REQUEST['name'];
	$value = $_REQUEST['value'];
	
	foreach($node['R'] as $f)
		if (!strcasecmp($f['N'], $value)) exit_print("New name already exists. <a href=\"home.php?path=" . $dir. "\">Back home</a>?");
	foreach($node['D'] as $f)
		if (!strcasecmp($f['N'], $value)) exit_print("New name already exists. <a href=\"home.php?path=" . $dir. "\">Back home</a>?");
	
	for (reset($node['R']); $it = each($node['R']); ) {
		if (!strcasecmp($it[1]['N'], $name)) {
			/* for ($i = 0; $i * 3145728 < $it[1]['S']; $i++) {
				$tmp = kv_get($kv, $dir . $name . ':' . $i);
				kv_delete($kv, $dir . $name . ':' . $i);
				kv_set($kv, $dir . $value . ':' . $i, $tmp);
			} */
			
			$node['R'][] = array('N' => $value, 'T' => time(), 'A' => $it[1]['A'], 'S' => $it[1]['S'], 'I' =>  $it[1]['I']);
			unset($node['R'][$it[0]]);
			usort($node['R'], item_cmp);
			kv_set($kv, $dir, serialize($node));
			
			exit_redirect('home.php?path=' . $dir);
		}
	}
	
	/* ERROR */
	for (reset($node['D']); $it = each($node['D']); ) {
		if (!strcasecmp($it[1]['N'], $name)) {
			$tmp = unserialize(kv_get($kv, $dir . $name . '/'));
			kv_delete($kv, $dir . $name . '/');
			$tmp['T'] = time();
			kv_set($kv, $dir . $value . '/', serialize($tmp));
			
			$node['D'][] = array('N' => $value, 'T' => $tmp['T'], 'A' => $tmp['A']);
			unset($node['D'][$it[0]]);
			usort($node['D'], item_cmp);
			kv_set($kv, $dir, serialize($node));
			
			exit_redirect('home.php?path=' . $dir);
		}
	}
	
	exit_print('No such file or directory.');
	break;
case 'share':
	$name = $_REQUEST['name'];
	
	$zz = array();
	
	foreach($name as $n) {
		
		foreach ($node['R'] as $k => $v) {
			if (!strcasecmp($v['N'], $n)) {
				$node['R'][$k]['A'] = !$node['R'][$k]['A'];
				
				if ($node['R'][$k]['A'])
					$zz[] = $v;
				break;
			}
		}
		
		/*foreach ($node['D'] as $k => $v) {
			if (!strcasecmp($v['N'], $n)) {
				$node['D'][$k]['A'] = !$node['D'][$k]['A'];
				$tmp = unserialize(kv_get($kv, $dir . $v['N'] . '/'));
				$tmp['A'] = !$tmp['A'];
				kv_set($kv, $dir . $v['N'] . '/', serialize($tmp));
				
				if ($node['D'][$k]['A'])
					$zz[] = $v['N'];
				break;
			}
		}*/
	}
	
	kv_set($kv, $dir, serialize($node));
	
	if(count($zz) == 0)
		echo 'No item is sharing!';
	echo '<ul id="shared">';
	foreach($zz as $z) {
		echo '<li class="clear">' . $z['N'] . '<span>/down.php?id=' . $z['I']
			. "<div onclick=\"clipboard('" . $z['I'] . "');\"" . ' class="clipboard"></div></span></li>';
	}
	echo '</ul>';
	
	break;
case 'move':
	$name = $_REQUEST['name'];
	$dest = $_REQUEST['dest'];
	$node2 = unserialize(kv_get($kv, $dest)) OR exit_print("No such directory");

	var_dump($node2);
	foreach($name as $n) {
		foreach ($node['R'] as $k => $v) {
			if (!strcasecmp($v['N'], $n)) {
				$node2['R'][] = $v;
				unset($node['R'][$k]);
				break;
			}
		}
		
		/* ERROR */
		foreach ($node['D'] as $k => $v) {
			if (!strcasecmp($v['N'], $n)) {
				$node2['D'][] = $v;
				$tmp = kv_get($kv, $dir . $v['N'] . '/');
				kv_set($kv, $dest . $v['N'] . '/', $tmp);
				kv_delete($kv, $dir . $v['N'] . '/');
				unset($node['D'][$k]);
				break;
			}
		}
	}
	
	kv_set($kv, $dir, serialize($node));
	kv_set($kv, $dest, serialize($node2));
	break;
case 'list':
	echo '<ul ' . ($dir == '/' ? 'class="root"' : '') . ' id="' . urlencode($dir) . '">';
	foreach($node['D'] as $f)
		echo '<li><span id="' . urlencode($dir . $f['N'] . '/') . '" class="expand"></span><input type="checkbox" />' . $f['N'] . '</li>';
	echo '</ul>';
	break;
default:
	exit_print('Unkown action.');
	break;
}
?>