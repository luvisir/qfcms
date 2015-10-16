<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>选择图片--CMS </title>
<meta http-equiv="content-type" content="text/html;charset=GB2312" />
<meta name="Generator" content="EditPlus" />
<meta name="Author" content="CMS" />
<meta name="Keywords" content="CMS" />
<link rel="stylesheet" href="../style/global.css" type="text/css" />
<script type="text/javascript" src="../javascript/function.js"></script>
</head>

<body>
<div class="main-box">
	<div id="navigator">
		当前位置: <?php
echo $_obj['navigator'];
?>
  &nbsp;&nbsp;选择相册 <?php
echo $_obj['selectCat'];
?>

	</div>
	<div class="white-box">
		<?php
if (!empty($_obj['picList'])){
if (!is_array($_obj['picList']))
$_obj['picList']=array(array('picList'=>$_obj['picList']));
$_tmp_arr_keys=array_keys($_obj['picList']);
if ($_tmp_arr_keys[0]!='0')
$_obj['picList']=array(0=>$_obj['picList']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['picList'] as $rowcnt=>$picList) {
$picList['ROWCNT']=$rowcnt;
$picList['ALTROW']=$rowcnt%2;
$picList['ROWBIT']=$rowcnt%2;
$_obj=&$picList;
?>
			<div class="thumb-div">
				<a href="javascript:void(null)" onclick="<?php
echo $_obj['action'];
?>
('<?php
echo $_obj['imagePath'];
?>
')">
					<img src="<?php
echo $_obj['picName'];
?>
" alt="点击选择该图片" />
				</a>
			</div>
		<?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
			<div class="body-box center <?php
echo $_obj['noChildClass'];
?>
">
				该相册暂时没有图片.
			</div>
	</div>
	<div class="white-box clear-both">
		<div class="page-bar">
			<?php
echo $_obj['pageParam'];
?>

		</div>
	</div>
	<div class="center">
		<input type="button" class="button" value="关闭窗口" onclick="window.close()" />
	</div>
</div>
</body>
</html>
