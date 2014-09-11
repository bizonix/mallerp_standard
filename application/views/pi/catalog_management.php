<?php
$head = array( 
    lang('chinese_name'),
    lang('english_name'),
    lang('path'),
    lang('created_date'),
    lang('options'),
);

$data = array();
$index = -1;
foreach ($product_catalogs as $product_catalogs)
{
    $index++;
    if ($action == 'edit')
    {
        $drop_button = $this->block->generate_drop_icon(
            'pi/catalog/drop_catalog',
            "{id: $product_catalogs->id}",
            TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('pi/catalog/edit', array($product_catalogs->id)));
        $url = $drop_button . $edit_button;
    }
    else
    {
        $url = $this->block->generate_view_link(site_url('pi/catalog/view', array($product_catalogs->id)));
    }
    $data[] = array(
        $product_catalogs->name_cn,
        $product_catalogs->name_en,
        $path_cn[$index]."<br/>".$path_en[$index],
        $product_catalogs->created_date,
        $url,
    );
}
$filters = array(
    array(
        'type'      => 'input',
        'field'     => 'name_cn',
    ),
    array(
        'type'      => 'input',
        'field'     => 'name_en',
    ),
    array(),
    array(
       'type'       => 'input',
       'field'      => 'created_date',
    ),
);


echo block_header(lang('catalog_management'));

echo $this->block->generate_pagination('product_catalog');
$config = array(
    'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'product_catalog');
echo $this->block->generate_pagination('product_catalog');
echo form_close();
?>

