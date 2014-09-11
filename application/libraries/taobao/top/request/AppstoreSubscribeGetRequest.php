<?php
/**
 * TOP API: taobao.appstore.subscribe.get request
 * 
 * @author auto create
 * @since 1.0, 2011-04-13 16:24:16.0
 */
class AppstoreSubscribeGetRequest
{
	/** 
	 * 用户昵称
	 **/
	private $nick;
	
	private $apiParas = array();
	
	public function setNick($nick)
	{
		$this->nick = $nick;
		$this->apiParas["nick"] = $nick;
	}

	public function getNick()
	{
		return $this->nick;
	}

	public function getApiMethodName()
	{
		return "taobao.appstore.subscribe.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
}
