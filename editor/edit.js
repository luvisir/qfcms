var editorPath = "../editor/";
SEP_PADDING = 5
HANDLE_PADDING = 7
//var Composition;
var yToolbars =	new Array();

function DebugObject(obj)
{
  var msg = "";
  for (var i in	TB) {
    ans=prompt(i+"="+TB[i]+"\n");
    if (! ans) break;
  }
}

function validateMode()
{
  if (!	bTextMode) return true;
  alert("请取消“查看HTML源代码”选项再使用系统编辑功能或者提交!");
  Composition.focus();
  return false;
}

function format1(what,opt)
{
  if (arguments.length<2)
	  Composition.document.execCommand(what);
  else 
	  Composition.document.execCommand(what,"",opt);

  pureText = false;
  Composition.focus();
}

function format(what,opt)
{
  if (!validateMode()) return;

  format1(what,opt);
}

function setMode(newMode)
{
  bTextMode = newMode;
  var cont;
  if (bTextMode) {
    cleanHtml();
    cleanHtml();

    cont=Composition.document.body.innerHTML;
    Composition.document.body.innerText=cont;
  } else {
    cont=Composition.document.body.innerText;
    Composition.document.body.innerHTML=cont;
  }

  Composition.focus();
}

function getEl(sTag,start)
{
  while	((start!=null) && (start.tagName!=sTag)) start = start.parentElement;
  return start;
}

function UserDialog(what)
{
  if (!validateMode()) return;

  Composition.document.execCommand(what, true);

  pureText = false;
  Composition.focus();
}

function foreColor()
{
  if (!	validateMode())	return;
  var arr = showModalDialog(editorPath + "selcolor.htm", "", "dialogWidth:18.5em; dialogHeight:17.5em; status:0");
  if (arr != null) format('forecolor', arr);
  else Composition.focus();
}

function fortable()
{
  if (!	validateMode())	return;
  var arr = showModalDialog(editorPath + "table.htm", "", "dialogWidth:20em; dialogHeight:8.6em; status:0");
  
  if (arr != null){
  var ss;
  ss=arr.split("*")
  row=ss[0];
  col=ss[1];
  var string;
  string="<table cellspacing='0' cellpadding='3' class='tbl'>";
  for(i=1;i<=row;i++){
  string=string+"<tr>";
  for(j=1;j<=col;j++){
  string=string+"<td></td>";
  }
  string=string+"</tr>";
  }
  string=string+"</table>";
  content=Composition.document.selection.createRange();
  content.pasteHTML(string);
  Composition.focus();
  }
  else Composition.focus();
}
function cleanHtml()
{
  var fonts = Composition.document.body.all.tags("FONT");
  var curr;
  for (var i = fonts.length - 1; i >= 0; i--) {
    curr = fonts[i];
    if (curr.style.backgroundColor == "#ffffff") curr.outerHTML	= curr.innerHTML;
  }
}
function clearContent()
{
	Composition.focus();
	Composition.document.body.innerHTML = "";
}
function getPureHtml()
{
  var str = "";
  var paras = Composition.document.body.all.tags("P");
  if (paras.length > 0)	{
    for	(var i=paras.length-1; i >= 0; i--) str	= paras[i].innerHTML + "\n" + str;
  } else {
    str	= Composition.document.body.innerHTML;
  }
  return str;
}

var bLoad=false
var pureText=true
var bodyTag = "<head>";
	bodyTag += "<style type=\"text/css\">";
	bodyTag += "@import url(" + editorPath + "editorContent.css);"
	bodyTag += "</style>";
	bodyTag += "<meta http-equiv=Content-Type content=\"text/html; charset=gb2312\">";
	bodyTag += "</head>";
	bodyTag += "<body bgcolor=\"#FFFFFF\" class='content'>";

var bTextMode=false

public_description=new Editor

function Editor()
{
  this.put_HtmlMode=setMode;
  this.put_value=putText;
  this.get_value=getText;
}

function getText()
{
	if (bTextMode)
		return Composition.document.body.innerText;
	else
	{
		cleanHtml();
		cleanHtml();
		return Composition.document.body.innerHTML;
	}
}

function putText(v)
{
	if (bTextMode)
		Composition.document.body.innerText = v;
	else
		Composition.document.body.innerHTML = v;
}

function InitDocument()
{
	Composition.document.open();
	if (artContent != "")
		Composition.document.write(bodyTag+artContent);
	else
		Composition.document.write(bodyTag);
	Composition.document.close();
	Composition.document.designMode = "on";
	bLoad=true;
}

function doSelectClick(str, el) {
	var Index = el.selectedIndex;
	if (Index != 0){
		el.selectedIndex = 0;
		if (el.id == "specialtype")
			specialtype(el.options[Index].value);
		else
			format(str,el.options[Index].value);
	}
}
var bIsIE5 = navigator.userAgent.indexOf("IE 5")  > -1;
var edit;
var RangeType;

function specialtype(Mark){
	var strHTML;
	if (bIsIE5) selectRange();	
	if (RangeType == "Text"){
		strHTML = "<" + Mark + ">" + edit.text + "</" + Mark + ">"; 
		edit.pasteHTML(strHTML);
		Composition.focus();
		edit.select();
	}		
}
function insertImg()
{
	
	if (!validateMode()) return;
	Composition.focus();	
	//var imgPath = showModalDialog("listImage.php", "", "dialogWidth:520px;dialogHeight:520px;help:0;status:0");
	window.open("listImage.php", "", "width=500,height=520px,menubar=no,location=no,status=no,toolbar=no,resizable=no");
	/*/
	if(typeof(imgPath) != "undefined")
		Composition.document.execCommand("InsertImage",false,imgPath);
	else
		Composition.focus();
	//*/
}

function insertBR()
{
	if (!validateMode()) return;
	var rangeType =  Composition.document.selection.type;
	var range = Composition.document.selection.createRange();
	Composition.focus();
	range.pasteHTML("<br/>");
	range.collapse(false);
	range.select();
}
function selectRange(){
	edit = Composition.document.selection.createRange();
	RangeType =  Composition.document.selection.type;
}

function rCode(s,a,b){
	var r = new RegExp(a,"gi");
	return 	s.replace(r,b); 
}

function lbcode(){
	if (!validateMode()) return;
	cont=getPureHtml(Composition.document.body.innerHTML);
	var aryCode0 = new Array("<strong>","[b]","</strong>","[/b]","<p","[p","</p>","[/p]","<a href=","[url=","</a>","[/url]");
	var aryCode1 = new Array("<em>","[i]","</em>","[/i]","<u>","[u]","</u>","[/u]","<ul>","[list]","</ul>","[/list]","<ol>","[list=1]","</ol>","[/list]");
	var aryCode2 = new Array("<li>","[*]","</li>","","<font color=","[color=","<font face=","[font=","<font size=","[size=");
	var aryCode9 = new Array(">","]","<","[","</","[/");
	var aryCode = aryCode0.concat(aryCode1).concat(aryCode2).concat(aryCode9);
	
	for (var i=0;i<aryCode.length;i+=2){
		cont=rCode(cont,aryCode[i],aryCode[i+1]);	
	}
	self.opener.FORM.inpost.value+=cont;
	self.close();
}

function help()
{
    var helpmess;
    helpmess="---------------CMSHTML编辑器---------------\r\n\r\n"+
         "CMS编辑器是根据动网编辑器修改而成\r\n再原有基础上添加了许多功能\r\n目前版本为1.0Beta.";
    alert(helpmess);

}

function submitPost(form)
{
   	form.content.value = Composition.document.body.innerHTML;
	form.submit();
}
function previewArt()
{
	var win = window.open('',"preview","width=500,height=400,menubar=no,scrollbars=yes,toolbar=no,location=no,status=no");
	var content = Composition.document.body.innerHTML;
	var css		 = "<style>@import url(" + editorPath + "editorContent.css);</style>";
	win.document.write(css+content);
}