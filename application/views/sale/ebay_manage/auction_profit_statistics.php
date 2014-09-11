<?php
$head = array(
    array('text' => lang('sku'), 'sort_key' => 'sku', 'id' => 'auction_statistics'),
    array('text' => lang('item_count'), 'sort_key' => 'item_count'),
    array('text' => lang('sold_count'), 'sort_key' => 'sold_count'),
    array('text' => lang('total_cost'), 'sort_key' => 'total_cost'),
    array('text' => lang('total_revenue'), 'sort_key' => 'total_gross'),
    array('text' => lang('deal_rate'), 'sort_key' => 'bid_rate'),
    array('text' => lang('profit'), 'sort_key' => 'profit'),
    array('text' => lang('profit_rate'), 'sort_key' => 'profit_rate'),
    array('text' => lang('average_cost'), 'sort_key' => 'average_cost'),
    array('text' => lang('average_gross'), 'sort_key' => 'average_gross'),
    array('text' => lang('suggestion_count'), 'sort_key' => 'suggestion_count'),
    lang('item_info'),
//    array('text' => lang('edited_date'), 'sort_key' => 'updated_date'),
);

$ebay_url = 'http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=';
$data = array();
foreach ($rows as $row)
{
    
    
    
    $item_no_info = explode('|', $row->item_no_info);
    $profit_rate_info = explode('|', $row->profit_rate_info);
    $item_id_info = explode('|', $row->item_id_info);
    $length = count($item_no_info);
    $info = '';

    for ($i = 0; $i < $length; $i++)
    {
        $ebay_html = '<a target="_blank" href="' . $ebay_url . $item_id_info[$i] . '">'. $item_id_info[$i] .'</a>';
        if($profit_rate_info[$i] > 0) 
        {
            $info .= $ebay_html . '&nbsp;' .
                 $item_no_info[$i] . '&nbsp;' .
                 '<font color=green>' . $profit_rate_info[$i] . '</font>'. br();
        } else {
            $info .= $ebay_html . '&nbsp;' .
                 $item_no_info[$i] . '&nbsp;' .
                 '<font color=red>' . $profit_rate_info[$i] . '</font>'. br();
        }
    }

    $detal_div = "detal_{$row->id}";
    $view = lang('view');
    $detal = <<<DETAL
     <a href="#" title="click to see detail" onclick="$('$detal_div').toggle();return false;">$view</a> <div id='$detal_div' style='display: none;border: 2px solid #F27B04;background-color: #F0FFF0;'>$info</div>
DETAL;
    
        $data[] = array(
            $row->sku,
            $row->item_count,
            $row->sold_count,
            $row->total_cost,
            $row->total_gross,
            $row->bid_rate,
            ($row->profit > 0) ? '<font color=green>' . $row->profit . '</font>' : '<font color=red>' . $row->profit . '</font>',
            ($row->profit_rate > 0) ? '<font color=green>' . $row->profit_rate . '</font>' : '<font color=red>' . $row->profit_rate . '</font>',
            $row->average_cost,
            $row->average_gross,
            ($row->suggestion_count < 0) ? '<font color=red>' . $row->suggestion_count . '</font>' : '<font color=green>' . $row->suggestion_count . '</font>',
            $detal,
//            $row->updated_date,
        );
    
}

$filters = array(
    array(
        'type' => 'input',
        'field' => 'sku',
    ),
    array(
        'type' => 'input',
        'field' => 'item_count',
        'method'    => 'from_to'
    ),
    array(
        'type' => 'input',
        'field' => 'sold_count',
        'method'    => 'from_to'
    ),
    array(
        'type' => 'input',
        'field' => 'total_cost',
        'method'    => 'from_to'
    ),
    array(
        'type' => 'input',
        'field' => 'total_gross',
        'method'    => 'from_to'
    ),
    array(
        'type' => 'input',
        'field' => 'bid_rate',
        'method'    => 'from_to'
    ),
    array(
        'type' => 'input',
        'field' => 'profit',
        'method'    => 'from_to'
    ),
    array(
        'type' => 'input',
        'field' => 'profit_rate',
        'method'    => 'from_to'
    ),
    array(
        'type' => 'input',
        'field' => 'average_cost',
        'method'    => 'from_to'
    ),
    array(
        'type' => 'input',
        'field' => 'average_gross',
        'method'    => 'from_to'
    ),
    array(
        'type' => 'input',
        'field' => 'suggestion_count',
        'method'    => 'from_to'
    ),

    array(
        'type' => 'input',
        'field' => 'item_no_info|item_id_info',
    ),
//    array(
//        'type' => 'date',
//        'field' => 'updated_date',
//        'method'    => 'from_to'
//    ),
);

$title = lang('auction_profit_statistics');
echo block_header($title);
echo $this->block->generate_pagination('auction_statistics');

$config = array(
    'filters' => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'auction_statistics');
echo form_close();
echo $this->block->generate_pagination('auction_statistics');
?>
