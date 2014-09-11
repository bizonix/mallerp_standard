<table width="90%" border="0" cellspacing="3" cellpadding="3" align="center" >
    <tr>
      <td align="center">
          <div align="center">
              <font size="4">
                  <B><?=lang('wait_for_qt_check_product');?></B>
              </font>
          </div>
      </td>
    </tr>
</table>

<?php
    $select_purchaser = '';
    if ($show_purchaser_filter)
    {
        $options = array(
            '-1'    => lang('all_purchasers'),
        );
        foreach ($purchasers as $purchaser)
        {
            $options[$purchaser->u_id] = $purchaser->u_name;
        }
        $url = site_url_no_key('qt/wait_for_product_list/view_list');
        $url_key = site_key('qt/wait_for_product_list/view_list');
        $js = "onChange='filter_purchase_list(\"$url\", \"$url_key\", this.value);'";
        $select_purchaser = form_dropdown('select_purchaser', $options, $purchaser_id, $js);
    }
?>
<table width="100%" border="0" align="center">
    <tr>
        <td><?=lang('deadline_of_order_confirmation');?> : <?=$fetch_dueout_update_time?></td>
        <td style="float: right;"><?=$select_purchaser?></td>
    </tr>
</table>

<?php
$head = array(
    lang('sort_num'),
    lang('sku'),
    lang('image_url'),
    lang('market_model'),
    lang('chinese_name'),
    lang('7-days_sales_amounts'),
    lang('30-days_sales_amounts'),
    lang('60-days_sales_amounts'),
    lang('dueout'),
    lang('stock_count'),
    lang('storage_warning'),
    lang('in_transit'),
    lang('purchasing_suggested'),
    lang('purchasing_actually'),
    lang('purchaser'),
    lang('price') . ' / ' .lang('provider'),
);

$data = array();
$index = 0;
foreach ($purchase_list as $purchase)
{  
    $data[] = array(
        $index+1,
        get_status_image($purchase['sku']) . anchor(site_url('pi/product/add_edit', array($purchase['id'])), block_center($purchase['sku']), array('target' => '_blank')),
        block_center(block_image($purchase['image_url'], array(40, 40))),
        block_center($purchase['market_model']),
        block_center($purchase['name_cn']),
        block_center($purchase['7_days_sale_amount'] ? $purchase['7_days_sale_amount'] : 0 ),
        block_center($purchase['30_days_sale_amount'] ? $purchase['30_days_sale_amount'] : 0 ),
        block_center($purchase['60_days_sale_amount'] ? $purchase['60_days_sale_amount'] : 0 ),
        block_center('<strong>' . $purchase['dueout_count'] . '</strong>'),
        block_center('<strong>' . $purchase['stock_count'] . '</strong>'),
        block_center($purchase['min_stock_number']),
        block_center($purchase['on_way_count']),
        block_center($purchase['purchase_suggestion']),
        '',
        block_center($purchase['purchaser']),
        $purchase['providers'],
    );
    $index++;
}
$batch_purchase = site_url('purchase/order/batch_purchase_sku');
echo form_open($batch_purchase);
echo block_js_sortable_table(
    $head,
    $data,
    array(
        NULL,
        'default',
        NULL,
        'default',
        'default',
        'integer',
        'integer',
        'integer',
        'integer',
        'integer',
        'integer',
        'integer',
        'integer',
        NULL,
        'default',
    ),
    "width: 100%;border-collapse: collapse;"
);
echo form_close();
?>
