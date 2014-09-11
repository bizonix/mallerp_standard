<?php
/**
 * TOP API: taobao.wlb.order.cancel request
 * 
 * @author auto create
 * @since 1.0, 2011-04-22 17:57:21.0
 */
class WlbOrderCancelRequest
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
		return "taobao.wlb.order.cancel";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
