<?php
/**
 * TOP API: taobao.fenxiao.cooperation.update request
 * 
 * @author auto create
 * @since 1.0, 2011-03-17 14:16:19.0
 */
class FenxiaoCooperationUpdateRequest
{
	/** 
	 * 分销商ID
	 **/
	private $distributorId;
	
	/** 
	 * 等级ID(0代表取消)
	 **/
	private $gradeId;
	
	private $apiParas = array();
	
	public function setDistributorId($distributorId)
	{
		$this->distributorId = $distributorId;
		$this->apiParas["distributor_id"] = $distributorId;
	}

	public function getDistributorId()
	{
		return $this->distributorId;
	}

	public function setGradeId($gradeId)
	{
		$this->gradeId = $gradeId;
		$this->apiParas["grade_id"] = $gradeId;
	}

	public function getGradeId()
	{
		return $this->gradeId;
	}

	public function getApiMethodName()
	{
		return "taobao.fenxiao.cooperation.update";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
