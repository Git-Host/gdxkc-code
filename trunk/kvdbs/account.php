<?php
# This file is to start the web disk, attention: it will delete permanently
# all the files exist. And login, logout, reset service.
#
# name         ->  ('T' - time, 'A' - public, 'R' - regular, 'D' - directory) 
# regular file ==  [('N' - name, 'T' - time, 'A' - access, 'S' - size)]
# directory    ==  [('N' - name, 'T' - time, 'A' - access)]

include "base.php"; session_start(); $_SESSION['error'] == '';

$kv = kv_init(); $account = unserialize(kv_get($kv, ':account'));

$email = $_REQUEST['email'];
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];
$verified = false;

switch ($_REQUEST['action']) {
case 'start':
	check_field('email', 'username', 'password');
	if ($account) exit_print('KVDB-MemStorage has been started.');
	
	$account = array('email' => $email, 'username' => $username, 'password' => md5($password));
	kv_set($kv, ':account', serialize($account));
	
	$temp = array();
	kv_set($kv, ':temp', serialize($temp));

	$root = array('T' => time(), 'A' => false, 'R' => array(), 'D' => array());
	kv_set($kv, '/', serialize($root));
	
	$_SESSION['auth'] = 'OK';
	exit_redirect('home.php');
	break;
case 'recover':
	switch($_REQUEST['period']) {
	case 'verify':
		check_field('email');
		if ($email == $account['email']) {
			$account['code'] = rand_str(32);
			$sm = new SaeMail();
			
			$re = $sm->quickSend($email, 'Account Recovery',
				'Are your sure to recover your account? Yes, visit '. $_SERVER['SCRIPT_URI']
					. '?action=recover&code=' . $account['code'], '*****', '*****');
			$kv->set(':account', serialize($account));
			
			if ($re)
				exit_print('A verify link has been seen to your Email.');
			else
				exit_print($sm->errmsg());
		} else {
			exit_print('Incorrect email. <a href="account.php?action=recover">Reverify</a>?');
		}
		break;
	case 'confirm':
		check_field('email', 'username', 'password', 'code');
		if ($_REQUEST['code'] != $account['code']) {
			exit_print('Incorrect code. <a href="account.php?action=recover">Reverify</a>?');
		} else {
			unset($account['code']);
			$account['email'] = $email;
			$account['username'] = $username;
			$account['password'] = md5($password);
			$kv->set(':account', serialize($account));
			$_SESSION['auth'] = 'OK';
			exit_print("It's OK now. <a href=\"home.php\">Back home</a>?");
		}
		break;
	default:
		if (isset($_REQUEST['code'])) {
			if ($_REQUEST['code'] != $account['code']) {
				exit_print('Incorrect code. <a href="account.php?action=recover">Reverify</a>?');
			} else {
				$verified = true;
			}
		}
	}
	break;
case 'clear':
	/* while ($ret = $kv->pkrget('', 100)) {
		foreach($ret as $key => $value)
			$kv->delete($key);
		
		if (count($ret) < 100) break;
	}*/
	
	exit_print('To clear database, just disable <a href="http://sae.sina.com.cn/?m=kv&app_id='
		. $_SERVER['HTTP_APPNAME'] . '&ver=' . $_SERVER['HTTP_APPVERSION'] . '">KVDB service</a>!');
	break;
case 'newpwd':
	if ($_SESSION['auth'] != 'OK') {
		$_SESSION['msg'] = 'You are not authorized!';
		exit_redirect('index.php');
	}
	check_field('password', 'newpwd', 'newpwd2');
	if (md5($password) == $account['password']) {
		if ($_REQUEST['newpwd'] == $_REQUEST['newpwd2']) {
			$account['password'] = md5($_REQUEST['newpwd']);
			kv_set($kv, ':account', serialize($account));
			$_SESSION['msg'] = 'Password has been changed.';
			unset($_SESSION['auth']);
			exit_redirect('index.php');
		} else {
			exit_print("Confirm doesn't match. <a href=\"home.php\">Back Home</a>?");	
		}
	} else {
		exit_print("Password is wrong. <a href=\"home.php\">Back Home</a>?");	
	}
	break;
case 'login':
	check_field('username', 'password');
	if ($username != $account['username'] || md5($password) != $account['password']) {
		$_SESSION['msg'] = 'User name or password wrong!';
		exit_redirect('index.php');
	}
	$_SESSION['auth'] = 'OK';
	exit_redirect('home.php');
	break;	
case 'logout':
	$_SESSION['auth'] = '';
	exit_redirect('index.php');
	break;
default:
	exit_print('Action unkown');
	break;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>KVDB-MemStorage - Use MemStorage From SaeKVDB To Save Files</title>
<style type="text/css">
body { font: 13px verdana, tahoma, sans-serif; color: #333; }
a { text-decoration: none; color:  #2323E4; }
form { width: 235px; margin: 140px auto; padding: 20px 13px;  border: 1px #ddd solid; background: #f7f7f7; margin-bottom: 40px;}
li { list-style: none; margin-bottom: 28px; clear: both; padding: 0 8px; }
li input { float: right; margin-right: 6px; margin-top: -3px; font-family: verdana, tahoma, sans-serif; }
#title { text-align: center; font-size: 16px; color: #2db033; margin-top: 10px; }
#msg { text-align: center; font-size: 12px; color:#D00; margin: 0 15px 20px 7px; border: 1px dotted #999; padding: 4px 0; }
#submit{ margin: 28px 0 8px 0; }
#submit input { width: 82px; height: 23px; border: 0px; background-color:#3cb43c; color: #fff; border-radius: 2px; margin-top: -4px; }
input[type=text], input[type=password] { width: 130px; height: 19px; border: 1px #ccc solid; background-color: #faffbd; }
.hidden { display: none; }
.clear { clear: both; }
</style>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
function validate() {
	if ($('#email').val() == "") {
		$('#email').focus();
		$('#msg').html('E-mail is empty').show();
	} else if ($('#email').val() && !/.+@.+\.[a-zA-Z]{2,4}$/.test($('#email').val())) {
		$('#email').focus();
		$('#msg').html('E-mail format illegal').show();
	} else if ($('#username').val() == "") {
		$('#username').focus();
		$('#msg').html('User name is empty').show();
	} else if ($('#password').val() == "") {
		$('#password').focus();
		$('#msg').html('Password is empty').show();
	} else {
		return true;
	}
	return false;
}

$(document).ready(function() {
	<?php if ($_SESSION['msg']) echo '$("#msg").html("' . $_SESSION['msg'] . '").show();'; unset($_SESSION['msg']); ?>
});
</script>
</head>

<body onkeydown="$('#msg').hide();">
	<form action="account.php" method="post" onSubmit="return validate();">
		<li id="title"><?php echo $verified ? 'Reset Account' : 'Account Recovery' ?></li>
		<li>E-mail: <input id="email" type="text" name="email" /></li>
		<?php if ($verified) echo '<li>Name: <input id="username" type="text" name="username" /></li>'
			. '<li>Password: <input id="password" type="password" name="password" /></li>' ?>
		<input type="hidden" name="action" value="recover" />
		<input type="hidden" name="period" value="<?php echo $verified ? 'confirm' : 'verify' ?>" />
		<input type="hidden" name="code" value="<?php echo $_REQUEST['code'] ?>" />
		<li id="msg" class="hidden"></div>
		<li id="submit"><input type="submit" value="submit" /><div class="clear"></div></li>
	</form>
</body>
</html>