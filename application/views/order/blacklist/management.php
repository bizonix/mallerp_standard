<?php

$head = array(
    array('text' => lang('platform'), 'sort_key' => 'platform', 'id' => 'blacklist'),
    array('text' => lang('buyer_id'), 'sort_key' => 'buyer_id'),
    array('text' => lang('email'), 'sort_key' => 'email'),
    array('text' => lang('name'), 'sort_key' => 'b_name'),
    array('text' => lang('remark'), 'sort_key' => 'remark'),
    array('text' => lang('creator_name'), 'sort_key' => 'creator_id'),
    array('text' => lang('created_date'), 'sort_key' => 'created_date'),
    lang('options'),
);

$data = array();

foreach ($blacklists as $blacklist) {
    $url = '<br/><br/>';
    if ($action == 'edit') {
        $drop_button = $this->block->generate_drop_icon(
                        'order/blacklist/drop_blacklist',
                        "{id: $blacklist->id}",
                        TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('order/blacklist/add_edit', array($blacklist->id)));
        $url = $drop_button . $edit_button;
    }

    $data[] = array(
        $blacklist->platform,
        $blacklist->buyer_id,
        $blacklist->email,
        $blacklist->b_name,
        $blacklist->remark,
        $blacklist->name,
        $blacklist->created_date,
        $url,
    );
}

$filters = array(
    array(
        'type' => 'input',
        'field' => 'platform',
    ),
    array(
        'type' => 'input',
        'field' => 'buyer_id',
    ),
    array(
        'type' => 'input',
        'field' => 'customer_black_list.email',
    ),
    array(
        'type' => 'input',
        'field' => 'b_name',
    ),
    array(
        'type' => 'input',
        'field' => 'remark',
    ),
    array(
        'type' => 'input',
        'field' => 'name',
    ),
    array(
        'type' => 'input',
        'field' => 'created_date',
    ),
);
if($action == 'edit')
{
    $title = lang('customer_black_list_manage');
}
else
{
    $title = lang('customer_black_list_view');
}
echo block_header($title);

echo $this->block->generate_pagination('blacklist');

$config = array(
    'filters' => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);

echo $this->block->generate_table($head, $data, $filters, 'blacklist');

echo form_close();

echo $this->block->generate_pagination('blacklist');
?>
