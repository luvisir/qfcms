<?php
/* 文件名 upload.php
** 说  明 图片上传处理程序
*/
/* 函数 save_img($img, $new_name)
** 功能 将由表单上传的图片保存到指定目录
** 参数 $img array() 图片的信息数组,即表单中文件域的name
** 参数 $new_name 图片上传后的文件名
*/
function save_img($img)
{
	$now 	  = date("Ymdhis");
	$p		  = strrpos($img['name'],"."); //得到最后一个.的位置
	$ext	  = substr($img['name'], $p+1); //得到图片扩展名
	$new_name = "../uploadImages/".$now.".".$ext; //要保存图片的路径
	$path	  = "/admin/uploadImages/".$now.".".$ext; //要放到数据库里的路径
	if (!move_uploaded_file($img['tmp_name'], $new_name))
	{
		return 0;
	}
	else //上传成功
	{
		//改变文件权限
		chmod($new_name, 0755);
		return $path;
	}
}
//================ 处理上传表单 ==============================
if (isset($_POST['uploadFile']))
{
	if ($_FILES['img']['error'] == 4 || $_FILES['img']['size'] == 0)
		echo "failed";
	else
	{
		if ($path = save_img($_FILES['img']))
		{
			echo "<script>alert('上传成功');</script><span id='path'>".$path."</span>";
		}
		else
		{
			echo "<script>alert('上传失败');</script>";
		}
	}
}
?>