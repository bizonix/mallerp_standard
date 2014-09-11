<?php

    $file_list_html = '';

    foreach ($files as $file)
    {
        $delete_url = site_url('edu/content/drop_file', array($file->id, $content_id));
        
        $file_list_html .= lang('file_name').' : '.$file->before_file_name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
                        .anchor(base_url().$file->file_url, lang('dowm_file')).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
                        .'<a href="#" onclick="delete_file('."'$delete_url'".'); return false;" >'.lang('delete').'</a>'.'<br>'
                        .$file->file_description.'<br/><br/>';
    }

    echo $file_list_html;

?>
