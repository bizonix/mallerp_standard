<?php
/**
 * TOP API: taobao.product.propimg.delete request
 * 
 * @author auto create
 * @since 1.0, 2010-08-03 15:54:04.0
 */
class ProductPropimgDeleteRequest
{
	/** 
	 * 属性图片ID
	 **/
	private $id;
	
	/** 
	 * 产品ID.Product的id.
	 **/
	private $productId;
	
	private $apiParas = array();
	
	public function setId($id)
	{
		$this->id = $id;
		$this->apiParas["id"] = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setProductId($productId)
	{
		$this->productId = $productId;
		$this->apiParas["product_id"] = $productId;
	}

	public function getProductId()
	{
		return $this->productId;
	}

	public function getApiMethodName()
	{
		return "taobao.product.propimg.delete";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
