<?php

$profiler_url = site_url('admin/system/proccess_enable_profiler');
$checkbox = array(
    'name'        => 'profiler',
    'id'          => 'profiler',
    'checked'     => $debug_mode ? TRUE : FALSE,
    'style'       => 'margin:10px',
    'onclick'    => "helper.ajax('$profiler_url', {checked: this.checked}, 1)",
);
$debug_mode = $this->block->generate_div('debug_mode', form_checkbox($checkbox) . form_label(lang('enable_profiler_or_not')));
echo $this->block->generate_fieldset(lang('debug_mode'), $debug_mode);

?>
