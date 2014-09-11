<?php

$add_button = $this->block->generate_add_icon('order/setting/add_stmp_host');
$head = array(
    lang('host'),
    lang('port'),
    lang('is_ssl'),
    lang('options') . $add_button
);

$host_update_url = site_url('order/setting/update_stmp_host');

$yes = lang('yes');
$no = lang('no');
$ssl_collection = to_js_array(array('1' => $yes, '0' => $no));
$data = array();
foreach ($hosts as $host)
{
    $host_id = $host->id;
    
    $drop_button = block_drop_icon(
        'order/setting/drop_stmp_host',
        "{id: $host_id}",
        TRUE
    );
    $data[] = array(
        $this->block->generate_div("stmp_host_$host_id", $host->host),
        $this->block->generate_div("stmp_port_$host_id", $host->port),
        $this->block->generate_div("stmp_is_ssl_$host_id", $host->is_ssl == 1 ? $yes : $no),
        $drop_button,
    );
    echo $this->block->generate_editor(
        "stmp_host_$host_id",
        'host_form',
        $host_update_url,
        "{id: $host_id, type: 'host'}"
    );
    echo $this->block->generate_editor(
        "stmp_port_$host_id",
        'host_form',
        $host_update_url,
        "{id: $host_id, type: 'port'}"
    );
    echo $this->block->generate_editor(
        "stmp_is_ssl_$host_id",
        'host_form',
        $host_update_url,
        "{id: $host_id, type: 'is_ssl'}",
        $ssl_collection
    );
}
echo block_header(lang('stmp_host_setting'));
echo block_table($head, $data);

?>
