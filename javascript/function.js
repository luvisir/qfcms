//������ߵ����˵�����ʾ������
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

//���ƶ�����ʾ������
function showObj(objID, action)
{
	var obj = document.getElementById(objID);
	action = (action == "show") ? "block" : "none";
	obj.style.display = action;
}
//ȫѡcheckbox��ȫ��ѡ
function checkAll(obj)
{
	var check =	document.getElementsByName("idList[]");
	for(i=0; i<check.length; i++)
	{
		check[i].checked = obj.checked;
	}
}
//���ñ���GDѡ���״̬
function setGDState(bool)
{
	var obj = document.getElementById("noGD");
	if (bool == true)
	{
		obj.checked = false;
	}
	obj.disabled = bool;
}
//���õ�ѡ��checkֵ
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
//ͼƬ��ʾ
function showImage(id)
{
	var feature = "dialogWidth:750px;dialogHeight:500px;help:no;status:no;"
	window.showModalDialog("showImage.php?id="+id, null, feature);
}
//ͼƬѡ��
function selectPic(path)
{
	var Composition = opener.document.getElementById("Composition");
	if(typeof(path) != "undefined")
		Composition.document.execCommand("InsertImage",false,path);
	else
		Composition.focus();
	window.close();
}
//����ͼѡ��
function selectThumb(path)
{
	//������������ͼ��ͼƬ
	var selectedThumb = opener.document.getElementById("selectedThumb");
	var imgName		  = opener.document.getElementById("imgName");
	selectedThumb.src = path;
	imgName.value	  = path;
	window.close();
}
