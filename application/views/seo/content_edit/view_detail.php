
<script type="text/javascript" src="<?php echo base_url(); ?>static/js/tiny_mce/tiny_mce.js"></script>
<!-- TinyMCE -->
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "exact",
		elements : "content_area",
		theme : "advanced",
		skin : "o2k7",
		skin_variant : "black",
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups,autosave",

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "<?php echo base_url(); ?>static/css/main.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Replace values for the template plugin
		template_replace_values : {
			username : "mallerp",
			staffid : "991234"
		}
	});
</script>
<!-- /TinyMCE -->


<?php
$head = array(
    lang('name'),
    lang('value'),
);

$data[] = array(
    'Creator',
    $content->user_name,
);

$data[] = array(
    'Title',
    $content->title,
);

$data[] = array(
    'Type',
    $content->type_name,
);

foreach ($catalogs as $catalog)
{
    $options[$catalog->id] = $catalog->name;
}
$cat_count = count($options);
$data[] = array(
    $this->block->generate_required_mark('Category'),
    form_dropdown(
        'catalogs[]',
        $options,
        $catalog_ids ? object_to_array($catalog_ids, 'catalog_id') : array_keys(($options)),
        "id='catalogs' size=" . "'$cat_count'"
    ),
);

$config = array(
    'name'        => 'content_area',
    'id'          => 'content_area',
    'rows'        => '30',
    'value'       => $content ? $content->content : '',
    'cols'        => '70',
    'style'       => 'width:50%',
);
$data[] = array(
    'Content',
    $popup ? $config['value'] : form_textarea($config),
);

$permission = '';
$permission_array = array();
if ($permissions)
{
    foreach ($permissions as $p)
    {
        $permission_array[] = $p->user_id;
    }
}
foreach ($seo_users as $seo_user)
{
    $config = array(
        'name'        => 'permissions[]',
        'value'       => $seo_user->u_id,
        'checked'     => in_array($seo_user->u_id, $permission_array) ? TRUE : FALSE,
        'style'       => 'margin:10px',
        'disabled'    => TRUE,
    );
    $permission .= form_checkbox($config) . form_label($seo_user->u_name);
}
$data[] = array(
    $this->block->generate_required_mark('resource permission'),
    $permission,
);
if ($popup)
{
    $back_button = '';
}
else
{
    $back_button = $this->block->generate_back_icon(site_url('seo/content_edit/view_list'));
}
$title = lang('content_detail'). $back_button;

echo block_header($title);

echo $this->block->generate_table($head, $data);

echo '<h2>'.$back_button.'</h2>';


?>
