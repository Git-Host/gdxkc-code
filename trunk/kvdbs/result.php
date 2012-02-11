<?php
#this file is used to show error message received from session.
session_start(); 
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>KVDB-MemStorage - Use MemStorage From SaeKVDB To Save Files</title>
<style type="text/css">
body { font: 13px verdana, tahoma, sans-serif; color: #333; }
a { text-decoration: none; color:  #2323E4; }
form { width: 300px; margin: 140px auto; padding: 20px 13px;  border: 1px #ddd solid; background: #f7f7f7; margin-bottom: 40px;}
li { list-style: none; margin-bottom: 28px; clear: both; padding: 0 8px; }
li input { float: right; margin-right: 6px; margin-top: -3px; font-family: verdana, tahoma, sans-serif; }
#title { text-align: center; font-size: 16px; color: #2db033; margin-top: 10px; }
#msg { text-align: center; font-size: 12px; color:#D00; margin-bottom: 20px; }
#submit{ margin-bottom: 8px; }
#submit input { width: 82px; height: 23px; border: 0px; background-color:#3cb43c; color: #fff; border-radius: 2px; }
input[type=text], input[type=password] {
	width: 130px; height: 19px; border: 1px #ccc solid; background-color: #faffbd; }
.hidden { display: none; }
.clear { clear: both; }
</style>
</head>

<body>
<form>
<?php echo $_SESSION['error']; /*unset($_SESSION['error'])*/; ?>
</form>
</body>
</html>