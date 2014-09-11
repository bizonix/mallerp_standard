<?php
$head = array(
    lang('name'),
    lang('value'),
);

$country_id = 'country';
$config = array(
      'name'        => 'country',
      'id'          => 'country',
      'value'       => isset($country) ? $country : '',
      'maxlength'   => '20',
      'size'        => '20',
);
$data[] = array(
    $this->block->generate_required_mark(lang('country')),
    form_input($config),
);
echo $this->block->generate_ac($country_id, array('country_code', 'name_cn'));
$config = array(
      'name'        => 'weight',
      'id'          => 'weight',
      'value'       => isset($weight) ? $weight : '',
      'maxlength'   => '20',
      'size'        => '20',
);
$data[] = array(
    $this->block->generate_required_mark(lang('weight') . ' (g)'),
    form_input($config),
);

$title = lang('calculate_shipping_price');
echo block_header($title);
$attributes = array(
    'id' => 'calculate_price'
);
echo form_open(site_url('shipping/shipping_company/calculate_shipping_price'), $attributes);
echo $this->block->generate_table($head, $data);
echo '<br/>';

$config = array(
    'name'        => 'calculate_price',
    'value'       => lang('calculate_price'),
    'type'        => 'submit',
);
echo block_button($config);
echo form_close();
echo '<br/>';

if (count($accepted_result))
{
    $head = array(
        lang('shipping_company'),
        lang('shipping_type'),
        lang('arrival_time'),
        lang('price') . '(RMB)',
        lang('rule'),
        lang('rule_meaning'),
        lang('global_rule'),
    );
    $data = array();
    foreach ($accepted_result as $row)
    {
        $item = array();
        $item[] = $row['company']->name;
        $item[] = $row['type']->type_name;
        $item[] = $row['type']->arrival_time;
        $item[] = $row['price'];
        $item[] = $row['rule']->rule;
        $item[] = $row['rule']->rule_meaning;
        $item[] = $row['global_rule'];
        $data[] = $item;
    }
    $title = lang('company_matches') . '(' . count($accepted_result) . ')';
    echo block_header($title);
    echo $this->block->generate_js_sortable_table($head, $data,
        array(
            'default',
            'default',
            'integer',
            'float',
        )
    );
}
else if (isset($country) && isset($weight))
{
    echo '<center>' . lang('no_result_matches') . '</center>';
}
