<?php

$url = site_url('shipping/shipping_code/add_shipping_code');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('shipping_code'),
    lang('english_name'),
    lang('chinese_name'),
    lang('order_check_address'),
    lang('contact_phone_requred'),
    lang('stock_code'),
    lang('taobao_deliver_code'),
	lang('wish_deliver_code'),
	lang('ydf_code'),
    lang('is_tracking'),
    lang('created_date'),
    lang('options') . $add_button,
);

$data = array();
$code_url = site_url('shipping/shipping_code/verigy_shipping_code');
foreach ($shipping_codes as $shipping_code)
{

    $drop_button = $this->block->generate_drop_icon(
        'shipping/shipping_code/drop_shipping_code',
        "{id: $shipping_code->id}",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("code_{$shipping_code->id}", empty($shipping_code->code) ?    '[edit]' : $shipping_code->code),
        $this->block->generate_div("name_en_{$shipping_code->id}", empty($shipping_code->name_en) ?  '[edit]': $shipping_code->name_en),
        $this->block->generate_div("name_cn_{$shipping_code->id}", empty($shipping_code->name_cn) ?    '[edit]' : $shipping_code->name_cn),
        $this->block->generate_div("check_url_{$shipping_code->id}", empty($shipping_code->check_url) ?  '[edit]' : $shipping_code->check_url),
        $this->block->generate_div("contact_phone_requred_{$shipping_code->id}", empty($shipping_code->contact_phone_requred) ?  lang('no') : lang('yes')),
        $this->block->generate_div("stock_code_{$shipping_code->id}", empty($shipping_code->stock_code) ?  '[edit]' : $shipping_code->stock_code),
        $this->block->generate_div("taobao_company_code_{$shipping_code->id}",  empty ($shipping_code->taobao_company_code) ? '[edit]' : $shipping_code->taobao_company_code),
		$this->block->generate_div("wish_company_code_{$shipping_code->id}",  empty ($shipping_code->wish_company_code) ? '[edit]' : $shipping_code->wish_company_code),
		$this->block->generate_div("ydf_code_{$shipping_code->id}",  empty ($shipping_code->ydf_code) ? '[edit]' : $shipping_code->ydf_code),
        $this->block->generate_div("is_tracking_{$shipping_code->id}", empty($shipping_code->is_tracking) ?  lang('no') : lang('yes')),
        $shipping_code->created_date,
        $drop_button,
    );
    echo $this->block->generate_editor(
        "code_{$shipping_code->id}",
        'shipping_code_form',
        $code_url,
        "{id: $shipping_code->id, type: 'code'}"
    );
    echo $this->block->generate_editor(
        "name_en_{$shipping_code->id}",
        'shipping_code_form',
        $code_url,
        "{id: $shipping_code->id, type: 'name_en'}"
    );
    echo $this->block->generate_editor(
        "name_cn_{$shipping_code->id}",
        'shipping_code_form',
        $code_url,
        "{id: $shipping_code->id, type: 'name_cn'}"
    );
    echo $this->block->generate_editor(
        "check_url_{$shipping_code->id}",
        'shipping_code_form',
        $code_url,
        "{id: $shipping_code->id, type: 'check_url'}"
    );
    echo $this->block->generate_editor(
        "taobao_company_code_{$shipping_code->id}",
        'shipping_code_form',
        $code_url,
        "{id: $shipping_code->id, type: 'taobao_company_code'}"
    );
	echo $this->block->generate_editor(
        "wish_company_code_{$shipping_code->id}",
        'shipping_code_form',
        $code_url,
        "{id: $shipping_code->id, type: 'wish_company_code'}"
    );
	echo $this->block->generate_editor(
        "ydf_code_{$shipping_code->id}",
        'shipping_code_form',
        $code_url,
        "{id: $shipping_code->id, type: 'ydf_code'}"
    );


    $stock_code_arr = array();
    foreach ($stock_codes as $code)
    {
        $stock_code_arr["$code->stock_code"] = $code->stock_code;
    }
    $collection = to_js_array($stock_code_arr);
    
    echo $this->block->generate_editor(
        "stock_code_{$shipping_code->id}",
        'shipping_code_form',
        $code_url,
        "{id: $shipping_code->id, type: 'stock_code'}",
        $collection
    );

    $collection_phone = to_js_array(array('1'=>lang('yes'), '0'=>lang('no')));
    echo $this->block->generate_editor(
        "contact_phone_requred_{$shipping_code->id}",
        'shipping_code_form',
        $code_url,
        "{id: $shipping_code->id, type: 'contact_phone_requred'}",
        $collection_phone
    );
        
    echo $this->block->generate_editor(
        "is_tracking_{$shipping_code->id}",
        'shipping_code_form',
        $code_url,
        "{id: $shipping_code->id, type: 'is_tracking'}",
        $collection_phone
    );

}
$title = lang('shipping_code_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();
?>
