<?php
/**
 * TOP API: taobao.wlb.item.map.get request
 * 
 * @author auto create
 * @since 1.0, 2011-04-22 14:44:32.0
 */
class WlbItemMapGetRequest
{
	/** 
	 * 要查询映射关系的物流宝商品id
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
		return "taobao.wlb.item.map.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
