<?php
/**
 * TOP API: taobao.wlb.item.consignment.delete request
 * 
 * @author auto create
 * @since 1.0, 2011-04-22 13:44:19.0
 */
class WlbItemConsignmentDeleteRequest
{
	/** 
	 * 商品id
	 **/
	private $itemId;
	
	/** 
	 * 货主商品id列表(支持多货主)
	 **/
	private $ownerItemList;
	
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

	public function setOwnerItemList($ownerItemList)
	{
		$this->ownerItemList = $ownerItemList;
		$this->apiParas["owner_item_list"] = $ownerItemList;
	}

	public function getOwnerItemList()
	{
		return $this->ownerItemList;
	}

	public function getApiMethodName()
	{
		return "taobao.wlb.item.consignment.delete";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
