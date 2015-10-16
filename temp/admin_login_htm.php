<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>CMS发布系统 </title>
<meta http-equiv="content-type" content="text/html;charset=GB2312" />
<meta name="Generator" content="EditPlus" />
<meta name="Author" content="CMS" />
<meta name="Keywords" content="CMS" />
<link rel="stylesheet" href="../style/global.css" type="text/css" />
</head>

<body class="center" onload="document.getElementById('login-form').uname.focus()">
<div id="login-box">
<div class="main-box">
	<div class="head-dark-box">
		CMS发布系统-管理员登录
	</div>
	<div class="<?php
echo $_obj['className'];
?>
">
		<?php
echo $_obj['message'];
?>

	</div>
	<form method="post" action="login.php" id="login-form">
		<table cellspacing="0">
			<tr class="dark-row">
				<td class="width-24percent">用户名</td>
				<td><input type="text" class="text-box" size="15" name="uname" /></td>
			</tr>
			<tr class="light-row">
				<td>密&nbsp;&nbsp;码</td>
				<td><input type="password" class="text-box" size="15" name="pwd" /></td>
			</tr>
			<tr class="dark-row">
				<td><input type="hidden" name="action" value="login" /> </td>
				<td><input type="submit" class="button" value="登录系统" /></td>
			</tr>
		</table>
	</form>
</div>
</div>
</body>
</html>
