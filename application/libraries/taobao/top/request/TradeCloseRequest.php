<?php
/**
 * TOP API: taobao.trade.close request
 * 
 * @author auto create
 * @since 1.0, 2011-02-17 10:17:51.0
 */
class TradeCloseRequest
{
	/** 
	 * 交易关闭原因。
	 **/
	private $closeReason;
	
	/** 
	 * 主订单或子订单编号。
	 **/
	private $tid;
	
	private $apiParas = array();
	
	public function setCloseReason($closeReason)
	{
		$this->closeReason = $closeReason;
		$this->apiParas["close_reason"] = $closeReason;
	}

	public function getCloseReason()
	{
		return $this->closeReason;
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
		return "taobao.trade.close";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
