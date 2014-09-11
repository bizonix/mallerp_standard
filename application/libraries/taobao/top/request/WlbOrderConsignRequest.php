<?php
/**
 * TOP API: taobao.wlb.order.consign request
 * 
 * @author auto create
 * @since 1.0, 2011-04-22 13:38:27.0
 */
class WlbOrderConsignRequest
{
	/** 
	 * 物流宝订单编号
	 **/
	private $wlbOrderCode;
	
	private $apiParas = array();
	
	public function setWlbOrderCode($wlbOrderCode)
	{
		$this->wlbOrderCode = $wlbOrderCode;
		$this->apiParas["wlb_order_code"] = $wlbOrderCode;
	}

	public function getWlbOrderCode()
	{
		return $this->wlbOrderCode;
	}

	public function getApiMethodName()
	{
		return "taobao.wlb.order.consign";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
