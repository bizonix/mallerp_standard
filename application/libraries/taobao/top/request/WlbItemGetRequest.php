<?php
/**
 * TOP API: taobao.wlb.item.get request
 * 
 * @author auto create
 * @since 1.0, 2011-04-22 17:57:45.0
 */
class WlbItemGetRequest
{
	/** 
	 * 商品ID
	 **/
	private $itemId;
	
	private $apiParas = array();
	
	public function setItemId($itemId)
	{
		$this->itemId = $itemId;
		$this->apiParas["item_id"] = $itemId;
	}

	public function getItemId()
	{
		return $this->itemId;
	}

	public function getApiMethodName()
	{
		return "taobao.wlb.item.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
