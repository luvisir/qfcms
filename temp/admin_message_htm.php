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

<body>
<div class="main-box">
	<div class="head-dark-box">
		<?php
echo $_obj['title'];
?>

	</div>
	<div class="body-box">
		<div class="<?php
echo $_obj['msgType'];
?>
">
			<ul>
			<?php
if (!empty($_obj['msgList'])){
if (!is_array($_obj['msgList']))
$_obj['msgList']=array(array('msgList'=>$_obj['msgList']));
$_tmp_arr_keys=array_keys($_obj['msgList']);
if ($_tmp_arr_keys[0]!='0')
$_obj['msgList']=array(0=>$_obj['msgList']);
$_stack[$_stack_cnt++]=$_obj;
foreach ($_obj['msgList'] as $rowcnt=>$msgList) {
$msgList['ROWCNT']=$rowcnt;
$msgList['ALTROW']=$rowcnt%2;
$msgList['ROWBIT']=$rowcnt%2;
$_obj=&$msgList;
?>
				<li><?php
echo $_obj['message'];
?>
</li>
			<?php
}
$_obj=$_stack[--$_stack_cnt];}
?>
			</ul><br />
			<input type="button" class="button" value="返 回" onclick="history.back()" />
		</div>
	</div>
</div>
</body>
</html>
