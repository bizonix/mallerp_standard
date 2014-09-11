<?php
/**
 * TOP API: taobao.wlb.item.authorization.add request
 * 
 * @author auto create
 * @since 1.0, 2011-04-22 13:50:52.0
 */
class WlbItemAuthorizationAddRequest
{
	/** 
	 * 授权结束时间
	 **/
	private $authorizeEndTime;
	
	/** 
	 * 授权开始时间
	 **/
	private $authorizeStartTime;
	
	/** 
	 * 被授权人用户id
	 **/
	private $consignUserId;
	
	/** 
	 * 商品id
	 **/
	private $itemId;
	
	/** 
	 * 规则名称
	 **/
	private $name;
	
	/** 
	 * 授权数量
	 **/
	private $quantity;
	
	/** 
	 * 授权规则：目前只有GRANT_FIX，按照数量授权
	 **/
	private $ruleCode;
	
	private $apiParas = array();
	
	public function setAuthorizeEndTime($authorizeEndTime)
	{
		$this->authorizeEndTime = $authorizeEndTime;
		$this->apiParas["authorize_end_time"] = $authorizeEndTime;
	}

	public function getAuthorizeEndTime()
	{
		return $this->authorizeEndTime;
	}

	public function setAuthorizeStartTime($authorizeStartTime)
	{
		$this->authorizeStartTime = $authorizeStartTime;
		$this->apiParas["authorize_start_time"] = $authorizeStartTime;
	}

	public function getAuthorizeStartTime()
	{
		return $this->authorizeStartTime;
	}

	public function setConsignUserId($consignUserId)
	{
		$this->consignUserId = $consignUserId;
		$this->apiParas["consign_user_id"] = $consignUserId;
	}

	public function getConsignUserId()
	{
		return $this->consignUserId;
	}

	public function setItemId($itemId)
	{
		$this->itemId = $itemId;
		$this->apiParas["item_id"] = $itemId;
	}

	public function getItemId()
	{
		return $this->itemId;
	}

	public function setName($name)
	{
		$this->name = $name;
		$this->apiParas["name"] = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
		$this->apiParas["quantity"] = $quantity;
	}

	public function getQuantity()
	{
		return $this->quantity;
	}

	public function setRuleCode($ruleCode)
	{
		$this->ruleCode = $ruleCode;
		$this->apiParas["rule_code"] = $ruleCode;
	}

	public function getRuleCode()
	{
		return $this->ruleCode;
	}

	public function getApiMethodName()
	{
		return "taobao.wlb.item.authorization.add";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
