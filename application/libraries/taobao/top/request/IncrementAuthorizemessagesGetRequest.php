<?php
/**
 * TOP API: taobao.increment.authorizemessages.get request
 * 
 * @author auto create
 * @since 1.0, 2011-04-28 14:54:02.0
 */
class IncrementAuthorizemessagesGetRequest
{
	/** 
	 * 需要返回的字段。具体字段间AuthorizeMessage说明
	 **/
	private $fields;
	
	/** 
	 * 用户昵称列表，每个nick之间以","间隔，一次不超过20个
	 **/
	private $nicks;
	
	/** 
	 * 页码。取值范围:大于零的整数; 默认值:1,即返回第一页数据。
	 **/
	private $pageNo;
	
	/** 
	 * 每页条数。取值范围:大于零的整数;最大值:200;默认值:40。注：只有不指定nick参数时分页才有作用。
	 **/
	private $pageSize;
	
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

	public function setNicks($nicks)
	{
		$this->nicks = $nicks;
		$this->apiParas["nicks"] = $nicks;
	}

	public function getNicks()
	{
		return $this->nicks;
	}

	public function setPageNo($pageNo)
	{
		$this->pageNo = $pageNo;
		$this->apiParas["page_no"] = $pageNo;
	}

	public function getPageNo()
	{
		return $this->pageNo;
	}

	public function setPageSize($pageSize)
	{
		$this->pageSize = $pageSize;
		$this->apiParas["page_size"] = $pageSize;
	}

	public function getPageSize()
	{
		return $this->pageSize;
	}

	public function getApiMethodName()
	{
		return "taobao.increment.authorizemessages.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
