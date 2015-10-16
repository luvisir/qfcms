<?php
//====================================================
//		FileName:timer.inc.php
//		Summary: 定时器类
//		
//====================================================
class timer 
{
	var $startTime;	//起始时间
	var $endTime;	//结束时间

	//==========================================
	// 函数: timer()
	// 功能: constructor
	// 参数: no
	//==========================================
	function timer() 
	{
		$this->start();
	}

	//==========================================
	// 函数: start()
	// 功能: 启动定时器
	// 参数: no
	//==========================================
	function start() 
	{
		$this->startTime = $this->getMicroTime();
	}
	
	//==========================================
	// 函数: getExecuteTime()
	// 功能: 返回定时器的计时秒数
	// 参数: no
	//==========================================

	function getExecuteTime() 
	{
		$this->endTime = $this->getMicroTime();
		$excuteTime = ($this->endTime - $this->startTime) * 1000;
		return round($excuteTime, 4);
	}
	//==========================================
	// 函数: getMicroTime()
	// 功能: 返回当前时间的毫秒值
	// 参数: no
	//==========================================
	function getMicroTime()
	{ 
		$timer = explode(" ",microtime()); 
		return ((float)$timer[0] + (float)$timer[1]); 
    } 

}
?>
