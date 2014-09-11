<?php
/**
 * TOP API: taobao.increment.subscribemessage.get request
 * 
 * @author auto create
 * @since 1.0, 2011-04-28 15:00:15.0
 */
class IncrementSubscribemessageGetRequest
{
	/** 
	 * 需要返回的字段。具体字段间SubscribeMessage说明
	 **/
	private $fields;
	
	private $apiParas = array();
	
	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function getApiMethodName()
	{
		return "taobao.increment.subscribemessage.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
