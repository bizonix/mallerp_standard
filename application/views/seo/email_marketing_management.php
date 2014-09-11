<?php
$url = site_url('seo/email_marketing/add_new_email_template');
$add_button = $this->block->generate_add_icon($url);
$head = array(
    lang('email_code'),
    lang('title'),
    lang('content'),
    lang('creator'),
    lang('remark'),
    lang('options') . $add_button,
);
$data = array();
foreach ($email_tps as $email_tp)
{    
    $edit_button = $this->block->generate_edit_link(site_url('seo/email_marketing/edit', array($email_tp->id)));
    $drop_button = $this->block->generate_drop_icon(
        'seo/email_marketing/drop_edm_email',
        "{id: $email_tp->id}",
        TRUE
    );
    $url = $drop_button.$edit_button;
    $mail_content =  "<p onclick = mail_content_show_hidden($email_tp->id) style = 'cursor:pointer'><span id= 'img_$email_tp->id'> + </span>".lang('view_template')."</p>";
    $mail_content .= "<div id='content_view_$email_tp->id' style = 'display:none'>";
    $mail_content .= $email_tp->content;
    $mail_content .= "</div>";

    $data[] = array(
        $email_tp->code,
        $email_tp->title,
        $mail_content,
        $email_tp->creator,
        $email_tp->remark,
        $url,
    );

}

$title = lang('email_template');
echo block_header($title);
$filters = array(
    array(
        'type'     =>'input',
        'field'    =>'title',
    ),
    array(
        'type'     =>'input',
        'field'    =>'code',
    ),
    array(
        'type'     =>'input',
        'field'    =>'content',
    ),
    NULL,
    NULL,
    NULL,
);
$config = array(
	'filters'    => $filters,
);
echo form_open();
echo $this->block->generate_reset_search($config);
echo $this->block->generate_table($head, $data);
echo form_close();

?>
