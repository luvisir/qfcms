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
<link rel="stylesheet" href="../editor/editor.css" type="text/css" />
<script type="text/javascript" src="../javaScript/function.js" ></script>
<script type="text/javascript" src="../editor/edit.js"></script>
</head>

<body onload="InitDocument();">
<div class="main-box">
	<div class="head-dark-box">
		<?php
echo $_obj['title'];
?>

	</div>
	<div class="body-box tip-msg">
		提示: 带 <img src="../images/star.gif" alt="*" /> 的项目为必填信息.
	</div>
	<form method="post" action="article.php" class="white-box">
		<table class="alt-table" cellspacing="0">
			<tr class="light-row">
				<td class="width-20percent">文章分类</td>
				<td><?php
echo $_obj['catPath'];
?>
</td>
			</tr>
			<tr class="dark-row">
				<td>图片新闻</td>
				<td><input type="radio" name="isImg" value="1"  onclick="showObj('imgTR', 'show')"/> 是
					<input type="radio" name="isImg" value="0"   onclick="showObj('imgTR', 'hide')"/> 否
				</td>
			</tr>
			<tr class="<?php
echo $_obj['imgShow'];
?>
" id="imgTR">
				<td class="require-field">缩略图</td>
				<td>
					<img src="<?php
echo $_obj['imgName'];
?>
" alt="新闻缩略图" class="thumbnail" id="selectedThumb" />
					<input type="button" class="button" value="从相册中添加" onclick="showThumbWindow()" />
					<input type="hidden" name="imgName" id="imgName" value="<?php
echo $_obj['imgName'];
?>
" />
				</td>
			</tr>
			<tr class="light-row">
				<td class="require-field">文章标题</td>
				<td><input type="text" class="text-box" name="title" size="30" value="<?php
echo $_obj['artTitle'];
?>
" maxlength="40" /></td>
			</tr>
			<tr class="dark-row">
				<td class="require-field">发布时间</td>
				<td><input type="text" class="text-box" name="postTime" size="20" value="<?php
echo $_obj['postTime'];
?>
" />
					如自己更改请注意格式.
				</td>
			</tr>
			<tr class="light-row">
				<td>文章摘要</td>
				<td><textarea class="text-box" name="summary" cols="40" rows="4"><?php
echo $_obj['summary'];
?>
</textarea>
					小于100个汉字.
				</td>
			</tr>
			<tr class="dark-row">
				<td class="require-field">文章作者</td>
				<td><input type="text" class="text-box" name="author" size="15" value="<?php
echo $_obj['author'];
?>
" maxlength="20"/></td>
			</tr>
			<tr class="light-row">
				<td>文章来源</td>
				<td><input type="text" class="text-box" name="comeFrom" size="25" value="<?php
echo $_obj['comeFrom'];
?>
" maxlength="25"/></td>
			</tr>
			<tr class="dark-row">
				<td class="require-field">关键字</td>
				<td><input type="text" class="text-box" name="keyword" size="20" value="<?php
echo $_obj['keyword'];
?>
" maxlength="20"/>
					用于文章搜索,多个请用","隔开.
				</td>
			</tr>
			<tr class="light-row">
				<td>是否推荐</td>
				<td>
					<input type="radio" name="recommend" value="1" /> 推荐
					<input type="radio" name="recommend" value="0" /> 不推荐
				</td>
			</tr>
		</table>
		<!-- 开始 HTML编辑器 -->
		<div class="toolbar-box">
			<div class="toolbar">
				<img class="toolbarItem" src="../editor/delete.gif" alt="删除" onclick="format1('delete')"/>
				<img class="toolbarItem" src="../editor/cut.gif" alt="剪切" onclick="format1('cut')" />
				<img class="toolbarItem" src="../editor/copy.gif" alt="复制" onclick="format1('copy')" />
				<img class="toolbarItem" src="../editor/paste.gif" alt="粘贴" onclick="format1('paste')" />
				<img class="toolbarItem" src="../editor/removeformat.gif" alt="删除所有格式"  onclick="format1('RemoveFormat')" />
				<img class="toolbarItem" src="../editor/undo.gif" alt="撤消" onclick="format1('undo')" />
				<img class="toolbarItem" src="../editor/redo.gif" alt="恢复" onclick="format1('redo')" />
				<img class="toolbarItem" src="../editor/table.gif" alt="插入表格" onclick="fortable()" />
				<img class="toolbarItem" src="../editor/wlink.gif" alt="插入超级连接" onclick="UserDialog('CreateLink')" />
				<img class="toolbarItem" src="../editor/img.gif" alt="插入图片" onclick="insertImg()" />
				<img class="toolbarItem" src="../editor/hr.gif" alt="插入水平线" onclick="format('InsertHorizontalRule')" />
				<img class="toolbarItem" src="../editor/br.gif" alt="插入换行符" onclick="insertBR()" />
			</div>
			<div class="toolbar">
				<select onchange="doSelectClick('FormatBlock',this)">
                    <option selected="selected">段落格式</option>
                    <option value="&lt;P&gt;">普通</option>
		            <option value="&lt;PRE&gt;">已编排格式</option>
		            <option value="&lt;H1&gt;">标题一</option>
		            <option value="&lt;H2&gt;">标题二</option>
		            <option value="&lt;H3&gt;">标题三</option>
		            <option value="&lt;H4&gt;">标题四</option>
		            <option value="&lt;H5&gt;">标题五</option>
		            <option value="&lt;H6&gt;">标题六</option>
                    <option value="&lt;H7&gt;">标题七</option>
				</select>
				<select onchange="doSelectClick('FormatBlock',this)">
                    <option selected="selected">特殊字体格式</option>
                    <option value="SUP">上标</option>
		            <option value="SUB">下标</option>
		            <option value="DEL">删除线</option>
		            <option value="BLINK">闪烁</option>
		            <option value="BIG">增大字体</option>
		            <option value="SMALL">减小字体</option>
				</select>
				<img class="toolbarItem" src="../editor/fgcolor.gif" alt="字体颜色" onclick="foreColor()" />
				<img class="toolbarItem" src="../editor/bold.gif" alt="加粗" onclick="format('bold')"/>
				<img class="toolbarItem" src="../editor/italic.gif" alt="斜体" onclick="format('italic')" />
				<img class="toolbarItem" src="../editor/underline.gif" alt="下划线" onclick="format('underline')" />
				<img class="toolbarItem" src="../editor/justifyleft.gif" alt="左对齐" onclick="format('justifyleft')" />
				<img class="toolbarItem" src="../editor/justifycenter.gif" alt="居中" onclick="format('justifycenter')" />
				<img class="toolbarItem" src="../editor/justifyright.gif" alt="右对齐" onclick="format('justifyright')" />
				<img class="toolbarItem" src="../editor/insertorderedlist.gif" alt="编号" onclick="format('insertorderedlist')" />
				<img class="toolbarItem" src="../editor/insertunorderedlist.gif" alt="项目符号" onclick="format('insertunorderedlist')" />
				<img class="toolbarItem" src="../editor/outdent.gif" alt="减少缩进量" onclick="format('outdent')" />
				<img class="toolbarItem" src="../editor/indent.gif" alt="增加缩进量" onclick="format('indent')"/>
				<img class="toolbarItem" src="../editor/help.gif" alt="使用帮助" onclick="help()"/>
			</div>
			<div class="toolbar">
				<select onchange="format('fontname',this.value)">
                    <option selected="selected">字体</option>
                    <option value="宋体">宋体</option>
                    <option value="黑体">黑体</option>
                    <option value="仿宋_GB2312">仿宋</option>
                    <option value="楷体_GB2312">楷体</option>
                    <option value="隶书">隶书</option>
                    <option value="幼圆">幼圆</option>
                    <option value="新宋体">新宋体</option>
                    <option value="细明体">细明体</option>
                    <option value="Arial">Arial</option>
                    <option value="Arial Black">Arial Black</option>
                    <option value="Arial Narrow">Arial Narrow</option>
                    <option value="Bradley Hand	ITC">Bradley Hand ITC</option>
                    <option value="Brush Script	MT">Brush Script MT</option>
                    <option value="Century Gothic">Century Gothic</option>
                    <option value="Comic Sans MS">Comic Sans MS</option>
                    <option value="Courier">Courier</option>
                    <option value="Courier New">Courier New</option>
                    <option value="MS Sans Serif">MS Sans Serif</option>
                    <option value="Script">Script</option>
                    <option value="System">System</option>
                    <option value="Times New Roman">Times New Roman</option>
                    <option value="Viner Hand ITC">Viner Hand ITC</option>
                    <option value="Verdana">Verdana</option>
                    <option value="Wide	Latin">Wide Latin</option>
                    <option value="Wingdings">Wingdings</option>
                  </select>
                  <select onchange="format('fontsize',this.value)">
                    <option selected="selected">字号</option>
                    <option value="7">一号</option>
                    <option value="6">二号</option>
                    <option value="5">三号</option>
                    <option value="4">四号</option>
                    <option value="3">五号</option>
                    <option value="2">六号</option>
                    <option value="1">七号</option>
                  </select>
				  <input type="checkbox" onclick="setMode(this.checked)" />
                  查看 HTML 源代码
			</div>
			<iframe name="Composition" id="Composition" frameborder="0"></iframe>
		</div>
		<!-- 结束 HTML编辑器 -->
		<div class="dark-row center">
				<input name="content" id="content "type="hidden" value="" />
				<input type="hidden" name="action" value="<?php
echo $_obj['action'];
?>
" />
				<input type="hidden" name="id" value="<?php
echo $_obj['artID'];
?>
" />
				<input type="hidden" name="linkPath" value="<?php
echo $_obj['linkPath'];
?>
" />
				<input type="hidden" name="oldPath"	value="<?php
echo $_obj['oldPath'];
?>
" />
				<input type="button" class="button" value="<?php
echo $_obj['buttonValue'];
?>
" onclick="submitPost(this.form)" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" class="button" value="预 览" onclick="previewArt()" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" class="button" value="清 空" onclick="clearContent()" />

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
<script>
var artContent = "<?php
echo $_obj['artContent'];
?>
";
//两个单选按钮的默认值
var isImgCheck = "<?php
echo $_obj['isImg'];
?>
";
var recommendCheck = "<?php
echo $_obj['recommend'];
?>
";
setRadioCheck("isImg", isImgCheck);
setRadioCheck("recommend", recommendCheck);
//显示选择文章缩略图的窗口
function showThumbWindow()
{
	var feature = "width=500,height=520px,menubar=no,location=no,status=yes,toolbar=no,resizable=no";
	window.open("listImage.php?action=selectThumb", "",feature);
}
</script>
</html>
