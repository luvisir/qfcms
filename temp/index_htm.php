<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php
echo $_obj['appName'];
?>
</title>
<meta http-equiv="content-type" content="text/html;charset=GB2312" />
<meta name="Generator" content="EditPlus" />
<meta name="Author" content="CMS" />
<meta name="Keywords" content="CMS" />
<link rel="stylesheet" href="<?php
echo $_obj['stylePath'];
?>
/main.css" type="text/css" />
</head>

<body>
<!-- 头部分 -->
<div id="head">
	<div id='logo'><img src="<?php
echo $_obj['stylePath'];
?>
/images/logo.gif"/></div>
	<div id='text'>CMS发布系统<br/>version 1.0</div>
</div>
<!-- 导航部分 -->
<div id="navigator">
	<ul>
		<li><a href="index.php">首 页</a></li>
	<?php
if (!empty($_obj['navList'])){
if (!is_array($_obj['navList']))
$_obj['navList']=array(array('navList'=>$_obj['navList']));
$_tmp_arr_keys=array_keys($_obj['navList']);
if ($_tmp_arr_keys[0]!='0')
$_obj['navList']=array(0=>$_obj['navList']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['navList'] as $rowcnt=>$navList) {
$navList['ROWCNT']=$rowcnt;
$navList['ALTROW']=$rowcnt%2;
$navList['ROWBIT']=$rowcnt%2;
$_obj=&$navList;
?>
		<li><a href="listArticle.php?catPath=<?php
echo $_obj['absPath'];
?>
"><?php
echo $_obj['catTitle'];
?>
</a></li>
	<?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
	</ul>
</div>
<!-- banner 开始 -->
<div id="banner">
	<img src="<?php
echo $_obj['stylePath'];
?>
/images/banner.jpg" alt="banner" />
</div>
<!-- banner 结束 -->
<!-- 主体部分 -->
<div id="body">
	<!-- 右列块 -->
	<div id="right-col">
		<!-- 搜索文章块表单 -->
		<div class="right-cat">
			<div class="title">快速搜索</div>
			<form method="get" action="search.php">
				<div class="body">
					<input type="hidden" name="action" value="search" />
					搜索
					<select name="type">
						<option value="title" selected="selected">标题</option>
						<option value="content">内容</option>
						<option value="keyword">关键字</option>
					</select>
					<input type="text" class="text-box" name="keyword" size="12" />
					<input type="submit" class="button" value="搜索" />
				</div>
			</form>
		</div>
		<!-- banner条 -->
		<div class="right-cat">
			<img src="<?php
echo $_obj['stylePath'];
?>
/images/right_banner.jpg" alt="banner" />
		</div>
		<?php
if (!empty($_obj['rightCatList'])){
if (!is_array($_obj['rightCatList']))
$_obj['rightCatList']=array(array('rightCatList'=>$_obj['rightCatList']));
$_tmp_arr_keys=array_keys($_obj['rightCatList']);
if ($_tmp_arr_keys[0]!='0')
$_obj['rightCatList']=array(0=>$_obj['rightCatList']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['rightCatList'] as $rowcnt=>$rightCatList) {
$rightCatList['ROWCNT']=$rowcnt;
$rightCatList['ALTROW']=$rowcnt%2;
$rightCatList['ROWBIT']=$rowcnt%2;
$_obj=&$rightCatList;
?>
		<div class="right-cat">
			<div class="title">
				<div class="cat-title"><?php
echo $_obj['catTitle'];
?>
</div>
				<div class="more">
					<a href="listArticle.php?catPath=<?php
echo $_obj['absPath'];
?>
">
						<img src="<?php
echo $_obj['stylePath'];
?>
/images/more.gif" />
					</a>
				</div>
			</div>
			<div class="body">
				<ul>
					<?php
if (!empty($_obj['rightArtList'])){
if (!is_array($_obj['rightArtList']))
$_obj['rightArtList']=array(array('rightArtList'=>$_obj['rightArtList']));
$_tmp_arr_keys=array_keys($_obj['rightArtList']);
if ($_tmp_arr_keys[0]!='0')
$_obj['rightArtList']=array(0=>$_obj['rightArtList']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['rightArtList'] as $rowcnt=>$rightArtList) {
$rightArtList['ROWCNT']=$rowcnt;
$rightArtList['ALTROW']=$rowcnt%2;
$rightArtList['ROWBIT']=$rowcnt%2;
$_obj=&$rightArtList;
?>
					<li>·<a href="<?php
echo $_obj['linkPath'];
?>
"><?php
echo $_obj['title'];
?>
</a></li>
					<?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
				</ul>
			</div>
		</div>
		<?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
	</div>
	<!--左边内容列表 -->
	<div id="left-col">
		<?php
if (!empty($_obj['catList'])){
if (!is_array($_obj['catList']))
$_obj['catList']=array(array('catList'=>$_obj['catList']));
$_tmp_arr_keys=array_keys($_obj['catList']);
if ($_tmp_arr_keys[0]!='0')
$_obj['catList']=array(0=>$_obj['catList']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['catList'] as $rowcnt=>$catList) {
$catList['ROWCNT']=$rowcnt;
$catList['ALTROW']=$rowcnt%2;
$catList['ROWBIT']=$rowcnt%2;
$_obj=&$catList;
?>	
		<div class="cat-box">
			<div class="title">
				<div class="cat-title"><?php
echo $_obj['catTitle'];
?>
</div>
				<div class="more">
					<a href="listArticle.php?catPath=<?php
echo $_obj['absPath'];
?>
">
						<img src="<?php
echo $_obj['stylePath'];
?>
/images/more.gif" />
					</a>
				</div>
			</div>
			<div class="body">
				<div class="cat-image"><img src="<?php
echo $_obj['catImage'];
?>
" alt="<?php
echo $_obj['catTitle'];
?>
" /></div>
				<div class="article-list">
					<ul>
					<?php
if (!empty($_obj['artList'])){
if (!is_array($_obj['artList']))
$_obj['artList']=array(array('artList'=>$_obj['artList']));
$_tmp_arr_keys=array_keys($_obj['artList']);
if ($_tmp_arr_keys[0]!='0')
$_obj['artList']=array(0=>$_obj['artList']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['artList'] as $rowcnt=>$artList) {
$artList['ROWCNT']=$rowcnt;
$artList['ALTROW']=$rowcnt%2;
$artList['ROWBIT']=$rowcnt%2;
$_obj=&$artList;
?>
						<li>·<a href="<?php
echo $_obj['linkPath'];
?>
"><?php
echo $_obj['title'];
?>
</a></li>
					<?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
					</ul>
				</div>
				<div class="clear-both"></div>
			</div>
			<div class="bottom"></div>
		</div>
		<?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
	</div>
	<div class="clear-both"></div>
</div>
<!-- 版权部分 -->
<div id="foot">
	<div id="adv">
		<a href="admin/">管理登录</a>
	</div>
	<div id="sql">
		Query Times: <?php
echo $_obj['queryTime'];
?>
 <br />Execute Time: <?php
echo $_obj['executeTime'];
?>
 ms
	</div>
</div>

</body>
</html>
