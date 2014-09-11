<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$head = array(
    '<br/>',
);

$url = site_url('order/setting/modification_order_status');

$config_input = array(
      'name'        => 'item_no',
      'id'          => 'item_no',
      'maxlength'   => '50',
      'size'        => '50',
      'onkeydown'        => "if(event.keyCode==13) { update_order_status('$url'); }",
);

$config_button = array(
    'name'        => 'submit',
    'value'       => lang('modification'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "update_order_status('$url');",
);

//echo '<pre>';
//
//var_dump($this->block->generate_search_dropdown('order_status', 'order_status'));
//echo '</pre>';

$html = lang('item_number');
$text = lang('clue_for_update_status');

$data[] = array(
    '<div align="center">' . $html . ':' . form_input($config_input) . ' ' .
    block_button($config_button) .'<div>',
);

echo "<div id='search_div'>";

echo block_header(lang('modification_order_status'));

echo $this->block->generate_table($head, $data);

echo block_notice_div($text);

echo "</div>";

?>
<script>

function update_order_status(url)
{
    var item_no = $('item_no').value;
    var params = {'item_no' : item_no};
    
    this.blur();
    if(confirm('此操作会直接更改数据库数据，请确认订单的后续流程并没有完成'))
    {
        helper.ajax(url, params);
    }
}

</script>
