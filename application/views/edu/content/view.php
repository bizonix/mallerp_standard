
<style type='text/css' media='all'>@import url('<?php echo base_url(); ?>static/css/document.css');</style>
<?php

$config = array(
    'name'        => 'document_comment',
    'id'          => 'document_comment',
    'maxlength'   => '80',
    'size'        => '20',
);
$data = array(
    form_textarea($config),
);
echo $this->block->generate_tinymce(array('document_comment'));

$attributes = array(
    'id' => 'document_comment_form',
);

$url = site_url('edu/content/comment_save');
$config = array(
    'name'        => 'submit',
    'value'       => lang('save_document_comment'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "return submit_comment(this, '$url');",
);

$button = block_button($config);

$file_list_html = '';

foreach ($files as $file)
{
        $delete_url = site_url('edu/content/drop_file', array($file->id, $document_content->dc_id));
        $dowm_url = site_url('edu/content/down_file', array($file->id));

        $file_list_html .= lang('file_name').' : '.$file->before_file_name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
                        .anchor($dowm_url, lang('dowm_file')).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
                        .$file->file_description.'<br/><br/>';
}

$back_button = $this->block->generate_back_icon(site_url('edu/content/view_list'), 'main-content-detail', 'main-content');
$view_time = $document_content->dc_custom_date ? $document_content->dc_custom_date : $document_content->dc_edited_date;
echo '<h2>'.$back_button.'</h2>';

echo '<div class="content">';

$image_url = base_url()."static/images/icons/flag/level-$document_content->level.gif";

$title = '';
if($document_content->level == 1)
{
    $title = lang('normal');
}
else if ($document_content->level == 2)
{
    $title = lang('important');
}
else if ($document_content->level == 3)
{
    $title = lang('very_important');
}

echo '<div class="title"><h2>' . "<image height=20 width=20 title='$title' src='$image_url' />".$document_content->dc_title . '</h2></div>';
echo '<div class="author">[ ' . $document_content->dcata_path .' ] [ '.$view_time. ' ] [ ' .$document_content->u_name .' ]</div>';
echo '<div class="article">' . $document_content->dc_content . '</div>';

echo '<div class="author"></div>';

echo '<div style="width:70%; margin:auto;"><br/>' . $file_list_html . '</div>';

echo '<br/><br/><B>'.lang('comment_list').'</B>';
echo '<hr size="5" color="#8CC7FB" /><div id="current_comment_div">';

foreach ($comments as $comment)
{
    $delete_url = site_url('edu/content/drop_comment', array($comment->id,$document_content->dc_id));

    echo "<div style='width:600; background-color:#CCCCCC; padding:5px;'>$comment->u_name  发表于 $comment->created_date</div>";
    echo $comment->comment;
    echo '<br><br>';
    if($tag)
    {
        echo '<div align="right"><a href="#" onclick="delete_comment('."'$delete_url'".'); return false;" >'.lang('delete').'</a></div>';
        echo '<br><br>';
    }
}

echo '</div>';

echo '<hr/><br/><B>'.lang('save_document_comment').'</B>';

echo form_open(site_url('edu/content/comment_save'),$attributes);
echo form_hidden('content_id', $document_content->dc_id);
echo $this->block->generate_table(null, $data);
echo form_close();
echo $button;
echo '</div>';

echo '<h2>'.$back_button.'</h2>';

?>