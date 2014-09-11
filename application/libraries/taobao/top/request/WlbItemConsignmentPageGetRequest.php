<?php
/**
 * TOP API: taobao.wlb.item.consignment.page.get request
 * 
 * @author auto create
 * @since 1.0, 2011-04-22 14:39:19.0
 */
class WlbItemConsignmentPageGetRequest
{
	/** 
	 * 代销商商品id
	 **/
	private $itemId;
	
	/** 
	 * 供应商商品id
	 **/
	private $tgtItemId;
	
	/** 
	 * 供应商用户id
	 **/
	private $tgtUserId;
	
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

	public function setTgtItemId($tgtItemId)
	{
		$this->tgtItemId = $tgtItemId;
		$this->apiParas["tgt_item_id"] = $tgtItemId;
	}

	public function getTgtItemId()
	{
		return $this->tgtItemId;
	}

	public function setTgtUserId($tgtUserId)
	{
		$this->tgtUserId = $tgtUserId;
		$this->apiParas["tgt_user_id"] = $tgtUserId;
	}

	public function getTgtUserId()
	{
		return $this->tgtUserId;
	}

	public function getApiMethodName()
	{
		return "taobao.wlb.item.consignment.page.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
