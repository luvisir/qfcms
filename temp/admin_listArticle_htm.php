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
<script type="text/javascript" src="../javascript/function.js"></script>
</head>

<body>
<div class="main-box">
	<div class="head-dark-box">
		文章管理
	</div>
	<div class="body-box tip-msg">
		<ul>
			<li>文章在添加后都处于锁定状态,只在审核后的文章才能发布.</li>
			<li>审核后的文章可以锁定,锁定的文章不被发布.</li>
		</ul>
	</div>
	<div id="navigator">
		当前位置: <?php
echo $_obj['navigator'];
?>
  &nbsp;&nbsp;选择分类 <?php
echo $_obj['selectCat'];
?>

	</div>
	<form method="get" action="article.php">
	<div class="white-box">
		<table class="alt-table" cellspacing="0">
			<tr class='head-light-box'>
				<td class="checkList">&nbsp;</td>
				<td>标题</td>
				<td>类别</td>
				<td>操作</td>
			</tr>
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
			<tr class="row-bg<?php
echo $_obj['ROWBIT'];
?>
">
				<td><input type="checkbox" name="idList[]" value="<?php
echo $_obj['id'];
?>
" /></td>
				<td><a href="<?php
echo $_obj['linkPath'];
?>
" target="_blank" title="点击查看"  class="article-title"><?php
echo $_obj['title'];
?>
</a></td>
				<td><a href="listArticle.php?catPath=<?php
echo $_obj['catPath'];
?>
"><?php
echo $_obj['catTitle'];
?>
</a></td>
				<td>
					[<a href="article.php?action=<?php
echo $_obj['auditAction'];
?>
&id=<?php
echo $_obj['id'];
?>
"><?php
echo $_obj['auditText'];
?>
</a>]
					[<a href="article.php?action=editArticle&id=<?php
echo $_obj['id'];
?>
">修改</a>]
					[<a href="article.php?action=deleteArticle&id=<?php
echo $_obj['id'];
?>
">删除</a>]
				</td>
			</tr>
		<?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
			<tr class="<?php
echo $_obj['noChildClass'];
?>
">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>该分类下暂时没有文章.</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</div>
	<div class="white-box clear-both">
		<div class="tool-bar">
			<input type="checkbox" onclick="checkAll(this)" /> 全选
			选中项: <input type="image" src="../images/audit.gif" alt="审核"  onclick="this.form.elements['action'].value='auditArticle'" />
					<input type="image" src="../images/lock.gif" alt="锁定" onclick="this.form.elements['action'].value='lockArticle'"  />
				   <input type="image" src="../images/delete.gif" alt="删除" onclick="this.form.elements['action'].value='deleteArticle'" />
				   <input type="hidden" name="action" value="" />
		</div>
		<div class="page-bar">
			<?php
echo $_obj['pageParam'];
?>

		</div>
	</div>
	</form>
</div>
<div class="main-box">
	<div class="foot-sql">
		Query Times: <?php
echo $_obj['queryTime'];
?>
  Execute Time: <?php
echo $_obj['executeTime'];
?>
 ms
	</div>
</div>
</body>
</html>
