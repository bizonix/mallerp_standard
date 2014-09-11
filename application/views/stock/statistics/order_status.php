<?php
echo '<h5>' . lang('order_from_label_to_purchase') . '</h5>';
$html = '';
foreach ($report['label_to_puchase'] as $item)
{
    $html .= $item['order']->item_no . ' -> ';
    if (count($item['in_stock']))
    {
        $html .= '<span style="color: green;">';
        $html .= lang('in_stock') . ': ';
        $html .= implode(',', array_keys($item['in_stock'])) . ', ';
        $html .= '</span>';
    }
    $html .= '<span style="color: red;">';
    $html .= lang('out_of_stock') . ': ';
    $html .= implode(',', $item['out_of_stock']) . '<br/>';
    $html .= '</span>';
}
echo $html;
echo '<h5>' . lang('order_from_purchase_to_label') . '</h5>';
$html = '';
foreach ($report['purchase_to_label'] as $item)
{
    $html .= $item['order']->item_no . ' -> ';
    $html .= lang('product_information') . ': ' . $item['order']->sku_str . '<br/>';
}
echo $html;
echo '<h5>' . lang('order_still_label') . '</h5>';
$html = '';
foreach ($report['still_label'] as $item)
{
    $html .= $item['order']->item_no . ' -> ';
    $html .= lang('product_information') . ': ' . $item['order']->sku_str . '<br/>';
}
echo $html;
echo '<h5>' . lang('order_still_purchase') . '</h5>';
$html = '';
foreach ($report['still_purchase'] as $item)
{
    $html .= $item['order']->item_no . ' -> ';
    $html .= lang('product_information') . ': ' . $item['order']->sku_str . '<br/>';
}
echo $html;

?>
