<?php
$head = array(
    '<br/>',
);
$config_input = array(
      'name'        => 'url_search',
      'id'          => 'url_search',
      'maxlength'   => '50',
      'size'        => '50',
);
$url = site_url('seo/seo_rank/seo_rank_result');
$id = "result";
$config_button = array(
    'name'        => 'submit',
    'value'       => lang('search'),
    'type'        => 'button',
    'style'       => 'margin:10px',
    'onclick'     => "return search('$url', '$id');",
);
$data[] = array(
    '<div align="center">'.lang('search_by_url') . ': ' . form_input($config_input) . ' ' .block_button($config_button) .'<div>',
);
echo $this->block->generate_table($head, $data);
 ?>

<div id="result">
</div>
<script type='text/javascript'>

function search(url,id)
{
    var search = $('url_search').value;
    var params = {'url_search' : search};
    this.blur();
    helper.update_content(url, params,id, 1);
    return false;
}

</script>