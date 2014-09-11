<form  action="<?php echo site_url('purchase/order/purchase_sku'); ?>" method="post">
    <table width="95%" border="0" align="center">
        <tr>
            <td>
                <label><b><?=lang('sku')?>:</b></label>
                <input type="text" name="sku"  />
                <input type="submit" value="<?=lang('single_category_of_commodities')?>" name="sku_submit" />
                <label><?=lang('multi_products_seperate_by_comma')?></label>
            </td>
        </tr>
    </table>
</form>
<br/><br/>

<table width="90%" border="0" cellspacing="3" cellpadding="3" align="center" >
    <tr>
      <td align="center">
          <div align="center">
              <font size="4">
                  <?=date(lang('Y-m-d')) ?> <?=lang('list_of_items_to_be_purchased');?>
              </font>
          </div>
      </td>
    </tr>
</table>

<?php

//var_dump($stock_code);

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
        $url = site_url_no_key('purchase/purchase_list/view_list');
        $url_key = site_key('purchase/purchase_list/view_list');
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
    lang('select'),
    lang('image_url'),
    lang('sku'),
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

//echo '<pre>';
//var_dump($purchase_list);

foreach ($purchase_list as $purchase)
{  
    $select_data = array(
        'name'        => 'checkbox_select_'.$index,
        'id'          => 'checkbox_select_'.$purchase['sku'],
        'value'       => $purchase['sku'],
        'style'       => 'margin:10px',
    );
    
    $dueout_count_html = '';
    $stock_count_html = '';
    $on_way_count_html = '';
    $min_stock_number_html = '';
    $purchase_suggestion_html = '';
    if(is_in_abroad_store($purchase['sku']))
    {
//        echo $purchase['sku'];
        foreach ($stock_code as $code)
        {
            if($code->stock_code == 'SZ')
            {
                $dueout_count_html .= $purchase['dueout_count']==''?'':'<font color=blue>'.$purchase['dueout_count'].'</font>'.' (SZ) <br/>';
                $stock_count_html .= $purchase['stock_count']==''?'':'<font color=blue>'.$purchase['stock_count'].'</font>'.' (SZ) <br/>';
                $on_way_count_html .= $purchase['on_way_count']==''?'':'<font color=blue>'.$purchase['on_way_count'].'</font>'.' (SZ) <br/>';
                $min_stock_number_html .= $purchase['min_stock_number']==''?'':'<font color=blue>'.$purchase['min_stock_number'].'</font>'.' (SZ) <br/>';
                $purchase_suggestion_html .= $purchase['purchase_suggestion'] <= 0 ?'':'<font color=blue>'.$purchase['purchase_suggestion'].'</font>'.' (SZ) <br/>';
            }
            if($code->stock_code == 'UK')
            {
                $dueout_count_html .= $purchase['uk_dueout_count']==''?'':'<font color=blue>'.($purchase['uk_dueout_count']).'</font>'.' (UK) <br/>';
                $stock_count_html .= $purchase['uk_stock_count']==''?'':'<font color=blue>'.($purchase['uk_stock_count']).'</font>'.' (UK) <br/>';
                $on_way_count_html .= $purchase['uk_on_way_count']==''?'':'<font color=blue>'.($purchase['uk_on_way_count']).'</font>'.' (UK) <br/>';
                $min_stock_number_html .= $purchase['uk_min_stock_number']==''?'':'<font color=blue>'.($purchase['uk_min_stock_number']).'</font>'.' (UK) <br/>';
                $purchase_suggestion_html .= $purchase['uk_purchase_suggestion'] <= 0 ?'':'<font color=blue>'.($purchase['uk_purchase_suggestion']).'</font>'.' (UK) <br/>';
            }
            if($code->stock_code == 'DE')
            {
                $dueout_count_html .= $purchase['de_dueout_count']==''?'':'<font color=blue>'.($purchase['de_dueout_count']).'</font>'.' (DE) <br/>';
                $stock_count_html .= $purchase['de_stock_count']==''?'':'<font color=blue>'.($purchase['de_stock_count']).'</font>'.' (DE) <br/>';
                $on_way_count_html .= $purchase['de_on_way_count']==''?'':'<font color=blue>'.($purchase['de_on_way_count']).'</font>'.' (DE) <br/>';
                $min_stock_number_html .= $purchase['de_min_stock_number']==''?'':'<font color=blue>'.($purchase['de_min_stock_number']).'</font>'.' (DE) <br/>';
                $purchase_suggestion_html .= $purchase['de_purchase_suggestion'] <= 0 ?'':'<font color=blue>'.($purchase['de_purchase_suggestion']).'</font>'.' (DE) <br/>';
            }
            if($code->stock_code == 'AU')
            {
                $dueout_count_html .= $purchase['au_dueout_count']==''?'':'<font color=blue>'.($purchase['au_dueout_count']).'</font>'.' (AU) <br/>';
                $stock_count_html .= $purchase['au_stock_count']==''?'':'<font color=blue>'.($purchase['au_stock_count']).'</font>'.' (AU) <br/>';
                $on_way_count_html .= $purchase['au_on_way_count']==''?'':'<font color=blue>'.($purchase['au_on_way_count']).'</font>'.' (AU) <br/>';
                $min_stock_number_html .= $purchase['au_min_stock_number']==''?'':'<font color=blue>'.($purchase['au_min_stock_number']).'</font>'.' (AU) <br/>';
                $purchase_suggestion_html .= $purchase['au_purchase_suggestion'] <= 0 ?'':'<font color=blue>'.($purchase['au_purchase_suggestion']).'</font>'.' (AU) <br/>';
            }
			if($code->stock_code == 'YB')
            {
                $dueout_count_html .= $purchase['yb_dueout_count']==''?'':'<font color=blue>'.($purchase['yb_dueout_count']).'</font>'.' (YB) <br/>';
                $stock_count_html .= $purchase['yb_stock_count']==''?'':'<font color=blue>'.($purchase['yb_stock_count']).'</font>'.' (YB) <br/>';
                $on_way_count_html .= $purchase['yb_on_way_count']==''?'':'<font color=blue>'.($purchase['yb_on_way_count']).'</font>'.' (YB) <br/>';
                $min_stock_number_html .= $purchase['yb_min_stock_number']==''?'':'<font color=blue>'.($purchase['yb_min_stock_number']).'</font>'.' (YB) <br/>';
                $purchase_suggestion_html .= $purchase['yb_purchase_suggestion'] <= 0 ?'':'<font color=blue>'.($purchase['yb_purchase_suggestion']).'</font>'.' (AU) <br/>';
            }
        }
    }
    else
    {
        $dueout_count_html .= $purchase['dueout_count']==''?'':'<font color=blue>'.$purchase['dueout_count'].'</font>'.'<br/>';
        $stock_count_html .= $purchase['stock_count']==''?'':'<font color=blue>'.$purchase['stock_count'].'</font>'.'<br/>';
        $on_way_count_html .= $purchase['on_way_count']==''?'':'<font color=blue>'.$purchase['on_way_count'].'</font>'.'<br/>';
        $min_stock_number_html .= $purchase['min_stock_number']==''?'':'<font color=blue>'.$purchase['min_stock_number'].'</font>'.'<br/>';
        $purchase_suggestion_html .= $purchase['purchase_suggestion'] <= 0 ?'':'<font color=blue>'.$purchase['purchase_suggestion'].'</font>'.'<br/>';
        
//        $purchase_suggestion = $purchase['purchase_suggestion'] <= 0 ? 0 : $purchase['purchase_suggestion'];
    }




    $data[] = array(
        form_checkbox($select_data),
        block_center(block_image($purchase['image_url'], array(40, 40))),
        get_status_image($purchase['sku']) . anchor(site_url('pi/product/add_edit', array($purchase['id'])), block_center($purchase['sku']), array('target' => '_blank')),
        block_center($purchase['market_model']),
        block_center($purchase['name_cn']),
        block_center($purchase['7_days_sale_amount'] ? $purchase['7_days_sale_amount'] : 0 ),
        block_center($purchase['30_days_sale_amount'] ? $purchase['30_days_sale_amount'] : 0 ),
        block_center($purchase['60_days_sale_amount'] ? $purchase['60_days_sale_amount'] : 0 ),
        block_center('<strong>' . "$dueout_count_html" . '</strong>'),
        block_center('<strong>' . $stock_count_html . '</strong>'),
        block_center($min_stock_number_html),
        block_center($on_way_count_html),
//        block_center($purchase_suggestion),
        block_center($purchase_suggestion_html),
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
echo form_hidden('item_count', $index);
$print_label = '<span style="float:right;">';
$print_label .= form_submit('batch_purchase',lang('batch_purchase'));
$print_label .= '</span>';
echo block_check_all() . $print_label;
echo form_close();
?>
