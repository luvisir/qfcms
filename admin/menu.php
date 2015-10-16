<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>CMS发布系统 </title>
<meta http-equiv="content-type" content="text/html;charset=GB2312" />
<meta name="Generator" content="EditPlus" />
<meta name="Author" content="CMS" />
<meta name="Keywords" content="CMS," />
<link rel="stylesheet" href="../style/global.css" type="text/css" />
<script language="javascript" src="../javascript/function.js"></script>
</head>

<body>
<div id="menu-box">
<div class="menu">
	<div class="menu-head">
		<a href="javascript:void(null)" onclick="showMenu('menu1',this)">常规设置</a>
	</div>
	<ul id="menu1">
		<li><a href="main.php?action=sysInfo" target="main">系统信息</a></li>
		<li><a href="main.php?action=baseSet" target="main">基本设置</a></li>
		<li><a href="main.php?action=setPwd" target="main">密码修改</a></li>
		<li><a href="main.php?action=logout" target="_top">退出系统</a></li>
	</ul>
</div>
<div class="menu">
	<div class="menu-head">
		<a href="javascript:void(null)" onclick="showMenu('menu2',this)">文章管理</a>
	</div>
	<ul id="menu2">
		<li><a href="main.php?action=addArticle" target="main">添加文章</a></li>
		<li><a href="main.php?action=listArticle" target="main">管理文章</a></li>
		<li><a href="main.php?action=addCat" target="main">添加分类</a></li>
		<li><a href="main.php?action=listCat" target="main">管理分类</a></li>
		<li><a href="main.php?action=updateArticle" target="main">批量更新</a></li>
	</ul>
</div>
<div class="menu">
	<div class="menu-head">
		<a href="javascript:void(null)" onclick="showMenu('menu3',this)">相册管理</a>
	</div>
	<ul id="menu3">
		<li><a href="main.php?action=addPic" target="main">添加图片</a></li>
		<li><a href="main.php?action=listPic" target="main">管理图片</a></li>
		<li><a href="main.php?action=addAlbum" target="main">添加相册</a></li>
		<li><a href="main.php?action=listAlbum" target="main">管理相册</a></li>
	</ul>
</div>
<div class="menu">
	<div class="menu-head">
		<a href="javascript:void(null)" onclick="showMenu('menu4',this)">帐户管理</a>
	</div>
	<ul id="menu4">
		<li><a href="main.php?action=addUser" target="main">添加帐户</a></li>
		<li><a href="main.php?action=listUser" target="main">管理帐户</a></li>
	</ul>
</div>
</div>
</body>
</html>
