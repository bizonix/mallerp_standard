<?php

require_once APPPATH . 'controllers/sale/sale' . EXT;

class Price extends Sale {

    public function __construct() {
        parent::__construct();

        $this->load->model('sale_model');
        $this->load->model('product_model');
        $this->load->model('product_packing_model');
        $this->load->model('order_model');
        $this->load->library('form_validation');
        $this->load->helper('validation');
        $this->load->model('fee_price_model');
        $this->load->model('shipping_company_model');
        $this->load->model('shipping_function_model');
        $this->load->model('shipping_subarea_model');
        $this->load->model('shipping_subarea_group_model');
        $this->load->model('shipping_type_model');
        $this->load->model('shipping_code_model');
        $this->load->helper('shipping_helper');
        $this->load->helper('db_helper');
    }

    public function calculate_price() {
        $paypals = $this->fee_price_model->fetch_all_paypal_cost();
        $pay_options = object_to_key_value_array($paypals, 'formula', 'name');
        $eshop_code_object = $this->fee_price_model->fetch_all_eshop_code();
        $eshop_codes = object_to_key_value_array($eshop_code_object, 'code', 'name');

        $sale_mode_object = $this->fee_price_model->fetch_all_sale_mode();
        $sale_modes = object_to_key_value_array($sale_mode_object, 'mode', 'name');
        $shipping_code_object = $this->shipping_code_model->fetch_all_shipping_codes();
        $shipping_types = array();
        $field = get_current_language() == 'english' ? 'name_en' : 'name_cn';
        foreach ($shipping_code_object as $item) {
            $shipping_types[$item->code] = $item->$field;
        }

        $data = array(
            'pay_options' => $pay_options,
            'eshop_codes' => $eshop_codes,
            'sale_modes' => $sale_modes,
            'shipping_types' => $shipping_types,
        );

        $this->template->write_view('content', 'sale/price/calculate_price', $data);
        $this->template->add_js('static/js/ajax/sale.js');
        $this->template->render();
    }

    public function proccess_calculating($suggest_price = NULL) {
        // don't calculate, just show the form
        if ($this->input->post('init')) {
            $eshop_code = $this->input->post('eshop_code');
            if ($eshop_code) {
                $currency_code = $this->fee_price_model->fetch_eshop_currency_code($eshop_code);
            } else {
                $currency_code = 'USD';
            }
            $data = array(
                'suggest_price' => 0,
                'buyer_shipping_cost' => 0,
                'list_fee' => 0,
                'trade_fee' => 0,
                'pay_fee' => 0,
                'shipping_cost' => 0,
                'shipping_type_name' => 0,
                'other_cost' => 0,
                'total_cost' => 0,
                'total_profit' => 0,
                'total_profit_rate' => 0,
                'currency_code' => $currency_code,
                'default_currency_code' => DEFAULT_CURRENCY_CODE,
            );
            return $this->load->view('sale/price/calculate_price_result', $data);
        }
        $product_count = $this->input->post('product_count');

        $total_price = 0;
        $total_weight = 0;
        $total_profit = 0;
		$max_length = 0;
        $max_width = 0;
        $total_height = 0;

        for ($i = 0; $i < $product_count; $i++) {
            $sku = trim($this->input->post('sku_' . $i));
            $product = $this->product_model->fetch_product_by_sku($sku);
            if (!$product) {
                echo $this->create_json(0, sprintf(lang('product_sku_doesnot_exists_with_sku'), $sku));
                return;
            }

            $qty = trim($this->input->post('qty_' . $i));
            if (!is_numeric($qty) || $qty <= 0) {
                echo $this->create_json(0, lang('qty_not_natural'));
                return;
            }
            $weight = trim($this->input->post('weight_' . $i));
            if (!numeric($weight) || $weight <= 0) {
                echo $this->create_json(0, lang('weight_format_error'));
                return;
            }
            $price = trim($this->input->post('price_' . $i));
            if (!numeric($price) || $price <= 0) {
                echo $this->create_json(0, lang('price_format_error'));
                return;
            }
            $profit = trim($this->input->post('profit_' . $i));
            if (!numeric($profit) || $profit <= 0 || $profit >= 1) {
                echo $this->create_json(0, lang('profit_format_error'));
                return;
            }
			$length = trim($this->input->post('length_' . $i));
			if($length>$max_length)
			{
				$max_length = $length;
			}
			$width = trim($this->input->post('width_' . $i));
			if($width>$max_width)
			{
				$max_width = $width;
			}
			$height = trim($this->input->post('height_' . $i));
			$total_height += $height;
            $total_price += $price * $qty;
            $total_weight += $weight * $qty;
            $total_profit += $profit;
        }
        $balance_profit = $total_profit / $product_count;
        $pay_option = $this->input->post('pay_option');
        $pay_discount = $this->input->post('pay_discount');
        $eshop_code = $this->input->post('eshop_code');
        $sale_mode = $this->input->post('sale_mode');
        $eshop_list_count = $this->input->post('eshop_list_count');
        $shipping_type = $this->input->post('shipping_type');
        $shipping_country = $this->input->post('shipping_country');
        if (empty($shipping_country)) {
            $shipping_country = DEFAULT_SHIPPING_COUNTRY;
        }
        $buyer_shipping_cost = $this->input->post('buyer_shipping_cost');
        $other_cost = $this->input->post('other_cost');
        $eshop_category = $this->input->post('eshop_category');

        if (!is_numeric($eshop_list_count) || $eshop_list_count <= 0) {
            echo $this->create_json(0, lang('eshop_list_count_format_error'));
            return;
        }
        if (!numeric($buyer_shipping_cost) || $buyer_shipping_cost < 0) {
            echo $this->create_json(0, lang('buyer_shipping_cost_format_error'));
            return;
        }
        if (!numeric($other_cost)) {
            echo $this->create_json(0, lang('other_cost_format_error'));
            return;
        }

        $eshop_list_fee_multiply = 1;
        if ($sale_mode == 'auction') {
            $bid_rate = $this->input->post('bid_rate');
            $eshop_list_fee_multiply = 1 + (100 - $bid_rate) / $bid_rate;
        }

        $input = array(
            'eshop_code' => $eshop_code,
            'buyer_shipping_cost' => $buyer_shipping_cost,
            'shipping_type' => $shipping_type,
            'shipping_country' => $shipping_country,
            'total_weight' => $total_weight,
            'sale_mode' => $sale_mode,
            'eshop_category' => $eshop_category,
            'suggest_price' => $suggest_price,
            'key' => $key,
            'total_price' => $total_price,
            'balance_profit' => $balance_profit,
            'eshop_list_count' => $eshop_list_count,
            'eshop_list_fee_multiply' => $eshop_list_fee_multiply,
            'pay_option' => $pay_option,
            'pay_discount' => $pay_discount,
            'other_cost' => $other_cost,
			'max_length' => $max_length,
			'max_width' => $max_width,
			'total_height' => $total_height,
        );

        $data = price_profit_rate($input);
        if (!is_array($data)) {
            echo $this->create_json(0, $data);
            return;
        }

        $this->load->view('sale/price/calculate_price_result', $data);
    }

    public function calculate_profit() {
        $suggest_price = $this->input->post('suggest_price');
        if ($suggest_price <= 0) {
            echo $this->create_json(0, lang('suggest_price_should_larger_than_0'));
            return;
        }

        $this->proccess_calculating($suggest_price, TRUE);
    }

    public function fetch_product_information() {
        $product_count = $this->input->post('product_count');

        $sql = 'pure_weight, fill_material_heavy, packing_material, total_weight, price, image_url,width,length,height';
        $result = array();
        for ($i = 0; $i < $product_count; $i++) {
            $sku_str = $this->input->post('sku_str_' . $i);
            $pos = strrpos($sku_str, "|");
            $index = substr($sku_str, $pos + 1);
            $sku = substr($sku_str, 0, strlen($sku_str) - strlen($index) - 1);

            $product = $this->product_model->fetch_product_by_sku($sku, $sql);
            if (!$product) {
                echo $this->create_json(0, sprintf(lang('product_sku_doesnot_exists_with_sku'), $sku));
                return;
            }

            $packing_weight = $this->product_packing_model->fetch_product_packing_weight($product->packing_material);
            $lowest_profit = $this->product_model->fetch_product_lowest_profit($sku);
            $result[] = array(
                'index' => $index,
                'total_weight' => $product->total_weight,
                'price' => $product->price,
				'length' => $product->length,
				'width' => $product->width,
				'height' => $product->height,
                'image_url' => $product->image_url,
                'lowest_profit' => $lowest_profit,
                'weight_more' => sprintf(lang('sale_price_weight_more'), $product->pure_weight, $packing_weight, $product->fill_material_heavy),
            );
        }
        
        $this->json_header();
        echo json_encode($result);
    }
    
    public function fetch_eshop_catalog() {
        $eshop_code = $this->input->post('eshop_code');

        $data = array(
            'categories' => $this->fee_price_model->fetch_categories_by_eshop_code($eshop_code),
        );

        $this->load->view('sale/price/eshop_catalog', $data);
    }

    private function _eval_fee($pay_option, $price) {
        $p = $price;
        if (empty($pay_option)) {
            return 0;
        }
        eval("\$fee = $pay_option;");
        return $fee;
    }

    private function _fetch_shipping_cost($weight, $shipping_type, $shipping_country) {
        $rule_matched = shipping_accepted_rule($shipping_country, $weight, $shipping_type);

        return $rule_matched;
    }

    public function make_pi() {

        $product_count = $this->input->post('product_count');
        $user_id = $this->get_current_user_id();
        $user_info = $this->user_model->fetch_user_by_id($user_id);

        for ($i = 0; $i < $product_count; $i++) {
            $sku = trim($this->input->post('sku_' . $i));
            $product = $this->product_model->fetch_product_by_sku($sku);

            if ( ! $product) {
                echo $this->create_json(0, sprintf(lang('product_sku_doesnot_exists_with_sku'), $sku));
                return;
            }

            $price = ($product->price)/0.75;
            $product_price[$i] = price(to_foreigh_currency('USD', $price));
            $product_sku[$i] = $product->sku;
            $product_image_url[$i] = $product->image_url;
            $product_name_en[$i] = $product->name_en;

            $qty = trim($this->input->post('qty_' . $i));
            if ( ! is_numeric($qty) OR $qty <= 0) {
                echo $this->create_json(0, lang('qty_not_natural'));
                return;
            }

            $sub_total[$i] = ($product_price[$i]) * $qty;
            $qtys[$i] = $qty;
        }
        $sum = array_sum($sub_total);
        $user_name = $user_info->name_en;
        $date = date('Y-m-d');
        $currency = 'USD';

        $data = array(
            'product_count' => $product_count,
            'product_price' => $product_price,
            'product_sku' => $product_sku,
            'product_image_url' => $product_image_url,
            'product_name_en' => $product_name_en,
            'qtys' => $qtys,
            'sub_total' => $sub_total,
            'user_info' => $user_info,
            'product' => $product,
            'sum' => $sum,
            'user_name' =>$user_name,
            'date' => $date,
            'currency' => $currency,
        );

        $this->load->view('sale/price/make_price_pi', $data);
    }

    public function save_make_price_pi() {
        $seller = trim($this->input->post('seller'));
        $addr = trim($this->input->post('addr'));
        $addr_cn = trim($this->input->post('addr_cn'));
        $tel = trim($this->input->post('tel'));
        $fax = trim($this->input->post('fax'));
        $mobile = trim($this->input->post('mobile'));
        $email = trim($this->input->post('email'));
        $web = trim($this->input->post('web'));
        $buy_addr = trim($this->input->post('buy_addr'));
        $buy_tel = trim($this->input->post('buy_tel'));
        $buy_fax = trim($this->input->post('buy_fax'));
        $buy_mobile = trim($this->input->post('buy_mobile'));
        $buy_email = trim($this->input->post('buy_email'));
        $buy_web = trim($this->input->post('buy_web'));
        $Buyer = trim($this->input->post('Buyer'));

        $sku = $this->input->post('sku');
        $quantity = $this->input->post('quantity');
        $sku_img = $this->input->post('sku_img');
        $good_name = $this->input->post('good_name');
        $unit_price = $this->input->post('unit_price');
        $currency = $this->input->post('currency');
        $note_t = $this->input->post('note_t');

        $note = nl2br($this->input->post('note'));
        $messages = $this->input->post('message');
        $sub_total = $this->input->post('sub_total');

        $count = $this->input->post('count');
        $date = $this->input->post('date');

        $data = array(
            'seller'=>$seller,
            'Buyer' => $Buyer,
            'count' => $count,
            'addr' => $addr,
            'addr_cn' => $addr_cn,
            'tel' => $tel,
            'fax' => $fax,
            'mobile' => $mobile,
            'email' => $email,
            'web' => $web,
            'buy_addr' => $buy_addr,
            'buy_tel' => $buy_tel,
            'buy_fax' => $buy_fax,
            'buy_mobile' => $buy_mobile,
            'buy_email' => $buy_email,
            'buy_web' => $buy_web,
            'sku' => $sku,
            'sku_img' => $sku_img,
            'good_name' => $good_name,
            'unit_price' => $unit_price,
            'currency' => $currency,
            'quantity' => $quantity,
            'note' => $note,
            'note_t' => $note_t,
            'messages' => $messages,
            'sub_total' => $sub_total,
            'user_id' => get_current_user_id(),
            'date' => $date,
        );

        $user_id = get_current_user_id();
        $time = time();

        $pi_file_name = $user_id . "-" . $time . '.html';
        $qty_str = '';
        foreach ($quantity as $qty_vlaue) {
            $qty_str .= $qty_vlaue . ",";
        }
        $qty_strs = rtrim($qty_str, ",");

        $sku_str = '';
        foreach ($sku as $sku_id => $sku_value) {
            $sku_str .= $sku_value . ",";
        }
        $sku_strs = rtrim($sku_str, ",");

        $save_data = array(
            'user_id' => $user_id,
            'sku_str' => $sku_strs,
            'qty_str' => $qty_strs,
            'pi_file_name' => $pi_file_name,
        );
        $this->order_model->save_after_make_pi($save_data);

        create_price_make_pi($data);

        $contact = '/var/www/html/mallerp/static/before_order_pi/';
        $path = $contact . $user_id . "-" . $time . '.html';
        echo file_get_contents($path);
    }

}

?>
