<?php
//====================================================
//		FileName:validate.inc.php
//		Summary: ���ݼ�����
//		
//====================================================
class validate
{

	//==========================================
	// ����: required($data)
	// ����: �������ݲ���Ϊ��
	// ����: $data ����
	//==========================================
	function required($data) 
	{

		if(trim($data) == "")
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	//==========================================
	// ����: checkLength($data)
	// ����: �������ݲ��ܳ���ָ������
	// ����: $data ����
	// ����: $len ָ������
	//==========================================
	function checkLength($data, $len)
	{
		if(!is_int($len))
			exit("���Ȳ�����������");
		if(strlen($data) > $len)
		{
			return false;
		}	
		else
		{
			return true;
		}
	}
	//==========================================
	// ����: isNumber($data)
	// ����: ��������Ƿ�Ϊ����
	// ����: $data ����
	//==========================================
	function isNumber($data)
	{
		$re = "|^\d+$|";
		if(preg_match($re, $data))
		{
			
			return true;
		}
		else
		{
			return false;
		}
	}
	//==========================================
	// ����: match($data, $re)
	// ����: ��������Ƿ�ƥ�������ģʽ
	// ����: $data ����
	// ����: $re ����ʹ�õ�������ʽ
	//==========================================
	function match($data, $re)
	{
		if(preg_match($re, $data))
		{
			
			return true;
		}
		else
		{
			return false;
		}
	}
	//==========================================
	// ����: equal($data1, $data2)
	// ����: ���������������Ƿ����
	// ����: $data1,$data2 ����
	//==========================================
	function equal($data1, $data2)
	{
		if($data1 === $data2)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>
