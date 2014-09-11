function submit_content(element, url)
{
    var form = $H($('keyword_form').serialize(true));
    if ($('note'))
    {
        form = form.merge({note: tinyMCE.get('note').getContent()});
    }
    element.blur();
    
    return helper.ajax(url, form, 1);
}