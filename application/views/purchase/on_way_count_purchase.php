<?php
$head = array(
    lang('sku'),
    lang('item_number'),
    lang('purchase_quantity'),
    lang('fcommitqty'),
    lang('instock_count'),
    lang('on_passage'),
);

$data = array();

foreach( $sku_purchase_list as $sku_purchase)
{
    $data[] = array(
        $sku_purchase->sku,
        $sku_purchase->item_no,
        $sku_purchase->sku_quantity,
        $sku_purchase->sku_arrival_quantity,
        $instock_count["$sku_purchase->sku"],
        $sku_purchase->sku_quantity - $instock_count["$sku_purchase->sku"],
    );
}

$title = lang('on_way_count_purchase');
echo block_header($title);

echo $this->block->generate_table($head, $data);

?>

