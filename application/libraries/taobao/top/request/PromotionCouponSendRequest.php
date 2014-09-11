<?php
/**
 * TOP API: taobao.promotion.coupon.send request
 * 
 * @author auto create
 * @since 1.0, 2011-04-12 10:43:25.0
 */
class PromotionCouponSendRequest
{
	/** 
	 * 买家昵称用半角';'号分割
	 **/
	private $buyerNick;
	
	/** 
	 * 优惠券的id
	 **/
	private $couponId;
	
	private $apiParas = array();
	
	public function setBuyerNick($buyerNick)
	{
		$this->buyerNick = $buyerNick;
		$this->apiParas["buyer_nick"] = $buyerNick;
	}

	public function getBuyerNick()
	{
		return $this->buyerNick;
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

	public function getApiMethodName()
	{
		return "taobao.promotion.coupon.send";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
