<?php
$head[] = lang('start_weight') . '(g, ' . lang('exclude') . ')';
$head[] = lang('end_weight') . '(g, ' . lang('include') . ')';
foreach ($subareas as $sub)
{
    $head[] = $sub->subarea_name . '(' . $sub->id . ')';
}
$subarea_group_id = $shipping_type->group_id;

$params = "{company_type_id: $company_type_id, subarea_group_id: $subarea_group_id}";
$add_button = $this->block->generate_add_icon('shipping/shipping_company/add_rule', $params);
$head[] = lang('options').$add_button;

$update_rule_url = site_url('shipping/shipping_company/save_rule');
$data = array();
$i = 0;
foreach ($all_weights as $weight)
{
    $i++;
    $row = array();
    $start_weight = $weight->start_weight;
    $end_weight = $weight->end_weight;
    $id = 'rule_' . $start_weight . $i;
    $row[] = $this->block->generate_div($id, $start_weight);
    echo $this->block->generate_editor(
        "$id",
        'rule_form',
        $update_rule_url,
        "{type: 'start_weight', start_weight: $start_weight, company_type_id: $company_type_id}"
    );

    $id = 'rule_' . $end_weight . $i;
    $row[] = $this->block->generate_div($id, $end_weight);
    echo $this->block->generate_editor(
        "$id",
        'rule_form',
        $update_rule_url,
        "{type: 'end_weight', end_weight: $end_weight, company_type_id: $company_type_id}"
    );
    foreach ($subareas as $sub)
    {
        $rule = $this->shipping_function_model->fetch_rule_with_exact_weight($start_weight, $end_weight, $sub->id, $company_type_id);
        $rule_value = NULL;
        if (isset($rule->rule))
        {
            $rule_value = $rule->rule;
        }
        $rule_meaning = NULL;
        if (isset($rule->rule_meaning))
        {
            $rule_meaning = $rule->rule_meaning;
        }
        $rule_id = -1;
        if (isset($rule->id))
        {
            $rule_id = $rule->id;
        }

        if ($rule_value === NULL OR $rule_value === '')
        {
            $rule_value = '[edit]';
        }
        if ($rule_meaning === NULL OR $rule_meaning === '')
        {
            $rule_meaning = '[meaning]';
        }
        $id = 'rule_' . $start_weight . '_' . $end_weight . '_' . $sub->id;
        $row[] = $this->block->generate_div($id, $rule_value) . $this->block->generate_div($id . '_meaning', $rule_meaning);
        echo $this->block->generate_editor(
            "$id",
            'rule_form',
            $update_rule_url,
            "{type: 'subarea', rule_id: $rule_id}"
        );
        echo $this->block->generate_editor(
            "{$id}_meaning",
            'rule_form',
            $update_rule_url,
            "{type: 'subarea_meaning', rule_id: $rule_id}"
        );
    }
    $drop_button = $this->block->generate_drop_icon(
        'shipping/shipping_company/drop_rule',
        "{start_weight: $start_weight, end_weight: $end_weight, company_type_id: $company_type_id}",
        TRUE
    );
    $row[] = $drop_button;
    $data[] = $row;    
}

$title = $company->name . ' - '.$shipping_type->type_name . ' - '. $subarea_group->subarea_group_name .' - ' . lang('price_table') ;
echo block_header($title);
echo $this->block->generate_table($head, $data);

$head = array();
$data = array();
$head[] = lang('global_rule');
$id = 'global_rule';
if (empty ($global_rule))
{
    $global_rule = '[edit]';
}
$data[] = array(
    $this->block->generate_div($id, $global_rule),
);
echo $this->block->generate_editor(
    "$id",
    'rule_form',
    $update_rule_url,
    "{type: 'global_rule', company_type_id: $company_type_id}"
);

echo '<br/>';
echo $this->block->generate_table($head, $data, array(), NULL, 'width: 20%;');

?>