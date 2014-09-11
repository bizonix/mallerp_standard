<?php
$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
    'name'        => 'title',
    'id'          => 'title',
    'value'       => $document_content ? $document_content->dc_title : '',
    'maxlength'   => '200',
    'size'        => '100',
);
$data[] = array(
    $this->block->generate_required_mark(lang('title')),
    form_input($config),
);

$config = array(
    'name'        => 'custom_date',
    'id'          => 'custom_date',
    'value'       => $document_content->dc_custom_date ?  $document_content->dc_custom_date : $document_content->dc_edited_date,
    'maxlength'   => '200',
    'size'        => '100',
);
$data[] = array(
    lang('custom_date'),
    form_input($config),
);

$str = form_dropdown('parent', $parent, $document_content->catalog_id);

$data[] = array(
    $this->block->generate_required_mark(lang('possession_catalog')),
    $str,
);

$config = array(
    'name'        => 'document_content',
    'id'          => 'document_content',
    'value'       => $document_content ? $document_content->dc_content : '',
    'maxlength'   => '80',
    'size'        => '20',
);
$data[] = array(
    $this->block->generate_required_mark(lang('document_content')),
    form_textarea($config),
);

echo $this->block->generate_tinymce(array('document_content'));

$image_url_1 = base_url().'static/images/icons/flag/level-1.gif';
$image_url_2 = base_url().'static/images/icons/flag/level-2.gif';
$image_url_3 = base_url().'static/images/icons/flag/level-3.gif';

$title_1 = lang('normal');
$title_2 = lang('important');
$title_3 = lang('very_important');

$data[] = array(
    lang('level'),
    form_radio('level', '1', $document_content->level == 1 ? TRUE : FALSE) . "<image height=20 width=20 title='$title_1' src='$image_url_1' />"
   .form_radio('level', '2', $document_content->level == 2 ? TRUE : FALSE) . "<image height=20 width=20 title='$title_2' src='$image_url_2' />"
   .form_radio('level', '3', $document_content->level == 3 ? TRUE : FALSE) . "<image height=20 width=20 title='$title_3' src='$image_url_3' />",
);


$file_list_html = '<div id="current_file_div">';

if($action == 'edit')
{
    foreach ($files as $file)
    {
        $delete_url = site_url('edu/content/drop_file', array($file->id, $document_content->dc_id));
        $dowm_url = site_url('edu/content/down_file', array($file->id));

        $file_list_html .= lang('file_name').' : '.$file->before_file_name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
                        .anchor($dowm_url, lang('dowm_file')).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
                        .'<a href="#" onclick="delete_file('."'$delete_url'".'); return false;" >'.lang('delete').'</a>'.'<br>'
                        .$file->file_description.'<br/><br/>';
    }

    $file_list_html .= '</div">';
    
    $data[] = array(
        lang('file_list'),
        $file_list_html,
    );
}
else
{
    foreach ($files as $file)
    {
        $delete_url = site_url('edu/content/drop_file', array($file->id, $document_content->dc_id));
        $dowm_url = site_url('edu/content/down_file', array($file->id));

        $file_list_html .= lang('file_name').' : '.$file->before_file_name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
                        .anchor($dowm_url, lang('dowm_file')).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
                        .$file->file_description.'<br/><br/>';
    }

    $file_list_html .= '</div">';

    $data[] = array(
        lang('file_list'),
        $file_list_html,
    );
}


if($action == 'edit')
{
    $title = lang('edit_document_content');
}
else
{
    $title = lang('add_a_new_document_content');
}

$back_button = $this->block->generate_back_icon(site_url('edu/content/manage'), 'main-contain');


echo block_header($title);

$attributes = array(
    'id' => 'document_content_form',
);

echo form_open(site_url('edu/content/save'),$attributes);
echo $this->block->generate_table($head, $data);
echo form_hidden('content_id', $document_content ? $document_content->dc_id : -1 );
echo form_close();

if($action === 'edit')
{
    $url = site_url('edu/content/save');
    $config = array(
        'name'        => 'submit',
        'value'       => lang('save_document_content'),
        'type'        => 'button',
        'style'       => 'margin:10px',
        'onclick'     => "return submit_content_edit(this, '$url');",
    );

    $button = block_button($config);
    
    echo '<h2>' . $button . '</h2>';

    include(APPPATH.'views/edu/content/upload.php');
}
else
{
    echo '<h2>'.$back_button.'</h2>';
}

$js_array = '$A([';
if (isset($document_content))
{
    $items = explode('>', $document_content->dcata_path);
    $count = count($items);
    for ($i = 0; $i < $count; $i++)
    {
        $js_array .= $items[$i];
        if ($i < $count -1)
        {
            $js_array .= ',';
        }
    }
}
$js_array .= '])';

?>

<script>
    document.observe('dom:loaded', function(){
        extand_catalog(<?=$js_array?>);
    });
</script>