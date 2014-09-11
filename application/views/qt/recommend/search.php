<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$head = array(
    '<br/>',
);

$url = site_url('qt/recommend/recommend_list');

$config_input = array(
      'name'        => 'search',
      'id'          => 'search',
      'maxlength'   => '50',
      'size'        => '50',
      'onkeydown'   => "if(event.keyCode==13) { search('$url','$tag'); }",
);

$config_button = array(
    'name'        => 'submit',
    'value'       => lang('search'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "search('$url','$tag');",
);

if($tag == 'order')
{
    $options = array(
        'item_no'           => lang('item_number'),
    );
    $html = lang('order_item_no_search');
}
else
{
    $options = array(
        'item_no'           => lang('item_number'),
        'name'              => lang('name'),
        'transaction_id'    => lang('transaction_id'),
        'buyer_id'          => lang('buyer_id'),
        'item_id_str'       => lang('item_id_str'),
        'track_number'      => lang('track_number'),
    );
    $html = lang('recommend_order_search');
}


$data[] = array(
    '<div align="center">' . $html . ':' . form_input($config_input) . ' ' .
    form_dropdown('type',$options, 'item_no',"id='type'"). block_button($config_button) .'</div>',
);

echo "<div id='search_div'>";
if($tag == 'qt')
{
    echo block_header(lang('recommend_service_list_add'));
}
elseif($tag == 'order')
{
    echo block_header(lang('copy_order'));
}
elseif($tag == 'shiping_edit_number')
{
    echo block_header(lang('edit_number'));
}
echo $this->block->generate_table($head, $data);
echo "</div>";

?>
<script>

function search(url, tag)
{
    var search = $('search').value;
    var type = $('type').value;
    var params = {'search' : search, 'type' : type, 'tag' : tag};

    this.blur();
    helper.update_content(url, params, 'search_div',1);
}

</script>
