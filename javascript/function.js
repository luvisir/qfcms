//控制左边导航菜单的显示和隐藏
function showMenu(menuID,obj)
{
	var menu =	document.getElementById(menuID);
	var target = obj;
	target.blur();
	if (menu.style.display != "block")
	{
		menu.style.display = "block";
		target.style.backgroundImage = "url(../images/min.gif)";

	}
	else
	{
		menu.style.display = "none";
		target.style.backgroundImage = "url(../images/max.gif)";
	}
}

//控制对象显示和隐藏
function showObj(objID, action)
{
	var obj = document.getElementById(objID);
	action = (action == "show") ? "block" : "none";
	obj.style.display = action;
}
//全选checkbox和全不选
function checkAll(obj)
{
	var check =	document.getElementsByName("idList[]");
	for(i=0; i<check.length; i++)
	{
		check[i].checked = obj.checked;
	}
}
//设置表单中GD选项的状态
function setGDState(bool)
{
	var obj = document.getElementById("noGD");
	if (bool == true)
	{
		obj.checked = false;
	}
	obj.disabled = bool;
}
//设置单选的check值
function setRadioCheck(name, checkValue)
{
	var radio = document.getElementsByName(name);
	for(i=0; i<radio.length; i++)
	{
		if(radio[i].value == checkValue)
		{
			radio[i].checked = true;
			return;
		}
	}
}
//图片显示
function showImage(id)
{
	var feature = "dialogWidth:750px;dialogHeight:500px;help:no;status:no;"
	window.showModalDialog("showImage.php?id="+id, null, feature);
}
//图片选择
function selectPic(path)
{
	var Composition = opener.document.getElementById("Composition");
	if(typeof(path) != "undefined")
		Composition.document.execCommand("InsertImage",false,path);
	else
		Composition.focus();
	window.close();
}
//缩略图选择
function selectThumb(path)
{
	//更改文章缩略图的图片
	var selectedThumb = opener.document.getElementById("selectedThumb");
	var imgName		  = opener.document.getElementById("imgName");
	selectedThumb.src = path;
	imgName.value	  = path;
	window.close();
}
