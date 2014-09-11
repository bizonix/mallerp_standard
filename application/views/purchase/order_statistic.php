<?php
$search_sort_url = site_url('purchase/order_statistic/order_statistic_show', array($provider_id));

$head = array(
   array('text' =>lang('item_no'), 'sort_key'=> 'item_no', 'id'=>'purchase_order', 'sort_url' => $search_sort_url),
   lang('sku'),
   lang('Total_payment_amount'),
   array('text' =>lang('order_time'), 'sort_key'=> 'created_date','sort_url' => $search_sort_url),
);

$title = lang('Supplier_purchasing_statistics')."--"."($company_name)";
echo block_header($title);
echo $this->block->generate_pagination('purchase_order', array($provider_id));
echo lang('total_order_count').':<font color = red >' . $total_order_count . '</font>'.lang('num').'&nbsp'.'&nbsp';
echo lang('Total_payment_amount').':<font color = red>'.$total_amount.'</font>';

$data = array();
foreach($order_statistics as $order_statistic)
{
    $data[] = array(
      $order_statistic->item_no,
      $set_skus["$order_statistic->id"],
      $total_money["$order_statistic->id"]?$total_money["$order_statistic->id"]:0,
      $order_statistic->created_date,
     );
}

$filters = array(
    array(
        'type'      => 'input',
        'field'     => 'item_no',
    ),
    array(
    ),
    array(
    ),
    array(
        'type'      => 'date',
        'field'     => 'created_date',
        'method'    => 'from_to',
    ),
 );

$config = array(
    'filters'    => $filters,
    'url'        => $search_sort_url,
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head,$data, $filters,'purchase_order');
echo form_close();
echo $this->block->generate_pagination('purchase_order', array($provider_id));
