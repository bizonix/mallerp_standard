
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

$company_permission = '';
$company_permissions_array = array();
if($company_permissions)
{
    foreach ($company_permissions as $cp)
    {
        $company_permissions_array[] = $cp->company_id;
    }
}
foreach ($content_companys as $content_company)
{
    $config = array(
        'name'        => 'company_permissions[]',
        'value'       => $content_company->id,
//        'checked'     => in_array($content_company->id, $company_permissions_array) ? TRUE : FALSE,
        'checked'     => empty ($company_permissions_array) && $content_company->name =='mallerp' ? TRUE : (in_array($content_company->id, $company_permissions_array) ? TRUE : FALSE),
        'style'       => 'margin:10px',
    );
    $company_permission  .= form_checkbox($config) . form_label($content_company->name);
}
$data[] = array(
    $this->block->generate_required_mark('service company'),
     block_check_group('company_permissions[]', $company_permission),
);

$config = array(
      'name'        => 'title',
      'id'          => 'title',
      'value'       => $content ? $content->title : '',
      'maxlength'   => '200',
      'size'        => '100',
);
$data[] = array(
    $this->block->generate_required_mark('title'),
    form_input($config),
);

foreach ($types as $type)
{
    $options[$type->id] = $type->name;
}
$data[] = array(
    $this->block->generate_required_mark('Type'),
    form_dropdown('type', $options, $content ? $content->type : $types[0]->id, 'id="type"'),
);

$language_options = array(
    'EN'    => 'EN',
    'DE'    => 'DE',
    'FR'    => 'FR',
    'CN'    => 'CN',
);
$js = 'id="language" ';

$data[] = array(
    $this->block->generate_required_mark('Language'),
    form_dropdown('language', $language_options, $content ? $content->language : 'EN', $js),
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
    $this->block->generate_required_mark('Content'),
    form_textarea($config),
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
    );
    $permission .= form_checkbox($config) . form_label($seo_user->u_name);
}
$data[] = array(
    $this->block->generate_required_mark('content permission'),
     block_check_group('permissions[]', $permission),
);



if ($content)
{
     $priority = $this->user_model->fetch_user_priority_by_system_code('seo');
     $integral_url = site_url('seo/content_edit/add_edit_content_integral', array('type' => 'content'));
     $seo_content =$this->seo_model->fetch_content_integral($content->id);
     $user_integral = $this->seo_model->fetch_user_integral($content->id, 'content');    
     $config = array(
         'name'       => 'integral',
         'id'         => 'integral',
         'value'      => isset($user_integral->integral) ? $user_integral->integral : $seo_content->integral,
         'size'       => 4,
     );
     $integral_str = form_input($config);
     $user_name = get_current_user_name();
     $CI = & get_instance();
     if($priority >1 || $CI->is_super_user())
     {
         $data[] = array(
             lang('integral_review'),
             $integral_str,
        );
     }
     else
     {
         $data[] = array(
             lang('integral'),
             isset($integral) ? $integral : '0',
        );
     }

    $title = lang('edit_content');

}
else
{
    $title = lang('add_a_new_content');
}
$title .= $this->block->generate_back_icon(site_url('seo/content_edit/manage'));
$back_button = $this->block->generate_back_icon(site_url('seo/content_edit/manage'));

echo block_header($title);

$attributes = array(
    'id' => 'content_form',
);
echo form_open(site_url('seo/content_edit/save'), $attributes);
echo $this->block->generate_table($head, $data);
if($content)
{
    $url = site_url('seo/content_edit/edit_save');
}
else
{
    $url = site_url('seo/content_edit/add_save');
}

$params = "{type: $('type').value,
            content_area: tinyMCE.get('content_area').getContent(),
            content_id: $('content_id').value,
            language: $('language').value,
            'catalogs[]': \$F('catalogs'),
            'permissions[]': \$F('permissions')
            }";
$params = "\$H($('content_form').serialize(true)).merge({content_area: tinyMCE.get('content_area').getContent()})";
$config = array(
    'name'        => 'submit',
    'value'       => 'Save content!',
    'type'        => 'button',
    'style'       => 'margin:10px;padding:5px;',
    'onclick'     => "this.blur();helper.ajax('$url', $params, 1);",
);
echo '<h2>'.form_input($config).$back_button.'</h2>';

$config = array(
    'name'        => 'content_id',
    'id'          => 'content_id',
    'value'       => $content ? $content->id : '-1',
    'type'        => 'hidden',
);
echo form_input($config);

echo form_close();
?>
