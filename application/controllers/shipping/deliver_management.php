<?php

require_once APPPATH . 'controllers/shipping/shipping' . EXT;

class Deliver_management extends Shipping {

    public function __construct() {
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->library('script');
        $this->load->model('mixture_model');
        $this->load->model('epacket_model');
        $this->load->model('order_model');
		$this->load->model('ebay_order_model');
        $this->load->model('product_model');
		$this->load->model('product_makeup_sku_model');
        $this->load->model('order_shipping_record_model');
        $this->load->model('shipping_code_model');
		$this->load->model('shipping_subarea_model');
		$this->load->library('excel');
        $this->load->helper('shipping_helper');
    }

    public function print_or_deliver() {
        $this->enable_search('order_shipping_record');
        $this->enable_sort('order_shipping_record');

        $pd_show = $this->order_shipping_record_model->print_or_deliver();
        $today = $this->order_shipping_record_model->print_or_deliver_today();
        $shiptoday = $this->order_shipping_record_model->print_or_deliver_shiptoday();
        $ship_confirm_day = $this->order_shipping_record_model->print_or_deliver_ship_confirm_day();
        $current_differ_money = $pd_show + $today - $shiptoday - $ship_confirm_day;
        $print_or_deliver_history = $this->order_shipping_record_model->print_or_deliver_history();
        $ship_remarks = $this->order_shipping_record_model->fetch_order_record_remark();
        $pd_data = array(
            'pd_show'                  => $pd_show,
            'today'                    => $today,
            'shiptoday'                => $shiptoday,
            'ship_confirm_day'         => $ship_confirm_day,
            'current_differ_money'     => $current_differ_money,
            'print_or_deliver_history' => $print_or_deliver_history,
            'ship_remarks'             => $ship_remarks,
        );

        $this->template->write_view('content', 'shipping/deliver_management/print_and_deliver_statistics', $pd_data);
        $this->template->render();
    }

    public function wait_for_shipping_label() {
        $this->enable_search('order');
        $this->enable_sort('order');

        $code = 'stock';
        $priority = fetch_user_priority_by_system_code($code);

        $stock_user_id = NULL;
        if (!$this->is_super_user() && $priority == 1) {
            $stock_user_id = get_current_user_id();
        }
		$not_shipping_codes=array('H','EUB');

        $orders = $this->order_model->fetch_all_wait_for_shipping_label_orders('wait_for_shipping_label', NULL, $stock_user_id,$not_shipping_codes);
        $all_stock_users = $this->user_model->fetch_users_by_system_code('stock');

        $all_stock_user_ids = array('' => lang('please_select'));
        foreach ($all_stock_users as $user) {
            $all_stock_user_ids[$user->u_id] = $user->u_name;
        }
		$shipping_types = $this->get_shipping_types();
		$countries=$this->get_countries_list();

        $data = array(
            'orders' => $orders,
            'all_stock_user_ids' => $all_stock_user_ids,
            'stock_user_id' => $stock_user_id,
			'shipping_types' => $shipping_types,
			'countries'=>$countries,
        );

        $this->template->write_view('content', 'shipping/deliver_management/wait_for_shipping_label', $data);
        $this->template->add_js('static/js/ajax/shipping.js');
        $this->template->render();
    }
	
	private function get_shipping_types() {
        $shipping_code_object = $this->shipping_code_model->fetch_all_shipping_codes();
        $shipping_types = array();
        $shipping_types[''] = lang('all');
        foreach ($shipping_code_object as $item)
        {
            $shipping_types[$item->code] = $item->code;
        }
        return $shipping_types;
    }
	private function get_countries_list() {
        $countries_code_object = $this->shipping_subarea_model->fetch_all_country();
        $countries_codes = array();
        $countries_codes[''] = lang('all');
        foreach ($countries_code_object as $item)
        {
            $countries_codes[$item->name_en] = $item->name_cn;
        }
        return $countries_codes;
    }

    public function wait_for_purchase_order_list() {
        $this->enable_search('order_list');
        $this->enable_sort('order_list');
        $code = 'stock';
        $priority = fetch_user_priority_by_system_code($code);
        $stock_user_id = NULL;
        if (!$this->is_super_user() && $priority == 1) {
            $stock_user_id = get_current_user_id();
        }
        $status_id = fetch_status_id('order_status', 'wait_for_purchase');
        $orders = $this->order_model->fetch_wait_for_purchase_order_list($status_id);
        $all_stock_users = $this->user_model->fetch_users_by_system_code('stock');
        $all_stock_user_ids = array('' => lang('please_select'));
        foreach ($all_stock_users as $user) {
            $all_stock_user_ids[$user->u_id] = $user->u_name;
        }
        $data = array(
            'orders' => $orders,
            'all_stock_user_ids' => $all_stock_user_ids,
            'stock_user_id' => $stock_user_id,
        );
        $this->template->write_view('content', 'shipping/deliver_management/wait_for_purchase_order_list', $data);
        $this->template->add_js('static/js/ajax/shipping.js');
        $this->template->render();
    }

    public function wait_for_purchase_order_list_abroad() {
        $this->enable_search('order_list');
        $this->enable_sort('order_list');
        $code = 'stock';
        $priority = fetch_user_priority_by_system_code($code);
        $stock_user_id = NULL;
        if (!$this->is_super_user() && $priority == 1) {
            $stock_user_id = get_current_user_id();
        }
        $status_id = fetch_status_id('order_status', 'wait_for_purchase');
        $cky_shipping_codes = $this->shipping_code_model->cky_fetch_all_shipping_codes();
        $orders = $this->order_model->fetch_wait_for_purchase_order_list_abroad($status_id, $cky_shipping_codes);
        $data = array(
            'orders' => $orders,
        );
        $this->template->write_view('content', 'shipping/deliver_management/wait_for_purchase_order_list_abroad', $data);
        $this->template->add_js('static/js/ajax/shipping.js');
        $this->template->render();
    }

    public function before_print_label() {
        if (!$this->input->is_post()) {
            return;
        }

        $post_count = count($_POST);
        $post_keys = array_keys($_POST);
        $order_ids = array();

        foreach ($post_keys as $key) {
            if (strpos($key, 'checkbox_select_') === 0) {
                $order_ids[] = $_POST[$key];
            }
        }

        $order_count = $this->input->post('order_count');
        $orders = array();
        foreach ($order_ids as $order_id) {
            $orders[] = $this->order_model->get_order($order_id);
        }

        $code = 'stock';
        $priority = fetch_user_priority_by_system_code($code);

        $all_stock_users = $this->user_model->fetch_users_by_system_code('stock');

        foreach ($all_stock_users as $user) {
            $all_stock_user_ids[$user->u_id] = $user->login_name;
        }

        $data = array(
            'orders' => $orders,
            'priority' => $priority,
            'all_stock_users' => $all_stock_user_ids,
            'current_user_id' => get_current_user_id(),
        );
        $this->template->write_view('content', 'shipping/deliver_management/before_print_label', $data);
        $this->template->render();
    }

    public function print_label() {
		$style = array(
            'position' => 'S',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => false,
            'padding' => 0,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 12,
            'stretchtext' => 4
        );
		$list_type = $this->input->post('list_type');
		if($list_type==1)
		{
			$width = 100;
			$height = 100;
			$pagelayout = array($width,$height);
			$my_tcpdf['page_format'] = $pagelayout;
			$my_tcpdf['page_orientation'] = 'L';
			$tcpdf['encoding'] = 'UTF-8';
			$this->load->library('pdf',$my_tcpdf);
        	$this->pdf->SetCreator('Mallerp');
        	$this->pdf->SetAuthor('Mansea');
        	$this->pdf->SetTitle('Ebay ShipOrder List');
        	$this->pdf->SetSubject('Mallerp');
        	$this->pdf->SetKeywords('Mansea, Mallerp, zhaosenlin, 278203374, 7410992');
        	$this->pdf->SetFont('arialunicid0', '', 23);
			$this->pdf->setPrintHeader(false);
			$this->pdf->setPrintFooter(false);
			$this->pdf->SetMargins(0, 0, 0);
		}
		if($list_type==2)
		{
			/*excel表头*/
			$head = array(
            	'Transaction Date',
				'Transaction ID',
				'Product',
				'SKU',
				'Quantity',
				'Name',
				'Shipping Cost',
				'ERP order number',
				'Price',
				'Discount Price',
				'Total Cost',
        	);
		}
		if($list_type==3)
		{
			$my_tcpdf['page_orientation'] = 'L';
			$tcpdf['encoding'] = 'UTF-8';
			$this->load->library('pdf',$my_tcpdf);
        	$this->pdf->SetCreator('Mallerp');
        	$this->pdf->SetAuthor('Mansea');
        	$this->pdf->SetTitle('Ebay ShipOrder List');
        	$this->pdf->SetSubject('Mallerp');
        	$this->pdf->SetKeywords('Mansea, Mallerp, zhaosenlin, 278203374, 7410992');
        	$this->pdf->SetFont('arialunicid0', '', 23);
		}
        
		$CI = & get_instance();
		$this->load->library('excel');
        if (!$this->input->is_post()) {
            return;
        }
		
		$post_keys = array_keys($_POST);
        $order_ids = array();
		$i_d=0;
        foreach ($post_keys as $key) {
            if (strpos($key, 'checkbox_select_') === 0) {
                $order_ids[] = $_POST[$key];
            }
        }
        $orders = array();
        $user_name = $this->get_current_user_name();
        $products = array();
		$data = array();
		$all_total_cost=0;
		foreach ($order_ids as $order_id) {
			$i_d++;
            $order = $this->order_model->get_order($order_id);
            $skus = explode(',', $order->sku_str);
            $qties = explode(',', $order->qty_str);
			$temp_skus=array();
			$temp_qties=array();
			foreach($skus as $i=>$sku)
			{
				if ($this->order_model->check_exists('product_makeup_sku', array('makeup_sku' =>$sku  )))
				{
					$makeup_sku=$this->product_makeup_sku_model->fetch_makeup_sku_by_sku($skus[$i]);
					$sku_arr=explode(',', $makeup_sku->sku);
					$qty_arr=explode(',', $makeup_sku->qty);
					foreach($sku_arr as $key=>$value)
					{
						$temp_skus[]=$value;
						$count_sku=(int)$qties[$i]*$qty_arr[$key];
						$temp_qties[]=$count_sku;
					}
				}else
				{
					$temp_skus[]=$sku;
					$temp_qties[]=$qties[$i];
				}
			}
			$skus = $temp_skus;
            $qties = $temp_qties;
            $j = 0;
            $error = FALSE;

			$label_content = create_print_label_content($order);
            $remark = $order->sys_remark;
            $remark .= sprintf(lang('print_label_remark'), date('Y-m-d H:i:s'), $user_name);
            $info = array(
                'label_content' => $label_content,
                'print_label_user' => $user_name,
                'print_label_date' => date('Y-m-d H:i:s'),
                'order_status' => $this->order_statuses['wait_for_shipping_confirmation'],
                'sys_remark' => $remark,
            );
            $this->order_model->update_order_information($order_id, $info);

			/*补打标签不更新库存*/
            $orders[] = $this->order_model->get_order($order_id);
			if($list_type==1)
			{/*添加一页pdf打印*/
				$this->pdf->AddPage();
				$page_index=1;
				/*定义打印变量*/
				$shipping_method = shipping_method($order->is_register);
				$country_name_cn=get_country_name_cn($order->country);
				$shipaddress = "";
				$phone = '';
				$shipaddress = trim($order->address_line_1) . " " . trim($order->address_line_2) . " " . trim($order->town_city) . " " . trim($order->state_province) . " " . trim($order->zip_code);
				$print_date = date('Y.m.d');
				if (!empty($order->contact_phone_number)) {
                	$phone = "<br/>Telephone:$order->contact_phone_number";
            	}
				$htmlshipping_method = <<<EOD
<span style="font-family:droidsansfallback;font-size:12;">{$shipping_method->name_cn} [{$order->is_register}]</span>
EOD;
				$htmlauction_site = <<<EOD
<span style="font-family:droidsansfallback;font-size:20;">{$order->auction_site}</span>
EOD;
				$text_to = <<<EOD
<span style="font-family:Arial, Helvetica, sans-serif; font-size:16;font-weight:bold;">TO:</span>
EOD;
				$ship_to_add = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:12;font-weight:bold;">$order->name<br>{$shipaddress} <br>$order->country[{$country_name_cn}] {$phone}</span>
EOD;
				$htmlprint_date = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:8;">日期:{$print_date}</span>
EOD;
				$img_hr = <<<EOD
<hr style="height:0.3mm;width:98mm;">
EOD;
				if($order->note!='')
				{
					$break_key=4;
					$first_page_sku_num=5;
					$page_break_key=8;
					$order_note = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:8;">NOTE：{$order->note}</span>
EOD;
				}else{
					$break_key=6;
					$first_page_sku_num=7;
					$page_break_key=6;
					$order_note = '';
				}
				$this->pdf->writeHTMLCell($w = 90, $h = 8, $x = 5, $y = 1, $htmlshipping_method, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				$this->pdf->write1DBarcode($order_id, 'C128A', 5, 8, 70, 8, 0.8, $style, 'C'); //write1DBarcode($code, $type, $x, $y, $w, $h, $xres, $newstyle, '');
				$this->pdf->writeHTMLCell($w = 18, $h = 10, $x = 81, $y =10, $htmlauction_site, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				$this->pdf->writeHTMLCell($w = 15, $h = 30, $x = 2, $y =21, $text_to, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);
				$this->pdf->writeHTMLCell($w = 75, $h = 30, $x = 18, $y =21, $ship_to_add, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				$this->pdf->writeHTMLCell($w = 98, $h = 1, $x = 1, $y =56, $img_hr, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				foreach ($skus as $key => $sku) {
					$no = $key + 1;
					$num_no = $key + 1;
					$sql1 = 'name_cn,shelf_code';
                    $myproduct = $CI->product_model->fetch_product_by_sku($sku, $sql1);
					$htmlproduct_list ='';
					$htmlproduct_list = <<<EOD
<span style="white-space:nowrap;font-size:9;">({$num_no}) {$myproduct->shelf_code}-{$sku}-{$myproduct->name_cn}*{$qties[$key]}</span>
EOD;
					$this->pdf->writeHTMLCell($w = 98, $h = 3, $x = 1, $y = 52 + 5 * $no, $htmlproduct_list, $border = 0, $ln = 0, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
					if($key==$break_key){break;}
				}
				/*备注打印在第一页*/
				if($order->note!='')
				{
					$this->pdf->writeHTMLCell($w = 98, $h = 6, $x = 1, $y =81, $order_note, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				}
				$this->pdf->writeHTMLCell($w = 40, $h = 3, $x = 2, $y =95, $htmlprint_date, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				if(count($skus)>$first_page_sku_num)
				{
					foreach ($skus as $key => $sku) {
						if($key>=$first_page_sku_num&&( ($page_index*13-$key)==$page_break_key ))
						{
							$this->pdf->AddPage();
							$page_index++;
							$htmllink_uppage = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:12;">接上页清单：order id:{$order_id},本页是第{$page_index}页</span>
EOD;
							$this->pdf->writeHTMLCell($w = 90, $h = 8, $x = 5, $y = 1, $htmllink_uppage, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
							$this->pdf->writeHTMLCell($w = 40, $h = 3, $x = 2, $y =95, $htmlprint_date, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
						}
						
						if(ceil(($key+$page_break_key+1)/13)==$page_index && $key>=$first_page_sku_num)
						{
							$no = ($key+$page_break_key)-($page_index-1)*13+1;
							$num_no = $key + 1;
							$sql1 = 'name_cn,shelf_code';
                    		$myproduct = $CI->product_model->fetch_product_by_sku($sku, $sql1);
							$htmlproduct_list ='';
							$htmlproduct_list = <<<EOD
<span style="white-space:nowrap;font-size:9;">({$num_no}) {$myproduct->shelf_code}-{$sku}-{$myproduct->name_cn}*{$qties[$key]}</span>
EOD;
							$this->pdf->writeHTMLCell($w = 98, $h = 3, $x = 1, $y = 10 + 5 * $no, $htmlproduct_list, $border = 0, $ln = 0, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
						}
					}
				}
				if(in_array(strtoupper($order->is_register),array('CHR','CHS','HKR','HKS','MYR','MYS','SGR','SGS')))
				{
					$big_is_register=strtoupper($order->is_register);
					$this->pdf->AddPage();
					$return_post_img = <<<EOD
<img width="99mm" height="99mm" src="/static/images/returnpost/{$big_is_register}.jpg"/>
EOD;
					$this->pdf->writeHTMLCell($w = 99, $h = 99, $x = 0, $y = 0, $return_post_img, $border = 0, $ln = 0, $fill = 0, $reseth = true, $align = 'C', $autopadding = false);
				}
				
			}
			elseif($list_type==2)
			{/*为wish pi excel增加一行*/
				$skus = explode(',', $order->sku_str);
				$qties = explode(',', $order->qty_str);
				$price='';
				$discount_price='';
				$total_cost=0;
				foreach($skus as $key=>$sku)
				{
					$sql1 = 'sale_price';
					$myproduct = $CI->product_model->fetch_product_by_sku($sku, $sql1);
					$price.=$myproduct->sale_price;
					$discount_price.=price($myproduct->sale_price*0.961);
					$total_cost+=$myproduct->sale_price*0.961*$qties[$key];
					if(($key+1)!=count($skus))
					{
						$price.=',';
						$discount_price.=',';
					}
				}
				$total_cost+=1.99;
				$all_total_cost+=$total_cost;
				$data[] = array(
						 $order->created_at,//Transaction Date
						 $order->transaction_id,//Transaction ID
						 $order->item_title_str,//'Product',
						 $order->sku_str,//'SKU',
						 $order->qty_str,//'Quantity',
						 $order->name,//'Name',
						 '1.99',//Shipping Cost',
						 $order->item_no,//'ERP order number',
						 $price,//'Price',
						 $discount_price,//'Discount Price',
						 price($total_cost),//'Total Cost',
						 );
			}
			elseif($list_type==3)
			{/*添加一页A4  pdf打印*/
				$this->pdf->AddPage('P','A4');
				//$skus = explode(',', $order->sku_str);
				//$qties = explode(',', $order->qty_str);
				$print_date = date('Y.m.d');
				$user=fetch_user_name_by_id(fetch_user_id_by_login_name($order->input_user));
				$country_cn_name=get_country_name_cn($order->country);
				$html8= <<<EOD
<table width="100%" border="1" cellspacing="0" cellpadding="0" style="font-family:Arial, Helvetica, sans-serif;font-size:12;">
<tr style="font-family:droidsansfallback;font-size:12;"><td>业务员</td><td>{$user}</td><td>订单识别号</td><td>{$order->item_no}</td><td colspan="2" rowspan="3">备注：{$order->note}</td></tr>
EOD;
$html8.= <<<EOD
<tr style="font-family:droidsansfallback;font-size:12;"><td>客户名称</td><td>{$order->name}</td><td>国家</td><td>{$country_cn_name}</td></tr>
<tr style="font-family:droidsansfallback;font-size:12;"><td>运输方式</td><td>{$order->is_register}</td><td>日期</td><td>{$print_date}</td></tr>
<tr style="font-family:droidsansfallback;font-size:12;"><td>货架号</td><td>SKU</td><td>数量</td><td>图片</td><td>中文名称</td><td>单价</td></tr>
EOD;
				foreach($skus as $key=>$sku)
				{
					$sql1 = 'name_cn,shelf_code,sale_price,image_url,price';
					$myproduct = $CI->product_model->fetch_product_by_sku($sku, $sql1);
					$image="";
					
					if($myproduct->image_url!=''&&$myproduct->image_url!=NULL&&$myproduct->image_url!='none')
					{
						if(strpos($myproduct->image_url, 'http://') !== false)
						{
							$url=$myproduct->image_url;
						}else{
							$url= 'http://erp.screamprice.com'.$myproduct->image_url;
						}
						//$header= get_headers($url);
						if($this->img_exits($url))
						{
							$arr = getimagesize($url);
							if(count($arr)>0)
							{
								$image='<img src="'.$myproduct->image_url.'" border="0" height="15mm" width="15mm" />';
							}
						}
					}else{
						$image='<img src="http://erp.screamprice.com/static/images/404-error.png" border="0" height="25mm" width="25mm" />';
					}

					
					$html8.= <<<EOD
<tr style="font-family:droidsansfallback;font-size:12;height:15mm;"><td style="font-family:droidsansfallback;font-size:12;height:15mm;">{$myproduct->shelf_code}</td><td style="font-family:droidsansfallback;font-size:12;height:15mm;">{$sku}</td><td style="font-family:droidsansfallback;font-size:12;height:15mm;">{$qties[$key]}</td><td style="font-family:droidsansfallback;font-size:12;height:15mm;">{$image}</td><td style="font-family:droidsansfallback;font-size:12;height:15mm;">{$myproduct->name_cn}</td><td style="font-family:droidsansfallback;font-size:12;height:15mm;">{$myproduct->sale_price}</td></tr>
EOD;
//if($sku=='YXDS0001'){break;}
				}
$html8.= <<<EOD
</table>
EOD;
				$this->pdf->writeHTMLCell($w=200, $h=0, $x=5, $y=5, $html8, $border=1, $ln=0, $fill=0, $reseth=true, $align='L', $autopadding=false);
				//$this->pdf->writeHTML($html8, true, false, true, false, '');
			}
			/*更改 订单状态为带发货确认*/
		}
		/*保存文件*/
		if($list_type==1)
		{
			$filename = "order_print_10x10_" . date("Ymd") . ".pdf";
			$this->pdf->Output($filename, 'D');
		}
		elseif($list_type==2)
		{
			$data[] = array(
						 ' ',//Transaction Date
						 ' ',//Transaction ID
						 ' ',//'Product',
						 ' ',//'SKU',
						 ' ',//'Quantity',
						 ' ',//'Name',
						 ' ',//Shipping Cost',
						 ' ',//'ERP order number',
						 ' ',//'Price',
						 ' ',//'Discount Price',
						 price($all_total_cost),//'Total Cost',
						 );
			$this->excel->array_to_excel($data, $head, 'wish_pi_list_' . date('Y-m-d'));
		}
		elseif($list_type==3)
		{
			$filename = "order_print_a4_" . date("Ymd") . ".pdf";
			$this->pdf->Output($filename, 'D');
		}
    }
	public function late_print_label() {
		$style = array(
            'position' => 'S',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => false,
            'padding' => 0,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 12,
            'stretchtext' => 4
        );
		$list_type = $this->input->post('list_type');
		if($list_type==1)
		{
			$width = 100;
			$height = 100;
			$pagelayout = array($width,$height);
			$my_tcpdf['page_format'] = $pagelayout;
			$my_tcpdf['page_orientation'] = 'L';
			$tcpdf['encoding'] = 'UTF-8';
			$this->load->library('pdf',$my_tcpdf);
        	$this->pdf->SetCreator('Mallerp');
        	$this->pdf->SetAuthor('Mansea');
        	$this->pdf->SetTitle('Ebay ShipOrder List');
        	$this->pdf->SetSubject('Mallerp');
        	$this->pdf->SetKeywords('Mansea, Mallerp, zhaosenlin, 278203374, 7410992');
        	$this->pdf->SetFont('arialunicid0', '', 23);
			$this->pdf->setPrintHeader(false);
			$this->pdf->setPrintFooter(false);
			$this->pdf->SetMargins(0, 0, 0);
		}
		if($list_type==2)
		{
			$head = array(
            	'Transaction Date',
				'Transaction ID',
				'Product',
				'SKU',
				'Quantity',
				'Name',
				'Shipping Cost',
				'ERP order number',
				'Price',
				'Discount Price',
				'Total Cost',
        	);
		}
		if($list_type==3)
		{
			$my_tcpdf['page_orientation'] = 'L';
			$tcpdf['encoding'] = 'UTF-8';
			$this->load->library('pdf',$my_tcpdf);
        	$this->pdf->SetCreator('Mallerp');
        	$this->pdf->SetAuthor('Mansea');
        	$this->pdf->SetTitle('Ebay ShipOrder List');
        	$this->pdf->SetSubject('Mallerp');
        	$this->pdf->SetKeywords('Mansea, Mallerp, zhaosenlin, 278203374, 7410992');
        	$this->pdf->SetFont('arialunicid0', '', 23);
		}
        
		$CI = & get_instance();
		$this->load->library('excel');
        if (!$this->input->is_post()) {
            return;
        }
		
		$post_keys = array_keys($_POST);
        $order_ids = array();
		$i_d=0;
        foreach ($post_keys as $key) {
            if (strpos($key, 'checkbox_select_') === 0) {
                $order_ids[] = $_POST[$key];
            }
        }
        $orders = array();
        $user_name = $this->get_current_user_name();
        $products = array();
		$data = array();
		$all_total_cost=0;
		foreach ($order_ids as $order_id) {
			$i_d++;
            $order = $this->order_model->get_order($order_id);
            $skus = explode(',', $order->sku_str);
            $qties = explode(',', $order->qty_str);
			$temp_skus=array();
			$temp_qties=array();
			foreach($skus as $i=>$sku)
			{
				if ($this->order_model->check_exists('product_makeup_sku', array('makeup_sku' =>$sku  )))
				{
					$makeup_sku=$this->product_makeup_sku_model->fetch_makeup_sku_by_sku($skus[$i]);
					$sku_arr=explode(',', $makeup_sku->sku);
					$qty_arr=explode(',', $makeup_sku->qty);
					foreach($sku_arr as $key=>$value)
					{
						$temp_skus[]=$value;
						$count_sku=(int)$qties[$i]*$qty_arr[$key];
						$temp_qties[]=$count_sku;
					}
				}else
				{
					$temp_skus[]=$sku;
					$temp_qties[]=$qties[$i];
				}
			}
			$skus = $temp_skus;
            $qties = $temp_qties;
            $j = 0;
            $error = FALSE;
			/*补打标签不更新库存*/
            $orders[] = $this->order_model->get_order($order_id);
			if($list_type==1)
			{/*添加一页pdf打印*/
				$this->pdf->AddPage();
				$page_index=1;
				/*定义打印变量*/
				$shipping_method = shipping_method($order->is_register);
				$country_name_cn=get_country_name_cn($order->country);
				$shipaddress = "";
				$phone = '';
				$shipaddress = trim($order->address_line_1) . " " . trim($order->address_line_2) . " " . trim($order->town_city) . " " . trim($order->state_province) . " " . trim($order->zip_code);
				$print_date = date('Y.m.d');
				if (!empty($order->contact_phone_number)) {
                	$phone = "<br/>Telephone:$order->contact_phone_number";
            	}
				$htmlshipping_method = <<<EOD
<span style="font-family:droidsansfallback;font-size:12;">{$shipping_method->name_cn} [{$order->is_register}]</span>
EOD;
				$htmlauction_site = <<<EOD
<span style="font-family:droidsansfallback;font-size:20;">{$order->auction_site}</span>
EOD;
				$text_to = <<<EOD
<span style="font-family:Arial, Helvetica, sans-serif; font-size:16;font-weight:bold;">TO:</span>
EOD;
				$ship_to_add = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:12;font-weight:bold;">$order->name<br>{$shipaddress} <br>$order->country[{$country_name_cn}] {$phone}</span>
EOD;
				$htmlprint_date = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:8;">日期:{$print_date}</span>
EOD;
				$img_hr = <<<EOD
<hr style="height:0.3mm;width:98mm;">
EOD;
				if($order->note!='')
				{
					$break_key=4;
					$first_page_sku_num=5;
					$page_break_key=8;
					$order_note = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:8;">NOTE：{$order->note}</span>
EOD;
				}else{
					$break_key=6;
					$first_page_sku_num=7;
					$page_break_key=6;
					$order_note = '';
				}
				$this->pdf->writeHTMLCell($w = 90, $h = 8, $x = 5, $y = 1, $htmlshipping_method, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				$this->pdf->write1DBarcode($order_id, 'C128A', 5, 8, 70, 8, 0.8, $style, 'C'); //write1DBarcode($code, $type, $x, $y, $w, $h, $xres, $newstyle, '');
				$this->pdf->writeHTMLCell($w = 18, $h = 10, $x = 81, $y =10, $htmlauction_site, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				$this->pdf->writeHTMLCell($w = 15, $h = 30, $x = 2, $y =21, $text_to, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);
				$this->pdf->writeHTMLCell($w = 75, $h = 30, $x = 18, $y =21, $ship_to_add, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				$this->pdf->writeHTMLCell($w = 98, $h = 1, $x = 1, $y =56, $img_hr, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				foreach ($skus as $key => $sku) {
					$no = $key + 1;
					$num_no = $key + 1;
					$sql1 = 'name_cn,shelf_code';
                    $myproduct = $CI->product_model->fetch_product_by_sku($sku, $sql1);
					$htmlproduct_list ='';
					$htmlproduct_list = <<<EOD
<span style="white-space:nowrap;font-size:9;">({$num_no}) {$myproduct->shelf_code}-{$sku}-{$myproduct->name_cn}*{$qties[$key]}</span>
EOD;
					$this->pdf->writeHTMLCell($w = 98, $h = 3, $x = 1, $y = 52 + 5 * $no, $htmlproduct_list, $border = 0, $ln = 0, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
					if($key==$break_key){break;}
				}
				/*备注打印在第一页*/
				if($order->note!='')
				{
					$this->pdf->writeHTMLCell($w = 98, $h = 6, $x = 1, $y =81, $order_note, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				}
				$this->pdf->writeHTMLCell($w = 40, $h = 3, $x = 2, $y =95, $htmlprint_date, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
				if(count($skus)>$first_page_sku_num)
				{
					foreach ($skus as $key => $sku) {
						if($key>=$first_page_sku_num&&( ($page_index*13-$key)==$page_break_key ))
						{
							$this->pdf->AddPage();
							$page_index++;
							$htmllink_uppage = <<<EOD
<span style="text-align:left;white-space:nowrap;font-size:12;">接上页清单：order id:{$order_id},本页是第{$page_index}页</span>
EOD;
							$this->pdf->writeHTMLCell($w = 90, $h = 8, $x = 5, $y = 1, $htmllink_uppage, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
							$this->pdf->writeHTMLCell($w = 40, $h = 3, $x = 2, $y =95, $htmlprint_date, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
						}
						
						if(ceil(($key+$page_break_key+1)/13)==$page_index && $key>=$first_page_sku_num)
						{
							$no = ($key+$page_break_key)-($page_index-1)*13+1;
							$num_no = $key + 1;
							$sql1 = 'name_cn,shelf_code';
                    		$myproduct = $CI->product_model->fetch_product_by_sku($sku, $sql1);
							$htmlproduct_list ='';
							$htmlproduct_list = <<<EOD
<span style="white-space:nowrap;font-size:9;">({$num_no}) {$myproduct->shelf_code}-{$sku}-{$myproduct->name_cn}*{$qties[$key]}</span>
EOD;
							$this->pdf->writeHTMLCell($w = 98, $h = 3, $x = 1, $y = 10 + 5 * $no, $htmlproduct_list, $border = 0, $ln = 0, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
						}
					}
				}
				if(in_array(strtoupper($order->is_register),array('CHR','CHS','HKR','HKS','MYR','MYS','SGR','SGS')))
				{
					$big_is_register=strtoupper($order->is_register);
					$this->pdf->AddPage();
					$return_post_img = <<<EOD
<img width="99mm" height="99mm" src="/static/images/returnpost/{$big_is_register}.jpg"/>
EOD;
					$this->pdf->writeHTMLCell($w = 99, $h = 99, $x = 0, $y = 0, $return_post_img, $border = 0, $ln = 0, $fill = 0, $reseth = true, $align = 'C', $autopadding = false);
				}
				
			}
			elseif($list_type==2)
			{/*为wish pi excel增加一行*/
				$skus = explode(',', $order->sku_str);
				$qties = explode(',', $order->qty_str);
				$price='';
				$discount_price='';
				$total_cost=0;
				foreach($skus as $key=>$sku)
				{
					$sql1 = 'sale_price';
					$myproduct = $CI->product_model->fetch_product_by_sku($sku, $sql1);
					$price.=$myproduct->sale_price;
					$discount_price.=price($myproduct->sale_price*0.961);
					$total_cost+=$myproduct->sale_price*0.961*$qties[$key];
					if(($key+1)!=count($skus))
					{
						$price.=',';
						$discount_price.=',';
					}
				}
				$total_cost+=1.99;
				$all_total_cost+=$total_cost;
				$data[] = array(
						 $order->created_at,//Transaction Date
						 $order->transaction_id,//Transaction ID
						 $order->item_title_str,//'Product',
						 $order->sku_str,//'SKU',
						 $order->qty_str,//'Quantity',
						 $order->name,//'Name',
						 '1.99',//Shipping Cost',
						 $order->item_no,//'ERP order number',
						 $price,//'Price',
						 $discount_price,//'Discount Price',
						 price($total_cost),//'Total Cost',
						 );
			}
			elseif($list_type==3)
			{/*添加一页A4  pdf打印*/
				$this->pdf->AddPage('P','A4');
				//$skus = explode(',', $order->sku_str);
				//$qties = explode(',', $order->qty_str);
				$print_date = date('Y.m.d');
				$user=fetch_user_name_by_id(fetch_user_id_by_login_name($order->input_user));
				$country_cn_name=get_country_name_cn($order->country);
				$html8= <<<EOD
<table width="100%" border="1" cellspacing="0" cellpadding="0" style="font-family:Arial, Helvetica, sans-serif;font-size:12;">
<tr style="font-family:droidsansfallback;font-size:12;"><td>业务员</td><td>{$user}</td><td>订单识别号</td><td>{$order->item_no}</td><td colspan="2" rowspan="3">备注：{$order->note}</td></tr>
EOD;
$html8.= <<<EOD
<tr style="font-family:droidsansfallback;font-size:12;"><td>客户名称</td><td>{$order->name}</td><td>国家</td><td>{$country_cn_name}</td></tr>
<tr style="font-family:droidsansfallback;font-size:12;"><td>运输方式</td><td>{$order->is_register}</td><td>日期</td><td>{$print_date}</td></tr>
<tr style="font-family:droidsansfallback;font-size:12;"><td>货架号</td><td>SKU</td><td>数量</td><td>图片</td><td>中文名称</td><td>单价</td></tr>
EOD;
				foreach($skus as $key=>$sku)
				{
					$sql1 = 'name_cn,shelf_code,sale_price,image_url,price';
					$myproduct = $CI->product_model->fetch_product_by_sku($sku, $sql1);
					$image="";
					
					if($myproduct->image_url!=''&&$myproduct->image_url!=NULL&&$myproduct->image_url!='none')
					{
						if(strpos($myproduct->image_url, 'http://') !== false)
						{
							$url=$myproduct->image_url;
						}else{
							$url= 'http://erp.screamprice.com'.$myproduct->image_url;
						}
						//$header= get_headers($url);
						if($this->img_exits($url))
						{
							$arr = getimagesize($url);
							if(count($arr)>0)
							{
								$image='<img src="'.$myproduct->image_url.'" border="0" height="15mm" width="15mm" />';
							}
						}
					}else{
						$image='<img src="http://erp.screamprice.com/static/images/404-error.png" border="0" height="25mm" width="25mm" />';
					}

					
					$html8.= <<<EOD
<tr style="font-family:droidsansfallback;font-size:12;height:15mm;"><td style="font-family:droidsansfallback;font-size:12;height:15mm;">{$myproduct->shelf_code}</td><td style="font-family:droidsansfallback;font-size:12;height:15mm;">{$sku}</td><td style="font-family:droidsansfallback;font-size:12;height:15mm;">{$qties[$key]}</td><td style="font-family:droidsansfallback;font-size:12;height:15mm;">{$image}</td><td style="font-family:droidsansfallback;font-size:12;height:15mm;">{$myproduct->name_cn}</td><td style="font-family:droidsansfallback;font-size:12;height:15mm;">{$myproduct->sale_price}</td></tr>
EOD;
//if($sku=='YXDS0001'){break;}
				}
$html8.= <<<EOD
</table>
EOD;
				$this->pdf->writeHTMLCell($w=200, $h=0, $x=5, $y=5, $html8, $border=1, $ln=0, $fill=0, $reseth=true, $align='L', $autopadding=false);
				//$this->pdf->writeHTML($html8, true, false, true, false, '');
			}
		}
		/*保存文件*/
		if($list_type==1)
		{
			$filename = "order_print_10x10_" . date("Ymd") . ".pdf";
			$this->pdf->Output($filename, 'D');
		}
		elseif($list_type==2)
		{
			$data[] = array(
						 ' ',//Transaction Date
						 ' ',//Transaction ID
						 ' ',//'Product',
						 ' ',//'SKU',
						 ' ',//'Quantity',
						 ' ',//'Name',
						 ' ',//Shipping Cost',
						 ' ',//'ERP order number',
						 ' ',//'Price',
						 ' ',//'Discount Price',
						 price($all_total_cost),//'Total Cost',
						 );
			$this->excel->array_to_excel($data, $head, 'wish_pi_list_' . date('Y-m-d'));
		}
		elseif($list_type==3)
		{
			$filename = "order_print_a4_" . date("Ymd") . ".pdf";
			$this->pdf->Output($filename, 'D');
		}
    }

    public function shipping_confirmation() {
        $data = array(
            'wait_confirmation_count' => $this->order_model->fetch_order_count('wait_for_shipping_confirmation'),
            'epacket_count' => $this->order_model->fetch_epacket_order_count(),
        );

        $this->template->write_view('content', 'shipping/deliver_management/shipping_confirmation_entry', $data);
        $this->template->add_js('static/js/ajax/shipping.js');
        $this->template->render();
    }

    public function before_late_print_label() {
        $this->enable_search('order');
        $this->enable_sort('order');

        $code = 'stock';
        $priority = fetch_user_priority_by_system_code($code);

        $stock_user_id = NULL;
        if (!$this->is_super_user() && $priority == 1) {
            $stock_user_id = get_current_user_id();
        }
		$not_shipping_codes=array('H','EUB');

        $orders = $this->order_model->fetch_all_wait_for_shipping_label_orders('wait_for_shipping_confirmation', NULL, $stock_user_id,$not_shipping_codes);

        $all_stock_users = $this->user_model->fetch_users_by_system_code('stock');

        $all_stock_user_ids = array('' => lang('please_select'));
        foreach ($all_stock_users as $user) {
            $all_stock_user_ids[$user->u_id] = $user->u_name;
        }
		$shipping_types = $this->get_shipping_types();
		$countries=$this->get_countries_list();

        $data = array(
            'orders' => $orders,
            'label_type' => 'before_late_print_label',
            'all_stock_user_ids' => $all_stock_user_ids,
            'stock_user_id' => $stock_user_id,
			'shipping_types' => $shipping_types,
			'countries'=>$countries,
        );

        $this->template->write_view('content', 'shipping/deliver_management/wait_for_shipping_label', $data);
        $this->template->add_js('static/js/ajax/shipping.js');
        $this->template->render();
    }

    public function before_late_print_label_abroad() {
        $this->enable_search('order');
        $this->enable_sort('order');

        $code = 'stock';
        $priority = fetch_user_priority_by_system_code($code);

        $stock_user_id = NULL;
        if (!$this->is_super_user() && $priority == 1) {
            $stock_user_id = get_current_user_id();
        }

        $cky_shipping_codes = $this->shipping_code_model->cky_fetch_all_shipping_codes();

        $orders = $this->order_model->fetch_all_wait_for_shipping_label_abroad_orders('wait_for_shipping_confirmation', NULL, $stock_user_id, $cky_shipping_codes);

        $all_stock_users = $this->user_model->fetch_users_by_system_code('stock');

        $all_stock_user_ids = array('' => lang('please_select'));
        foreach ($all_stock_users as $user) {
            $all_stock_user_ids[$user->u_id] = $user->u_name;
        }

        $data = array(
            'orders' => $orders,
            'label_type' => 'before_late_print_label',
            'all_stock_user_ids' => $all_stock_user_ids,
            'stock_user_id' => $stock_user_id,
        );

        $this->template->write_view('content', 'shipping/deliver_management/wait_for_shipping_label_abroad', $data);
        $this->template->add_js('static/js/ajax/shipping.js');
        $this->template->render();
    }
	public function img_exits($url)
	{
    	$head=@get_headers($url);
        if(is_array($head)) {
                return true;
        }
        return false;
	}
    public function before_make_order_shipped() {
        if (!$this->input->is_post()) {
            return;
        }
        $type = $this->input->post('type');
        $value = $this->input->post('value');

        switch ($type) {
            case 'bar_code':
                $order = $this->order_model->get_order($value);
                break;
            case 'item_no':
                $order = $this->order_model->get_order_with_item_no($value);
                break;
        }

        $shipping_weight = '';
        if ($order) {
            $qties = explode(',', $order->qty_str);
            if (count($qties) == 1 && array_sum($qties) == 1) {
                $shipping_weight = $this->product_model->fetch_product_total_weight_by_sku($order->sku_str);
            }
        }
        $local_shipping_codes = $this->shipping_code_model->fetch_local_shipping_codes();
        $local_shipping = array();
        foreach($local_shipping_codes as $local_shipping_code) {
            $local_shipping[$local_shipping_code->code] = $local_shipping_code->code;
        }
        
        $data = array(
            'order' => $order,
            'shipping_weight' => $shipping_weight,
            'local_shipping' => $local_shipping,
        );
        $this->load->view('shipping/deliver_management/before_shipping_confirmation', $data);
    }

    public function make_order_shipped() {
        if (!$this->input->is_post()) {
            return;
        }
        $order_id = $this->input->post('order_id');
        $is_register = strtoupper($this->input->post('is_register'));
        $shipping_remark = $this->input->post('shipping_remark');
        $packet_count = $this->input->post('packet_count');
        $weight = 0;
        $sub_weights = array();
        $sub_track_numbers = array();
        for ($i = 0; $i < $packet_count; $i++)
        {
            if ($this->input->post('weight_' . $i))
            {
                $sub_weight = $this->input->post('weight_' . $i);
                // weight should larger than 0.
                if ($sub_weight <= 0) {
                    echo $this->create_json(0, lang('a_number_greater_than_zero'));

                    return;
                }
                $sub_track_number = '';
                if ($this->input->post('track_number_' . $i))
                {
                    $sub_track_number = $this->input->post('track_number_' . $i);
                }
                $sub_weights[] = $sub_weight;
                $sub_track_numbers[] = strtoupper($sub_track_number);
                $weight += $sub_weight;
            }
        }
        $sub_weight_str = trim(implode(',', $sub_weights), ',');
        $track_number = trim(implode(',', $sub_track_numbers), ',');

        if (empty($shipping_remark)) {
            $shipping_remark = '';
        }

        $save_register = $is_register;

        $order = $this->order_model->get_order($order_id);
        $new_item_no = change_item_register($order->item_no, $order->is_register, $is_register);
        $user_name = $this->get_current_user_name();

        $remark = $order->sys_remark;
        $remark .= sprintf(lang('confirm_shipped_remark'), date('Y-m-d H:i:s'), $user_name);

        /* double check the order status
         * if the order status is not wait for shipping confirmation,
         * ignore it!
         */
        if ($order->order_status != 0 && $order->order_status != $this->order_statuses['wait_for_shipping_confirmation']) {
            $order_status = lang('order_status_is') . lang(fetch_status_name('order_status', $order->order_status));
            if ($is_customer) {
                echo $this->create_json(0, $order_status);

                return;
            }
        }

        /*
         * Epacket:
         */
        if (strtoupper($is_register) == 'H') {
            /* check if there is any available ebay transaction id, or return false */
            $paypal_transaction_id = $order->transaction_id;
            $item_ids = explode(',', trim($order->item_id_str, ','));
            $item_ids = array_unique($item_ids);
            foreach ($item_ids as $item_id)
            {
                if ( ! $this->epacket_model->ebay_transaction_id_exists($item_id, $paypal_transaction_id)) 
                {
                    echo $this->create_json(0, lang('no_ebay_transaction_id_info'));
 
                    return;
                }
            }

            if ($order->ship_weight && $order->ship_confirm_user)
            {
                echo $this->create_json(0, lang('shipping_weight_exists_no_need_try_again'));

                return;
            }

            $data = array(
                'descript' => $shipping_remark,
                'ship_weight' => $weight,
                'sub_ship_weight_str' => $sub_weight_str,
                'is_register' => $is_register,
                'item_no' => $new_item_no,
                'ship_confirm_user' => $user_name,
				'ship_confirm_date'=> date('Y-m-d H:i:s'),
            );
            $this->order_model->update_order_information($order_id, $data);

            $data = array(
                'order_id' => $order_id,
                'transaction_id' => $paypal_transaction_id,
                'input_user' => get_current_user_id(),
            );
            $this->epacket_model->save_epacket_confirm_list($data);
            $this->script->fetch_epacket_track_number(array('order_id' => $order_id));
			//根据订单id扣取库存
			$this->product_model->update_product_stock_count_by_order_id($order_id);

            return;
        }elseif(strtoupper($is_register) == 'EUB'){
			/*线下eub*/
			$data = array(
                //'descript' => $shipping_remark,
                'ship_weight' => $weight,
                //'sub_ship_weight_str' => $sub_weight_str,
                //'is_register' => $is_register,
                //'item_no' => $new_item_no,
				'order_status' => $this->order_statuses['wait_for_feedback'],
				'ship_confirm_date' => date('Y-m-d H:i:s'),
                'ship_confirm_user' => $user_name,
				'sys_remark' => $remark,
            );
            $this->order_model->update_order_information($order_id, $data);
			$this->product_model->update_product_stock_count_by_order_id($order_id);

            $data = array(
                'order_id' => $order_id,
                'input_user' => get_current_user_id(),
            );
			
            $this->epacket_model->save_specification_epacket_confirm_list($data);
            $this->script->fetch_specification_epacket_track_number(array('order_id' => $order_id));
		}else {
            $data = array(
                'descript' => $shipping_remark,
                'ship_weight' => $weight,
                'sub_ship_weight_str' => $sub_weight_str,
                'is_register' => $is_register,
                'item_no' => $new_item_no,
                'ship_confirm_user' => $user_name,
                'ship_confirm_date' => date('Y-m-d H:i:s'),
                'order_status' => $this->order_statuses['wait_for_feedback'],
                'sys_remark' => $remark,
            );

            // use the first time shipping confirmation date
            if (!empty($order->ship_confirm_date)) {
                //unset($data['ship_confirm_date']);
            }

            if ($track_number) {
                $data['track_number'] = $track_number;
            }
			$this->ebay_order_model->save_wait_complete_sale($order_id);
        	$this->order_model->update_order_information($order_id, $data);
			//根据订单id扣取库存
			$this->product_model->update_product_stock_count_by_order_id($order_id);
        }
		

		
        // notify customer with email in another process
        /*$this->events->trigger(
                'shipping_confirmation_after',
                array(
                    'order_id' => $order_id,
                )
        );*/
    }

    public function epacket($year = NULL, $month = NULL, $key = NULL) {
        $prefs = array(
            'start_day' => 'saturday',
            'month_type' => 'long',
            'day_type' => 'short',
            'show_next_prev' => TRUE,
            'next_prev_url' => 'shipping/deliver_management/epacket'
        );

        $this->load->library('calendar', $prefs);
        $unconfirmed_orders = $this->epacket_model->get_unconfirmed_orders();
        $unconfirmed_count = count($unconfirmed_orders); // $this->epacket_model->fetch_unconfirmed_count();
        $confirmed_count = $this->epacket_model->fetch_today_confirm_count();
        $part_confirmed_count = $this->epacket_model->fetch_today_confirm_count(TRUE);  // no downloaded
        $print_no_confirmed = $this->epacket_model->fetch_print_no_confirmed();
		$undownload_orders=$this->epacket_model->get_undownload_orders();
		
		$confirmed_ems_count = $this->epacket_model->fetch_today_confirm_ems_count();
		$part_confirmed_ems_count = $this->epacket_model->fetch_today_confirm_ems_count(TRUE);  // no downloaded
		$unconfirmed_ems_orders = $this->epacket_model->get_unconfirmed_ems_orders();
		$unconfirmed_ems_count = count($unconfirmed_ems_orders); 
		$print_no_ems_confirmed = $this->epacket_model->fetch_ems_print_no_confirmed();
		$undownload_ems_orders=$this->epacket_model->get_undownload_ems_orders();

        if (!($year && $month)) {
            $year = date('Y');
            $month = date('m');
        }
        $data = array(
            'confirmed_count' => $confirmed_count,
            'part_confirmed_count' => $part_confirmed_count,
            'unconfirmed_count' => $unconfirmed_count,
			'print_no_confirmed'  => $print_no_confirmed,
			'undownload_orders'  => $undownload_orders,
			
			'confirmed_ems_count' => $confirmed_ems_count,
            'part_confirmed_ems_count' => $part_confirmed_ems_count,
            'unconfirmed_ems_count' => $unconfirmed_ems_count,
			'print_no_ems_confirmed'  => $print_no_ems_confirmed,
			'undownload_ems_orders'  => $undownload_ems_orders,
			
            'unconfirmed_orders' => $unconfirmed_orders,
            'calendar' => $this->calendar,
            'year' => $year,
            'month' => $month,
            
        );
        if ($this->order_model->is_get_track_number_stop()) {
            $this->template->write_view('content', 'shipping/epacket/track_number', $data);
            $this->template->add_js('static/js/ajax/epacket.js');
            $this->template->render();
        } else {
            $this->template->write_view('content', 'shipping/epacket/track_number_running', $data);
            $this->template->add_js('static/js/ajax/epacket.js');
            $this->template->render();
        }
    }

    public function add_order() {
        $this->order_model->enable_get_track_number();

        $orders = $this->epacket_model->fetch_unconfirmed_list(NULL);
        $counter = 0;
        foreach ($orders as $order) {
            if ($this->order_model->is_get_track_number_stop()) {
                break;
            }
            $shipped = $this->order_model->check_order_shipped_or_not($order->id);
            if ($shipped) {
                continue;
            }
            $this->script->fetch_epacket_track_number(array('order_id' => $order->id));

            if ((++$counter/ 20)  == 0)
            {
                sleep(60);
            }
        }
        $this->order_model->reset_get_track_number();
    }

    public function stop_add_order() {
        $this->order_model->reset_get_track_number();
    }

    public function fetch_unconfirmed_count() {
        $unconfirmed_orders = $this->epacket_model->get_unconfirmed_orders();
        $unconfirmed_count = count($unconfirmed_orders); // $this->epacket_model->fetch_unconfirmed_count();

        echo $unconfirmed_count;
    }

    public function remove_order_from_epacket($order_id) {
        $this->epacket_model->remove_order_from_epacket($order_id);

        header('Location: ' . site_url('shipping/deliver_management/epacket'));
    }

    public function download_pdf($date = NULL, $key = NULL) {
		$timeStamp = strtotime($date);
		$date = date("Y-m-d", $timeStamp);
        $this->process_download_pdf($date, $key);
    }

    public function download_part_pdf($date = NULL, $key = NULL) {
        $this->process_download_pdf($date, $key, TRUE);
    }

    private function process_download_pdf($date = NULL, $key = NULL, $part = FALSE) {
        require_once APPPATH . 'libraries/pdf/PDFMerger.php';

        $pdf = new PDFMerger;

        if (!($date)) {
            $date = date('Y-m-d');
        }

        $pdf_folder = "/var/www/html/mallerp/static/pdf/$date/";
        $input_user = get_current_user_id();

        $priority = fetch_user_priority_by_system_code('shipping');
        // director? then set input_user as FALSE, no need to filter it
        if ($priority > 1) {
            $input_user = FALSE;
        }
        $confirmed_list = $this->epacket_model->fetch_confirmed_list($date, $input_user, $part);
        if (empty($confirmed_list) OR !file_exists($pdf_folder)) {
            echo 'No pdf for ' . $date;
            return;
        }

        foreach ($confirmed_list as $order) {
            $transaction_id = $order->transaction_id;
			$track_number = $order->track_number;
            $pdf_url = $pdf_folder . $transaction_id . '.pdf';
			$sku_pdf_url = $pdf_folder . 'sku_list_'.$track_number . '.pdf';
            if ( ! file_exists($pdf_url))
            {
                continue;
            }
            $pdf->addPDF($pdf_url, 'all');
            $data = array(
                'downloaded' => 1,
            );
            $this->epacket_model->update_confirmed_list($order->id, $data);
			if ( ! file_exists($sku_pdf_url))
            {
                continue;
            }
			$pdf->addPDF($sku_pdf_url, 'all');
        }
        $pdf->merge('download', "epacket $date.pdf");
    }

    public function give_order_back() {
        $order_id = $this->input->post('order_id');
        $remark = $this->input->post('remark');
        $is_customer = $this->input->post('is_customer');

        $order = $this->order_model->get_order($order_id);
        $data = array(
            'order_status' => $this->order_statuses['holded'],
            'descript' => get_current_user_name() . ' ' . lang('give_order_back') . ': ' . $remark,
        );

        if (!in_array($order->order_status, array($this->order_statuses['wait_for_purchase'], $this->order_statuses['wait_for_shipping_label'],$this->order_statuses['not_handled']))) {
            $order_status = lang('order_status_is') . lang(fetch_status_name('order_status', $order->order_status));
            if ($is_customer) {
                echo $this->create_json(0, $order_status);

                return;
            }
        }

        // shipping department
        /*if (!$is_customer) {
            if ($order->order_status != $this->order_statuses['wait_for_shipping_confirmation']) {
                echo $this->create_json(0, $order_status);

                return;
            }
            $user_name = get_current_login_name();
            $type_extra = $user_name . '/' . date('Y-m-d H:i:s');

            $this->product_model->update_product_stock_count_by_order_id($order_id, 'label_instock', $type_extra, FALSE);
        }*/

        try {
            if ($is_customer) {
                $this->order_model->update_order_information($order_id, $data);
            } else {
                $data['item_no'] = make_returned_item_no($order->item_no, $order->is_register);
                $this->order_model->renew_order($order_id, $data);
                /* remove dead data from epacket_confirm_list table */
                $this->epacket_model->remove_order_from_epacket($order_id);
            }
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
        echo $this->create_json(1, lang('ok'));
        $order = $this->order_model->get_order($order_id);
        $this->events->trigger(
                'give_order_back_after',
                array(
                    'type' => 'give_order_back',
                    'click_url' => site_url('order/regular_order/view_order'),
                    'content' => sprintf(lang('give_order_back_notice'), $order->item_no),
                    'owner_id' => get_current_user_id(),
                )
        );
    }

    public function force_change() {
        if (!$this->input->is_post()) {
            return;
        }
        $order_id = $this->input->post('order_id');
        $order = $this->order_model->force_change($order_id);
        echo $this->create_json(1, lang('ok'));
    }

    public function update_shipping_information() {
        $data = array(
            'wait_confirmation_count' => $this->order_model->fetch_order_count('wait_for_shipping_confirmation'),
            'epacket_count' => $this->order_model->fetch_epacket_order_count(),
        );

        $this->template->write_view('content', 'shipping/deliver_management/update_shipping_information_entry', $data);
        $this->template->add_js('static/js/ajax/shipping.js');
        $this->template->render();
    }

    public function before_update_shipping_information() {
        if (!$this->input->is_post()) {
            return;
        }
        $type = $this->input->post('type');
        $value = $this->input->post('value');
        switch ($type) {
            case 'bar_code':
                $order = $this->order_model->get_order($value);
                break;
            case 'item_no':
                $order = $this->order_model->get_order_with_item_no($value);
                break;
        }
        $data = array(
            'order' => $order,
        );
        $this->load->view('shipping/deliver_management/update_shipping_information', $data);
    }

    public function give_order_back_to_shipping() {
        $order_id = $this->input->post('order_id');
        $is_register = $this->input->post('is_register');
        $shipping_remark = $this->input->post('shipping_remark');
        $track_number = $this->input->post('track_number');
        $weight = $this->input->post('weight');

        $item = $this->order_model->get_order_item($order_id);
        $new_item_no = change_item_register($item->item_no, $item->is_register, $is_register);
        $sys_remark = $item->sys_remark . ',' . sprintf(lang('feedback_to_shipping_remark'), get_current_time(), get_current_login_name());

        $data = array(
            'is_register'   => $is_register,
            'item_no'       => $new_item_no,
            'ship_weight'   => '',
            'track_number'  => strtoupper($track_number),
            'order_status'  => $this->order_statuses['wait_for_shipping_confirmation'],
            'descript'      => $shipping_remark,
            'sys_remark'    => $sys_remark,
        );

        try {
			$user_name = get_current_login_name();
            $type_extra = $user_name . '/' . date('Y-m-d H:i:s');

            $this->product_model->update_product_stock_count_by_order_id($order_id, 'label_instock', $type_extra, FALSE);
            $this->order_model->update_order_information($order_id, $data);
        } catch (Exception $e) {
            echo lang('error_msg');
            $this->ajax_failed();
        }
    }

    public function print_express_list($id)
    {
        $order = $this->order_model->fetch_print_express_infos($id);
        $data = array('order' => $order);
        switch($order->is_register)
        {
            case "YD":
            $this->load->view('shipping/print_express_list/print_yd_express_list',$data);
            break;
            case "D":
            $this->load->view('shipping/print_express_list/print_dhl_express_list',$data);
            break;
            case "Y":
            $this->load->view('shipping/print_express_list/print_yt_express_list',$data);
            break;
            case "S":
            $this->load->view('shipping/print_express_list/print_sf_express_list',$data);
            break;
            case "E":
            $this->load->view('shipping/print_express_list/print_ems_e_express_list',$data);
            break;
            case "EZ":
            $this->load->view('shipping/print_express_list/print_ems_ez_express_list',$data);
            break;
        }
    }

    public function edit_save_order_ship_remark()
    {
        
        $cur_time = get_current_time();
        $cur_date = date("Y-m-d");
        $id = $this->order_shipping_record_model->fetch_id_of_record();
        $data = array(
            'date'                    => $cur_date,
            'stock_note'              => trim($this->input->post('stock_remark')),
            'shipping_note'           => trim($this->input->post('ship_remark')),
            'created_date'            => $cur_time,
       );

       try
       {
          if($id)
          {
              $this->order_shipping_record_model->delete_ship_record_remark($id);
              $this->order_shipping_record_model->save_ship_record_remark($data);
              echo $this->create_json(1, lang('remark_saved'));
          }
          else
          {
              $this->order_shipping_record_model->save_ship_record_remark($data);
              echo $this->create_json(1, lang('remark_saved'));
          }

       }
       catch(Exception $e)
       {
            echo lang('error_msg');
            $this->ajax_failed();
       }

    }

     public function print_all_express_list() {
       if (!$this->input->is_post()) {
            return;
        }

        $post_keys = array_keys($_POST);
        $order_ids = array();

        foreach ($post_keys as $key) {
            if (strpos($key, 'checkbox_select_') === 0) {
                $order_ids[] = $_POST[$key];
            }
        }
        if (!$order_ids) {
            return;
        }

        $orders = array();
        $user_name = $this->get_current_user_name();

        /* save sku => qty */
        $products = array();
        $all_register = array();
        $data = array('order_ids' => $order_ids);
        foreach ($order_ids as $order_id) {
            $order = $this->order_model->fetch_print_express_infos($order_id);
            $all_register[] = $order->is_register;
        }

        for($i=1; $i<count($all_register); $i++) {
            if($all_register[0] != $all_register[$i]) {
                exit(lang('print_all_list_wrong'));
            }
        }
        
        switch($all_register[0])
        {
            case "YD":
            $this->load->view('shipping/print_express_list/print_yd_express_list_all',$data);
            break;
            case "D":
            $this->load->view('shipping/print_express_list/print_dhl_express_list_all',$data);
            break;
            case "Y":
            $this->load->view('shipping/print_express_list/print_yt_express_list_all',$data);
            break;
            case "S":
            $this->load->view('shipping/print_express_list/print_sf_express_list_all',$data);
            break;
            case "E":
            $this->load->view('shipping/print_express_list/print_ems_e_express_list_all',$data);
            break;
            case "EZ":
            $this->load->view('shipping/print_express_list/print_ems_ez_express_list_all',$data);
            break;
        }
    }
	public function change_order_shiped() {
		if (!$this->input->is_post()) {
            return;
        }


		$data = array();

        $post_keys = array_keys($_POST);
        $order_ids = array();

        foreach ($post_keys as $key) {
            if (strpos($key, 'checkbox_select_') === 0) {
                $order_ids[] = $_POST[$key];
            }
        }

        $orders = array();
        $user_name = $this->get_current_user_name();

        /* save sku => qty */
        $products = array();

        foreach ($order_ids as $order_id) {
            $order = $this->order_model->get_order($order_id);

            /* double check the order status
             * if the order status is not wait for shipping confirmation,
             * ignore it!
             */

            if ($order->order_status != $this->order_statuses['wait_for_shipping_confirmation']) {
                continue;
            }

            $skus = explode(',', $order->sku_str);
            $qties = explode(',', $order->qty_str);
            $j = 0;
            $error = FALSE;
            foreach ($skus as $sku) {
                if (isset($products[$sku])) {
                    $products[$sku] += $qties[$j];
                } else {
                    $products[$sku] = $qties[$j];
                }

                $j++;
            }
			$weight=$this->order_model->get_order_whole_weight($order->id);
			$remark = $order->sys_remark;
			$remark .= sprintf(lang('confirm_shipped_remark'), date('Y-m-d H:i:s'), $user_name);
			$is_register=$order->is_register;
			
		/*
         * Epacket:
         */
        if (strtoupper($is_register) == 'H') {
            /* check if there is any available ebay transaction id, or return false */
            $paypal_transaction_id = $order->transaction_id;
            $item_ids = explode(',', trim($order->item_id_str, ','));
            $item_ids = array_unique($item_ids);
            foreach ($item_ids as $item_id)
            {
                if ( ! $this->epacket_model->ebay_transaction_id_exists($item_id, $paypal_transaction_id)) 
                {
					echo "***1****";
                    echo $this->create_json(0, lang('no_ebay_transaction_id_info'));
 
                    return;
                }
            }

            if ($order->ship_weight && $order->ship_confirm_user)
            {
				echo "***2****";
                echo $this->create_json(0, lang('shipping_weight_exists_no_need_try_again'));

                return;
            }

            $data = array(
                //'descript' => $shipping_remark,
                'ship_weight' => $weight,
                //'sub_ship_weight_str' => $sub_weight_str,
                //'is_register' => $is_register,
                //'item_no' => $new_item_no,
				'order_status' => $this->order_statuses['wait_for_feedback'],
				'ship_confirm_date' => date('Y-m-d H:i:s'),
                'ship_confirm_user' => $user_name,
				'sys_remark' => $remark,
            );
            $this->order_model->update_order_information($order_id, $data);
			$this->product_model->update_product_stock_count_by_order_id($order_id);

            $data = array(
                'order_id' => $order_id,
                'transaction_id' => $paypal_transaction_id,
                'input_user' => get_current_user_id(),
            );
			
            $this->epacket_model->save_epacket_confirm_list($data);
            $this->script->fetch_epacket_track_number(array('order_id' => $order_id));

            //return;
        }elseif(strtoupper($is_register) == 'EUB'){
			/*线下eub*/
			$data = array(
                //'descript' => $shipping_remark,
                'ship_weight' => $weight,
                //'sub_ship_weight_str' => $sub_weight_str,
                //'is_register' => $is_register,
                //'item_no' => $new_item_no,
				'order_status' => $this->order_statuses['wait_for_feedback'],
				'ship_confirm_date' => date('Y-m-d H:i:s'),
                'ship_confirm_user' => $user_name,
				'sys_remark' => $remark,
            );
            $this->order_model->update_order_information($order_id, $data);
			$this->product_model->update_product_stock_count_by_order_id($order_id);

            $data = array(
                'order_id' => $order_id,
                'input_user' => get_current_user_id(),
            );
			
            $this->epacket_model->save_specification_epacket_confirm_list($data);
			$this->ebay_order_model->save_wait_complete_sale($order_id);
            $this->script->fetch_specification_epacket_track_number(array('order_id' => $order_id));
		} else {
            $data = array(
                //'descript' => $shipping_remark,
                'ship_weight' => $weight,
                //'sub_ship_weight_str' => $sub_weight_str,
                //'is_register' => $is_register,
                //'item_no' => $new_item_no,
                'ship_confirm_user' => $user_name,
                'ship_confirm_date' => date('Y-m-d H:i:s'),
                'order_status' => $this->order_statuses['wait_for_feedback'],
                'sys_remark' => $remark,
            );

            // use the first time shipping confirmation date
            if (!empty($order->ship_confirm_date)) {
                //unset($data['ship_confirm_date']);
            }

            //if ($track_number) {
                //$data['track_number'] = $track_number;
            //}

            $this->order_model->update_order_information($order_id, $data);
			$this->ebay_order_model->save_wait_complete_sale($order_id);
			//根据订单id扣取库存
			$this->product_model->update_product_stock_count_by_order_id($order_id);
        }
			

			// notify customer with email in another process
			/*
			$this->events->trigger(
                'shipping_confirmation_after',
                array(
                    'order_id' => $order_id,
                )
			);*/
			
			
			/*
			$this->events->trigger(
            'complete_ebay_sale',
				array(
                'order_id' => $order_id,
				)
			);
			*/

            $orders[] = $this->order_model->get_order($order_id);
        }

        $type_extra = $user_name . '/' . date('Y-m-d H:i:s');
		/*订单清单扣库存
        foreach ($products as $sku => $qty) {
            $this->product_model->update_product_stock_count_by_sku($sku, $qty, TRUE, 'order_outstock', $type_extra,$stock_code);
        }*/
		
		
        $code = 'stock';
        $priority = fetch_user_priority_by_system_code($code);

        $all_stock_users = $this->user_model->fetch_users_by_system_code('stock');

        foreach ($all_stock_users as $user) {
            $all_stock_user_ids[$user->u_id] = $user->login_name;
        }
		
		
        $data = array(
            'orders' => $orders,
            'priority' => $priority,
            'all_stock_users' => $all_stock_user_ids,
            'current_user_id' => get_current_user_id(),
        );
		
		$this->template->write_view('content', 'shipping/deliver_management/change_order_shiped', $data);
        $this->template->render();

	}
	public function get_ebayorder_by_transaction_id($paypal_transaction_id) {
        $this->db->select('myebay_order_list.*');
        $this->db->from('myebay_order_list');
		$this->db->where('myebay_order_list.paypal_transaction_id', $paypal_transaction_id);
        $query = $this->db->get();
        return $query->result();
    }
	
	public function import_track_number() {
        $data = array(
            'error' => '',
        );
        $this->template->write_view('content', 'shipping/deliver_management/import_track_number', $data);
        $this->template->render();
    }
	function do_upload()
	{
		$config['upload_path'] = '/tmp/';
        $config['allowed_types'] = '*';
        $config['max_size'] = '100';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());

            $this->load->view('shipping/deliver_management/import_track_number', $error);
        }else{
			$data = array('upload_data' => $this->upload->data());
            $file_path = $data['upload_data']['full_path'];
            $before_file_arr = $this->excel->csv_to_array($file_path);

            
            $sueecss_counts = 0;
            $failure_counts = 0;
            $blank_counts = 0;
            $number = 1;
            $output_data = array();
			$i=0;
			

            foreach ($before_file_arr as $row)
            {
				$i++;
                //$output_data["$number"] = sprintf(lang('start_number_note'), $number);
                $data = array();
				if($i==1){continue;}
               
                if(count($row) < 1)
                {
                    //$output_data[] = $number.lang('no_data');
                    $blank_counts++;
                    $number++;
                    continue;
                }
                else
                {
					$ship_user = $this->get_current_login_name();
					
					$data=array();
					$order_id = $row[0];
					$order = $this->order_model->get_order($order_id);
					$order_status = fetch_status_id('order_status', 'wait_for_shipping_confirmation');
					$close_status = fetch_status_id('order_status', 'closed');
					if ($order->order_status!=$order_status||$order->order_status==$close_status||$order->is_register=='H'||$order->is_register=='EUB') {
						$failure_counts++;
						$number++;
            			continue;
        			}
					$remark = $order->sys_remark;
					$remark .= sprintf(lang('confirm_shipped_remark'), date('Y-m-d H:i:s'),$ship_user);
					$data = array(
                		'descript' => $ship_user.'import shipped!',
                		'ship_confirm_user' => $ship_user,
						'order_status' => $this->order_statuses['wait_for_feedback'],
						'ship_confirm_date' => date('Y-m-d H:i:s'),
						'sys_remark' => $remark,
						'ship_weight' => 0.05,
            		);
					if($row[1]!='')
					{
						$data['track_number']=$row[1];
					}
					if($row[2]!='')
					{
						$data['ship_weight']=$row[2];
					}
					$this->ebay_order_model->save_wait_complete_sale($order_id);
					$this->product_model->update_product_stock_count_by_order_id($order_id);
					$this->order_model->update_order_information($order_id, $data);
					
					$number++;
				}
			}/*end foreach*/
        
		}/*end if*/
			$output_data["total"] = sprintf(lang('total_count_result'), $number-1, $sueecss_counts, $failure_counts, $blank_counts);

            $data_page = array(
                'data' => $output_data,
            );

            $this->template->write_view('content', 'shipping/deliver_management/success', $data_page);
            $this->template->render();
	}
	public function import_shipping_cost() {
        $data = array(
            'error' => '',
        );
        $this->template->write_view('content', 'shipping/deliver_management/import_shipping_cost', $data);
        $this->template->render();
    }
	function import_shipping_cost_by_order_id()
	{
		$config['upload_path'] = '/tmp/';
        $config['allowed_types'] = '*';
        $config['max_size'] = '100';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());

            $this->load->view('shipping/deliver_management/import_shipping_cost', $error);
        }else{
			$data = array('upload_data' => $this->upload->data());
            $file_path = $data['upload_data']['full_path'];
            $before_file_arr = $this->excel->csv_to_array($file_path);

            
            $sueecss_counts = 0;
            $failure_counts = 0;
            $blank_counts = 0;
            $number = 1;
            $output_data = array();
			$i=0;
			

            foreach ($before_file_arr as $row)
            {
				$i++;
                //$output_data["$number"] = sprintf(lang('start_number_note'), $number);
                $data = array();
				if($i==1){continue;}
               
                if(count($row) < 1)
                {
                    //$output_data[] = $number.lang('no_data');
                    $blank_counts++;
                    $number++;
                    continue;
                }
                else
                {
					
					$data=array();
					$order_id = $row[0];
					if($row[1]!='')
					{
						$data['shipping_cost']=$row[1];
					}
					$this->order_model->update_order_information($order_id, $data);
					
					$number++;
					$output_data[$row[0]] = $row[1];
				}
			}/*end foreach*/
        
		}/*end if*/
			

            $data_page = array(
                'data' => $output_data,
            );

            $this->template->write_view('content', 'shipping/deliver_management/import_shipping_cost_success', $data_page);
            $this->template->render();
	}
	function import_shipping_cost_by_track_number()
	{
		$config['upload_path'] = '/tmp/';
        $config['allowed_types'] = '*';
        $config['max_size'] = '100';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());

            $this->load->view('shipping/deliver_management/import_shipping_cost', $error);
        }else{
			$data = array('upload_data' => $this->upload->data());
            $file_path = $data['upload_data']['full_path'];
            $before_file_arr = $this->excel->csv_to_array($file_path);

            
            $sueecss_counts = 0;
            $failure_counts = 0;
            $blank_counts = 0;
            $number = 1;
            $output_data = array();
			$i=0;
			

            foreach ($before_file_arr as $row)
            {
				$i++;
                //$output_data["$number"] = sprintf(lang('start_number_note'), $number);
                $data = array();
				if($i==1){continue;}
               
                if(count($row) < 1)
                {
                    //$output_data[] = $number.lang('no_data');
                    $blank_counts++;
                    $number++;
                    continue;
                }
                else
                {
					
					$data=array();
					$track_number = $row[0];
					if($row[1]!='')
					{
						$data['shipping_cost']=price($row[1]);
					}
					$this->order_model->update_order_by_track_number($track_number, $data);
					
					$number++;
					$output_data[$row[0]] = $row[1];
				}
			}/*end foreach*/
        
		}/*end if*/
			

            $data_page = array(
                'data' => $output_data,
            );

            $this->template->write_view('content', 'shipping/deliver_management/import_shipping_cost_success', $data_page);
            $this->template->render();
	}
	public function for_the_wish_orders()
    {
        if ( ! $this->input->is_post())
        {          
            $begin_time = date('Y-m-d'). ' ' . '00:00:00';
            $end_time = date('Y-m-d'). ' ' . '24:00:00';
        }
        else
        {
            $begin_time = $this->input->post('begin_time');
            $end_time = $this->input->post('end_time');
        }
        
        $data = array(
                'begin_time'          => $begin_time,
                'end_time'            => $end_time,
        );
        $this->template->write_view('content', 'shipping/deliver_management/for_the_wish_orders', $data);
        $this->template->render();
    }
	public function download_the_wish_orders()
	{
		$CI = & get_instance();
		$begin_time = $this->input->post('begin_time');
		$end_time = $this->input->post('end_time');
		$orders=$this->order_model->get_the_wish_orders($begin_time,$end_time);
		//var_dump($orders);
		/*excel表头*/
			$head = array(
            	'Transaction Date',
				'Transaction ID',
				'Product',
				'SKU',
				'Quantity',
				'Name',
				'Shipping Cost',
				'ERP order number',
				'Price',
				'Discount Price',
				'Total Cost',
        	);
			$all_total_cost=0;
		foreach($orders as $order)
		{
			/*为wish pi excel增加一行*/
				$skus = explode(',', $order->sku_str);
				$qties = explode(',', $order->qty_str);
				$price='';
				$discount_price='';
				$total_cost=0;
				foreach($skus as $key=>$sku)
				{
					$sql1 = 'sale_price';
					$myproduct = $CI->product_model->fetch_product_by_sku($sku, $sql1);
					$price.=isset($myproduct->sale_price)?$myproduct->sale_price:0;
					$discount_price.=price((isset($myproduct->sale_price)?$myproduct->sale_price:0)*0.961);
					$total_cost+=(isset($myproduct->sale_price)?$myproduct->sale_price:0)*0.961*$qties[$key];
					if(($key+1)!=count($skus))
					{
						$price.=',';
						$discount_price.=',';
					}
				}
				$total_cost+=1.99;
				$all_total_cost+=$total_cost;
				$data[] = array(
						 $order->created_at,//Transaction Date
						 $order->transaction_id,//Transaction ID
						 $order->item_title_str,//'Product',
						 $order->sku_str,//'SKU',
						 $order->qty_str,//'Quantity',
						 $order->name,//'Name',
						 '1.99',//Shipping Cost',
						 $order->item_no,//'ERP order number',
						 $price,//'Price',
						 $discount_price,//'Discount Price',
						 price($total_cost),//'Total Cost',
						 );
		}
		$data[] = array(
						 ' ',//Transaction Date
						 ' ',//Transaction ID
						 ' ',//'Product',
						 ' ',//'SKU',
						 ' ',//'Quantity',
						 ' ',//'Name',
						 ' ',//Shipping Cost',
						 ' ',//'ERP order number',
						 ' ',//'Price',
						 ' ',//'Discount Price',
						 price($all_total_cost),//'Total Cost',
						 );
			$this->excel->array_to_excel($data, $head, 'wish_pi_list_' . date('Y-m-d'));
	}
	public function down_order_excel($id)
	{
		//$this->load->library('PHPExcel');
		//$this->load->library('PHPExcel/IOFactory');
		require_once APPPATH . 'libraries/PHPExcel' . EXT;
		require_once APPPATH . 'libraries/PHPExcel/IOFactory' . EXT;
		require_once APPPATH . 'libraries/PHPExcel/Worksheet/Drawing' . EXT;
		
		
		$CI = & get_instance();
		$order = $this->order_model->get_order($id);//var_dump($order);die();
		if($order)
		{
			$print_date = date('Y.m.d');
			$user=fetch_user_name_by_id(fetch_user_id_by_login_name($order->input_user));
			$country_cn_name=get_country_name_cn($order->country);
			
			$objPHPExcel = new PHPExcel();
			
			$objPHPExcel->createSheet();//创建sheet
			$objPHPExcel->setActiveSheetIndex(0);
			$objActSheet = $objPHPExcel->getActiveSheet();
			
			$objPHPExcel->getProperties()->setCreator("mallerp")
							 ->setLastModifiedBy("mallerp")
							 ->setTitle("Ebay ShipOrder List")
							 ->setSubject("Ebay ShipOrder List")
							 ->setDescription("Mansea, Mallerp, zhaosenlin, 278203374, 7410992")
							 ->setKeywords("Mansea, Mallerp, zhaosenlin, 278203374, 7410992")
							 ->setCategory("mallerp");
			$objActSheet->getColumnDimension('E')->setWidth(20);
			$objActSheet->getColumnDimension('A')->setWidth(0);
			/*excel表头*/
			$objPHPExcel->getActiveSheet()->setCellValue('A1', '');
			$objPHPExcel->getActiveSheet()->setCellValue('A2', '');
			$objPHPExcel->getActiveSheet()->setCellValue('A3', '');
			$objPHPExcel->getActiveSheet()->setCellValue('A4', '');
			
			$objActSheet->getStyle('B1:B4')->getFont()->setSize(12);
			$objActSheet->getStyle('B1:B4')->getFont()->setBold(true);
			$objActSheet->getStyle('D1:D4')->getFont()->setBold(true);
			$objActSheet->getStyle('F1:F4')->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->mergeCells('H1:M4');
			
			$objPHPExcel->getActiveSheet()->setCellValue('B1', '业务员');
			$objPHPExcel->getActiveSheet()->setCellValue('C1', $user);
			$objPHPExcel->getActiveSheet()->setCellValue('D1', 'ERP订单号');
			$objPHPExcel->getActiveSheet()->setCellValue('E1', $order->item_no);
			$objPHPExcel->getActiveSheet()->setCellValue('F1', '日期');
			$objPHPExcel->getActiveSheet()->setCellValue('G1', $print_date);
			$objPHPExcel->getActiveSheet()->setCellValue('H1', '备注：'.$order->note);
			
			$objPHPExcel->getActiveSheet()->setCellValue('B2', '客户名称');
			$objPHPExcel->getActiveSheet()->setCellValue('C2', $order->name);
			$objPHPExcel->getActiveSheet()->setCellValue('D2', '国家');
			$objPHPExcel->getActiveSheet()->setCellValue('E2', $country_cn_name);
			$objPHPExcel->getActiveSheet()->setCellValue('F2', '运输方式');
			$objPHPExcel->getActiveSheet()->setCellValue('G2', $order->is_register);
			
			$objPHPExcel->getActiveSheet()->setCellValue('B3', '订单总额$');
			$objPHPExcel->getActiveSheet()->setCellValue('C3', $order->gross);
			$objPHPExcel->getActiveSheet()->setCellValue('D3', '运费$');
			$objPHPExcel->getActiveSheet()->setCellValue('E3', $order->shippingamt);
			$objPHPExcel->getActiveSheet()->setCellValue('F3', '付款方式');
			$objPHPExcel->getActiveSheet()->setCellValue('G3', $order->payment_type);
			
			$objPHPExcel->getActiveSheet()->setCellValue('B4', '订单总额￥');
			$objPHPExcel->getActiveSheet()->setCellValue('C4', $order->gross*$order->ex_rate);
			$objPHPExcel->getActiveSheet()->setCellValue('D4', '运费￥');
			$objPHPExcel->getActiveSheet()->setCellValue('E4', $order->shippingamt*$order->ex_rate);
			$objPHPExcel->getActiveSheet()->setCellValue('F4', '实际运费');
			$objPHPExcel->getActiveSheet()->setCellValue('G4', '');
			$objPHPExcel->getActiveSheet()->setCellValue('H4', '交易号');
			$objPHPExcel->getActiveSheet()->setCellValue('I4', $order->transaction_id);
			
			//$objPHPExcel->getActiveSheet()->mergeCells("F5":"G5":"H5");
			$objActSheet->getStyle('B5:M5')->getFont()->setSize(12);
			$objActSheet->getStyle('B5:M5')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('F5:H5');
			$objPHPExcel->getActiveSheet()->setCellValue('B5', '货架号');
			$objPHPExcel->getActiveSheet()->setCellValue('C5', 'SKU');
			$objPHPExcel->getActiveSheet()->setCellValue('D5', '数量');
			$objPHPExcel->getActiveSheet()->setCellValue('E5', '图片');
			$objPHPExcel->getActiveSheet()->setCellValue('F5', '中文名称');
			$objPHPExcel->getActiveSheet()->setCellValue('I5', '单价$');
			$objPHPExcel->getActiveSheet()->setCellValue('J5', '单价￥');
			$objPHPExcel->getActiveSheet()->setCellValue('K5', '总价￥');
			$objPHPExcel->getActiveSheet()->setCellValue('L5', '成本￥');
			$objPHPExcel->getActiveSheet()->setCellValue('M5', '利润￥');
			
			$skus = explode(',', $order->sku_str);
			$qties = explode(',', $order->qty_str);
			$item_prices = explode(',', $order->item_price_str);
			$i=5;
			foreach($skus as $key=>$sku)
			{
				$i++;
					$sql1 = 'name_cn,shelf_code,sale_price,image_url,price';
					$myproduct = $CI->product_model->fetch_product_by_sku($sku, $sql1);
					$image="";
					
					if($myproduct->image_url!=''&&$myproduct->image_url!=NULL&&$myproduct->image_url!='none')
					{
						if(strpos($myproduct->image_url, 'http://') !== false)
						{
							$image="/var/www/html/mallerp/static/images/404-error.png";
						}else{
							$image= '/var/www/html/mallerp'.$myproduct->image_url;
						}
						
					}else{
						$image="/var/www/html/mallerp/static/images/404-error.png";
					}//die($image);
					
					$objActSheet->getRowDimension($i)->setRowHeight(100);
					
					$objActSheet->getStyle('B'.$i.':M'.$i)->getFont()->setSize(12);
					$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':H'.$i);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $myproduct->shelf_code);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $sku);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $qties[$key]);
$objDrawing = new PHPExcel_Worksheet_Drawing();						
$objDrawing->setName('avatar');
$objDrawing->setDescription('avatar');
$objDrawing->setPath($image);
$objDrawing->setHeight(100);
$objDrawing->setWidth(100);
$objDrawing->setCoordinates('E'.$i);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					
					//$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, '图片');
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $myproduct->name_cn);
					$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, isset($item_prices[$key])?$item_prices[$key]:0);
					$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, isset($item_prices[$key])?$item_prices[$key]:0*$order->ex_rate);
					$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, isset($item_prices[$key])?$item_prices[$key]:0*$order->ex_rate*$qties[$key]);
					$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, '');
					$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, '');
			}
		}
		$filename = date("Y-m-d_H_i_s").'.xls';
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename='.$filename);
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	public function test()
	{
		$ids=array();
		foreach($ids as $id)
		{
			$this->product_model->update_product_stock_count_by_order_id($id);
			echo $id." ok !<br>";
		}
		
	}
	
}
?>
