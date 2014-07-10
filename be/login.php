<?
session_start();
unset($_SESSION['user_id']);
unset($_SESSION['user_data']);
require_once(dirname(__FILE__)."/../config/config.php");

require_once(dirname(__FILE__)."/../lib/db.php");
require_once(dirname(__FILE__)."/../lib/be/users.php");
Users::logout();

//echo $_SESSION['userID'];

function login($username,$pass) {
	$db=getDB();
	if(empty($pass)||empty($username)) {
		return 0;
	}
	$result=$db->getAssoc("select * from users where active=1 and username='{$username}' and `password`='{$pass}'");
	
	if(count($result)==1) {
		foreach ($result as $key=>$value) {
			$_SESSION['user_id']=$key;
			$_SESSION['user_data']=$value;
			setcookie('username',$username);
			return true;
		}
	}
	return 0;
}

if($_POST[do_login])
{
	
	$logged = login($_POST[username], $_POST[userpassword]);
	
	if($logged)
	{
		Users::loadUserData($logged);
		$_COOKIE['username']=$_POST['username'];
		header("Location: /be/indexMenu.php");
		exit;
	}
	

}

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="/be/lib.css">
	<script src="/lib.js" type="text/javascript"></script>
</head>
<body topmargin="0" LeftMargin="0" rightmargin="0" marginwidth="0" marginheight="0" bgcolor="white" text="#000000" link="#336699" alink="#660000" vlink="#336699" onLoad="document.loginForm.username.focus();">
<a name="top"></a>


<form method=post name="loginForm" action="login.php">
<input type=hidden name="r" value="<?=$r?>">
<table border="0" width="100%" height="100%"  cellpadding="10" cellspacing="5">
<tr>
<td width="100%" height="100%" align="center" valign="middle">

<table border="0" cellspacing="5" cellpadding="15" class="test1" align="center" vAlign='middle'  >
<tr><td>
<b>User&nbsp;Name:</b></td><td><input type="text" name="username" size="15" id=LoginInput value="<?=$_COOKIE['username']?>">
</td></tr>
<tr><td>
<b>Password:</b></td><td><input type="password" name="userpassword" size="15" id=LoginInput>
</td></tr>
	<tr valign=bottom>
		<td></td>
<!--		<td><a href="/?pid=23&spid=4">forgotten<br>password?</a></td>-->
		<td align="right"><input type="submit" name="do_login" value="Login"></td>
	</tr>
	</table>
</form>

</td></tr>
</table>

</td>
</tr>
</table>


</body>
</html>