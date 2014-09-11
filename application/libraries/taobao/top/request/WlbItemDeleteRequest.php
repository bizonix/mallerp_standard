<?php
/**
 * TOP API: taobao.wlb.item.delete request
 * 
 * @author auto create
 * @since 1.0, 2011-04-22 13:56:04.0
 */
class WlbItemDeleteRequest
{
	/** 
	 * 商品ID
	 **/
	private $itemId;
	
	/** 
	 * 商品所有人淘宝nick
	 **/
	private $userNick;
	
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

	public function setUserNick($userNick)
	{
		$this->userNick = $userNick;
		$this->apiParas["user_nick"] = $userNick;
	}

	public function getUserNick()
	{
		return $this->userNick;
	}

	public function getApiMethodName()
	{
		return "taobao.wlb.item.delete";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
