<?php
class MY_Block {
    private $CI = NULL;
    
    public function  __construct() {
        $this->CI = & get_instance();
    }
    
    public function generate_table($head, $data, $filters = array(), $key = NULL, $style='width: 100%;', $more = NULL)
    {
        if ( ! isset($style))
        {
            $style='width: 100%;';
        }
        $tmpl = array(
            'table_open'            => '<table cellspacing="1" cellpadding="0" border="0" class="tableborder" style="'.$style.'">',
            'heading_row_start'     => '<tr>',
            'heading_row_end'       => '</tr>',
            'heading_cell_start'    => '<th>',
            'heading_cell_end'      => '</th>',
            'first_row_start'       => '<tr>',
            'row_start'             => '<tr class="td-odd">',
            'row_end'               => '</tr>',
            'cell_start'            => '<td style="padding: 2px;">',
            'cell_end'              => '</td>',
            'row_alt_start'         => '<tr  class="td">',
            'row_alt_end'           => '</tr>',
            'cell_alt_start'        => '<td style="padding: 2px;">',
            'cell_alt_end'          => '</td>',
            'table_close'           => '</table>',
        );

        $this->CI->table->clear();
        $this->CI->table->set_template($tmpl);
        $this->CI->table->set_heading($head);

        $filters_session = $this->CI->filter->get_filters($key);
        $filter_row = array();
        foreach ($filters as $filter)
        {
            if (isset($filter['type']))
            {
                $name = str_replace('.', '_DOT_', $filter['field']);
                $name = str_replace('|', '_OR_', $name);
                switch ($filter['type'])
                {
                    case 'input':
                        if (isset($filter['method']) && $filter['method'] == 'from_to')
                        {
                            $size = isset($filter['size']) ? $filter['size'] : 12;
                            $input_id = 'filter_' . $name . '_from';
                            $config = array(
                                'name'        => $input_id,
                                'id'          => $input_id,
                                'value'       => isset($filters_session['from_MULT_' . $name]) ? $filters_session['from_MULT_' . $name] : '',
                                'class'       => 'input-text no-changes',
                                'size'        => $size,
                            );
                            $form_str = lang('from') . ' ' . form_input($config);
                            $input_id = 'filter_' . $name . '_to';
                            $config = array(
                                'name'        => $input_id,
                                'id'          => $input_id,
                                'value'       => isset($filters_session['to_MULT_' . $name]) ? $filters_session['to_MULT_' . $name] : '',
                                'class'       => 'input-text no-changes',
                                'size'        => $size,
                            );
                            $form_str .= '<br/>' . lang('to') . ' ' . form_input($config);
                            
                            $filter_row[] = $form_str;
                        }
                        else
                        {
                            $size = isset($filter['size']) ? $filter['size'] : 12;
                            $config = array(
                                'name'        => $name,
                                'id'          => 'filter_' . $name,
                                'value'       => isset($filters_session[$name]) ? $filters_session[$name] : '',
                                'class'       => 'input-text no-changes',
                                'size'        => $size,
                            );
                            $method = isset($filter['method']) ? $filter['method'] : '';
                            $filter_row[] = $method . ' ' . form_input($config);
                        }
                        break;
                    case 'dropdown':
                        if (isset($filter['options']))
                        {
                            $filter_row[] = form_dropdown($name, $filter['options'], isset($filters_session[$name]) ? $filters_session[$name] : (isset($filter['default']) ? $filter['default'] : NULL), "id='filter_$name' onchange=$('search_button').click();");
                        }
                        else
                        {
                            $filter_row[] = null;
                        }
                        break;
                    case 'date':
                        $input_id = 'filter_' . $name . '_from';
                        $button_id = 'calendar_' . $name . '_from';
                        $config = array(
                            'name'        => $input_id,
                            'id'          => $input_id,
                            'value'       => isset($filters_session['from_MULT_' . $name]) ? $filters_session['from_MULT_' . $name] : '',
                        );
                        echo $this->generate_calendar_setup($input_id, $button_id);
                        $from_calendar = lang('from') . $this->generate_calendar_icon($button_id) . form_input($config);

                        $input_id = 'filter_' . $name . '_to';
                        $button_id = 'calendar_' . $name . '_to';
                        $config = array(
                            'name'        => $input_id,
                            'id'          => $input_id,
                            'value'       => isset($filters_session['to_MULT_' . $name]) ? $filters_session['to_MULT_' . $name] : '',
                        );
                        echo $this->generate_calendar_setup($input_id, $button_id);
                        $to_calendar = lang('to') . $this->generate_calendar_icon($button_id) . form_input($config);

                        $filter_row[] = $from_calendar . '<br/>' . $to_calendar;
                        break;

                    default :
                        $filter_row[] = NULL;
                        break;
                }
            }
            else {
                $filter_row[] = NULL;
            }
        }
        
        while (count($filter_row) < count($head))
        {
            $filter_row[] = NULL;
        }
        
        $filter_row_with_content = FALSE;
        foreach ($filter_row as $item)
        {
            if ($item !== NULL)
            {
                $filter_row_with_content = TRUE;
                break;
            }
        }
        if ($filter_row_with_content)
        {
            $this->CI->table->add_row($filter_row);
        }

        if (is_array($data))
        {
            foreach ($data as $row)
            {
                if ($more)
                {
                    foreach ($more as $one)
                    {
                        $row[] = $one;
                    }

                }
                $this->CI->table->add_row($row);
            }

            return $this->CI->table->generate();
        }
        else if (is_object($data))
        {
            return $this->CI->table->generate($data);
        }
    }

    public function generate_js_sortable_table($head, $data, $sort_config = array(), $style='width: 100%;border-collapse: collapse;', $more = NULL)
    {
        $tmpl = array(
            'table_open'            => '<table cellspacing="1" cellpadding="0" border="1" class="sortable tableborder" style="'.$style.'">',
            'heading_row_start'     => '<tr>',
            'heading_row_end'       => '</tr>',
            'heading_cell_start'    => '<th>',
            'heading_cell_end'      => '</th>',
            'first_row_start'       => '<tr style="color:red;">',
            'row_start'             => '<tr class="td-odd" style="background-color: #ffffff">',
            'row_end'               => '</tr>',
            'cell_start'            => '<td style="padding-bottom: 10px;padding-top: 10px;">',
            'cell_end'              => '</td>',
            'row_alt_start'         => '<tr  class="td">',
            'row_alt_end'           => '</tr>',
            'cell_alt_start'        => '<td  style="padding-bottom: 10px;padding-top: 10px;">',
            'cell_alt_end'          => '</td>',
            'table_close'           => '</table>'
        );

        $this->CI->table->clear();
        $this->CI->table->set_template($tmpl);
        $this->CI->table->set_heading($head);

        if (is_array($data))
        {
            foreach ($data as $row)
            {
                if ($more)
                {
                    foreach ($more as $one)
                    {
                        $row[] = $one;
                    }

                }
                $this->CI->table->add_row($row);
            }

            return $this->CI->table->generate(NULL, $sort_config);
        }
        else if (is_object($data))
        {
            return $this->CI->table->generate($data, $sort_config);
        }
    }
    
    public function generate_add_icon($url, $params = '{}', $float = 'right')
    {
        $add_url = $url;
        if (strpos($add_url, 'http://') === FALSE)
        {
            $add_url = site_url($url);
        }
        $base_url = base_url();
$add_button = <<<BUTTON
    <span style="float:$float;cursor:pointer"
    onclick="helper.add_row('$add_url', $params);">
        <img src="{$base_url}static/images/icons/add.png" style="float:$float;cursor:pointer"/>
    </span>
BUTTON;

        return $add_button;
    }

    public function generate_add_icon_only($onlick)
    {
        $base_url = base_url();
        
$add_button = <<<BUTTON
    <span style="cursor:pointer; padding-top: 5px;"
    onclick="$onlick">
        <img src="{$base_url}static/images/icons/add.png"/>
    </span>
BUTTON;

        return $add_button;
    }

    public function generate_cancel_icon_only($onlick)
    {
        $base_url = base_url();

$add_button = <<<BUTTON
    <span style="cursor:pointer; padding-top: 5px;"
    onclick="$onlick">
        <img src="{$base_url}static/images/icons/cancel.gif"/>
    </span>
BUTTON;

        return $add_button;
    }

    public function generate_more_icon($url, $param)
    {
        $more_url = site_url($url);
        $base_url = base_url();
        $add_more = lang('add_more');
$more_button = <<<BUTTON
    <span style="float:left;cursor:pointer"
    onclick="helper.add_row('$more_url', $param);" title="$add_more">
        <img src="{$base_url}static/images/icons/more.gif"/>
    </span>
BUTTON;

        return $more_button;
    }

    public function generate_drop_icon($url, $param, $confirm, $style="float:right;cursor:pointer; padding: 5px;", $removeNode = 0)
    {
        $confirm_msg = '';
        if ($confirm == TRUE)
        {
            $confirm_msg = lang('confirm_msg');
        }
        if (strpos($url, 'http://') === FALSE)
        {
            $drop_url = site_url($url);
        }
        else
        {
            $drop_url = $url;
        }
        $base_url = base_url();
$drop_button = <<<BUTTON
        <span style="$style" onclick="helper.drop_row(this, '$confirm_msg', '$drop_url', $param, $removeNode);">
            <img src="{$base_url}static/images/icons/drop.png"/>
        </span>
BUTTON;

            return $drop_button;
    }

    public function generate_back_icon($url, $content_id = NULL, $content_id_2 = NULL)
    {
        if (strpos($url, 'http://') === FALSE)
        {
            $url = site_url($url, array('page', $this->CI->filter->get_offset('order')));
        }
        $onclick = '';
        if ($content_id && $content_id_2)
        {
            $onclick = "onClick='helper.toggle_content(\"$content_id\", \"$content_id_2\"); return false;'";
        }
        else if ($content_id)
        {
            $onclick = "onClick='helper.update_content(this.href, {}, \"$content_id\"); return false;'";
        }

        $base_url = base_url();
$drop_button = <<<BUTTON
        <a style="float:right;cursor:pointer; padding: 5px;" href="$url" $onclick>
            <img src="{$base_url}static/images/icons/back.png" title="back" alt="back"/>
        </a>
        <div style="clear:right;"></div>
BUTTON;

            return $drop_button;
    }

    public function generate_edit_link($url, $new_page = FALSE)
    {
        $base_url = base_url();
        $onclick = '';
        $target = '';
        if ($new_page) {
            $target = 'target="_blank"';
        }
$edit_button = <<<BUTTON
        <span style="float:right;cursor:pointer; padding-left: 5px;padding-right: 5px;">
            <a href="$url" $target >
                <img src="{$base_url}static/images/icons/edit.png"/>
            </a>
        </span>
BUTTON;

        return $edit_button;
    }

    public function generate_calendar_icon($id)
    {
        $base_url = base_url();
        $icon =<<< ICON
<img src="{$base_url}static/images/icons/grid-cal.gif" title="calendar" title="calendar" id="$id"/>
ICON;
        
        return $icon;
    }

    public function generate_view_link($url, $attributes = array(), $new_page = FALSE, $show_content_id = '', $hide_content_id = '')
    {
        $base_url = base_url();
        if ($new_page) {
            $attributes['target'] = "_blank";
        }
        if ($show_content_id)
        {
            $attributes['onClick'] = "helper.ajax_toggle_content(this.href, {}, '$show_content_id', '$hide_content_id'); return false;";
        }
        $anchor = anchor($url, "<img src='{$base_url}static/images/icons/view.png'/>", $attributes);
$edit_button = <<<BUTTON
        <span style="float:right;cursor:pointer;padding: 5px;">
            $anchor
        </span>
BUTTON;

        return $edit_button;
    }

    public function generate_div($id, $text, $html = '')
    {
        return "<div id='$id' '$html' >$text</div>";
    }

    public function generate_editor($id, $form_id, $url, $param, $collection = '[]', $on_create = '')
    {
$script =<<< SCRIPT
<script type="text/javascript">
    document.observe('dom:loaded', function() {
        helper.editor(
            '$id',
            "$url",
            '$form_id',
            $param,
            $collection,
            "$on_create"
        );
    });
</script>
SCRIPT;

        return $script;
    }

    public function generate_required_mark($text)
    {
        return $text . '<span style="color: red"> *</span>';
    }

    public function generate_pagination($key = NULL, $params = array(), $content_id = NULL)
    {
        $total = $this->CI->filter->get_total($key);
        $limit = $this->CI->filter->get_limit($key);
        if (empty ($limit))
        {
            $limit = 20;
        }
        if (empty($total) || $total <= 0)
        {
            return '';
        }

        $config['full_tag_open'] = '<div style="margin:0;margin-top:10px;"><center>';
        $config['base_url'] = fetch_request_uri();
        $config['total_rows'] = $total;
        $config['per_page'] = $limit;
        $config['uri_segment'] = request_uri_count() + count($params) + 2;
        $config['num_links'] = 5;
        $config['full_tag_close'] = '</center></div>';
        $config['first_link'] = '&lt;&lt;' . lang('first');
        $config['last_link'] =  lang('last') . '&gt;&gt;';
        $config['num_tag_open'] = '<span class="pageOff">';
        $config['num_tag_close'] = '</span>';
        $config['cur_tag_open'] = '<span class="current">';
        $config['cur_tag_close'] = '</span>';
        $config['next_tag_open'] = '<span class="pageOff">';
        $config['next_tag_close'] = '</span>';
        $config['prev_tag_open'] = '<span class="pageOff">';
        $config['prev_tag_close'] = '</span>';
        $config['last_tag_open'] = '<span class="pageOff">';
        $config['last_tag_close'] = '</span>';
        $config['first_tag_open'] = '<span class="pageOff">';
        $config['first_tag_close'] = '</span>';

        $this->CI->pagination->initialize($config);

        return $this->CI->pagination->create_links($params, $key, $content_id);
    }

    public function generate_reset_search($config = array(), $content_id = '')
    {
        $url = site_url(fetch_request_uri());
        if (isset($config['url']))
        {
            $url = $config['url'];
        }        
        $key = NULL;
        if (isset($config['key']))
        {
            $key = $config['key'];
        }
        $params = '{}';
        if (isset($config['filters']))
        {
            $filters = $config['filters'];
            $params = '{';
            foreach ($filters as $filter)
            {
                if (isset($filter['field']))
                {
                    $field_method = 'LIKE';
                    if (isset($filter['method']))
                    {
                        $field_method = $filter['method'];
                    }
                    switch ($field_method)
                    {
                        case 'from_to':
                            $field = $filter['field'];
                            $field = str_replace('.', '_DOT_', $field);
                            $field = str_replace('|', '_OR_', $field);
                            $field_method = '>=';
                            $from_field = $field . '_from';
                            $params .= "'from_MULT_$field': \$('filter_$from_field').value,'{$field}_method': '$field_method',";
                            $field_method = '<=';
                            $to_field = $field . '_to';
                            $params .= "'to_MULT_$field': \$('filter_$to_field').value,'{$to_field}_method': '$field_method',";
                            break;
                        default :
                            $field = $filter['field'];
                            $field = str_replace('.', '_DOT_', $field);
                            $field = str_replace('|', '_OR_', $field);
                            $params .= "'$field': \$('filter_$field').value,'{$field}_method': '$field_method',";
                            break;
                    }
                }
            }
            $params = rtrim($params, ',');
            $params .= '}';
        }

        $config = array(
            'name'        => 'search_button',
            'id'          => 'search_button',
            'type'        => 'submit',
            'value'       => lang('search'),
            'class'       => 'form-button',
            'style'       => 'float: right',
            'onclick'      => "helper.reset_or_search('$url', $params, '$content_id');return false;",
        );
        $buttons = form_input($config);
        $params = "{reset: 'reset'}";
        $config = array(
            'name'        => 'reset_button',
            'id'          => 'reset_button',
            'type'        => 'button',
            'value'       => lang('reset'),
            'class'       => 'form-button',
            'style'       => 'float: right',
            'onclick'      => "helper.reset_or_search('$url', $params, '$content_id');return false;",
        );
        $buttons .= form_input($config) . '<div style="clear:right;"></div>';
        return $buttons;
    }

    public function generate_ac($id, $config)
    {
        $uri = 'mallerp/ac';
        $url = site_url_no_key($uri, $config);
        $key = site_key($uri);

        $script =<<< SCRIPT
<script type="text/javascript">
    document.observe('dom:loaded', function() {
        new AutoComplete('$id', "$url", {
            method: 'POST',
            delay: .25,
            resultFormat: AutoComplete.Options.RESULT_FORMAT_JSON
        }, "$key");
    });
</script>
SCRIPT;

        return $script;
    }

    public function generate_tinymce($textareas = array(), $simple = FALSE)
    {
        $elements = implode(',', $textareas);
        $url = base_url();
        if ($simple)
        {
            $script =<<< SCRIPT
<script type="text/javascript" src="$url/static/js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
<!-- TinyMCE -->
	tinyMCE.init({
		mode : "textareas",
		theme : "simple"
	});
<!-- /TinyMCE -->
</script>
SCRIPT;
        }
        else
        {
        $script =<<< SCRIPT
<script type="text/javascript" src="$url/static/js/tiny_mce/tiny_mce.js"></script>
<!-- TinyMCE -->
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "exact",
		elements : "$elements",
		theme : "advanced",
		skin_variant : "black",
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",

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
		content_css : "{$url}static/css/main.css",

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
SCRIPT;
        }
        return $script;
    }

    public function generate_accordion($config, $id = 'panel_')
    {
        $source = '';
        $i = 0;
        foreach ($config as $head => $body)
        {
            $i++;
            $source .= "<div class='panel' id='{$id}{$i}'>";
            $source .= "<h5 ";
            $source .= $body !== NULL ? "onClick='accordion(this);'" : '';
            $source .= "class='panel_title_tab'>$head";
            $source .= is_array($body) ? ' + ' : '';
            $source .= "</h5>";
            if (is_array($body))
            {
                $source .= "<div class='panel_body' id='{$id}{$i}-body'>" . $this->generate_accordion($body, $id . $i) . "</div>";
            }
            else
            {
                $source .= $body !== NULL ? "<div class='panel_body' id='{$id}{$i}-body'> <div>$body</div> </div>" : '';
            }
            $source .= '</div>';
        }

        return $source;
    }

    public function generate_image_input($config)
    {
        $base_url = base_url();
        
        if (! isset($config['id']))
        {
            $image_id = 'image_' . time();
        }
        else
        {
            $image_id = 'image_' . $config['id'];
        }
        if ( ! isset($config['onchange']))
        {
            $config['onchange'] = "javascript: \$('$image_id').src = this.value";
        }
        $value = '';
        if (isset($config['value']))
        {
            $value = $config['value'];
        }
        else
        {
            $value = "{$base_url}static/images/icons/dot.png";
        }
        $span = '';
            $span =<<<SPAN
<br/>
<span>
    <img id='$image_id' src="$value" width='100px;' height='100px;'/>
</span>
SPAN;
            
        return form_input($config) . $span;
    }

    public function generate_permissions($users, $permissions, $user_id = 'user_id', $name = 'permissions[]')
    {
        $permission_html = '';
        $permission_array = array();
        if ($permissions)
        {
            foreach ($permissions as $p)
            {
                $permission_array[] = $p->$user_id;
            }
        }
        foreach ($users as $user)
        {
            $config = array(
                'name'        => "$name",
                'value'       => $user->u_id,
                'checked'     => in_array($user->u_id, $permission_array) ? TRUE : FALSE,
                'style'       => 'margin:10px',
            );
            $permission_html .= form_checkbox($config) . form_label($user->u_name);
        }

        return $permission_html;
    }

    public function generate_search_dropdown($field, $type)
    {
        $options = array();
        $options[' '] = lang('all');  // search will ignore this condition.
        $statuses = fetch_statuses($type);
        foreach ($statuses as $key => $value)
        {
            $options[$key] = lang($value);
        }
        
        return array(
            'type'      => 'dropdown',
            'field'     => $field,
            'options'   => $options,
            'method'    => '=',
        );
    }

    public function generate_image($url, $d = array())
    {
        $width_height = " height='100' ";
        if (count($d))
        {
            $width_height = "width='$d[0]' height='$d[1]' ";
        }
        $img =<<< IMG
<a target='_blank' href="$url">
    <img src="$url" $width_height/>
</a>
IMG;
        return $img;
    }

    /*
     * check all item list.
     */
    public function generate_check_all()
    {
        $url = base_url();
        $check_all = lang('check_all');
        $uncheck_all = lang('uncheck_all');
        $div =<<< DIV
<span style='margin-left: 15px;'>
<img src="{$url}static/images/icons/arrow_ltr.png"/>
<a onclick="helper.check_all(); return false;" style="cursor: pointer;">
    $check_all
</a>
/
<a onclick="helper.uncheck_all(); return false;" style="cursor: pointer;">
    $uncheck_all
</a>
</span>
DIV;

        return $div;
    }

    public function generate_check_group($group_name, $content)
    {
        $check_all = lang('check_all');
        $uncheck_all = lang('uncheck_all');
        
        $legend = <<< HTML
<a onclick="helper.check_group('$group_name'); return false;" style="cursor: pointer;">
    $check_all
</a>
/
<a onclick="helper.uncheck_group('$group_name'); return false;" style="cursor: pointer;">
    $uncheck_all
</a>
HTML;
        return $this->generate_fieldset($legend, $content);
    }

    public function generate_select_checkbox($id)
    {
        $config = array(
            'name'        => 'checkbox_select_' . $id,
            'id'          => 'checkbox_select_' . $id,
            'value'       => $id,
            'checked'     => FALSE,
            'style'       => 'margin:15px',
        );

        return form_checkbox($config);
    }

    public function generate_calendar_setup($input_id, $button_id, $style = 'green')
    {
        $this->CI->template->add_js('static/js/calendar/calendar.js');
        $this->CI->template->add_js('static/js/calendar/calendar-setup.js');
        $this->CI->template->add_css("static/css/calendar/calendar-$style.css");
        $javascript = <<<JS
            <script type="text/javascript">
                //<![CDATA[
                enUS = {"m":{"wide":["January","February","March","April","May","June","July","August","September","October","November","December"],"abbr":["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]}}; // en_US locale reference
                Calendar._DN = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]; // full day names
                Calendar._SDN = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"]; // short day names
                Calendar._FD = 0; // First day of the week. "0" means display Sunday first, "1" means display Monday first, etc.
                Calendar._MN = ["January","February","March","April","May","June","July","August","September","October","November","December"]; // full month names
                Calendar._SMN = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]; // short month names
                Calendar._am = "AM"; // am/pm
                Calendar._pm = "PM";

                // tooltips
                Calendar._TT = {};
                Calendar._TT["INFO"] = "About the calendar";

                Calendar._TT["ABOUT"] = "DHTML Date/Time Selector Author: Mihai Bazon";

                Calendar._TT["PREV_YEAR"] = "Prev. year (hold for menu)";
                Calendar._TT["PREV_MONTH"] = "Prev. month (hold for menu)";
                Calendar._TT["GO_TODAY"] = "Go Today";
                Calendar._TT["NEXT_MONTH"] = "Next month (hold for menu)";
                Calendar._TT["NEXT_YEAR"] = "Next year (hold for menu)";
                Calendar._TT["SEL_DATE"] = "Select date";
                Calendar._TT["DRAG_TO_MOVE"] = "Drag to move";
                Calendar._TT["PART_TODAY"] = ' (' + "Today" + ')';

                // the following is to inform that "%s" is to be the first day of week
                Calendar._TT["DAY_FIRST"] = "Display %s first";

                // This may be locale-dependent. It specifies the week-end days, as an array
                // of comma-separated numbers. The numbers are from 0 to 6: 0 means Sunday, 1
                // means Monday, etc.
                Calendar._TT["WEEKEND"] = "6";

                Calendar._TT["CLOSE"] = "Close";
                Calendar._TT["TODAY"] = "Today";
                Calendar._TT["TIME_PART"] = "(Shift-)Click or drag to change value";

                // date formats
                Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
                Calendar._TT["TT_DATE_FORMAT"] = "%Y-%m-%d";

                Calendar._TT["WK"] = "Week";
                Calendar._TT["TIME"] = "Time:";

                CalendarDateObject._LOCAL_TIMZEONE_OFFSET_SECONDS = -28800;

                //]]>
                document.observe('dom:loaded', function () {
                    Calendar.setup({
                        inputField : "$input_id",
                        ifFormat : "%Y-%m-%d 00:00:00",
                        button : "$button_id",
                        showsTime: false,
                        align : "Bl",
                        singleClick : true
                    });
                });
            </script>
JS;
        return $javascript;
    }

    public function generate_fieldset($legend, $content)
    {
        $html = <<< HTML
<fieldset>
    <legend>
        $legend
    </legend>
    $content
</fieldset>
HTML;

        return $html;
    }

    public function generate_clickable_fieldset($legend, $content, $id = 'display')
    {
        $fieldset_legend = "fieldset_legend_$id";
        $fieldset = "fieldset_$id";
        $html = <<< HTML
<span id="$fieldset_legend">+ </span>
<a onClick="helper.show_update('$fieldset', '$fieldset_legend', '- ', '+ ');" href="javascript:void(0)">$legend</a>
<div style="display: none; margin: 0px;" id="$fieldset">
    <fieldset>
        $content
    </fieldset>
</div>
HTML;

        return $html;
    }

    public function generate_header($title)
    {
        $html = <<< HTML
<div class='content-header'>
    <h3>
        $title
    </h3>
</div>
HTML;

        return $html;
    }

    public function generate_status_image($status)
    {
        if ($status == 1)
        {
            $status = 'alive';
            $title = 'active';
        }
        else if ($status == -1)
        {
            $status = 'dead';
            $title = 'disabled';
        }
        else
        {
            return '';
        }
        $base_url = base_url();
        
        return "<img src='{$base_url}static/images/icons/status_{$status}.png' width='10px' title='$title' />";
    }

    public function gererate_entry($title, $data, $save_button = NULL, $open = TRUE)
    {
        if ($save_button)
        {
            echo form_open();
            $save_button['style']   = 'margin:10px;padding:5px;float:right;';
            $save_button = $this->generate_button($save_button);
        }
        if (is_array($data))
        {
        $content = <<<CONTENT
<table cellspacing="0" class="form-list" style="width: 100%;">
                    <tbody>
CONTENT;
        foreach ($data as $item)
        {
            $content .= <<<CONTENT
                        <tr>
                            <td class="label"  style="width: 10%; align: left; padding: 10px;">
                            <label style="padding-right:30px;" for="">
                                {$item['label']}
                            </label>
                            </td>
                            <td class="value">
                                {$item['value']}
                            </td>
                        </tr>
CONTENT;
        }
        if ($save_button)
        {
            $content .=<<<  CONTENT
                        <tr>
                            <td class="label"  style="padding: 10px;"><label style="padding-right:30px;" for=""></label>
                            </td>
                            <td class="value">
                                $save_button
                            </td>
                        </tr>
CONTENT;
        }
        }
        else
        {
            $content = $data;
        }
        $style = 'display:none';
        if ($open)
        {
            $open = "class='open' ";
            $style = 'display:block';
        }
        if (is_array($data))
        {
        $content .= <<< CONTENT
                    </tbody>
                </table>
CONTENT;
        }
        $html = <<< HTML

<div class="entry-edit">
        <div class="entry-edit-head  collapseable">
            <h4 class="icon-head head-edit-form fieldset-legend">
            <a $open onclick="this.blur(); return helper.toggle_parent_next(this, 2, 'open');return false;" href="#" id="general_country-head" class="">{$title}</a></h4>
        </div>
        <div  class="fieldset " style="$style">
            <div class="hor-scroll">
                $content <br/>
            </div>
        </div>
</div>
HTML;
        if ($save_button)
        {
            echo form_close();
        }

        return $html;
    }

    public function generate_button($config)
    {
        if ( ! isset($config['type']))
        {
            $config['type']    = 'button';
        }
        $config['class']   = 'form-button';
        
        return form_input($config);
    }

    public function generate_center($text)
    {
        return "<center>$text</center>";
    }

    public function generate_update_content_js($url, $param, $id)
    {
        echo <<< SCRIPT
            <script type="text/javascript">
                //<![CDATA[
                helper.update_content('$url', $param, '$id', 0);
                //]]>
            </script>
SCRIPT;
    }

    public function generate_time_picker($input_id, $value = NULL)
    {
        $button_id = 'button_' . $input_id;
        $config = array(
            'name'        => $input_id,
            'id'          => $input_id,
            'value'       => $value,
        );
        echo $this->generate_calendar_setup($input_id, $button_id);

        return $this->generate_calendar_icon($button_id) . '&nbsp;' . form_input($config);
    }

    public function generate_export_button($title, $url)
    {
        $base_url = base_url();
        $export_str = "<span style='float: right'>";
        $export_str .= "<img class='v-middle' src='{$base_url}static/images/icons/icon_export.gif'> ";
        $export_str .= anchor($url, $title);
        $export_str .= "</span>";

        return $export_str;
    }

    public function generate_notice_div($text)
    {
        $base_url = base_url();
        $html = <<<HTML
<div class="notice-main">
	<div style="float:left; display:inline;">
<img src="{$base_url}static/images/icons/notice-light.gif" />
</div>
	<div style="float:left; display:inline; padding-left:10px;">
	$text
	</div>
	<div style="clear:both;"></div>
	</div>
HTML;

        return $html;
    }
}
