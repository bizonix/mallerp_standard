<?php
/**
 * TOP API: taobao.promotion.meal.add request
 * 
 * @author auto create
 * @since 1.0, 2011-04-13 11:47:14.0
 */
class PromotionMealAddRequest
{
	/** 
	 * 搭配套餐商品列表。item_id为商品的id(数字类型);item_show_name为商品显示名。最多允许5个商品进行搭配，最少是2个商品，且虚拟商品和拍卖商品不能参加套餐活动。以json格式传入。item_show_name最大长度为8,可以为空。
	 **/
	private $itemList;
	
	/** 
	 * 搭配套餐描述。
	 **/
	private $mealMemo;
	
	/** 
	 * 搭配套餐名称。(30个汉字以下)
	 **/
	private $mealName;
	
	/** 
	 * 搭配套餐一口价。这个值要大于0.01，小于商品的价值总和。
	 **/
	private $mealPrice;
	
	/** 
	 * 普通运费模板id。商品API:taobao.postages.get获取卖家的运费模板。当type_postage为BUY时，为必添项。当type_postage为SELL时，此字段忽略。
	 **/
	private $postageId;
	
	/** 
	 * 运费模板类型。卖家标识'SELL';买家标识'BUY'。若为'SELL',则字段postage_id忽略。若为'BUY'，则postage_id为运费模板id，为必添项。
	 **/
	private $typePostage;
	
	private $apiParas = array();
	
	public function setItemList($itemList)
	{
		$this->itemList = $itemList;
		$this->apiParas["item_list"] = $itemList;
	}

	public function getItemList()
	{
		return $this->itemList;
	}

	public function setMealMemo($mealMemo)
	{
		$this->mealMemo = $mealMemo;
		$this->apiParas["meal_memo"] = $mealMemo;
	}

	public function getMealMemo()
	{
		return $this->mealMemo;
	}

	public function setMealName($mealName)
	{
		$this->mealName = $mealName;
		$this->apiParas["meal_name"] = $mealName;
	}

	public function getMealName()
	{
		return $this->mealName;
	}

	public function setMealPrice($mealPrice)
	{
		$this->mealPrice = $mealPrice;
		$this->apiParas["meal_price"] = $mealPrice;
	}

	public function getMealPrice()
	{
		return $this->mealPrice;
	}

	public function setPostageId($postageId)
	{
		$this->postageId = $postageId;
		$this->apiParas["postage_id"] = $postageId;
	}

	public function getPostageId()
	{
		return $this->postageId;
	}

	public function setTypePostage($typePostage)
	{
		$this->typePostage = $typePostage;
		$this->apiParas["type_postage"] = $typePostage;
	}

	public function getTypePostage()
	{
		return $this->typePostage;
	}

	public function getApiMethodName()
	{
		return "taobao.promotion.meal.add";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
