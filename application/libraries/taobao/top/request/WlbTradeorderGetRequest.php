<?php
/**
 * TOP API: taobao.wlb.tradeorder.get request
 * 
 * @author auto create
 * @since 1.0, 2011-04-22 17:58:10.0
 */
class WlbTradeorderGetRequest
{
	/** 
	 * 指定交易类型的交易号
	 **/
	private $tradeId;
	
	/** 
	 * 交易类型:
TAOBAO--淘宝交易
PAIPAI--拍拍交易
YOUA--有啊交易
	 **/
	private $tradeType;
	
	private $apiParas = array();
	
	public function setTradeId($tradeId)
	{
		$this->tradeId = $tradeId;
		$this->apiParas["trade_id"] = $tradeId;
	}

	public function getTradeId()
	{
		return $this->tradeId;
	}

	public function setTradeType($tradeType)
	{
		$this->tradeType = $tradeType;
		$this->apiParas["trade_type"] = $tradeType;
	}

	public function getTradeType()
	{
		return $this->tradeType;
	}

	public function getApiMethodName()
	{
		return "taobao.wlb.tradeorder.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
