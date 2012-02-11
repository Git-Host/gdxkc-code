<?php
# Used as a wrapper for SaeKVClient functions, and some utilities.

$kv_search = array("\f", "\n", "\r", "\t", "\v", " ");
$kv_replace = array('\f', '\n', '\r', '\t', '\v', '\s');

function kv_init() {
	$kv = new SaeKVClient();
	if (!$kv->init()) {
		exit_print('You need to enable <a href="http://sae.sina.com.cn/?m=kv&app_id='
			. $_SERVER['HTTP_APPNAME'] . '&ver=' . $_SERVER['HTTP_APPVERSION']
			. '">KVDB service</a>!');
	}
	return $kv;
}

function kv_replace($key) {
	global $kv_search, $kv_replace;
	
	return str_replace($kv_search, $kv_replace, $key);
}

function kv_set($kv, $key, $value) {
	return $kv->set(kv_replace($key), $value);
}

function kv_get($kv, $key) {
	return $kv->get(kv_replace($key));
}

function kv_delete($kv, $key) {
	return $kv->delete(kv_replace($key));
}

/* start a session and check authority status */
function check_auth() {
	session_start();
	$_SESSION['error'] == '';
	if ($_SESSION['auth'] != 'OK') {
		$_SESSION['msg'] = 'You are not authorized!';
		exit_redirect('index.php');
	}
}

function check_field() {
	$fields = func_get_args();
	foreach($fields as $f) {
		if ($_REQUEST[$f] == null || trim($_REQUEST[$f]) == '')
			exit_print(ucfirst($f) . ' not specified');
	}
}

function exit_json($code, $msg) {
	echo json_encode(array('code' => $code, 'msg' => $msg));
	exit();
}

function exit_redirect($page) {
	header('HTTP/1.1 302 Temporarily Moved');
	header ('Location: ' . $page);
	exit();
}

function exit_print($error) {
	$_SESSION['error'] = $error;
	exit_redirect('result.php');
}

function rand_str($len) {
	$chars = array(
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
		'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
		'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
		'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
		'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2',
		'3', '4', '5', '6', '7', '8', '9'
	);
	$nchars = count($chars) - 1;
	shuffle($chars);
	$output = '';
	for ($i=0; $i<$len; $i++) {
		$output .= $chars[mt_rand(0, $nchars)];
	}
	return $output;
}

function item_cmp($a, $b) {
	return strcasecmp($a['N'], $b['N']);	
}

function escape($str) {
  preg_match_all("/[/x80-/xff].|[/x01-/x7f]+/",$str,$r);
  $ar = $r[0];
  foreach($ar as $k=>$v) {
    if(ord($v[0]) < 128)
      $ar[$k] = rawurlencode($v);
    else
      $ar[$k] = "%u".bin2hex(iconv("GB2312","UCS-2",$v));
  }
  return join("",$ar);
}

function unicode2utf8($c) {
	$str="";
	if ($c < 0x80) {
		$str.=chr($c);
	} else if ($c < 0x800) {
		$str.=chr(0xc0 | $c>>6);
		$str.=chr(0x80 | $c & 0x3f);
	} else if ($c < 0x10000) {
		$str.=chr(0xe0 | $c>>12);
		$str.=chr(0x80 | $c>>6 & 0x3f);
		$str.=chr(0x80 | $c & 0x3f);
	} else if ($c < 0x200000) {
		$str.=chr(0xf0 | $c>>18);
		$str.=chr(0x80 | $c>>12 & 0x3f);
		$str.=chr(0x80 | $c>>6 & 0x3f);
		$str.=chr(0x80 | $c & 0x3f);
	}
	return $str; 
}

function unescape($source) { 
	$target = "";
	
	for ($pos = 0; $pos < strlen($source); ) { 
		$char = substr($source, $pos++, 1);
		
		if ($char == '%') {
			$next = substr($source, $pos++, 1);
			if ( $next == 'u') {
				$codestr = substr($source, $pos, 4);
				$unicode = hexdec ($codestr);
				$target .= unicode2utf8($unicode);
				$pos += 4;
			} else {
				$target .= $char;
				$target .= $next;
			}
		} else {
			$target .= $char;
		}
	}
	return $target;
}
?>