<?php
$head = array(
    lang('name'),
    lang('path'),
    lang('created_date'),
    lang('options'),
);

$data = array();

foreach ($catalogs as $catalog)
{
    if ($action == 'edit')
    {
        $drop_button = $this->block->generate_drop_icon(
            'edu/catalog/drop_catalog',
            "{id: $catalog->id}",
            TRUE
        );
        $edit_button = $this->block->generate_edit_link(site_url('edu/catalog/edit', array($catalog->id)));
        $url = $drop_button . $edit_button;
    }
    else
    {
        $url = $this->block->generate_view_link(site_url('edu/catalog/view', array($catalog->id)));
    }
    
    $data[] = array(
        $catalog->name,
        $catalog->path,
        $catalog->created_date,
        $url,
    );
}
$title = lang('document_catalog_management');
echo block_header($title);

$filters = array(
    array(
        'type'      => 'input',
        'field'     => 'name',
    ),
    NULL,
    array(
        'type'      => 'input',
        'field'     => 'created_date',
    ),
);

echo $this->block->generate_pagination('document_catalog');

$config = array(
    'filters'    => $filters,
);

echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data, $filters, 'document_catalog');
echo form_close();

echo $this->block->generate_pagination('document_catalog');

?>
