<?php
/**
 * TOP API: taobao.item.recommend.add request
 * 
 * @author auto create
 * @since 1.0, 2010-12-14 14:55:04.0
 */
class ItemRecommendAddRequest
{
	/** 
	 * 商品数字ID，该参数必须
	 **/
	private $numIid;
	
	private $apiParas = array();
	
	public function setNumIid($numIid)
	{
		$this->numIid = $numIid;
		$this->apiParas["num_iid"] = $numIid;
	}

	public function getNumIid()
	{
		return $this->numIid;
	}

	public function getApiMethodName()
	{
		return "taobao.item.recommend.add";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
