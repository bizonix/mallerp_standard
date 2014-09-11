<?php
/**
 * TOP API: taobao.logistics.online.confirm request
 * 
 * @author auto create
 * @since 1.0, 2011-04-28 17:46:15.0
 */
class LogisticsOnlineConfirmRequest
{
	/** 
	 * 运单号.具体一个物流公司的真实运单号码。淘宝官方物流会校验，请谨慎传入；若company_code中传入的代码非淘宝官方物流合作公司，此处运单号不校验，。<br><font color='red'>无需物流发货可以不填运单号，其他必填</font>
	 **/
	private $outSid;
	
	/** 
	 * 淘宝交易ID
	 **/
	private $tid;
	
	private $apiParas = array();
	
	public function setOutSid($outSid)
	{
		$this->outSid = $outSid;
		$this->apiParas["out_sid"] = $outSid;
	}

	public function getOutSid()
	{
		return $this->outSid;
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
		return "taobao.logistics.online.confirm";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
