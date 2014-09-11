<?php
/**
 * TOP API: taobao.logistics.online.cancel request
 * 
 * @author auto create
 * @since 1.0, 2011-04-28 17:44:36.0
 */
class LogisticsOnlineCancelRequest
{
	/** 
	 * 淘宝交易ID
	 **/
	private $tid;
	
	private $apiParas = array();
	
	public function setTid($tid)
	{
		$this->tid = $tid;
		$this->apiParas["tid"] = $tid;
	}

	public function getTid()
	{
		return $this->tid;
	}

	public function getApiMethodName()
	{
		return "taobao.logistics.online.cancel";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
