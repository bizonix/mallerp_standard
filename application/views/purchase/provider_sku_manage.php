<?php
$data = array(
    '1'     => '1',
    '2'     => '2',
    '3'     => '3',
);
$collection = to_js_array($data);
$url = site_url('purchase/provider/add_provider_sku',array( 'provider_id' => $provider_id));
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('sku'),
    lang('picture'),
    lang('1_to_9_price'),
    lang('10_to_99_price'),
    lang('100_to_999_price'),
    lang('1000_price'),
    lang('provider_sequence'),
    lang('separating_shipping_cost'),
    lang('options').$add_button,
);
$sku_url = site_url('purchase/provider/update_provider_sku',array( 'provider_id' => $provider_id));
$data = array();
foreach ($skus as $sku)
{
    $drop_button = $this->block->generate_drop_icon(
            'purchase/provider/drop_provider_sku',
            "{id: $sku->m_id}",
            TRUE
        );
    $data[] = array(       
        get_status_image($sku->p_sku) . $this->block->generate_div("sku_{$sku->m_id}", isset($sku->p_sku) ?  $sku->p_sku : '[edit]'),
        $this->block->generate_div("image_url_{$sku->m_id}", "<img width='80' height='80' src='{$sku->pm_image_url}' />"),
        $this->block->generate_div("price1to9_{$sku->m_id}", $sku->m_price1to9),
        $this->block->generate_div("price10to99_{$sku->m_id}", $sku->m_price10to99),
        $this->block->generate_div("price100to999_{$sku->m_id}", $sku->m_price100to999),
        $this->block->generate_div("price1000_{$sku->m_id}", $sku->m_price1000),
        $this->block->generate_div("provide_level_{$sku->m_id}", $sku->m_provide_level),
        $this->block->generate_div("separating_shipping_cost_{$sku->m_id}", $sku->m_separating_shipping_cost),
        $drop_button,
    );
    echo $this->block->generate_editor(
        "sku_{$sku->m_id}",
        'sku_form',
        $sku_url,
        "{id: $sku->m_id, type: 'sku'}"
    );
    echo $this->block->generate_editor(
        "price1to9_{$sku->m_id}",
        'price1to9_form',
        $sku_url,
        "{id: $sku->m_id, type: 'price1to9'}"
    );
    echo $this->block->generate_editor(
        "price10to99_{$sku->m_id}",
        'price10to99_form',
        $sku_url,
        "{id: $sku->m_id, type: 'price10to99'}"
    );
    echo $this->block->generate_editor(
        "price100to999_{$sku->m_id}",
        'price100to999_form',
        $sku_url,
        "{id: $sku->m_id, type: 'price100to999'}"
    );
    echo $this->block->generate_editor(
        "price1000_{$sku->m_id}",
        'price1000_form',
        $sku_url,
        "{id: $sku->m_id, type: 'price1000'}"
    );
    echo $this->block->generate_editor(
        "provide_level_{$sku->m_id}",
        'provide_level_form',
        $sku_url,
        "{id: $sku->m_id, type: 'provide_level'}",
        "$collection"
    );
    echo $this->block->generate_editor(
        "separating_shipping_cost_{$sku->m_id}",
        'separating_shipping_cost_form',
        $sku_url,
        "{id: $sku->m_id, type: 'separating_shipping_cost'}"
    );
}

echo $this->block->generate_pagination('provider_sku', array($provider_id));

$filters = array(    
    array(
        'type'      => 'input',
        'field'     => 'sku',
    ),
    array(
        'type'      => 'input',
        'field'     => 'image_url',
    ),
    array(
        'type'      => 'input',
        'field'     => 'price1to9',
    ),
    array(
        'type'      => 'input',
        'field'     => 'price10to99',
    ),
    array(
        'type'      => 'input',
        'field'     => 'price100to999',
    ),
    array(
        'type'      => 'input',
        'field'     => 'price1000',
    ),
    array(
        'type'      => 'input',
        'field'     => 'provide_level',
    ),
);
$config = array(
    'filters'    => $filters,
    'url'        => site_url('purchase/provider/provider_sku_manage', array($provider_id)),
);
echo $this->block->generate_back_icon(site_url('purchase/provider/edit', array($provider_id)));
echo form_open();
echo $this->block->generate_reset_search($config, array('provider_id' => $provider_id));
echo $this->block->generate_table($head, $data, $filters, 'purchase_sku');
echo $this->block->generate_back_icon(site_url('purchase/provider/edit', array($provider_id)));
echo $this->block->generate_pagination('provider_sku', array($provider_id));
echo form_close();
?>
