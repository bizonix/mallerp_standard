<?php
/**
 * TOP API: taobao.taohua.itemdetail.get request
 * 
 * @author auto create
 * @since 1.0, 2011-02-15 11:10:16.0
 */
class TaohuaItemdetailGetRequest
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
		return "taobao.taohua.itemdetail.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
