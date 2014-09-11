<?php
/**
 * TOP API: taobao.promotion.activity.delete request
 * 
 * @author auto create
 * @since 1.0, 2011-03-28 15:28:35.0
 */
class PromotionActivityDeleteRequest
{
	/** 
	 * 优惠券的id
	 **/
	private $activityId;
	
	private $apiParas = array();
	
	public function setActivityId($activityId)
	{
		$this->activityId = $activityId;
		$this->apiParas["activity_id"] = $activityId;
	}

	public function getActivityId()
	{
		return $this->activityId;
	}

	public function getApiMethodName()
	{
		return "taobao.promotion.activity.delete";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
