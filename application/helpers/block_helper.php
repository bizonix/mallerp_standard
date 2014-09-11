<?php

function block_header($title)
{
    $CI = & get_instance();
    return $CI->block->generate_header($title);
}

function block_table($head, $data, $filters = array(), $key = NULL, $style='width: 100%;', $more = NULL)
{
    $CI = & get_instance();
    return $CI->block->generate_table($head, $data, $filters, $key, $style, $more);
}

function block_js_sortable_table($head, $data, $sort_config = array(), $style='width: 100%;', $more = NULL)
{
    $CI = & get_instance();
    return $CI->block->generate_js_sortable_table($head, $data, $sort_config, $style, $more);
}

function block_add_icon($url, $params = '{}', $float = 'right')
{
    $CI = & get_instance();
    return $CI->block->generate_add_icon($url, $params, $float);
}

function block_add_icon_only($onlick)
{
    $CI = & get_instance();
    return $CI->block->generate_add_icon_only($onlick);
}

function block_cancel_icon_only($onlick)
{
    $CI = & get_instance();
    return $CI->block->generate_cancel_icon_only($onlick);
}

function block_more_icon($url, $param)
{
    $CI = & get_instance();
    return $CI->block->generate_more_icon($url, $param);
}


function block_drop_icon($url, $param, $confirm,  $style="float:right;cursor:pointer; padding: 5px;", $removeNode = 0)
{
    $CI = & get_instance();
    return $CI->block->generate_drop_icon($url, $param, $confirm, $style, $removeNode);
}

function block_back_icon($url, $content_id = NULL, $content_id_2 = NULL)
{
    $CI = & get_instance();
    return $CI->block->generate_back_icon($url, $content_id, $content_id_2);
}

function block_edit_link($url, $new_page = FALSE)
{
    $CI = & get_instance();
    return $CI->block->generate_edit_link($url, $new_page);
}

function block_calendar_icon($id)
{
    $CI = & get_instance();
    return $CI->block->generate_calendar_icon($id);
}

function block_view_link($url, $attributes = array(), $new_page = FALSE, $show_content_id = '', $hide_content_id = '')
{
    $CI = & get_instance();
    return $CI->block->generate_view_link($url, $attributes, $new_page, $show_content_id, $hide_content_id);
}

function block_div($id, $text, $html = '')
{
    $CI = & get_instance();
    return $CI->block->generate_div($id, $text, $html);
}

function block_editor($id, $form_id, $url, $param, $collection = '[]', $on_create = '')
{
    $CI = & get_instance();
    return $CI->block->generate_editor($id, $form_id, $url, $param, $collection, $on_create);
}

function block_required_mark($text)
{
    $CI = & get_instance();
    return $CI->block->generate_required_mark($text);
}

function block_pagination($key = NULL, $params = array(), $content_id = NULL)
{
    $CI = & get_instance();
    return $CI->block->generate_pagination($key, $params, $content_id);
}

function block_reset_search($config = array(), $content_id = '')
{
    $CI = & get_instance();
    return $CI->block->generate_reset_search($config, $content_id);
}

function block_ac($id, $config)
{
    $CI = & get_instance();
    return $CI->block->generate_ac($id, $config);
}

function block_tinymce($textareas = array())
{
    $CI = & get_instance();
    return $CI->block->generate_tinymce($textareas);
}

function block_accordion($config, $id = 'panel_')
{
    $CI = & get_instance();
    return $CI->block->generate_accordion($config, $id);
}

function block_image_input($config)
{
    $CI = & get_instance();
    return $CI->block->generate_image_input($config);
}

function block_permissions($users, $permissions, $user_id = 'user_id', $name = 'permissions[]')
{
    $CI = & get_instance();
    return $CI->block->generate_permissions($users, $permissions, $user_id, $name);
}

function block_search_dropdown($field, $type)
{
    $CI = & get_instance();
    return $CI->block->generate_search_dropdown($field, $type);
}

function block_image($url, $d = array())
{
    $CI = & get_instance();
    return $CI->block->generate_image($url, $d);
}

function block_check_group($group_name, $content)
{
    $CI = & get_instance();
    return $CI->block->generate_check_group($group_name, $content);
}

function block_check_all()
{
    $CI = & get_instance();
    return $CI->block->generate_check_all();
}

function block_select_checkbox($id)
{
    $CI = & get_instance();
    return $CI->block->generate_select_checkbox($id);
}

function block_calendar_setup($input_id, $button_id, $style = 'green')
{
    $CI = & get_instance();
    return $CI->block->generate_calendar_setup($input_id, $button_id, $style);
}

function block_fieldset($legend, $content)
{
    $CI = & get_instance();
    return $CI->block->generate_fieldset($legend, $content);
}

function block_clickable_fieldset($legend, $content, $id = 'display')
{
    $CI = & get_instance();
    return $CI->block->generate_clickable_fieldset($legend, $content, $id);
}

function block_status_image($status)
{
    $CI = & get_instance();
    return $CI->block->generate_status_image($status);
}

function block_entry($title, $content, $save_button = NULL, $open = TRUE)
{
    $CI = & get_instance();
    return $CI->block->gererate_entry($title, $content, $save_button, $open);
}

function block_button($config)
{
    $CI = & get_instance();
    return $CI->block->generate_button($config);
}

function block_form_uploads($name = 'uploads[]', $form_url = FALSE, $more = NULL, $file_type = 'upload_images')
{
    $html = '';
    if ($form_url)
    {
        $html .= "<form action='$form_url' method='post' enctype='multipart/form-data'>";
        $html .= $more;

    }
    $html .= lang($file_type) . ':' . "<input name='$name' type=file multiple size='30px;'>";
    if ($form_url)
    {
        $html .= "&nbsp;&nbsp;" . "<input type='submit' value='" . lang('submit') . "'>";
        $html .= '</form>';
    }
    return $html;
}

function block_center($text)
{
    $CI = & get_instance();
    return $CI->block->generate_center($text);
}

function block_update_content_js($url, $param, $id)
{
    $CI = & get_instance();
    return $CI->block->generate_update_content_js($url, $param, $id);
}

function block_time_picker($input_id, $value = NULL)
{
    $CI = & get_instance();
    return $CI->block->generate_time_picker($input_id, $value);
}

function block_export_button($title, $url)
{
    $CI = & get_instance();
    return $CI->block->generate_export_button($title, $url);
}

function block_notice_div($text)
{
    $CI = & get_instance();
    return $CI->block->generate_notice_div($text);
}

function js_sortabl()
{
    return '<script type="text/javascript" src="' . base_url() . 'static/js/sorttable.js"></script>';
}

function generate_check_box($array, $name, $post = array())
{
    $check_box = "";
    if (empty($post))
    {
        $is_check = FALSE;
    }
    if (empty ($array))
    {
        return;
    }
    foreach ($array as $key => $value)
    {
        if ($post)
        {
            if (in_array($key, $post))
            {
                $is_check = TRUE;
            }
            else
            {
                $is_check = FALSE;
            }
        }
        $data = array(
            'name'        => $name,
            //'id'          => $key,
            'checked'     => $is_check,
            'value'       => $key,
            'style'       => 'margin:10px',
        );
        $check_box .= form_checkbox($data).$value;
    }
    return $check_box;
}
?>
