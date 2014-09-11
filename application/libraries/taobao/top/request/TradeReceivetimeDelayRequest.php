<?php
/**
 * TOP API: taobao.trade.receivetime.delay request
 * 
 * @author auto create
 * @since 1.0, 2011-03-03 10:42:58.0
 */
class TradeReceivetimeDelayRequest
{
	/** 
	 * 延长收货的天数，可选值为：3, 5, 7, 10。
	 **/
	private $days;
	
	/** 
	 * 主订单号
	 **/
	private $tid;
	
	private $apiParas = array();
	
	public function setDays($days)
	{
		$this->days = $days;
		$this->apiParas["days"] = $days;
	}

	public function getDays()
	{
		return $this->days;
	}

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
		return "taobao.trade.receivetime.delay";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
