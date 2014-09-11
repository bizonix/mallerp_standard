<?php
$head = array(
    lang('name'),
    lang('description'),
    lang('version'),
    lang('operation'),
);

$url = site_url('admin/system/process_enable_disable');
foreach ($all_subsys as $key => $info)
{
    $checkbox = array(
        'name'        => $key,
        'id'          => $key,
        'value'       => $key,
        'checked'     => $info['status'] ? TRUE : FALSE,
        'style'       => 'margin:10px',
        'onclick'    => "helper.ajax('$url', {sys: this.value, checked: this.checked}, this)",
    );
    $data[] = array(
        lang($info['name']),
        $info['description'],
        $info['version'],
        form_checkbox($checkbox) . form_label(lang('check_to_enable')),
    );
}
echo block_header(lang('system_enable_disable'));
echo $this->block->generate_table($head, $data);

?>