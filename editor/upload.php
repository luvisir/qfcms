<?php
/* �ļ��� upload.php
** ˵  �� ͼƬ�ϴ��������
*/
/* ���� save_img($img, $new_name)
** ���� ���ɱ��ϴ���ͼƬ���浽ָ��Ŀ¼
** ���� $img array() ͼƬ����Ϣ����,�������ļ����name
** ���� $new_name ͼƬ�ϴ�����ļ���
*/
function save_img($img)
{
	$now 	  = date("Ymdhis");
	$p		  = strrpos($img['name'],"."); //�õ����һ��.��λ��
	$ext	  = substr($img['name'], $p+1); //�õ�ͼƬ��չ��
	$new_name = "../uploadImages/".$now.".".$ext; //Ҫ����ͼƬ��·��
	$path	  = "/admin/uploadImages/".$now.".".$ext; //Ҫ�ŵ����ݿ����·��
	if (!move_uploaded_file($img['tmp_name'], $new_name))
	{
		return 0;
	}
	else //�ϴ��ɹ�
	{
		//�ı��ļ�Ȩ��
		chmod($new_name, 0755);
		return $path;
	}
}
//================ �����ϴ��� ==============================
if (isset($_POST['uploadFile']))
{
	if ($_FILES['img']['error'] == 4 || $_FILES['img']['size'] == 0)
		echo "failed";
	else
	{
		if ($path = save_img($_FILES['img']))
		{
			echo "<script>alert('�ϴ��ɹ�');</script><span id='path'>".$path."</span>";
		}
		else
		{
			echo "<script>alert('�ϴ�ʧ��');</script>";
		}
	}
}
?>