<?php
/**
 * TOP API: taobao.logistics.trace.search request
 * 
 * @author auto create
 * @since 1.0, 2011-04-28 17:55:23.0
 */
class LogisticsTraceSearchRequest
{
	/** 
	 * 卖家昵称
	 **/
	private $sellerNick;
	
	/** 
	 * 交易号
	 **/
	private $tid;
	
	private $apiParas = array();
	
	public function setSellerNick($sellerNick)
	{
		$this->sellerNick = $sellerNick;
		$this->apiParas["seller_nick"] = $sellerNick;
	}

	public function getSellerNick()
	{
		return $this->sellerNick;
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
		return "taobao.logistics.trace.search";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
