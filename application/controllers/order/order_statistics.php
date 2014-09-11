<?php
require_once APPPATH.'controllers/order/order'. EXT;

class Order_statistics extends Order {

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
		$this->load->model('order_model');
		$this->load->model('product_model');
		$this->load->helper('order');
    }
	public function get_date_by_id($q) {
		/**
 		* php 获取时间 今天，昨天，本周，本月，上周，本月，上月(今天,昨天,三天内,本周,上周,本月,三年内,半年内,一年内,三年内)
		*/
		$text = '';
		$now = time();
		$q=(int)$q;
		if($q===0){return array();}
		if ($q === 1) {// 今天
			$text = '今天';
			$beginTime = date('Y-m-d 00:00:00', $now);
			$endTime = date('Y-m-d 23:59:59', $now);
		} elseif ($q === 2) {// 昨天
			$text = '昨天';
			$time = strtotime('-1 day', $now);
			$beginTime = date('Y-m-d 00:00:00', $time);
			$endTime = date('Y-m-d 23:59:59', $time);
		} elseif ($q === 3) {// 本周
			$text = '本周';
			$time = '1' == date('w') ? strtotime('Monday', $now) : strtotime('last Monday', $now);
			$beginTime = date('Y-m-d 00:00:00', $time);
			$endTime = date('Y-m-d 23:59:59', strtotime('Sunday', $now));
		} elseif ($q === 4) {// 上周
			$text = '上周';
			// 本周一
			$thisMonday = '1' == date('w') ? strtotime('Monday', $now) : strtotime('last Monday', $now);
			// 上周一
			$lastMonday = strtotime('-7 days', $thisMonday);
			$beginTime = date('Y-m-d 00:00:00', $lastMonday);
			$endTime = date('Y-m-d 23:59:59', strtotime('last sunday', $now));
		} elseif ($q === 5) {// 本月
			$text = '本月';
			$beginTime = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m', $now), '1', date('Y', $now)));
			$endTime = date('Y-m-d 23:39:59', mktime(0, 0, 0, date('m', $now), date('t', $now), date('Y', $now)));
		} elseif ($q === 6) {// 上月
			$text = '上月';
			$time = strtotime('-1 month', $now);
			$beginTime = date('Y-m-d 00:00:00', mktime(0, 0,0, date('m', $time), 1, date('Y', $time)));
			$endTime = date('Y-m-d 23:39:59', mktime(0, 0, 0, date('m', $time), date('t', $time), date('Y', $time)));
		}
		/*
		echo $text;
		echo '<br />';
		echo $beginTime;
		echo '<br />';
		echo $endTime;*/
		return array('begintime'=>$beginTime,'endtime'=>$endTime);
	}
	public function sale_statistics()
	{
		$all_domain=$this->order_model->statistics_order_get_all_domain();
		$all_check_user=$this->order_model->statistics_order_get_all_check_user();
		
		$domain=$this->input->post('domain');
		$check_user=$this->input->post('check_user');
		$time_line=$this->input->post('time_line');
		if (!$this->input->is_post()) {
            $time_line=1;
        }
		$date=$this->get_date_by_id($time_line);
		if((int)$time_line==0)
		{
			$date['begintime']=$this->input->post('begin_time');
			$date['endtime']=$this->input->post('end_time');
		}
		$statistics_type=$this->input->post('statistics_type');
		//var_dump($all_domain);
		$data = array(
            'all_domain' => $all_domain,
			'all_check_user'=>$all_check_user,
			'begin_time'=>$date['begintime'],
			'end_time'=>$date['endtime'],
			'domain'=>$domain,
			'check_user'=>$check_user,
			'time_line'=>$time_line,
			'statistics_type'=>$statistics_type,
        );
		//var_dump($data);
		$this->template->write_view('content', 'order/order_statistics/sale_statistics', $data);
        $this->template->render();
	}
	
	public function my_sale_statistics()
	{
		$all_domain=$this->order_model->statistics_order_get_all_domain();
		$all_input_user=$this->order_model->statistics_order_get_all_input_user();
		
		$domain=$this->input->post('domain');
		$input_user=$this->input->post('input_user');
		$time_line=$this->input->post('time_line');
		if (!$this->input->is_post()) {
            $time_line=1;
        }
		$date=$this->get_date_by_id($time_line);
		if((int)$time_line==0)
		{
			$date['begintime']=$this->input->post('begin_time');
			$date['endtime']=$this->input->post('end_time');
		}
		$statistics_type=$this->input->post('statistics_type');
		//var_dump($all_domain);
		$data = array(
            'all_domain' => $all_domain,
			'all_input_user'=>$all_input_user,
			'begin_time'=>$date['begintime'],
			'end_time'=>$date['endtime'],
			'domain'=>$domain,
			'input_user'=>$input_user,
			'time_line'=>$time_line,
			'statistics_type'=>$statistics_type,
        );
		//var_dump($data);
		$this->template->write_view('content', 'order/order_statistics/my_sale_statistics', $data);
        $this->template->render();
	}
	public function customer_rank()
	{
		$time_line=$this->input->post('time_line');
		$limit=$this->input->post('limit');
		$orderby=$this->input->post('orderby');
		if (!$this->input->is_post()) {
            $time_line=1;
			$limit=50;
			$orderby=0;
        }
		$date=$this->get_date_by_id($time_line);
		if((int)$time_line==0)
		{
			$date['begintime']=$this->input->post('begin_time');
			$date['endtime']=$this->input->post('end_time');
		}
		$statistics_type=$this->input->post('statistics_type');
		//var_dump($all_domain);
		$data = array(
			'begin_time'=>$date['begintime'],
			'end_time'=>$date['endtime'],
			'time_line'=>$time_line,
			'limit'=>$limit,
			'orderby'=>$orderby,
        );
		//var_dump($data);
		$this->template->write_view('content', 'order/order_statistics/customer_rank', $data);
        $this->template->render();
	}
	public function sku_rank()
	{
		$time_line=$this->input->post('time_line');
		$limit=$this->input->post('limit');
		$orderby=$this->input->post('orderby');
		if (!$this->input->is_post()) {
            $time_line=1;
			$limit=50;
			$orderby=0;
        }
		$date=$this->get_date_by_id($time_line);
		if((int)$time_line==0)
		{
			$date['begintime']=$this->input->post('begin_time');
			$date['endtime']=$this->input->post('end_time');
		}
		$statistics_type=$this->input->post('statistics_type');
		//var_dump($all_domain);
		$data = array(
			'begin_time'=>$date['begintime'],
			'end_time'=>$date['endtime'],
			'time_line'=>$time_line,
			'limit'=>$limit,
			'orderby'=>$orderby,
        );
		//var_dump($data);
		$this->template->write_view('content', 'order/order_statistics/sku_rank', $data);
        $this->template->render();
	}



}

?>
