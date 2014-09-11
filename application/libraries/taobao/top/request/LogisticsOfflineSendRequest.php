<?php
/**
 * TOP API: taobao.logistics.offline.send request
 * 
 * @author auto create
 * @since 1.0, 2011-04-28 17:47:21.0
 */
class LogisticsOfflineSendRequest
{
	/** 
	 * 卖家联系人地址库ID，可以通过taobao.logistics.address.search接口查询到地址库ID。<br><font color='red'>如果为空，取的卖家的默认退货地址</font><br>
<font color='red'><b>注：默认退货地址暂不支持</b></font>
	 **/
	private $cancelId;
	
	/** 
	 * 物流公司代码.如"POST"就代表中国邮政,"ZJS"就代表宅急送.调用 taobao.logistics.companies.get 获取。如传入的代码非淘宝官方物流合作公司，默认是“其他”物流的方式，在淘宝不显示物流具体进度，故传入需谨慎。
	 **/
	private $companyCode;
	
	/** 
	 * 运单号.具体一个物流公司的真实运单号码。淘宝官方物流会校验，请谨慎传入；若company_code中传入的代码非淘宝官方物流合作公司，此处运单号不校验。
	 **/
	private $outSid;
	
	/** 
	 * 卖家联系人地址库ID，可以通过taobao.logistics.address.search接口查询到地址库ID。br><font color='red'>如果为空，取的卖家的默认取货地址</font>
	 **/
	private $senderId;
	
	/** 
	 * 淘宝交易ID
	 **/
	private $tid;
	
	private $apiParas = array();
	
	public function setCancelId($cancelId)
	{
		$this->cancelId = $cancelId;
		$this->apiParas["cancel_id"] = $cancelId;
	}

	public function getCancelId()
	{
		return $this->cancelId;
	}

	public function setCompanyCode($companyCode)
	{
		$this->companyCode = $companyCode;
		$this->apiParas["company_code"] = $companyCode;
	}

	public function getCompanyCode()
	{
		return $this->companyCode;
	}

	public function setOutSid($outSid)
	{
		$this->outSid = $outSid;
		$this->apiParas["out_sid"] = $outSid;
	}

	public function getOutSid()
	{
		return $this->outSid;
	}

	public function setSenderId($senderId)
	{
		$this->senderId = $senderId;
		$this->apiParas["sender_id"] = $senderId;
	}

	public function getSenderId()
	{
		return $this->senderId;
	}

	public function setTid($tid)
	{
		$this->tid = $tid;
		$this->apiParas["tid"] = $tid;
	}

	public function getTid()
	{
		return $this->tid;
	}

	public function getApiMethodName()
	{
		return "taobao.logistics.offline.send";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
