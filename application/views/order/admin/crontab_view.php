<?php

$url = site_url('admin/crontab/crontab_add_row');
$add_button = $this->block->generate_add_icon($url);

$head = array(
    lang('on'),
    lang('job'),
    lang('description'),
    lang('creator'),
    lang('created_date'),
    lang('options') . $add_button,
);

$data = array();

$crontab_url = site_url('admin/crontab/verify_crontab');

foreach ($crontabresult as $show_result) {
    $drop_button = $this->block->generate_drop_icon(
                    'admin/crontab/crontab_delete', "{id: $show_result->id}", TRUE
    );

    $data[] = array(
        $this->block->generate_div("on_{$show_result->id}", empty($show_result->on) ? '[edit]' : $show_result->on),
        $this->block->generate_div("job_{$show_result->id}", empty($show_result->job) ? '[edit]' : $show_result->job),
        $this->block->generate_div("description_{$show_result->id}", empty($show_result->description) ? '[edit]' : $show_result->description ),
        fetch_user_name_by_id($show_result->creator),
        $show_result->created_date,
        $drop_button,
    );

    echo $this->block->generate_editor(
        "on_{$show_result->id}", 'crontab_view_form', $crontab_url, "{id: $show_result->id ,type: 'on'}"
    );

    echo $this->block->generate_editor(
        "job_{$show_result->id}", 'crontab_view_form', $crontab_url, "{id: $show_result->id ,type: 'job'}"
    );

    echo block_editor(
        "description_{$show_result->id}", 'crontab_view_form', $crontab_url, "{id: $show_result->id ,type: 'description'}"
    );
}

$title = lang('admin_crontab');
echo block_header($title);
echo $this->block->generate_table($head, $data);

$activate_crontab_url = site_url('admin/crontab/activate_crontab');
$view_crontab_url = site_url('admin/crontab/view_crontab_list');

$config = array(
    'name'      => 'activate_crontab',
    'id'        => 'activate_crontab',
    'value'     => lang('activate_crontab_again'),
    'type'      => 'button',
    'onclick'   => "helper.ajax('$activate_crontab_url', {}, 1);",
);
$activate_crontab = '<div style="float: right; ">';
echo br();
$activate_crontab .= block_button($config);
$attributes = "onclick=\"helper.update_content('$view_crontab_url', {}, 'crontab_list_div'); return false;\"";
$activate_crontab .= ' ' . anchor(current_url(), lang('view_crontab_list'), $attributes);
$activate_crontab .= '</div>';
echo $activate_crontab;
echo '<div style="clear:both;"></div>';
echo '<div id="crontab_list_div" style="display:none;"></div>';
