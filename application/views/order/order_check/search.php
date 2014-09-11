<?php
$head = array(
    '<br/>',
);

$config_input = array(
      'name'        => 'search',
      'id'          => 'search',
      'maxlength'   => '50',
      'size'        => '50',
);

$url = site_url('order/order_check/order_check_list');

$config_button = array(
    'name'        => 'submit',
    'value'       => lang('search'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "search('$url');",
);

$options = array(
    'item_no'           => lang('item_no'),
    'name'              => lang('name'),
    'transaction_id'    => lang('transaction_id'),
    'buyer_id'          => lang('buyer_id'),
    'item_id_str'       => lang('item_id_str'),
);

$data[] = array(
    '<div align="center">'.lang('order_check_search') . ': ' . form_input($config_input) . ' ' .
    form_dropdown('type',$options, lang('item_no'),"id='type'"). block_button($config_button) .'<div>',
);

echo $this->block->generate_table($head, $data);

?>
<script>

function search(url)
{
    var search = $('search').value;
    var type = $('type').value;
    var params = {'search' : search, 'type' : type};

    this.blur();
    helper.update_content(url, params);
}

</script>