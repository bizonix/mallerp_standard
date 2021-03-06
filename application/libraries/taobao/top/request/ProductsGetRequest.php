<?php
/**
 * TOP API: taobao.products.get request
 * 
 * @author auto create
 * @since 1.0, 2010-08-03 15:39:09.0
 */
class ProductsGetRequest
{
	/** 
	 * 类目id
	 **/
	private $cid;
	
	/** 
	 * 需返回的字段列表.可选值:Product数据结构中的所有字段;多个字段之间用","分隔
	 **/
	private $fields;
	
	/** 
	 * 用户昵称
	 **/
	private $nick;
	
	/** 
	 * 页码.传入值为1代表第一页,传入值为2代表第二页,依此类推.默认返回的数据是从第一页开始.
	 **/
	private $pageNo;
	
	/** 
	 * 每页条数.每页返回最多返回100条,默认值为40
	 **/
	private $pageSize;
	
	/** 
	 * 属性串pid:vid
	 **/
	private $props;
	
	private $apiParas = array();
	
	public function setCid($cid)
	{
		$this->cid = $cid;
		$this->apiParas["cid"] = $cid;
	}

	public function getCid()
	{
		return $this->cid;
	}

	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setNick($nick)
	{
		$this->nick = $nick;
		$this->apiParas["nick"] = $nick;
	}

	public function getNick()
	{
		return $this->nick;
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

	public function setProps($props)
	{
		$this->props = $props;
		$this->apiParas["props"] = $props;
	}

	public function getProps()
	{
		return $this->props;
	}

	public function getApiMethodName()
	{
		return "taobao.products.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
