<?php
/**
 * TOP API: taobao.wlb.orderschedulerule.query request
 * 
 * @author auto create
 * @since 1.0, 2011-04-22 14:43:47.0
 */
class WlbOrderscheduleruleQueryRequest
{
	/** 
	 * 当前页
	 **/
	private $pageNo;
	
	/** 
	 * 分页记录个数，如果用户输入的记录数大于50，则一页显示50条记录
	 **/
	private $pageSize;
	
	private $apiParas = array();
	
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
		return "taobao.wlb.orderschedulerule.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
