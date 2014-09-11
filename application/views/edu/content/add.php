<?php
$head = array(
    lang('name'),
    lang('value'),
);

$config = array(
    'name'        => 'title',
    'id'          => 'title',
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
    'maxlength'   => '200',
    'size'        => '100',
    'value'       => get_current_time(),
);
$data[] = array(
    lang('custom_date'),
    form_input($config),
);

$str = form_dropdown('parent', $parent, isset($document_content) ? $document_content->catalog_id : '');

$data[] = array(
    $this->block->generate_required_mark(lang('possession_catalog')),
    $str,
);

$config = array(
    'name'        => 'document_content',
    'id'          => 'document_content',
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
    $this->block->generate_required_mark(lang('level')),
    form_radio('level', '1', TRUE) . "<image height=20 width=20 title='$title_1' src='$image_url_1' />"
   .form_radio('level', '2', FALSE) . "<image height=20 width=20 title='$title_2' src='$image_url_2' />"
   .form_radio('level', '3', FALSE) . "<image height=20 width=20 title='$title_3' src='$image_url_3' />",
);

$title = lang('add_a_new_document_content');

$back_button = $this->block->generate_back_icon(site_url('edu/content/manage'));
$title .= $back_button ;


echo block_header($title);

$attributes = array(
    'id' => 'document_content_form',
);

echo form_open(site_url('edu/content/save'),$attributes);
echo $this->block->generate_table($head, $data);
echo form_close();

$url = site_url('edu/content/save');
$new_url = site_url('edu/content/manage');
$clue = lang('article_add_succeed');

$config = array(
    'name'        => 'submit',
    'value'       => lang('save_document_content'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "return submit_content(this, '$url', '$new_url','$clue');",
);

$button = block_button($config);

echo '<h2>' . $button . $back_button.'</h2>';

?>