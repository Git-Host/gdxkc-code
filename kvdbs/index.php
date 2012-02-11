<?php
#this file is the entrance for user, it first check the authority status and detect
#whether to init kvdb or just login(msg may be received from session).

include 'base.php'; session_start(); $_SESSION['error'] == '';
if ($_SESSION['auth'] == 'OK') exit_redirect('home.php');

$kv = kv_init();
$type = kv_get($kv, ':account'); /* wheather the KBDB has been initilized */
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
		<img src="images/new.png" width="60" height="60" style="position: absolute; margin-top: -22px; margin-left: -15px;" />
		<li id="title">Welcome to KVDB-MemStorage</li>
		<?php if (!$type) echo '<li>E-mail: <input id="email" type="text" name="email" /></li>' ?>
		<li>Name: <input id="username" type="text" name="username" /></li>
		<li>Password: <input id="password" type="password" name="password" /></li>
		<input type="hidden" name="action" value="<?php echo $type ? 'login' : 'start' ?>" />
		<li id="msg" class="hidden"></div>
		<li id="submit"><?php if ($type) echo '<a href="account.php?action=recover">Recovery</a><input type="submit" value="Login" />';
			  else echo '<input type="submit" value="Start" />' ?><div class="clear"></div></li>
	</form>
</body>
</html>