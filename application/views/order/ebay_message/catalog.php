<?php
$CI = & get_instance();
$url = site_url('order/ebay_message/add_new_catalog');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('category'),
    lang('remark'),
    lang('creator'),
    lang('created_date'),
	lang('keyword'),
	lang('options') . $add_button,
);

$data = array();
$url = site_url('order/ebay_message/update_catalog');

foreach ($catalogs as $catalog)
{
	$drop_button = $this->block->generate_drop_icon(
        'order/ebay_message/drop_catalog_by_id',
        "{id: $catalog->id}",
        TRUE
    );
	$user_info = $CI->user_model->fetch_user_by_id($catalog->user);
    $data[] = array(
		$this->block->generate_div("category_name_{$catalog->id}", isset($catalog) && $catalog->category_name  ?  $catalog->category_name : '[edit]'),
		$this->block->generate_div("ebay_note_{$catalog->id}", isset($catalog) && $catalog->ebay_note  ?  $catalog->ebay_note : '[edit]'),
		$user_info->name,
		$catalog->created_date,
		$this->block->generate_div("category_keywords_{$catalog->id}", isset($catalog) && $catalog->category_keywords  ?  $catalog->category_keywords : '[edit]'),
		$drop_button,
    );
    echo $this->block->generate_editor(
        "category_name_{$catalog->id}",
        'ebay_message_catalog_form',
        $url,
        "{id: $catalog->id, type: 'category_name'}"
    );
	echo $this->block->generate_editor(
        "ebay_note_{$catalog->id}",
        'ebay_message_catalog_form',
        $url,
        "{id: $catalog->id, type: 'ebay_note'}"
    );
	echo $this->block->generate_editor(
        "category_keywords_{$catalog->id}",
        'ebay_message_catalog_form',
        $url,
        "{id: $catalog->id, type: 'category_keywords'}"
    );
    
}
$title = lang('ebay_message_catalog_management');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();

?>
