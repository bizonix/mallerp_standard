<?php
/**
 * TOP API: taobao.logistics.dummy.send request
 * 
 * @author auto create
 * @since 1.0, 2011-04-28 17:48:24.0
 */
class LogisticsDummySendRequest
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
		return "taobao.logistics.dummy.send";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
