<?php
$url = site_url('sale/ebay_competitor/add_competitor');
$add_button = $this->block->generate_add_icon($url, "{'item_id' : $item_id}");
$head = array(
    lang('seller_id'),
    lang('url'),
    lang('allowed_difference'),
    lang('options') . $add_button,
);

$data = array();
$code_url = site_url('sale/ebay_competitor/update_competitor');
foreach ($competitors as $competitor)
{
    $drop_button = $this->block->generate_drop_icon(
        'sale/ebay_competitor/drop_competitor_by_id',
        "{id: $competitor->id}",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("seller_id_{$competitor->id}", isset($competitor) && $competitor->seller_id  ?  $competitor->seller_id : '[edit]'),
        $this->block->generate_div("url_{$competitor->id}", isset($competitor) ?  $competitor->url : '[edit]'),
        $this->block->generate_div("allowed_difference_{$competitor->id}", isset($competitor) ?  $competitor->allowed_difference : '[edit]'),
        $drop_button,
    );
    
    echo $this->block->generate_editor(
        "seller_id_{$competitor->id}",
        'ebay_competitor_form',
        $code_url,
        "{id: $competitor->id, type: 'seller_id'}"
    );
    echo $this->block->generate_editor(
        "url_{$competitor->id}",
        'ebay_competitor_form',
        $code_url,
        "{id: $competitor->id, type: 'url'}"
    );
    echo $this->block->generate_editor(
        "allowed_difference_{$competitor->id}",
        'ebay_competitor_form',
        $code_url,
        "{id: $competitor->id, type: 'allowed_difference'}"
    );
}
$title = lang('competitor_setting');
echo block_header($title);
echo form_open();
echo $this->block->generate_table($head, $data);
echo form_close();

echo block_notice_div(lang('competitor_allowed_difference_notice'));
?>
