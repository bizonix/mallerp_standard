<?php
/**
 * TOP API: taobao.promotion.activity.add request
 * 
 * @author auto create
 * @since 1.0, 2011-04-25 11:27:40.0
 */
class PromotionActivityAddRequest
{
	/** 
	 * 优惠券总领用数量
	 **/
	private $couponCount;
	
	/** 
	 * 优惠券的id，唯一标识这个优惠券
	 **/
	private $couponId;
	
	/** 
	 * 每个人最多领用数量，0代表不限
	 **/
	private $personLimitCount;
	
	private $apiParas = array();
	
	public function setCouponCount($couponCount)
	{
		$this->couponCount = $couponCount;
		$this->apiParas["coupon_count"] = $couponCount;
	}

	public function getCouponCount()
	{
		return $this->couponCount;
	}

	public function setCouponId($couponId)
	{
		$this->couponId = $couponId;
		$this->apiParas["coupon_id"] = $couponId;
	}

	public function getCouponId()
	{
		return $this->couponId;
	}

	public function setPersonLimitCount($personLimitCount)
	{
		$this->personLimitCount = $personLimitCount;
		$this->apiParas["person_limit_count"] = $personLimitCount;
	}

	public function getPersonLimitCount()
	{
		return $this->personLimitCount;
	}

	public function getApiMethodName()
	{
		return "taobao.promotion.activity.add";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
